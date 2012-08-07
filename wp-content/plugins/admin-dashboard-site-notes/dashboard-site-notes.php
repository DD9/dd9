<?php
/*
Plugin Name: Dashboard Site Notes
Plugin URI: http://innerdvations.com/plugins/admin-dashboard-notes/
Description: Create site notes to appear either on the dashboard or at the top of various admin pages.
Version: 1.3.2
Author: Ben Irvin
Author URI: http://innerdvations.com/
Tags: instructions, manual, admin, notes, notices, instruction manual
Wordpress URI: http://wordpress.org/extend/plugins/admin-dashboard-site-notes/
License: GPLv3
Text Domain: dsnmanager
*/

function dsn_init_manager() {
	// add our shortcode
	if(defined('DSN_SHORTCODE') && strlen(DSN_SHORTCODE)) {
		add_shortcode( DSN_SHORTCODE, 'dsn_shortcode_handler' );
	}
	else {
		add_shortcode( 'sitenote', 'dsn_shortcode_handler' );
	}
	
	// DSNManager is always initialized on admin pages
	if(is_admin()) {
		require_once('class.DSNManager.php');
		$dsnmanager = new DSNManager(plugin_basename(__FILE__));
	}
}
add_action('init','dsn_init_manager', 1);

function dsn_shortcode_handler($atts=array(), $content='') {
	// don't initalize DSNManager unless the shortcode is actually used
	// verify that this id has permission to be used in a shortcode
	if(isset($atts['id']) && get_post_meta($atts['id'], '_dsn_shortcodable', true) == true) {
		require_once('class.DSNManager.php');
		$dsnmanager = new DSNManager(plugin_basename(__FILE__));
		return $dsnmanager->shortcode($atts, $content);
	}
	
	return $content;
}