<?php
get_header(); the_post();  

$raw = get_users();
$users = get_data_for_user_array($raw);

$about_children = array(
	'depth'        => 0,
	'child_of'     => 3470,
	'title_li'     => '',
	'sort_column'  => 'menu_order'
); 

?>

		
     <div class="block_container full_width clearfix">
        
        <div class="two_column">
          <h4 class="subheading_full_width"><span>About</span></h4>
            <div class="block_content">
              <?php the_content(); ?>
              
              <ul class="arrows sidebar_nav"><?php wp_list_pages( $about_children ); ?></ul> 
            </div>
        </div><!-- .two_column -->
                  
                 
        
        <div class="content_right">
        	<ul id="preview_content"> 
                <li class="preview_content_block wide">
                    <div class="block_link subheading"><a href="/about/team/">The Team</a></div>
                	
                    <ul id="preview_team">
                    	<li>
                        	<h3 class="arrow_red_large">Meet <br />your Team</h3>
                        	<a class="more_link" href="/about/team/">...view all</a>
                        </li>
						<?php foreach($users['team'] as $user): ?>
                
                        
                            <li class="user_gravatar">                        
                                <a href="<?= $user['posts_url'] ?>">
                                    <?= get_avatar($user['id'], '108', null, $name) ?>                    
                                </a>
                            </li><!-- .user_gravatar -->              
                   
                  
                        <?php endforeach; ?>
                    </ul><!-- #preview_team -->
                    
           		</li><!-- .preview_content_block.wide -->
                
                <li id="studio_block" class="preview_content_block wide last">
                    <div class="block_link subheading"><a href="/about/the-studio/">The Studio</a></div>
                    <article class="post no_border">
                     <?php echo get_the_post_thumbnail( 4475, 'thumbnail'); ?>
                     
                         <?php $excerpt = get_the_excerpt_by_id(4475);
					   			echo $excerpt; ?>
                    </article>
                
                </li><!-- .preview_content_block.wide -->

                <li class="preview_content_block">
                    <div class="block_link subheading"><a href="/about/history/">History</a></div>
                    
                    <article class="post no_border">
                         <?php $excerpt = get_the_excerpt_by_id(3911);
					   			echo $excerpt; ?>
                	</article>
                </li><!-- .preview_content_block.wide -->
                
                <li class="preview_content_block">
                    <div class="block_link subheading"><a href="/about/jobs/">Jobs</a></div>
                    
                    <article class="post no_border">
                         <?php $excerpt = get_the_excerpt_by_id(3917);
					   			echo $excerpt; ?>
                	</article>
                </li><!-- .preview_content_block.wide -->
                
                <li class="preview_content_block">
                    <div class="block_link subheading"><a href="/about/why-9/">The Name</a></div>
                	
                    <article class="post no_border">
                         <?php $excerpt = get_the_excerpt_by_id(3913);
					   			echo $excerpt; ?>
                    </article>
                </li><!-- .preview_content_block.wide -->
                
                <li class="preview_content_block">
                    <div class="block_link subheading"><a href="/about/philosophy/">Philosophy</a></div>
                    
                    <article class="post no_border">
                         <?php $excerpt = get_the_excerpt_by_id(3921);
					   			echo $excerpt; ?>
                	</article>
                </li><!-- .preview_content_block.wide -->
                
                <li class="preview_content_block last">
                    <div class="block_link subheading"><a href="/about/standards/">Standards</a></div>
                    
                    <article class="post no_border">
                       
                       <?php $excerpt = get_the_excerpt_by_id(3915);
					   		echo $excerpt; ?>
                 
                 
                    </article>
                </li><!-- .preview_content_block.wide -->
        	</ul><!-- #preview_content -->

        

         </div><!-- .content_right -->
      
      </div><!-- .block_container -->
		

<?php get_footer(); ?>