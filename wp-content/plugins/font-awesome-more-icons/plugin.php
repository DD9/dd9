<?php
/*
Plugin Name: Font Awesome More Icons
Plugin URI: http://blog.webguysaz.com/font-awesome-more-icons-wordpress-plugin/
Description: Easily use the Font Awesome icons in WordPress but with MORE icons and MORE features using HTML, shortcodes, or TinyMCE plugin.
Version:  3.5
Author: Web Guys
Author URI: http://webguysaz.com
Author Email: jeremy@webguysaz.com
Credits:
    The Font Awesome icon set was created by Dave Gandy (dave@davegandy.com)
     http://fortawesome.github.com/Font-Awesome/
    The Font Awesome More (Fontstrap) icon extension sets created by Greg Loucas (me@gregoryloucas.com)
     http://gregoryloucas.github.io/Font-Awesome-More/
    Original Font Awesome plugin by Rachel Baker (rachel@rachelbaker.me)
     http://rachelbaker.me/font-awesome-icons-wordpress-plugins/
*/

class FontAwesomeMore {
    private static $instance;
    const VERSION = ' 3.5';

    private static function has_instance() {
        return isset(self::$instance) && self::$instance != null;
    }

    public static function get_instance() {
        if (!self::has_instance())
            self::$instance = new FontAwesomeMore;
        return self::$instance;
    }

    public static function setup() {
        self::get_instance();
    }

    protected function __construct() {
        if (!self::has_instance()) {
            add_action('init', array(&$this, 'init'));
        }
    }

    public function init() {
        add_action('wp_enqueue_scripts', array(&$this, 'register_plugin_styles'));
        add_action('admin_enqueue_scripts', array(&$this, 'register_plugin_styles'));
        add_shortcode('icon', array($this, 'setup_shortcode'));
        add_filter('widget_text', 'do_shortcode');

        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) &&
                get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array(&$this, 'register_tinymce_plugin'));
            add_filter('mce_buttons', array(&$this, 'add_tinymce_buttons'));
            add_filter('mce_css', array(&$this, 'add_tinymce_editor_sytle'));
        }
    }

    public function register_plugin_styles() {
        global $wp_styles;
        $protocol = empty($_SERVER['HTTPS']) ? 'http:' : 'https:';

        wp_enqueue_style( 'font-awesome-styles', $protocol . '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', array(), self::VERSION, 'all');
        wp_enqueue_style( 'font-awesome-corp-styles', plugins_url( 'assets/css/font-awesome-corp.css', __FILE__  ), array(), self::VERSION, 'all');
        wp_enqueue_style( 'font-awesome-ext-styles', plugins_url( 'assets/css/font-awesome-ext.css', __FILE__  ), array(), self::VERSION, 'all');
        wp_enqueue_style( 'font-awesome-social-styles', plugins_url( 'assets/css/font-awesome-social.css', __FILE__  ), array(), self::VERSION, 'all');
        wp_enqueue_style( 'font-awesome-more-ie7', plugins_url( 'assets/css/font-awesome-more-ie7.min.css', __FILE__ ), array(), self::VERSION, 'all');
        $wp_styles->add_data( 'font-awesome-more-ie7', 'conditional', 'lte IE 7' );
    }

    public function setup_shortcode( $params ) {
        extract( shortcode_atts( array(
                    'name'      => '',
                    'title'     => '',
                    'size'      => '',
                    'space'     => ''
                ), $params ) );

        $icon_title = $title ? 'title="' . $title . '" ' : '';
        $space      = $space == 'false' ? '' : '&nbsp;';
        $name       = self::famPrefix($name);
        $size       = self::famPrefix($size);

        $icon = '<i class="' . $name . ' ' . $size . '" ' . $icon_title . '>' . $space . '</i>';

        return $icon;
    }

    private function famPrefix($item){
        if(stripos($item, 'icon-') === false){
            $item = 'icon-' . $item;
        }
        return $item;
    }

    public function register_tinymce_plugin($plugin_array) {
        $plugin_array['font_awesome_more_glyphs'] = plugins_url('assets/js/font-awesome-more.js', __FILE__);

        return $plugin_array;
    }

    public function add_tinymce_buttons($buttons) {
        array_push($buttons, '|', 'fontAwesomeMoreGlyphSelect');

        return $buttons;
    }

    public function add_tinymce_editor_sytle($mce_css) {
        $mce_css .= ', ' . plugins_url('assets/css/admin/editor_styles.css', __FILE__);

        return $mce_css;
    }

}

FontAwesomeMore::setup();
