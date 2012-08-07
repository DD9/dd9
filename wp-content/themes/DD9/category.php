<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 
$thecategory = get_category($cat);
?>

<?php if ( have_posts() ) : ?>
      <div id="secondary" class="two_column blog">
      
		
        <?php if ($thecategory->category_parent == '3' || $thecategory->cat_ID == '3'): ?> 
       
         
            <h4 class="subheading_full_width"><a href="/category/design-screenshots/" title="DD9 Design Stream Home">Design Stream</a></h4>
              <div class="block_content">
                <h1 class="secondary"><?php
						printf( __( '%s', 'twentyeleven' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?> </h1>
                <p class="top_line"></p>
                <?php echo category_description( $thecategory->cat_ID ); ?> 
                <p class="top_line"></p>
                
                <h4>Design Stream Categories </h4>
                <ul class="blog_categories">
                  <?php wp_list_categories( array(
                          'orderby'            => 'name',
                          'order'              => 'ASC',
                          'child_of'           => '3',
                          'show_count'         => 1,
                          'title_li'           => '',
                  )); ?>
                </ul>  
              
        
        <?php else: ?>	
        
        
            <h4 class="subheading_full_width"><a href="/blog/" title="DD9 Blog Home">Blog</a></h4>
              <div class="block_content">
                <h1 class="secondary"><?php
						printf( __( '%s', 'twentyeleven' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?> </h1>
                <p class="top_line"></p>
                <?php echo category_description( $thecategory->cat_ID ); ?> 
                <p class="top_line"></p>
                <h4> Categories </h4>
                <ul class="blog_categories">
                  <?php wp_list_categories( array(
                          'orderby'            => 'name',
                          'order'              => 'ASC',
                          'exclude'            => '3',
                          'show_count'         => 1,
                          'title_li'           => '',
                  )); ?>
                </ul>
               
                
		<?php endif; ?>	
			
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

				<aside id="archives" class="widget">
					<h4 class="widget-title"><?php _e( 'Archives', 'twentyeleven' ); ?></h4>
					<ul>
						<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
					</ul>
				</aside>

				<aside id="meta" class="widget">
					<h4 class="widget-title"><?php _e( 'Meta', 'twentyeleven' ); ?></h4>
					<ul>
						<?php wp_register(); ?>
						<li><?php wp_loginout(); ?></li>
						<?php wp_meta(); ?>
					</ul>
				</aside>

			<?php endif; // end sidebar widget area ?>
            
            </div><!-- .block_content -->
		</div><!-- #secondary .widget-area -->

		<div class="content_right blog">


				<?php /*?><?php twentyeleven_content_nav( 'nav-above' ); ?><?php */?>

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

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			

		</div><!-- #content_right -->
<?php endif; ?>
<?php get_footer(); ?>
