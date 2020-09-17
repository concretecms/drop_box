<?php

namespace Concrete5\DropBox;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Routing\Router;
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
        /** @var UploadedFileRouteList $routeList */
        $routeList = $this->app->make(UploadedFileRouteList::class);
        $routeList->loadRoutes($router);
    }

}
