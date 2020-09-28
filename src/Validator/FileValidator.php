<?php

namespace Concrete5\DropBox\Validator;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;
use InvalidArgumentException;

class FileValidator extends AbstractTranslatableValidator
{
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }

        $file = File::getByID($mixed);

        if (!$file instanceof FileEntity) {
            $error[] = t('The given file is not valid.');
            return false;
        } else {
            return true;
        }
    }
}