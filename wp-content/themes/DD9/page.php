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
                  <?php //returns the root parent page 
					function get_top_ancestor($id){
						$current = get_post($id);
						
						if( !$current->post_parent ) {
							return $current->post_title; 
						} 
						else {
							return get_top_ancestor($current->post_parent);
						}
					} ?>
                    
				  <?php $top_ancestor =  get_top_ancestor($post->ID); ?> 
				  
				  <?php if(!$post->post_parent): 
				  $children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$post->ID."&echo=0");
				  ?>
                        
                   
                    <!--<h4 class="subheading blue_arrow breadcrumbs"><a href="/about/">/about/</a></h4>-->
                      <h4 class="subheading_full_width"><span><?php the_title(); ?></span></h4>
                        <div class="block_content">
                          <ul class="arrows sidebar_nav"><?php echo $children; ?></ul> 
                        </div><!-- .block_content -->
                   
                   
                   <?php elseif($post->ancestors):
				    $ancestors = end($post->ancestors);
                    $children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$ancestors."&echo=0");
				   ?>
                        
                       <h4 class="subheading_full_width"><a href="<?php echo esc_url( get_permalink( get_page_by_title( $top_ancestor ) ) ); ?>"><?php echo $top_ancestor; ?></a></h4>
                         <div class="block_content">
                           <h1><?php the_h1_override(); ?></h1>
                           <ul class="arrows sidebar_nav"><?php echo $children; ?></ul>
                         </div><!-- .block_content -->
                   
               
				
				   <?php else: ?>
                   <?php endif; ?>
                 
                 </div><!-- .secondary -->
                 
                 
                 
                
                         
                
                <div class="content_right"> 

				<?php the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				

			 </div><!-- .content_right -->
          </div><!-- .block_container -->
		

<?php get_footer(); ?>