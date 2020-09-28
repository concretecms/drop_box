<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field\Field;

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\File\File;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\UploadedFileList;

class FileField extends AbstractField
{
    protected $requestVariables = [
        'file'
    ];
    
    public function getKey()
    {
        return 'file';
    }
    
    public function getDisplayName()
    {
        return t('File');
    }
    
    /**
     * @param UploadedFileList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByFile(File::getById($this->data['file']));
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var $fileManager FileManager */
        $fileManager = $app->make(FileManager::class);
        return $fileManager->file("file", "file", t('Please choose'), $this->data['file']);
    }
}
