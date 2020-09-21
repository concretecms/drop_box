<?php

namespace Concrete5\DropBox\Validator;

use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;
use InvalidArgumentException;

class GuidValidator extends AbstractTranslatableValidator
{
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }

        if (!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($mixed))) {
            $error[] = t('The given guid is not valid.');
            return false;
        } else {
            return true;
        }
    }
}