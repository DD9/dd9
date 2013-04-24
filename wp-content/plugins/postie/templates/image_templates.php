<?php

$simple_link = '<a href="{FILELINK}">{FILENAME}</a>';
$robert_felty = '<div style="margin-right:10px;background:black;color:white;padding:2px; width:{MEDIUMWIDTH};float:left"><a href="{IMAGE}"><img src="{MEDIUM}" alt="{CAPTION}" title="{CAPTION}" class="attachment" /></a><div style="padding:.2em;text-align:left">{CAPTION}</div></div>';
$no_wrappers = '<a href="{IMAGE}"><img src="{THUMBNAIL}" alt="{CAPTION}" title="{CAPTION}" class="attachment" /></a>';
$thumbnail_left = '<div style="float:left;margin-right:10px;"><a href="{IMAGE}"><img src="{THUMBNAIL}" alt="{CAPTION}" title="{CAPTION}" class="attachment" /></a></div>';
$thumbnail_right = '<div style="float:right;margin-left:10px;"><a href="{IMAGE}"><img src="{THUMBNAIL}" alt="{CAPTION}" title="{CAPTION}" class="attachment" /></a></div>';
$wordpress_default = '<div id="attachment_{ID}" class="wp-caption alignleft" style="width: {MEDIUMWIDTH};"><a rel="attachment wp-att-{ID}" href="{PAGELINK}"><img class="size-medium wp-image-{ID}" title="{TITLE}" alt="{CAPTION}" src="{MEDIUM}" /> </a><p class="wp-caption-text">{CAPTION}</p></div>';
$postie_legacy = '<div class="postie-image-div"><a href="{IMAGE}"><img src="{THUMBNAIL}" alt="{FILENAME}" title="{FILENAME}" style="border:none" class="postie-image" /></a></div>';
$custom = isset($config) ? (array_key_exists('IMAGETEMPLATE', $config) ? $config['IMAGETEMPLATE'] : "") : "";
$imageTemplates = compact('simple_link', 'no_wrappers', 'wordpress_default', 'thumbnail_left', 'thumbnail_right', 'robert_felty', 'postie_legacy', 'custom');
?>
