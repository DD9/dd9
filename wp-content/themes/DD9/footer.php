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



	<footer id="colophon" role="contentinfo" >
 
 
<div class="block_container full_width clearfix">
 
 <div id="social" class="two_column thin_border">			

<ul id="social_links" class="social_icons">

 <li>
   <a href="https://www.twitter.com/DD9inc" title="DD9 on Twitter" target="_blank">
     <i class="icon-twitter-sign"></i>
   </a>
 </li>
 <li>
   <a href="https://www.facebook.com/DD9inc" title="DD9 On Facebook" target="_blank">
    <i class="icon-facebook-sign"></i>
   </a>
 </li>

 <li>
    <a href="http://www.flickr.com/photos/dd9inc/" title="DD9 On Flickr" target="_blank">
     <i class="icon-flickr-sign"></i>   
    </a>
 </li>

 <li>
    <a href="https://github.com/DD9" title="DD9 on GitHub" target="_blank">
      <i class="icon-github-sign"></i> 
    </a>
 </li>

 <li>
    <a href="http://www.linkedin.com/company/dd9" title="DD9 on LinkedIn" target="_blank">
      <i class="icon-linkedin-sign"></i> 
    </a>
 </li>


 
</ul> 

</div><!-- #social -->
 
    
    <div class="content_right thin_border">

			<?php

				get_sidebar( 'footer' );
			?>

			<div id="site-generator">
				<p>Copyright &copy; 2001 - 2012 DD9, Inc. All rights reserved. <a href="/wp-admin/" style="color:#CCC; font-size:8px; float:right" rel="nofollow">Admin Login</a></p>

<div itemscope itemtype="http://data-vocabulary.org/Organization" id="microformat_block"> 
    <span itemprop="name">DD9</span>, Inc.  &#8226; 
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



            
            </div>
            
            </div><!-- .content_right -->
    
    </div><!-- .block_containter -->
            
	</footer><!-- #colophon -->
    
 </div><!-- #page -->  


<?php wp_footer(); ?>


</body>
</html>