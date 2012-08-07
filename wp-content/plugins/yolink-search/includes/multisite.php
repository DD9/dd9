<?php

	/*
	    Yolink blog and apikey mapping page detail.
	*/
	function yolink_multisite() 
	{
	    if (!current_user_can('manage_options'))  
	    {
    		wp_die( __('You do not have sufficient permissions to access this page.') );
	    }
		
	    global $wpdb;
	    global $blog_id;
	    $yolink_config = get_option('yolink_config');
	    $included_blogs = $yolink_config['included_blogs'];
		
	?>
	    <script type="text/javascript">
	    
            function blogs_to_include()
            {
        		var tmp = "";
		        jQuery('.blogs').each( 
       		    function(i,v)
          	    {
			        if( jQuery(v).is(':checked') )
			        {
					if( tmp != "" )
					{
						tmp += "|";
					}
				        tmp += jQuery(v).attr('name');
			        }
                });
				
		        jQuery('#yolink-action-blog-include-submit').attr('value', tmp);

                return confirm("<?php _e('This configuration change may require that each site be crawled again. Click OK to begin crawling immediately.', 'yolink') ?> ");
     	    }
       	    </script>
	<?php
	    if( isset( $yolink_config['yolink_apikey'] ) )
	    {
        	echo '<p>The API key for this blog is <b>' . $yolink_config['yolink_apikey'] . '</b>.</p>';	
		    $blogs= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_blogs ORDER BY blog_id" ) );
		
		    echo '<form action="" method="post" id="yolink-change-apikey-form">';
            echo '<p>' . _e('Please select the content from other sites that will be included in this site\'s search:', 'yolink') . '</p>';
            echo '<table class="form-table">';
	
            foreach( $blogs as $blog )
            {
                if( $blog->blog_id != $blog_id )
                {
                    echo '<tr>';

                    $checked = false;
                    if( isset( $included_blogs ) )
                    {
                        foreach ($included_blogs as $b)
                        {
                            if( $b == $blog->blog_id )
                            {
                                $checked = true;
                                break;
                            }
                        }
                    }
                    if( $checked )
                    {
                        echo '<td align="center"><input class = "blogs" type="checkbox" name=' . $blog->blog_id . ' id=' . $blog->blog_id . ' value=' . $blog->blog_id . ' checked /></td>';
                        echo '<td><b>' . $blog->domain . $blog->path . ' </b></td>';
                    }
                    else
                    {
                        echo '<td align="center"><input class = "blogs" type="checkbox" name=' . $blog->blog_id . ' id=' . $blog->blog_id . ' value=' . $blog->blog_id . ' /></td>';
                        echo '<td>' . $blog->domain . $blog->path . ' </td>';
                    }
                    echo '</tr>';
                }
            }

            echo '</table>';
            echo '<p class="submit">';
            echo '<input type="submit" name="yolink-blog-include-submit" id="yolink-blog-include-submit" class="button-primary" value="Save Settings" onclick="return blogs_to_include();" />';
            echo '<input type="hidden" name="yolink-action-blog-include-submit" id="yolink-action-blog-include-submit" value="" /></p>';
            echo '</form>';
	    }	
	}
?>