<?php

defined('C5_EXECUTE') or die('Access denied');

?>

<div id="ccm-search-results-table">
    <table class="ccm-search-results-table" data-search-results="pages">
        <thead>
        <tr>
            <th><div style="width: 41px"></div></th>
            <th><?=t('File')?></th>
            <th class="ccm-results-list-active-sort-desc"><a href="#"><?=t('Date Added')?></a></th>
            <th><?=t('Size')?></th>
            <th><?=t('Uploaded By')?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        /**
         * @var $uploadedFiles \Concrete5\DropBox\Entity\UploadedFile[]
         */
        foreach($uploadedFiles as $entry) {
            $file = $entry->getFile();
            $uploader = $entry->getOwner(); ?>
           <tr data-details-url="javascript:void(0)">
               <td class="ccm-search-results-icon">
                   <?php echo $file->getListingThumbnailImage() ?>
               </td>
               <td class="ccm-search-results-name"><?=$file->getFileName()?></td>
               <td><?=$entry->getCreatedAt()->format('F d Y, g:i a')?></td>
               <td><?=$file->getSize()?></td>
               <td><?=$uploader->getUserName()?></td>
               <td class="ccm-search-results-menu-launcher">
                   <div class="dropdown" data-menu="search-result">

                       <button class="btn btn-icon" data-boundary="viewport" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                           <svg width="16" height="4">
                               <use xlink:href="#icon-menu-launcher"></use>
                           </svg>
                       </button>

                       <div class="dropdown-menu"><a class="dropdown-item" href="#" data-tree-action="edit-node" dialog-title="Edit Folder" data-tree-action-url="http://brandcentral.test/index.php/ccm/system/dialogs/tree/node/edit/file_folder?treeNodeID=20">Download File</a><div class="dropdown-divider"></div><a class="dropdown-item" href="#" data-tree-action="delete-node" dialog-title="Delete Folder" data-tree-action-url="http://brandcentral.test/index.php/ccm/system/dialogs/tree/node/delete?treeNodeID=20">Delete</a></div>                                </div>
               </td>           </tr>

        <?php } ?>

        </tbody>
    </table>
</div>
