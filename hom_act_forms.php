<?php 
/**
 * Submission of Contact forms
 */
/** Action for submission of contact form for hosts */
add_action( 'wp_ajax_nopriv_homey_child_contact_list_host_submission', 'homey_child_contact_list_host_submission' );  
add_action( 'wp_ajax_homey_child_contact_list_host_submission', 'homey_child_contact_list_host_submission' );  
function homey_child_contact_list_host_submission() { 
    $response = ''; $user_id = 2; //default guest user as per system.
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); 
    
    //create a lead and enter into db
    $listingId = explode('=',$form_details[4]); $postcode = explode('=',$form_details[5]);
    $pname = explode('=',$form_details[7]); $pemail = explode('=',$form_details[8]);
    $pphone = explode('=',$form_details[9]); $pmessage = explode('=',$form_details[10]);
    $contactdetails = 'Name: '.$pname[1].PHP_EOL.'Email: '.$pemail[1].PHP_EOL.
		      'Phone: '.$pphone[1].PHP_EOL.'Message: '.$pmessage[1].PHP_EOL;
    $querytitle = 'Lead: Property ID: '.$listingId[1].' PostCode: '.$postcode[1];
    $homelistingpost = get_post($listingId[1]); //var_dump($homelistingpost); 
    $listingpostcode = get_post_meta($homelistingpost->ID, 'homey_zip', true); 
    $broker_id = 0; 
    $user_args  = array( 'role__in' => ['homey_brokers'], 'orderby' => 'display_name' ); 
    $brokers = get_users($user_args); 
    foreach($brokers as $broker){  //echo $broker->ID; 
       $brokerpostcode = get_user_meta($broker->ID,'postalcode',true);
       if($listingpostcode == $brokerpostcode){ $broker_id = $broker->ID; break; }
    }
    // no brokers exist, then assign to admin
    if($broker_id == 0){ $broker_id = 0; }

    $lead = array(
	'post_author'	=> $user_id, 
	'post_title'	=> $querytitle,
        'post_type' 	=> 'homey_reservation', 
        'post_status'	=> 'publish',
        'post_parent'	=> 0,
        'post_excerpt'  => $contactdetails,	
    );
    $lead_id =  wp_insert_post($lead);
    update_post_meta($lead_id, 'reservation_listing_id', $listingId[1]);
    update_post_meta($lead_id, 'listing_owner', $homelistingpost->post_author);
    update_post_meta($lead_id, 'listing_renter', $user_id);
    update_post_meta($lead_id, 'reservation_checkin_date', date('Y-m-d'));
    update_post_meta($lead_id, 'reservation_status', 'under_review');
    update_post_meta($lead_id, 'is_hourly', 'no');
    update_post_meta($lead_id, 'slide_template', 'default');
    update_post_meta($lead_id, 'homey_brokers', $broker_id);
    update_post_meta($lead_id, 'homey_lead_type', 'soft_lead');

    $response = 'Success: Thanks For Contacting Us. Our Agent Will Get In Touch.';
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}

/** Action for submission of contact form for hosts from Agent Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_agent_contact_host_submission', 'homey_child_agent_contact_host_submission' );  
add_action( 'wp_ajax_homey_child_agent_contact_host_submission', 'homey_child_agent_contact_host_submission' );  
function homey_child_agent_contact_host_submission() { 
    $local = homey_get_localization(); $response = '';
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); exit;

    $security = explode('=',$form_details[1]); $nonce = $security[1];
    if ( ! wp_verify_nonce( $nonce, 'host-contact-nonce' ) ) {
       echo json_encode( array( 'success' => false, 'message' => $local['security_check_text'] ) );
       wp_die();
    }

    //get the email of the user from listing.
    $listingId = explode('=',$form_details[4]); $propertyLink = explode('=',$form_details[2]); 
    $propertyPCode = explode('=',$form_details[5]); $propertyTitle =  explode('=',$form_details[3]);
    $homelistingpost = get_post($listingId[1]); //var_dump($homelistingpost);  
    $author_id = get_post_field ('post_author', $listingId[1]);
    $author_email = get_the_author_meta( 'user_email', $author_id ); 
    $agent_message = explode('=',$form_details[10]); $agent_name = explode('=',$form_details[7]);
    $agent_email = explode('=',$form_details[8]); $agent_ph = explode('=',$form_details[9]); 
    $rservation_id = $_POST['reservationid'];

    // send a message to broker about creation of the user
    $message = esc_html__('Availability Check On Property:', 'homey-child' ) . "<br/><br/>"; 
    $message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "<br/><br/>"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "<br/><br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "<br/><br/>";    
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "<br/><br/>";
    $message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "<br/><br/>";
    $title = esc_html__('Availability Check On Property. Property Id: '.$listingId[1], 'homey-child') . "\n";
    $result = homey_send_emails($author_email, wp_specialchars_decode( $title ), $message);

    //after an email is sent to the client, status would be modified to be in progress.
    update_post_meta($rservation_id, 'reservation_status', 'in_progress'); 

    //also the selected properties will be assigned.
    $selected_props = get_post_meta($rservation_id, 'reservation_selections', true); 
    if(empty($selected_props)){
      update_post_meta($rservation_id, 'reservation_selections', $listingId[1]); 
    }else{
      $addlistings = $selected_props.','.$listingId[1];
      update_post_meta($rservation_id, 'reservation_selections', $addlistings);
    }

    //add the host id to the list 
    $selected_hosts = get_post_meta($rservation_id, 'reservation_host_contacts', true); 
    if(empty($selected_hosts)){
      update_post_meta($rservation_id, 'reservation_host_contacts', $author_id); 
    }else{
      $addhost = $selected_hosts.','.$author_id;
      update_post_meta($rservation_id, 'reservation_host_contacts', $addhost);
    }    

    //update comments
    $agent = get_user_by( 'email', $agent_email[1]);
    $args = array(
	'comment_agent' => 'form:availablity_check',
	'comment_approved' => 1, 'comment_author' => $agent_name[1],
	'comment_author_email' => $agent_email[1], 'comment_author_IP' => '',
	'comment_author_url' => '', 'comment_content' => $message, 
	'comment_date' => date('Y-m-d H:i:s'),
	'comment_date_gmt' => date('Y-m-d H:i:s'),
	'comment_karma' => 0, 'comment_parent' => 0, 
	'comment_post_ID' => $listingId[1], 
	'comment_type' => 'availablity_check',
	'user_id' => $agent->ID,
	'comment_meta' => array(
	   '_wxr_import_user' => $agent->ID,
	   'reservation_id' => $rservation_id,
	),
    ); 
    $commentid = wp_insert_comment($args);

    //return the result
    if($result){ 
      $response = 'Email Sent To Host.';
      echo json_encode( array('success' => true, 'msg' => $response) ); 
    } else{ 
      $response = 'Email To Host Could Not Be Sent.';
      echo json_encode( array('success' => false, 'msg' => $response) ); 
    }
    wp_die();
}

/**
 * Misc Actions for Dashboards
 */
/** Action for display of a modal for contacting host */
add_action( 'wp_ajax_nopriv_homey_child_display_host_contact_modal', 'homey_child_display_host_contact_modal' );  
add_action( 'wp_ajax_homey_child_display_host_contact_modal', 'homey_child_display_host_contact_modal' );  
function homey_child_display_host_contact_modal() { 
    global $post, $homey_prefix, $homey_local;
    $homey_prefix = 'homey_'; $response = ''; 
    $homey_local = homey_get_localization();

    $post = get_post($_POST['listingid']); //var_dump($post); 
    get_template_part('single-listing/contact-host'); 
    $response .= ob_get_contents();
    ob_end_clean();
    wp_reset_postdata(); 

    if($response != ''){ echo $response;
    }else{ echo '<p>No Modal - Could Not Create Modal</p>'; }
    wp_die();
}

/*------------------------------------------------------------------------------*/
// Register New User From "Register" Menu
/*------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_homey_child_register', 'homey_child_register' );
add_action( 'wp_ajax_homey_child_register', 'homey_child_register' );
function homey_child_register() { //var_dump($_POST); exit;
        //$local = homey_get_localization();
        check_ajax_referer('homey_register_nonce', 'homey_register_security');
        $allowed_html = array();

        //$usermane = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));

        $userFname = trim( sanitize_text_field( wp_kses($_POST['userfname'], $allowed_html) ));
        $userLname = trim( sanitize_text_field( wp_kses($_POST['userlname'], $allowed_html) ));
        $email     = trim( sanitize_text_field( wp_kses($_POST['useremail'], $allowed_html) ));
        $term_condition  = trim( $_POST['term_condition'] );
        $enable_password = homey_option('enable_password');
        $response = $_POST["g-recaptcha-response"];

        $user_role = get_option( 'default_role' );  

        if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
            $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
        } else {
            $user_role = $user_role;
        }

        $term_condition = ( $term_condition == 'on') ? true : false;

	$username = strtolower($userFname).strtolower($userLname);

	$userdisplayname = $userFname.' '.$userLname; 

        if( !$term_condition ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'homey-login-register') ) );
            wp_die();
        }

        /*if( empty( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'homey-login-register') ) );
            wp_die();
        }
        if( strlen( $usermane ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'homey-login-register') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'homey-login-register') ) );
            wp_die();
        }*/

        if( username_exists( $username ) ) { 
	    if( email_exists( $email ) ) {
              echo json_encode( array( 'success' => false, 'msg' => esc_html__('User Is Already Registered.', 'homey-login-register') ) );
              wp_die();
	    }else{
		$usersalt = substr(md5(time()), 0, 6); 
		$username = strtolower($userFname).strtolower($userLname).'_'.$usersalt;
	    }
        }

        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'homey-login-register') ) );
            wp_die();
        }

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Email Address Is Already Registered.', 'homey-login-register') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid Email Address.', 'homey-login-register') ) );
            wp_die();
        }

        if( $enable_password == 'yes' ){
            $user_pass         = trim( sanitize_text_field( wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
            $user_pass_retype  = trim( sanitize_text_field( wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

            if ($user_pass == '' || $user_pass_retype == '' ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'homey-login-register') ) );
                wp_die();
            }

            if ($user_pass !== $user_pass_retype ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'homey-login-register') ) );
                wp_die();
            }
        }

        $enable_forms_gdpr = homey_option('enable_forms_gdpr');

        if( $enable_forms_gdpr != 0 ) {
            $privacy_policy = isset($_POST['privacy_policy']) ? $_POST['privacy_policy'] : '';
            if ( empty($privacy_policy) ) {
                echo json_encode(array(
                    'success' => false,
                    'msg' => homey_option('forms_gdpr_validation')
                ));
                wp_die();
            }
        }

        homey_google_recaptcha_callback();

        if($enable_password == 'yes' ) {
            $user_password = $user_pass;
        } else {
            $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        }

        //$user_id = wp_create_user( $usermane, $user_password, $email );
        $userdata = array(
	   'user_pass' => $user_password, 
	   'user_login' => $username,
           'user_nicename' => $userdisplayname, 
           'user_email' => $email,
           'display_name' => $userdisplayname,
           'nickname' => $username,
	   'first_name' => $userFname, 
	   'last_name' => $userLname, 
	   'user_registered' => date('Y-m-d H:i:s'), 
	   'show_admin_bar_front' => false, 
      	);
      	$user_id = wp_insert_user( $userdata ); 

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {
            wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

            if( $enable_password =='yes' ) {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now! UserID: '.$username, 'homey-login-register') ) );
            } else {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Registration complete. Please check your email!', 'homey-login-register') ) );
            }
            homey_wp_new_user_notification( $user_id, $user_password );
        }
        wp_die();
}


