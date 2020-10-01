<?php

namespace Concrete5\DropBox;

use Concrete\Core\Foundation\Psr4ClassLoader;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Routing\Router;
use Concrete5\DropBox\Routing\TusRouteList;
use Concrete5\DropBox\Routing\UploadedFileRouteList;
use Concrete5\DropBox\Search\UploadedFile\Field\ManagerServiceProvider;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->initializeSearchProvider();
        $this->initializeRoutes();
        $this->initializeFileStorageType();
    }

    private function initializeFileStorageType()
    {
        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        /** @var Package $pkg */
        $pkg = $packageService->getClass("drop_box");

        $loader = new Psr4ClassLoader();

        $loader->addPrefix('\\Concrete5\\DropBox\\File\\StorageLocation\\Configuration', $pkg->getPackagePath() . '/src/File/StorageLocation/Configuration/');

        $loader->addPrefix(
            '\\Concrete\\Package\\DropBox\\Core\\File\\StorageLocation\\Configuration',
            $pkg->getPackagePath() . '/src/File/StorageLocation/Configuration/'
        );

        $loader->addPrefix(
            '\\Concrete\\Package\\DropBox\\Src\\File\\StorageLocation\\Configuration',
            $pkg->getPackagePath() . '/src/File/StorageLocation/Configuration/'
        );

        $loader->addPrefix(
            '\\Concrete\\Package\\DropBox\\File\\StorageLocation\\Configuration',
            $pkg->getPackagePath() . '/src/File/StorageLocation/Configuration/'
        );

        $loader->register();

        $this->app->bind(
            'Concrete\Package\DropBox\Src\File\StorageLocation\Configuration\S3Configuration',
            'Concrete5\DropBox\File\StorageLocation\Configuration\S3Configuration'
        );

        $this->app->bind(
            'Concrete\Package\DropBox\Core\File\StorageLocation\Configuration\S3Configuration',
            'Concrete5\DropBox\File\StorageLocation\Configuration\S3Configuration'
        );

        $this->app->bind(
            'Concrete\Package\DropBox\File\StorageLocation\Configuration\S3Configuration',
            'Concrete5\DropBox\File\StorageLocation\Configuration\S3Configuration'
        );
    }

    public function initializeSearchProvider()
    {
        /** @var ManagerServiceProvider $searchProvider */
        $searchProvider = $this->app->make(ManagerServiceProvider::class);
        $searchProvider->register();
    }

    public function initializeRoutes()
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        /** @var UploadedFileRouteList $uploadedFileRouteList */
        $uploadedFileRouteList = $this->app->make(UploadedFileRouteList::class);
        $uploadedFileRouteList->loadRoutes($router);

        /** @var TusRouteList $tusRouteList */
        $tusRouteList = $this->app->make(TusRouteList::class);
        $tusRouteList->loadRoutes($router);
    }

}
