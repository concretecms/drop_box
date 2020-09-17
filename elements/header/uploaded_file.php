<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<?php if (version_compare(APP_VERSION, '8.5.2 ', '>=')): ?>
    <div class="ccm-header-search-form ccm-ui" data-header="uploaded_file-search">
        <form class="form-inline" method="get" action="<?php echo (string)Url::to('/ccm/system/search/uploaded_file/basic') ?>">
            <div class="ccm-header-search-form-input">
                <a class="ccm-header-reset-search" href="#" data-button-action-url="<?php echo (string)Url::to('/ccm/system/search/uploaded_file/clear')?>" data-button-action="clear-search">
                    <?php echo t('Reset Search')?>
                </a>

                <a class="ccm-header-launch-advanced-search" href="<?php echo (string)Url::to('/ccm/system/dialogs/uploaded_file/advanced_search')?>" data-launch-dialog="advanced-search">
                    <?php echo t('Advanced')?>
                </a>

                <?php echo $form->text("cKeywords", ["autocomplete" => "off", "placeholder" => t('Search')]); ?>
            </div>

            <button class="btn btn-info" type="submit">
                <i class="fa fa-search"></i>
            </button>

            <ul class="ccm-header-search-navigation-files ccm-header-search-navigation">
                <li>
                    <a href="<?php echo Url::to("/dashboard/files/drop_box/add"); ?>">
                        <i class="fa fa-plus"></i> <?php echo t('Add Entry'); ?>
                    </a>
                </li>
            </ul>
        </form>
    </div>
<?php else: ?>
    <div class="ccm-header-search-form ccm-ui" data-header="uploaded_file-search">
        <form  method="get" action="<?php echo (string)Url::to('/ccm/system/search/uploaded_file/basic') ?>">
            <div class="input-group">
                <div class="ccm-header-search-form-input">
                    <a class="ccm-header-reset-search" href="#" data-button-action-url="<?php echo (string)Url::to('/ccm/system/search/uploaded_file/clear')?>" data-button-action="clear-search">
                        <?php echo t('Reset Search')?>
                    </a>
                    
                    <a class="ccm-header-launch-advanced-search" href="<?php echo (string)Url::to('/ccm/system/dialogs/uploaded_file/advanced_search')?>" data-launch-dialog="advanced-search">
                        <?php echo t('Advanced')?>
                    </a>
                    
                    <?php echo $form->text("cKeywords", ["autocomplete" => "off", "placeholder" => t('Search')]); ?>
                </div>
                
                <span class="input-group-btn">
                    <button class="btn btn-info" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
            
            <ul class="ccm-header-search-navigation-files ccm-header-search-navigation">
                <li>
                    <a href="<?php echo Url::to("/dashboard/files/drop_box/add"); ?>">
                        <i class="fa fa-plus"></i> <?php echo t('Add Entry'); ?>
                    </a>
                </li>
            </ul>
        </form>
    </div>
<?php endif; ?>
