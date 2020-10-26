<?php

namespace Concrete5\DropBox\Search\UploadedFile\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class MenuFactory
{

    public function createBulkMenu(): DropdownMenu
    {
        $menu = new DropdownMenu();

        $menu->addItem(new LinkItem('#', t('Delete'), [
            'data-bulk-action-type' => 'dialog',
            'data-bulk-action-title' => t('Delete'),
            'data-bulk-action-url' => Url::to('/ccm/system/dialogs/uploaded_file/bulk/delete'),
            'data-bulk-action-dialog-width' => '500',
            'data-bulk-action-dialog-height' => '400',
        ]));

        return $menu;
    }

}
