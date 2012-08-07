<?php
include_once('../../../../wp-config.php');
include_once('../../../../wp-includes/wp-db.php');
//-------------------------------------------------------
// Perform crawling a batch of post urls. Batch size is 
// provided in 'batch_size'. Batch starting id is provided
// in 'from_id'.
//-------------------------------------------------------
    /*
     * Constructs an absolute URL for a link.
     */
    function make_site_url( $url )
    {
        $pos = stripos( $url, ':' );
        if( $pos > 0 )
        {
            return $url;
        }
        else
        if( is_multisite() )
        {
            return network_site_url( $url );
        }
        else
        {
            return site_url( $url );
        }
    }

    crawl_submit();
    function crawl_submit( )
    {
        global $wpdb;
        global $blog_id;

        $post_type_in = '';

        $id_from = $_POST['from_id'];
        if( !isset( $id_from ) )
        {
            return false;
        }
        $orig_apikey = $_POST['apikey'];  
        $foreign_blog_id = $_POST['blog_id'];

        $batch_size = $_POST['batch_size'];

        $strToken = explode(',', $_POST['post_types']); 

        foreach( $strToken as $t )
        {
            if( !empty( $post_type_in ) )
            {
                $post_type_in .= ',';
            }
            $post_type_in .= '"' . esc_sql( $t ) . '"';        
        }
        $post_type_in = '(' . $post_type_in . ')' ;

        $yolink_config = get_option('yolink_config');
        if( !isset( $orig_apikey ) )
        {
            $orig_apikey = $yolink_config['yolink_apikey'];
        }

        if( isset( $foreign_blog_id ) && $foreign_blog_id != $blog_id )
        {
            switch_to_blog( $foreign_blog_id );
        }

        $post_recs = $wpdb->get_results( $wpdb->prepare( "SELECT ID,GUID FROM $wpdb->posts WHERE post_status=%s AND post_type IN $post_type_in AND ID > %d order by ID asc LIMIT %d", 'publish', $id_from, $batch_size ) );
        $post_count = $wpdb->num_rows;

        echo '<tl_count>', $post_count , '</tl_count>';

        $last_id = -1;

        $permalinks = array();
        
        $post_recs = (object) $post_recs;
        $source = '';
        if( is_multisite() )
        {
            $blog_details = get_blog_details($blog_id);
            $source = $blog_details->domain . $blog_details->path;
        } 
        foreach( $post_recs as $post_rec )
        {
            (object)$post_rec = $post_rec;
            if( $post_rec->GUID != null )
            {
                $annotation = array( 'wp_blog_id' => (int)$blog_id, 'wp_post_id' => (int)$post_rec->ID );
                $url        = get_permalink( $post_rec->ID );

                $custom     = get_post_custom( $post_rec->ID );
                $yolinkURL  = @$custom[ 'yolink_custom_url' ][0];

                if( $yolinkURL )
                {
                    $yolinkURL                  = make_site_url( $yolinkURL );
                    $annotation[ 'actual_url' ] = $yolinkURL;
                    $url                        = $yolinkURL;
                }

                $postdata = array(
                    'url'           => $url,
                    'depth'         => 0,
                    'annotation'    => (object)$annotation,
                    'source'        => $source,
                );
                $permalinks['urls'][] = $postdata;
            }
            $last_id = $post_rec->ID;
        }

        $json_object = json_encode($permalinks);
        if( is_wp_error( $json_object ) )
        {
            if( is_multisite() )
            {
                restore_current_blog();
            }
            return false;
        }
        if( $post_recs )
        {
            $json_out = do_post( $json_object, $orig_apikey );            
        }
        if( is_multisite() )
        {
            restore_current_blog();
        }
        if( $post_count >= $batch_size )
        {
            echo '<tl_last>', $last_id, '</tl_last>';
        }
        return $last_id;    
    }

    function do_post( $postdata, $apikey )
    {
        $api_url = 'http://index.yolink.com/index/crawl?o=JSON&ak=' . urlencode($apikey);
        $request = new WP_Http;
        $args = array(
            'headers'        => array( 'Content-Type' => 'application/json; charset=utf-8'),
            'body'            => $postdata,
        );
        $out = $request->post( $api_url, $args );
    }
    
?>