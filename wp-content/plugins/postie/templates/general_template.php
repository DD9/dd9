<?php

$postie_default = '<a href="{FILELINK}">{ICON} {FILENAME}</a>';

$simple_link = '<a href="{FILELINK}">{FILENAME}</a>';

$custom = "";
if (isset($config)) {
    $custom = (array_key_exists('GENERALTEMPLATE', $config) ? $config['GENERALTEMPLATE'] : "");
}

$generalTemplates = compact('postie_default', 'simple_link', 'custom');
?>
