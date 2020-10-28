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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo t("Success"); ?>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>
                    <?php echo $uploadCompleteResponse; ?>
                </p>

                <?php if ((int)$displayUrlToUploadedFile === 1) { ?>
                    <h3>
                        <?php echo t("List of uploaded files"); ?>
                    </h3>

                    <ul class="drop-box-file-list">

                    </ul>
                <?php } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo t("Close"); ?>
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