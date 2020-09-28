<?php

namespace Concrete5\DropBox\Search\UploadedFile\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete5\DropBox\Search\UploadedFile\Field\Manager;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\UploadedFileList;

class FileColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.fID';
    }
    
    public function getColumnName()
    {
        return t('File');
    }
    
    public function getColumnCallback()
    {
        return [Manager::class, 'getFile'];
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
        $where = sprintf('t0.fID %s :file', $sort);
        $query->setParameter('file', $mixed->getFile());
        $query->andWhere($where);
    }
}
