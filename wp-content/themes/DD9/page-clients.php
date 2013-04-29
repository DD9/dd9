<?php 

get_header(); the_post(); 

$clients = get_posts(array(
	'post_type'=>'client',
	'suppress_filters' => false,
	'numberposts'=>-1,	
	'orderby' => 'title',
	'order' => 'ASC'
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

?>

	      <div class="block_container full_width clearfix">
            <div class="secondary">
              <div class="two_column">
                <h4 class="subheading_full_width"><span><?php the_title(); ?></span></h4>
                  <div class="block_content">
                    
                    <p class="top_line"></p>
                    
                    <?php the_content(); ?>
                  </div>
              </div><!-- .two_column -->   
                  
              <div class="two_column">   
                <h4 class="subheading breadcrumbs">Services</a></h4>     
                 <div class="block_content border_top">
                   <div id="services_container" class="sidebar">
    
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
                 </div><!-- .block_content -->
              
              </div><!-- .two_column -->
            
            </div><!-- #secondary -->
             
                
              <div class="content_right thin_border">   
                
                <?php if($clients): ?>
                <ul id="thumbnail_grid" class="clearfix">
                	<?php foreach($clients as $client): 
                        $images = get_posts(array(
                            'numberposts' => 1,
                            'order'=> 'ASC',
                            'orderby' => 'menu_order',
                            'post_mime_type' => 'image',
                            'post_parent' => $client->ID,
                            'post_status' => null,
                            'post_type' => 'attachment'
                        )); 
                            
                        if($images)
                        {
                            $image_data = wp_get_attachment_image_src($images[0]->ID, 'thumbnail');
                            $image_src = $image_data[0];
                        } 
                        else $image_src = '/wp-content/uploads/feat_placeholder.jpg';
                        
                        $projects = get_posts(array(
                            'connected_type' => 'project_clients',
                        	'post_type' => 'project',
                        	'suppress_filters' => false,
                        	'numberposts' => -1,
                        	'connected_to' => $client->ID,
                        	'connected_orderby' => '_order_to'	
                        ));
                        
                        $client_services = array();
                        if($projects)
                        {
                            foreach($projects as $project)
                            {
                                $project_services = get_posts(array(
                                        'connected_type' => 'project_services',
                                    	'post_type' => 'service',
                                    	'suppress_filters' => false,
                                    	'numberposts' => -1,
                                    	'connected_from' => $project->ID
                                ));
                                
                                if($project_services)
                                {
                                    foreach($project_services as $project_service)
                                    {
                                        $client_services[$project_service->ID] = $project_service;
                                    }
                                }
                            }
                        }
                        
                        $client_services = array_slice($client_services, 0, 6);
                	?>
                        <li>
                        	<div class="preview_thumbnail client">
								<a href="<?= get_permalink($client->ID) ?>">   
                                    <img src="<?= $image_src ?>" alt="Client Preview"  title="<?= $client->post_title ?>" />
                                </a>
                            </div>
                            
                            <a href="<?= get_permalink($client->ID) ?>" class="info_panel">
                                <span class="thumbnail_title"><?= $client->post_title ?></span>
                                
                                <?php if($client_services): $i = 0; ?>
                                    <ul class="thumbnail_tags">
                                        <?php foreach($client_services as $client_service): if($i == 6) break; ?>
                                            <li>
                                                <?= $client_service->post_title ?><?php if($client_service != $client_services[count($client_services) - 1]) echo ","; ?>
                                            </li>
                                        <?php $i++; endforeach; ?>
                                      </ul><!-- .project_tags -->
                                <?php endif; ?>
                            </a><!-- #info_panel -->
                            
                        </li>
              		
					
					<?php endforeach; ?>
                   
                </ul><!-- #thumbnail_grid.client_projects -->	
				<?php else: ?>
                    No clients found.
                <?php endif; ?>

              <footer class="entry-meta">
                  <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
              </footer><!-- .entry-meta -->
                
			  </div><!-- .content_right -->   
			</div><!-- .block_container.full_width --> 

<?php get_footer(); ?>