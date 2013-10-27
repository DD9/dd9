<?php 

get_header(); the_post(); 

$clients = get_posts(array(
    'connected_type' => 'project_clients',
	'post_type' => 'client',
	'suppress_filters' => false,
	'numberposts' => -1,
	'connected_from' => $post->ID
));

$project_services = get_posts(array(
    'connected_type' => 'project_services',
    'post_type' => 'service',
    'suppress_filters' => false,
    'numberposts' => -1,
    'connected_from' => $post->ID
));

$project_service_ids = array();
if($project_services)
{
    foreach($project_services as $project_service)
    {
        $project_service_ids[] = $project_service->ID;
    }
}

$all_related_projects = get_posts(array(
    'connected_type' => 'project_services',
    'post_type' => 'project',
    'connected_items' => $project_service_ids,
    'post__not_in' => array($post->ID),
    'suppress_filters' => false,
    'numberposts' => -1
));

$related_projects = array();

if($all_related_projects)
{
    foreach($all_related_projects as $related_project)
    {
        $related_projects[$related_project->ID] = $related_project;
    }
}

shuffle($related_projects);
$related_projects = array_slice($related_projects, 0, 3);

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

$posts = get_posts(array(
    'connected_type' => 'project_posts',
    'post_type' => 'post',
    'suppress_filters' => false,
    'numberposts' => -1,
    'connected_from' => $post->ID
));

$users = get_users(array(
    'connected_type' => 'project_users',
    'connected_items' => $post->ID
));

$users = get_data_for_user_array($users);

$groups = array('team', 'associates', 'alumni');

$attributes = wp_get_post_terms($post->ID, 'attribute');

$display_url = get_post_meta($post->ID, 'display_url', true);
$full_url = get_post_meta($post->ID, 'full_url', true);
$start_work = format_short_date(get_post_meta($post->ID, 'start_work', true));
$end_work = format_short_date(get_post_meta($post->ID, 'end_work', true));

?>

      <div class="block_container">    
        <div class="secondary">   
          <div class="two_column">     
             <h4 class="subheading_full_width"><a href="/projects/" title="DD9 Portfolio">Projects</a></h4>
             <div class="block_content">
                <h1> <?php the_title(); ?></h1>
                <p class="top_line"></p>
                <?php include('social.php'); ?>
             </div><!-- .block_content -->
          </div><!-- .two_column -->

          <div class="two_column clearfix">
            <div class="block_content">
              <p class="top_line"><em></em></p>
              <div id="services_container" class="sidebar">
                <?php if($wd_services): ?>
                <ul class="services web_design clearfix">
                  <li class="parent_service subheading <?php if(in_array($website_design->ID, $project_service_ids)) echo "active"; ?>">
                    <a href="<?= get_permalink($website_design->ID) ?>"><?= $website_design->post_title ?></a>
                  </li>
                  <?php foreach($wd_services as $service): ?>
                    <li<?php if(in_array($service->ID, $project_service_ids)) echo " class='active'"; ?>>
                      <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul><!-- .services --> 
                <?php else: ?>
                  No services found.
                <?php endif; ?>
                
                <?php if($wd_services): ?>
                <ul class="services graphic_design clearfix">
                  <li class="parent_service subheading <?php if(in_array($graphic_design->ID, $project_service_ids)) echo "active"; ?>">
                    <a href="<?= get_permalink($graphic_design->ID) ?>"><?= $graphic_design->post_title ?></a>
                  </li>
                  <?php foreach($gd_services as $service): ?>
                    <li<?php if(in_array($service->ID, $project_service_ids)) echo " class='active'"; ?>>
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
        </div> <!-- #secondary -->
            
        <div class="content_right"> 
          
          <?php $images = get_posts(array(
                'exclude'=>'$featured_image_id',
                'post_type'=>'attachment',
                'numberposts'=>-1,
                'orderby'=>'menu_order',
                'order'=>'asc',
                'post_parent'=>$post->ID,
                'post_mime_type'=>'image'
            ));
            
          $featured_image_id = get_post_thumbnail_id();
          $count = count( $images );
          $specific = array();
          $i = 1;     
          ?>
          
          <?php if($images): ?>
            
            <div id="image_data">
              <div class="total_images"> &nbsp; 1 &nbsp;<?php  $specific[$image->ID] = $i; ++$i; ?> of <?php echo $count; ?></div>
            </div><!-- #images_data -->
            
            <div class="flexslider project">        
              <ul id="image_attachments_full" class="slides">
                <?php foreach($images as $image): ?>
                  <?php if($class = get_image_class($image->ID)): ?>
                  <li  style="display:none">
                    <?php $image_data = wp_get_attachment_image_src($image->ID, 'thumbnail'); ?>
                    <?php $full_image_data = wp_get_attachment_image_src($image->ID, 'full'); ?>
                    <?php /*?><a href="<?= $full_image_data[0] ?>">
                        <img src="<?= $image_data[0] ?>" alt="Project Image"  title="<?= $image->post_excerpt ?>" class="<?= $class ?>" />
                    </a><?php */?>
                      <img src="<?= $full_image_data[0] ?>" alt="<?= $image->post_excerpt ?>" class="<?= $class ?>" />

                  <?php else: ?>
                  <li style="display:none">
                    <?php $image_data = wp_get_attachment_image_src($image->ID, 'large'); ?>
                    <img src="<?= $image_data[0] ?>" width="864" alt="<?= $image->post_excerpt ?>" />
                  <?php endif; ?>
                  <div class="caption"><?php
                   if($image->post_excerpt) {
                     echo $image->post_excerpt;
                     } else {
                    the_title(); 
                    $img_count =  $image->ID;
                    echo ' project image #' . $img_count; 
                    } ?></div>
                    </li>
                <?php endforeach; ?>
              </ul><!-- #image_attachments_full -->
            </div><!-- .flexslider -->
            <?php endif; ?> 
         
        </div><!-- .content_right -->
        <div class="clearfloat"></div>
      </div><!-- .block_container.full_width -->
        
      <div class="block_container"> 
        <div class="secondary">  
          <?php if($related_projects): ?> 
          <div class="two_column">         
            <div class="block_content border_top">
              <h4 class="black">Related Projects</h4> 
              <ul class="related_projects">
                <?php foreach($related_projects as $related_project): ?>  
                  <?php
                  $images = get_posts(array(
                  'numberposts' => 1,
                  'order'=> 'ASC',
                  'orderby' => 'menu_order',
                  'post_mime_type' => 'image',
                  'post_parent' => $related_project->ID,
                  'post_status' => null,
                  'post_type' => 'attachment'
                  ));
                  
                  if($images)
                  {
                    $image_data = wp_get_attachment_image_src($images[0]->ID, 'thumbnail');
                    $image_src = $image_data[0];
                  }
                  else $image_src = 'http://dd9.com/wp-content/uploads/feat_placeholder.jpg';
                  ?>
                  
                  <li>
                    <a href="<?= get_permalink($related_project->ID) ?>">
                      <img src="<?= $image_src ?>" alt="<?= $related_project->post_title ?>" />
                      <span class="thumbnail_title">
                        <?= $related_project->post_title ?>
                      </span>
                    </a>
                  </li>
                <?php endforeach; ?> 
              </ul>
            </div>
          </div><!-- .two_column -->  
          <?php endif; ?>

          <?php if($posts): ?>        
          <div class="two_column">
            <div class="block_content">
              <p class="top_line"></p>
              <h4 class="black">Related Posts</h4> 
              <ul class="related_posts">
                <?php foreach($posts as $project_post): ?>
                <li>
                  <a href="<?= get_permalink($project_post->ID) ?>">
                      <?= $project_post->post_title ?>
                  </a>
                      
                  <span class="post_time"><?= get_the_time('F jS, Y', $project_post->ID) ?></span>
                </li>
                <?php endforeach; ?> 
              </ul>
            </div><!-- .block_content -->
		      </div><!-- .two_column -->
          <?php endif; ?>
        </div> <!-- .secondary -->
                             
        <div class="content_right thin_border"> 
          <article class="post">  
          <div class="entry_content">   
            <h3>About the Project</h3>
            <?php the_content(); ?>
          </div> 

          <!--div class="featured_image"> 
              <?php if ( has_post_thumbnail()) {echo get_the_post_thumbnail($id, 'thumbnail'); }?>
          </div-->

          <ul class="post_details">
            <?php if($clients): ?><?php foreach($clients as $client): ?>
              <li><h4 class="title">Client:</h4> <a href="<?= get_permalink($client->ID) ?>"><?= $client->post_title ?></a></li>    
              <?php endforeach; ?><?php endif; ?> 
             
              <?php if($full_url): ?><li><h4 class="title">Launch Project:</h4> <a href="<?= $full_url ?>" class="external_link" target="_blank"><?= $display_url; ?></a></li><?php endif; ?>
              
              <?php if($start_work): ?>
              <li>
                  
                <h4 class="title">Date:</h4>          
                <?= $start_work; ?>  
              
              </li>
              <?php endif; ?>                
              
              <?php if($users): ?>
                <li>
                  <ul class="users">
                    <li><h4 class="title">Contributors:</h4></li>
                    <?php $first = true; ?>
                    <?php foreach($groups as $group): ?>
                      <?php foreach($users[$group] as $user): ?>
                    
                        <?php //  Order list of users below by same order that you set up in page-team.php
                              //  Team users should be listed first, followed by associates, followed by alumni ?>
                           
                         <li>
                           <?php if(!$first) echo ", "; ?>
                           <a href="<?= $user['posts_url'] ?>" class="<?= $group ?>"><?= $user['name'] ?></a>
                         </li>
                      <?php $first = false; endforeach; ?>                       
                    <?php endforeach; ?>
                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($attributes): ?>
                <li>
                  <ul class="attributes">
                    <li><h4 class="title">Tags:</h4></li>
                    <?php foreach($attributes as $attribute): ?>
                      <li>
                       <a href="<?= get_term_link($attribute) ?>"> <?= $attribute->name ?> </a>
                       <?php if($attribute != $attributes[count($attributes) - 1]) echo ","; ?> 
                      </li>
                    <?php endforeach; ?>
                  </ul>
                 </li>
              <?php endif; ?>
             
          </ul><!-- .post_details -->

          </article>
          
          <footer class="entry-meta">
            <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
          </footer><!-- .entry-meta -->
        </div><!-- .content_right -->
      </div><!-- .block_container.full_width -->

   

<?php get_footer(); ?>