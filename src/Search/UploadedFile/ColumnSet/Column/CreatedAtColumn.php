<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\Search\UploadedFile\Field\Manager;
use Concrete5\DropBox\UploadedFileList;

class CreatedAtColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.createdAt';
    }
    
    public function getColumnName()
    {
        return t('Created At');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getCreatedAt'];
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
        $where = sprintf('t0.created_at %s :created_at', $sort);
        $query->setParameter('created_at', $mixed->getCreatedAt());
        $query->andWhere($where);
    }
}
