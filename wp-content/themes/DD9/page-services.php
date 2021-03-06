<?php 

get_header(); the_post(); 

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

      <div class="secondary">
        <h4 class="subheading_full_width"><span><?php the_title(); ?></span></h4>
          <div class="block_content">
            <h1><?php the_h1_override(); ?></h1>
            <p class="top_line"><em>Click on a service to learn more and view sample projects.</em></p>
            
            <?php the_content(); ?>
            <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
          </div>
      </div><!-- .secondary -->

      <?php include('primary_cta.php'); ?>
      
      <div id="services_container">

        <?php if($wd_services): ?>
        <ul class="services web_design clearfix">
          <li class="parent_service">
          
            <h3 class="subheading"><a href="<?= get_permalink($website_design->ID) ?>"><?= $website_design->post_title ?></a></h3>
            <p><?php echo get_the_post_thumbnail($website_design->ID, 'full'); ?><?= $website_design->post_excerpt ?></p>
          </li>
          <?php foreach($wd_services as $service): ?>
            <li class="child_service">
             <a href="<?= get_permalink($service->ID) ?>" title="<?= $service->post_title ?>">
            <?php echo get_the_post_thumbnail($service->ID, array(100,100)); ?>
             <?= $service->post_title ?> <!--<?= $service->post_excerpt ?><?php //print_r($service); ?>-->  </a>
            </li>
          <?php endforeach; ?>
        </ul><!-- .services -->	
        <?php else: ?>
            No services found.
        <?php endif; ?>
        
        <?php if($gd_services): ?>
        <ul class="services graphic_design clearfix">
          <li class="parent_service">
           
            <h3 class="subheading"><a href="<?= get_permalink($graphic_design->ID) ?>">
			
			<?= $graphic_design->post_title ?></a></h3>
           <p><?php echo get_the_post_thumbnail($graphic_design->ID, 'full'); ?><?= $graphic_design->post_excerpt ?></p>
          </li>
          <?php foreach($gd_services as $service): ?>
            <li class="child_service">
            <a href="<?= get_permalink($service->ID) ?>" title="<?= $service->post_title ?>">
            <?php echo get_the_post_thumbnail($service->ID, array(100,100)); ?>
              <?= $service->post_title ?></a>
            </li>
          <?php endforeach; ?>
        </ul><!-- .services -->	
        
        <?php else: ?>
            No services found.
        <?php endif; ?>
      </div><!-- services_container -->
    
    </div><!-- .block_container.full_width --> 
			
            

<?php get_footer(); ?>