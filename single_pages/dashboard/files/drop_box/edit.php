<?php

/**
 *
 * This file was build with the Entity Designer add-on.
 *
 * https://www.concrete5.org/marketplace/addons/entity-designer
 *
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Application\Service\FileManager;

/** @var $entry UploadedFile */
/** @var $form Form */
/** @var $token Token */

$app = Application::getFacadeApplication();
/** @var $fileManager FileManager */
$fileManager = $app->make(FileManager::class);
/** @var $userSelector UserSelector */
$userSelector = $app->make(UserSelector::class);
/** @var $dateTime DateTime */
$dateTime = $app->make(DateTime::class);

?>
<form action="#" method="post">
    <?php echo $token->output("save_drop_box_entity"); ?>
    
    <div class="form-group">
        <?php echo $form->label(
            "primaryIdentifier",
            t("Primary Identifier"),
            [
                "class" => "control-label"
            ]
        ); ?>
        
        <?php echo $form->text(
            "primaryIdentifier",
            $entry->getPrimaryIdentifier(),
            [
                "class" => "form-control",
                "max-length" => "255",
            ]
        ); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form->label(
            "fileIdentifier",
            t("File Identifier"),
            [
                "class" => "control-label"
            ]
        ); ?>
        
        <?php echo $form->text(
            "fileIdentifier",
            $entry->getFileIdentifier(),
            [
                "class" => "form-control",
                "max-length" => "255",
            ]
        ); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form->label(
            "file",
            t("File"),
            [
                "class" => "control-label"
            ]
        ); ?>
        
        <?php echo $fileManager->file("file", "file", t('Please choose'), $entry->getFile()); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form->label(
            "owner",
            t("Owner"),
            [
                "class" => "control-label"
            ]
        ); ?>
        
        <?php echo $userSelector->selectUser(
            "owner",
            $entry->getOwner() instanceof \Concrete\Core\Entity\User\User ? $entry->getOwner()->getUserID() : null
        ); ?>
    </div>
    
    <div class="form-group">
        <?php echo $form->label(
            "created_at",
            t("Created At"),
            [
                "class" => "control-label"
            ]
        ); ?>
        
        <?php echo $dateTime->datetime(
            "created_at",
            $entry->getCreatedAt()
        ); ?>
    </div>
    
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/files/drop_box"); ?>" class="btn btn-default">
            <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
        </a>
        
        <div class="pull-right">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
            </button>
        </div>
    </div>
</form>
