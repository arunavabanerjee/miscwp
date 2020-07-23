<?php 
/**
 * Actions for Agents From Agent Dashboard
 */

/** Action for change selected houses in agent and broker dashboard 
 * Mainly done by agent from agent dashboard.
 */
add_action( 'wp_ajax_nopriv_homey_child_fetch_selected_houses', 'homey_child_fetch_selected_houses' );  
add_action( 'wp_ajax_homey_child_fetch_selected_houses', 'homey_child_fetch_selected_houses' );  
function homey_child_fetch_selected_houses() { 
    global $post; $html='';
    $reservation_Id = $_POST['reservationid'];
    $selected_props = get_post_meta($reservation_Id, 'reservation_selections', true);
    //----------------------------------------------
    // generate query for selected houses listings
    //----------------------------------------------
    $no_of_listing   =  '9';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $selectedPropsArray = explode(',',$selected_props);
    $selected_qargs = array(
       'post_type'        =>  'listing',
       //'author'           =>  $userID,
       'paged'             => $paged,
       'posts_per_page'    => $no_of_listing,
       'post_status'      =>  'publish', 
       'post__in' => $selectedPropsArray
    );
    $selected_qargs = homey_listing_sort ($selected_qargs);
    $selected_props_qry = new WP_Query($selected_qargs);
    $meta_query = []; 

    if(isset($selected_props_qry)){ 
      if($selected_props_qry->have_posts()): 
	$html .= '<div class="table-block dashboard-listing-table dashboard-table">';
        $html .= '<table class="table table-hover"><tbody id="selected_houses_listings">';
	while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	  $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	  $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
          $html .= '<a href="'.get_the_permalink($post->ID).'">';
          if( has_post_thumbnail( $post->ID ) ) {
            $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
          }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
          $html .= '</a></td>';
          $html .= '<td data-label="'.esc_html__('title-address').'">';
	  $html .= '<div style="float:left;width:100%">';
          $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
          if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	  $html .= '</div>';
          //$html .= '<div style="float:left;width:100%">';
          //$html .= '<button type="button" class="btn btn-full-width accepted" 
	  //		  style="font-size:10px;border:1px solid #d0c8c8;background:#85c341;width:16%;float:left;line-height:25px;margin-right:3px;">';
	  //$html .= esc_html__('Accepted').'</button>';
	  //$html .= '<button type="button" class="btn btn-full-width denied" 
	  //		  style="font-size:10px;border:1px solid #d0c8c8;background:#f15e75;width:16%;float:left;line-height:25px;">';
	  //$html .= esc_html__('Denied').'</button>';
	  //$html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	  //$html .= '</div>';
	  $html .= '<div class="text-center text-small" style="font-size:10px;float:left;"><i class="fa fa-info-circle"></i>';
	  $html .= esc_html__(' Confirm/Deny Will Be Mentioned By Host ', 'homey-child').'</div></td></tr>';
	endwhile;
	$html .= '</tbody></table>';
	$html .= '</div>';
	$html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
     else:
        $html .= '<div class="">'; 
        $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	$html .= '</div>';      
     endif; 
   } 
   wp_reset_postdata();
   echo $html;
   wp_die();
}

/** Action for change accepted and declined response from client 
 *  Mainly performed by Agent from Agent Dahboard
 */
add_action( 'wp_ajax_nopriv_homey_child_reserv_modification_via_client_response', 'homey_child_reserv_modification_via_client_response' );  
add_action( 'wp_ajax_homey_child_reserv_modification_via_client_response', 'homey_child_reserv_modification_via_client_response' );  
function homey_child_reserv_modification_via_client_response() { 
    global $post, $current_user; $html='';
    $reservation_Id = $_POST['reservationid'];
    $listing_Id = $_POST['listingid'];
    $reserv_listingId = get_post_meta($reservation_Id, 'reservation_listing_id', true);
    $postcode = get_post_meta($reserv_listingId, 'homey_zip', true); 
    $current_user = wp_get_current_user();
    $userID = $current_user->ID; $currentuser_role = $current_user->roles[0]; 

    //-------------------------------------------------------------------------
    // generate query for available houses in the same postcode based on date
    //-------------------------------------------------------------------------
    /*$no_of_listing   =  '9'; $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
     $available_qargs = array(
       'post_type'      => 'listing',
       'paged'           => $paged,
       'posts_per_page'  => $no_of_listing,
       'post_status'     => 'publish', 
       'fields'		=> 'ids',
     );
     $meta_query[] = array('key' => 'host_approval_till', 'value' => date('Y-m-d'),'compare' => '=>', 'type' => 'DATE' );
     $meta_query[] = array('key' => 'homey_zip','value' => $postcode,'compare' => '=');
     $meta_query['relation'] = 'AND'; $available_qargs['meta_query'] = $meta_query; 
     $available_qargs = homey_listing_sort ($available_qargs);
     $available_props_qry = new WP_Query($available_qargs); $meta_query = []; 
     //var_dump($available_props_qry); exit;

     //if available query has posts, then approved reservations is updated.
     if(!empty($available_props_qry->posts)){
	//update approved reservation section 
    	$approved_props = get_post_meta($reservation_Id, 'approved_reservations', true); 
	if(empty($approved_props)){ $listings = ''; 
	  foreach($available_props_qry->posts as $propertyId){ $listings .= $propertyId.','; }
      	  update_post_meta($reservation_Id, 'approved_reservations', $listings);
        } else{
	  $listings_array = explode(',',$approved_props);
	  foreach($available_props_qry->posts as $propertyId){
	    if(!in_array($propertyId,$listings_array)){ $approved_props .= $propertyId.','; }
	  }
      	  update_post_meta($reservation_Id, 'approved_reservations', $approved_props);
	}
     }*/

     // accept the response from client and create the post meta values. 
     if($_POST['type'] == 'accepted'){
	//update the postmeta values for the reservation
    	$approved_props = get_post_meta($reservation_Id, 'approved_reservations', true); 
    	if(empty($approved_props)){
      	   update_post_meta($reservation_Id, 'approved_reservations', $listing_Id);
    	}else{
	   $listings_array = explode(',',$approved_props);
	   if(!in_array($listing_Id, $listings_array)){
	     $addlistings = $approved_props.','.$listing_Id;
             update_post_meta($reservation_Id, 'approved_reservations', $addlistings);
	   }
        }

	//set a timetag in the listing when host approves booking 
        //this approval to remain in db for 7 days  
	if($currentuser_role == 'homey_host'){
      	  update_post_meta($listing_Id, 'host_approval_till', date('Y-m-d', strtotime("+7 days")));
	}

	if($currentuser_role == 'homey_agents'){
    	  //also the selected properties will be assigned.
    	  $selected_props = get_post_meta($reservation_Id, 'reservation_selections', true); 
    	  if(empty($selected_props)){
      	    update_post_meta($reservation_Id, 'reservation_selections', $listing_Id); 
    	  }else{
	    $listings_array = explode(',',$selected_props);
	    if(!in_array($listing_Id, $listings_array)){
	      $addlistings = $selected_props.','.$listing_Id;
      	      update_post_meta($reservation_Id, 'reservation_selections', $addlistings);
	    }
	  }
    	  update_post_meta($reservation_Id, 'reservation_status', 'in_progress'); 
	}
			
	//create the html to replace the listings
    	$selected_props = get_post_meta($reservation_Id, 'approved_reservations', true);
    	//----------------------------------------------
    	// generate query for selected houses listings
    	//----------------------------------------------
    	$no_of_listing   =  '12';
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$selectedPropsArray = explode(',',$selected_props);
    	$selected_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $selectedPropsArray
    	);
    	$selected_qargs = homey_listing_sort ($selected_qargs);
    	$selected_props_qry = new WP_Query($selected_qargs); $meta_query = []; 

    	if(isset($selected_props_qry)){ 
      	if($selected_props_qry->have_posts()): 
	   $html .= '<div class="table-block dashboard-listing-table dashboard-table">';
           $html .= '<table class="table table-hover"><tbody id="approval_houses_listings">';
	   while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	      $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	      $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
              $html .= '<a href="'.get_the_permalink($post->ID).'">';
              if( has_post_thumbnail( $post->ID ) ) {
                 $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
              }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
              $html .= '</a></td>';
              $html .= '<td data-label="'.esc_html__('title-address').'">';
	      $html .= '<div style="float:left;width:75%">';
              $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
              if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	      $html .= '</div>';
	      $html .= '<div style="float:left;width:25%">';
	      $html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	      //$html .= '<button type="button" class="btn btn-full-width" style="font-size:10px;border: 1px solid #d0c8c8;">';
	      //$html .= esc_html__('Notify Customer').'</button>';
	      //$html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>';
	      //$html .= esc_html__('Send To Customer', 'homey-child').'</div></div>';
	      $html .= '<button type="button" name="agent-broker-confirmation" class="btn btn-full-width" 
						style="font-size:9px;border:1px solid #d0c8c8;background-color:#dfdcdc;">';
	      $html .= esc_html__('Customer To Confirm').'</button>';
	      $html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>'; 
	      $html .= esc_html__('Customer To Confirm Lead', 'homey-child').'</div>';
	      $html .= '</td></tr>';
	   endwhile;
	   $html .= '</tbody></table>';
	   $html .= '</div>';
	   $html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
        else:
           $html .= '<div class="">'; 
           $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	   $html .= '</div>';      
        endif; 
        } 
        wp_reset_postdata();
        echo $html;
    }

    // denial response from client
    if($_POST['type'] == 'denied'){
	//update the postmeta values for the reservation
    	$denied_props = get_post_meta($reservation_Id, 'denied_reservations', true); 
    	if(empty($denied_props)){
      	   update_post_meta($reservation_Id, 'denied_reservations', $listing_Id);
    	}else{
      	   $addlistings = $denied_props.','.$listing_Id;
           update_post_meta($reservation_Id, 'denied_reservations', $addlistings);
        }

	//create the html to replace the listings
    	$selected_props = get_post_meta($reservation_Id, 'denied_reservations', true);
    	//----------------------------------------------
    	// generate query for selected houses listings
    	//----------------------------------------------
    	$no_of_listing   =  '9';
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$selectedPropsArray = explode(',',$selected_props);
    	$selected_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $selectedPropsArray
    	);
    	$selected_qargs = homey_listing_sort ($selected_qargs);
    	$selected_props_qry = new WP_Query($selected_qargs);
    	$meta_query = []; 

    	if(isset($selected_props_qry)){ 
      	if($selected_props_qry->have_posts()): 
	   $html .= '<div class="table-block dashboard-listing-table dashboard-table">';
           $html .= '<table class="table table-hover"><tbody id="denial_houses_listings">';
	   while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	      $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	      $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
              $html .= '<a href="'.get_the_permalink($post->ID).'">';
              if( has_post_thumbnail( $post->ID ) ) {
                 $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
              }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
              $html .= '</a></td>';
              $html .= '<td data-label="'.esc_html__('title-address').'">';
	      $html .= '<div style="float:left;width:100%">';
              $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
              if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	      $html .= '</div>';
              $html .= '<div style="float:left;width:100%">';
	      $html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	      $html .= '</div>';
	      $html .= '</div></td></tr>';
	   endwhile;
	   $html .= '</tbody></table>';
	   $html .= '</div>';
	   $html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
        else:
           $html .= '<div class="">'; 
           $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	   $html .= '</div>';      
        endif; 
        } 
        wp_reset_postdata();
        echo $html;
    }
    wp_die();
}

/** Action for submission of add new registered user from agent dashboard 
 *  Mainly performed by Agent from Agent Dashboard
 */
add_action( 'wp_ajax_nopriv_homey_child_agent_register_customer', 'homey_child_agent_register_customer' );  
add_action( 'wp_ajax_homey_child_agent_register_customer', 'homey_child_agent_register_customer' );  
function homey_child_agent_register_customer() { 
    $response = ''; $customeruname = ''; 
    //generate the variables
    $customername = trim(urldecode($_POST['customername']));
    $customeremail = trim(urldecode($_POST['customeremail']));
    $customerphone = trim(urldecode($_POST['customerphone']));
    $reservationid = trim(urldecode($_POST['reservationid']));   
    //var_dump($customername); var_dump($customeremail); 
    //var_dump($customerphone); var_dump($reservationid); exit;
    $password = wp_generate_password(12, true ); 

    if(strpos($customername, ' ') !== false){ 
	$namearray = explode(' ', $customername); 
	$customeruname = strtolower($namearray[0]).strtolower($namearray[1]); 
	$customerfname = $namearray[0];
	$customerlname = '';
	foreach($namearray as $indx => $name){
	   if($indx > 0){ $customerlname .= $name; }
	}
    } else { 
	$customeruname = strtolower($customername); 
	$customerfname = $namearray[0];
	$customerlname = '';
    }
    //var_dump($customeruname); exit;

    // check if user exists in the database
    if( (null == username_exists( $customeruname )) && (null == email_exists( $customeremail )) ){ 
      //create user with all the details
      $userdata = array(
	'user_pass'	=> $password, 
	'user_login'	=> $customeruname,
        'user_nicename' => $customername, 
        'user_email'	=> $customeremail,
        'display_name'	=> $customername,
        'nickname'  	=> $customeruname,
	'first_name' 	=> $customerfname, 
	'last_name' 	=> $customerlname, 
	'user_registered' => date('Y-m-d H:i:s'), 
	'show_admin_bar_front' => false, 
	'role' => 'homey_renter',
      );
      $user_id = wp_insert_user( $userdata ); 

      //update user meta with the address details.
      //update_user_meta( $user_id, 'address', $agent_address[1] ); 
      //update_user_meta( $user_id, 'city', $agent_city[1] ); 
      //update_user_meta( $user_id, 'postalcode', $agent_pcode[1] ); 
      //update_user_meta( $user_id, 'state', $agent_state[1] );

      //update listing author using user id
      //$arg = array( 'ID' => $reservationid, 'post_author' => $user_id, );
      //wp_update_post( $arg );
      update_post_meta( $reservationid, 'listing_renter', $user_id );
      update_post_meta( $reservationid, 'homey_lead_type', 'client_lead' );

      //send email to admin and user after creation of the user
      homey_wp_new_user_notification($user_id, $password);

      //send a message to broker about creation of the user
      //$message = esc_html__('A new agent has been set up:', 'homey-child' ) . "\r\n\r\n";
      //$message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username[1]) . "\r\n\r\n";
      //$message .= sprintf(esc_html__('Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
      //$message .= esc_html__('Agent Will Now Be Able To Login Using The Password Received.', 'homey-child') . "\r\n\r\n";
      //$title = esc_html__('Agent Setup Message.', 'homey-child') . "\n";
      //homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

      $response = 'User Created And Notified!!!';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }else{
      $response = 'Error: User Already Exists In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }
}

/** Action for change reservation status from customer dashboard on clicking confirm button 
 *  Mainly viwed by agent From Agent Dashboard.
 */
add_action( 'wp_ajax_nopriv_homey_child_confirm_modification_via_customer_response', 'homey_child_confirm_modification_via_customer_response' );  
add_action( 'wp_ajax_homey_child_confirm_modification_via_customer_response', 'homey_child_confirm_modification_via_customer_response' );  
function homey_child_confirm_modification_via_customer_response() { 
    $listing_Id = trim($_POST['listingid']);
    $reservation_Id = trim($_POST['reservationid']);

    //update the postmeta with the following
    update_post_meta($reservation_Id, 'reservation_confirmed', $listing_Id);
    update_post_meta($reservation_Id, 'reservation_status', 'confirmed');

    // send a message to broker about creation of the user
    //$message = esc_html__('Availability Check On Property:', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "\r\n\r\n";
    //$message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Availability Check On Property. Property Id: '.$listingId, 'homey-child') . "\n";
    //$result = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);
    //if($result){ 
    //  $response = 'Email Sent To Host.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //} else{ 
    //  $response = 'Email To Host Could Not Be Sent.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //}

    $response = 'Confirmed, Awaiting Agent Confirmation!!!';
    echo json_encode( array('success' => true, 'msg' => $response) ); 
    wp_die();
}


/** Action for change final reservation status from agent dashboard after response from Renter 
 *  Mainly performed by Agent From Agent Dashboard.
 */
add_action( 'wp_ajax_nopriv_homey_child_final_confirmation_agent_response', 'homey_child_final_confirmation_agent_response' );  
add_action( 'wp_ajax_homey_child_final_confirmation_agent_response', 'homey_child_final_confirmation_agent_response' );  
function homey_child_final_confirmation_agent_response() { 
    $listing_Id = trim($_POST['listingid']);
    $reservation_Id = trim($_POST['reservationid']);

    //update the postmeta with the following
    //update_post_meta($reservation_Id, 'reservation_confirmed', $listing_Id);
    update_post_meta($reservation_Id, 'reservation_status', 'booked'); 
    update_post_meta($reservation_Id, 'reservation_booked', $listing_Id); 

    //get required variables for processing
    $renterId = get_post_meta( $reservation_Id, 'listing_renter', true ); 
    $renter = get_userdata($renterId);
    $brokerId = get_post_meta( $reservation_Id, 'homey_brokers', true ); 
    $broker = get_userdata($brokerId);
    $agentId = get_post_meta( $reservation_Id, 'homey_agents', true );  
    $agent = get_userdata($agentId);   
    //$homelistingpost = get_post($listing_Id);
    $propertyPCode = get_post_meta($listing_Id, 'homey_zip', true); 
    $agent_name = $agent->display_name; $agent_email = $agent->user_email; 
    $renter_name = $renter->display_name; $renter_email = $renter->user_email;
    $broker_name = $broker->display_name; $broker_email = $broker->user_email;

    // send a message about the booking to the related persons
    $message = esc_html__('The Property Has Been Booked. Please Find the Details Attached.', 'homey-child' ) . "<br/><br/>"; 
    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($listing_Id)) . "<br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($listing_Id)) . "<br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode) . "<br/><br/>";
    $message .= esc_html__('Agent Details (Booked By):', 'homey-child' ) . "<br/>"; 
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name) . "<br/>";    
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email) . "<br/><br/>";
    $message .= esc_html__('Customer Details (Booked For):', 'homey-child' ) . "<br/>"; 
    $message .= sprintf(esc_html__('Customer Name: %s', 'homey-child'), $renter_name) . "<br/>";    
    $message .= sprintf(esc_html__('Customer Email: %s', 'homey-child'), $renter_email) . "<br/><br/>";
    $title = esc_html__('Booking Confirmation On Property. Property Id: '.$listingId, 'homey-child') . "\n";

    //send emails to the related persons
    $res1 = homey_send_emails($renter_email, wp_specialchars_decode( $title ), $message);
    $res2 = homey_send_emails($agent_email, wp_specialchars_decode( $title ), $message);
    $res3 = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

    if($res1 && $res2 && $res3){ 
        $response = 'Booking Confirmed. Emails Sent.';
        echo json_encode( array('success' => true, 'msg' => $response) ); 
    } else{ 
       $response = 'Booking Confirmed. All Emails Could Not Be Sent.';
       echo json_encode( array('success' => true, 'msg' => $response) ); 
    }
    wp_die();
}

/** Action for change of renter to host from Renter Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_renter_to_host_conversion', 'homey_child_renter_to_host_conversion' );  
add_action( 'wp_ajax_homey_child_renter_to_host_conversion', 'homey_child_renter_to_host_conversion' );  
function homey_child_renter_to_host_conversion() { 
    $response = ''; 
    //get the variables from the post data
    $renterId = $_POST['renterid'];
 
    //check if user exists in DB
    $wp_user = get_user_by('ID', $renterId); 
    if(!$wp_user){
      $response = 'Error: User Does Not Exist In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();

    } else {
      //update user to host
      $userdata = array(
	'ID'   => $renterId, 
	'role' => 'homey_host',
      );
      $user_id = wp_update_user( $userdata ); 

      //send a message to user about change of role
      $message = esc_html__('User Has Been Changed From Renter to Host.:', 'homey-child' ) . "<br/><br/>";
      $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $wp_user->display_name) . "<br/>";
      $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $wp_user->user_email) . "<br/>";
      $message .= esc_html__('User Will Now Be Able To Login Using Same Username and Password.', 'homey-child') . "<br/><br/>";
      $title = esc_html__('User Has Been Changed From Renter to Host.', 'homey-child') . "\n";
      homey_send_emails($wp_user->user_email, wp_specialchars_decode( $title ), $message);

      $response = 'User Updated And Notified!!!. Please Re-Login To View The Modified Dashboard.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }
}


