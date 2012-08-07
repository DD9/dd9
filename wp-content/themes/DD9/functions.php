<?php

define('GRAPHIC_DESIGN', 4034);
define('WEBSITE_DESIGN', 3621);
$gd_id = GRAPHIC_DESIGN; //these variables are needed for get_posts to work… lame.
$wd_id = WEBSITE_DESIGN;

function my_connection_types() 
{
	if(!function_exists( 'p2p_register_connection_type'))
		return;

	p2p_register_connection_type(array( 
	    'name' => 'project_services',
		'from' => 'project',
		'to' => 'service',
		'sortable' => 'to',
		'admin_column' => 'any'
	));	
	
	
	p2p_register_connection_type(array( 
	    'name' => 'project_clients',
		'from' => 'project',
		'to' => 'client',
        'sortable' => 'to',
		'admin_column' => 'any'
	));	
	
	p2p_register_connection_type(array(
		'name' => 'project_users',
		'from' => 'project',
		'to' => 'user'
	));
	
	p2p_register_connection_type(array(
        'name' => 'project_posts',
        'from' => 'project',
        'to' => 'post'
    ));

}
add_action( 'init', 'my_connection_types', 100 );

function format_short_date($raw)
{
    $parts = split("-", $raw);
    
    if(count($parts) != 2)
        return $raw;
    
    return $parts[1] . "/" . $parts[0];
}

// smart jquery inclusion
if (!is_admin()) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false);
	wp_enqueue_script('jquery');
}

//user profile custom fields
function dd9_user_contactmethods($contactmethods)
{
    return array_merge($contactmethods, array('group' => 'Group', 'relationship' => 'Relationship', 'order' => 'Order'));
}
add_filter('user_contactmethods', 'dd9_user_contactmethods');

//categories get parent category template, if exists
//http://stackoverflow.com/questions/3119961/make-all-wordpress-categories-use-their-parent-category-template
function load_cat_parent_template()
{
    global $wp_query;

    if (!$wp_query->is_category)
        return true; // saves a bit of nesting

    // get current category object
    $cat = $wp_query->get_queried_object();

    // trace back the parent hierarchy and locate a template
    while ($cat && !is_wp_error($cat)) {
        $template = TEMPLATEPATH . "/category-{$cat->slug}.php";

        if (file_exists($template)) {
            load_template($template);
            exit;
        }

        $cat = $cat->parent ? get_category($cat->parent) : false;
    }
}
add_action('template_redirect', 'load_cat_parent_template');

//remove service parent from permalink
function services_link_filter($post_link, $id = 0, $leavename = FALSE)
{
    //based on http://xplus3.net/2010/05/20/wp3-custom-post-type-permalinks/   
    $post = get_post($id);
    
    if(!is_object($post) || $post->post_type != 'service') 
    {
        return $post_link;
    }

    return preg_replace('/^http:\/\/([^\/]*)\/([^\/]+)\/([^\/]+)\/(.+)$/', 'http://$1/$2/$4', $post_link);
}
add_filter('post_type_link', 'services_link_filter', 1, 3);

function get_data_for_user_array($raw)
{
    foreach($raw as $raw_user)
    {
        if($group = get_the_author_meta('group', $raw_user->ID))
        {
            if(!$users[$group]) 
            {
                $users[$group] = array();
            }
    
            $order = get_the_author_meta('order', $raw_user->ID);
            if(!$order && $order != '0') //set default order
            {
                $order = count($users) + 1000;
            }
        
            $name = get_the_author_meta('user_firstname', $raw_user->ID) . ' ' .
                    get_the_author_meta('user_lastname', $raw_user->ID);
        
            $users[$group][$order] = array(
                'id' => $raw_user->ID,
                'name' => $name,
    			'display_name' => $raw_user->display_name,
                'relationship' => get_the_author_meta('relationship', $raw_user->ID),
                'description' => get_the_author_meta('user_description', $raw_user->ID),
                'order' => $order,
                'posts_url' => get_author_posts_url($raw_user->ID),
                'url' => $raw_user->user_url,
                'clean_url' => str_replace('http://','', str_replace('www.', '', $raw_user->user_url)),
                'email' => $raw_user->user_email,
                'projects' => get_posts(array(
                    'connected_type' => 'project_users',
                	'post_type' => 'project',
                	'suppress_filters' => false,
                	'numberposts' => 3,
                	'connected_to' => $raw_user->ID,
                	'connected_orderby' => '_order_from'
                ))
            );
        }
    }
    
    foreach(array('team','alumni','associates') as $group)
    {
        if(!$users[$group])
        {
            $users[$group] = array();
        }
        else
        {
            ksort($users[$group]);        
        }
    }
    
    return $users;
}



// Custom excerpt length
 
function custom_excerpt($length){
 global $post;
 $content = strip_tags($post->post_content);
 preg_match('/^\s*+(?:\S++\s*+){1,'.$length.'}/', $content, $matches);
 echo "<p>" . $matches[0] . " ...</p>";
}


function the_h1_override()
{
    if($value = get_post_meta(get_queried_object_id(), 'h1_override', true))
    {
        echo $value;
    }
    else 
    {
        echo get_the_title(get_queried_object_id());
    }
}


/* custom meta boxes for attachments */
/* from http://net.tutsplus.com/tutorials/wordpress/creating-custom-fields-for-attachments-in-wordpress/ */
$attachment_fields = array(
	'original' => array(
		'label' => 'Original',
		'type' => 'checkbox'
	),
	'draft' => array(
		'label' => 'Draft',
		'type' => 'checkbox'
	)
);

function my_attachment_fields_to_edit($form_fields, $post) 
{  
	global $attachment_fields;
    
    foreach($attachment_fields as $id=>$field)
    {
    	$field['value'] = get_post_meta($post->ID, $id, true);
    	$name = "attachments[{$post->ID}][$id]";
    	
    	switch($field['type'])
    	{
    		case 'select':
    		
    			$field['input'] = 'html';
    			$field['html'] = "<select name='$name' id='$name'>" . get_select_options($field['options'],$field['value']) . "</select>";
	    		
    			break;
    		
    		case 'checkbox':
    			$field['input'] = 'html';
    			$field['html'] = "<input type='checkbox' name='$name' id='$name' ";
    			if($field['value'])
    				$field['html'] .= 'checked';
    			$field['html'] .= "/>";
    		
    			break;
    			
    		case 'text':
    			$field['input'] = 'html';
    			$field['html'] = "<input name='$name' id='$name' value='" . $field['value'] . "' />";
    			break;
    	}
    	
    	if(isset($field['type']))
	    	unset($field['type']);
	    if(isset($field['options']))
	    	unset($field['options']);
	    	
		$form_fields[$id] = $field;
    }
  
    return $form_fields;  
}  
add_filter("attachment_fields_to_edit", "my_attachment_fields_to_edit", null, 2);  

function get_select_options($options, $current)
{
	$result = '';
	
	foreach($options as $value=>$name)
	{
		$result .= "<option value='$value' ";
		
		if($value == $current)
			$result .= 'selected';
		
		$result .= ">$name</option>";	
	}
	
	return $result;
}

function my_attachment_fields_to_save($post, $attachment) 
{  
  	global $attachment_fields;
  	
  	foreach($attachment_fields as $id=>$field)
  	{
  		update_post_meta($post['ID'], $id, $attachment[$id]);
  	}

    return $post;  
}  
add_filter("attachment_fields_to_save", "my_attachment_fields_to_save", null, 2);


function get_image_class($id)
{
    $draft = get_post_meta($id, 'draft', true) ? 'draft' : '';
    $original = get_post_meta($id, 'original', true) ? 'original' : '';
    
    $class = $draft;
    
    if($draft && $original)
        $class .= ' ';
        
    $class .= $original;
    
    return $class;
}

function set_custom_sitemap_priorities($score, $item, $freq)
{
    switch($item->post_type)
    {
        case 'post':
            return $score * 0.5;
        
        case 'client':
            return $score * 0.4;
            
        case 'project':
            return $score * 0.6; 
    }
    
    return $score;
}
add_filter('bwp_gxs_priority_score', 'set_custom_sitemap_priorities', 10, 3);

/**  Todd's Test **/
function get_the_excerpt_by_id($post_id) {
  global $post;  
  $save_post = $post;
  $post = get_post($post_id);
  $output = get_the_excerpt();
  $output = '<p>' . $output . '</p>';
    $post = $save_post;
  
  return $output;
}



/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Tell WordPress to run twentyeleven_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'twentyeleven_setup' );

if ( ! function_exists( 'twentyeleven_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyeleven_setup() in a child theme, add your own twentyeleven_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_setup() {

	/* Make Twenty Eleven available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Eleven, use a find and replace
	 * to change 'twentyeleven' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentyeleven', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( dirname( __FILE__ ) . '/inc/theme-options.php' );

	// Grab Twenty Eleven's Ephemera widget.
	require( dirname( __FILE__ ) . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twentyeleven' ) );

	// Add support for a variety of post formats
	//add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// Add support for custom backgrounds
	//add_custom_background();

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// The next four constants set how Twenty Eleven supports custom headers.

	// The default header text color
	define( 'HEADER_TEXTCOLOR', '000' );

	// By leaving empty, we allow for random image rotation.
	define( 'HEADER_IMAGE', '' );

	// The height and width of your custom header.
	// Add a filter to twentyeleven_header_image_width and twentyeleven_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyeleven_header_image_width', 1000 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyeleven_header_image_height', 288 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	//set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add Twenty Eleven's custom image sizes
	//add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
	//add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist

	// Turn on random header image rotation by default.
	//add_theme_support( 'custom-header', array( 'random-default' => true ) );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyeleven_admin_header_style(), below.
	//add_custom_image_header( 'twentyeleven_header_style', 'twentyeleven_admin_header_style', 'twentyeleven_admin_header_image' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'wheel' => array(
			'url' => '%s/images/headers/wheel.jpg',
			'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Wheel', 'twentyeleven' )
		),
		'hanoi' => array(
			'url' => '%s/images/headers/hanoi.jpg',
			'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Hanoi Plant', 'twentyeleven' )
		)
	) );
}
endif; // twentyeleven_setup

/** Attempt to fix JPEG **/
add_filter( 'jpeg_quality', 'jpeg_full_quality' );
function jpeg_full_quality( $quality ) { return 91; }


/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function twentyeleven_excerpt_length( $length ) {
	return 50;
}
add_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function twentyeleven_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'more', 'twentyeleven' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function twentyeleven_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyeleven_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyeleven_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function twentyeleven_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyeleven_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyeleven_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function twentyeleven_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyeleven_page_menu_args' );

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_widgets_init() {

	register_widget( 'Twenty_Eleven_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

	register_sidebar( array(
		'name' => __( 'Showcase Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-2',
		'description' => __( 'The sidebar for the optional Showcase Template', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'twentyeleven' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'twentyeleven' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'twentyeleven' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentyeleven_widgets_init' );

/**
 * Display navigation to next/previous pages when applicable
 */
function twentyeleven_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty Eleven 1.0
 * @return string|bool URL or false when no link is present.
 */
function twentyeleven_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function twentyeleven_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'twentyeleven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyeleven_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
    <?php $i = 1; ?>
	<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="post comments clearfix">
            
            <ul class="post_details">
 
              <li class="post_author"><?php comment_author( $comment_ID ); ?> <span class="post_time"><?php comment_date( 'F jS, Y', $comment_ID ); ?></span> </li>
              
              <li id="author-info">
                  <div id="author-avatar">
                      <?php
						  $avatar_size = 68;
						  if ( '0' != $comment->comment_parent )
							  $avatar_size = 68;
	  
						  echo get_avatar( $comment, $avatar_size );
						  
					  ?>
                  </div><!-- #author-avatar -->
                 
              </li><!-- #entry-author-info -->
              
              <li class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			  </li><!-- .reply -->
             
           </ul><!-- .post_details -->
           
           <div class="comment_content"><?php comment_text(); ?></div>  
            
            <?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
            

            <?php if ( $comment->comment_approved == '0' ) : ?>
                <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
                <br />
            <?php endif; ?>

			
            
            
		</article><!-- #comment-## -->
    <?php $i++; ?>
	<?php
			break;
	endswitch;
}
endif; // ends check for twentyeleven_comment()

if ( ! function_exists( 'twentyeleven_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own twentyeleven_posted_on to override in a child theme
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'twentyeleven' ), get_the_author() ),
		esc_html( get_the_author() )
	);
}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_body_classes( $classes ) {

	if ( ! is_multi_author() ) {
		$classes[] = 'single-author';
	}

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'twentyeleven_body_classes' );

