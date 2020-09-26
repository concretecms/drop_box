<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/tus_server
 * Namespace: \Concrete5\DropBox\Controller
 */

$router->all('/files', 'TusServer::upload');