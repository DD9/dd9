<?php
/**
 * The default template for displaying content
 */

$images = get_posts(array(
	'numberposts' => 1,
	'order'=> 'ASC',
	'orderby' => 'menu_order',
	'post_mime_type' => 'image',
	'post_parent' => $post->ID,
	'post_status' => null,
	'post_type' => 'attachment'
)); 

if($images)
{
	$image_data = wp_get_attachment_image_src($images[0]->ID, 'thumbnail');
	$image_src = $image_data[0];
}

$projects = get_posts(array(
    'connected_type' => 'project_posts',
	'post_type' => 'project',
	'suppress_filters' => false,
	'numberposts' => -1,
	'connected_to' => $post->ID,
));

$project = $projects ? $projects[0] : null;

?>

	<article id="post-<?php the_ID(); ?>" class="post">
    
    
          <div class="entry_content">
            <header class="entry-header">
                <?php if ( is_sticky() ) : ?>
                    <hgroup>
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                        <h3 class="entry-format"><?php _e( 'Featured', 'twentyeleven' ); ?></h3>
                    </hgroup>
                <?php else : ?>
                <h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
                <?php endif; ?>
    
                <?php /*?><!--<?php if ( comments_open() && ! post_password_required() ) : ?>
                <div class="comments-link">
                    <?php comments_popup_link( '<span class="leave-reply">' . __( 'Reply', 'twentyeleven' ) . '</span>', _x( '1', 'comments number', 'twentyeleven' ), _x( '%', 'comments number', 'twentyeleven' ) ); ?>
                </div>
                <?php endif; ?>--><?php */?>
            </header><!-- .entry-header -->
          
            <?php the_excerpt( __( 'read more <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?>
            

            <?php if($project): ?>
              <ul class="news_project_links clearfix">
              	<li><a href="<?= get_permalink($project->ID) ?>"><i class="icon-beaker"></i> View Case Study</a></li>
              
                <?php if($full_url = get_post_meta($project->ID, 'full_url', true)): ?>
              	<li><span class="sep"> |</span> <a href="<?= $full_url ?>" target="_blank" title="<?php get_post_meta($project->ID, 'full_url', true); ?>"><i class="icon-external-link"></i> Launch the Site</a></li>
                <?php endif; ?>
              </ul>
            <?php endif; ?>
          
          </div><!-- .entry-content -->
		
        
        
          <div class="featured_image">
            <?php if($images): ?>
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">   
                <img src="<?= $image_src ?>" width="234" height="162" alt="<?php the_title(); ?>" />
              </a>
            <?php else: ?>
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <img src="<?php bloginfo('template_url'); ?>/images/dd9-placeholder.jpg" alt="<?php the_title(); ?>" width="234" height="162" />
              </a>
            <?php endif; ?>                                
        </div>
        
          
          <ul class="post_details">

            <li class="post_author"><h6 class="title">Author:</h6> <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php the_author_meta('display_name'); ?></a></li>
            <li class="post_time"><h6 class="title">Posted On:</h6><?php the_time('F jS, Y'); ?></li> 
            
			<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
			if ( $categories_list ):
			?>
			<li class="cat-links">
			<h6 class="title">Categories:</h6>
				<?php printf( __( ' %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
				$show_sep = true; ?>
			</li>
			<?php endif; // End if categories ?>
              
          </ul><!-- .post_details -->
        

		<!--<footer class="entry-meta">
			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
				if ( $tags_list ):
				if ( $show_sep ) : ?>
			<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
			<span class="tag-links">
				<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
				$show_sep = true; ?>
			</span>
			<?php endif; // End if $tags_list ?>

			<?php if ( comments_open() ) : ?>
			<?php if ( $show_sep ) : ?>
			<span class="sep"> | </span>
			<?php endif; // End if $show_sep ?>
			<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twentyeleven' ) . '</span>', __( '<b>1</b> Reply', 'twentyeleven' ), __( '<b>%</b> Replies', 'twentyeleven' ) ); ?></span>
			<?php endif; // End if comments_open() ?>

			<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- #entry-meta -->
	</article><!-- #post-<?php the_ID(); ?> -->
