<?php 

/**
 * Lead Submission From Frontend
 */
/** Action for submission of enquire now form for unregistered user */
add_action( 'wp_ajax_nopriv_homey_child_add_reservation', 'homey_child_add_reservation' );  
add_action( 'wp_ajax_homey_child_add_reservation', 'homey_child_add_reservation' ); 
function homey_child_add_reservation() {
	global $current_user; $pauthor_id = 1; //default authors to admin
        $current_user = wp_get_current_user();
        $userID       = $current_user->ID;
        $local = homey_get_localization();
        $allowded_html = array();
        $reservation_meta = array(); 

	// -------------------------
	// sanitize all variables 
	// -------------------------
	$username = $_POST['name'];  
	if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ $useremail = $_POST['email']; 
	}else{ 
            echo json_encode( array( 'success' => false, 'message' => esc_html__('A Valid Email Is Required','homey-child') ) );
            wp_die();
	}
	if($_POST['phone'] != ''){
	if( preg_match('/^[0-9]{10}+$/', $_POST['phone']) ) { 
	    $userphone = $_POST['phone'];
	}else{
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Phone Number Would Need To Be Of 10 digits','homey-child') ) );
            wp_die();
	}}
        if($_POST['bedrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $_POST['bedrooms']) ) { 
	  $bedrooms = $_POST['bedrooms']; 
	}else{ 
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Bedrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}
        if($_POST['bathrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $_POST['bathrooms']) ) { 
	  $bathrooms = $_POST['bathrooms']; 
	}else{
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Bathrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}

        $listing_id = intval($_POST['listing_id']); 
	$postcode = get_post_meta($listing_id, 'homey_zip', true); 
        $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
        $check_in_date     =  wp_kses ( $_POST['check_in_date'], $allowded_html );
        $check_out_date    =  wp_kses ( $_POST['check_out_date'], $allowded_html );
        $extra_options    =  $_POST['extra_options'];
        $guest_message = stripslashes ( $_POST['guest_message'] );
        $guests   =  intval($_POST['guests']);
        //$title = $local['reservation_text']; 
	$title = 'Lead: Property ID: '.$listing_id.' PostCode: '.$postcode;
        $owner = homey_usermeta($listing_owner_id);
        $owner_email = $owner['email'];

	// ---------------------
	// check for errors 
	// ---------------------
        //if ( !is_user_logged_in() || $userID === 0 ) {   
        //    echo json_encode( array( 'success' => false, 'message' => $local['login_for_reservation'] ) );
        //    wp_die();
        //}

        /*if(!homey_is_renter()) {
        //    echo json_encode( array( 'success' => false, 'message' => $local['host_user_cannot_book'] ) );
        //    wp_die();
        }*/

        if($userID == $listing_owner_id) {
            echo json_encode( array( 'success' => false, 'message' => $local['own_listing_error'] ) );
            wp_die();
        }

        //check security
        $nonce = $_REQUEST['security'];
        if ( ! wp_verify_nonce( $nonce, 'reservation-security-nonce' ) ) {
             echo json_encode( array( 'success' => false, 'message' => $local['security_check_text'] ) );
             wp_die();
        }

	//assign a broker based on postcode
    	$broker_id = 0; $user_args  = array('role__in' => ['homey_brokers'], 'orderby' => 'display_name'); 
    	$brokers = get_users($user_args); 
    	foreach($brokers as $broker){  //echo $broker->ID; 
       	   $brokerpostcode = get_user_meta($broker->ID,'postalcode',true);
       	   if($postcode == $brokerpostcode){ $broker_id = $broker->ID; break; }
        }
    	// no brokers exist, then assign to admin
    	if($broker_id == 0){ $broker_id = 0; }

	//assign an agent based on postcode
    	$agent_id = 0; $user_args  = array('role__in' => ['homey_agents'], 'orderby' => 'display_name'); 
    	$agents = get_users($user_args); 
    	foreach($agents as $agent){  //echo $broker->ID; 
       	   $agentpostcode = get_user_meta($agent->ID,'postalcode',true);
       	   if($postcode == $agentpostcode){ $agent_id = $agent->ID; break; }
        }
    	// no agents exist, then assign to none
    	if($agent_id == 0){ $agent_id = 0; }

    	$contactdetails = 'Name: '.$username.PHP_EOL.'Email: '.$useremail.PHP_EOL.
		          'Phone: '.$userphone.PHP_EOL.'Message: '.$guest_message.PHP_EOL;

        $check_availability = check_booking_availability($check_in_date, $check_out_date, $listing_id, $guests);
        $is_available = $check_availability['success'];
        $check_message = $check_availability['message'];

        if($is_available) {
            $prices_array = homey_get_prices($check_in_date, $check_out_date, $listing_id, $guests, $extra_options);
            $reservation_meta['no_of_days'] = $prices_array['days_count'];
            //$reservation_meta['additional_guests'] = $prices_array['additional_guests'];
            //$upfront_payment = $prices_array['upfront_payment'];
            //$balance = $prices_array['balance'];
            //$total_price = $prices_array['total_price'];
            //$price_per_night = $prices_array['price_per_night'];
            //$nights_total_price = $prices_array['nights_total_price'];
            //$cleaning_fee = $prices_array['cleaning_fee'];
            //$city_fee = $prices_array['city_fee'];
            //$services_fee = $prices_array['services_fee'];
            //$days_count = $prices_array['days_count'];
            //$period_days = $prices_array['period_days'];
            //$taxes = $prices_array['taxes'];
            //$taxes_percent = $prices_array['taxes_percent'];
            //$security_deposit = $prices_array['security_deposit'];
            //$additional_guests = $prices_array['additional_guests'];
            //$additional_guests_price = $prices_array['additional_guests_price'];
            //$additional_guests_total_price = $prices_array['additional_guests_total_price'];
            //$booking_has_weekend = $prices_array['booking_has_weekend'];
            //$booking_has_custom_pricing = $prices_array['booking_has_custom_pricing'];

            $reservation_meta['check_in_date'] = $check_in_date;
            $reservation_meta['check_out_date'] = $check_out_date;
            $reservation_meta['guests'] = $guests;
            $reservation_meta['listing_id'] = $listing_id;
            //$reservation_meta['upfront'] = $upfront_payment;
            //$reservation_meta['balance'] = $balance;
            //$reservation_meta['total'] = $total_price;
            //$reservation_meta['price_per_night'] = $price_per_night;
            //$reservation_meta['nights_total_price'] = $nights_total_price;
            //$reservation_meta['cleaning_fee'] = $cleaning_fee;
            //$reservation_meta['city_fee'] = $city_fee;
            //$reservation_meta['services_fee'] = $services_fee;
            //$reservation_meta['period_days'] = $period_days;
            //$reservation_meta['taxes'] = $taxes;
            //$reservation_meta['taxes_percent'] = $taxes_percent;
            //$reservation_meta['security_deposit'] = $security_deposit;
            //$reservation_meta['additional_guests_price'] = $additional_guests_price;
            //$reservation_meta['additional_guests_total_price'] = $additional_guests_total_price;
            //$reservation_meta['booking_has_weekend'] = $booking_has_weekend;
            //$reservation_meta['booking_has_custom_pricing'] = $booking_has_custom_pricing;
            if($_POST['bedrooms'] != ''){ $reservation_meta['no_of_bedrooms'] = $bedrooms; }
            if($_POST['bathrooms'] != ''){ $reservation_meta['no_of_bathrooms'] = $bathrooms; }	
            $reservation_meta['location'] = $postcode;	

            $reservation = array(
                'post_title'    => $title,
                'post_status'   => 'pending', 
                //'post_status'   => 'publish',
                'post_type'     => 'homey_reservation' ,
                'post_author'   => $pauthor_id,
	        'post_excerpt'  => $contactdetails,
            );
            $reservation_id =  wp_insert_post($reservation );  
            
            /*$reservation_update = array(
                'ID'         => $reservation_id,
                'post_title' => $title.' '.$reservation_id
            );
            wp_update_post( $reservation_update );*/

            update_post_meta($reservation_id, 'reservation_listing_id', $listing_id);
            update_post_meta($reservation_id, 'listing_owner', $listing_owner_id);
            update_post_meta($reservation_id, 'reservation_checkin_date', $check_in_date);
            update_post_meta($reservation_id, 'reservation_checkout_date', $check_out_date);
            update_post_meta($reservation_id, 'reservation_guests', $guests);
            update_post_meta($reservation_id, 'reservation_meta', $reservation_meta);
            update_post_meta($reservation_id, 'reservation_status', 'under_review');
            update_post_meta($reservation_id, 'is_hourly', 'no');
    	    update_post_meta($reservation_id, 'slide_template', 'default');
    	    update_post_meta($reservation_id, 'homey_brokers', $broker_id);
    	    update_post_meta($reservation_id, 'homey_agents', $agent_id);
	
	    //client lead and soft lead
	    if($userID == 0){
              update_post_meta($reservation_id, 'listing_renter', $userID);
    	      update_post_meta($reservation_id, 'homey_lead_type', 'soft_lead');
	    }else{
              update_post_meta($reservation_id, 'listing_renter', $userID);
    	      update_post_meta($reservation_id, 'homey_lead_type', 'client_lead');
	    }

            //update_post_meta($reservation_id, 'extra_options', $extra_options);
            //update_post_meta($reservation_id, 'reservation_upfront', $upfront_payment);
            //update_post_meta($reservation_id, 'reservation_balance', $balance);
            //update_post_meta($reservation_id, 'reservation_total', $total_price);

            $pending_dates_array = homey_get_booking_pending_days($listing_id);      
            update_post_meta($listing_id, 'reservation_pending_dates', $pending_dates_array); 
          
            //$message_link = homey_thread_link_after_reservation($reservation_id);
            //$email_args = array(
            //    'reservation_detail_url' => reservation_detail_link($reservation_id),
            //    'guest_message' => $guest_message,
            //    'message_link' => $message_link
            //);
            //homey_email_composer( $owner_email, 'new_reservation', $email_args ); 

	    // send a message to broker about creation of the user
	    $message = esc_html__('Lead Request Placed On Property:', 'homey-child' ) . "<br/><br/>"; 
	    $message .= esc_html__('Lead Details In Notification.', 'homey-child' ) . "<br/>"; 
    	    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($listing_id)) . "<br/>";    
    	    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($listing_id)) . "<br/>";
    	    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $postcode) . "<br/>";
    	    $message .= sprintf(esc_html__('Customer Message: %s', 'homey-child'), $guest_message) . "<br/>";
    	    $message .= sprintf(esc_html__('Checkin Date: %s', 'homey-child'), $check_in_date) . "<br/>";    
    	    $message .= sprintf(esc_html__('Checkout Date: %s', 'homey-child'), $check_out_date) . "<br/>";
    	    $message .= sprintf(esc_html__('Guests: %s', 'homey-child'), $guests) . "<br/>";
    	    $message .= esc_html__('Notification Sent To Respective Agent, Will Be In Touch Soon.', 'homey-child') . "<br/><br/>";
    	    $title = esc_html__('New Lead Request Placed On Property. Property Id: '.$listing_id, 'homey-child') . "\n";

	    //users to send emails
	    //$broker = get_userdata($broker_id); $agent = get_userdata($agent_id);
	    $res1 = homey_send_emails($useremail, wp_specialchars_decode( $title ), $message);
	    //$res2 = homey_send_emails($broker->user_email, wp_specialchars_decode( $title ), $message);
	    //$res3 = homey_send_emails($agent->user_email, wp_specialchars_decode( $title ), $message);

            //echo json_encode( array( 'success' => true, 'message' => $local['request_sent'], 'reservid' => $reservation_id ) );
            echo json_encode( array( 'success' => true, 'message' => $local['request_sent'] ) );
            //do_action('homey_create_messages_thread', $guest_message, $reservation_id);
            wp_die();

         } else { // end $check_availability
            echo json_encode( array( 'success' => false, 'message' => $check_message ) );
            wp_die();
         }

} 


/**
 * Lead Modification Based On Rules Created By Brokers 
 * This is a standalone function that will be called via WP-CRON. 
 * This will only work on "pending" reservation posts.
 */
/**
 * Creation of cron schedules 
 * Creation of a scheduled event for generation
 */
//add_filter('cron_schedules','custom_cron_schedules');
//function custom_cron_schedules($schedules){
//    if(!isset($schedules["30min"])){ 
//	$schedules["30min"] = array( 'interval' => 30*60, 'display' => __('Once every 30 minutes'));
//    }
//    if(!isset($schedules["5min"])){ 
//	$schedules["5min"] = array( 'interval' => 5*60, 'display' => __('Once every 5 minutes'));
//    }
//    return $schedules;
//}

//add an action in init for the scheduled event
//add_action( 'init', 'func_schedule_action');
//function func_schedule_action(){
//  add_action('homey_child_allocate_reservation', 'homey_child_allocate_reservation_byrules'); 
//  //creation of schedule event
//  if ( ! wp_next_scheduled( 'homey_child_allocate_reservation' ) ) {
//     wp_schedule_event(time(), '5min', 'homey_child_allocate_reservation');
//  }
//}
//var_dump(wp_next_scheduled( 'homey_child_allocate_reservation'));

//add an action for the scheduled event
//add_action( 'homey_child_allocate_reservation', 'homey_child_allocate_reservation_byrules');
add_action( 'wp_ajax_nopriv_homey_child_allocate_reservation_byrules', 'homey_child_allocate_reservation_byrules' );  
add_action( 'wp_ajax_homey_child_allocate_reservation_byrules', 'homey_child_allocate_reservation_byrules' ); 
function homey_child_allocate_reservation_byrules() {
	global $current_user; $pauthor_id = 1; //default authors to admin
        $current_user = wp_get_current_user();
        $userID = $current_user->ID; $local = homey_get_localization();

	//check for the action of rule allocation being set. 
	$leadAlloc_On = get_option('generate_lead_alloc');
	if(!($leadAlloc_On)){ update_option('generate_lead_alloc', 1); }
	else{ 
	   $msg = "Lead Allocation Is Already In Progress.";
	   echo json_encode( array( 'success' => false, 'msg' => $msg ) );
           wp_die(); 
	}

	//create a list of reservations having status as "pending"
	$args = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'orderby' => array( 'date' =>'DESC' ), 
			'post_status' => 'pending', 'fields' => 'ids' );
	$res_query = new WP_Query($args); //var_dump($res_query); exit;

	//if there are pending reservations
        if($res_query->have_posts()){ 
	  foreach($res_query->posts as $reservPostId){ 
	    //variables required
	    $reservation_id = $reservPostId; 
            $listing_id = get_post_meta($reservation_id, 'reservation_listing_id', true); 
	    $listingtitle = get_the_title($listing_id);
	    $postcode = get_post_meta($listing_id, 'homey_zip', true); 

	    //list all rules for the postcode as in the property.
	    //if no rules exist for a postcode, the earlier rule based on postcode prevails
	    $args = array('post_type' => 'homey_routing', 'posts_per_page' => -1, 'fields' => 'ids');
	    $meta_query[] = array('key'=>'homey_routing_pcode','value'=>$postcode,'compare'=>'='); 
	    $meta_query[] = array('key'=>'homey_routing_status','value'=>'enabled','compare'=>'='); 
	    $meta_query['relation'] = 'AND'; $args['meta_query'] = $meta_query; 
	    $rules_query = new WP_Query($args); //var_dump($reservation_id); var_dump($rules_query); exit;

	    if($rules_query->have_posts()){ $prule_rank = array(); $final_ruleset = array();
	  	// this will shorten the ruleset based on the price range. 
	  	// if property price is within range then it will have more preference than general
	  	foreach($rules_query->posts as $ruleId){
	    		//get meta info from the rule. 
	    		$rule_prange = get_post_meta($ruleId, 'homey_routing_prange', true); 
	    		//check price range for the posts
	    		if($rule_prange != '*'){ 
				$prop_price = get_post_meta($listing_id, 'homey_night_price', true);
				$prange = explode('-', $rule_prange); 
				//if price range match then add to array in towards beginning
				if($prop_price > trim($prange[0]) && $prop_price < trim($prange[1])){ 
		   		  array_unshift($prule_rank, $ruleId);
				} else { continue; }
	    		}else{ //if price is all prices, then append to the array.
				array_push($prule_rank, $ruleId);
	    		}
	  	} //var_dump($reservation_id); var_dump($prule_rank); exit;
	  	//check keywords in the price ranked array 
	  	if(!empty($prule_rank)){ $final_ruleset = $prule_rank;
	    	   foreach($prule_rank as $indx => $ruleId){ 
	      		$rule_keywords = get_post_meta($ruleId, 'homey_routing_keywords', true); 
	      		if(strstr($rule_keywords, ',')){ //there are multile keywords for the rule
				$keywordsArr = explode(',',$rule_keywords); $match = 0;
				foreach($keywordsArr as $keyword){
				    if($keyword == ''){ continue; }
		  		    if(stristr($listingtitle, trim($keyword))){ $match = 1; }
		  		    //if(!strstr($proplisting->post_content, $keyword)){ $match = 0 } else{ $match = 1; } 
				}
				//remove the rule from the array
				if($match == 0){ array_splice($final_ruleset, $indx, 1); }
	      		} elseif($rule_keywords == ''){ //there are no keywords selected for the rule
				//the rule remains unchanged
	      		} else{ //there is only one keyword for the rule
		 		$match = 0; 
		 		if(strstr($listingtitle, trim($rule_keywords))){ $match = 1; }
		 		if($match == 0){ array_splice($final_ruleset, $indx, 1); }
	      		}
	    	    }
	  	 } //var_dump($reservation_id); var_dump($final_ruleset); exit;

		 //get the agent and broker id from the final ruleset 
	  	 $assigned_rule_id = $final_ruleset[0]; $assigned_rule = get_post($assigned_rule_id); 
	  	 $broker_id = $assigned_rule->post_author; 
	  	 $agent_id = get_post_meta($assigned_rule_id, 'homey_routing_agent', true);

	  	 //update post meta variables 
	  	 if(isset($assigned_rule_id) && !empty($assigned_rule_id)){ 
    	    	    update_post_meta($reservation_id, 'homey_brokers', $broker_id);
    	    	    update_post_meta($reservation_id, 'homey_agents', $agent_id);
	    	    update_post_meta($reservation_id, 'homey_assigned_ruleid', $assigned_rule_id); 
		    //update post status
		    $args = array( 'ID' => $reservation_id, 'post_status'   => 'publish', );
		    wp_update_post( $args );
	  	 }

	    }else{
	       $broker_id = get_post_meta($reservation_id, 'homey_brokers', true);
	       $agent_id =  get_post_meta($reservation_id, 'homey_agents', true);
	       //update post status
	       $args = array( 'ID' => $reservation_id, 'post_status'   => 'publish', );
	       wp_update_post( $args );
	    } 

            // send a message to broker and agent about the new lead
	    $message = esc_html__('Lead Request Placed On Property:', 'homey-child' ) . "<br/><br/>"; 
	    $message .= esc_html__('Lead Details In Notification.', 'homey-child' ) . "<br/>"; 
    	    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($listing_id)) . "<br/>";    
    	    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($listing_id)) . "<br/>";
    	    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $postcode) . "<br/>";
    	    $message .= sprintf(esc_html__('Customer Message: %s', 'homey-child'), $guest_message) . "<br/>";
    	    $message .= sprintf(esc_html__('Checkin Date: %s', 'homey-child'), $check_in_date) . "<br/>";    
    	    $message .= sprintf(esc_html__('Checkout Date: %s', 'homey-child'), $check_out_date) . "<br/>";
    	    $message .= sprintf(esc_html__('Guests: %s', 'homey-child'), $guests) . "<br/>";
    	    $message .= esc_html__('Notification Sent To Respective Agent, Will Be In Touch Soon.', 'homey-child') . "<br/><br/>";
    	    $title = esc_html__('New Lead Request Placed On Property. Property Id: '.$listing_id, 'homey-child') . "\n";

	    //users to send emails
	    $broker = get_userdata($broker_id); $agent = get_userdata($agent_id);
	    //$res1 = homey_send_emails($useremail, wp_specialchars_decode( $title ), $message);
	    $res2 = homey_send_emails($broker->user_email, wp_specialchars_decode( $title ), $message);
	    $res3 = homey_send_emails($agent->user_email, wp_specialchars_decode( $title ), $message);
	    //break;
	  } //end $res_query->posts as $postid

	  // reset the option
	  update_option('generate_lead_alloc', 0); $msg = "All Pending Leads Allocated.";
          echo json_encode( array( 'success' => true, 'msg' => $msg ) );
          //do_action('homey_create_messages_thread', $guest_message, $reservation_id);
          wp_die(); 

	}else{ //if there are no pending reservations
	    update_option('generate_lead_alloc', 0); $msg = "No Pending Leads Exist.";
            echo json_encode( array( 'success' => false, 'msg' => $msg ) );
            wp_die(); 
        }
} 

