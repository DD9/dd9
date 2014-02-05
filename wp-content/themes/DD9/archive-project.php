<?php

get_header(); the_post();

$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$posts_per_page = get_option('posts_per_page');
$offset = $posts_per_page * ($paged - 1);

if($paged > 1) {
  // if we're on the second page, we need to also load the post _before_ the first one, to
  // check to see if we should display the first year found
  $offset = $offset - 1;
  $posts_per_page = $posts_per_page + 1;
  echo "offset: $offset, posts_per_page: $posts_per_page";
}

$all_projects = get_posts(array(
	'post_type'=>'project',
	'suppress_filters' => false,
	'numberposts' => $posts_per_page,
  'offset' => $offset,
	'orderby' => 'meta_value',
	'order' => 'DESC',
  'meta_key' => 'start_work'
));

if($paged > 1) {
  $previous_project = $all_projects[0];
  $previous_project_date = get_post_meta($previous_project->ID, 'start_work', true);
  $previous_year = substr($previous_project_date, 0, 4);

  array_shift($all_projects);
}

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

// KEEP JUST IN CASE
// CUT HERE TO REMOVE NULL PROJECT HUNT CODE
/*
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
echo "-->";
*/
// CUT HERE TO REMOVE NULL PROJECT HUNT CODE


$graphic_design = get_post($gd_id);

$gd_services = get_posts(array(
	'post_type'=>'service',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'menu_order',
	'order' => 'ASC',
	'post_parent' => GRAPHIC_DESIGN
));

$website_design = get_post($wd_id);

$wd_services = get_posts(array(
	'post_type'=>'service',
	'suppress_filters' => false,
	'numberposts'=>-1,
	'orderby' => 'menu_order',
	'order' => 'ASC',
	'post_parent' => WEBSITE_DESIGN
));

?>

  <div class="block_container full_width clearfix">
    <div class="secondary fixed">
      
       <h4 class="subheading_full_width"><span>Work</span></h4>
       <div class="block_content">
          <p>
            The master list of DD9 graphic design, web design and web development projects.
            Select a service below to filter the list or go to our
            <a title="Services" href="/services/">services page</a> where you can
            <a title="Services" href="/services/">view projects by service category</a>.
          </p>
          <p>Below is a list of the graphic and web design services that we provide. Visit a service page to get in depth information about our <a href="/service">design offerings</a>.</p>
       </div><!-- .block_content -->
      
      
      <div class="two_column">
        <div class="block_content">
                          
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
    </div><!-- #secondary -->

    <div class="content_right">
    
      <ul id="infinite-scroll-container" class="clearfix">
      <?php foreach($projects_by_year as $year => $projects): ?>
        <?php if($year != $previous_year): ?>
          <li class="infinite-scroll-item year"><div class="year_inner"><span><?= $year ?></span></div></li>
        <?php endif ?>

        <?php foreach($projects as $project):

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
        <li class="infinite-scroll-item">
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
      <?php endforeach; ?>
    </ul>
    
    
    </div><!-- .content_right thin_border -->
  </div><!-- .block_container.full_width -->

  
  <div id="infinite-scroll-nav"><?php posts_nav_link() ?></span>

<?php get_footer(); ?>