<?php 

/**
 * Actions From The Broker Dashboard
 */

/** Action for submission of add new agent from brokers */
add_action( 'wp_ajax_nopriv_homey_child_add_new_agent_submission', 'homey_child_add_new_agent_submission' );  
add_action( 'wp_ajax_homey_child_add_new_agent_submission', 'homey_child_add_new_agent_submission' );  
function homey_child_add_new_agent_submission() { 
    $response = ''; 
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); exit;

    //check security
    $nonce = explode('=',$form_details[1]);
    if ( ! wp_verify_nonce( $nonce[1], 'homey-new-agent-create' ) ) {
       echo json_encode( array( 'success' => false, 'msg' => $local['security_check_text'] ) );
       wp_die();
    }

    //generate the variables
    $broker_email = explode('=',$form_details[0]);
    $username = explode('=',$form_details[3]); $agent_email = explode('=',$form_details[4]);
    $password = wp_generate_password(12, true ); 
    $firstname = explode('=',$form_details[5]); $lastname = explode('=',$form_details[6]);
    $agent_address = explode('=',$form_details[7]); $agent_city = explode('=',$form_details[8]);
    $agent_state = explode('=',$form_details[9]); $agent_pcode = explode('=',$form_details[10]);

    $wp_user = get_user_by('email', trim($broker_email)); 
    // check if user exists in the database
    if( !username_exists( $username[1] )){
       if( email_exists($agent_email[1]) ){
         $response = 'Error: Email Already Exists In Database.';
         echo json_encode( array('success' => false, 'msg' => $response) );
         wp_die();
       }
 
      //create user with all the details
      $userdata = array(
	'user_pass'	=> $password, 
	'user_login'	=> $username[1],
        'user_nicename' => $firstname[1].' '.$lastname[1], 
        'user_email'	=> $agent_email[1],
        'display_name'	=> $firstname[1].' '.$lastname[1],
        'nickname'  	=> $username[1],
	'first_name' 	=> $firstname[1], 
	'last_name' 	=> $lastname[1], 
	'user_registered' => date('Y-m-d H:i:s'), 
	'show_admin_bar_front' => false, 
	'role' => 'homey_agents',
      );
      $user_id = wp_insert_user( $userdata );

      //update user meta with the address details.
      update_user_meta( $user_id, 'address', $agent_address[1] ); 
      update_user_meta( $user_id, 'city', $agent_city[1] ); 
      update_user_meta( $user_id, 'postalcode', $agent_pcode[1] ); 
      update_user_meta( $user_id, 'state', $agent_state[1] );
      update_user_meta( $user_id, 'created_by', $wp_user->ID );

      //send email to admin and user after creation of the user
      homey_wp_new_user_notification($user_id, $password);

      // send a message to broker about creation of the user
      $message = esc_html__('A new agent has been set up:', 'homey-child' ) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username[1]) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
      $message .= esc_html__('Agent Will Now Be Able To Login Using The Password Received.', 'homey-child') . "\r\n\r\n";
      $title = esc_html__('Agent Setup Message.', 'homey-child') . "\n";
      homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

      $response = 'Agent Has Been Created. Email Sent Successfully!';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();

    }else{
      $response = 'Error: Username Already Exists In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }
}


/** Action for submission of add new client from brokers dashboard */
add_action( 'wp_ajax_nopriv_homey_child_add_new_client_submission', 'homey_child_add_new_client_submission' );  
add_action( 'wp_ajax_homey_child_add_new_client_submission', 'homey_child_add_new_client_submission' );  
function homey_child_add_new_client_submission() { 
    $response = ''; 
    $local = homey_get_localization();
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); exit;

    //check security
    $nonce = explode('=',$form_details[1]);
    if ( ! wp_verify_nonce( $nonce[1], 'homey-new-client-create' ) ) {
       echo json_encode( array( 'success' => false, 'msg' => $local['security_check_text'] ) );
       wp_die();
    }

    //generate the variables
    $broker_email = explode('=',$form_details[0]);
    $username = explode('=',$form_details[3]); $client_email = explode('=',$form_details[4]);
    $password = wp_generate_password(12, true ); 
    $firstname = explode('=',$form_details[5]); $lastname = explode('=',$form_details[6]);
    $client_phone = explode('=',$form_details[7]); $client_age = explode('=',$form_details[8]);
    $client_occup = explode('=',$form_details[9]); $client_gender = explode('=',$form_details[10]);
    $client_acctn_type = explode('=',$form_details[11]);
    // address variables 
    $client_address = explode('=',$form_details[12]); $client_city = explode('=',$form_details[13]);
    $client_state = explode('=',$form_details[14]); $client_pcode = explode('=',$form_details[15]); 

    if($client_acctn_type[1] == 'host'){ $role = 'homey_host'; }
    else{ $role = 'homey_renter'; }

    $wp_user = get_user_by('email', trim($broker_email[1])); 
    // check if user exists in the database
    if( !username_exists($username[1]) ){ 
       if( email_exists($client_email[1]) ){
         $response = 'Error: Email Already Exists In Database.';
         echo json_encode( array('success' => false, 'msg' => $response) );
         wp_die();
       }

      //create user with all the details
      $userdata = array(
	'user_pass'	=> $password, 
	'user_login'	=> $username[1],
        'user_nicename' => $firstname[1].' '.$lastname[1], 
        'user_email'	=> $client_email[1],
        'display_name'	=> $firstname[1].' '.$lastname[1],
        'nickname'  	=> $username[1],
	'first_name' 	=> $firstname[1], 
	'last_name' 	=> $lastname[1], 
	'user_registered' => date('Y-m-d H:i:s'), 
	'show_admin_bar_front' => false, 
	'role' => $role,
      );
      $user_id = wp_insert_user( $userdata ); 

      //update user meta with the address details.
      update_user_meta( $user_id, 'address', $client_address[1] ); 
      update_user_meta( $user_id, 'city', $client_city[1] ); 
      update_user_meta( $user_id, 'postalcode', $client_pcode[1] ); 
      update_user_meta( $user_id, 'state', $client_state[1] ); 
      update_user_meta( $user_id, 'age', $client_age[1] );
      update_user_meta( $user_id, 'phone', $client_phone[1] );
      update_user_meta( $user_id, 'occupation', $client_occup[1] );
      update_user_meta( $user_id, 'gender', $client_gender[1] );
      update_user_meta( $user_id, 'created_by', $wp_user->ID );

      //send email to admin and user after creation of the user
      homey_wp_new_user_notification($user_id, $password);

      // send a message to broker about creation of the user
      $message = esc_html__('New user has been set up:', 'homey-child' ) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username[1]) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $client_email[1]) . "\r\n\r\n";
      $message .= esc_html__('User Will Now Be Able To Login Using The Password Received.', 'homey-child') . "\r\n\r\n";
      $title = esc_html__('User Setup Message.', 'homey-child') . "\n";
      homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

      $response = 'User Has Been Created. Email Sent Successfully!';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();

    }else{
      $response = 'Error: Username Already Exists In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }
}


/**
 * Agent Selection For Brokers
 */
/** Action for submission of agent selection from brokers for leads */
/*add_action( 'wp_ajax_nopriv_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
add_action( 'wp_ajax_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
function homey_child_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $listingpost = get_post($details[2]); //var_dump($listingpost); 
    // update post meta
    update_post_meta($listingpost->ID, 'homey_agents', $details[0]); 

    //get userdata from userid
    // agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new lead has been added to profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "\r\n\r\n";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $title = esc_html__('Notification For A Lead Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    // broker will receive an email.
    $broker_info = get_userdata($details[1]);
    $brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    $message = esc_html__('A new lead has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    $title = esc_html__('Notification Of A Lead Assignment To An Agent.', 'homey-child') . "\n";
    homey_send_emails($brokeremail, $title, $message);

    $response = $userdisplayname;
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}*/

/** Action for submission of agent selection from brokers for properties */
add_action( 'wp_ajax_nopriv_homey_child_property_agent_allocation', 'homey_child_property_agent_allocation' );  
add_action( 'wp_ajax_homey_child_property_agent_allocation', 'homey_child_property_agent_allocation' );  
function homey_child_property_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $listingpost = get_post($details[2]); //var_dump($listingpost); 
    // update post meta
    update_post_meta($listingpost->ID, 'homey_assignedagent', $details[0]); 

    //get userdata from userid
    // agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new property has been added to profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "\r\n\r\n";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $title = esc_html__('Notification For A Property Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    // broker will receive an email.
    $broker_info = get_userdata($details[1]);
    $brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    $message = esc_html__('A new property has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    $title = esc_html__('Notification Of A Property Assignment To An Agent.', 'homey-child') . "\n";
    homey_send_emails($brokeremail, $title, $message);

    $response = $userdisplayname;
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}


/** Action for change of property type from brokers for properties */
add_action( 'wp_ajax_nopriv_homey_child_property_change_type', 'homey_child_property_change_type' );  
add_action( 'wp_ajax_homey_child_property_change_type', 'homey_child_property_change_type' );  
function homey_child_property_change_type() { 
    $response = ''; global $current_user;  
    $current_user = wp_get_current_user();
    $postid = $_POST['data'];  
    $posttype = $_POST['type']; 
    //var_dump($postid); var_dump($posttype); exit;

    //get post varibles 
    $listingpost = get_post($postid); //var_dump($listingpost); 
    $postcode = get_post_meta($postid, 'homey_zip', true);
    $authorid = $listingpost->post_author; $author = get_userdata($authorid); 
     
    // update post meta
    update_post_meta($postid, 'homey_typeofpropertylisting', $posttype); 

    //get userdata from userid
    $broker_info = get_userdata($current_user->ID);
    $useremail = $broker_info->user_email; 
    $message = esc_html__('Property Type Has Changed.', 'homey-child' ) . "<br/><br/>";
    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($postid)) . "<br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($postid)) . "<br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $postcode) . "<br/>";
    $message .= sprintf(esc_html__('Property Type: %s', 'homey-child'), strtoupper($posttype)) . "<br/>";
    $title = esc_html__('Notification For Property Type Modification.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message); 
    homey_send_emails($author->user_email, $title, $message);

    $response .= 'Property Type:'.strtoupper($posttype);
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}


/** Action for approval of property from brokers for properties */
add_action( 'wp_ajax_nopriv_homey_child_broker_property_approval', 'homey_child_broker_property_approval' );  
add_action( 'wp_ajax_homey_child_broker_property_approval', 'homey_child_broker_property_approval' );  
function homey_child_broker_property_approval() { 
    $response = ''; global $current_user;  
    $current_user = wp_get_current_user();
    $postid = $_POST['data']; //var_dump($postid); exit;

    //get post varibles 
    $listingpost = get_post($postid); //var_dump($listingpost); 
    $postcode = get_post_meta($postid, 'homey_zip', true);
    $authorid = $listingpost->post_author; $author = get_userdata($authorid); 
    $updt_post = array( 'ID' => $postid, 'post_status' => 'publish', );
    $result = wp_update_post( $updt_post, true );

    // if there is any error then display error message. 
    if (is_wp_error($result)) {
	$errors = $result->get_error_messages();
    	foreach ($errors as $error) { $response .= $error; }
        echo json_encode( array('success' => false, 'msg' => $response) );
        wp_die(); 
    }

    //get userdata from userid
    $broker_info = get_userdata($current_user->ID);
    $useremail = $broker_info->user_email; 
    $message = esc_html__('Property Has Been Approved.', 'homey-child' ) . "<br/><br/>";
    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($postid)) . "<br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($postid)) . "<br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $postcode) . "<br/>";
    $title = esc_html__('Notification For Property Approval.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message); 
    homey_send_emails($author->user_email, $title, $message);

    $response .= 'Property Approved, Host Notified';
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}


/** Action for change reservation status from Broker dashboard 
 *  This action overrides all status set by agent
 */
add_action( 'wp_ajax_nopriv_homey_child_change_reservation_status', 'homey_child_change_reservation_status' );  
add_action( 'wp_ajax_homey_child_change_reservation_status', 'homey_child_change_reservation_status' );  
function homey_child_change_reservation_status() { 
    $status_val = $_POST['statusval']; $reservation_Id = $_POST['reservationid'];

    //after an email is sent to the client, status would be modified to be in progress.
    update_post_meta($reservation_Id, 'reservation_status', $status_val);

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

    $response = 'Status Changed For The Lead!!!';
    echo json_encode( array('success' => true, 'message' => $response) ); 
    wp_die();
}

/** Action for submission of agent deletion from broker dashboard */
add_action( 'wp_ajax_nopriv_homey_child_broker_dashboard_agent_deletion', 'homey_child_broker_dashboard_agent_deletion' );  
add_action( 'wp_ajax_homey_child_broker_dashboard_agent_deletion', 'homey_child_broker_dashboard_agent_deletion' );  
function homey_child_broker_dashboard_agent_deletion() { 
    $response = ''; 
    //get the variables from the post data
    $agentId = $_POST['agentid']; $brokerId = $_POST['brokerid']; 

    $wp_user = get_user_by('ID', $agentId); 
    if(!$wp_user){
      $response = 'Error: User Does Not Exist In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();

    } else {

      //list all listings for properties and update to none
      $largs = array( 'post_type' => 'listing', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
      $lmeta_query[] = array( 'key' => 'homey_assignedagent', 'value' => $agentId, 'compare' => '=' );
      $largs['meta_query'] = $lmeta_query; $lqry_res = new WP_Query($largs); 
      if ( $lqry_res->have_posts() ){
        foreach($lqry_res->posts as $prop){ 
	  update_post_meta($prop->ID, 'homey_assignedagent', 0);					    
        }
      } 
      wp_reset_postdata(); $lmeta_query=[]; 

      //list all listings for reservations and update to none
      $rargs = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
      $rmeta_query[] = array( 'key' => 'homey_agents', 'value' => $agentId, 'compare' => '=' );
      $rargs['meta_query'] = $rmeta_query; $rqry_res = new WP_Query($rargs); //var_dump($rqry_res);
      if ( $rqry_res->have_posts() ){
        foreach($rqry_res->posts as $res1){ 
	  update_post_meta($res1->ID, 'homey_agents', 0);	
        }
      }
      wp_reset_postdata(); $rmeta_query=[];
 
      $result = wp_delete_user( $agentId, null );
      //send a message to user about change of role
      //$message = esc_html__('User Has Been Changed From Renter to Host.:', 'homey-child' ) . "<br/><br/>";
      //$message .= sprintf(esc_html__('Username: %s', 'homey-child'), $wp_user->display_name) . "<br/>";
      //$message .= sprintf(esc_html__('Email: %s', 'homey-child'), $wp_user->user_email) . "<br/>";
      //$message .= esc_html__('User Will Now Be Able To Login Using Same Username and Password.', 'homey-child') . "<br/><br/>";
      //$title = esc_html__('User Has Been Changed From Renter to Host.', 'homey-child') . "\n";
      //homey_send_emails($wp_user->user_email, wp_specialchars_decode( $title ), $message);

      $response = 'Agent Deleted And All Assignment Nullified.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }
}

/** Action for new lead when rejected by agent from Broker Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_new_lead_agent_denial', 'homey_child_new_lead_agent_denial' );  
add_action( 'wp_ajax_homey_child_new_lead_agent_denial', 'homey_child_new_lead_agent_denial' );  
function homey_child_new_lead_agent_denial() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    //get the variables from the post data
    $reservationId = $_POST['reservationid']; 
    //get post meta of the reservation id 
    $agentId = get_post_meta($reservationId, 'homey_agents', true);
    $cstatus = get_post_meta($reservationId, 'reservation_status', true);

    //if the status is not as "under_review", then rejection not possible. 
    if($cstatus != 'under_review'){
      $response = 'Error: Lead Is Not New Lead, Rejection Not Possible.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();	
    } elseif($current_user->ID != $agentId){ //if another user pressed reject, then discard
      $response = 'Error: Lead Is Assigned To Another Agent, Cannot Reject.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();	
    } else{
      //agent has rejected the lead change status
      update_post_meta($reservationId, 'reservation_status_agent', 'rejected');
      update_post_meta($reservationId, 'homey_agents', 0);

      $routedAgentIds = get_post_meta($reservationId, 'homey_agents_routed', true);
      if(empty($routedAgentIds)){
	update_post_meta($reservationId, 'homey_agents_routed', $agentId);
      }else{
	$agentdenied = $routedAgentIds.','.$agentId;
        update_post_meta($reservationId, 'homey_agents_routed', $agentdenied);
      }

      // genearte a comment for rejection of the reservation. 
      $agent = get_user_by( 'ID', $agentId);
      $message = 'Lead Rejected';
      $args = array(
	'comment_agent' => 'lead reject button',
	'comment_approved' => 1, 'comment_author' => $agent->display_name,
	'comment_author_email' => $agent->user_email, 'comment_author_IP' => '',
	'comment_author_url' => '', 'comment_content' => $message, 
	'comment_date' => date('Y-m-d H:i:s'),
	'comment_date_gmt' => date('Y-m-d H:i:s'),
	'comment_karma' => 0, 'comment_parent' => 0, 
	'comment_post_ID' => $reservationId, 
	'comment_type' => 'lead_rejection',
	'user_id' => $agent->ID,
	'comment_meta' => array(
	   '_wxr_import_user' => $agent->ID,
	   'reservation_id' => $rservation_id,
	),
      ); 
      $commentid = wp_insert_comment($args);

      $response = 'New Lead Rejected.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
   }
}

/** Action for new lead when accepted by agent From Broker Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_new_lead_agent_accept', 'homey_child_new_lead_agent_accept' );  
add_action( 'wp_ajax_homey_child_new_lead_agent_accept', 'homey_child_new_lead_agent_accept' );  
function homey_child_new_lead_agent_accept() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    //get the variables from the post data
    $reservationId = $_POST['reservationid']; 
    //get post meta of to check the agentid.
    $agentIds = get_post_meta($reservationId, 'homey_agents_notified', true);
    if(!empty($agentIds)){
      if(strstr($agentIds,',')){
         $checkAgIds = explode(',',$agentIds);
      }else{ 
	$checkAgIds = array($agentIds);
      }
    }else{
      $response = 'Error: Not A Rejected Lead.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    if(!in_array($current_user->ID, $checkAgIds)){
      $response = 'Error: Agent Not In Notified.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }else{
	//update the rejected status 
	update_post_meta($reservationId, 'reservation_status_agent', 'accepted');
	update_post_meta($reservationId, 'homey_agents', $current_user->ID);
	//remove all agents notified. 
	delete_post_meta($reservationId, 'homey_agents_notified');
        // genearte a comment for rejection of the reservation. 
        $agent = get_user_by( 'ID', $current_user->ID);
        $message = 'Lead Accepted';
        $args = array(
	  'comment_agent' => 'lead accept button',
	  'comment_approved' => 1, 'comment_author' => $agent->display_name,
	  'comment_author_email' => $agent->user_email, 'comment_author_IP' => '',
	  'comment_author_url' => '', 'comment_content' => $message, 
	  'comment_date' => date('Y-m-d H:i:s'),
	  'comment_date_gmt' => date('Y-m-d H:i:s'),
	  'comment_karma' => 0, 'comment_parent' => 0, 
	  'comment_post_ID' => $reservationId, 
	  'comment_type' => 'lead_acceptance',
	  'user_id' => $agent->ID,
	  'comment_meta' => array(
	    '_wxr_import_user' => $agent->ID,
	    'reservation_id' => $rservation_id,
	  ),
        ); 
        $commentid = wp_insert_comment($args);
        $response = 'Agent Assigned.';
        echo json_encode( array('success' => true, 'msg' => $response) );
        wp_die();
    }
}

/**
 * Agent Selection For Brokers For Lead Checkout. 
 * This happens when an agent rejects the lead routed to them.
 */
add_action( 'wp_ajax_nopriv_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
add_action( 'wp_ajax_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
function homey_child_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $reservationpost = get_post($details[2]); //var_dump($listingpost); 

    //generate the agents list based on the input. 
    $listagents = get_post_meta($reservationpost->ID, 'homey_agents_notified', true);   
    if(empty($listagents)){
	update_post_meta($reservationpost->ID, 'homey_agents_notified', $details[0]);
    } else {
	$check_agents = explode(',',$listagents); 
	if(!in_array($details[0], $check_agents)){
	  $new_agents = $listagents.','.$details[0]; 
	  update_post_meta($reservationpost->ID, 'homey_agents_notified', $new_agents); 
	} else { 
    	  $response = 'Agent Already Notified';
    	  echo json_encode( array('success' => false, 'msg' => $response) );
    	  wp_die();	  
	}
    } 

    //agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new lead has been added to profile.', 'homey-child' ) . "<br/>";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "<br/><br/>";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "<br/>";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "<br/>";
    $title = esc_html__('Notification For A Lead Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    //broker will receive an email.
    //$broker_info = get_userdata($details[1]);
    //$brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    //$message = esc_html__('A new lead has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    //$message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Notification Of A Lead Assignment To An Agent.', 'homey-child') . "\n";
    //homey_send_emails($brokeremail, $title, $message);

    if(isset($new_agents) && !empty($new_agents)){
	$c_agents = explode(',',$new_agents); $c_agentStr = '';
	foreach($c_agents as $agId){ 
	  $agent_info = get_userdata($agId); 
	  $c_agentStr .= $agent_info->display_name."\n";
	}
    } else {
	$agent_info = get_userdata($details[0]); $c_agentStr = ''; 
	$c_agentStr = $agent_info->display_name."\n";
    }
    $response = $c_agentStr;
    echo json_encode(array('success' => true, 'msg' => nl2br($response)));
    wp_die();
}

/**
 * Generate Routing Rules from Broker Dashboard. 
 */
add_action( 'wp_ajax_nopriv_homey_child_generate_routing_rules', 'homey_child_generate_routing_rules' );  
add_action( 'wp_ajax_homey_child_generate_routing_rules', 'homey_child_generate_routing_rules' );  
function homey_child_generate_routing_rules() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data);  
    //var_dump($form_details); exit; 

    //generate routing variables 
    $r_state = explode('=',$form_details[0]); $r_city = explode('=',$form_details[1]); 
    $r_pcode = explode('=',$form_details[2]); $r_agentId = explode('=',$form_details[3]); 
    $r_prange = explode('=',$form_details[4]); $r_keywords = explode('=',$form_details[5]); 
    $r_brokerId = $current_user->ID;  

    //if state, city and postcode is empty, generate error
    if($r_state[1] == '*' || $r_city[1] == '*' || $r_pcode[1] == '*'){ 
      $response = 'Error: State, City And Postcode Are Mandatory !!!.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    //check if there is any such listing for city, state and postcode
    $args = array('post_type'  => 'listing', 'posts_per_page' => -1, 'fields' => 'ids'); 
    $tax_query[] = array('taxonomy' => 'listing_state','terms' => $r_state[1],'field' => 'slug','operator' => 'AND', );
    $tax_query[] = array('taxonomy' => 'listing_city','terms' => $r_city[1],'field' => 'slug','operator' => 'AND', );
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $meta_query[] = array('key' => 'homey_zip','value' => $r_pcode[1],'compare' => '=');
    $args['meta_query'] = $meta_query; $findListing = new WP_Query($args); //var_dump($findListing); exit;
    if(!$findListing->have_posts()){ 
      $response = 'Error: State, City and Postcode Does Not Match Any Listing!!!.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }    
    $meta_query = $args = $tax_query = []; wp_reset_postdata();

    //check all rules for same postcode, if exists donot add
    $args = array('post_type'  => 'homey_routing', 'posts_per_page' => -1,);
    $meta_query[] = array('key' => 'homey_routing_pcode','value' => $r_pcode[1],'compare' => '=');
    $args['meta_query'] = $meta_query;   
    $pcodeRules = new WP_Query($args); $match = 0;     
    if($pcodeRules->have_posts()){
      foreach($pcodeRules->posts as $pcodeRule){
	$pcodeRuleState = get_post_meta($pcodeRule->ID,'homey_routing_state',true);
	$pcodeRuleCity = get_post_meta($pcodeRule->ID,'homey_routing_city',true);
	$pcodeRuleAgent = get_post_meta($pcodeRule->ID,'homey_routing_agent',true);
	$pcodeRulePRange = get_post_meta($pcodeRule->ID,'homey_routing_prange',true);
	$pcodeRuleKeywords = get_post_meta($pcodeRule->ID,'homey_routing_keywords',true); 
	if($pcodeRuleState == $r_state[1] && $pcodeRuleCity == $r_city[1] 
	   && $pcodeRuleAgent == $r_agentId[1] && $pcodeRulePRange == $r_prange[1] 
	   && $pcodeRuleKeywords == $r_keywords[1]){ $match = 1; break; }
      }
    }
    $meta_query = $args = $tax_query = []; wp_reset_postdata();

    //if match occurs then return
    if($match==1){ 
      $response = 'Error: Rule Already Exists For Agent !!!.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    //generate agent and broker
    $agent = get_userdata($r_agentId[1]); $broker = get_userdata($r_brokerId); 

    //title for the routing rules
    $r_rule_title = 'Routing Rule By Broker. Id-'.$r_brokerId; 
    $r_rule_details = 'State: '.$r_state[1].PHP_EOL.'City: '.$r_city[1].PHP_EOL.
		      'PCode: '.$r_pcode[1].PHP_EOL.'Agent: '.$agent->display_name.PHP_EOL. 
		      'Price Range: '.$r_prange[1].PHP_EOL.'Keywords: '.$r_keywords[1].PHP_EOL.
		      'Generated By: '.$broker->display_name.PHP_EOL;

    //generation of routing rule
    $rule = array(
	'post_author'	=> $r_brokerId, 
	'post_title'	=> $r_rule_title,
        'post_type' 	=> 'homey_routing', 
        'post_status'	=> 'publish',
        'post_parent'	=> 0,
        'post_excerpt'  => $r_rule_details,	
	//'post_content' => $r_rule_details,
	'post_name' => 'routing-rule-brokerid-'.$r_brokerId,
    );
    $rule_id =  wp_insert_post($rule);
    update_post_meta($rule_id, 'homey_routing_status', 'disabled');
    update_post_meta($rule_id, 'homey_routing_state', $r_state[1]);
    update_post_meta($rule_id, 'homey_routing_city', $r_city[1]);
    update_post_meta($rule_id, 'homey_routing_pcode', $r_pcode[1]);
    update_post_meta($rule_id, 'homey_routing_agent', $r_agentId[1]);
    update_post_meta($rule_id, 'homey_routing_prange', $r_prange[1]);
    update_post_meta($rule_id, 'homey_routing_keywords', $r_keywords[1]);

    $response = 'Success: Rule Generated!!!.';
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** Action for change of status of the rule from Broker Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_rulestatus_change_broker_dashboard', 'homey_child_rulestatus_change_broker_dashboard' );  
add_action( 'wp_ajax_homey_child_rulestatus_change_broker_dashboard', 'homey_child_rulestatus_change_broker_dashboard' );  
function homey_child_rulestatus_change_broker_dashboard() { 
    $response = ''; 
    $rule_Id = trim($_POST['ruleid']); $status = trim($_POST['status']);

    //update the postmeta with the following
    update_post_meta($rule_Id, 'homey_routing_status', $status);

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

    if($status == 'enabled'){
	$response = 'Rule Enabled !!!';
    	echo json_encode( array('success' => true, 'msg' => $response) ); 
    	wp_die();
    } else {
	$response = 'Rule Disabled !!!';
    	echo json_encode( array('success' => true, 'msg' => $response) ); 
    	wp_die();
    }
}


/** Action for adding a lead from Broker Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_add_lead_broker_dashboard', 'homey_child_add_lead_broker_dashboard' );  
add_action( 'wp_ajax_homey_child_add_lead_broker_dashboard', 'homey_child_add_lead_broker_dashboard' );  
function homey_child_add_lead_broker_dashboard() { 
    $response = ''; $request_type = $_POST['request_type']; 
    
    //if request_type == property_det
    if($request_type == 'property_det'){
	$propId = $_POST['propid'];
	$propName = get_the_title($propId); 
	if($propName != ''){
	  $propLoc = get_post_meta($propId,'homey_zip',true);
	  $propAddr = get_post_meta($propId,'homey_listing_address',true);
	  //create the json
	  $response = 'Property Name Found For ID !!!';
    	  echo json_encode( array( 'success' => true, 'msg' => $response, 'nam' => "$propName",  
				   'loc' => $propLoc, 'addr' => $propAddr ) ); 
    	  wp_die();
	}else{
	  //create the json from the array
	  $response = 'Property Name Not Found For ID !!!';
    	  echo json_encode( array( 'success' => false, 'msg' => $response ) ); 
    	  wp_die();	
	}
    }

    //if request_type == submit_form
    if($request_type == 'submit_form'){
	$response = ''; $pauthor_id = 1;
        $local = homey_get_localization();
        $allowded_html = array(); $reservation_meta = array(); $form_details= array();
	$form_data = urldecode($_POST['form_data']); //$form_details = explode('&', $form_data); 
	parse_str($form_data, $form_details); //var_dump($form_details); exit; 

	// -------------------------
	// sanitize all variables 
	// -------------------------
        if($_POST['n_bedrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $form_details['n_bedrooms']) ) { 
	  $bedrooms = $form_details['n_bedrooms']; 
	}else{ 
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Bedrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}
        if($_POST['n_bathrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $form_details['n_bathrooms']) ) { 
	  $bathrooms = $form_details['n_bathrooms']; 
	}else{
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Bathrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}

        $listing_id = intval($form_details['propertyid']); 
	$postcode = get_post_meta($listing_id, 'homey_zip', true); 
        $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
        $check_in_date  =  wp_kses ( $form_details['arrive'], $allowded_html );
        $check_out_date =  wp_kses ( $form_details['depart'], $allowded_html );
        //$extra_options  =  $_POST['extra_options'];
	$extra_options  =  ''; 
        $guests   =  intval($form_details['n_guests']);
        //$title = $local['reservation_text']; 
	$title = 'Lead: Property ID: '.$listing_id.' PostCode: '.$postcode;
        $owner = homey_usermeta($listing_owner_id);
        $owner_email = $owner['email'];

	// ---------------------
	// check for errors 
	// ---------------------
        //check security
        $nonce = $form_details['homey_addnewlead_security']; 
        if ( ! wp_verify_nonce( $nonce, 'homey-new-lead-create' ) ) {
             echo json_encode( array( 'success' => false, 'msg' => $local['security_check_text'] ) );
             wp_die();
        }

	//get broker, agent and customer info
	$broker = get_user_by('email', $form_details["broker_email"]); $broker_id = $broker->ID; 
	$agent_id = $form_details["agent-id"]; $agent = get_userdata($agent_id);
  	$renter_id = $form_details["renter-id"]; $renter = get_user_by('ID', $renter_id); 
	$phone = get_user_meta($renter_id, 'phone', true);
	if($phone != ''){ $userphone = $phone; } else { $userphone = ''; }
	$guest_message = 'Applied By Broker. ID: '.$broker_id;

	//contact message details
    	$contactdetails = 'Name: '.$renter->display_name.PHP_EOL.'Email: '.$renter->user_email.PHP_EOL.
		          'Phone: '.$userphone.PHP_EOL.'Message: '.$guest_message.PHP_EOL;

        $check_availability = check_booking_availability($check_in_date, $check_out_date, $listing_id, $guests);
        $is_available = $check_availability['success']; $check_message = $check_availability['message']; 
	//$is_available = true; 

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
            if($form_details['n_bedrooms'] != ''){ $reservation_meta['no_of_bedrooms'] = $bedrooms; }
            if($form_details['n_bathrooms'] != ''){ $reservation_meta['no_of_bathrooms'] = $bathrooms; }	
            $reservation_meta['location'] = $postcode;	

            $reservation = array(
                'post_title'    => $title,
                'post_status'   => 'publish', 
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

	    // reservation post meta update
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
            update_post_meta($reservation_id, 'listing_renter', $renter_id);
    	    update_post_meta($reservation_id, 'homey_lead_type', 'client_lead');	
    	    update_post_meta($reservation_id, 'reservation_price_range', $form_details["n_prange"]);
    	    update_post_meta($reservation_id, 'reservation_keywords', $form_details["keywords"]);

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
	    $res1 = homey_send_emails($renter->user_email, wp_specialchars_decode( $title ), $message);
	    $res2 = homey_send_emails($broker->user_email, wp_specialchars_decode( $title ), $message);
	    $res3 = homey_send_emails($agent->user_email, wp_specialchars_decode( $title ), $message);

            //echo json_encode( array( 'success' => true, 'message' => $local['request_sent'], 'reservid' => $reservation_id ) );
            echo json_encode( array( 'success' => true, 'msg' => $local['request_sent'] ) );
            //do_action('homey_create_messages_thread', $guest_message, $reservation_id);
            wp_die();

         } else { // end $check_availability
            echo json_encode( array( 'success' => false, 'msg' => $check_message ) );
            wp_die();
         }
    } //end if request_type == submit_form

}



