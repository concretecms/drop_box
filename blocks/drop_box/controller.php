<?php

namespace Concrete\Package\DropBox\Block\DropBox;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = "btDropBox";

    public function getBlockTypeDescription()
    {
        return t('Add an drop box to a page.');
    }

    public function getBlockTypeName()
    {
        return t('Drop Box');
    }
}
