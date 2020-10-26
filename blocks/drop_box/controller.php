<?php

namespace Concrete\Package\DropBox\Block\DropBox;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Form\Service\Validation;

class Controller extends BlockController
{
    protected $btTable = "btDropBox";

    /** @var string|null */
    public $uploadCompleteResponse;
    /** @var int|null */
    public $displayUrlToUploadedFile;

    public function getBlockTypeDescription()
    {
        return t('Add an drop box to a page.');
    }

    public function getBlockTypeName()
    {
        return t('Drop Box');
    }

    public function validate($args)
    {
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        $formValidator->setData($args);
        $formValidator->addRequired("uploadCompleteResponse");
        $formValidator->test();
        return $formValidator->getError();
    }

    public function save($args)
    {
        $args['displayUrlToUploadedFile'] = isset($args['displayUrlToUploadedFile']) ? 1 : 0;

        parent::save($args);
    }
}
