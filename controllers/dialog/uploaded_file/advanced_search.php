<?php

namespace Concrete\Package\DropBox\Controller\Dialog\UploadedFile;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete5\DropBox\Search\UploadedFile\SearchProvider;
use Doctrine\ORM\EntityManager;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        }

        return false;
    }

    public function on_before_render()
    {
        parent::on_before_render();

        // use core views (remove package handle)
        $viewObject = $this->getViewObject();
        $viewObject->setInnerContentFile(null);
        $viewObject->setPackageHandle(null);
        $viewObject->setupRender();
    }

    public function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedUploadedFileSearch::class);
        }

        return null;
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('uploaded_file');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        if (version_compare(APP_VERSION, "9.0", ">=")) {
            return $this->app->make('url')->to('/dashboard/files/drop_box', 'preset', $search->getID());
        } else {
            return (string)Url::to('/ccm/system/search/uploaded_file/preset', $search->getID());
        }
    }

    public function getCurrentSearchBaseURL()
    {
        return Url::to('/ccm/system/search/uploaded_file/current');
    }

    public function getBasicSearchBaseURL()
    {
        return Url::to('/ccm/system/search/uploaded_file/basic');
    }


    public function getSubmitMethod()
    {
        if (version_compare(APP_VERSION, "9.0", ">=")) {
            return 'get';
        } else {
            return parent::getSubmitMethod();
        }
    }

    public function getSubmitAction()
    {
        if (version_compare(APP_VERSION, "9.0", ">=")) {
            return $this->app->make('url')->to('/dashboard/files/drop_box', 'advanced_search');
        } else {
            return parent::getSubmitAction();
        }
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/uploaded_file/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        if (version_compare(APP_VERSION, "9.0", ">=")) {

        } else {
            return (string)Url::to('/ccm/system/dialogs/uploaded_file/advanced_search/preset/edit?presetID=' . $search->getID());
        }
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedUploadedFileSearch::class)->findAll();
        }

        return null;
    }
}
