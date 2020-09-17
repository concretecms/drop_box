<?php

namespace Concrete\Package\DropBox\Controller\Search;

use Concrete5\DropBox\Entity\Search\SavedUploadedFileSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Controller\Search\Standard;
use Concrete\Core\Permission\Key\Key;
use Concrete\Package\DropBox\Controller\Dialog\UploadedFile\AdvancedSearch;

class UploadedFile extends Standard
{
    /**
     * @return \Concrete\Controller\Dialog\Search\AdvancedSearch
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make(AdvancedSearch::class);
    }
    
    /**
     * @param int $presetID
     *
     * @return SavedUploadedFileSearch|null
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManagerInterface::class);
        return $em->find(SavedUploadedFileSearch::class, $presetID);
    }
    
    /**
     * @return KeywordsField[]
     */
    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('cKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }
        
        return $fields;
    }
    
    /**
     * @return bool
     */
    protected function canAccess()
    {
        $permissionKey = Key::getByHandle("read_uploaded_file_entries");
        return $permissionKey->validate();
    }
}
