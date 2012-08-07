<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 

$graphic_design = get_post($gd_id);

$gd_services = get_posts(array(
	'post_type'=>'service',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'title',
	'order' => 'ASC',
	'post_parent' => GRAPHIC_DESIGN
));

$website_design = get_post($wd_id);

$wd_services = get_posts(array(
	'post_type'=>'service',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'title',
	'order' => 'ASC',
	'post_parent' => WEBSITE_DESIGN
));

?>

	<div class="block_container full_width clearfix">
        		
            <div class="two_column">
               <div class="block_content border_top">
                 <h1 class="secondary">Page Not Found</h1>
                 
                 <?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 5 ), array( 'widget_id' => '404' ) ); ?>
                 
               </div><!-- .block_content -->
                         
            </div><!-- .two_column -->
            
            
            <div class="content_right thin_border">  
                <article id="post-0" class="post error404 not-found">
                    
    
                    <div class="entry-content">
                        <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'twentyeleven' ); ?></p>
    
                        <?php get_search_form(); ?>
                
                    </div><!-- .entry-content -->
                </article><!-- #post-0 -->
			</div><!-- .content_right --> 
		
     </div><!-- .block_container.full_width -->    
        
     <div class="block_container full_width clearfix">

        <div class="two_column">
            <h4 class="subheading_full_width"><span>Services</span></h4>
            <div class="block_content">
              <h3>Web Design and Graphic Design Services</h3>
              <p class="top_line"><em>Click on a service to learn more and view sample projects.</em></p>
              
            </div>
        </div><!-- .two_column -->
  
        
        <div id="services_container">
  
          <?php if($wd_services): ?>
          <ul class="services web_design clearfix">
            <li class="parent_service subheading">
              <a href="<?= get_permalink($website_design->ID) ?>"><?= $website_design->post_title ?></a>
            </li>
            <?php foreach($wd_services as $service): ?>
              <li>
                <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
              </li>
            <?php endforeach; ?>
          </ul><!-- .services -->	
          <?php else: ?>
              No services found.
          <?php endif; ?>
          
          <?php if($wd_services): ?>
          <ul class="services graphic_design clearfix">
            <li class="parent_service subheading">
              <a href="<?= get_permalink($graphic_design->ID) ?>"><?= $graphic_design->post_title ?></a>
            </li>
            <?php foreach($gd_services as $service): ?>
              <li>
                <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
              </li>
            <?php endforeach; ?>
          </ul><!-- .services -->	
          
          <?php else: ?>
              No services found.
          <?php endif; ?>
        </div><!-- services_container -->
    
    </div><!-- .block_container.full_width --> 

<?php get_footer(); ?>