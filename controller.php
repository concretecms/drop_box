<?php

namespace Concrete\Package\DropBox;

use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete5\DropBox\ServiceProvider;
use Concrete\Core\File\StorageLocation\Type\Type;

class Controller extends Package
{
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.2.0';
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

    private function installOrUpdate()
    {
        // Install S3 storage provider

        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        $pkg = $packageService->getByHandle($this->getPackageHandle());

        $storageObject = Type::getByHandle("s3");

        if (!$storageObject instanceof Type) {
            Type::add('s3', t('Amazon S3'), $pkg);
        }
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("install.xml");
        $this->installOrUpdate();
    }

    public function install()
    {
        parent::install();
        $this->installContentFile("install.xml");
        $this->installOrUpdate();
    }

    public function on_start()
    {
        if (file_exists($this->getPackagePath() . "/vendor")) {
            require_once $this->getPackagePath() . "/vendor/autoload.php";
        }

        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
}