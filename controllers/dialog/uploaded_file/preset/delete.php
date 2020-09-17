<?php

namespace Concrete\Package\DropBox\Controller\Dialog\UploadedFile\Preset;

use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete\Core\Permission\Key\Key;
use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
{
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
    
    public function getSavedSearchEntity()
    {
        /** @var EntityManager $em */
        $em = $this->app->make(EntityManager::class);
        
        if (is_object($em)) {
            return $em->getRepository(SavedUploadedFileSearch::class);
        }
        
        return null;
    }
}
