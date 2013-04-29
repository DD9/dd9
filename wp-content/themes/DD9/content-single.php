<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
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

        <div class="secondary">
             
             <h4 class="subheading_full_width"><a href="/blog/" title="DD9 Blog Home">Blog</a></h4>
             <div class="block_content">
               
               <h1><?php the_title(); ?></h1>
                <?php
					/* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
					if ( $tags_list ): ?>
                    
					<p class="top_line tag-links">
                    
						<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</p>
					<?php endif; // End if $tags_list ?>    
               
              <p class="top_line"><em></em></p> 
                <?php include('social.php'); ?>
               
               <ul class="post_details">
                  <li id="author-info">
                      <div id="author-avatar">
                          <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 68 ) ); ?>
                      </div><!-- #author-avatar -->
                     
                  </li><!-- #entry-author-info -->
                  
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
               
             </div><!-- .block_content -->
          </div><!-- .secondary -->


	  <div class="content_right single">
        <article class="post">
          <div class="entry_content">
         
           
            <?php if($project): ?>
                <p><em><a href="<?= get_permalink($project->ID) ?>">View Project Case Study</a></em></p>
              
                <?php if($full_url = get_post_meta($project->ID, 'full_url', true)): ?>
              <p><em><a href="<?= $full_url ?>" class="external_link" target="_blank" title="<?php get_post_meta($project->ID, 'full_url', true); ?>">Launch the Site</a></em></p>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php the_content(); ?>
             
            <?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
          </div><!-- .entry-content -->
          
           <ul class="post_details">
                      
                    
                    <?php if ( get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries ?>
                    <li id="author-info">
                        <div id="author-avatar">
                            <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 68 ) ); ?>
                        </div><!-- #author-avatar -->
                       
                    </li><!-- #entry-author-info -->
                   
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
                      
                  
 
                  
                  
                 <?php endif; ?>
                 </ul><!-- .post_details -->
       
           <footer class="entry-meta">
             <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
           </footer><!-- .entry-meta -->
        </article>  
      </div><!-- #content_right -->