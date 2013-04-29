<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>
      
      <div class="block_container full_width clearfix">
		<div class="secondary">
          <div class="two_column">
            <h4 class="subheading_full_width"><span>Contact</span></h4>
              <div class="block_content">
                 <h3><a href="http://g.co/maps/5gszk" title="DD9 on Google Maps" target="_blank">DD9, Inc. <br/>4725 16th St. #104, Boulder, CO 80304</a></h3>
                <p class="top_line"></p>
                <h3><a href="tel:3034176369">(303) 417-6369</a></h3>
                <p class="top_line"></p>
                
				<script type="text/javascript">
                  var euser = "info";
                  var edomain = "dd9.com";
                  var esubCon = "DD9 Website Inquiry";
                  document.write('<h3><a href="mailto:' + euser + '@' + edomain +'?subject=' + esubCon +' " ' +' >' + euser + '@' + edomain +'<\/a><\/h3>');
                </script>
                <p class="top_line"></p>
                
                  <img src="http://dd9.com/wp-content/uploads/office_screengrab-234x162.png" alt="DD9 Boulder Office Photo" />  
              </div>
          </div><!-- .two_column -->         
      
        </div><!-- .secondary -->

	    <div class="content_right"> 

		  <?php the_post(); ?>

          <?php // get_template_part( 'content', 'page' ); ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <header>
                  <h1><?php the_h1_override(); ?></h1>
              </header><!-- .entry-header -->
          
              <div class="entry-content">
                  <?php the_content(); ?>
                           
          
          

              <div id="wufoo-x7x3p9">
              Fill out <a href="http://dd9.wufoo.com/forms/x7x3p9"> web design project form</a>.
              </div>
              
              <script type="text/javascript">var x7x3p9;(function(d, t) {
              var s = d.createElement(t), options = {
              'userName':'dd9', 
              'formHash':'x7x3p9', 
              'autoResize':true,
              'height':'1504',
              'async':true,
              'header':'show'};
              s.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'wufoo.com/scripts/embed/form.js';
              s.onload = s.onreadystatechange = function() {
              var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
              try { x7x3p9 = new WufooForm();x7x3p9.initialize(options);x7x3p9.display(); } catch (e) {}}
              var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
              })(document, 'script');</script>       
                  
                  
              </div><!-- .entry-content -->
              <footer class="entry-meta">
                  <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
              </footer><!-- .entry-meta -->
          </article><!-- #post-<?php the_ID(); ?> -->
                

           </div><!-- .content_right -->   
		 </div><!-- .block_container --> 

<?php get_footer(); ?>