<?php

namespace Concrete5\DropBox\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class DropBoxRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->setNamespace('Concrete5\DropBox\Controller')
            ->setPrefix('/ccm/drop_box')
            ->routes('drop_box.php', 'drop_box');
    }
}
