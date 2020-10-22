<?php /** @noinspection PhpUnused */

namespace Concrete5\DropBox\Controller;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete5\DropBox\Entity\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server;
use DateTime;

class DropBox extends AbstractController
{
    /** @var Server */
    protected $server;
    /** @var FileImporter */
    protected $fileImporter;
    /** @var Repository */
    protected $config;
    /** @var ErrorList */
    protected $error;
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var StorageLocationFactory */
    protected $storageLocationFactory;
    /** @var LoggerFactory */
    protected $loggerFactory;
    /** @var LoggerInterface */
    protected $logger;

    public function on_start()
    {
        parent::on_start();
        $this->fileImporter = $this->app->make(FileImporter::class);
        $this->config = $this->app->make(Repository::class);
        $this->error = $this->app->make(ErrorList::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->storageLocationFactory = $this->app->make(StorageLocationFactory::class);
        $this->loggerFactory = $this->app->make(LoggerFactory::class);
        $this->logger = $this->loggerFactory->createLogger(Channels::CHANNEL_PACKAGES);
    }

    /** @noinspection PhpUnused */
    public function onUploadComplete(TusEvent $event)
    {
        // First, removed the cached file
        $this->server->getCache()->delete($event->getFile()->getKey());
        
        $fileVersion = null;

        try {
            $fileVersion = $this->fileImporter->importLocalFile($event->getFile()->getFilePath(), $event->getFile()->getName());
        } catch (ImportException $e) {
            $this->error->add($e);
        }

        if ($fileVersion instanceof Version) {
            $file = $fileVersion->getFile();

            $permissionChecker = new Checker();

            /** @noinspection PhpUnhandledExceptionInspection */
            if (!$permissionChecker->canUploadFileToDropBox()) {
                $this->error->add(t("You don't have the permission to upload files."));

                try {
                    $file->delete();
                } catch (Exception $e) {
                    $this->error->add($e);
                }

            } else {
                if ($this->config->has("drop_box.storage_location")) {
                    try {
                        $targetStorageLocation = $this->storageLocationFactory->fetchByID((int)$this->config->get("drop_box.storage_location"));
                        $file->setFileStorageLocation($targetStorageLocation);
                    } catch (Exception $e) {
                        $this->error->add($e);
                    }
                }

                $owner = null;
                $primaryIdentifier = null;
                $fileIdentifier = null;

                $user = new User();

                if ($user->isRegistered()) {
                    /** @var UserEntity $owner */
                    $owner = $this->entityManager->getRepository(UserEntity::class)->findOneBy(["uID" => $user->getUserID()]);
                }

                try {
                    $primaryIdentifier = Uuid::uuid4()->toString();
                    $fileIdentifier = Uuid::uuid4()->toString();
                } catch (Exception $e) {
                    $this->error->add(t("There was an error while generating the unique identifiers."));
                }

                /*
                 * After the file was successfully imported we are associate the file
                 * with a new uploaded file entry.
                 */

                $uploadedFileEntry = new UploadedFile();

                $uploadedFileEntry->setCreatedAt(new DateTime());
                $uploadedFileEntry->setFile($file);
                $uploadedFileEntry->setPrimaryIdentifier($primaryIdentifier);
                $uploadedFileEntry->setFileIdentifier($fileIdentifier);
                /** @noinspection PhpParamsInspection */
                $uploadedFileEntry->setOwner($owner);

                $this->entityManager->persist($uploadedFileEntry);
                $this->entityManager->flush();
            }

        } else {
            $this->error->add(t("There was an error while uploading the file."));
        }

        /*
         * Remove the temp file
         */

        $fs = new Filesystem(new Local(dirname($event->getFile()->getFilePath())));

        try {
            $fs->delete(basename($event->getFile()->getFilePath()));
        } catch (FileNotFoundException $e) {
            $this->error->add(t("There was an error while removing the temp file."));
        }

        if ($this->error->has()) {

            /*
             * Unfortunately the tus protocol only supports a 404 response
             * if something went wrong with the file uploading. So we save the
             * errors into the concrete5 log and answer with an generic 404 response.
             */

            foreach ($this->error->getList() as $error) {
                $this->logger->error($error);
            }

            $this->responseFactory->create(t("There was an error while uploading. Please check the logs."), Response::HTTP_NOT_FOUND)->send();
            $this->app->shutdown();
        }
    }

    public function download($primaryIdentifier = null, $fileIdentifier = null)
    {
        $entry = $this->entityManager->getRepository(UploadedFile::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier,
            "fileIdentifier" => $fileIdentifier
        ]);

        if ($entry instanceof UploadedFile) {
            $file = $entry->getFile();

            if ($file instanceof File) {
                $approvedVersion = $file->getApprovedVersion();

                if ($approvedVersion instanceof Version) {
                    return new StreamedResponse(function() use ($approvedVersion) {
                        $outputStream = fopen('php://output', 'wb');
                        $fileStream = $approvedVersion->getFileResource();
                        stream_copy_to_stream($fileStream->readStream(), $outputStream);
                    }, Response::HTTP_OK, [
                        "Content-Type" => $approvedVersion->getMimeType(),
                        "Content-Disposition" => 'attachment; filename=' . $approvedVersion->getFileName()
                    ]);
                }
            }
        }

        return $this->responseFactory->notFound(t("The download item is invalid or expired."));
    }

    public function upload($path = null)
    {
        /*
         * Manipulate the super global $_SERVER to fake the required path information for the
         * tus request. Honestly this is not a really nice solution but it work's. Implementing a
         * middleware is not working because the tus request object contains a symfony 
         * request object which is protected.
         *
         * The concrete5 Routing system and all request stuff is unaffected by this change
         * because all requests are already initialized.
         */

        $_SERVER['REQUEST_URI'] = $path;

        $this->server = new Server();

        /*
         * We need to resolve the path with the url resolver otherwise this won't work
         * on concrete5 installations that are running in subdirectories.
         */

        $this->server->setApiPath("/" . Url::to("/ccm/drop_box/upload")->getPath());

        /*
         * We need to hook into the upload event to import the file into the file manager.
         */

        $this->server->event()->addListener('tus-server.upload.complete', [$this, 'onUploadComplete']);

        /*
         * And finally we need to set the upload dir - that's it.
         */

        $uploadDir = ini_get("upload_tmp_dir") ? ini_get("upload_tmp_dir") : sys_get_temp_dir();

        $this->server->setUploadDir($uploadDir);

        return $this->server->serve();
    }
}

