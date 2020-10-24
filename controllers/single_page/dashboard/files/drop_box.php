<?php

namespace Concrete\Package\DropBox\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Http\Response;
use Concrete\Core\Logging\Search\Menu\MenuFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\DropBox\Controller\Search\UploadedFile;
use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete5\DropBox\Form\Service\Validation;
use Concrete5\DropBox\Search\UploadedFile\SearchProvider;
use Concrete\Core\File\File;
use Concrete5\DropBox\Entity\UploadedFile as UploadedFileEntity;
use Concrete\Package\DropBox\Controller\Element\Header\UploadedFile as HeaderController;

class DropBox extends DashboardPageController
{

    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var DateTime */
    protected $dateTime;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
        $this->dateTime = $this->app->make(DateTime::class);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     * @param UploadedFileEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data)) {
            /** @var User $owner */
            $owner = $this->entityManager->getRepository(User::class)->findOneBy(["uID" => $data["owner"]]);
            $createdAt = $this->dateTime->translate("created_at", $data, true);
            $entry->setPrimaryIdentifier($data["primaryIdentifier"]);
            $entry->setFileIdentifier($data["fileIdentifier"]);
            $entry->setFile(File::getById($data["file"]));
            $entry->setOwner($owner);
            $entry->setCreatedAt($createdAt);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/files/drop_box/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->setDefaults($entry);
        }
    }

    private function setDefaults($entry = null)
    {

        $this->set("entry", $entry);
        $this->render("/dashboard/files/drop_box/edit");
    }

    public function removed()
    {
        $this->set("success", t('The item has been successfully removed.'));
        $this->view();
    }

    public function saved()
    {
        $this->set("success", t('The item has been successfully updated.'));
        $this->view();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param array $data
     * @return bool
     */
    public function validate($data = null)
    {
        /** @var Validation $validator */
        $validator = $this->app->make(Validation::class);
        $validator->setData($data);
        $validator->addRequiredGuid("primaryIdentifier", t("You need to enter a primary identifier."));
        $validator->addRequiredGuid("fileIdentifier", t("You need to enter a file identifier."));
        $validator->addRequiredFile("file", t("You need to select a valid file."));
        $validator->addRequiredDateTime("created_at", t("You need to enter a valid date."));

        if ($validator->test()) {
            return true;
        } else {
            $this->error = $validator->getError();
            return false;
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new UploadedFileEntity();

        if ($this->token->validate("save_drop_box_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function edit($primaryIdentifier = null, $fileIdentifier = null)
    {
        /** @var UploadedFileEntity $entry */
        $entry = $this->entityManager->getRepository(UploadedFileEntity::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier,
            "fileIdentifier" => $fileIdentifier
        ]);

        if ($entry instanceof UploadedFileEntity) {
            if ($this->token->validate("save_drop_box_entity")) {
                return $this->save($entry);
            }

            $this->setDefaults($entry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function remove($primaryIdentifier = null, $fileIdentifier = null)
    {
        /** @var UploadedFileEntity $entry */
        $entry = $this->entityManager->getRepository(UploadedFileEntity::class)->findOneBy([
            "primaryIdentifier" => $primaryIdentifier,
            "fileIdentifier" => $fileIdentifier
        ]);

        if ($entry instanceof UploadedFileEntity) {
            $file = $entry->getFile();

            if ($file instanceof \Concrete\Core\Entity\File\File) {
                $file->delete();
            }

            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/files/drop_box/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('dashboard/files/drop_box/search/menu', 'drop_box');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('dashboard/files/drop_box/search/search', 'drop_box');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerMenu->getElementController()->setQuery($result->getQuery());
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerSearch->getElementController()->setQuery($result->getQuery());

        //$this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->set('resultsBulkMenu', $this->app->make(\Concrete5\DropBox\Search\UploadedFile\Menu\MenuFactory::class)->createBulkMenu());

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        /** @var ResultFactory $resultFactory */
        $resultFactory = $this->app->make(ResultFactory::class);
        /** @var QueryModifier $queryModifier */
        $queryModifier = $this->app->make(QueryModifier::class);
        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);
        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedUploadedFileSearch::class, $presetID);

            if ($preset) {
                /** @noinspection PhpParamsInspection */
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);
                return;
            }
        }

        $this->view();
    }

    public function view()
    {
        if (version_compare(APP_VERSION, "9.0", ">=")) {
            $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
                $this->getSearchKeywordsField()
            ]);
            $result = $this->createSearchResult($query);
            $this->renderSearchResult($result);
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $this->headerSearch->getElementController()->setQuery(null);
        } else {
            $headerMenu = new HeaderController();
            $this->set('headerMenu', $headerMenu);
            /** @var UploadedFile $searchProvider */
            $searchProvider = $this->app->make(UploadedFile::class);
            $result = $searchProvider->getCurrentSearchObject();
            if (is_object($result)) {
                $this->set('result', $result);
            }
        }
    }
}
