<?php 

/**
 * Loading More Listings for Suggested / Available Houses.
 */
/** Action for loading more listings in suggested houses */
add_action( 'wp_ajax_nopriv_homey_child_loadmore_listings', 'homey_child_loadmore_listings' );
add_action( 'wp_ajax_homey_child_loadmore_listings', 'homey_child_loadmore_listings' );
function homey_child_loadmore_listings() {
        global $post, $homey_prefix, $homey_local;
        $homey_prefix = 'homey_';
        $homey_local = homey_get_localization(); 
	$curr_reservationid = $_POST['reservationid']; 
	//var_dump($curr_reservationid); exit;

        $fake_loop_offset = 0; 
        $tax_query = array();

        $searchText = sanitize_text_field($_POST['searchterm']);
	$searchType = $_POST['stype'];
	$postcodeToLook = sanitize_text_field($_POST['postcode']);
        $type = sanitize_text_field($_POST['type']);
        $roomtype = sanitize_text_field($_POST['roomtype']);
        $country = sanitize_text_field($_POST['country']);
        $state = sanitize_text_field($_POST['state']);
        $city = sanitize_text_field($_POST['city']);
        $area = sanitize_text_field($_POST['area']);
        $nbeds = sanitize_text_field($_POST['beds']);
        $nbaths = sanitize_text_field($_POST['baths']);

        $listing_style = sanitize_text_field($_POST['style']);
        $listing_type = homey_traverse_comma_string($type);
        $listing_roomtype = homey_traverse_comma_string($roomtype);
        $listing_country = homey_traverse_comma_string($country);
        $listing_state = homey_traverse_comma_string($state);
        $listing_city = homey_traverse_comma_string($city);
        $listing_area = homey_traverse_comma_string($area);
        $featured = sanitize_text_field($_POST['featured']);
        $posts_limit = sanitize_text_field($_POST['limit']);
        $sort_by = sanitize_text_field($_POST['sort_by']);
        $offset = sanitize_text_field($_POST['offset']);
        $paged = sanitize_text_field($_POST['paged']);
        $author = sanitize_text_field($_POST['author']);
        $authorid = sanitize_text_field($_POST['authorid']);

        $wp_query_args = array(
            'ignore_sticky_posts' => 1
        );

	if(!empty($searchText)){ 
	   $keywords = '';
	   if(strstr($searchText,',')){
	    $keyword_array = explode(',',$searchText);
	    foreach($keyword_array as $keyword){ $keywords .= trim($keyword).' '; }
	   } else{ $keywords .= $searchText; }
	   $wp_query_args['s'] = $keywords;
	}

        if (!empty($listing_type)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_type',
                'field' => 'slug',
                'terms' => $listing_type
            );
        }

        if (!empty($listing_roomtype)) {
            $tax_query[] = array(
                'taxonomy' => 'room_type',
                'field' => 'slug',
                'terms' => $listing_roomtype
            );
        }

        if (!empty($listing_country)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_country',
                'field' => 'slug',
                'terms' => $listing_country
            );
        }
        if (!empty($listing_state)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_state',
                'field' => 'slug',
                'terms' => $listing_state
            );
        }
        if (!empty($listing_city)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_city',
                'field' => 'slug',
                'terms' => $listing_city
            );
        }
        if (!empty($listing_area)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_area',
                'field' => 'slug',
                'terms' => $listing_area
            );
        }

        if($author == 'yes') {
            $wp_query_args['author'] = $authorid;
        }

        if ( $sort_by == 'a_price' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'homey_night_price';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_price' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'homey_night_price';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'a_rating' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'listing_total_rating';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_rating' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'listing_total_rating';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured' ) {
            $wp_query_args['meta_key'] = 'homey_featured';
            $wp_query_args['meta_value'] = '1';
        } else if ( $sort_by == 'a_date' ) {
            $wp_query_args['orderby'] = 'date';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_date' ) {
            $wp_query_args['orderby'] = 'date';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured_top' ) {
            $wp_query_args['orderby'] = 'meta_value';
            $wp_query_args['meta_key'] = 'homey_featured';
            $wp_query_args['order'] = 'DESC';
        }

        if (!empty($featured)) {
            if( $featured == "yes" ) {
                $wp_query_args['meta_key'] = 'homey_featured';
                $wp_query_args['meta_value'] = '1';
            } else {
                $wp_query_args['meta_key'] = 'homey_featured';
                $wp_query_args['meta_value'] = '0';
            }
        }

	if(!empty($postcodeToLook)){ 
	  $wp_query_args['meta_key'] = 'homey_zip';
          $wp_query_args['meta_value'] = $postcodeToLook;
	}

	if(!empty($nbeds)){ 
	  $wp_query_args['meta_key'] = 'homey_beds';
          $wp_query_args['meta_value'] = $nbeds;
	  $wp_query_args['compare'] = '>=';
	}

	if(!empty($nbaths)){ 
	  $wp_query_args['meta_key'] = 'homey_zip';
          $wp_query_args['meta_value'] = $nbaths;
	  $wp_query_args['compare'] = '>=';
	}

        $tax_count = count( $tax_query );
        if( $tax_count > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if( $tax_count > 0 ){
            $wp_query_args['tax_query'] = $tax_query;
        }

        $wp_query_args['post_status'] = 'publish';

        if (empty($posts_limit)) {
            $posts_limit = get_option('posts_per_page');
        }
        $wp_query_args['posts_per_page'] = $posts_limit;

        if (!empty($paged)) {
            $wp_query_args['paged'] = $paged;
        } else {
            $wp_query_args['paged'] = 1;
        }

        if (!empty($offset) and $paged > 1) {
            $wp_query_args['offset'] = $offset + ( ($paged - 1) * $posts_limit) ;
        } else {
            $wp_query_args['offset'] = $offset ;
        }

        $fake_loop_offset = $offset;
        $wp_query_args['post_type'] = 'listing'; 
	//var_dump($wp_query_args); exit;
        $the_query = new WP_Query($wp_query_args); 

	$selected_props = get_post_meta($curr_reservationid, 'reservation_selections', true); 
        if(!empty($selected_props)){ $selected_props_array = explode(',',$selected_props); }

	$html = '';
        if ($the_query->have_posts()) :
	$html .= '<div class="table-block dashboard-listing-table dashboard-table">';
        $html .= '<table class="table table-hover">';
	if($searchType == 'suggested-houses'){ 
	  $html .= '<tbody id="suggested_houses_listings_ajax">';
	} else { $html .= '<tbody id="available_houses_listings_ajax">'; }	
            while ($the_query->have_posts()) : $the_query->the_post();
		$address = get_post_meta( get_the_ID(), 'homey_listing_address', true );
		$approvedDt = get_post_meta( get_the_ID(), 'host_approval_till', true );
 
                //if($listing_style == 'card') {
                //    $html .= get_template_part('template-parts/listing/listing-card');
                //} else {
                //    $html .= get_template_part('template-parts/listing/listing-item');
                //}

	        $style='';
	        if(!empty($selections)){ if(in_array($post->ID, $selections)){ $style = 'style="background:#d0c8c8;"'; }} 
	        if(!empty($approvedDt) && (!in_array($post->ID, $selections))){ 
		  if( time() <= strtotime($approvedDt)){ $style = 'style="background:#D6F4D0;"'; }
		} 
		//$html .= '<tr id="tr-id-'.$post->ID.'"'; 
		//if(!empty($selected_props)){ if(in_array($post->ID, $selected_props_array)){ $html .= ' style="background:#d0c8c8;"'; }}
		//$html .= '>';

	   	$html .= '<tr id="tr-id-'.$post->ID.'"'.' '.$style.'>';	
		$html .= '<td data-label="'.esc_html__('thumbnail').'" width="11%">';
        	$html .= '<a href="'.get_the_permalink().'">';
		if( has_post_thumbnail( $post->ID ) ) {
            	  $html .= get_the_post_thumbnail( $post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        	}else{ 
		  $html .= homey_image_placeholder( 'homey-listing-thumb' ); 
		} 
        	$html .= '</a></td>';
		$html .= '<td data-label="'.esc_html__('title-address').'">';
		$html .= '<div style="float:left;width:26%">';
		$html .= '<a href="'.get_the_permalink().'"><strong>'.get_the_title().'</strong></a>';
       		if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
		$html .= '</div>';
		$html .= '<div style="float:left;width:10%">';
		$html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';

		//$html .= '<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" style="font-size:10px;border: 1px solid #d0c8c8;"';
		//if(!empty($selected_props)){ if(in_array($post->ID, $selected_props_array)){ $html .= ' disabled=true'; }}
		//$html .= '>'.esc_html__('Check Availability').'</button>';
		//$html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>'.esc_html__('Message Host', 'homey-child').'</div></div></td>';

		if(!empty($approvedDt)){ 
		  if(time() <= strtotime($approvedDt)){ 
		    $html .= '<button type="button" class="btn btn-full-width add-property-to-approval"';
	     	    if(!empty($selections)){ if(in_array($post->ID, $selections)){ $html .= ' disabled=true '; }}
		    $html .= 'style="font-size:10px;border: 1px solid #d0c8c8;">'.esc_html__('AddTo Approval').'</button>';
		    $html .= '<div class="text-center text-small" style="font-size:10px;">';
		    $html .= '<i class="fa fa-info-circle"></i>'.esc_html__('Add To Approved', 'homey-child').'</div>';
		  } else { 
		    $html .= '<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal"';
		    if(!empty($selections)){ if(in_array($post->ID, $selections)){ $html .= ' disabled=true '; }} 
		    $html .= 'style="font-size:10px;border: 1px solid #d0c8c8;">'.esc_html__('Check Availability').'</button>';
		    $html .= '<div class="text-center text-small" style="font-size:10px;">';
		    $html .= '<i class="fa fa-info-circle"></i>'.esc_html__('Send Message To Host', 'homey-child').'</div>';
		  }
		} else { 
		    $html .= '<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal"';
		    if(!empty($selections)){ if(in_array($post->ID, $selections)){ $html .= ' disabled=true '; }} 
		    $html .= 'style="font-size:10px;border: 1px solid #d0c8c8;">'.esc_html__('Check Availability').'</button>';
		    $html .= '<div class="text-center text-small" style="font-size:10px;">';
		    $html .= '<i class="fa fa-info-circle"></i>'.esc_html__('Send Message To Host', 'homey-child').'</div>';
		}
		$html .= '</tr>';
            endwhile;
	    $html .= '</tbody></table></div>';
            wp_reset_postdata();
	    echo $html; 
        else:
            echo '<p> No Properties Obtained In Search </p>';
        endif;
        wp_die();
}

