<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

<div class="block_container full_width clearfix">
    

			<?php if ( have_posts() ) : ?>
            
             <div class="two_column">
               <h4 class="subheading breadcrumbs">Search Results For</h4>
                 <div class="block_content border_top">
                   <h1 class="secondary"><?php printf( __( '%s', 'twentyeleven' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
                   
                 </div><!-- .block_content -->
                           
              </div><!-- .two_column -->
               
             <div class="content_right blog">

				<?php twentyeleven_content_nav( 'nav-above' ); ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

				<?php twentyeleven_content_nav( 'nav-below' ); ?>

			   </div><!-- #content_right -->
			
			<?php else : ?>
                <div class="content_right blog">
                  <article id="post-0" class="post no-results not-found">
                     
                          <h4><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h4>
  
                      <div class="entry-content">
                          <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'twentyeleven' ); ?></p>
                          <?php get_search_form(); ?>
                      </div><!-- .entry-content -->
                  </article><!-- #post-0 -->
                </div><!-- #content_right -->
			
			
			<?php endif; ?>

	</div><!-- .block_container.full_width -->  
<?php get_footer(); ?>