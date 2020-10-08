<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var array $storageLocationList */
/** @var int $storageLocation */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<form action="#" method="post">
    <?php echo $token->output("save_drop_box_settings"); ?>

    <div class="form-group">
        <?php echo $form->label("storageLocation", t("File Storage Location")); ?>
        <?php echo $form->select('storageLocation', $storageLocationList, $storageLocation); ?>
    </div>

    <div class="help-block">
        <?php echo t(
            "If you want to upload larger files it is recommend use a S3 Storage. Go to the dashboard page %sFile Storage Locations%s to create one..",
            "<a href=\"" . Url::to("/dashboard/system/files/storage") . "\">",
            "</a>"
        ); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <button type="submit" class="btn btn-primary">
                    <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>