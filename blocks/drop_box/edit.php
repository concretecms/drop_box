<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Support\Facade\Url;

?>

<div class="alert alert-warning">
    <?php echo t(
            "This block type has no settings. If you want to manage global drop box settings please click %shere%s",
            "<a href=\"" . Url::to("/dashboard/system/files/drop_box_settings") . "\">",
            "</a>"
    ); ?>
</div>
