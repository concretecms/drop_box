<?php

namespace Concrete5\DropBox;

use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\Search\ItemList\Pager\Manager\UploadedFileListPagerManager;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Permission\Key\Key;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;

class UploadedFileList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t0.primaryIdentifier', 't0.fileIdentifier', 't0.file', 't0.owner', 't0.createdAt'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('t0.*')
            ->from("UploadedFile", "t0");
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        $this->query->andWhere('(t0.`primaryIdentifier` LIKE :keywords OR t0.`fileIdentifier` LIKE :keywords OR t0.`createdAt` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * @param string $primaryIdentifier
     */
    public function filterByPrimaryIdentifier($primaryIdentifier)
    {
        $this->query->andWhere('t0.`primaryIdentifier` LIKE :primaryIdentifier');
        $this->query->setParameter('primaryIdentifier', '%' . $primaryIdentifier . '%');
    }

    /**
     * @param string $fileIdentifier
     */
    public function filterByFileIdentifier($fileIdentifier)
    {
        $this->query->andWhere('t0.`fileIdentifier` LIKE :fileIdentifier');
        $this->query->setParameter('fileIdentifier', '%' . $fileIdentifier . '%');
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     */
    public function filterByFile($file)
    {
        $this->query->andWhere('t0.fID = :file');
        $this->query->setParameter('file', $file->getFileId());
    }

    /**
     * @param string $owner
     */
    public function filterByOwner($owner)
    {
        $this->query->andWhere('t0.`owner` LIKE :owner');
        $this->query->setParameter('owner', '%' . $owner . '%');
    }

    /**
     * @param string $createdAt
     * @param string $comparison
     */
    public function filterByCreatedAt($createdAt, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t0.`createdAt`', $comparison,
            $this->query->createNamedParameter($createdAt)));
    }


    /**
     * @param array $queryRow
     * @return UploadedFile
     */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(UploadedFile::class)->findOneBy(["primaryIdentifier" => $queryRow["primaryIdentifier"], "fileIdentifier" => $queryRow["fileIdentifier"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.primaryIdentifier)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager()
    {
        return new UploadedFileListPagerManager($this);
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.primaryIdentifier)')
                ->setMaxResults(1);
        });
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            }

            /** @noinspection PhpParamsInspection */
            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        $permissionKey = Key::getByHandle("read_uploaded_file_entries");
        return $permissionKey->validate();
    }

    public function setPermissionsChecker(Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function isFulltextSearch()
    {
        return $this->isFulltextSearch;
    }
}
