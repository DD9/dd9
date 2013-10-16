<?php

//support moving wp-config.php as described here http://codex.wordpress.org/Hardening_WordPress#Securing_wp-config.php
$wp_config_path = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($wp_config_path . DIRECTORY_SEPARATOR . "wp-config.php")) {
    include_once($wp_config_path . DIRECTORY_SEPARATOR . "wp-config.php");
} elseif (file_exists(dirname($wp_config_path) . DIRECTORY_SEPARATOR . "wp-config.php")) {
    include_once (dirname($wp_config_path)) . DIRECTORY_SEPARATOR . "wp-config.php";
} elseif (file_exists('/usr/share/wordpress/wp-config.php')) {
    include_once('/usr/share/wordpress/wp-config.php');
} else {
    die("wp-config.php could not be found.");
}
?>
