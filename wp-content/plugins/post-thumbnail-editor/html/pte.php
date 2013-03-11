<?php
global $post;
function ep(){
   echo PTE_PLUGINURL;
}
?>

<!--
<base href="/wp-admin/"/>
-->
<script type="text/javascript" charset="utf-8">
   var post_id     = <?php echo $post->ID; ?>
     , post_width  = <?php echo $meta['width']; ?>
     , post_height = <?php echo $meta['height']; ?>
</script>
 
<link rel="stylesheet" href="<?php ep() ?>apps/font-awesome/fontawesome.css"/>
<link rel="stylesheet" href="<?php ep() ?>apps/jcrop/css/jquery.Jcrop.css"/>
<style type="text/css" media="all">
   #pte-subtitle {
      font-size: .7em;
      color: #444444;
   }
   #aspect-ratio-selector a,
   .nav-tab-wrapper a {
      cursor: pointer;
   }
   #pte-image { float: left; margin-right: 10px;}
   #pte-thumbnail-column {
      float: left;
      width: 400px;
   }
   #pte-thumbnail-column button {
      float: right;
      margin: 5px;
   }
   /**.pte-thumbnails-menu { display: none; }**/
   .pte-thumbnail-menu {
      font-size: 1.2em;
      line-height: 1.3em;
   }
   td.pte-thumbnail-options {
      width: 50px;
   }
   .pte-thumbnail-menu .icon-save { color: green; }
   .pte-thumbnail-menu .icon-trash { color: red; }
   .pte-thumbnail-menu i:hover,
   .pte-thumbnails-menu i:hover {
      font-size: 1.2em;
   }

   i.disabled {
      color: #aaaaaa;
   }
   tr.selected-true           { background-color: #e0ffe0; }
   tr.selected-true.alternate { background-color: #eaffea; }
   th.center,
   td.center {
      text-align: center;
   }

   #pte-thumbnail-table td {
      line-height: 1.8em;
   }
   #pte-thumbnail-table th {
      line-height: 1.5em;
   }

   #pte-thumbnail-table th input,
   #pte-thumbnail-table td input {
      margin: 1px 0 0;
   }

   #aspect-ratio-selector {
      margin-top: 20px;
      font-size: 1.3em;
   }

   .info-message {
      background-color: #ddddff;
      border: 1px solid blue;
      color: blue;
      font-size: 1.5em;
      margin-bottom: 10px;
      padding: 10px;
      position:relative;
   }
   .info-message .icon-remove {
      font-size: 1.1em;
      position: absolute;
      top: 9px;
      right: 10px;
   }

   .error-message {
      font-size: 1.5em;
      padding: 10px;
      margin-bottom: 10px;
      position:relative;
      border: 1px solid red;
      background-color: #ffdddd;
   }
   .error-message .icon-remove {
      font-size: 1.1em;
      position: absolute;
      top: 9px;
      right: 10px;
   }

   #pte-crop-settings {
      background-color: #f9f9f9;
      border: 1px solid #888888;
      padding: 7px 10px;
   }

   #pte-crop-settings .icon-remove {
      position: absolute;
      top: 12px;
      right: 10px;
      font-size: 12pt;
   }
   #pte-crop-controls {
      text-align: center;
      margin: 10px 0;
   }
   .pte-display-thumbnail-image {
      margin-bottom: 10px;
      position: relative;
   }

   .pte-display-thumbnail-menu {
      font-size: 1.2em;
      float:left;
      margin-right: 3px;
      padding: 2px;
   }

   .no-current-image {
      font-size: 3em;
   }

   .pte-display-thumbnail-image.selected {
      border-width: 5px !important;
      border-style: solid;
      border-color: #cccccc;
   }

   .pte-display-thumbnail-image.modified {
      border: 1px solid green;
      background-color: #ddffdd;
      padding: 10px 5px;
   }
   .pte-display-thumbnail-image.original {
      transition: all 1.5s ease-in-out;
      -webkit-transition: background 1.5s ease-in-out, padding 1.5s ease-in-out;
      -moz-transition: all 1.5s ease-in-out;
   }

   /*** Angular cloak ***/
   [ng\:cloak], [ng-cloak], .ng-cloak {
      display: none;
   }
</style>
<div class="wrap ng-cloak" ng-controller="PteCtrl">
   <?php screen_icon(); ?>
   <h2><?php _e("Post Thumbnail Editor", PTE_DOMAIN);?> &ndash; 
      <span id="pte-subtitle"><?php _e("crop and resize", PTE_DOMAIN); ?></span>
   </h2>
   <div class="subtitle"><?php echo $post->post_title; ?></div>
   <h3 class="nav-tab-wrapper">
      <a ng-href="" ng-class="pageClass('crop')" ng-click="changePage('crop')" class="nav-tab"><?php _e("Crop", PTE_DOMAIN); ?></a>
      <a ng-href="" ng-class="pageClass('view')" ng-click="changePage('view')" class="nav-tab"><?php _e("View", PTE_DOMAIN); ?></a>
   </h3>
   <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-1">
         <div id="post-body-content">
            <div class="error-message" ng-show="errorMessage">
               <i class="icon-remove" ng-click="errorMessage = null"></i>
               <i class="icon-warning-sign"></i>
               {{ errorMessage }}
            </div>
            <div class="info-message" ng-show="infoMessage">
               <i class="icon-remove" ng-click="infoMessage = null"></i>
               <i class="icon-info-sign"></i>
               {{ infoMessage }}
            </div>
            <div class="pte-page-switcher" ng-show="page.crop">
            <div id="pte-image" ng-controller="CropCtrl">
               <img id="pte-preview" src="<?php 
               echo admin_url('admin-ajax.php'); 
               ?>?action=imgedit-preview&amp;_ajax_nonce=<?php
               echo $nonce; 
               ?>&amp;postid=<?php
               echo $post->ID;
               ?>&amp;rand=<?php
               echo rand(1, 99999); // Verify that the image is up to date
               ?>"/>
      
               <div id="pte-crop-controls">
               <a ng-click="toggleOptions()" class="button button-secondary" ng-href=""><?php
                  _e( "Options", PTE_DOMAIN ); ?>
   <i class="icon-caret-down" ng-hide="cropOptions"></i>
   <i class="icon-caret-up" ng-show="cropOptions"></i>
</a>
<a ng-disabled="cropInProgress" class="button button-primary" ng-href="" ng-click="submitCrop()">
<span ng-hide="cropInProgress"><?php _e( "Crop", PTE_DOMAIN ); ?></span>
   <i ng-show="cropInProgress" class="icon-spin icon-spinner"></i>
</a>
               </div>
<div style="position: relative">
   <div id="pte-crop-settings" ng-show="cropOptions">
      <i class="icon-remove" ng-click="toggleOptions()"></i>
      <!--ui-event="{blur : 'aspectRatioBlur()'}"-->
      <label for="pte-aspect-ratio"><?php _e( "Aspect Ratio", PTE_DOMAIN ); ?>: </label>
      <input id="pte-aspect-ratio" type="number" placeholder="<?php _e( "width/height", PTE_DOMAIN ); ?>"
            ng-model="aspectRatio" ng-change="changeAR()"/>
            <!--ng-pattern="aspectRatioPattern"/>-->
      <i class="icon-undo" ng-click="aspectRatio = null"></i>
   </div>
</div>
            </div>
            <div id="pte-thumbnail-column" ng-controller="TableCtrl">
               <table id="pte-thumbnail-table" class="wp-list-table widefat" >
                  <thead>
                     <tr>
                        <th class="center">
                           <input type="checkbox" ng-model="tableSelector" ng-change="toggleAll()"/>
                        </th>
                        <th><?php _e( "Thumbnails" ); ?></th>
                        <th class="center">
                           <span class="pte-thumbnails-menu">
                              <i ng-show="anyProposed()" 
                                 ng-click="save(thumbnails)"
                                 id="pte-save-all"
                                 title="<?php _e( "Save all", PTE_DOMAIN ); ?>"
                                 class="icon-save"></i>
                              <i ng-show="anyProposed()" 
                                 ng-click="trashAll()"
                                 id="pte-reset-all"
                                 title="<?php _e( "Reset all", PTE_DOMAIN ); ?>"
                                 class="icon-trash"></i>
                              <i ng-click="view(anyProposed());"
                                 id="pte-view-modified" 
                                 title="<?php _e( 'View all/modified', PTE_DOMAIN ); ?>" 
                                 class="icon-search"></i>
                           </span>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr ng-class="'selected-'+thumbnail.selected" 
                           ng-click="toggleSelected(thumbnail)"
                           ng-class-odd="'alternate'" 
                           ng-repeat="thumbnail in thumbnails">
                        <td class="center">
                           <input type="checkbox" ng-model="thumbnail.selected" ng-change="updateSelected()"/>
                        </td>
                        <td>{{ thumbnail.name }}</td>
                        <td class="center pte-thumbnail-options">
                           <span class="pte-thumbnail-menu">
                              <i ng-show="thumbnail.proposed" 
                                 ng-click="save([thumbnail])"
                                 title="<?php _e( "Save", PTE_DOMAIN ); ?>" class="icon-save"></i>
                              <i ng-show="thumbnail.proposed" 
                                 ng-click="trash(thumbnail)"
                                 title="<?php _e( "Reset", PTE_DOMAIN ); ?>" class="icon-trash"></i>
                              <i ng-show="thumbnail.proposed" 
                                 ng-click="changePage('view'); view(thumbnail.name);" 
                                 title="<?php _e( "Compare/View", PTE_DOMAIN ); ?>" class="icon-search"></i>
                           </span>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <div id="aspect-ratio-selector">
                  <?php _e( "These thumbnails have an aspect ratio set:", PTE_DOMAIN ); ?>
                  <ul>
                     <li ng-repeat="aspectRatio in aspectRatios | orderBy:size">
                        <a ng-click="selectAspectRatio(aspectRatio)" ng-href="">
                           <i class="icon-ok"></i>
                           {{ aspectRatio.thumbnails.toString().replace(",",", ") }}</a></li>
                  </ul>
               </div>
            </div>
            </div>
            <div class="pte-page-switcher" ng-show="page.view" ng-controller="ViewCtrl">
               <div class="pte-display-thumbnail" 
                     ng-repeat="thumbnail in thumbnails | filter:viewFilterFunc | orderBy:orderBy">
                  <div class="pte-display-thumbnail-image" ng-class="thumbnailClass(thumbnail)">
                     <div class="pte-display-thumbnail-menu" ng-show="thumbnail.proposed">
                        <button ng-click="thumbnail.showProposed = !thumbnail.showProposed"><i class="icon-refresh"></i></button>
                        <br/>
                        <button ng-click="save([thumbnail])" ng-show="thumbnail.showProposed"><i class="icon-save"></i></button>
                        <br/>
                        <button ng-click="trash(thumbnail)" ng-show="thumbnail.showProposed"><i class="icon-trash"></i></button>
                     </div>
                     <div 
                        ng-dblclick="changePage('crop');event.stopPropagation();"
                        ng-click="thumbnail.selected = !thumbnail.selected;updateSelected();" 
                        ng-hide="thumbnail.showProposed">
                        <span ng-show="thumbnail.proposed"><strong><?php _e( "Original", PTE_DOMAIN ); ?>: {{ thumbnail.name }}</strong><br/></span>
                        <img ng-src="{{ thumbnail.current.url | randomizeUrl }}" 
                              ng-show="thumbnail.current"
                              alt="{{ thumbnail.name }}" 
                              title="{{ thumbnail.name }}"/>
                        <span class="no-current-image" ng-hide="thumbnail.current">
                           <i class="icon-exclamation-sign"></i>
                           <?php _e( "No image has been generated yet for image: ", PTE_DOMAIN ) ?> '{{ thumbnail.name }}'
                        </span>
                     </div>
                     <div
                        ng-dblclick="changePage('crop');event.stopPropagation();"
                        ng-click="thumbnail.selected = !thumbnail.selected;updateSelected();"
                        ng-show="thumbnail.showProposed">
                        <span><strong><?php _e( "Proposed", PTE_DOMAIN ); ?>: {{ thumbnail.name }}</strong><br/></span>
                              <!--ng-click="selectThumb(thumbnail)"-->
                        <img ng-src="{{ thumbnail.proposed.url | randomizeUrl }}" 
                              ng-show="thumbnail.showProposed"
                              alt="{{ thumbnail.name }}" 
                              title="{{ thumbnail.name }}"/>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
               <script src="<?php ep(); ?>apps/requirejs/require.js" data-main="<?php 
               ep();
    $options = pte_get_options();

    if ( $options['pte_debug'] ){
       print "js";
    }
    else {
       print "js-build";
    }

?>/main"></script>
