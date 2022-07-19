<?php /** @noinspection PhpUnused */

namespace Concrete5\DropBox\Controller;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
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
use Doctrine\Persistence\ObjectRepository;
use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use TusPhp\Events\TusEvent;
use TusPhp\Events\UploadComplete;
use TusPhp\Events\UploadCreated;
use TusPhp\Exception\FileException;
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

    public function onUploadCreated(UploadCreated $upload)
    {
        // Make sure we have permission to upload
        $permissionChecker = new Checker();
        if (!$permissionChecker->canUploadFileToDropBox()) {
            $this->error->add(t("You don't have the permission to upload files."));
            return;
        }

        $filename = $upload->getFile()->getName();

        // Validate the file type, make sure it's both in the allow list and not in the deny list
        $config = $this->app['config'];
        $fileService = $this->app->make('helper/concrete/file');
        $allowedFileTypes = $fileService
            ->unSerializeUploadFileExtensions(mb_strtolower($config->get('concrete.upload.extensions')));
        $deniedFileTypes = $fileService
            ->unSerializeUploadFileExtensions(mb_strtolower($config->get('concrete.upload.extensions_denylist')));

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($extension, $deniedFileTypes) || !in_array($extension, $allowedFileTypes)) {
            $this->error->add('File type not allowed.');
        }

        // Make sure the file we're uploading to is empty
        $filePath = $upload->getFile()->getFilePath();
        if (file_exists($filePath)) {
            file_put_contents($filePath, '');
        }
    }

    /** @noinspection PhpUnused */
    public function onUploadComplete(UploadComplete $event)
    {
        $fileVersion = null;

        $eventFile = $event->getFile();
        try {
            $tmpFile = tempnam('/tmp', $eventFile->getName());
            $fileVersion = $this->fileImporter->importLocalFile($tmpFile, $eventFile->getName());
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
                    $primaryIdentifier = $eventFile->getKey();
                    $fileIdentifier = Uuid::uuid4()->toString();
                } catch (Exception $e) {
                    $this->error->add(t("There was an error while generating the unique identifiers."));
                }

                /*
                 * After the file was successfully imported we are associate the file
                 * with a new uploaded file entry.
                 */

                if (!$this->error->has()) {
                    $uploadedFileEntry = new UploadedFile();

                    $uploadedFileEntry->setCreatedAt(new DateTime());
                    $uploadedFileEntry->setFile($file);
                    $uploadedFileEntry->setPrimaryIdentifier($primaryIdentifier);
                    $uploadedFileEntry->setFileIdentifier($fileIdentifier);
                    /** @noinspection PhpParamsInspection */
                    $uploadedFileEntry->setOwner($owner);

                    $this->entityManager->persist($uploadedFileEntry);
                    $this->entityManager->flush();

                    // Move the uploaded file into its final place
                    $storageLocation = $file->getFileStorageLocationObject();
                    $fileSystem = $storageLocation->getFileSystemObject();
                    $adapter = $fileSystem->getAdapter();

                    // If we have a local file we can just move the file, otherwise we have to import it.
                    if ($adapter instanceof Local) {
                        $cf = $this->app->make('helper/concrete/file');
                        $filePath = $cf->prefix($fileVersion->getPrefix(), $fileVersion->getFileName());
                        $realPath = $adapter->applyPathPrefix($filePath);

                        // Move the uploaded file on top of the imported dummy file
                        rename($eventFile->getFilePath(), $realPath);
                    } else {
                        $stream = fopen($eventFile->getFilePath(), 'rb+');
                        // Write the data from the upload file over the dummy imported file. This can be very slow due
                        // to bandwidth limitations
                        $fileSystem->writeStream($fileVersion->getRelativePath(), $stream);
                        fclose($stream);
                        unlink($eventFile->getFilePath());
                    }

                    // Remove the cache now that the uploaded file is gone
                    $this->server->getCache()->delete($eventFile->getKey());

                    // Update file size
                    $sizeSetter = new class extends Version
                    {
                        public function setFileSize(Version $fileVersion, int $size)
                        {
                            $fileVersion->fvSize = $size;
                        }
                    };
                    $sizeSetter->setFileSize($fileVersion, $eventFile->getFileSize());
                    $this->entityManager->flush();
                }
            }

        } else {
            $this->error->add(t("There was an error while uploading the file."));
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
                    return new StreamedResponse(function () use ($approvedVersion) {
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

    public function resolveDownloadUrl($primaryIdentifier)
    {
        $downloadUrl = null;
        $fileName = null;

        $entry = $this->entityManager->getRepository(UploadedFile::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier
        ]);

        if ($entry instanceof UploadedFile) {
            $file = $entry->getFile();

            if ($file instanceof File) {
                $approvedVersion = $file->getApprovedVersion();

                if ($approvedVersion instanceof Version) {
                    $downloadUrl = (string)$approvedVersion->getDownloadURL();
                    $fileName = $approvedVersion->getFileName();
                }
            }
        }

        return new JsonResponse([
            "downloadUrl" => $downloadUrl,
            "fileName" => $fileName
        ]);
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
        $this->server->event()->addListener('tus-server.upload.created', [$this, 'onUploadCreated']);

        /*
         * And finally we need to set the upload dir - that's it.
         */
        $uploadDir = $_ENV['DROPBOX_TMP_DIR'] ?? (ini_get("upload_tmp_dir") ?: sys_get_temp_dir());

        $this->server->setUploadDir($uploadDir);

        set_time_limit(0);
        $response = $this->server->serve();

        // Check for any errors added by event handlers
        if ($this->error->has()) {
            return new JsonResponse($this->error, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}

