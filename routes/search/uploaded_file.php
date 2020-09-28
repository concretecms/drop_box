<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/search/uploaded_file
 * Namespace: Concrete\Package\DropBox\Controller\Search\
 */

$router->all('/basic', 'UploadedFile::searchBasic');
$router->all('/current', 'UploadedFile::searchCurrent');
$router->all('/preset/{presetID}', 'UploadedFile::searchPreset');
$router->all('/clear', 'UploadedFile::clearSearch');
