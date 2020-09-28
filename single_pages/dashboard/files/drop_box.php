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

use Concrete5\DropBox\Search\UploadedFile\Result\Result;
use Concrete\Core\Support\Facade\Url;

/** @var Result|null $result */

?>

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
                        <a href="<?php echo Url::to("/dashboard/files/drop_box/edit"); ?>/<%=item.primaryIdentifier%>/<%=item.fileIdentifier%>">
                            <?php echo t("Edit"); ?>
                        </a>
                    </li>
                    
                    <li>
                        <a href="<?php echo Url::to("/dashboard/files/drop_box/remove"); ?>/<%=item.primaryIdentifier%>/<%=item.fileIdentifier%>">
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
        <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
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
