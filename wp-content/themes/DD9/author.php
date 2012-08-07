<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 

//exclude category id 3 ("Design Screenshots")
unset($wp_query->query['pagename']);
query_posts(array_merge($wp_query->query, array('cat' => -3)));

$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));

$user_id = $curauth->ID;
$full_name = get_the_author_meta('user_firstname', $curauth->ID) . ' ' .
             get_the_author_meta('user_lastname', $curauth->ID);
$description = get_the_author_meta('user_description', $curauth->ID);
$email = $curauth->user_email;
$url = $curauth->user_url;
$clean_url = str_replace('http://','', str_replace('www.', '', $curauth->user_url));
$relationship = get_the_author_meta('relationship', $curauth->ID);

$projects = get_posts(array(
	'connected_type' => 'project_users',
	'post_type' => 'project',
	'suppress_filters' => false,
	'numberposts' => 3,
	'connected_to' => $curauth->ID,
	'connected_orderby' => '_order_from'
));

$design_shots = get_posts(array(
	'post_type' => 'post',
	'author' => $curauth->ID,
	'numberposts' => 8,
	'category' => 3,
	'post_status' => 'publish',
	'order' => 'DESC',
	'orderby' => 'post_date'
));

?>



		<section id="primary" class="full_width">
			<div id="content" role="main">
            
             <div class="block_container full_width clearfix">
        
              <div class="two_column">
                <h4 class="subheading_full_width"><span>Author Archives</span></h4>
                  <div class="block_content">
                    <h1 class="secondary"><?= $full_name ?> </h1>
                    
                  </div>
              </div><!-- .two_column -->         
              
              <div class="content_right"> 
                
                <div class="user_block team clearfix">

                  <div class="user_bio">		
                       <h3 class="user_name">
                
                        <?= $full_name ?><span class="relationship">, <?= $relationship ?></span>	
                      
                      </h3>
                      
                      <p><?= $description ?></p>
                      
                  </div><!-- .user_bio -->
                  
                  <div class="user_gravatar">
                    <?= get_avatar($user_id, '234', null, $full_name) ?>       
                  </div><!-- .user_gravatar -->
                   	           
                                         
                      <?php 
                      // Put this back in when we're sure we want our email addresses available on the internet unobfuscated
                      
                      /*
                      <h4 class="title">Email:</h4> 
                      <a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a>
                      */
                      
                      ?>
                    <ul class="user_details">
                     
                     <?php if($projects): ?>
                        <li>
                          <ul class="user_projects">
                              <li><h4 class="title">Recent Projects:</h4> </li>
                            <?php foreach($projects as $project): ?>
                              <li>
                                  <a href="<?= get_permalink($project->ID) ?>"><?= $project->post_title ?></a>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        </li>
                      <?php endif; ?>   
                    
                     <?php if($url): ?>
                        <li>
                          <h4 class="title">Elsewhere Online:</h4> 
                          <a href='<?= $url ?>' target="_blank"><?= $clean_url ?></a>
                        <li>
                      <?php endif; ?>
                       
                    </ul><!-- .user_details --> 	
                
                </div><!-- .user_block -->
                  
                
                </div><!-- .content_right -->
              </div><!-- .block_container -->

			
            
             
            <?php if($design_shots): ?>
               <div class="block_container full_width clearfix">
        
                <div class="two_column">
                    <div class="block_content border_top">
                      <h3> Design Stream</h3>
                      <p class="top_line"><em><a class="viewmore" href="/category/design-screenshots/">View More Real-time Project Screenshots</a></em></p>
                    </div>
                </div><!-- .two_column -->         
                
                <div class="content_right thin_border"> 
                  
                  <ul id="design_stream" class="clearfix">
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
                             
                              <?php if($image->ID): $image_thumb = wp_get_attachment_image_src($image->ID, 'thumbnail');$image_full = wp_get_attachment_image_src($image->ID, 'large'); ?>
                          <a href="<?= $image_full[0] ?>" class="fancybox"><img src="<?= $image_thumb[0] ?>" width="108" height="108" alt="<?php the_title(); ?>"  title="<?= $image->post_excerpt ?>" /></a>
                        <?php endif; ?>
                            
                            <span class="design_shot_time"><?php the_time('m/d/y g:i a'); ?></span>
                            </a>
                          <?php endforeach; ?>
                        <?php else: ?>
                        <?php endif; ?> 
                      </li>
                     <?php endforeach; ?>
                  </ul>
                  
                
              
                  </div><!-- .content_right -->
                </div><!-- .block_container -->
		  <?php else: ?>
          <?php endif; ?>
						
				<?php if ( have_posts() ) : ?>
                
                
                 <div class="block_container full_width clearfix">
        
                  <div class="two_column">
                      <div class="block_content border_top">
                        <h3><?php echo $curauth->display_name; ?>'s Posts</h3>
                        <p class="top_line"><em><a class="viewmore" href="/blog">View Full Archive</a></em></p>
                      </div>
                  </div><!-- .two_column -->         
                  
                  <div class="content_right blog"> 

                    <?php /* Start the Loop */ ?>
                    <?php while ( have_posts() ) : the_post(); ?>
    
                        <?php
                            /* Include the Post-Format-specific template for the content.
                             * If you want to overload this in a child theme then include a file
                             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                             */
                            get_template_part( 'content', get_post_format() );
                        ?>
    
                    <?php endwhile; ?>
    
                    <?php twentyeleven_content_nav( 'nav-below' ); ?>
                    
                   </div><!-- .content_right -->
                </div><!-- .block_container --> 
    
                <?php else : ?>

                  <article id="post-0" class="post no-results not-found">
                      <header class="entry-header">
                          <h3 class="entry-title"><?php _e( 'No posts at this time', 'twentyeleven' ); ?></h3>
                      </header><!-- .entry-header -->
  
                      <?php /*?><div class="entry-content">
                          <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
                          <?php get_search_form(); ?>
                      </div><!-- .entry-content --><?php */?>
                  </article><!-- #post-0 -->

			<?php endif; ?>

			</div><!-- #content -->
		</section><!-- #primary -->

<?php /*?><?php get_sidebar(); ?><?php */?>
<?php get_footer(); ?>