<?php

namespace Concrete5\DropBox\Entity;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="`UploadedFile`")
 */
class UploadedFile
{
    /**
     * @ORM\Id
     * @var string
     * @ORM\Column(name="`primaryIdentifier`", type="guid", nullable=true)
     */
    protected $primaryIdentifier = '';
    
    /**
     * @ORM\Id
     * @var string
     * @ORM\Column(name="`fileIdentifier`", type="guid", nullable=true)
     */
    protected $fileIdentifier = '';
    
    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", onDelete="CASCADE", nullable=true)
     */
    protected $file;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", nullable=true)
     */
    protected $owner;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @return string
     */
    public function getPrimaryIdentifier()
    {
        return $this->primaryIdentifier;
    }

    /**
     * @param string $primaryIdentifier
     */
    public function setPrimaryIdentifier($primaryIdentifier)
    {
        $this->primaryIdentifier = $primaryIdentifier;
    }

    /**
     * @return string
     */
    public function getFileIdentifier()
    {
        return $this->fileIdentifier;
    }

    /**
     * @param string $fileIdentifier
     */
    public function setFileIdentifier($fileIdentifier)
    {
        $this->fileIdentifier = $fileIdentifier;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
