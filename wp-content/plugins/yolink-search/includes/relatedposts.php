<?php
	/**
		Checks if updating $related_post's is needed.
	*/
	function is_update_necessary( $related_post )
	{
		global $blog_id;
		
		if( $blog_id != $related_post->annotation->wp_blog_id )
		{
			switch_to_blog( $related_post->annotation->wp_blog_id );
		} 
		$yolink_config = get_option('yolink_config');
		$cached = get_post_meta( $related_post->annotation->wp_post_id, 'yolink_related_posts', true);

		// If no cached found or cache is expired, then skip
		if( $cached == false || !isset( $cached ) || $cached->timestamp < 0 )
		{
			if( is_multisite() )
			{
				restore_current_blog();
			}
			return false;
		}
	
		// If same blog, return true;
		if( $blog_id == $related_post->annotation->wp_blog_id )
		{	
			if( is_multisite() )
			{
				restore_current_blog();
			}
			return true;
		}
		// Ensure the show relate post is enabled on the foreign blog
		if( !isset($yolink_config['wp_show_related_posts']) || $yolink_config['wp_show_related_posts'] != 'true' )
		{
			if( is_multisite() )
			{
				restore_current_blog();
			}
			return false;
		}

		// If not same blog, ensure foreign blog is configured to include the current post
		$included_blogs = $yolink_config['included_blogs'];
		if( isset( $included_blogs ) )
		{
			foreach( $included_blogs as $b )
		        {
			 	if( !empty($b) )
            			{
					if( $blog_id == $b )
					{
						if( is_multisite() )
						{
							restore_current_blog();
						}
						return true;
					}
			     	}
                    	}
		}
		if( is_multisite() )
		{
			restore_current_blog();
		}
		return false;
	}

	function add_current_post_to( $related_post )
	{
		if( is_update_necessary( $related_post ) )
		{
			global $post;
			global $blog_id;
			$cur_permalink = get_permalink( $post->ID );
			$title = get_the_title();
			
			if( $blog_id != $related_post->annotation->wp_blog_id )
			{
				switch_to_blog( $related_post->annotation->wp_blog_id );
			}
			$yolink_config = get_option('yolink_config');
			$cached = get_post_meta( $related_post->annotation->wp_post_id, 'yolink_related_posts', true);
			$found = false;
			foreach( $cached->urls as $key=> $rec )
			{
				if( $rec->annotation->wp_blog_id == $blog_id && 
				    $rec->annotation->wp_post_id == $post->ID )
				{
					// already have it, skip
					$found= true;
					break;
				}
			}
			if( $found == false )
			{
				$cached->urls[] = (object) array(
					'url'			=> $cur_permalink,
					'title'			=> ( $title . ' | ' . get_bloginfo() ),
					'annotation'	=> (object) array( 'wp_blog_id' => (int)$blog_id, 'wp_post_id' => (int)$post->ID ),
				);
				update_post_meta( $related_post->annotation->wp_post_id, 'yolink_related_posts', $cached);
			}
			if( is_multisite() )
			{
				restore_current_blog();
			}
			
		}
	}
	function remove_current_post_from( $related_post )
	{
		if( is_update_necessary( $related_post ) )
		{
			global $post;
			global $blog_id;
			$cur_permalink = get_permalink( $post->ID );
			$title = get_the_title();
			
			if( $blog_id != $related_post->annotation->wp_blog_id )
			{
				switch_to_blog( $related_post->annotation->wp_blog_id );
			}
			$yolink_config = get_option('yolink_config');
			$cached = get_post_meta( $related_post->annotation->wp_post_id, 'yolink_related_posts', true);
			foreach( $cached->urls as $key=> $rec )
			{
				if( $rec->annotation->wp_blog_id == $blog_id && 
				    $rec->annotation->wp_post_id == $post->ID )
				{
					unset( $cached->urls[$key] );
					update_post_meta( $related_post->annotation->wp_post_id, 'yolink_related_posts', $cached);
					break;
				}
			}
			if( is_multisite() )
			{
				restore_current_blog();
			}
		}
	}
	function compare_posts( $url1, $url2 )
	{
		return $url1->wp_blog_id == $url2->wp_blog_id && $url1->wp_post_id == $url2->wp_post_id;
	}
	/**
		Update related posts cached records. For the current post, comparing its related posts before and after
		an update. If any different, update its's related post's counter association. 
		$related_posts_old -- previously cached 
		$related_posts_new -- new related posts
	*/
	function update_related_posts_counterpart( $related_posts_old, $related_posts_new )
	{
		
		if( isset( $related_posts_new ) )
		{
			foreach( $related_posts_new->urls as $new )
			{
			    if( isset( $new->annotation ) )
			    {
				$found = false;
					
				if( !empty( $related_posts_old ) )
				{
					foreach( $related_posts_old->urls as $key=>$old )
					{
						if( compare_posts( $old->annotation, $new->annotation ) )
						{
							$found = true;
							unset( $related_posts_old->urls[$key] );
							break;
						}
					}
					
				}
				if( !$found )
				{
					add_current_post_to( $new );
				}
			    }
			}
		}
		if( !empty( $related_posts_old ) )
		{
			foreach( $related_posts_old->urls as $key=>$old )
			{
				remove_current_post_from( $old );
			}			
		}
	}

	function yolink_related_posts()
	{
		$yolink_config = get_option('yolink_config');
		if( !isset( $yolink_config['yolink_apikey'] ) )
		{
			return;
		}
		if (!isset($yolink_config['wp_show_related_posts'])) 
        	{
            		$wp_show_related_posts = "false";
        	}
        	else
        	{
            		$wp_show_related_posts = $yolink_config['wp_show_related_posts'];
        	}
?>
<h3><?php _e('Show Related Posts', 'yolink' ); ?></h3>
    <form action="" method="post" id="yolink-wp-show-related-form">
        <?php wp_nonce_field('yolink_wp_show_related_posts'); ?>
        <p><?php _e('Show related posts on each page?', 'yolink') ?></p>
        <table class="form-table">
        <tr>
            <th scope="row"><?php if ($wp_show_related_posts=="true") {echo "<b>";} ?>Yes<?php if ($wp_show_related_posts=="true") {echo "</b>";} ?></th>
            <td><input type="radio" name="yolink_wp_show_related_posts" id="wp_show_related_posts" value="true" <?php if ($wp_show_related_posts=="true") {echo "checked=\"checked\"";} ?> /></td>
        </tr>
        <tr>
            <th scope="row"><?php if ($wp_show_related_posts!="true") {echo "<b>";} ?>No<?php if ($wp_show_related_posts!="true") {echo "</b>";} ?></th>
            <td><input type="radio" name="yolink_wp_show_related_posts" id="wp_show_related_posts" value="false" <?php if ($wp_show_related_posts=="false" || $wp_show_related_posts==false) {echo "checked=\"checked\"";} ?> /></td>
        </tr>
        </table>
        <p class="submit">
            <input type="submit" name="yolink-wp-show-related-posts-submit" id="yolink-wp-show-related-posts-submit" class="button-primary" value="<?php _e('Save Settings','yolink') ?>" onclick="return confirm('Are you sure you want to change the setting?');"/>
            <input type="hidden" name="yolink-action-wp-show-related-posts-submit" id="yolink-action-wp-show-related-posts-submit" value="yolink_wp_show_related_posts_submit" />
        </p>
    </form>

    <h3><?php _e('Clear Related Posts Cache Data', 'yolink' ); ?></h3>
    <form action="" method="post" id="yolink-wp-clear-related-cache-form">
        <p><?php _e('To ensure faster page loads, related posts will only be calculated ten minutes after most recent update. If you\'d like to make sure that related posts are accurate up to the most recent post click Clear Cache below to force yolink to re-calculate all related posts.', 'yolink') ?></p>

        <!-- Malformed HTML... No opening <table> -->
        </table>
							  
        <p class="submit">
            <input type="submit" name="yolink-wp-clear-related-cache-submit" id="yolink-wp-clear-related-cache-submit" class="button-primary" value="<?php _e('Clear Cache','yolink') ?>" onclick="return confirm('Are you sure you want to clear the cache?');"/>
            <input type="hidden" name="yolink-action-wp-clear-related-cache-submit" id="yolink-action-wp-clear-related-cache-submit" value="yolink_wp_clear_related_cache_submit" />
        </p>
    </form>
<?php
	}
?>