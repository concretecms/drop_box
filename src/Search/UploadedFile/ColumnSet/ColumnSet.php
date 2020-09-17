<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet;

use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Search\Column\Set;
use Concrete5\DropBox\Search\UploadedFile\SearchProvider;


class ColumnSet extends Set
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public static function getCurrent()
    {
        $app = Facade::getFacadeApplication();
        /** @var $provider SearchProvider */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();
        
        if ($query) {
            return $query->getColumns();
        }
        
        return $provider->getDefaultColumnSet();
    }
}
