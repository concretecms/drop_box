<?php

namespace Concrete5\DropBox\Search\UploadedFile;

use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete5\DropBox\UploadedFileList;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\DefaultSet;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Available;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\ColumnSet;
use Concrete5\DropBox\Search\UploadedFile\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('uploaded_file');
    }
    
    public function getSessionNamespace()
    {
        return 'drop_box';
    }
    
    public function getCustomAttributeKeys()
    {
        return [];
    }
    
    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }
    
    public function getAvailableColumnSet()
    {
        return new Available();
    }
    
    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }
    
    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }
    
    public function getItemList()
    {
        return new UploadedFileList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedUploadedFileSearch();
    }
}
