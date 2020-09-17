<?php

namespace Concrete\Package\DropBox\Controller\Dialog\UploadedFile;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\EntityManager;
use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete5\DropBox\Search\UploadedFile\SearchProvider;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;
    
    protected function canAccess()
    {
        $permissionKey = Key::getByHandle("read_uploaded_file_entries");
        return $permissionKey->validate();
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
        return (string) Url::to('/ccm/system/search/uploaded_file/preset', $search->getID());
    }
    
    public function getCurrentSearchBaseURL()
    {
        return Url::to('/ccm/system/search/uploaded_file/current');
    }
    
    public function getBasicSearchBaseURL()
    {
        return Url::to('/ccm/system/search/uploaded_file/basic');
    }
    
    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/uploaded_file/advanced_search/preset/delete?presetID=' . $search->getID());
    }
    
    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/uploaded_file/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
