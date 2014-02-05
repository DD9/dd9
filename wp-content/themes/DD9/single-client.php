<?php 

get_header(); the_post(); 

$projects = get_posts(array(
    'connected_type' => 'project_clients',
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

$users = array();
$client_services = array();
if($projects)
{
    foreach($projects as $project)
    {
        $project_users = get_users(array(
            'connected_type' => 'project_users',
            'connected_items' => $project->ID
        ));
        
        if($project_users)
        {
            foreach($project_users as $project_user)
            {
                $users[$project_user->ID] = $project_user;
            }
        }
        
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
                $client_services[] = $project_service->ID;
            }
        }
    }
}
$users = get_data_for_user_array($users);
$groups = array('team', 'associates', 'alumni');

$client_services = array_unique($client_services);

$industries = wp_get_object_terms($post->ID, 'industry');

$location = get_post_meta($post->ID, 'location', true);
$display_url = get_post_meta($post->ID, 'display_url', true);
$full_url = get_post_meta($post->ID, 'full_url', true);
$start_work = format_short_date(get_post_meta($post->ID, 'start_work', true));
$end_work = format_short_date(get_post_meta($post->ID, 'end_work', true));

?>
       
    <div class="block_container breadcrumbs clearfix">    
          
           
          <div class="secondary">     
             <h4 class="subheading_full_width"><a href="/clients/" title="DD9 Clients">Clients</a></h4>
             <div class="block_content">
               
               <h1> <?php the_title(); ?></h1>
               <p class="top_line"><em></em></p>
               
             </div><!-- .block_content -->
             
             <div class="block_content">
                            
               <div id="services_container" class="sidebar">

								<?php if($wd_services): ?>
                <ul class="services web_design clearfix">
                  <li class="parent_service subheading <?php if(in_array($service->ID, $client_services)) echo "active";?>">
                    <a href="<?= get_permalink($website_design->ID) ?>"><?= $website_design->post_title ?></a>
                  </li>
                  <?php foreach($wd_services as $service): ?>
                    <li<?php if(in_array($service->ID, $client_services)) echo " class='active'";?>>
                      <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul><!-- .services -->	
                <?php else: ?>
                    No services found.
                <?php endif; ?>
                
                <?php if($wd_services): ?>
                <ul class="services graphic_design clearfix">
                  <li class="parent_service subheading <?php if(in_array($service->ID, $client_services)) echo "active";?>">
                    <a href="<?= get_permalink($graphic_design->ID) ?>"><?= $graphic_design->post_title ?></a>
                  </li>
                  <?php foreach($gd_services as $service): ?>
                    <li<?php if(in_array($service->ID, $client_services)) echo " class='active'";?>>
                      <a href="<?= get_permalink($service->ID) ?>"><?= $service->post_title ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul><!-- .services -->	
                
                <?php else: ?>
                    No services found.
                <?php endif; ?>
              </div><!-- services_container -->
               
             </div><!-- .block_content -->
          </div><!-- .secondary -->
            
              
          <div class="content_right thin_border"> 
            <article class="post">  
              <div class="entry_content">   
               <?php the_content(); ?>
              </div> 
              
              <div class="featured_image"> 
                
                  <?php 
                   if ( has_post_thumbnail()) {
                   echo get_the_post_thumbnail($id, 'thumbnail'); 
                  }
                  ?>
               
              </div>
              
              <ul class="post_details">
                 
                    <?php if($location): ?><li><h4 class="title">Location:</h4> <?= $location; ?></li><?php endif; ?>
                    <?php if($full_url): ?><li><h4 class="title">Online:</h4> <a href="<?= $full_url ?>" class="external_link" target="_blank"><?php echo $display_url; ?></a></li><?php endif; ?>
                    
                    
                    <?php if($start_work): ?>
                    <li>
                        
                      <h4 class="title">Date:</h4>          
                      <?= $start_work; ?> 
                    
                    </li>
                    <?php endif; ?>                
        
                    <li>
                      
                      <?php if($users): ?>
                        <ul class="users">
                          <li><h4 class="title">Contributors:</h4></li>
                          <?php foreach($groups as $group): ?>
                            <?php foreach($users[$group] as $user): ?>
                          
                              <?php //  Order list of users below by same order that you set up in page-team.php
                                    //  Team users should be listed first, followed by associates, followed by alumni ?>
                                 
                               <li>
                                 <a href="<?= $user['posts_url'] ?>" class="<?= $group ?>">
                                   <?= $user['name'] ?>
                                 </a>
                               </li>
                            <?php endforeach; ?>                       
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    
                    </li>
                    
                    <li>
                        
                      <?php if($industries): ?>
                      <ul class="industries">
                        <li><h4 class="title">Industry:</h4></li>
                        <?php foreach($industries as $industry): ?>
                          <li>
                          
                            <a href="<?= get_term_link($industry); ?>"><?= $industry->name ?></a>
                            
                          </li>
                        <?php endforeach; ?>
                      </ul>
                      <?php endif; ?>
                        
                    </li>
                  
                  </ul><!-- .post_details -->
              
             </article>
           </div><!-- .content_right -->
           
           <div class="content_right thin_border"> 
        
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
                }
                
                $attributes = wp_get_post_terms($project->ID, 'attribute');
                ?>          
                <li>
                  <div class="preview_thumbnail client">
                    <?php if($images): ?>
                      <a href="<?= get_permalink($project->ID) ?>">   
                        <img src="<?= $image_src ?>" width="234" height="162" alt="Project Preview"  title="<?= $project->post_title ?>" />
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
          
          <footer class="entry-meta">
            <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
          </footer><!-- .entry-meta -->
 
         </div><!-- .content_right -->
        </div><!-- .block_container.full_width -->
     
 
<?php get_footer(); ?>