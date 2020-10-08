<?php

namespace Concrete5\DropBox;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;
use Concrete5\DropBox\Entity\UploadedFile;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(UploadedFile $uploadedFileEntry)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/files/drop_box/edit", $uploadedFileEntry->getPrimaryIdentifier(), $uploadedFileEntry->getFileIdentifier()),
                t('Edit')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/ccm/drop_box/download", $uploadedFileEntry->getPrimaryIdentifier(), $uploadedFileEntry->getFileIdentifier()),
                t('Download')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/files/drop_box/remove", $uploadedFileEntry->getPrimaryIdentifier(), $uploadedFileEntry->getFileIdentifier()),
                t('Delete')
            )
        );
    }
}
