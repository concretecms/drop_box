<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet;

use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column\PrimaryIdentifierColumn;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column\FileIdentifierColumn;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column\FileColumn;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column\OwnerColumn;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column\CreatedAtColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new PrimaryIdentifierColumn());
        $this->addColumn(new FileIdentifierColumn());
        $this->addColumn(new FileColumn());
        $this->addColumn(new OwnerColumn());
        $this->addColumn(new CreatedAtColumn());
        
        $id = $this->getColumnByKey('t0.primaryIdentifier');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
