<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo" class="content_right thin_border">

			<?php

				get_sidebar( 'footer' );
			?>

			<div id="site-generator">
				<p>Copyright &copy; 2001 - 2012 DD9, Inc. All rights reserved. <a href="/wp-admin/" style="color:#CCC; font-size:8px; float:right" >Admin Login</a></p>

<div itemscope itemtype="http://data-vocabulary.org/Organization" id="microformat_block"> 
    <span itemprop="name">DD9</span>  &#8226; 
     <span itemprop="address" itemscope 
      itemtype="http://data-vocabulary.org/Address">
      <span itemprop="street-address">4725 16th St. #104</span>  &#8226;  
      <span itemprop="locality">Boulder</span>,
      <span itemprop="region">CO</span>
      <span itemprop="postal-code">80304</span>
      <span itemprop="country-name">USA</span>  
    </span>
    <span itemprop="tel">303-417-6369</span>  &#8226; 
    <a href="http://dd9.com/" itemprop="url" title="DD9 Website">http://dd9.com/</a>  &#8226; 
<a href="http://g.co/maps/5gszk" title="DD9 on Google Maps" target="_blank">Map</a>
</div>


<div id="social">			

<g:plusone size="small" count="false" href="https://dd9.com"></g:plusone>
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<a href="https://www.twitter.com/DD9inc" title="Follow DD9 on Twitter"><img src="https://twitter-badges.s3.amazonaws.com/t_mini-b.png" alt="Follow DD9 on Twitter"/></a>

<a href="http://www.flickr.com/photos/dd9inc/"><img src="<?php bloginfo('template_url'); ?>/images/flickr.png" alt="DD9 On Flickr" width="16" height="16" /></a>

<a href="https://github.com/organizations/DD9" title="DD9 on GitHub"><img src="<?php bloginfo('template_url'); ?>/img/github_icon.png" alt="DD9 On GitHub" width="16" height="16" /></a>

<div id="fb-root" style="display:inline"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like" style="display:inline" data-href="https://www.facebook.com/DD9inc" data-send="false" data-layout="button_count" data-width="80" data-show-faces="false" data-font="tahoma"></div>



</div><!-- #social -->
            
            </div>
	</footer><!-- #colophon -->
    
   
</div><!-- #page -->

<?php wp_footer(); ?>


</body>
</html>