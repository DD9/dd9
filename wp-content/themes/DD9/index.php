<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 */

get_header(); 

//exclude category id 3 ("Design Screenshots")
unset($wp_query->query['pagename']);
query_posts(array_merge($wp_query->query, array('cat' => -3)));

?>

<div class="block_container full_width clearfix">
     <div id="secondary" class="two_column blog">
        <h4 class="subheading_full_width"><a href="/blog/" title="DD9 Blog Home">blog</a></h4>
        
        <div class="block_content"> 
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

			<?php endif; // end sidebar blog area ?>
          
          </div><!-- .block_content -->
	  </div><!-- #secondary.two_column -->  


		<div class="content_right blog">
			
			
<?php /* Exclude Category ID "3" ("Design Screenshots") from the results. */ ?>            
			<?php if (is_home() && have_posts()) : ?>

				<?php /*?><?php twentyeleven_content_nav( 'nav-above' ); ?><?php */?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>

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

			<?php endif; ?>

			
		</div><!-- #content_right -->
 </div><!-- .block_container.full_width -->

<?php get_footer(); ?>