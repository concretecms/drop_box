<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;

/** @var string|null $uploadCompleteResponse */
/** @var int|null $displayUrlToUploadedFile */

$app = Application::getFacadeApplication();
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
$id = "drop-box-" . $idHelper->getString("16");
?>

<div id="<?php echo h($id); ?>" class="drop-box"></div>

<script>
    (function ($) {
        $(function () {
            $("#<?php echo $id; ?>").dropBox(<?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode([
                "uploadCompleteResponse" => $uploadCompleteResponse,
                "displayUrlToUploadedFile" => (int)$displayUrlToUploadedFile === 1
            ]); ?>);
        });
    })(jQuery);
</script>