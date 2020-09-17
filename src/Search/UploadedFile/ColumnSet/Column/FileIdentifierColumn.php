<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\UploadedFileList;

class FileIdentifierColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.fileIdentifier';
    }
    
    public function getColumnName()
    {
        return t('File Identifier');
    }
    
    public function getColumnCallback()
    {
        return 'getFileIdentifier';
    }
    
    /**
    * @param UploadedFileList $itemList
    * @param $mixed UploadedFile
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t0.file_identifier %s :file_identifier', $sort);
        $query->setParameter('file_identifier', $mixed->getFileIdentifier());
        $query->andWhere($where);
    }
}
