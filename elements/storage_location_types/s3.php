<?php

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete5\DropBox\File\StorageLocation\Configuration\S3Configuration;

/** @var S3Configuration $configuration */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<?php if (is_object($configuration)) { ?>

    <fieldset>
        <legend>
            <?php echo t("General Settings"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("fslType[bucket]", t("Bucket")); ?>
            <?php echo $form->text('fslType[bucket]', $configuration->bucket); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("fslType[region]", t("Region")); ?>
            <?php echo $form->text('fslType[region]', $configuration->region, ['placeholder' => 'us-east-1']); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("fslType[base_url]", t("Alternate Host")); ?>
            <?php echo $form->text('fslType[base_url]', $configuration->base_url, ['placeholder' => 'Optional. E.g. http://s3.example.com']); ?>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('fslType[useIAM]', 1, $configuration->useIAM); ?>
                    <?php echo t("Use IAM Roles"); ?>
                </label>
            </div>
        </div>
    </fieldset>

    <div id="credentials">
        <fieldset>
            <legend>
                <?php echo t("Credentials"); ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label("fslType[key]", t("Key")); ?>
                <?php echo $form->text('fslType[key]', $configuration->key); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("fslType[secret]", t("Secret")); ?>
                <?php echo $form->text('fslType[secret]', $configuration->secret); ?>
            </div>
        </fieldset>
    </div>

    <script>
        $("input[name='fslType[useIAM]']").change(function() {
            if ($(this).is(":checked")) {
                $("#credentials").addClass("hidden");
            } else {
                $("#credentials").removeClass("hidden");
            }
        }).trigger("change");
    </script>
<?php } ?>