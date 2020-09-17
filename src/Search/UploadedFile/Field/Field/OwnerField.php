<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\UploadedFileList;

class OwnerField extends AbstractField
{
    protected $requestVariables = [
        'owner'
    ];

    public function getKey()
    {
        return 'owner';
    }

    public function getDisplayName()
    {
        return t('Owner');
    }

    /**
     * @param UploadedFileList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByOwner($this->data['owner']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var UserSelector $userSelector */
        $userSelector = $app->make(UserSelector::class);
        return $userSelector->selectUser('owner', $this->data['owner']);
    }
}
