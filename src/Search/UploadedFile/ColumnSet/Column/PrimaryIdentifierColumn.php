<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\UploadedFileList;

class PrimaryIdentifierColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.primaryIdentifier';
    }
    
    public function getColumnName()
    {
        return t('Primary Identifier');
    }
    
    public function getColumnCallback()
    {
        return 'getPrimaryIdentifier';
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
        $where = sprintf('t0.primary_identifier %s :primary_identifier', $sort);
        $query->setParameter('primary_identifier', $mixed->getPrimaryIdentifier());
        $query->andWhere($where);
    }
}
