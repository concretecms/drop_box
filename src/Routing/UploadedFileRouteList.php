<?php

namespace Concrete5\DropBox\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class UploadedFileRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete\Package\DropBox\Controller\Dialog\UploadedFile')
            ->setPrefix('/ccm/system/dialogs/uploaded_file')
            ->routes('dialogs/uploaded_file.php', 'drop_box');
    
        $router->buildGroup()->setNamespace('Concrete\Package\DropBox\Controller\Search')
            ->setPrefix('/ccm/system/search/uploaded_file')
            ->routes('search/uploaded_file.php', 'drop_box');
    }
}
