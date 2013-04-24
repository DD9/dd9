<?php

define('ABSPATH', dirname(dirname(__FILE__)) . '/');
define('WP_PLUGIN_URL', 'localhost');

class wpdb {

    public $t_get_var = "";
    public $terms = 'wp_terms';

    public function get_var($query, $column_offset = 0, $row_offset = 0) {
        if (is_array($this->t_get_var)) {
            if (count($this->t_get_var) > 0) {
                $r = $this->t_get_var[0];
                unset($this->t_get_var[0]);
                $this->t_get_var = array_values($this->t_get_var);
            } else {
                $r = null;
            }
        } else {
            $r = $this->t_get_var;
            $this->t_get_var = "";
        }
        return $r;
    }

}

class WP_Error {
    
}

$wpdb = new wpdb();

function get_option($option, $default = false) {
    return 'open';
}

function get_post_types() {
    return array("post", "page", "custom", "custom1", "Custom2");
}

function current_time() {
    return '2005-08-05 10:41:13';
}

function is_admin() {
    return false;
}

function get_post() {
    $r = new stdClass();
    $r->post_date = '';
    $r->post_parent = 0;
    $r->guid = '7b0d965d-b8b0-4654-ac9e-eeef1d8cf571';
    $r->post_title = '';
    $r->post_excerpt = '';
    return $r;
}

function __($t) {
    return $t;
}

function endsWith($haystack, $needle)
{
    return substr($haystack, -strlen($needle)) == $needle;
}

function wp_check_filetype($filename) {
    if (empty($filename))
        return null;
    if (endsWith($filename, ".png"))
            return array('ext' => 'png', 'type' => 'image/png');
    if (endsWith($filename, ".ics"))
            return array('ext' => 'ics', 'type' => 'text/calendar');
    return array('ext' => 'xxx', 'type' => 'xxx/xxx');
}

function wp_upload_dir() {
    return array(
        'path' => sys_get_temp_dir(),
        'url' => 'http://example.com/upload/',
        'subdir' => sys_get_temp_dir(),
        'basedir' => sys_get_temp_dir(),
        'baseurl' => 'http://example.com/',
        'error' => false
    );
}

function wp_unique_filename() {
    return uniqid("postie");
}

function wp_get_attachment_url() {
    return 'http://example.net/wp-content/uploads/filename';
}

function image_downsize() {
    return array('http://example.net/wp-content/uploads/filename.jpg', 10, 10, true);
}

function image_hwstring() {
    return 'width="10" height="10" ';
}

function get_attachment_link() {
    return 'http://example.net/wp-content/uploads/filename.jpg';
}

function get_user_by() {
    return false;
}

function register_activation_hook() {
    
}

function add_action() {
    
}

function add_filter() {
    
}

function register_deactivation_hook() {
    
}

function apply_filters($filter, $value) {
    return $value;
}

function wp_insert_attachment() {
    return 1;
}

function wp_update_attachment_metadata() {
    
}

function wp_generate_attachment_metadata() {
    
}

function is_wp_error() {
    return false;
}

function sanitize_title($title) {
    return $title;
}

function get_temp_dir() {
    return sys_get_temp_dir();
}

function sanitize_term($s) {
    return trim($s);
}

$g_get_term_by = new stdClass();
$g_get_term_by->term_id = 1;

function get_term_by() {
    global $g_get_term_by;
    return $g_get_term_by;
}

function get_post_format_slugs() {
    return array('standard' => 'standard', 'video' => 'video', 'image' => 'image', 'aside' => 'aside');
}

?>
