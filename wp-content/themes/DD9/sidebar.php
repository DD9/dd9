<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

$thecategory = get_category($cat);
?>
	 <div class="secondary" class="blog">
       
        
        <?php if ($thecategory->category_parent == '3' || $thecategory->cat_ID == '3'): ?> 
       
         
            <h4 class="subheading_full_width"><a href="/category/design-screenshots/" title="DD9 Design Stream Home">Design Stream</a></h4>
              <div class="block_content">
                <h1><?php
						printf( __( '%s', 'twentyeleven' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?> </h1>
                <p class="top_line"></p>
                 <?php echo category_description( $thecategory->cat_ID ); ?> 
                <p class="top_line"></p>
                <h4>Design Stream Categories </h4>
                <ul class="blog_categories">
                  <?php wp_list_categories( array(
                          'orderby'            => 'name',
                          'order'              => 'ASC',
                          'child_of'           => '3',
                          'show_count'         => 1,
                          'title_li'           => '',
                  )); ?>
                </ul>  
              
        
        <?php else: ?>	
        
        
            <h4 class="subheading_full_width"><a href="/blog/" title="DD9 Blog Home">Blog</a></h4>
              <div class="block_content">
                <h3><?php
						printf( __( '%s', 'twentyeleven' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?> </h3>
                <p class="top_line"></p>
                <?php echo category_description( $thecategory->cat_ID ); ?> 
                <p class="top_line"></p>
                <h4> Categories </h4>
                <ul class="blog_categories">
                  <?php wp_list_categories( array(
                          'orderby'            => 'name',
                          'order'              => 'ASC',
                          'exclude'            => '3',
                          'show_count'         => 1,
                          'title_li'           => '',
                  )); ?>
                </ul>
               
                
		<?php endif; ?>	
			
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

				<aside id="archives" class="widget">
					<h4 class="widget-title"><?php _e( 'Archives', 'twentyeleven' ); ?></h4>
					<ul>
						<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
					</ul>
				</aside>

				<aside id="meta" class="widget">
					<h4 class="widget-title"><?php _e( 'Meta', 'twentyeleven' ); ?></h4>
					<ul>
						<?php wp_register(); ?>
						<li><?php wp_loginout(); ?></li>
						<?php wp_meta(); ?>
					</ul>
				</aside>

			<?php endif; // end sidebar widget area ?>
            
            </div><!-- .block_content -->
		</div><!-- #secondary .two_column -->
