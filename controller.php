<?php

namespace Concrete\Package\DropBox;

use Concrete\Core\Package\Package;
use Concrete5\DropBox\ServiceProvider;

class Controller extends Package
{
    protected $appVersionRequired = '8.2.0';
    protected $pkgVersion = '0.1.2';
    protected $pkgHandle = 'drop_box';
    protected $pkgDescription = '';
    protected $pkgAutoloaderRegistries = ['src' => 'Concrete5\DropBox'];

    public function getPackageName()
    {
        return t('Drop Box');
    }

    public function getPackageDescription()
    {
        return t('concrete5 package to enable big file uploads.');
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("install.xml");
    }

    public function install()
    {
        parent::install();
        $this->installContentFile("install.xml");
    }

    public function on_start()
    {
        if (file_exists($this->getPackagePath() . "/vendor")) {
            require_once  $this->getPackagePath() . "/vendor/autoload.php";
        }

        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
}