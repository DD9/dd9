<?php

/*
  Plugin Name: Postie
  Plugin URI: http://PostiePlugin.com/
  Description: Signifigantly upgrades the posting by mail features of Word Press (See <a href='options-general.php?page=postie/postie.php'>Settings and options</a>) to configure your e-mail settings. See the <a href='http://wordpress.org/extend/plugins/postie/other_notes'>Readme</a> for usage. Visit the <a href='http://wordpress.org/support/plugin/postie'>postie forum</a> for support.
  Version: 1.5.16
  Author: Wayne Allen
  Author URI: http://allens-home.com/
  License: GPL2
 */

/*  Copyright (c) 2012  Wayne Allen  (email : wayne@allens-home.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*
  $Id: postie.php 772964 2013-09-15 22:53:47Z WayneAllen $
 */

define('POSTIE_VERSION', '1.5.16');
define("POSTIE_ROOT", dirname(__FILE__));
define("POSTIE_URL", WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)));

//register the hooks early in the page in case some method needs the result of one of them (i.e. cron_schedules)
add_action('init', 'disable_kses_content', 20);
add_action('check_postie_hook', 'check_postie');

add_filter('whitelist_options', 'postie_whitelist');
add_filter('cron_schedules', 'postie_more_reccurences');

register_activation_hook(__FILE__, 'activate_postie');
register_activation_hook(__FILE__, 'postie_cron');
register_deactivation_hook(__FILE__, 'postie_decron');

function postie_loadjs_add_page() {
    $postiepage = add_options_page('Postie', 'Postie', 'manage_options', POSTIE_ROOT . '/postie.php', 'postie_loadjs_options_page');
    add_action("admin_print_scripts-$postiepage", 'postie_loadjs_admin_head');
}

function postie_loadjs_options_page() {
    require_once POSTIE_ROOT . '/config_form.php';
}

function postie_loadjs_admin_head() {
    wp_enqueue_script('loadjs', plugins_url('js/simpleTabs.jquery.js', __FILE__));
    echo '<link type="text/css" rel="stylesheet" href="' . plugins_url('css/style.css', __FILE__) . "\"/>\n";
    echo '<link type="text/css" rel="stylesheet" href="' . plugins_url('css/simpleTabs.css', __FILE__) . "\"/>\n";
}

if (isset($_GET["postie_read_me"])) {
    include_once(ABSPATH . "wp-admin/admin.php");
    $title = __("Edit Plugins");
    $parent_file = 'plugins.php';
    include(ABSPATH . 'wp-admin/admin-header.php');
    postie_ShowReadMe();
    include(ABSPATH . 'wp-admin/admin-footer.php');
}
//Add Menu Configuration
if (is_admin()) {
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "postie-functions.php");
    add_action('admin_init', 'postie_admin_settings');
    add_action('admin_menu', 'postie_loadjs_add_page');
    if (function_exists('load_plugin_textdomain')) {

        function postie_load_domain() {
            $plugin_dir = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__));
            load_plugin_textdomain('postie', $plugin_dir . "/languages/", basename(dirname(__FILE__)) . '/languages/');
        }

        add_action('init', 'postie_load_domain');
    }
    postie_warnings();
}

/*
 * called by WP when activating the plugin
 * Note that you can't do any output during this funtion or activation
 * will fail on some systems. This means no DebugEcho, EchoInfo or DebugDump.
 */

function activate_postie() {
    static $init = false;
    $options = config_Read();

    if ($init) {
        return;
    }

    if (!$options) {
        $options = array();
    }
    $default_options = config_GetDefaults();
    $old_config = array();

    $result = config_GetOld();
    if (is_array($result)) {
        foreach ($result as $key => $val) {
            $old_config[strtolower($key)] = $val;
        }
    }

    // overlay the options on top of each other:
    // the current value of $options takes priority over the $old_config, which takes priority over the $default_options
    $options = array_merge($default_options, $old_config, $options);
    $options = config_ValidateSettings($options);
    update_option('postie-settings', $options);
    $init = true;
}

/**
 * set up actions to show relevant warnings, 
 * if mail server is not set, or if IMAP extension is not available
 */
function postie_warnings() {

    $config = config_Read();

    if ((empty($config['mail_server']) ||
            empty($config['mail_server_port']) ||
            empty($config['mail_userid']) ||
            empty($config['mail_password'])
            ) && !isset($_POST['submit'])) {

        function postie_enter_info() {
            echo "<div id='postie-info-warning' class='updated fade'><p><strong>" . __('Postie is almost ready.', 'postie') . "</strong> "
            . sprintf(__('You must <a href="%1$s">enter your email settings</a> for it to work.', 'postie'), "options-general.php?page=postie/postie.php")
            . "</p></div> ";
        }

        add_action('admin_notices', 'postie_enter_info');
    }

    $p = strtolower($config['input_protocol']);
    if (!function_exists('imap_mime_header_decode') && ($p == 'imap' || $p == 'imap-ssl' || $p == 'pop-ssl')) {

        function postie_imap_warning() {
            echo "<div id='postie-imap-warning' class='error'><p><strong>";
            echo __('Warning: the IMAP php extension is not installed. Postie can not use IMAP, IMAP-SSL or POP-SSL without this extension.', 'postie');
            echo "</strong></p></div>";
        }

        add_action('admin_notices', 'postie_imap_warning');
    }
    if ($p == 'pop3' && $config['email_tls']) {

        function postie_tls_warning() {
            echo "<div id='postie-lst-warning' class='error'><p><strong>";
            echo __('Warning: The POP3 connector does not support TLS.', 'postie');
            echo "</strong></p></div>";
        }

        add_action('admin_notices', 'postie_tls_warning');
    }

    if (!function_exists('mb_detect_encoding')) {

        function postie_mbstring_warning() {
            echo "<div id='postie-mbstring-warning' class='error'><p><strong>";
            echo __('Warning: the Multibyte String php extension (mbstring) is not installed. Postie will not function without this extension.', 'postie');
            echo "</strong></p></div>";
        }

        add_action('admin_notices', 'postie_mbstring_warning');
    }

    if (!function_exists('get_user_by')) {
        include ABSPATH . 'wp-includes/pluggable.php';
    }
    $adminuser = get_user_by('login', $config['admin_username']);
    if ($adminuser === false) {

        function postie_adminuser_warning() {
            echo "<div id='postie-mbstring-warning' class='error'><p><strong>";
            echo __('Warning: the Admin username is not a valid WordPress login. Postie may reject emails if this is not corrected.', 'postie');
            echo "</strong></p></div>";
        }

        add_action('admin_notices', 'postie_adminuser_warning');
    }
}

function disable_kses_content() {
    remove_filter('content_save_pre', 'wp_filter_post_kses');
}

function postie_whitelist($options) {
    $added = array('postie-settings' => array('postie-settings'));
    $options = add_option_whitelist($added, $options);
    return $options;
}

//don't use DebugEcho or EchoInfo here as it is not defined when called as an action
function check_postie() {
    //error_log("check_postie");

    $fullurl = plugins_url("get_mail.php", __FILE__);
    preg_match("/https?:\/\/(.[^\/]*)(.*)/i", $fullurl, $matches);
    $host = $matches[1];

    $url = "";
    if (isset($matches[2])) {
        $url = $matches[2];
    }

    $port = is_ssl() ? 443 : 80;

    $fp = fsockopen($host, $port, $errno, $errstr);
    if ($fp) {
        fputs($fp, "GET $url HTTP/1.0\r\n");
        fputs($fp, "User-Agent:  Cronless-Postie\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "\r\n");
        $page = '';
        while (!feof($fp)) {
            $page.=fgets($fp, 128);
        }
        fclose($fp);
    } else {
        error_log("Cannot connect to server on port $port. Please check to make sure that this port is open on your webhost. Additional information: $errno: $errstr");
    }
}

function postie_cron($interval = false) {
    //Do not echo output in filters, it seems to break some installs
    //error_log("postie_cron: setting up cron task: $interval");
    //$schedules = wp_get_schedules();
    //error_log("postie_cron\n" . print_r($schedules, true));

    if (!$interval) {
        $config = config_Read();
        $interval = $config['interval'];
        //error_log("postie_cron: setting up cron task from config: $interval");
    }
    if (!$interval || $interval == '') {
        $interval = 'hourly';
        //error_log("Postie: setting up cron task: defaulting to hourly");
    }
    if ($interval == 'manual') {
        postie_decron();
        //error_log("postie_cron: clearing cron (manual)");
    } else {
        if ($interval != wp_get_schedule('check_postie_hook')) {
            postie_decron(); //remove existing
            //try to create the new schedule with the first run in 5 minutes
            if (false === wp_schedule_event(time() + 5 * 60, $interval, 'check_postie_hook')) {
                //error_log("postie_cron: Failed to set up cron task: $interval");
            } else {
                //error_log("postie_cron: Set up cron task: $interval");
            }
        } else {
            //error_log("postie_cron: OK: $interval");
            //don't need to do anything, cron already scheduled
        }
    }
}

function postie_decron() {
    //error_log("postie_decron: clearing cron");
    wp_clear_scheduled_hook('check_postie_hook');
}

/* here we add some more cron options for how often to check for e-mail */

function postie_more_reccurences($schedules) {
    //Do not echo output in filters, it seems to break some installs
    //error_log("postie_more_reccurences: setting cron schedules");
    $schedules['weekly'] = array('interval' => (60 * 60 * 24 * 7), 'display' => __('Once Weekly'));
    $schedules['twiceperhour'] = array('interval' => 60 * 30, 'display' => __('Twice per hour'));
    $schedules['tenminutes'] = array('interval' => 60 * 10, 'display' => __('Every 10 minutes'));
    $schedules['fiveminutes'] = array('interval' => 60 * 5, 'display' => __('Every 5 minutes'));

    return $schedules;
}

?>