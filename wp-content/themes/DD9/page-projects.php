<?php 

get_header(); the_post(); 

$all_projects = get_posts(array(
	'post_type'=>'project',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'meta_value',
	'order' => 'DESC',
  'meta_key' => 'start_work'
));

$projects_by_year = array();

foreach($all_projects as $project)
{
  $start_date = get_post_meta($project->ID, 'start_work', true);
  $year = substr($start_date, 0, 4);

  if(!array_key_exists($year, $projects_by_year))
  {
    $projects_by_year[$year] = array();
  }

  $projects_by_year[$year][] = $project;
}


// CUT HERE TO REMOVE NULL PROJECT HUNT CODE

$all_projects = get_posts(array(
  'post_type' => 'project',
  'suppress_filters' => false,
  'numberposts' => -1
));

$null_projects = array();

foreach($all_projects as $project) {
  $start_date = get_post_meta($project->ID, 'start_work', true);
  if($start_date == null)
  {
    $null_projects[] = $project;
  }
}

echo "<!-- ";
print_r($null_projects);
echo "-- >";

// CUT HERE TO REMOVE NULL PROJECT HUNT CODE


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

      <?php foreach($projects_by_year as $year => $projects): ?>
      <div class="block_container full_width clearfix">
        <div class="two_column">
          <h4 class="subheading breadcrumbs alt_xl"><?= $year ?></h4>

          <div class="block_content border_top"> </div>
        </div><!-- .two_column -->

        <div class="content_right">

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

        <footer class="entry-meta">
          <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
        </footer><!-- .entry-meta -->
              
      	</div><!-- .content_right -->   
			</div><!-- .block_container.full_width --> 
      <?php endforeach; ?>
          
<?php get_footer(); ?>