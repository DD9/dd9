<?php 

get_header(); the_post(); 

$projects = get_posts(array(
	'post_type'=>'project',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'post_date',
	'order' => 'DESC'
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
      	<div id="secondary">
          <div class="two_column">
            <h4 class="subheading_full_width"><span>Work</span></h4>
          </div><!-- .two_column -->
        </div><!-- #secondary -->
          
        <div class="content_right">
          <article class="post plain">    
            <div class="entry_content">   
              <?php the_content(); ?>
              <p>To the right is a list of the graphic and web design services that we provide. Visit a service page to get in depth information about our <a href="/service">design offerings</a>.</p>
            </div>                  
            
            <div id="services_container" class="project_index">
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
          </article>
        </div><!-- .content_right thin_border -->
      </div><!-- .block_container.full_width --> 
          
			
      
      <?php 
			// We'd like to group projects by year (based on the End Date custom field), most recent first.
			// For each year, load the year in the left column and the thumbmail grid of projects in the right content area.
			?>
      
      <?php // For each year ?>
      <div class="block_container full_width clearfix"> 
        <div class="two_column">
        	
          <?php // Get the year ?>
          <h4 class="subheading breadcrumbs alt_xl">2013</h4>
          
          <div class="block_content border_top"> </div>
        </div><!-- .two_column --> 
        
        <div class="content_right">   
          
          <?php // Get the projects ?>
          <?php if($projects): ?>
            
            <ul id="thumbnail_grid" class="clearfix">
							<?php foreach($projects as $project):
							
							$project_services = get_posts(array(
							'connected_type' => 'project_services',
							'post_type' => 'service',
							'suppress_filters' => false,
							'numberposts' => -1,
							'connected_from' => $project->ID,
							'connected_orderby' => '_order_to'
							));
	
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
							}
							
							$attributes = wp_get_post_terms($project->ID, 'attribute');
							?>          
              <li class="element all<?php foreach($project_services as $project_service){ echo " ". $project_service->post_name; }?>">
                <div class="preview_thumbnail client">
                  <?php if($images): ?>
                    <a href="<?= get_permalink($project->ID) ?>" title="<?= $project->post_title ?>">   
                      <img src="<?= $image_src ?>" width="234" height="162" alt="<?php $project->post_title; ?> Preview" />
                    </a>
                  <?php else: ?>
                    <a href="<?= get_permalink($project->ID) ?>" title="<?= $project->post_title ?>">
                      <img src="http://dd9.com/wp-content/uploads/feat_placeholder.jpg" alt="<?php $project->post_title; ?> Preview" width="234" height="162" />
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
  
        <footer class="entry-meta">
          <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
        </footer><!-- .entry-meta -->
              
      	</div><!-- .content_right -->   
			</div><!-- .block_container.full_width --> 
      <?php // End for each Year ?>
          
<?php get_footer(); ?>