<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\Search\UploadedFile\Field\Manager;
use Concrete5\DropBox\UploadedFileList;

class OwnerColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.owner';
    }
    
    public function getColumnName()
    {
        return t('Owner');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getOwner'];
    }

    public function isColumnSortable()
    {
        return false;
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
        $where = sprintf('t0.owner %s :owner', $sort);
        $query->setParameter('owner', $mixed->getOwner());
        $query->andWhere($where);
    }
}
