<?php

namespace Concrete5\DropBox\Search\UploadedFile\Field;

use Concrete\Core\Entity\User\User;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete5\DropBox\Search\UploadedFile\Field\Field\PrimaryIdentifierField;
use Concrete5\DropBox\Search\UploadedFile\Field\Field\FileIdentifierField;
use Concrete5\DropBox\Search\UploadedFile\Field\Field\FileField;
use Concrete5\DropBox\Search\UploadedFile\Field\Field\OwnerField;
use Concrete5\DropBox\Search\UploadedFile\Field\Field\CreatedAtField;
use DateTime;

class Manager extends FieldManager
{
    /**
     * @param UploadedFile $mixed
     * @return string
     */
    public function getOwner($mixed)
    {
        $owner = $mixed->getOwner();

        if ($owner instanceof User) {
            return $owner->getUserName();
        }

        return '';
    }
    /**
     * @param UploadedFile $mixed
     * @return string
     */
    public function getCreatedAt($mixed)
    {
        $createdAt = $mixed->getCreatedAt();

        if ($createdAt instanceof DateTime) {
            $app = Application::getFacadeApplication();
            /** @var Date $dateHelper */
            $dateHelper = $app->make(Date::class);
            return $dateHelper->formatDateTime($createdAt);
        }

        return '';
    }
    
    /**
     * @param UploadedFile $mixed
     * @return string
     */
    public function getFile($mixed)
    {
        $file = $mixed->getFile();
        
        if ($file instanceof File) {
            $approvedVersion = $file->getApprovedVersion();
            
            if ($approvedVersion instanceof Version) {
                return $approvedVersion->getFileName();
            }
        }
        
        return '';
    }
    
    public function __construct()
    {
        $properties = [
            new PrimaryIdentifierField(),
            new FileIdentifierField(),
            new FileField(),
            new OwnerField(),
            new CreatedAtField(),
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
