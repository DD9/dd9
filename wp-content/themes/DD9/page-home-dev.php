<?php
/**
 * The template for the Landing Page
 */

get_header('home-dev'); 
$design_shots = get_posts(array(
	'post_type' => 'post',
	'numberposts' => 8,
	'category' => 3,
	'post_status' => 'publish',
	'order' => 'DESC',
	'orderby' => 'post_date'
));

$launch_posts = get_posts(array(
	'post_type' => 'post',
	'numberposts' => 4,
	'category__not_in' => array( 3 ),
	'post_status' => 'publish',
	'order' => 'DESC',
	'orderby' => 'post_date'
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

		<div id="primary" class="home full_width clearfix">

			<?php the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				  <header class="entry-header home left">
					<h2>DD9&trade; | DesignDivine Evolution</h2>
				  </header><!-- .entry-header -->
			  
				  <div class="entry-content">
                    <header class="entry-header home">
                    		<h1 class="entry-title"><?php the_h1_override(); ?></h1>
					</header><!-- .entry-header -->
					<?php the_content(); ?>
				  </div><!-- .entry-content -->
		    </article>
            <!-- #post-<?php the_ID(); ?> -->
		  	
		  
		  <!--div id="featured_work" class="home">
		  	<ul class="block left">
			  <li class="title subheading"><a href="/projects">Recent Work</a></li>
			  <li></li>
			  <li></li>
			  <li></li>
			</ul>
			
			<ul class="block right">
			  <li></li>
			</ul>
		  </div-->
		  
	      
		
		</div><!-- #primary -->
        
	   
<div class="block_container full_width clearfix home">

  <div class="two_column">
  	<h4 class="subheading_full_width"><a href="/services/">Services</a></h4>
	  <div class="block_content">
		<h3>Website &amp; Graphic Design Services</h3>
		<p class="top_line">Click on a service to learn more and view sample projects.</p>
      </div>
  </div><!-- .two_column -->
  
  <?php include('primary_cta.php'); ?>
  			   
	<?php // Services Hierarchy: regroup master services list into two lists, organized by 'Web Design' and 'Graphic Design' 
		  // Add a class called 'parent_service' the <li> of the parent service in each list (i.e. 'web design' and 'graphic design')
	?>
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

<?php if($launch_posts): ?>
<div class="block_container full_width clearfix">

  <div class="two_column">
  	<h4 class="subheading_full_width"><a href="/blog">Recent News...</a></h4>
	  <div class="block_content">
	    <h3> Latest Website Launches </h3>
	    <p class="top_line"><a target="_blank" class="viewmore" href="/category/news/site_launches/">view archives</a></p>
	  </div>
  </div>
    <div id="posts_container">
	  <ul id="launch_posts" class="clearfix">
		<?php foreach($launch_posts as $post):
		setup_postdata($post);
		$images = get_posts(array(
			'numberposts' => 1,
			'order'=> 'ASC',
			'orderby' => 'menu_order',
			'post_mime_type' => 'image',
			'post_parent' => $post->ID,
			'post_status' => null,
			'post_type' => 'attachment'
		)); 
		
		if($images)
		{
			$image_data = wp_get_attachment_image_src($images[0]->ID, 'thumbnail');
			$image_src = $image_data[0];
		}
		?>
		  <li>
            <div class="preview_thumbnail">
            <?php if($images): ?>
                <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">   
                    <img src="<?= $image_src ?>" width="234" height="162" alt="<?php the_title(); ?> Preview" />
                </a>
            <?php else: ?>
                <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <img src="http://dd9.com/wp-content/uploads/feat_placeholder.jpg" alt="<?php the_title(); ?> Preview" width="234" height="162" />
                </a>
            <?php endif; ?> 
            </div>
            
            <div class="launch_post_content">
                <h5><a href="<?php the_permalink() ?>" class="plain"><?php the_title(); ?></a></h5>
                <?= custom_excerpt(10); ?>
                <a class="more_link" href="<?php the_permalink() ?>">...read more</a>
                <span class="launch_post_time"><?php the_time('F jS, Y'); ?></span>
            </div> 
		  </li>
		 <?php endforeach; ?>
	  </ul>
    </div>
</div><!-- .block_container full_width --> 
<?php else: ?>
	No posts found.
<?php endif; ?>   
          


<?php if($design_shots): ?>
<div class="block_container full_width clearfix">
 <div class="two_column">
   <h4 class="subheading_full_width"><a href="/category/design_screenshots/">Design Stream</a></h4>
     <div class="block_content">
       <h3> Real-time Project Screenshots </h3>
	   <p class="top_line"><a class="viewmore" href="/category/design_screenshots/">view archives</a></p>
	 </div>
  </div>
    <div id="screenshots_container">
	  <ul id="design_stream">
	  <?php foreach($design_shots as $post): 
	  setup_postdata($post); 
	  $images = get_posts(array(
		  'post_type'=>'attachment',
		  'numberposts'=> 1,
		  'orderby'=>'menu_order',
		  'order'=>'ASC',
		  'post_parent'=>$post->ID,
		  'post_mime_type'=>'image'
	  ));
	  ?>
		<li>
		  <?php if($images): ?>
			<?php foreach($images as $image): ?>
			  <a href="<?php the_permalink() ?>">   
				<?php if($image->ID): $image_data = wp_get_attachment_image_src($image->ID, 'thumbnail'); ?>
					<img src="<?= $image_data[0] ?>" width="108" height="108" alt="Project Preview"  title="<?= $image->post_excerpt ?>" />
				<?php endif; ?>
			  
			  <span class="design_shot_time"><?php the_time('m/d/y g:i a'); ?></span>
			  </a>
			<?php endforeach; ?>
		  <?php else: ?>
		  <?php endif; ?> 
		</li>
	   <?php endforeach; ?>
	  </ul>
	</div><!-- screenshots_container -->

</div><!-- .block_container full_width -->
<?php else: ?>
	No design screenshots found.
<?php endif; ?>	

<?php get_footer(); ?>