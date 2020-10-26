<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Package\DropBox\Controller\Dialog\UploadedFile\Bulk\Delete;
use Concrete5\DropBox\Entity\UploadedFile;

/** @var Delete $controller */
/** @var UploadedFile[] $uploadedFileEntries */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<p>
    <?php echo t('Are you sure you would like to delete the following uploaded files?'); ?>
</p>

<form method="post" data-dialog-form="save-file-set" action="<?php echo h($controller->action('submit')); ?>">

    <?php echo $token->output("bulk_delete_uploaded_file_entries"); ?>

    <?php foreach ($uploadedFileEntries as $uploadedFileEntry) { ?>
        <?php echo $form->hidden("uploadedFileEntries[]", $uploadedFileEntry->getPrimaryIdentifier() . "_" . $uploadedFileEntry->getFileIdentifier()); ?>
    <?php } ?>

    <div class="ccm-ui">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>
                    <?php echo t('Primary Identifier') ?>
                </th>

                <th>
                    <?php echo t('File Identifier') ?>
                </th>

                <th>
                    <?php echo t('File') ?>
                </th>

                <th>
                    <?php echo t('Owner') ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($uploadedFileEntries as $uploadedFileEntry) { ?>
                <tr>
                    <td>
                        <?php echo $uploadedFileEntry->getPrimaryIdentifier(); ?>
                    </td>

                    <td>
                        <?php echo $uploadedFileEntry->getFileIdentifier(); ?>
                    </td>

                    <td>
                        <?php
                        $fileName = "";

                        if ($uploadedFileEntry->getFile() instanceof File) {
                            $approvedVersion = $uploadedFileEntry->getFile()->getApprovedVersion();

                            if ($approvedVersion instanceof Version) {
                                $fileName = $approvedVersion->getFileName();
                            }
                        }

                        echo $fileName;
                        ?>
                    </td>

                    <td>
                        <?php
                        $userName = "";

                        if ($uploadedFileEntry->getOwner() instanceof \Concrete\Core\Entity\User\User) {
                            $userName = $uploadedFileEntry->getOwner()->getUserName();
                        }

                        echo $userName;
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-left" data-dialog-action="cancel">
            <?php echo t('Cancel'); ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-right">
            <?php echo t('Delete'); ?>
        </button>
    </div>

</form>