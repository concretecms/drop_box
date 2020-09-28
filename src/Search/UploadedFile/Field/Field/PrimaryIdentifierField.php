<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\UploadedFileList;

class PrimaryIdentifierField extends AbstractField
{
    protected $requestVariables = [
        'primaryIdentifier'
    ];
    
    public function getKey()
    {
        return 'primaryIdentifier';
    }
    
    public function getDisplayName()
    {
        return t('Primary Identifier');
    }
    
    /**
     * @param UploadedFileList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByPrimaryIdentifier($this->data['primaryIdentifier']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('primaryIdentifier', $this->data['primaryIdentifier']);
    }
}
