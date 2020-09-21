<?php

namespace Concrete\Package\DropBox\Controller\Dialog\UploadedFile\Preset;

use Concrete\Core\Legacy\FilePermissions;
use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete\Controller\Dialog\Search\Preset\Edit as PresetEdit;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Support\Facade\Url;

class Edit extends PresetEdit
{
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
    
    public function getSavedSearchEntity()
    {
        /** @var EntityManager $em */
        $em = $this->app->make(EntityManager::class);
        
        if (is_object($em)) {
            return $em->getRepository(SavedUploadedFileSearch::class);
        }
        
        return null;
    }
    
    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/search/uploaded_file/preset', $search->getID());
    }
}
