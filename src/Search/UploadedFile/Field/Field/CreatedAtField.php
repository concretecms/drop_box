<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field\Field;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\UploadedFileList;

class CreatedAtField extends AbstractField
{
    protected $requestVariables = [
        'createdAt'
    ];
    
    public function getKey()
    {
        return 'createdAt';
    }
    
    public function getDisplayName()
    {
        return t('Created At');
    }
    
    /**
     * @param UploadedFileList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        $dateFrom = $dateTime->translate('created_at_from', $this->data);
        if ($dateFrom) {
            $list->filterByCreatedAt($dateFrom, '>=');
        }
        $dateTo = $dateTime->translate('created_at_to', $this->data);
        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }
            $list->filterByCreatedAt($dateTo, '<=');
        }
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        return $dateTime->datetime('created_at_from', $dateTime->translate('created_at_from', $this->data)) . t('to') . $dateTime->datetime('created_at_to', $dateTime->translate('created_at_to', $this->data));;
    }
}
