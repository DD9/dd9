<?php
/*
Testing
Plugin Name: yolink Search for WordPress
Plugin URI: http://yolink.com/wordpress
Description: Drop-in replacement for WordPress search that actually provides relevant results.  To initialize the plugin, click Activate to the left, then click Settings and follow the instructions to generate and register your API key.
Version: 2.6
Author: WP Engine
Yolink Search
Author URI: http://wpengine.com
*/

include("includes/multisite.php");
include("includes/relatedposts.php");
include("includes/sidebar.php");

$yolink = new YolinkSearch;

class YolinkSearch
{
    var $api_key;
    var $never_include_post_types;
    var $allowed_post_types;
    var $wp_blurbs;
    var $preview;
    var $throttle_rate;
    var $social_services;
    var $http_requests;
    var $using_full_content;
    var $results_title;
    var $max_results;
    var $use_mini_share;
    var $ignore_robots;
    var $crawl_state;
    var $yolink_search_results;
    var $search_options;
    var $load_script_once;
    var $search_result_urls;    
    var $search_query_state;

    function __construct() 
    {    
        load_plugin_textdomain( 'yolink' );
        $yolink_config = get_option('yolink_config');
        if( isset( $yolink_config['yolink_apikey']) )
        {
            $this->api_key = $yolink_config['yolink_apikey'];
        }
        else
        {
            $this->api_key = null;
        }
        if( isset( $yolink_config['preview']) )
        {
            $this->preview = $yolink_config['preview'];
        }
        else
        {
            $this->preview = "original";
        }
        if( !isset( $yolink_config['wp_blurbs']) )
        {
            $this->wp_blurbs = "true";
        }
        else
        {
            $this->wp_blurbs = $yolink_config['wp_blurbs'];
        }
        if( isset( $yolink_config['max_results']) )
        {
            $this->max_results = $yolink_config['max_results'];
        }
        else
        {
            $this->max_results = 2;
        }
        if( isset( $yolink_config['yolink_search_options']) )
        {
            $this->search_options = $yolink_config['yolink_search_options'];
        }
        else
        {
            $this->search_options = "best_match";
        }
        if( isset( $yolink_config['allowed_post_types'] ) )
        {
            $this->allowed_post_types = $yolink_config['allowed_post_types'];
        }
        else
        {
            $this->allowed_post_types = array('post','page');
        }
                        
        $this->never_include_post_types = array( 'revision', 'attachment', 'nav_menu_item' );
        if( isset( $yolink_config['throttle_rate'] ) )
        {
            $this->throttle_rate = $yolink_config['throttle_rate'];
        }
        else
        {
            $this->throttle_rate = 5;
        }
        
        $this->ignore_robots = 'true';
        $this->social_services = array( 'share' => 'false', 'googledocs' => 'false', 'fblike' => '', 'tweet' => '' );
        if( isset($yolink_config['active_services']['share']) && $yolink_config['active_services']['share'] == 'true' )
        {
            $this->social_services['share'] = 'true';
        }

        if( isset($yolink_config['active_services']['googledocs']) && $yolink_config['active_services']['googledocs'] == 'true' )
        {
            $this->social_services['googledocs'] = 'true';
        }

        if( isset($yolink_config['active_services']['fblike']) && $yolink_config['active_services']['fblike'] == 'local' )
        {
            $this->social_services['fblike'] = 'local';
        }

        if( isset($yolink_config['active_services']['tweet']) && $yolink_config['active_services']['tweet'] == 'local' )
        {
             $this->social_services['tweet'] = 'local';
        }
        $this->load_script_once = 'false';
        $this->search_query_state = 0;

        if( defined( 'YOLINK_USE_MAXI_SHARE' ) )
        {
            $this->use_mini_share = 'standard';
        }
        else
        {
            $this->use_mini_share = 'mini';
        }
            
        $this->http_requests = array();
        $this->results_title = '';
        
        $this->run_hooks();
    }

    function __destruct()
    {
    }

    function run_hooks()
    {    
        add_action( 'admin_init', array( $this, 'save_registration' ) );
        add_action( 'admin_init', array( $this, 'save_options' ) );
        add_action( 'admin_init', array( $this, 'crawl_local' ) );
        add_action( 'admin_init', array( $this, 'save_crawl_options' ) );
        add_action( 'admin_init', array( $this, 'delete_index' ) );
        add_action( 'admin_init', array( $this, 'save_services_settings' ) );
        add_action( 'admin_init', array( $this, 'save_search_options' ) );
        add_action( 'admin_init', array( $this, 'save_crawler_throttle_rate' ) );
        add_action( 'admin_init', array( $this, 'save_wp_blurbs_settings' ) );
        add_action( 'admin_init', array( $this, 'save_wp_show_related_posts_settings' ) );
        add_action( 'admin_init', array( $this, 'clear_related_cache' ) );
        add_action( 'admin_init', array( $this, 'save_preview_settings' ) );
        add_action( 'admin_init', array( $this, 'save_max_results_settings' ) );        
        add_action( 'admin_notices', array( $this, 'crawl_notices'), 50 );
        add_action('init', array($this, 'yolink_init') );
        
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'wp_set_comment_status', array( $this, 'notify_comment_change'), 20);
        add_action( 'edit_comment', array( $this, 'notify_comment_change'), 21);
        add_action( 'comment_post', array( $this, 'notify_comment_post'), 22);

        # -- 1.0.5 Only add the hooks outside of internal wp-admin search. Currently it is conflicting
        # with internal ones, so this will ensure that search hooks are applied for external searches only
        if( !is_admin() )
        {       
            add_filter( 'get_search_form', array( $this, 'get_search_form' ), 50 );
            
            add_filter( 'posts_where', array( $this, 'yolink_sql_where' ),20 );
                    
            add_action( 'wp_head', array( $this, 'init_js' ), 10 );
            add_action( 'wp_head', array( $this, 'yolink_css' ) );
            add_filter( 'the_content', array( $this, 'add_permalink_for_yolink_use' ) );
            add_filter( 'the_content', array( $this, 'append_related_posts' ) );
            add_filter( $this->yolink_insert_results_hook('the_excerpt'), array( $this, 'add_permalink_for_yolink_use' ) );
            
            # -- 1.0.3 Frugal Compatibility
            add_filter( $this->yolink_insert_results_hook('frugal_hook_after_headline'), array( $this, 'add_permalink_for_yolink_use' ) );
            
            # Thesis Compatibility
            add_action( 'thesis_hook_after_post_box', array( $this, 'add_permalink_for_yolink_use_for_thesis') );
            
            # Thematic Compatibility
            add_filter( 'thematic_search_form', array( $this, 'get_search_form' ), 50 );
            
            # Other themes cleanr, vigilance, default, etc 
            $this->add_hook_for_non_excerpt_themes();

            if( is_multisite() )
            {
                add_filter( 'the_posts', array( $this, 'set_yolink_search_results' ) );
                add_action( 'the_post', array( $this, 'hook_the_post' ) );
            }
            else
            {
                add_filter( 'pre_get_posts', array( $this, '_yolink_query_where' ), 10, 1 );
                add_filter( 'posts_orderby', array( $this, '_yolink_query_order' ),21 );
            }
        }

        foreach( $this->allowed_post_types as $post_type )
        {
            add_action( 'publish_' . $post_type, array( $this, 'submit_post_for_crawl' ), 1, 2 );
        }
        /**
         * Add Settings link to plugins - code from GD Star Ratings
        */
        function add_yolink_settings_link($links, $file) 
        {
            static $this_plugin;
            if (!$this_plugin) 
            {
                $this_plugin = plugin_basename(__FILE__);
            } 
            if ($file == $this_plugin) 
            {
                $settings_link = '<a href="admin.php?page=yolink">'.__("Settings", "yolink-search").'</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }
        
    
        function yolink_filter_timeout_time($time) 
        {
            $time = 60; //new number of seconds
            return $time;
        }

        function yolilnk_switch_blog( $blog_id, $prev_blog_id ) 
        {
            // Save the previous alloptions cache
            $prev_alloptions = wp_cache_get( 'alloptions', 'options' );
            // Do we have a previously cached alloptions for the new blog? If so,
            // replace the current alloptions cache otherwise delete it.
            if ( $alloptions = wp_cache_get( "alloptions_$blog_id", 'options' ) )
            {
                wp_cache_replace( "alloptions", $alloptions, 'options' );
            }
            else
            {
                wp_cache_delete( 'alloptions', 'options' );
            }
        }

        add_filter('plugin_action_links', 'add_yolink_settings_link', 10, 2 );
        add_filter( 'http_request_timeout', 'yolink_filter_timeout_time' );
        add_action( 'switch_blog', 'yolilnk_switch_blog', null, 2 );
    }
    
    /**
    * The plugin implementor should determine when to remove the hook but we want to make it easier for people so we only add this title hook
    * So we are adding this function to support those themes that do not contain typical hooks.
    * @since 1.0.2
    * @return      void
    */    
    function add_hook_for_non_excerpt_themes()
    {
        $curTheme = strtolower(get_current_theme() );

        if( $curTheme === 'cleanr' ||
            $curTheme === 'default' ||
            $curTheme === '9ths current' ||
            $curTheme === 'blueberry' )
        {
            add_filter( $this->yolink_insert_results_hook('the_title'), array( $this, 'add_permalink_for_yolink_use' ) );    
        }
        else if( $curTheme === 'vigilance' )
        {
            add_filter( $this->yolink_insert_results_hook('the_time'), array( $this, 'add_permalink_for_yolink_use' ) );    
        }
    }

    function yolink_insert_results_hook( $hook )
    {
        return apply_filters( 'yolink_insert_results_hook', $hook );
    }

    /*
        For edit, update, delete, approve and unapprove, recrawl the post.
    */
    function notify_comment_change( $comment_id )
    {
        $comment = get_comment($comment_id);
        $postdata = get_post($comment->comment_post_ID);        
        $this->crawl_submit_single( $postdata->ID );
    }
    
    /*
        Skip unapproved new comments.
    */
    function notify_comment_post( $comment_id )
    {
        $comment = get_comment($comment_id);
        if( $comment->comment_approved == '1' )
        {
            $this->notify_comment_change( $comment_id );
        }
    }
    
    function yolink_init()
    {
        function yolink_warning()
        {
            echo "<div id='yolink-warning' class='updated'><p><strong>"
               . sprintf(__('yolink Search for WordPress requires attention: In order for search to work, please visit your index  <a href="%1$s">settings page</a> to accept the agreement, register your API key, and crawl your content.'), "admin.php?page=yolink")
               . "</strong></p></div>";
        }

        $yolink_config = get_option('yolink_config');
                
        if( !isset( $yolink_config['yolink_apikey']) )
        {
            add_action('admin_notices', 'yolink_warning');
        }
    }

    function init_js()
    {
        if( !is_search() )
        {
            return false;
        }

        wp_register_script( 'jquery-tl', 'http://cloud.yolink.com/yolinklite/js/tigr.jquery-1.4.2-min.js', array() );
        wp_register_script('yolink', 'http://cloud.yolink.com/yolinklite/js/v2/yolink-2.0.js', array('jquery-tl') );
        wp_enqueue_script('jquery-tl');
        wp_enqueue_script('jquery');
        wp_enqueue_script('yolink');
        wp_print_scripts();
        
        $urls = array();
        $results = get_transient( 'yolink_search_results_' . get_search_query() . '_' . $this->search_options ) ;
        if( $results == NULL || false === $results )
        {
            $results = $this->do_search( get_search_query() );
        }

        if(  false !== $results )
        {
            foreach( $results as $annotation )
            {    
                if( is_multisite() )
                {
                    $urls[] = "'" . get_blog_permalink( $annotation->wp_blog_id, $annotation->wp_post_id ) . "'";
                }
                else
                {
                    $urls[] = "'" . get_permalink( $annotation->wp_post_id ) . "'";
                }
            }
        }
        $urls = implode( ',', $urls );
        
        $yolink_config = get_option('yolink_config');
        ?>
        <script type="text/javascript">
            tigr.yolink.Widget.initialize(
            {
                maxResults : <?php echo $this->max_results; ?>,
                selectAll: true,
                display : 'embed',
                algorithm : '<?php echo $this->search_options; ?>',
                formfactor : '<?php echo $this->use_mini_share ?>',
                getSearch : function()
                {
                    var urls = [<?php echo $urls ?>];
                    var matched =  $tigr('a.yolink-href-key').filter( function(x) {
                    var r = false;
                    var that = this;
                    $tigr(urls).each(
                        function(idx,value)
                        {
                            r = r || value == $tigr(that).attr('href');
                        } );
                    return r;
                });

                var searchLinks = new Array();
                $tigr(matched).each(
                    function(idx,value)
                    {
                        var parent=$tigr( value ).parent();
                        if( $tigr( parent ).is( ':visible' ) )
                        {
                            searchLinks.push( parent );
                        }
                    } );

                return searchLinks;
            },

                getSearchURL : function(v)
                {
                    return $tigr(v).attr('url') || $tigr(v).attr('href');
                },
<?php
            $term = html_entity_decode( get_search_query() );
            $term = mysql_real_escape_string(strip_tags(trim($term)));
?>
            keywords : '<?php echo $term ?>', // the keyword will be pulled from the s input box of the search term
            showTools : 'result',
                <?php 
                echo ( $yolink_config['active_services']['share'] == 'true' ) ? 'share : true,' : 'share : false,';
                echo "\n";
                echo ( $yolink_config['active_services']['googledocs'] == 'true' ) ? 'googledocs : true,' : 'googledocs : false,';
                echo "\n";
                echo ( $yolink_config['active_services']['fblike'] == 'local' ) ? 'fblike : "local",' : '';
                echo "\n";
                echo ( $yolink_config['active_services']['tweet'] == 'local' ) ? 'tweet : "local",' : '';
                echo "\n";
                ?>
                <?php
                if ($yolink_config['preview']) 
                {
                    echo "preview: '" . $yolink_config['preview'] . "',";
                }
                else 
                {
                    echo "preview : 'original',\n";
                }                    
                ?>
            auto : true,
            apikey : '<?php echo $this->api_key ?>',
            showHide : true
            });            
        </script>
        <?php
    }
    
    function urlencode( $string )
    {   
        return urlencode( html_entity_decode( $string ) );
    }
    
    function yolink_css()
    {
        if( !is_search() )
        {
            return false;
        }
        ?>
        <style type="text/css">
            a.yolink-href-key { display:none }
            .yolink-widget-result { padding: 0 10px; background: #efefef; border: 1px solid: #eee}
            .yolink-widget-result h4 { font-style:italic; font-weight:bold;}
            .yolink-results-logo { padding-left:2px; height:20px; vertical-align: middle;}
        </style>
        <?php
        if (!$this->wp_blurbs || !isset($this->wp_blurbs) || $this->wp_blurbs == "false" )
        {
        ?>
        <style>
        .entry-content p {
            display:none;
        }
        .entry-summary p {
            display:none;
        }
        .post-excerpt p {
            display:none;
        }
        .entry p {
            display:none;
        }
        </style>
        <?php
        }
        ?>
        <?php
    }
    
    function sanitize_excerpt( $text ) 
    {
        if( !is_search() )
        {
            return $text;
        }
            
        return str_replace( $this->results_title . '#', '', $text );
    }
    
    function add_permalink_for_yolink_use_for_thesis()
    {
        global $post;
        if( !is_search() )
        {
            return false;
        }
        else
        {
            # -- 2.3.1 Include support for custom URLs

            $permalink = get_permalink( $post->ID );
            $url       = $this->get_actual_url_from_post( $post, $permalink );

            # -- 1.0.6 <noscript> support
            echo '<div class="yolink-widget-result"><a class="yolink-href-key" url="' . $url . '" href="' . $permalink . '">' . esc_html( $post->post_title ) . '</a>' . $this->noscript() . '</div>';
        }
    }
    
    function get_page_related_links()
    {
        global $post;
        $result = get_post_meta( $post->ID, 'yolink_related_posts', true);
        $yolink_config = get_option('yolink_config');
        $lastModDate = $yolink_config['last_modified_date'];
    
        if( !isset( $result ) || empty( $result ) || $result->timestamp < $lastModDate )
        {
            $related_url = 'http://index.yolink.com/index/related?ak=' . $yolink_config['yolink_apikey'] . '&o=json&c=10&include_features=false&feature_limit=200&u=' .get_permalink( $post->ID ) ;

            $request = new WP_Http;
            $out = $request->post( $related_url );
            if( !is_wp_error( $out ) )
            {
                if( $out['response']['code'] == '200' )
                {
                    $body= $out['body'];
                    $json_body = json_decode( $body );
                    if( isset( $json_body->urls ) )
                    {
                        $json_body->timestamp = time();
                        $body                 = json_encode( $json_body );
            
                        update_related_posts_counterpart( $result, $json_body );

                        $result = $json_body;

                        update_post_meta( $post->ID, 'yolink_related_posts', $json_body);
                    }
                }
            }
        }

        return $result;
    }

    /**
    Generate related post in html format
    */
    function gen_page_related_links_html( )
    {
        global $blog_id;
        $json_o = $this->get_page_related_links();
    
        if( isset( $json_o ) && sizeof($json_o->urls) > 0 )
        {
            $html = '<div class="yorelatedposts"><p><strong>You might also like ... ' . '</strong></p>';
            if( isset( $json_o->urls ) )
            {
                foreach( $json_o->urls as $e )
                {
                    if( isset( $e->annotation ) )
                    {
                        $title = $e->title;
                        if( isset( $e->annotation->wp_blog_id ) )
                        {
                            $post = null;
                            $blog_name = null;

                            if( is_multisite() )
                            {
                                $post         = get_blog_post( $e->annotation->wp_blog_id, $e->annotation->wp_post_id );
                                $post->url    = isset( $e->annotation->actual_url ) ? $e->annotation->actual_url : NULL;
                                $blog_details = get_blog_details( $e->annotation->wp_blog_id );

                                if( isset( $blog_details ) && ( $blog_id != $e->annotation->wp_blog_id ) )
                                {
                                    $blog_name = $blog_details->blogname;
                                }
                            }
                            else
                            {
                                $post             = get_post( $e->annotation->wp_post_id );

                                # 2.3.1 -- Allow for custom URL annotation
                                $post->actual_url = isset( $e->annotation->actual_url ) ? $e->annotation->actual_url : NULL;
                            }

                            if( isset( $post ) )
                            {
                                $title = $post->post_title;
                                if( isset( $blog_name ) )
                                {
                                    $title .= ' | ' . $blog_name;
                                }
                            }
                        }

                        $html .= '<div><a href="' . $e->url . '">' . $title . '</a></div>';
                    }
                }
            }
            $html.= '</p></div>';
        }


        return $html ;
    }

    function append_related_posts( $content )
    {
        if( !is_search() )
        {
            $related = null;
            $useragent= $_SERVER['HTTP_USER_AGENT'];
            $yolink_config = get_option('yolink_config');

            if( !strstr($useragent,'yolinkBot') && isset($yolink_config['wp_show_related_posts']) && $yolink_config['wp_show_related_posts'] == 'true' )
            {
                $related = $this->gen_page_related_links_html();
            }

            $content .= $related;
        }
        return $content;
    } 
    function add_permalink_for_yolink_use( $content )
    {
        global $post;
        if( !is_search() )
        {
            return $content;
        }
        else
        {
            # -- 2.3.1 Include support for custom URLs

            $permalink = get_permalink( $post->ID );
            $url       = $this->get_actual_url_from_post( $post, $permalink );

            # -- 1.0.6 <noscript> support
            
            $yolink_script = '<div class="yolink-widget-result"><a class="yolink-href-key" url="' . $url . '" href="' . $permalink . '">' . esc_html( $post->post_title ) . '</a>' . $this->noscript() . '</div>';
            $curTheme      = strtolower(get_current_theme() );

            if( $curTheme === 'magazine basic' || $curTheme === 'magazine premium' )
            {
                echo $yolink_script;
            }
            else
            {
                $content .= $yolink_script;
            }
            /* this is for auto searching, and why we need to import these again, because it doesn't go throught init path
            * due to the fact that it doesn't reload the page*/
            if( $_GET["dynamic"] == "true" && $this->load_script_once == "true" )
            {
                $this->load_script_once = 'false';
                $content .= '<script type="text/javascript">function go(){tigr.yolink.Widget.initialize({maxResults:'. $this->max_results .',selectAll:true,display:"embed",algorithm:"'. $this->search_options .'",formfactor:"'. $this->use_mini_share .'",getSearch:function(){var urls=[' . $this->search_result_urls . '];var matched=$tigr("a.yolink-href-key").filter(function(x){var r=false;var that=this;$tigr(urls).each(function(idx,value){r=r||value==$tigr(that).attr("href");});return r;});var searchLinks=new Array();$tigr(matched).each(function(idx,value){var parent=$tigr(value).parent();if($tigr(parent).is(":visible")){ searchLinks.push( parent );}});return searchLinks;},keywords:"' . get_search_query() . '",showTools:"result",share:'.$this->social_services['share'].',googledocs:'. $this->social_services['googledocs'] .',fblike:"'. $this->social_services['fblike'] .'",tweet:"'. $this->social_services['tweet'] .'",preview:"original",auto:true,apikey:"'. $this->api_key .'",showHide:true});}if(!window.tigr){jQuery.getScript( "http://cloud.yolink.com/yolinklite/js/tigr.jquery-1.4.2-min.js?ver=3.2.1",function(){jQuery.getScript( "http://cloud.yolink.com/yolinklite/js/v2/yolink-2.0.js?ver=3.2.1",go);});}else{go();}</script>';
                $content .= '<style type="text/css">a.yolink-href-key { display:none }.yolink-widget-result { padding: 0 10px; background: #efefef; border: 1px solid: #eee}.yolink-widget-result h4 { font-style:italic; font-weight:bold;}.yolink-results-logo { padding-left:2px; height:20px; vertical-align: middle;}</style>';
                if (!$this->wp_blurbs || !isset($this->wp_blurbs) || $this->wp_blurbs == "false" )
                {
                    $content .=    '<style>.entry-content p {display:none;}.entry-summary p { display:none;}.post-excerpt p {display:none;}.entry p {display:none;}</style>';
                }
            }
            return $content;
           }
    }
    
    function noscript()
    {
        # -- 1.0.6 <noscript> support

        global $post;

        if ($this->preview == 'none') 
        {
            $params = '?ak=' . $this->api_key . '&limit=3&q=' . urlencode(html_entity_decode(get_search_query())) . '&o=html_noscript_nolink&u=' .
                      urlencode(get_permalink( $post->ID ) ) . '&a=best_more_text';
        }
        else 
        {
            $params = '?ak=' . $this->api_key . '&limit=3&q=' . urlencode(html_entity_decode(get_search_query())) . '&o=html_noscript&u=' .
                      urlencode(get_permalink( $post->ID ) ) . '&a=best_more_text';
        }

        return '<noscript><iframe allowTransparency="true" frameborders="0" height="200" width="100%" src="http://api.yolink.com/yolinklite/search-page' .$params . '"></iframe></noscript>';
    }
    
    function throttle_yolink_index_rate()
    {
        $yolink_config = get_option('yolink_config');
        if( !isset( $yolink_config['yolink_apikey'] ) )
        {
            return false;
        }

        $this->redefine_crawler( $yolink_config['yolink_apikey'] );
    }
    
    function redefine_crawler( $apikey )
    {
        $api_url = 'http://index.yolink.com/index/define?ak=' . $apikey . '&clear=false';
        $request = new WP_Http;

                # --1.0.4  made a fix to the way we sent the delay and ignore robots flag to the server. The prior way sent null
                $args = array(
                'headers' => array( 'Content-Type' => 'application/json; charset=utf-8'),
                'body'    => '{"ignore-robots":' . $this->ignore_robots . ', "crawl-delay":' . $this->throttle_rate . ',"root" :{}}',
                'timeout' => 5
                );

        $out = $request->post( $api_url, $args );
    }

    function admin_page()
    {
        $current_tab = 1;
        $included_blogs_update = false;
        $bulkcrawl = $this->test_bulkcrawl();
        
        if( isset( $_POST['yolink-action-blog-include-submit'] ) )
            {
            if( $bulkcrawl == true )
            {
                $included_blogs_update = $this->save_included_blogs();
            }
            $current_tab = 3;
        }
        else
        if( isset( $_POST['yolink-action-wp-show-related-posts-submit'] )  ||
            isset( $_POST['yolink-action-wp-clear-related-cache-submit'] ) )
        {
            $current_tab = 2;
        }
        
        echo '<h2 style="font-style:normal;"><img src="http://www.yolink.com/yolink/images/yolink_logo.png" alt="yolink" style="vertical-align:text-bottom; height:25px; margin-right:7px" />' . __('Search', 'yolink') . '</h2>';
        
        
          ?>
    <div class='postbox-container' style='width:70%;'>
        <script type="text/javascript">
            function switchToTab(current)
            {
                if( jQuery('#multisite').is(":visible") )
                {
                    jQuery('#multisite').fadeOut( 'fast', function(){ jQuery('#'+current).fadeIn('fast'); } );
                }
                else if( jQuery('#options').is(":visible") )
                {
                    jQuery('#options').fadeOut( 'fast', function(){ jQuery('#'+current).fadeIn('fast'); } );
                }
                else if( jQuery('#relatedposts').is(":visible") )
                {
                    jQuery('#relatedposts').fadeOut( 'fast', function(){ jQuery('#'+current).fadeIn('fast'); } );
                }               
            }
            
        </script>
        <h3><a onmouseover="this.style.cursor='pointer'" onclick="switchToTab('options')">Plugin Design</a>
        <b>|</b>
        <a onmouseover="this.style.cursor='pointer'" onclick="switchToTab('relatedposts')">Related Posts</a>
        
        <?php 
            if( is_super_admin() && is_multisite() )
            {
        ?>
            <b>|</b>
            <a onmouseover="this.style.cursor='pointer'" onclick="switchToTab('multisite')">Multi-Site</a></h3>
            <div class="wrap" id="multisite" <?php if ($current_tab != 3 ) {echo 'style="display:none"';} ?> >
        <?php
            yolink_multisite();
        ?>
            </div>
        <?php
            }
            else
            {
                echo '</h3>';
            }
        ?>
            <div class="wrap" id="relatedposts" <?php if ($current_tab != 2 ) {echo 'style="display:none"';} ?> >
            <?php
                yolink_related_posts();
            ?>
            </div>
            <div class="wrap" id="options" <?php if ($current_tab != 1 ) {echo 'style="display:none"';} ?> >
    
            
    
            <form name="auth" action="" method="post" id="yolink-auth">
                <?php
                wp_nonce_field('yolink_registration');
                
                $yolink_config = get_option('yolink_config');
                
                if( !isset( $yolink_config['yolink_apikey']) )
                {
                    
                    ?>
                    <iframe frameborder="1" src="http://www.yolink.com/yolink/legal/yolink_plugin_API_license_agreement_iframe.jsp" width="80%" height="300" style="margin-bottom:15px;border:1px solid #DDDDDD;">
                        <p>Your browser does not support iframes.</p>
                    </iframe><br/>
                    <script type="text/javascript">
                        function toggleRegistration()
                        {
                            if( jQuery(jQuery('#agree-terms')).attr('checked') && jQuery('#registerMe').is(':hidden') )
                            {
                                jQuery('#registerMe').show();
                            }
                            else
                            { 
                                jQuery('#registerMe').hide();
                            }                     
                        }
            
                    </script>
                    <input style="margin-left:20px;" type="checkbox" onClick="toggleRegistration()" name="agree" id="agree-terms"  value="true" /> Check to Agree to the License Agreement</input>
                    <span style="margin-left:50px"><a href="http://www.yolink.com/yolink/legal/yolink_plugin_API_license_agreement_iframe.jsp" target="_blank">Click here for Print Friendly Version</a></span><br />      
                    <div id="registerMe" style="display:none">
                    <?php
                        printf(__('<p>By clicking Agree, the administrative email for this blog %s, will be shared with TigerLogic.</p>', 'yolink'), get_option('admin_email') );
                    ?>
                        <p class="submit" >
                            <input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Agree', 'yolink') ?>" />
                            <input type="hidden" name="yolink-action" id="yolink-action" value="save_auth" />
                        </p>
                    </div>
                    <?php
                }
                else
                {
                    // Apply throttle rate before crawling
                    if( isset( $_POST['yolink-action-crawl'] ) )
                    {
                        $this->throttle_yolink_index_rate();
                    }
                    printf(__('<p> The API key for this blog is <strong>%s</strong>.</p>', 'yolink'), $yolink_config['yolink_apikey'] );
                    
                ?> 
        
                <div id="isRegistered"></div>
                <script type="text/javascript">
            
            function bulkcrawl(begin, blog_id, apikey)
            {
                var post_types = "";

    <?php
            if(  isset($this->allowed_post_types) )
            {
                foreach( $this->allowed_post_types as $t )
                {
    ?>
                    if( post_types != "" )
                    {
                        post_types += ",";
                    }
                    post_types += "<?php echo $t; ?>";
    <?php
                }
            }
    ?>        
            var data =
            {
                from_id:begin,
                orig_apikey:apikey,
                blog_id:blog_id,
                post_types:post_types,
        <?php
            
            if( isset( $yolink_config['yolink_crawl_state']) && $yolink_config['yolink_crawl_state'] === "not crawled" || $yolink_config['yolink_crawl_state'] === "crawled" )
            {
                $crawl_state = $yolink_config['yolink_crawl_state'];
            }
            else 
            {
                $crawl_state = "not crawled";
            }
        ?>
            batch_size:500};
    
                jQuery.ajax(
                    {
                        url: "<?php echo plugins_url('yolink-search/includes/bulkcrawl.php'); ?>",
                        type : 'POST',
                        async : true,
                        data : data,
                        dataType: 'html',

                        success : function(ret)
                        {
                            var s = ret.indexOf("<tl_last>");
                            var e = ret.indexOf("</tl_last>");

                            if( s > 0 && e > 0 && e > s + 9 )
                            {
                                var last = ret.substring(s+9, e);
                                jQuery('#index_progress_msg').text('<?php _e('Submitting selected content. Do NOT navigate away from this page. Post ID = ', 'yolink') ?>' + last ).show();
                                bulkcrawl(last, blog_id, apikey);
                            }
                            else
                            {
                                jQuery('#index_progress_msg').text('<?php _e('The content selected has been submitted. Processing time will vary depending on the amount of content.', 'yolink') ?> ').show();
                            }
                        },

                        /** @review No need to define if you're not taking action. */
                        error: function(reqObj, err,reason)
                        {
                            ;
                        }

                    } );
            }

            function hasRegistered()
            {
                var registered = false;
                jQuery.ajax(
                {
                    url : 'http://signup.yolink.com/yoadmin/rpcservices',
                    async : true,
                    dataType : 'jsonp',
                    data : 'o={\"method\":\"get-userinfo\",\"id\":1,\"params\":[{\"apikey\":\"<?php echo $yolink_config['yolink_apikey']?>\"}]}',
                    success : function(ret)
                    {
                        var crawlState = "<?php echo $crawl_state; ?>";
                        var justCrawled = "<?php echo $_POST['yolink-action-crawl']; ?>";
                        jQuery.each( ret.result.product,
                            function(idx, val)
                        {

                            if( val.name === 'cloud' && ( val.index && parseInt(val.index) > 50))
                            {
                                //handle case when user has registered and hasn't re-crawled yet
                                if (crawlState == "not crawled" && !justCrawled)
                                {
                                    jQuery('#hasCrawled').show('fast');
                                    jQuery('#hasCrawled').html('<p><?php _e('Thank you for registering! Click the "Crawl" button below to increase the number of pages crawled by yolink.', 'yolink') ?></p>');
                                }

                                jQuery('#isRegistered').html('<a href="http://admin.yolink.com/account" target="_blank"><?php _e('Manage your account', 'yolink') ?></a>.');
                                registered = true;
                                return false;
                            }
                        });
                    },
                    error: function(reqObj, err,reason)
                    {
                        registered = false;
                    }
                } );

                if( !registered )
                {
                    jQuery('#isRegistered').html(' <?php _e('This unregistered API key is limited to 50 indexed pages.'. 'yolink') ?> <a href="http://www.yolink.com/yolink/pricing/multi/index.jsp?ak=<?php echo urlencode($this->get_yolink_apikeys_for_all_blogs())?>&affiliate_id=<?php echo $this->yolink_affiliate();?>" target="_blank">Click here</a><?php _e(' to register the API key to increase the number of permitted indexed pages. yolink search for WordPress is free for personal sites, and pricing for businesses start at $60 per year.', 'yolink') ?>');
                }
            }
            hasRegistered();
        <?php

            if( isset( $_POST['yolink-action-crawl'] ) && $bulkcrawl )
            {
                global $blog_id;
        ?>
                bulkcrawl(0, "<?php echo $blog_id; ?>", null);
        <?php
                $yolink_config['yolink_crawl_state'] = "crawled";
                $yolink_config['last_modified_date'] = time();
                update_option( 'yolink_config', $yolink_config );
                       
                $included_blogs = $yolink_config['included_blogs'];
                $orig_apikey = $yolink_config['yolink_apikey'];

                if( isset( $included_blogs ) )
                {
                    foreach( $included_blogs as $b )
                    {
                        if( !empty($b) )
                        {
        ?>
                bulkcrawl(0, "<?php echo $b; ?>", "<?php echo $orig_apikey; ?>");
        <?php
                          }
                    }
                }
            }
        else if( isset( $_POST['yolink-action-blog-include-submit'] ) && $included_blogs_update != false && $bulkcrawl )
        {  
    
                $yolink_config['yolink_crawl_state'] = "crawled";
        $yolink_config['last_modified_date'] = time();
                update_option( 'yolink_config', $yolink_config );
                       
                $included_blogs = $included_blogs_update['addition'];
                $orig_apikey = $yolink_config['yolink_apikey'];
            
                if( isset( $included_blogs ) )
                {
                    foreach( $included_blogs as $b )
                    {
                        if( !empty($b) )
                        {
        ?>
                bulkcrawl(0, "<?php echo $b; ?>", "<?php echo $orig_apikey; ?>");
        <?php
                          }
                    }
                }
        }
        ?>
            </script>
        <?php
        }
        ?>
            </form>
            <div id="hasCrawled" class="updated fade" style="display:none;"></div>
            <?php
            if( isset( $yolink_config['yolink_apikey'] ) )
            {
                ?>
                <h3><?php _e('Crawl Content', 'yolink' ) ?></h3>
                <p><?php _e('Default selection is both posts and pages.  Please uncheck a type if you do not want it crawled.', 'yolink'); ?></p>
                <form action="" method="post" id="yolink-crawl-form">
                    <?php wp_nonce_field('yolink_crawl'); ?>
                    <p><?php _e('Search which post types', 'yolink') ?></p>
                    <table class="form-table">
                <?php
                    $post_types = get_post_types();
                    foreach( $this->never_include_post_types as $never )
                    {
                        unset( $post_types[array_search( $never, $post_types )] );
                    }
                
                    foreach( $post_types as $post_type )
                    {
                        if( in_array( $post_type, $this->allowed_post_types ) )
                        {
                            echo '<tr valign="top"><th scope="row">' . ucwords( $post_type ) . '</th><td><input type="checkbox" name="post-type-crawl[]" id="post-type-' . $post_type . '" checked="checked" value="' . $post_type . '" /></td></tr>';
                        }
                        else
                        {
                            echo '<tr valign="top"><th scope="row">' . ucwords( $post_type ) . '</th><td><input type="checkbox" name="post-type-crawl[]" id="post-type-' . $post_type . '" value="' . $post_type . '" /></td></tr>';
                        }
                    }
                    
                    // obtain # of indexed pages
                    // -------------------------
                    $counter = $this->getIndexedCounter();
                ?>                    
                    <tr valign="top"><th scope="row"> Total Indexed : </th><td><label><?php echo $counter; ?></label></td></tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="crawl-submit" id="crawl-submit" class="button-primary" value="<?php _e('Crawl','yolink') ?>" onclick="return confirm('Are you sure you want to index selected content?');" />
                        <input type="hidden" name="yolink-action-crawl" id="yolink-action-crawl" value="yolink_crawl" />
                    </p>
                </form>

                <h3><?php _e('Clear Index', 'yolink' ) ?></h3>
                <p><?php _e('Clear index of the current blog.', 'yolink'); ?></p>
                <form action="" method="post" id="yolink-delete-index-form">
                    <?php wp_nonce_field('yolink_delete_index'); ?>
                    
                    <p class="submit">
                        <input type="submit" name="delete-index-submit" id="delete-index-submit" class="button-primary" value="<?php _e('Clear Index','yolink') ?>" onclick = "return confirm('Are you sure you want to clear index of the current blog?');"  />
                        <input type="hidden" name="yolink-action-delete-index" id="yolink-action-delete-index" value="yolink_delete_index" />
                    </p>
                </form>
            
                <h3><?php _e('Crawler Throttle Rate', 'yolink' ) ?></h3>
                <p><?php _e('The interval (in seconds) between indexing each page.', 'yolink'); ?></p>
    <script type="text/javascript">
            function validate_throttle_rate()
            {
        var rate = jQuery('#throttle_rate').val();
        if( rate < 0 || rate > 60 || isNaN(rate) )
        {
            alert( "Throttle rate must be between 0 and 60.");
            return false;
        }
        else
        {
            return confirm('Are you sure you want to change the crawler throttle rate?');
        }
        }
    </script>
                <form action="" method="post" id="yolink-throttle-rate-form">
                    <?php wp_nonce_field('yolink_throttle_rate'); ?>
                    
                    <table class="form-table">
                    <tr>
                        <th scope="row">Throttle Rate:</th>
                        <td><input type="text" name="yolink_throttle_rate" id="throttle_rate" value="<?php echo $this->throttle_rate; ?>" style="width:40px;" /></td>
                    </tr>
                    </table>              
                    <p class="submit">
                        <input type="submit" name="throttle-rate-submit" id="throttle-rate-submit" class="button-primary" value="<?php _e('Save Changes','yolink') ?>" onclick = "return validate_throttle_rate();"  />
                        <input type="hidden" name="yolink-action-save-throttle-rate-submit" id="yolink-action-save-throttle-rate-submit" value="yolink_save_throttle_rate_submit" />
                    </p>
                </form>                

                <h3><?php _e('yolink Sharing Services', 'yolink' ); ?></h3>
                <form action="" method="post" id="yolink-service-form">
                    <?php wp_nonce_field('yolink_services'); ?>
                    <p><?php _e('Please select the sharing service(s) to include with yolink search results.', 'yolink') ?></p>
                    <table class="form-table">
                    <?php
                    $services = array( 
                        'share' => __('Share', 'yolink'), 
                        'googledocs' => __('Google Docs', 'yolink'), 
                        'fblike' => __('Facebook Like Button', 'yolink'),
                        'tweet' => __('Twitter', 'yolink')
                    );
                    foreach( $services as $service => $display ) :
                    ?>
                        <tr valign="top">
                            <th scope="row"><?php echo $display ?></th>
                        <?php
                        $iftrue = array( 'share' => 'true', 'googledocs' => 'true', 'fblike' => 'local', 'tweet' => 'local' );
                        if( isset($yolink_config['active_services'][$service]) && $yolink_config['active_services'][$service] == $iftrue[$service] )
                        {
                    ?>                            
                            <td><input type="checkbox" name="yolink_service[<?php echo $service ?>]" id="service-<?php echo $service ?>" checked="checked" value="<?php echo $service; ?>" /></td>
                    <?php
                        }
                        else
                        {
                            ?>
                            <td><input type="checkbox" name="yolink_service[<?php echo $service ?>]" id="service-<?php echo $service ?>" value="<?php echo $service; ?>" /></td>
                            <?php
                        }
                    ?>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                    </table>
                    <p class="submit">
                        <input type="submit" name="yolink-services-submit" id="yolink-services-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" />
                        <input type="hidden" name="yolink-action-services-submit" id="yolink-action-services-submit" value="yolink_services_submit" />
                    </p>
                </form>
                <?php
                if (!isset($yolink_config['wp_blurbs']))
                {
                    $wp_blurbs = "true";
                }
                else 
                {
                    $wp_blurbs = $yolink_config['wp_blurbs'];
                }
        
                if (!isset($yolink_config['preview']))
                {
                    $preview = "original";
                }
                else 
                {
                    $preview = $yolink_config['preview'];
                }
        
                if (!isset($yolink_config['max_results']) || !($yolink_config['max_results'] % 1) == 0) 
                {
                    $max_results = 2;
                }
                else 
                {
                    $max_results = $yolink_config['max_results'];
                }
        
                if ( isset($yolink_config['yolink_search_options']) )
                {
                    $search_options = $yolink_config['yolink_search_options'];
                }
                else 
                {
                    $search_options = "best_match";
                }
        
                ?>

                <h3><?php _e('Default WordPress Blurbs', 'yolink' ); ?></h3>
                <form action="" method="post" id="yolink-wp-blurbs-form">
                    <?php wp_nonce_field('yolink_wp_blurbs'); ?>
                    <p><?php _e('Show default WordPress result blurbs?', 'yolink') ?></p>
                    <table class="form-table">
                    <tr>
                        <th scope="row">Yes</th>
                        <td><input type="radio" name="yolink_wp_blurbs" id="wp_blurbs" value="true" <?php if ($wp_blurbs=="true") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">No</th>
                        <td><input type="radio" name="yolink_wp_blurbs" id="wp_blurbs" value="false" <?php if ($wp_blurbs=="false" || $wp_blurbs==false) {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    </table>                  
                    <p class="submit">
                        <input type="submit" name="yolink-wp-blurbs-submit" id="yolink-wp-blurbs-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" />
                        <input type="hidden" name="yolink-action-wp-blurbs-submit" id="yolink-action-wp-blurbs-submit" value="yolink_wp_blurbs_submit" />
                    </p>
                </form>

                <h3><?php _e('yolink Maximum Results', 'yolink' ); ?></h3>
                <form action="" method="post" id="yolink-max-results-form">
                    <?php wp_nonce_field('yolink_max_results'); ?>
                    <p><?php _e('How many result paragraphs would you like to display?', 'yolink') ?></p>
                    <table class="form-table">
                    <tr>
                        <th scope="row">Maximum Results</th>
                        <td><input type="text" name="yolink_max_results" id="max_results" value="<?php echo $max_results; ?>" style="width:40px;" /></td>
                    </tr>
                    </table>                  
                    <p class="submit">
                        <input type="submit" name="yolink-max-results-submit" id="yolink-max-results-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" />
                        <input type="hidden" name="yolink-action-max-results-submit" id="yolink-action-max-results-submit" value="yolink_max_results_submit" />
                    </p>
                </form>

                <h3><?php _e('yolink Result Previews', 'yolink' ); ?></h3>
                <form action="" method="post" id="yolink-preview-form">
                    <?php wp_nonce_field('yolink_preview'); ?>
                    <p><?php _e('Please select what should open when users click a result paragraph.', 'yolink') ?></p>
                    <table class="form-table">
                    <tr>
                        <th scope="row">Original page</th>
                        <td><input type="radio" name="yolink_preview" id="preview" value="original" <?php if ($preview=="original") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">Cached page with pinning to keyword locations</th>
                        <td><input type="radio" name="yolink_preview" id="preview" value="tab" <?php if ($preview=="tab") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">Nothing</th>
                        <td><input type="radio" name="yolink_preview" id="preview" value="none" <?php if ($preview=="none") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    </table>                  
                    <p class="submit">
                        <input type="submit" name="yolink-preview-submit" id="yolink-preview-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" />
                        <input type="hidden" name="yolink-action-preview-submit" id="yolink-action-preview-submit" value="yolink_preview_submit" />
                    </p>
                </form>

        <h3><?php _e('yolink Search Options', 'yolink' ); ?></h3>
                <form action="" method="post" id="yolink-search-options-form">
                    <?php wp_nonce_field('yolink_search_options'); ?>
                    <p><?php _e('Please select a search option.', 'yolink') ?></p>
                    <table class="form-table">
                    <tr>
                        <th scope="row">Best Match</th>
                        <td><input type="radio" name="yolink_search_options" id="search-option" value="best_match" <?php if ($search_options=="best_match") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">Nearest Match</th>
                        <td><input type="radio" name="yolink_search_options" id="search-option" value="nearest_match" <?php if ($search_options=="nearest_match") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">Match All</th>
                        <td><input type="radio" name="yolink_search_options" id="search-option" value="match_all" <?php if ($search_options=="match_all") {echo "checked=\"checked\"";} ?> /></td>
                    </tr>
                    </table>                  
                    <p class="submit">
                        <input type="submit" name="yolink-search-options-submit" id="yolink-search-options-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" />
                        <input type="hidden" name="yolink-action-search-options-submit" id="yolink-action-search-options-submit" value="yolink_search_options_submit" />
                    </p>
                </form>        
            <?php
            }
            ?>
        </div>
        </div>
        <?php
            if( isset( $yolink_config['yolink_apikey']) )
            {
                sidebar();
            }
    }
    
    function save_included_blogs()
    {
        $value = $_POST['yolink-action-blog-include-submit'];
        if( !isset( $value ) )
        {
            return false;
        }

        $include_blogs = explode('|', $value);    
        $yolink_config = get_option('yolink_config');
        $cur_included = $yolink_config['included_blogs'];
        if( !isset( $cur_included ) )
        {
            $cur_included = array();        
        }
        if( $cur_included != $include_blogs )
        {
            $diff = array();
            $addition = array();
            foreach( $include_blogs as $include_blog )
            {
                $key = array_search($include_blog, $cur_included);
                if( $key === 0 || !empty($key) )
                {
                    unset( $cur_included[$key] );    
                }
                else
                {
                    $addition[] = $include_blog;
                }
            }
            
            $diff['addition']                = $addition;
            $diff['subtraction']             = $cur_included;
            $yolink_config['included_blogs'] = $include_blogs;    
                
            $subtraction = $cur_included;
            $orig_apikey = $yolink_config['yolink_apikey'];
            $modified    = false;

            foreach( $subtraction as $b_id )
            {
                if( !empty($b_id) )
                {
                    $this->delete_blog_index( $b_id, $orig_apikey );
                    $modified = true;
                }
            }
            if( $modified == true )
            {
                $yolink_config['last_modified_date'] = time();
            }

            update_option( 'yolink_config', $yolink_config );
            return $diff;    
        }
        return false;
    }
    
    function save_crawler_throttle_rate()
    {
        if( !isset( $_POST['yolink-action-save-throttle-rate-submit'] ) )
        {
            return false;
        }
        $yolink_config = get_option('yolink_config');
        $throttle_rate = $_POST['yolink_throttle_rate'];
        $yolink_config['throttle_rate'] = $throttle_rate;
        update_option( 'yolink_config', $yolink_config );
        $this->throttle_rate = $throttle_rate;
        $this->redefine_crawler( $yolink_config['yolink_apikey'] );
    }    

    function save_services_settings()
    {
        if( !isset( $_POST['yolink-action-services-submit'] ) )
        {
            return false;
        }
        
        check_admin_referer('yolink_services');
        $yolink_config = get_option('yolink_config');
        $service_status = array();
        $new_settings = $_POST['yolink_service'];
        if( !isset( $new_settings ) )
        {
            $new_settings = array();
        }
        $service_status['share'] = ( in_array( 'share', $new_settings ) ) ? 'true' : 'false';
        $service_status['googledocs'] = ( in_array( 'googledocs', $new_settings ) ) ? 'true' : 'false';
        $service_status['fblike'] = ( in_array( 'fblike', $new_settings ) ) ? 'local' : '';
        $service_status['tweet'] = ( in_array( 'tweet', $new_settings ) ) ? 'local' : '';
        $yolink_config['active_services'] = $service_status;
        update_option( 'yolink_config', $yolink_config );
    }
    
    function save_wp_blurbs_settings()
    {
        if( !isset( $_POST['yolink-action-wp-blurbs-submit'] ) )
        {
            return false;
        }
        check_admin_referer('yolink_wp_blurbs');
        $yolink_config = get_option('yolink_config');
        $wp_blurbs = $_POST['yolink_wp_blurbs'];
        $yolink_config['wp_blurbs'] = $wp_blurbs;
        update_option( 'yolink_config', $yolink_config );
    }

    function save_wp_show_related_posts_settings()
    {
        if( !isset( $_POST['yolink-action-wp-show-related-posts-submit'] ) )
        {
            return false;
        }

        check_admin_referer('yolink_wp_show_related_posts');
        $yolink_config = get_option('yolink_config');
        $wp_show_related_posts = $_POST['yolink_wp_show_related_posts'];
        $yolink_config['wp_show_related_posts'] = $wp_show_related_posts;
        update_option( 'yolink_config', $yolink_config );
    }
    
    function clear_related_cache()
    {
        if( !isset( $_POST['yolink-action-wp-clear-related-cache-submit'] ) )
        {
            return false;
        }

        global $wpdb;
        $post_recs = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts " ) );
        foreach( $post_recs as $postinfo)
        {
            delete_post_meta( $postinfo->ID, 'yolink_related_posts' );
        }
    }
    
    function save_max_results_settings()
    {
        if( !isset( $_POST['yolink-action-max-results-submit'] ) )
        {
            return false;
        }
        check_admin_referer('yolink_max_results');
        $yolink_config = get_option('yolink_config');

        if ($_POST['yolink_max_results'] % 1 == 0)
        {
            $max_results = $_POST['yolink_max_results'];
        }
        else
        {
            $max_results = 2;
        }

        $yolink_config['max_results'] = $max_results;
        update_option( 'yolink_config', $yolink_config );
    }

    function save_preview_settings()
    {
        if( !isset( $_POST['yolink-action-preview-submit'] ) )
        {
            return false;
        }
        check_admin_referer('yolink_preview');
        $yolink_config = get_option('yolink_config');
        $preview = $_POST['yolink_preview'];
        $yolink_config['preview'] = $preview;
        update_option( 'yolink_config', $yolink_config );
    }

    function save_search_options()
    {
        if( !isset( $_POST['yolink-action-search-options-submit'] ) )
        {
            return false;
        }

        check_admin_referer('yolink_search_options');
        $yolink_config = get_option('yolink_config');
        $search_options = $_POST['yolink_search_options'];
        $yolink_config['yolink_search_options'] = $search_options;
        update_option( 'yolink_config', $yolink_config );        
    }

    function _yolink_query_where( $query )
    {
        if ( $query->is_search && class_exists( 'YolinkSearch' ) )
        {
            $this->search_query_state = 1;
            if( !$results = get_transient( 'yolink_search_results_' . $_GET['s'] . '_' . $this->search_options ))
            {
                $results = $this->do_search( get_search_query() );
            }

            $this->save_search_result_to_widget_format( $results );

            $this->load_script_once      = 'true';
            $post_set                    = array();
            $this->yolink_search_results = array();

            if( isset( $results ) && false !== $results )
            {
                foreach ( $results as $annotation )
                {
                    $post_set[]                    = $annotation->wp_post_id;
                    $this->yolink_search_results[] = array( 'blog_id' => $annotation->wp_blog_id, 'id' => $annotation->wp_post_id, 'actual_url' => $annotation->actual_url );
                }
            }

            $query->set( 'post__in', $post_set);
            $query->set( 'post_type', $this->allowed_post_types );
        }
        return $query;
    }
    /**
    Format search result urls for yolink widget
    */
    function save_search_result_to_widget_format( $results )
    {
        $urls = array();
        if(  false !== $results )
        {
            foreach( $results as $annotation )
            {
                if( is_multisite() )
                {
                    $urls[] = "'" . get_blog_permalink( $annotation->wp_blog_id, $annotation->wp_post_id ) . "'";
                }
                else
                {
                    $urls[] = "'" . get_permalink( $annotation->wp_post_id ) . "'";
                }
            }
        }
        $urls = implode( ',', $urls );
        $this->search_result_urls = $urls;
    }    

    function _yolink_query_order( $order_by )
    {
        if( is_search() && $this->search_query_state == 1 )
        {
            $this->search_query_state = 0;
            global $wpdb, $wp_query;
            $fields = implode( ',', $wp_query->query_vars['post__in'] );
            $order_by = "FIELD($wpdb->posts.ID, $fields)";
        }

        return $order_by;
    }
    
    function yolink_sql_where( $where )
    {
        if( !is_search() )
        {
            return $where;
        }    

        $where = preg_replace("#LIKE '(%.*%)'#", "LIKE '%'", $where );
        return $where;
    }
    
    function search_qv( $qvs )
    {
        $qvs[] = 'yolink_search';
        return $qvs;
    }
    
    function do_search( $term = false )
    {            
        $search_term = false;
        if( $term )
        {
            $search_term = stripslashes($term);
        }
        else
        {    
            $search_form_value = get_search_query();
            if( !$search_term )
            {
                $search_term = $search_form_value;
            }
            else
            {
                return false;
            }
        }
        // sanitize the incoming data
        $search_term = mysql_real_escape_string(strip_tags(trim($search_term)));
        $search_term = htmlentities($search_term, ENT_QUOTES, "UTF-8");
        $search_results = $this->yolink_search( $search_term );
        set_transient( 'yolink_search_results_' . $search_term . '_' . $this->search_options, $search_results, 600 );
        
        return $search_results;
    }
    
    function crawl_notices()
    {
        if( !isset( $_POST['yolink-action-crawl'] ) )
        {
            return false;
        }
        echo '<div class="updated fade"><p>' . __( '<strong>yolink Notice:</strong> <span id="index_progress_msg">Submitting selected content. Do NOT navigate away from this page.</span>', 'yolink' ) . '</p></div>';
    }
    
    /*
    Used by crawl_local() in case bulkcrawl doesn't work
    */
    function crawl_submit_all($foreign_blog_id, $apikey)
    {
        global $wpdb;
        global $blog_id;

        $orig_apikey = $apikey;
        $batch_size  = $_POST['batch_size'];

        if( isset( $_POST['post-type-crawl'] ) )
        {
            $this->allowed_post_types = (array)$_POST['post-type-crawl'];    
        }
            
        $post_type_in = array();
        foreach( $this->allowed_post_types as $post_type )
        {
            $post_type_in[] = '"' . esc_sql( $post_type ) . '"';
        }
        $post_type_in = '(' . implode(',', $post_type_in) . ')';
        $yolink_config = get_option('yolink_config');
        if( !isset( $orig_apikey ) )
        {
            $orig_apikey = $yolink_config['yolink_apikey'];
        }

        if( isset( $foreign_blog_id ) && $foreign_blog_id != $blog_id )
        {
            switch_to_blog( $foreign_blog_id );
        }

        $post_recs = $wpdb->get_results( $wpdb->prepare( "SELECT ID,GUID FROM $wpdb->posts WHERE post_status=%s AND post_type IN $post_type_in order by ID asc", 'publish' ) );
        $post_count = $wpdb->num_rows;
        $permalinks = array();        
        $post_recs = (object) $post_recs;
        $source = '';
        if( is_multisite() )
        {
            $blog_details = get_blog_details($blog_id);
            $source = $blog_details->domain . $blog_details->path;
        } 

        foreach( $post_recs as $post_rec )
        {
            (object)$post_rec = $post_rec;
            if( $post_rec->GUID != null )
            { 
                # 2.3.1 -- Support search URL customization

                $annotation = array( 'wp_blog_id' => (int)$blog_id, 'wp_post_id' => (int)$post_rec->ID );
                $url        = get_permalink( $post_rec->ID );

                $custom     = get_post_custom( $post_rec->ID );
                $yolinkURL  = @$custom[ 'ecpt_yolink_custom_url:' ][0];

                if( $yolinkURL )
                {
                    $yolinkURL                  = $this->make_site_url( $yolinkURL );
                    $annotation[ 'actual_url' ] = $yolinkURL;
                    $url                        = $yolinkURL;
                }

                $postdata = array(
                    'url'            => $url,
                    'depth'            => 0,
                    'annotation'    => (object)$annotation,
                    'source'        => $source,
                );

                $permalinks['urls'][] = $postdata;
            }
            $last_id = $post_rec->ID;
        }

        $json_object = json_encode($permalinks);
        if( is_wp_error( $json_object ) )
        {
            if( is_multisite() )
            {
                restore_current_blog();
            }
            return false;
        }
        if( $post_recs )
        {
            $json_out = $this->submit_crawl( $json_object, $orig_apikey );            
        }

        if( is_multisite() )
        {
            restore_current_blog();
        }
        return true;
    }
    
    /*
     * Constructs an absolute URL for a link.
     */
    function make_site_url( $url )
    {
        $pos = stripos( $url, ':' );
        if( $pos > 0 )
        {
            return $url;
        }
        else
        if( is_multisite() )
        {
            return network_site_url( $url );
        }
        else
        {
            return site_url( $url );
        }
    }

    /*
    The fallback solution in case bulkcrawl doesn't work
    */
    function crawl_local()
    {    
        if( isset( $_POST['yolink-action-crawl'] ) )
        {
            if( $this->test_bulkcrawl() == true )
            {
                return false;
            }

            $yolink_config = get_option('yolink_config');
            if( !isset( $yolink_config ) )
            {
                return false;
            }

            global $blog_id;

            $this->crawl_submit_all( $blog_id, null );
        
            $yolink_config['yolink_crawl_state'] = "crawled";
            $yolink_config['last_modified_date'] = time();

            update_option( 'yolink_config', $yolink_config );
                       
            $included_blogs = $yolink_config['included_blogs'];
            $orig_apikey = $yolink_config['yolink_apikey'];
            if( isset( $included_blogs ) )
            {
                foreach( $included_blogs as $b )
                {
                    if( !empty($b) )
                    {
                        $this->crawl_submit_all( $b, $orig_apikey );
                    }
                }
            }
        }
        else
        if( isset( $_POST['yolink-action-blog-include-submit'] ) )
        {
            if( $this->test_bulkcrawl() == true )
            {
                return false;
            }

            $yolink_config = get_option('yolink_config');

            if( !isset( $yolink_config ) )
            {
                return false;
            }
            $included_blogs_update = $this->save_included_blogs();
            if( $included_blogs_update == true )
            {
                $yolink_config = get_option('yolink_config');
                $yolink_config['yolink_crawl_state'] = "crawled";
                $yolink_config['last_modified_date'] = time();
                update_option( 'yolink_config', $yolink_config );

                $included_blogs = $included_blogs_update['addition'];
                $orig_apikey = $yolink_config['yolink_apikey'];

                if( isset( $included_blogs ) )
                {
                    foreach( $included_blogs as $b )
                    {
                        if( !empty($b) )
                        {
                            $this->crawl_submit_all( $b, $orig_apikey );
                        }
                    }
                }
            }
        }
    }

    /*
    Crawl a single page. It is used for add and update a page. For 
    multisite, it update other site's index if the current page is 
    also included by other blogs.
    */
    function crawl_submit_single( $post_id = false)
    {
        global $wpdb;
        global $blog_id;

        // Determine if we're only submitting a single URL
        $is_single = false;
        if( isset( $post_id ) )
        {
            $is_single = true;
        }
        // If this is a multi-URL batch, make sure we have our special field. Ignored for single URL submits
        if( !isset( $_POST['yolink-action-crawl'] ) && !$is_single )
        {
            return false;
        }
        $yolink_config = get_option('yolink_config');
        if( !isset( $yolink_config ) )
        {
                return false;
        }
        if( isset( $_POST['yolink-action-crawl'] ) )
        {    
            if( $post_id )
            {
                $query = new WP_Query( array(
                    'post__in'        => (int) $post_id,
                    'showposts'        => 1,
                    'post_status'    => 'publish',
                    'post_type'        => $this->allowed_post_types,
                    )
                );
                while( $query->have_posts() ) : $query->the_post();
                    $post_ids[] = $query->post->ID;
                endwhile;
            }
        }

        // Generate permalink list
        $permalinks = array();
        $source     = '';

        if( is_multisite() )
        {
            $blog_details = get_blog_details($blog_id);
            $source = $blog_details->domain . $blog_details->path;
        }
    
        # 2.3.1 -- Support search URL customization

        $annotation = array( 'wp_blog_id' => (int)$blog_id, 'wp_post_id' => (int)$post_id );
        $url        = get_permalink( $post_id );

        $custom     = get_post_custom( $post_id );
        $yolinkURL  = @$custom[ 'ecpt_yolink_custom_url:' ][0];

        if( $yolinkURL )
        {
            $yolinkURL                  = $this->make_site_url( $yolinkURL );
            $annotation[ 'actual_url' ] = $yolinkURL;
            $url                        = $yolinkURL;
        }

        $postdata = array(
            'url'        => $url,
            'depth'      => 0,
            'annotation' => (object)$annotation,
            'source'     => $source,
        );

        $permalinks['urls'][] = $postdata;
        
        $json_object = json_encode($permalinks);
        if( is_wp_error( $json_object ) )
        {
            return false;
        }

        $json_out = $this->submit_crawl_multi_site( $json_object );
    }
    
    function get_posts_for_initial_crawl()
    {
        global $wpdb;
        global $blog_id;
       
        //this seems unnecessary here
        $combined_posts = array();

        $query = new WP_Query( array(
            'showposts'        => 100,
            'post_status'    => 'publish',
            'post_type'     => get_post_types(),
            'offset'        => 0,
        ));
        while( $query->have_posts() ) : $query->the_post();
            $post_ids[] = $query->post->ID;
        endwhile;
        
        $post_ids = (object) $post_ids;
        if( is_int( $post_id ) )
        {
            $post_ids = (object) array( $post_id );
        }
        // Generate permalink list
        $permalinks = array();
        
        if( !isset( $post_ids) )
        {
            return false;
        }    
        //this doesn't return true, but can't hurt to check
        if( is_array( $post_ids ) )
        {
            $post_ids = (object) $post_ids;
        }    
        $source = '';
        if( is_multisite() )
        {
            $blog_details = get_blog_details($blog_id);
            $source = $blog_details->domain . $blog_details->path;
        } 

        foreach( $post_ids as $post_id )
        {
            # 2.3.1 -- Support search URL customization

            $annotation = array( 'wp_blog_id' => (int)$blog_id, 'wp_post_id' => (int)$post_id );
            $url        = get_permalink( $post_id );

            $custom     = get_post_custom( $post_id );
            $yolinkURL  = @$custom[ 'ecpt_yolink_custom_url:' ][0];

            if( $yolinkURL )
            {
                $yolinkURL                  = $this->make_site_url( $yolinkURL );
                $annotation[ 'actual_url' ] = $yolinkURL;
                $url                        = $yolinkURL;
            }

            $postdata = array(
                'url'        => $url,
                'depth'         => 0,
                'annotation' => (object)$annotation,
                'source'     => $source,
            );
            $permalinks['urls'][] = $postdata;
        }
        $json_object = json_encode($permalinks);

        if( is_wp_error( $json_object ) )
        {
            return false;
        }
        return $json_object;
    }
    
    function delete_blog_index($blog_id, $apikey)
    {
        $blog_details = get_blog_details($blog_id);
        $source       = $blog_details->domain . $blog_details->path;
        $api_url      = 'http://index.yolink.com/index/removesource?ak=' . urlencode($apikey) . '&n=' . $source;
        $request      = new WP_Http;
        $out          = $request->get( $api_url );
    }
    
    function getIndexedCounter()
    {
        $yolink_config = get_option('yolink_config');
        $api_url = 'http://index.yolink.com/index/diagnostic?action=count&ak=' . $yolink_config['yolink_apikey'];
        $request = new WP_Http;    
        $args = array( 'timeout' => 5 );
    
        $res = $request->post( $api_url, $args );
        if( false == $res || !isset( $res ) )
        {
            return -1;
        }
        else
        {
            return next( $res );
        }
    }
    
    function submit_crawl( $postdata, $apikey ) 
    {
        $api_url = 'http://index.yolink.com/index/crawl?o=JSON&ak=' . urlencode($apikey);
        $request = new WP_Http;
        $args   = array(
            'headers'        => array( 'Content-Type' => 'application/json; charset=utf-8'),
            'body'            => $postdata,
        );

        $out = $request->post( $api_url, $args );
    }
   
    function submit_crawl_multi_site( $postdata )
    {
        if( !is_multisite() )
        {
            $yolink_config = get_option('yolink_config');
            $this->submit_crawl( $postdata, $yolink_config['yolink_apikey'] );
            return;
        }
        global $wpdb;
        global $blog_id;
        $current_blog_id = $blog_id;
        $apikey = '';
        $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_blogs ORDER BY blog_id" ) );
        foreach( $blogs as $b )
        {
            $crawl_posts = false;
            if( $b->blog_id == $blog_id )
            {
                $crawl_posts = true;
                $yolink_config = get_option('yolink_config');
                $apikey = $yolink_config['yolink_apikey'];

                // update last modified time
                // -------------------------
                $yolink_config['last_modified_date'] = time();
                update_option('yolink_config', $yolink_config );
            }
            else
            {
                switch_to_blog( $b->blog_id );
                $yolink_config = get_option('yolink_config');

                $included_blogs = $yolink_config['included_blogs'];
                $apikey = $yolink_config['yolink_apikey'];
                if( isset( $included_blogs ) )
                {
                    foreach( $included_blogs as $blogId )
                    {
                        if( $blogId == $current_blog_id )
                        {
                            $crawl_posts = true;

                            // update last modified time
                            // -------------------------
                            $yolink_config['last_modified_date'] = time();
                                update_option('yolink_config', $yolink_config );
                            break;
                        }
                    }
                }

                if( is_multisite() )
                {
                    restore_current_blog();
                }
            }

            if( $crawl_posts )
            {
                $this->submit_crawl( $postdata, $apikey );
            }
        }
    }
    /*
        For single page crawl, hooked by update or add new post/page.
    */
    function submit_post_for_crawl( $post_id, $post )
    {        
        if( in_array( $post->post_type, $this->never_include_post_types ) )
        {
            return false;
        }

        $this->crawl_submit_single( $post_id );
    }
    
    function add_menu()
    {
        add_menu_page( __('yolink Search', 'yolink'), __('yolink Search', 'yolink'), 'manage_options', 'yolink', array( $this, 'admin_page') , WP_PLUGIN_URL . '/yolink-search/images/yolink-icon.jpg' );
    }
    
    function save_registration()
    {
        if( !isset( $_POST['yolink-action'] ) )
        {
            return false;
        }
        $yolink_config = get_option('yolink_config');
        if( isset( $yolink_config['yolink_apikey']) )
        {
            return false;
        }
        check_admin_referer('yolink_registration');
        $json_object = $this->yolink_register() ;
        $response_body = json_decode( $json_object[0]['body'] );
        
        if( !get_option('yolink_config') || !is_array( get_option('yolink_config') ) )
        {
            return false;
        }
        
        update_option( 'yolink_config', array_merge( get_option('yolink_config'), array( 'yolink_apikey' => $response_body->apikey, 'yolink_user_url' => $response_body->id ) ) );

        
        //define and crawl once right after user loads plugin first time
        $this->throttle_yolink_index_rate();
        $this->initial_crawl($response_body->apikey);
        
        //credit affiliate if user got plugin that way
        $affiliate_id = $this->yolink_affiliate();
        if ($affiliate_id)
        {
            $this->yolink_save_affiliate($response_body->apikey, $affiliate_id);
        }
    }   
    
    /*
    For the initial crawl, right after user click Agree button. 
    */
    function initial_crawl($apikey)
    {
        $postdata = $this->get_posts_for_initial_crawl();

        $this->submit_crawl( $postdata, $apikey );
    }

    /*
    Delete index for the current apikey 
    */
    function delete_index()
    {
        if( isset( $_POST['yolink-action-delete-index'] ) )
        {
            $yolink_config = get_option('yolink_config');
            $delete_index_api_url = 'http://index.yolink.com/index/indexadmin?action=del&ak=' . urlencode($yolink_config['yolink_apikey']);
            $request = new WP_Http;
            $out = $request->get( $delete_index_api_url );
        }
    }
    function save_crawl_options()
    {
        $yolink_config = get_option('yolink_config');
        if( isset( $_POST['yolink-action-crawl'] ) )
        {    
            check_admin_referer('yolink_crawl');    
            $this->allowed_post_types = (array) $_POST['post-type-crawl'];
            $yolink_config['allowed_post_types'] = $this->allowed_post_types;
            update_option('yolink_config', $yolink_config );
        }
    }
    
    function save_options()
    {
        if( isset( $_POST['yolink-action-options'] ) && !check_admin_referer('yolink_config') )
        {
            return false;
        }
        
        $options = array();    
        $options = get_option('yolink_config');
        $old_options = $options;
        $use_extended = ( isset( $_POST['yolink_extended_show'] ) ) ? '1' : '0';
        $options['use_extended'] = $use_extended;
        update_option('yolink_config', $options );
    }
    
    function yolink_affiliate()
    {
        $dir = WP_PLUGIN_DIR.'/yolink-search/';
        
        if ($handle = opendir($dir))
        {
            while ($file = readdir($handle)) 
            {
                if ($file != "." && $file != "..") 
                {
                    // Look for txt file with name that is eight alphanumeric characters long
                    $regex = '/[A-Fa-f0-9]{8}\.txt/is';
                    if(preg_match($regex, $file)) 
                    {
                        $affiliate_id = file_get_contents($dir.$file);
                        closedir($handle);
                        return $affiliate_id;
                    }
                }
            }

        }
        else 
        {
            return;
        }
    }
    
    function yolink_save_affiliate($apikey, $affid)
    {
        $url = 'http://signup.yolink.com/yoadmin/questions';
        $qna_string = '"api_key":"'.urlencode($apikey).'","name":"","email":"'.urlencode(get_option('admin_email')).'","email_again":"'.urlencode(get_option('admin_email')).'","website":"'.urlencode(get_option('siteurl')).'","referral":"'.urlencode($affid).'","license_agreement_check":"on"';
        $qs = '?';
        $qs .= 'apikey=' . urlencode($apikey) . '&qna=' . $qna_string;
        $http = new WP_Http;
        $api_url = $url . $method . $qs;
        $data = $http->get( $api_url );

        return array( $data );
    }
    
    function yolink_register()
    {
        $url = 'http://signup.yolink.com/yoadmin/';
        $method = 'register-external-user';
        $user_object = array();
        $user_object = (object) $user_object;
        $parameters = array(
            'e'    => urlencode(get_option('admin_email')),
            'p'    => 'wpengine.com',
            'o'    => 'json',
            'id' => parse_url( urlencode(get_option('siteurl')), PHP_URL_HOST ) . '_' . urlencode(str_replace( array( '@', '.', '+' ), array( '_at_', '_dot_', '_plus_' ), get_option('admin_email')) ),
            'g' => 'true',
            'i'    => json_encode( $user_object ),
            'callback' => '',
        );
        $qs = '?';
        foreach( $parameters as $key => $val )
        {
            $qs .= $key . '=' . $val . '&';
        }
        $http = new WP_Http;
        $api_url = $url . $method . $qs;
        $data = $http->get( $api_url );
        return array( $data );
    }
    
    function yolink_search( $term )
    {
        if( !$this->api_key )
        {
            return false;
        }

        $url = 'http://index.yolink.com/index/';
        $method = 'search';
        $user_object = array();
        $user_object = (object) $user_object;
        
            
        $parameters = array(
            'ak'=> $this->api_key,
            'q'    => $term,
            'o'    => 'json',
            'a'    => $this->search_options,
            'c'     => get_option('posts_per_page'),
            'callback' => '',
        );
        $qs = '?';
        foreach( $parameters as $key => $val )
        {
            $qs .= $key . '=' . $this->urlencode( $val ) . '&';
        }
        
        $api_url = $url . $method . $qs;
                
        $http = new WP_Http;
        $args = array(
            'headers'        => array( 'Content-Type' => 'application/json; charset=utf-8')
        );
        $out_raw = $http->get( $api_url );
        $out = json_decode($out_raw['body']);
        $results = array();
        if( isset( $out->urls ) )
        {
            foreach( $out->urls as $result )
            {
                if( isset( $result->annotation ) )
                {
                    $results[] = $result->annotation;
                }
            }
        }
        return $results;
    }
    
    function get_search_form( $form )
    {
        $search = get_search_query();
        $search = mysql_real_escape_string(strip_tags(trim($search)));
        $search = htmlentities($search, ENT_QUOTES, "UTF-8");

        $form = '<form role="yolink_search" method="get" id="yolink_searchform" action="' . home_url( '/' ) . '" >
        <div><label class="screen-reader-text" for="s">' . __('Search for:', 'yolink') . '</label>
        <input type="text" value="' . $search . '" name="s" id="s" /> 
        <input type="submit" id="searchsubmit" value="'. esc_attr__('Search', 'yolink') .'" />
        <cite style="float:right; margin-right:20px">' . __('Powered by ', 'yolink') . '<a href="http://yolink.com/yolink/plugins/whichplugin.jsp" target="_blank"><img src="http://www.yolink.com/yolink/images/yolink_logo.png" alt="' . __('Powered by yolink', 'yolink') . '" width="41" height="15" style="vertical-align:text-bottom;"/></a>
        </cite>
        </div>
        </form>';
        return $form;
    }
    
    function wp_http_request_log( $response, $type, $transport=null ) 
    {
        if ( $type == 'response' ) 
        {
            $debug = "$transport: {$response['response']['code']} {$response['response']['message']}";
            foreach ( $response['headers'] as $header => $value )
            {
                $debug .= "\t". trim( $header ) . ': ' . trim($value);
            }
            if ( isset($response['body']) )
            {
                $debug .= ( "Response body: " . trim($response['body']) );
            }
            $this->http_requests[] = $debug;
        }
    }

    function wp_http_response_log( $response, $r, $url ) 
    {
        $debug = "{$r['method']} {$url} HTTP/{$r['httpversion']}";
        $debug .= "{$response['response']['code']} {$response['response']['message']}";
        $this->http_requests[] = $debug;
        return $response;
    }
    
    /*
        Used in multisite situation. When $post is from another blog, it must 
        switch blog context.
    */
    function hook_the_post( $post )
    {
        if( is_search() )
        {
            $the_blog_id = $this->get_blog_id_from_post( $post );
            if( isset( $the_blog_id ) )
            {
                switch_to_blog( $the_blog_id );
            } 
        }
        return $post;
    }

    /*
        Substitute $posts with yolink search results.
    */
    function set_yolink_search_results($posts, $query=false)
    {
        if( is_search() &&
            $this->search_query_state == 0 )
        {
            $this->search_query_state = 1; // do it only once
            global $blog_id;
            $blog_id_last_item = $blog_id;
            $posts = array();
            $results = get_transient( 'yolink_search_results_' . get_search_query() . '_' . $this->search_options );
            if( !isset( $results ) || false === $results )
            {
                $results = $this->do_search( get_search_query() );
            }
            $this->save_search_result_to_widget_format( $results );        

            $this->load_script_once = 'true';
            // save the results for rendering chunks
            $this->yolink_search_results = array();
            if( isset( $results ) && false !== $results )
            {
                foreach ( $results as $annotation )
                {
                    $post = get_blog_post($annotation->wp_blog_id, $annotation->wp_post_id);
                    $posts[] = $post;

                    # 2.3.1 -- Check if a URL customization is present.
                    $url     = @$annotation->actual_url;

                    $map = array( 'blog_id' => $annotation->wp_blog_id, 'post' => $post, 'actual_url' => $url );
                    $this->yolink_search_results[] = $map;    
                    $blog_id_last_item = $annotation->wp_blog_id;
                }

                if( $blog_id_last_item != $blog_id )
                {
                    // Here we create a fake post as the last item, so that we can reset the blog context to
                    // the original
                    $post                    = new stdClass();
                    $post->ID                = 0;
                    $post->post_author       = '';
                    $post->post_date         = date('Y-m-d H:m:s');
                    $post->post_type         = 'post';
                    $post->post_title        = '';
                    $post->post_content      = '';
                    $post->post_status       = 'open';
                    $post->comment_status    = 'open';
                    $post->ping_status       = 'open';
                    $post->ecpt_yolink_custom_url = '';
                    $posts[]                 = $post;

                    $this->yolink_search_results[] = array( 'blog_id' => $blog_id, 'post' => $post, );
                }
            }
        }
        return $posts;
    }

    /*
        Utility function which retrieves the blog_id from saved global variable
        $yolink_search_results, a blog_id -> post map.
    */
    function get_blog_id_from_post( $post )
    {
        foreach( $this->yolink_search_results as $result )
        {
            if( $result['post'] == $post )
            {
                return $result['blog_id'];    
            }
        }    
    }

    /**
     * Utility function for retrieving the "actual" URL from a post.
     */
    function get_actual_url_from_post( $post, $fallback )
    {
        foreach( $this->yolink_search_results as $result )
        {
            if( $result['post'] == $post ||
                $result['id'] == $post->ID )
            {
                return @$result['actual_url'];
            }
        }

        return $fallback;
    }

    /*
        Test whether bulkcrawl.php is accessible.
    */
    function test_bulkcrawl()
    {
        $bulkcrawl_url = plugins_url('yolink-search/includes/bulkcrawl.php');
        $request       = new WP_Http;
        $out           = $request->post( $bulkcrawl_url );

        if( !is_wp_error( $out ) )
        {
            if( $out['response']['code'] == '200' )
            {
                return true;
            }
        }

        return false;
    }
    
    /*
        Return json object of list of yolink apikeys for all blogs.
        For single site, return the current blog's apikey.    
    */
    function get_yolink_apikeys_for_all_blogs()
    {
        $yolink_apikeys = array();
        if( is_multisite() && is_super_admin() )
        {
            global $wpdb;
            global $blog_id;

            $blogs= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_blogs ORDER BY blog_id" ) );
            foreach( $blogs as $blog )
            {
                switch_to_blog( $blog->blog_id );
                $yolink_config = get_option('yolink_config');
                if( isset( $yolink_config ) )
                {
                    $apikey = $yolink_config['yolink_apikey'];
                    if( isset( $apikey ) )
                    {
                        $apikey_info = array(
                                    'apikey'    => $apikey,
                                    'name'        => $blog->domain . $blog->path,
                                );
                        $yolink_apikeys['apikeys'][] = $apikey_info;
                    }
                }
                if( is_multisite() )
                {
                    restore_current_blog();
                }
            }
        }
        else
        {
            $yolink_config = get_option('yolink_config');
            if( isset( $yolink_config ) )
            {
                $apikey = $yolink_config['yolink_apikey'];
                if( isset( $apikey ) )
                {
                    $apikey_info = array(
                                    'apikey'    => $apikey,
                                    'name'        => '',
                                );
                    $yolink_apikeys['apikeys'][] = $apikey_info;
                }
            }
        }
        return json_encode($yolink_apikeys);
    }

    /*
      Return total number of posts that can be indexed of the current blog
    */
    function num_of_indexing_candidate()
    {
        global $wpdb;
        if( isset( $_POST['post-type-crawl'] ) )
        {
            $this->allowed_post_types = (array)$_POST['post-type-crawl'];
        }
            
        $post_type_in = array();
        foreach( $this->allowed_post_types as $post_type )
        {
            $post_type_in[] = '"' . esc_sql( $post_type ) . '"';
        }

        $post_type_in = '(' . implode(',', $post_type_in) . ')';
        $post_count   = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM $wpdb->posts WHERE post_status=%s AND post_type IN $post_type_in", 'publish' ) );
       
        return $post_count;
    }
}
?>
