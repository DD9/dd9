<?php 

get_header(); the_post(); 

$projects = get_posts(array(
    'connected_type' => 'project_services',
	'post_type' => 'project',
	'suppress_filters' => false,
	'numberposts' => -1,
	'connected_to' => $post->ID,
	'connected_orderby' => '_order_to'
));

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

/* 
 *
 * If the above query seems awkward, fragile, or cumbersome, there is another way:
 * https://github.com/scribu/wp-posts-to-posts/wiki/Basic-usage
 *
 * Or: 
 * $projects_query = p2p_type('project_services')->get_connected(get_queried_object_id());
 *
 * In both of these alternate approaches (which are really the same approach), we're 
 * essentially using query_posts() instead of get_posts(). This can sometimes be more memory
 * efficient. It's also a little bit more dangerous, because we need to remember to call
 * wp_reset_postdata() when we're done rendering projects, otherwise the WP loop will be
 * very, very confused.
 *
 * If we go with oen of these alternate approaches, however, we can use this:
 * https://github.com/scribu/wp-posts-to-posts/wiki/Using-p2p_list_posts%28%29
 *
 * For example:
 * p2p_list_posts($projects_query, array('before_list' => '', 'after_list' => ''));
 *
 * This helper method is equivalent to:
 * while($projects_query->have_posts())
 * {
 *     $projects_query->the_post();
 *     echo "<li><a href='" . the_permalink() . "'>" . the_title() . "</a></li>";
 * }
 * wp_reset_postdata();
 *
 * You can also exclude the before_list and after_list parameters to have p2p_list_posts
 * default to rendering <ul></ul> tags around the list. 
 *
 * Long story short: If you want complete control, use the get_posts query above. If you just
 * want to render the projects in a list, you're probably better off using p2p_type,
 * get_connected, and p2p_list_posts.
 *
 * For more information on how I constructed the get_posts query above, see:
 * https://github.com/scribu/wp-posts-to-posts/wiki/Query-vars
*/

?>

		<div class="block_container breadcrumbs clearfix">    
             
          <div class="two_column">
          <h4 class="subheading_full_width"><a href="/services/" title="DD9 Serivce Directory">Services</a></h4>     
             <!--h4 class="subheading breadcrumbs"><a href="/services/" title="DD9 Serivce Directory">/services/</a></h4-->
             <div class="block_content">
               
               <h1 class="secondary"><?php the_h1_override(); ?></h1>
               <p class="top_line"><em></em></p>
               
               <ul id="social_links">
                 <li>
                   <a class="facebook" rel="nofollow" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>&amp;t=<?php echo urlencode(get_the_title($id)); ?>" title="Share this post on Facebook" target="_blank">
                     Facebook
                   </a>
                 </li>
                 <li>
                   <a class="twitter" rel="nofollow" href="http://twitter.com/home?status=<?php echo urlencode("Currently reading: "); ?><?php the_permalink(); ?>" title="Share this article with your Twitter followers" target="_blank">
                     Twitter
                   </a>
                 </li>
                 <li>
                   <a class="email" href="mailto:type email address here?subject=I wanted to share this post with you from <?php bloginfo('name'); ?>&body=<?php the_title('','',true); ?>&#32;&#32;<?php the_permalink(); ?>" title="Email to a friend/colleague" target="_blank">
                     Email
                   </a>
                 </li>
               </ul>
               
             </div><!-- .block_content -->
          </div><!-- .two_column -->
             
          <div class="content_right thin_border"> 
            <article class="post">
              <div class="entry_content">   
               <?php the_content(); ?>
              </div>
              
               <?php include('primary_cta.php'); ?>
              
            </article> 
            
            <div class="featured_image service">
				<?php 
                 if ( has_post_thumbnail()) {
                 echo get_the_post_thumbnail($id, 'full'); 
                }
                ?>
            </div>
            
                
         </div><!-- .content_right -->
             
        </div><!-- .block_container.full_width --> 	        
        
        
        <div class="block_container breadcrumbs clearfix extra_bottom">    
             
          <div class="two_column services">
            <h4 class="subheading breadcrumbs">Agency Services</a></h4>     
             
             <div class="block_content border_top">
                            
               <div id="services_container" class="sidebar">

				<?php if($wd_services): ?>
                <ul class="services web_design clearfix">
                  <li class="parent_service subheading<?= $website_design->ID == $post->ID ? " active" : '' ?>">
                    <a href="<?= get_permalink($website_design->ID) ?>"><?= $website_design->post_title ?></a>
                  </li>
                  <?php foreach($wd_services as $service): ?>
                    <li<?= $service->ID == $post->ID ? " class='active'" : '' ?>>
                      <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul><!-- .services -->	
                <?php else: ?>
                    No services found.
                <?php endif; ?>
                
                <?php if($wd_services): ?>
                <ul class="services graphic_design clearfix">
                  <li class="parent_service subheading<?= $graphic_design->ID == $post->ID ? " active" : '' ?>">
                    <a href="<?= get_permalink($graphic_design->ID) ?>"><?= $graphic_design->post_title ?></a>
                  </li>
                  <?php foreach($gd_services as $service): ?>
                    <li<?= $service->ID == $post->ID ? " class='active'" : '' ?>>
                      <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul><!-- .services -->	
                
                <?php else: ?>
                    No services found.
                <?php endif; ?>
              </div><!-- services_container -->
               
             </div><!-- .block_content -->
          </div><!-- .two_column -->
             
          <div class="content_right thin_border"> 
            
              <h4 class="subheading breadcrumbs">Projects <span>for <?php the_title(); ?></span> </h4>
    
              <?php if($projects): ?>
              
              <ul id="thumbnail_grid" class="clearfix">
                  <?php foreach($projects as $project): ?>
                      <?php 
                          $images = get_posts(array(
                              'numberposts' => 1,
                              'order'=> 'ASC',
                              'orderby' => 'menu_order',
                              'post_mime_type' => 'image',
                              'post_parent' => $project->ID,
                              'post_status' => null,
                              'post_type' => 'attachment'
                          )); 
                          
                          if($images)
                          {
                              $image_data = wp_get_attachment_image_src($images[0]->ID, 'thumbnail');
                              $image_src = $image_data[0];
							  $image_metadata = wp_get_attachment_image($images[0]->ID);
                          }
                          
                          $attributes = wp_get_post_terms($project->ID, 'attribute');
                      ?>           
                    <li>
                      <div class="preview_thumbnail">
                          <?php if($images): ?>
                              <a href="<?= get_permalink($project->ID) ?>" title="<?php echo $project->post_title; ?> <?php the_title(); ?>">   
                                  <!--img src="<?= $image_src ?>" width="234" height="162" alt="Project Preview"  title="<?= $project->post_title ?>" /--><?php echo $image_metadata; ?> 
                              </a>
                          <?php else: ?>
                              <a href="<?= get_permalink($project->ID) ?>">
                                  <img src="http://dd9.com/wp-content/uploads/feat_placeholder.jpg" alt="<?php $project->post_title; ?>" width="234" height="162" />
                              </a>
                          <?php endif; ?>                                
                      </div>
                      
                      <a href="<?= get_permalink($project->ID) ?>" class="info_panel">
                          <span class="thumbnail_title"><?= $project->post_title ?></span>
  
                          <?php if($attributes): $i = 0; ?>                                  
                              <ul class="thumbnail_tags">                                        
                                  <?php foreach($attributes as $attribute): if($i == 6) break; ?>
                                      <li>
                                          <?= $attribute->name ?><?php if($i != count($attributes) - 1) echo "," ?> 
                                      </li>
                                  <?php $i++; endforeach; ?>
                              </ul><!-- .project_tags -->
                          <?php endif; ?>
                      </a><!-- #info_panel -->
                      
                    </li>
  
                  <?php endforeach; ?>
    
                                      
              </ul><!-- #thumbnail_grid -->	   
              <?php else: ?>
               No projects found.
              <?php endif; ?>
            
            
          </div><!-- .content_right -->
             
        </div><!-- .block_container.full_width --> 	    	 


<div class="block_container full_width clearfix">
 <div class="two_column">
   <h4 class="subheading_full_width"><span>Learn More</span></h4>
     <div class="block_content">
     <h3><?php the_title(); ?> Techniques</h3>
     
	   <p class="top_line">&nbsp;</p>
	 </div>
  </div>
    <div class="content_right">
         <article class="post">
         <?php the_field('support_content'); ?>
            
        </article>


	</div><!-- content_right -->

</div><!-- .block_container full_width -->

                
        <footer class="entry-meta">
            <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
        </footer><!-- .entry-meta -->
        
		
<?php get_footer(); ?>