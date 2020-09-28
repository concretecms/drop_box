<?php

namespace Concrete5\DropBox\Search\UploadedFile\Result;

use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        return new Item($this, $this->listColumns, $item);
    }
    
    public function getColumnDetails($column)
    {
        return new Column($this, $column);
    }
}
