<?php

/**
 *
 * This file was build with the Entity Designer add-on.
 *
 * https://www.concrete5.org/marketplace/addons/entity-designer
 *
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\DropBox\Controller\Element\Header;

use Concrete\Core\Controller\ElementController;

class UploadedFile extends ElementController
{
    protected $pkgHandle = "drop_box";
    
    public function getElement()
    {
        return "header/uploaded_file";
    }
}
