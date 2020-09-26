<?php

namespace Concrete5\DropBox\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class TusRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete5\DropBox\Controller')
            ->setPrefix('/ccm/tus_server')
            ->routes('tus_server.php', 'drop_box');
    }
}
