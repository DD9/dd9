<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

<?php get_sidebar(); ?>

		
		<div class="content_right blog">

			<?php if ( have_posts() ) : ?>

				<?php /*?><?php twentyeleven_content_nav( 'nav-above' ); ?><?php */?>
                
                <ul id="thumbnail_grid" class="clearfix">	
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
				
                  <li>
					<article id="post-<?php the_ID(); ?>" class="design_stream_preview">
                       
					<?php  $images = get_posts(array(
                        'post_type'=>'attachment',
                        'numberposts'=>1,
                        'orderby'=>'menu_order',
                        'order'=>'asc',
                        'post_parent'=>$post->ID,
                        'post_mime_type'=>'image'
                    ));
                    ?>
                    
					<?php if($images): ?>				
                        
                        <?php foreach($images as $image): ?>
                            <div class="preview_thumbnail">
                              
                                <?php if($image->ID): $image_thumb = wp_get_attachment_image_src($image->ID, 'thumbnail');$image_full = wp_get_attachment_image_src($image->ID, 'large'); ?>
                                    <img src="<?= $image_thumb[0] ?>" width="234" alt="<?php the_title(); ?>"  title="<?= $image->post_excerpt ?>" />
                                <?php endif; ?>
                            
                            </div><!-- .preview_thumbnail -->
                        
                                        
                            <div class="info_panel">
                                
                                <a href="<?= $image_full[0] ?>" class="fancybox thumbnail_title"><?php the_title(); ?></a>
                               
                                <div class="screenshot_meta">
									<?php the_time('m/d/y'); ?> by <?php the_author() ?> in
                                    
                                    <?php
                                    /* translators: used between list items, there is a space after the comma */
                                    $categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
                                    if ( $categories_list ):
                                    ?>
                                        <span class="cat-links">
                                            <?php printf( __( '%2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
                                            $show_sep = true; ?>
                                        </span>
                                    <?php endif; // End if categories ?>
                                  
                                </div><!-- .screenshot_meta -->
                                
                                <a href="<?= $image_full[0] ?>" class="fancybox options">Expand Image</a>
                                <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" class="options">Full Post</a>
                            
                            </div><!-- .info_panel -->
                    
                    	<?php endforeach; ?>
                    
                    <?php endif; ?>	 
                

                    </article><!-- #post-<?php the_ID(); ?> -->
                  </li> 
				<?php endwhile; ?>
                </ul>
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

<?php get_footer(); ?>
