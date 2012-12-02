<?php
/*
Plugin Name: Font Awesome More
Plugin URI: 
Description: Plugin from Rachel Baker, forked by DD9, assets from Font Awesome More
Version: 1.0
Author: DD9
Author URI: 
Author Email: 
Credits:
    The Font Awesome icon set was created by Dave Gandy (dave@davegandy.com)
     http://fortawesome.github.com/Font-Awesome/

Plugin derived from	 
   http://rachelbaker.me/font-awesome-icons-wordpress-plugins/

Assets from
http://gregoryloucas.github.com/Font-Awesome-More/



License:


*/

class FontAwesome
{
    public function __construct()
    {
        add_action( 'init', array( &$this, 'init' ) );
    }

    public function init()
    {
        add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_styles' ) );
        add_shortcode('icon', array( &$this, 'setup_shortcode' ) );
        add_filter('widget_text', 'do_shortcode');
    }

    public function register_plugin_styles()
    {
        wp_enqueue_style( 'font-awesome-styles', plugins_url( 'assets/css/font-awesome.css', __FILE__  ) );
    }

    public function setup_shortcode($params)
    {
     extract( shortcode_atts( array(
             'name'  => 'icon-wrench'
             ), $params));
     $icon = '<i class="'.$params['name'].'">&nbsp;</i>';

     return $icon;
    }

}

new FontAwesome();
