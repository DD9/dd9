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
<meta content="width=device-width; initial-scale=1.0" name="viewport">
<title>
<?php wp_title(''); ?>
</title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<meta name="google-site-verification" content="jV9HcFOm5nSF-iidDOCEeIpjkIgGGYxKkc0E30FCyLE" />
<meta name="google-site-verification" content="mniubP5QuKqY3c9zhYIaQTcWdw6J3B12tSty5Ia4ADA" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<!--link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,400italic|Oswald:400' rel='stylesheet' type='text/css'-->
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

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.isotope.min.js"></script> 
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/flexslider/jquery.flexslider-min.js"></script>

<script type="text/javascript" charset="utf-8">
  $(function() {
    $('.flexslider').flexslider({
      animation: "fade",
		  slideshowSpeed: 6000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
		  animationDuration: 400,         //Integer: Set the speed of animations, in milliseconds
		  directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
		  controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
		  keyboardNav: true
    });

  	$(".toggle_grid a").click(function () {
  	  $("#page").toggleClass("grid", 200);
  	});

  	// cache container
  	var $container = $('ul#thumbnail_grid');
  	// initialize isotope
  	$container.isotope({
  	  // options
  	  itemSelector : 'ul#thumbnail_grid li',
  	});

  	// filter items when filter link is clicked
  	$('.filters a').click(function(){
  	  var selector = $(this).attr('data-filter');
  	  $container.isotope({ filter: selector });
  	  return false;
  	});

  });
</script>


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24980306-1']);
  _gaq.push(['_setDomainName', 'dd9.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body <?php body_class(); ?>>
    <div id="page" class="hfeed">
	<header id="branding" role="banner">
	
            <div id="prenav">
            <span class="toggle_grid"><a href="#">Show Grid</a></span> &nbsp; 
   <script type="text/javascript">
  var euser = "info";
  var edomain = "dd9.com";
  var esubCon = "DD9 Website Inquiry";
  document.write('<a href="mailto:' + euser + '@' + edomain +'?subject=' + esubCon +' " ' +' >' + euser + '@' + edomain +'<\/a>');
</script> &nbsp;
         
   <a href="tel:3034176369" id="header_tel">(303)417-6369</a> &nbsp; <a href="http://go.dd9.com/" title="DD9 Client Extranet Login" rel="nofollow" id="client_login">Client Portal</a> &nbsp; <a href="http://basecamp.com/1922309" title="DD9 on Basecamp" rel="nofollow" id="basecamp_login">Active Projects</a> 
   </div>
  
    		<a href="/" title="DD9 Home" id="home_link"><img src="<?php bloginfo('template_url'); ?>/img/DD9_logo_v2.png" width="132" height="80" alt="DD9 Logo"></a>



				<?php //get_search_form(); ?>


			 <nav id="access" role="navigation" class="clearfix">
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav>
			    <!-- #access -->
	</header>
    
 <div id="beta">Design &#8226; Development &#8226; Branding</div>
	<!-- #branding -->


	<div id="main" class="clearfix">
