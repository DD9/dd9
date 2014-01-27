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
</div><!-- #page --> 


<footer id="colophon" role="contentinfo" >
 
  <div class="block_container full_width clearfix">
  	
    <div class="two_column footer_contact">
   		<h4 class="footer_name">DD9, Inc.</h4>
      
      <p class="footer_address"><a href="http://goo.gl/maps/N1olx" title="DD9 on Google Maps" target="_blank">4725 16th St. #104 <br />
      Boulder, CO 80304</a> </p>
      
      <p><strong><i class="icon-phone"></i></strong> 303-417-6369 <br />
      <a href="mailto:info@dd9.com" class="footer_email">info@dd9.com</a></p>
    </div>
   
      
    <div class="content_right">
    	
      <ul id="social_links">
        <li>
          <a href="https://www.twitter.com/DD9inc" title="DD9 on Twitter" target="_blank"><i class="icon-twitter-sign"></i></a>
        </li>
        <li>
          <a href="https://www.facebook.com/DD9inc" title="DD9 On Facebook" target="_blank"><i class="icon-facebook-sign"></i> </a>
        </li>
        <li>
          <a href="http://www.flickr.com/photos/dd9inc/" title="DD9 On Flickr" target="_blank"><i class="icon-flickr-sign"></i></a>
        </li>
        <li>
          <a href="https://github.com/DD9" title="DD9 on GitHub" target="_blank"><i class="icon-github-sign"></i> </a>
        </li>
        <li>
          <a href="http://www.linkedin.com/company/dd9" title="DD9 on LinkedIn" target="_blank"><i class="icon-linkedin-sign"></i></a>
        </li>
      </ul> 
      
      <div id="footer_tagline">
      	Design • Development • Branding
      </div><!-- /footer_tagline -->
      
      
    	<?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => false, 'menu_id' => 'footer_nav' ) ); ?>
  
      <div id="footer_utility">
        Copyright &copy; 2001 - <?php echo date('Y'); ?>  DD9, Inc. All rights reserved. <a href="/wp-admin/" style="color:#CCC; font-size:8px; float:right" rel="nofollow">Admin Login</a>
      </div><!-- /footer_utility -->
            
     </div><!-- .content_right -->
      
  </div><!-- .block_container -->
            
</footer><!-- #colophon -->

 


<?php wp_footer(); ?>


</body>
</html>