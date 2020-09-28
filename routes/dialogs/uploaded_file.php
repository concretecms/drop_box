<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/dialogs/uploaded_file
 * Namespace: Concrete\Package\DropBox\Controller\Dialog\UploadedFile
 */

$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');
$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');
$router->all('/ccm/system/search/uploaded_file/basic', '\Concrete\Package\DropBox\Controller\Search\UploadedFile::searchBasic');
$router->all('/ccm/system/search/uploaded_file/current', '\Concrete\Package\DropBox\Controller\Search\UploadedFile::searchCurrent');
$router->all('/ccm/system/search/uploaded_file/preset/{presetID}', '\Concrete\Package\DropBox\Controller\Search\UploadedFile::searchPreset');
$router->all('/ccm/system/search/uploaded_file/clear', '\Concrete\Package\DropBox\Controller\Search\UploadedFile::clearSearch');
