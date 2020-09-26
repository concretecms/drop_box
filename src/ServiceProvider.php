<?php

namespace Concrete5\DropBox;

use Concrete\Core\Foundation\Service\Provider;
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
