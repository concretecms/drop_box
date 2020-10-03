<?php /** @noinspection PhpUnused */

namespace Concrete5\DropBox\Controller;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Checker;
use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server;

class TusServer extends AbstractController
{
    protected $server;
    /** @var FileImporter */
    protected $fileImporter;
    /** @var Repository */
    protected $config;
    /** @var ErrorList */
    protected $error;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();
        $this->server = new Server();
        $this->fileImporter = $this->app->make(FileImporter::class);
        $this->config = $this->app->make(Repository::class);
        $this->error = $this->app->make(ErrorList::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    /** @noinspection PhpUnused */
    public function onUploadComplete(TusEvent $event)
    {
        $fileVersion = null;

        try {
            $fileVersion = $this->fileImporter->importLocalFile($event->getFile()->getFilePath(), $event->getFile()->getName());
        } catch (ImportException $e) {
            $this->error->add($e);
        }

        if ($fileVersion instanceof Version) {
            $file = $fileVersion->getFile();

            if ($this->config->has("drop_box.storage_location")) {
                $targetStorageLocation = Type::getByID($this->config->get("drop_box.storage_location"));
                try {
                    /** @noinspection PhpParamsInspection */
                    $file->setFileStorageLocation($targetStorageLocation);
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            }

            $fs = new Filesystem(new Local(dirname($event->getFile()->getFilePath())));

            try {
                $fs->delete(basename($event->getFile()->getFilePath()));
            } catch (FileNotFoundException $e) {
                $this->error->add(t("There was an error while removing the temp file."));
            }

        } else {
            $this->error->add(t("There was an error while uploading the file."));
        }

        if ($this->error->has()) {
            $editResponse = new EditResponse();
            $editResponse->setError($this->error);
            $this->responseFactory->json($editResponse)->send();
            $this->app->shutdown();
        }
    }

    public function upload()
    {
        $permissionChecker = new Checker();

        /** @noinspection PhpUnhandledExceptionInspection */
        if ($permissionChecker->getResponseObject()->validate("upload_file_to_drop_box")) {
            $this->error->add(t("You don't have the permission to upload files."));
            $editResponse = new EditResponse();
            $editResponse->setError($this->error);
            return $this->responseFactory->json($editResponse);
        } else {
            $this->server->event()->addListener('tus-server.upload.complete', [$this, 'onUploadComplete']);
            return $this->server->serve();
        }
    }
}

