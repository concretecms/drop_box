<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var string|null $uploadCompleteResponse */
/** @var int|null $displayUrlToUploadedFile */

$app = Application::getFacadeApplication();
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var Form $form */
$form = $app->make(Form::class);
?>

<div class="form-group">
    <?php echo $form->label("uploadCompleteResponse", t("Upload Complete Response")); ?>
    <?php echo $editor->outputStandardEditor("uploadCompleteResponse", $uploadCompleteResponse ?? ''); ?>
</div>

<div class="form-group">
    <div class="form-check">
        <?php echo $form->checkbox("displayUrlToUploadedFile", 1, ((int)($displayUrlToUploadedFile ?? 0) === 1), ["class" => "form-check-input"]); ?>
        <?php echo $form->label("displayUrlToUploadedFile", t("Display URL to Uploaded File"), ["class" => "form-check-label"]); ?>
    </div>
</div>

<div class="form-group">
    <div class="help-block">
        <?php echo t(
            "If you want to manage the global drop box settings please click %shere%s",
            "<a href=\"" . (string)Url::to("/dashboard/system/files/drop_box_settings") . "\">",
            "</a>"
        ); ?>
    </div>
</div>
