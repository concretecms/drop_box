<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\UploadedFileList;

class FileIdentifierField extends AbstractField
{
    protected $requestVariables = [
        'fileIdentifier'
    ];
    
    public function getKey()
    {
        return 'fileIdentifier';
    }
    
    public function getDisplayName()
    {
        return t('File Identifier');
    }
    
    /**
     * @param UploadedFileList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByFileIdentifier($this->data['fileIdentifier']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('fileIdentifier', $this->data['fileIdentifier']);
    }
}
