<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;

/** @var string|null $uploadCompleteResponse */
/** @var int|null $displayUrlToUploadedFile */

$app = Application::getFacadeApplication();
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
$dropBoxId = "drop-box-" . $idHelper->getString("16");
$dropBoxModalId = "drop-box-modal-" . $idHelper->getString("16");
?>

<div class="modal" tabindex="-1" role="dialog" id="<?php echo $dropBoxModalId; ?>">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mb-2 mt-3">
                <h5 class="modal-title">
                    <?php echo t("Upload Complete"); ?>
                </h5>
            </div>

            <div class="modal-body">
                <?php echo $uploadCompleteResponse; ?>

                <?php if ((int)$displayUrlToUploadedFile === 1) { ?>
                    <h4>
                        <?php echo t("Download URL(s)"); ?>
                    </h4>

                    <ul class="drop-box-file-list">

                    </ul>

                    <div class="alert alert-info"><?=t('Files uploaded to the drop box will be purged after 45 days.')?></div>

                <?php } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-round" data-dismiss="modal">
                    <?php echo t("Close Window"); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="<?php echo h($dropBoxId); ?>" class="drop-box"></div>

<script>
    (function ($) {
        $(function () {
            $("#<?php echo $dropBoxId; ?>").dropBox(<?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode([
                "displayUrlToUploadedFile" => (int)$displayUrlToUploadedFile === 1,
                "modalSelector" => "#" . $dropBoxModalId
            ]); ?>);
        });
    })(jQuery);
</script>