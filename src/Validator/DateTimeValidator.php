<?php

namespace Concrete5\DropBox\Validator;

use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;
use DateTime;

class DateTimeValidator extends AbstractTranslatableValidator
{
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if (!$mixed instanceof DateTime) {
            $error[] = t('The given date is not valid.');
            return false;
        } else {
            return true;
        }
    }
}