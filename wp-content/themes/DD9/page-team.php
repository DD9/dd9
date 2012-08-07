<?php 

$raw = get_users();
$users = get_data_for_user_array($raw);

$about_children = array(
	'depth'        => 0,
	'child_of'     => 3470,
	'title_li'     => '',
	'sort_column'  => 'menu_order'
); 

get_header(); the_post(); 

?>



         
            <?php // Still need the content for a potential page intro
            the_content(); ?>


          
			<?php 
            // Get the authors from the database ordered by a custom order.
            //  Custom order: Up to Taavo. Can be custom field or another method.
            // Group users into three categories: 1) Team   2) Associates  3) Alumni
            // Grouping can potentially be done by user role (i.e. team=admin, associates=subscribors, but would need to exclude certain users (like "admin" and "wpengine")
            //  Grouping could also be done by custom field, or another method 
			//  We also need to add some metafields to User Profile.  To start, just this one field, may be expanded later:
			    //  1. Relationship  (this is the user's job title or work relationship with DD9)
            
            ?>
            
            
<?php // Category One: The Team (Todd, Taavo, Hilary)?> 
        <div class="block_container full_width clearfix">
        
          <div class="two_column">
            <h4 class="subheading_full_width"><a href="/about/">About</a></h4>
              <div class="block_content">
                <h1 class="secondary">The DD9 Team</h1>
                <p class="top_line"></p>
                <ul class="arrows sidebar_nav"><?php wp_list_pages( $about_children ); ?></ul>
              </div>
          </div><!-- .two_column -->         
          
          <div class="content_right"> 
			<?php foreach($users['team'] as $user): ?>
            
                <div class="user_block team clearfix">

                    <div class="user_bio">		
                        <h3 class="user_name">
                        
                            <a href="<?= $user['posts_url'] ?>">
                                <?= $user['name'] ?><span class="relationship">, <?= $user['relationship'] ?></span>
                            </a>
                          
                        </h3>
                        
                        <p><?= $user['description'] ?></p>
                        
                    </div><!-- .user_bio --> 	
                    
                    <div class="user_gravatar">                        
                        <a href="<?= $user['posts_url'] ?>">
                            <?= get_avatar($user['id'], '234', null, $name) ?>                    
                        </a>
                    </div><!-- .user_gravatar -->              
                   
                   <?php // All calls below should be "if" statements, since we don't want the <h4> to exist without it's respective data. ?>	
                    
                        <?php 
                        // Put this back in when we're sure we want our email addresses available on the internet unobfuscated
                        
                        /*
                        <h4 class="title">Email:</h4> 
                        <a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a>
                        */
                        
                        ?>
                      <ul class="user_details">
                       <li>    
                         <h4 class="title">Blog:</h4> 
                         <a href="<?= $user['posts_url'] ?>">View <?= $user['display_name'] ?>'s posts</a>
                       </li> 
					   <?php if($user['projects']): ?>
                         <li>
                         
                          <ul class="user_projects">
 							  <li> 
                                <h4 class="title">Recent Projects:</h4>
                              </li>
							
							<?php foreach($user['projects'] as $project): ?>
                              <li>
                                  <a href="<?= get_permalink($project->ID) ?>"><?= $project->post_title ?></a>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        </li>
                       <?php endif; ?>
                      
                       <?php if($user['url']): ?>
                         <li>
                          <h4 class="title">Elsewhere Online:</h4> 
                          <a href="<?= $user['url'] ?>" target="_blank"><?= $user['clean_url'] ?></a>
                         </li>
					   <?php endif; ?>
                         
                      </ul><!-- .user_details --> 	
                
                </div><!-- .user_block -->
              
			<?php endforeach; ?>
            
            </div><!-- .content_right -->
          </div><!-- .block_container -->




          <div class="block_container full_width clearfix">
            
            <div class="two_column">
                <div class="block_content border_top">
                  <h3> The Associates </h3>
                  <p class="top_line"></p>
                  <p>Professional associates, recommended for an array of support services.</p>
                </div>
            </div><!-- .two_column -->   
            
            <div class="content_right thin_border overflow"> 
              <ul class="support_staff clearfix">
              <?php foreach($users['associates'] as $user): ?>
                  <li class="user_block">
                      <div class="user_header clearfix">
                          <h6 class="user_name">                        
                              <a href="<?= $user['posts_url'] ?>"><?= $user['name'] ?></a>
                              <span class="relationship"><?= $user['relationship'] ?></span>	
                          </h6>
                          
                          <div class="user_gravatar">
                            <a href="<?= $user['posts_url'] ?>">
                                <?= get_avatar($user['id'], '108', null, $name) ?>                    
                            </a>
                          </div>
                          
                      </div><!-- .user_header -->  
                      
                      <div class="user_bio">		
                          <p><?= $user['description'] ?></p>
                      </div><!-- .user_bio --> 	            
                      
                      <ul class="user_details">                        
                          <!--li>
                            <h6 class="title">Design Stream:</h6> 
                            <a href="<?= $user['posts_url'] ?>">View <?= $user['display_name'] ?>'s posts</a>
                          </li-->
                            
                          <?php if($user['projects']): ?>
                          <li>
                            
                              <?php // Load title and link of 3 most recent projects connected to this user ?>
                              
                              <ul class="user_projects">
                                     <li> 
                                       <h6 class="title">Recent Projects:</h6>
                                     </li>
                                  <?php foreach($user['projects'] as $project): ?>
                                      <li>
                                          <a href="<?= get_permalink($project->ID) ?>"><?= $project->post_title ?></a>
                                      </li>
                                  <?php endforeach; ?>
                              </ul>
                          </li>
                          <?php endif; ?>                    
                      </ul><!-- .user_details --> 	
                  </li><!-- .user_block -->
              <?php endforeach; ?>
              </ul><!--.support_staff -->
            </div><!-- .content_right -->
          </div><!-- .block_container -->  

          
          <div class="block_container full_width clearfix">
            
             <div class="two_column">
                <div class="block_content border_top">
                  <h3> The Alumni</h3>
                  <p class="top_line"></p>
                     <p>Fantastic talent, and members of the DD9 team past.</p>
                </div>
            </div><!-- .two_column -->   
            
            
            <div class="content_right thin_border overflow"> 
              <ul class="support_staff clearfix">
			  <?php foreach($users['alumni'] as $user): ?>            
                  <li class="user_block">
                      <div class="user_header clearfix">
                          <h6 class="user_name">                        
                              <a href="<?= $user['posts_url'] ?>"><?= $user['name'] ?></a>
                              <span class="relationship"><?= $user['relationship'] ?></span>	
                          </h6>
                          
                          <div class="user_gravatar">
                            <a href="<?= $user['posts_url'] ?>">
                                <?= get_avatar($user['id'], '108', null, $name) ?>                    
                            </a>
                          </div>
                          
                      </div><!-- .user_header -->  
                      
                      <div class="user_bio">		
                          <p><?= $user['description'] ?></p>
                      </div><!-- .user_bio --> 	            
                      
                      <ul class="user_details">                        
                          <li>
                            <h6 class="title">Design Stream:</h6> 
                            <a href="<?= $user['posts_url'] ?>">View <?= $user['display_name'] ?>'s posts</a>
                          </li>
                            
                          <?php if($user['projects']): ?>
                          <li>
                              
                              <?php // Load title and link of 3 most recent projects connected to this user ?>
                              
                              <ul class="user_projects">
                                     <li> 
                                       <h6 class="title">Recent Projects:</h6>
                                     </li>

                                  <?php foreach($user['projects'] as $project): ?>
                                      <li>
                                          <a href="<?= get_permalink($project->ID) ?>"><?= $project->post_title ?></a>
                                      </li>
                                  <?php endforeach; ?>
                              </ul>
                          </li>
                          <?php endif; ?>                    
                      </ul><!-- .user_details --> 	
                  </li><!-- .user_block -->    
              <?php endforeach; ?>
              </ul><!--.support_staff -->
            </div><!-- .content_right -->   
              
		  </div><!-- .block_container --> 
          
          

<?php get_footer(); ?>