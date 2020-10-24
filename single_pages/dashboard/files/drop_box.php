<?php

/**
 *
 * This file was build with the Entity Designer add-on.
 *
 * https://www.concrete5.org/marketplace/addons/entity-designer
 *
 */

defined('C5_EXECUTE') or die('Access denied');

/** @noinspection DuplicatedCode */

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\MenuInterface;
use Concrete5\DropBox\Entity\UploadedFile;
use Concrete5\DropBox\Menu;
use Concrete5\DropBox\Search\UploadedFile\Result\Column;
use Concrete5\DropBox\Search\UploadedFile\Result\Item;
use Concrete5\DropBox\Search\UploadedFile\Result\ItemColumn;
use Concrete5\DropBox\Search\UploadedFile\Result\Result;
use Concrete\Core\Support\Facade\Url;

/** @var Result|null $result */
/** @var DropdownMenu $resultsBulkMenu */
/** @var MenuInterface $menu */
/** @var Result $result */

?>

<?php if (version_compare(APP_VERSION, "9.0", ">=")): ?>
    <div id="ccm-search-results-table">
        <table class="ccm-search-results-table" data-search-results="pages">
            <thead>
            <tr>
                <th class="ccm-search-results-bulk-selector">
                    <div class="btn-group dropdown">
                    <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input type="checkbox" data-search-checkbox="select-all"/>
                    </span>

                        <button
                                type="button"
                                disabled="disabled"
                                data-search-checkbox-button="dropdown"
                                class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                data-toggle="dropdown"
                                data-reference="parent">

                            <span class="sr-only">
                                <?php echo t("Toggle Dropdown"); ?>
                            </span>
                        </button>

                        <div data-search-menu="dropdown">
                            <?php echo $resultsBulkMenu->getMenuElement(); ?>
                        </div>
                    </div>
                </th>

                <?php foreach ($result->getColumns() as $column): ?>
                    <?php /** @var Column $column */ ?>
                    <th class="<?php echo $column->getColumnStyleClass() ?>">
                        <?php if ($column->isColumnSortable()): ?>
                            <a href="<?php echo $column->getColumnSortURL() ?>">
                                <?php echo $column->getColumnTitle() ?>
                            </a>
                        <?php else: ?>
                            <span>
                            <?php echo $column->getColumnTitle() ?>
                        </span>
                        <?php endif; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($result->getItems() as $item): ?>
                <?php
                /** @var Item $item */
                /** @var UploadedFile $page */
                $uploadedFileEntry = $item->getItem();
                ?>
                <tr data-details-url="javascript:void(0)">
                    <td class="ccm-search-results-checkbox">
                        <?php if ($uploadedFileEntry instanceof UploadedFile): ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input data-search-checkbox="individual"
                                   type="checkbox"
                                   data-item-id="<?php echo $uploadedFileEntry->getPrimaryIdentifier() ?>_<?php echo $uploadedFileEntry->getFileIdentifier() ?>"/>
                        <?php endif; ?>
                    </td>

                    <?php foreach ($item->getColumns() as $column): ?>
                        <?php /** @var ItemColumn $column */ ?>
                        <td>
                            <?php echo $column->getColumnValue(); ?>
                        </td>
                    <?php endforeach; ?>

                    <?php $menu = new Menu($uploadedFileEntry); ?>

                    <?php if ($menu): ?>
                        <td class="ccm-search-results-menu-launcher">
                            <div class="dropdown" data-menu="search-result">

                                <button class="btn btn-icon"
                                        data-boundary="viewport"
                                        type="button"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">

                                    <svg width="16" height="4">
                                        <use xlink:href="#icon-menu-launcher"/>
                                    </svg>
                                </button>

                                <?php echo $menu->getMenuElement(); ?>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        (function ($) {
            $(function () {
                let searchResultsTable = new window.ConcreteSearchResultsTable($("#ccm-search-results-table"));
                searchResultsTable.setupBulkActions();
            });
        })(jQuery);
    </script>

    <?php echo $result->getPagination()->renderView('dashboard'); ?>

<?php else: ?>
    <?php if (!is_object($result)): ?>
        <div class="alert alert-warning">
            <?php echo t('Currently there are no items available.'); ?>
        </div>
    <?php else: ?>
        <script type="text/template" data-template="search-results-table-body">
            <% _.each(items, function (item) {%>
            <tr data-launch-search-menu="<%=item.primaryIdentifier%>_<%=item.fileIdentifier%>">
                <td class="ccm-search-results-icon">
                    <%=item.resultsThumbnailImg%>
                </td>
                <% for (i = 0; i < item.columns.length; i++) {
                var column = item.columns[i]; %>
                <% if (i == 0) { %>
                <td class="ccm-search-results-name"><%-column.value%></td>
                <% } else { %>
                <td><%-column.value%></td>
                <% } %>
                <% } %>
            </tr>
            <% }); %>
        </script>

        <div data-search-element="wrapper"></div>

        <div data-search-element="results">
            <div class="table-responsive">
                <table class="ccm-search-results-table ccm-search-results-table-icon">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="ccm-search-results-pagination"></div>
        </div>

        <script type="text/template" data-template="search-results-pagination">
            <%=paginationTemplate%>
        </script>

        <script type="text/template" data-template="search-results-menu">
            <div class="popover fade" data-search-menu="<%=item.primaryIdentifier%>_<%=item.fileIdentifier%>">
                <div class="arrow"></div>
                <div class="popover-inner">
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo (string)Url::to("/dashboard/files/drop_box/edit"); ?>/<%=item.primaryIdentifier%>/<%=item.fileIdentifier%>">
                                <?php echo t("Edit"); ?>
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo (string)Url::to("/ccm/drop_box/download"); ?>/<%=item.primaryIdentifier%>/<%=item.fileIdentifier%>" target="_blank">
                                <?php echo t("Download"); ?>
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo (string)Url::to("/dashboard/files/drop_box/remove"); ?>/<%=item.primaryIdentifier%>/<%=item.fileIdentifier%>">
                                <?php echo t("Remove"); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </script>

        <script type="text/template" data-template="search-results-table-head">
            <tr>
                <th>
                    <div class="dropdown">
                        <button class="btn btn-menu-launcher" disabled data-toggle="dropdown"><i
                                    class="fa fa-chevron-down"></i></button>
                    </div>
                </th>
                <%
                for (i = 0; i < columns.length; i++) {
                var column = columns[i];
                if (column.isColumnSortable) { %>
                <th class="<%=column.className%>"><!--suppress HtmlUnknownTarget -->
                    <a href="<%=column.sortURL%>"><%-column.title%></a></th>
                <% } else { %>
                <th><span><%-column.title%></span></th>
                <% } %>
                <% } %>
            </tr>
        </script>

        <script type="text/javascript">
            $(function () {
                $('#ccm-dashboard-content').concreteAjaxSearch(<?php echo json_encode(["result" => $result->getJSONObject()]) ?>);
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
