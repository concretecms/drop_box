<?php

namespace Concrete5\DropBox\Entity\Search;

use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`SavedUploadedFileSearchQueries`")
 */
class SavedUploadedFileSearch extends SavedSearch
{
    /**
    * @var integer
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(name="`id`", type="integer", nullable=true)
    */
    protected $id;
    
}
