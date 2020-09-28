<?php

namespace Concrete5\DropBox\Form\Service;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete5\DropBox\Validator\DateTimeValidator;
use Concrete5\DropBox\Validator\FileValidator;
use Concrete5\DropBox\Validator\GuidValidator;
use Concrete\Core\File\ValidationService;
use Concrete\Core\Form\Service\Validation as CoreValidation;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validator\String\EmailValidator;
use stdClass;

class Validation extends CoreValidation
{
    const VALID_GUID = 32;
    const VALID_DATE_TIME = 33;
    const VALID_FILE = 34;

    public function addRequiredGuid($field, $errorMsg = null)
    {
        $obj = new stdClass();
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = self::VALID_GUID;
        $this->fields[] = $obj;
    }

    public function addRequiredDateTime($field, $errorMsg = null)
    {
        $obj = new stdClass();
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = self::VALID_DATE_TIME;
        $this->fields[] = $obj;
    }

    public function addRequiredFile($field, $errorMsg = null)
    {
        $obj = new stdClass();
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = self::VALID_FILE;
        $this->fields[] = $obj;
    }

    public function test()
    {
        $app = Application::getFacadeApplication();
        /** @var GuidValidator $guidValidator */
        $guidValidator = $app->make(GuidValidator::class);
        /** @var FileValidator $fileValidator */
        $fileValidator = $app->make(FileValidator::class);
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        /** @var DateTimeValidator $dateTimeValidator */
        $dateTimeValidator = $app->make(DateTimeValidator::class);
        /** @var Strings $stringValidator */
        $stringValidator = $app->make(Strings::class);
        /** @var Numbers $numberValidator */
        $numberValidator = $app->make(Numbers::class);
        /** @var ValidationService $uploadedFileValidator */
        $uploadedFileValidator = $app->make(ValidationService::class);
        /** @var Token $tokenValidator */
        $tokenValidator = $app->make(Token::class);
        /** @var EmailValidator $emailValidator */
        $emailValidator = $app->make(EmailValidator::class);

        foreach ($this->fields as $field) {
            $validate = $field->validate;
            $fieldName = isset($field->field) ? $field->field : null;
            $fieldValue = isset($this->data[$fieldName]) ? $this->data[$fieldName] : null;

            switch ($validate) {
                case self::VALID_GUID:
                    if (!$guidValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_FILE:
                    if (!$fileValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_DATE_TIME:
                    $dateValue = $dateTime->translate($fieldName, $this->data, true);

                    if (!$dateTimeValidator->isValid($dateValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_NOT_EMPTY:
                    if (!$stringValidator->notempty($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_TOKEN:
                    if (!$tokenValidator->validate($field->value)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_INTEGER:
                    if ((!$numberValidator->integer($fieldValue)) && ($stringValidator->notempty($fieldValue))) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_INTEGER_REQUIRED:
                    if (!$numberValidator->integer($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_IMAGE:
                    if ((!$uploadedFileValidator->image($this->files[$fieldName]['tmp_name'])) && ($this->files[$fieldName]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_IMAGE_REQUIRED:
                    if (!$uploadedFileValidator->image($this->files[$fieldName]['tmp_name'])) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_FILE:
                    if ((!$uploadedFileValidator->file($this->files[$fieldName]['tmp_name'])) && ($this->files[$fieldName]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_UPLOADED_FILE_REQUIRED:
                    if (!$uploadedFileValidator->file($this->files[$fieldName]['tmp_name'])) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;

                case self::VALID_EMAIL:
                    if (!$emailValidator->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $field;
                    }

                    break;
            }
        }

        $this->setErrorsFromInvalidFields();

        return count($this->fieldsInvalid) == 0;
    }

}