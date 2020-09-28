<?php

namespace Concrete5\DropBox\Controller;

use Concrete\Core\Controller\AbstractController;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server;

class TusServer extends AbstractController
{
    protected $server;

    public function on_start()
    {
        parent::on_start();

        // @todo: add config to server object

        $this->server = new Server();
    }

    public function onUploadComplete(TusEvent $event)
    {
        // @todo: import file $event->getFile() to concrete5 file manager and create UploadedFile-Entry
    }

    public function upload()
    {
        $this->server->event()->addListener('tus-server.upload.complete', [$this, 'onUploadComplete']);
        return $this->server->serve();
    }
}

