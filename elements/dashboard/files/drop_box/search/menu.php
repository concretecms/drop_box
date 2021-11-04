<?php

use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Support\Facade\Url as UrlFacade;

defined('C5_EXECUTE') or die("Access Denied.");

/** @var $urlHelper Url */
?>

<div class="row row-cols-auto align-items-center">
    <?php if (!empty($itemsPerPageOptions)): ?>
        <div class="dropdown">
            <button
                    type="button"
                    class="btn btn-secondary p-2 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                <span id="selected-option">
                    <?php echo $itemsPerPage; ?>
                </span>
            </button>

            <ul class="dropdown-menu">
                <li class="dropdown-header">
                    <?php echo t('Items per page') ?>
                </li>

                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption): ?>
                    <?php
                    $url = $urlHelper->setVariable([
                        'itemsPerPage' => $itemsPerPageOption
                    ]);
                    ?>

                    <li data-items-per-page="<?php echo $itemsPerPageOption; ?>">
                        <a class="dropdown-item <?php echo ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                           href="<?php echo $url ?>">
                            <?php echo $itemsPerPageOption; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <ul class="ccm-dashboard-header-icons">
        <li>
            <a href="<?php echo UrlFacade::to("/dashboard/files/drop_box/add"); ?>">
                <i class="fas fa-plus"></i> <?php echo t('Add Entry'); ?>
            </a>
        </li>
    </ul>
</div>
