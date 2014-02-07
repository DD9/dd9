<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<title><?php wp_title(''); ?></title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link href='http://fonts.googleapis.com/css?family=Lato:400,400italic,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<script type="text/javascript" src="http://fast.fonts.net/jsapi/0e507d20-4e4f-4641-b84f-d59dd0dc677b.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.jsticky.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/custom.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-24980306-1', 'dd9.com');
  ga('send', 'pageview');

</script>
</head>

<body <?php body_class(); ?>>
  <div id="page" class="hfeed">
    <header id="branding" role="banner">
    
      <div id="prenav">
        
        <script type="text/javascript">
            var euser = "info";
            var edomain = "dd9.com";
            var esubCon = "DD9 Website Inquiry";
            document.write('<a href="mailto:' + euser + '@' + edomain +'?subject=' + esubCon +' " ' +' >' + euser + '@' + edomain +'<\/a>');
        </script> 
             
        <a href="tel:3034176369" id="header_tel">(303)417-6369</a> &nbsp; <a href="http://go.dd9.com/" title="DD9 Client Extranet Login" rel="nofollow" id="client_login">Client Portal</a> &nbsp; <a href="http://basecamp.com/1922309" title="DD9 on Basecamp" rel="nofollow" id="basecamp_login">Active Projects</a> 
      </div>
      
      <a href="/" title="DD9 Home" id="home_link"><img src="<?php bloginfo('template_url'); ?>/img/DD9_logo_v3.png" width="132" height="80" alt="DD9 Logo"></a>
    
      <nav id="access" role="navigation" class="clearfix">
        <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
      </nav><!-- #access -->
    
      <div id="beta">Design &#8226; Development &#8226; Branding</div>
      
      <ul id="header_contact_mobile">
        <li><a target="_blank" href="tel:3034176369 "><i class="icon-phone"></i></a></li>
        <li><a target="_blank" href="mailto:info@dd9.com"><i class="icon-envelope"></i></a></li>
      </ul>
    
    </header>
      
    
  
  
    <div id="main" class="clearfix">
