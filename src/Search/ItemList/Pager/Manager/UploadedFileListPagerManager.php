<?php

namespace Concrete5\DropBox\Search\ItemList\Pager\Manager;

use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\Search\UploadedFile\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\AbstractPagerManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

class UploadedFileListPagerManager extends AbstractPagerManager
{
    /** 
     * @param UploadedFile $uploadedFile
     * @return int 
     */
    public function getCursorStartValue($uploadedFile)
    {
        return $uploadedFile->getId();
    }
    
    public function getCursorObject($cursor)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(UploadedFile::class)->findOneBy(["id" => $cursor]);
    }
    
    public function getAvailableColumnSet()
    {
        return new Available();
    }
    
    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $itemList->getQueryObject()->addOrderBy('t0.primaryIdentifier', $direction);
    }
}
