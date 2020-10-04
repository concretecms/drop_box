<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/drop_box
 * Namespace: \Concrete5\DropBox\Controller
 */

$router->all('/upload', 'DropBox::upload');
$router->all('/upload/{path}', 'DropBox::upload');
$router->all('/download/{primaryIdentifier}/{fileIdentifier}', 'DropBox::download');
