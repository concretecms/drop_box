<?php

namespace Concrete5\DropBox\Search\UploadedFile\Result;

use Concrete5\DropBox\Entity\UploadedFile;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;

class Item extends SearchResultItem
{
    public $primaryIdentifier;
    public $fileIdentifier;
    
    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }
    
    /**
    * @param UploadedFile $item
    */
    protected function populateDetails($item)
    {
        $this->primaryIdentifier = $item->getPrimaryIdentifier();
        $this->fileIdentifier = $item->getFileIdentifier();
    }
}
