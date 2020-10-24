<?php

namespace Concrete5\DropBox;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Url;
use Concrete5\DropBox\Entity\UploadedFile;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(UploadedFile $uploadedFileEntry)
    {
        parent::__construct();

        if ($uploadedFileEntry->getFile() instanceof File) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to("/dashboard/files/details", $uploadedFileEntry->getFile()->getFileID()),
                    t('View Details')
                )
            );
        }

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
