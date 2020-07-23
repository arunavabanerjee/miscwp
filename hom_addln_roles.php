<?php

/**
 * addition of new roles as per requirment.
 * brokers and agents.
 */ 

//create role for brokers
$bwp_capabilities = get_role('editor')->capabilities; 
$ext_capabilities = array( 'read_listing' => true, 'edit_listing' => true, 'create_listings' => true,
        		'edit_listings' => true, 'delete_listings' => true, 'edit_published_listings' => true,
        		'publish_listings' => true, 'delete_published_listings'=> true, 'delete_private_listings' => true, 
			'create_users'	=> true, 'promote_users' => true, );
$broker_capabilities = array_merge($bwp_capabilities, $ext_capabilities);
add_role( 'homey_brokers', esc_html__( 'Brokers', 'homey-child' ), $broker_capabilities ); 

//create role for agents
$awp_capabilities = get_role('contributor')->capabilities;
$ext_capabilities = array( 'read_listing' => true, 'edit_listing' => true, 'create_listings' => true,
        		'edit_listings' => true, 'edit_published_listings' => true, 'publish_listings' => true, );
$agent_capabilities = array_merge($awp_capabilities, $ext_capabilities);
add_role( 'homey_agents', esc_html__( 'Agents', 'homey-child' ), $agent_capabilities ); 


/** setup additional fields in user profile to enter address information */
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );
function extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>
    <table class="form-table">
    <tr>
        <th><label for="address"><?php _e("Address"); ?></label></th>
        <td>
            <input type="text" name="address" id="address" value="<?php echo esc_attr( get_user_meta($user->ID, 'address', true) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your address."); ?></span>
        </td>
    </tr>
    <tr>
        <th><label for="city"><?php _e("City"); ?></label></th>
        <td>
            <input type="text" name="city" id="city" value="<?php echo esc_attr( get_user_meta($user->ID, 'city', true) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your city."); ?></span>
        </td>
    </tr>
    <tr>
    <th><label for="postalcode"><?php _e("Postal Code"); ?></label></th>
        <td>
            <input type="text" name="postalcode" id="postalcode" value="<?php echo esc_attr( get_user_meta($user->ID, 'postalcode', true) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your postal code."); ?></span>
        </td>
    </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
function save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    update_user_meta( $user_id, 'address', $_POST['address'] );
    update_user_meta( $user_id, 'city', $_POST['city'] );
    update_user_meta( $user_id, 'postalcode', $_POST['postalcode'] );
}


/* 
 * ========================================
 * Reservation
 * ========================================
 * Add extra meta boxes to reservation post type
 * These are to be used by brokers to select agents.
 */
/** add excerpt support to homey_reservation */
//add_action( 'init', 'add_excerpt_support_for_homey_reservation' );
//function add_excerpt_support_for_homey_reservation() {
//  add_post_type_support( 'homey_reservation', 'excerpt' ); 
//}
function homey_get_reservation_label($status, $reservation_id = null) {
   $status_label = '';
   $local = homey_get_localization();
   if(homey_listing_guest($reservation_id)) {
      if($status == 'under_review') {
          $status_label = '<span class="label label-danger">'.$local['under_review_label'].'</span>';
      } elseif($status == 'available') {
          $status_label = '<span class="label label-secondary">'.$local['res_avail_label'].'</span>';
      } elseif($status == 'in_progress') {
          $status_label = '<span class="label label-secondary"> IN PROGRESS </span>';
      } elseif($status == 'confirmed') {
          $status_label = '<span class="label label-warning"> CONFIRMED </span>';
      } elseif($status == 'rejected') {
          $status_label = '<span class="label label-danger"> REJECTED </span>';
      } 
   } else {
      if($status == 'under_review') {
          $status_label = '<span class="label label-danger">'.$local['new_label'].'</span>';     
      } elseif($status == 'available') {
         $status_label = '<span class="label label-secondary">'.$local['payment_process_label'].'</span>';
      } elseif($status == 'in_progress') {
          $status_label = '<span class="label label-secondary"> IN PROGRESS </span>';
      } elseif($status == 'confirmed') {
          $status_label = '<span class="label label-warning"> CONFIRMED </span>';
      } elseif($status == 'rejected') {
          $status_label = '<span class="label label-danger"> REJECTED </span>';
      } 
   }

   if($status == 'booked') {
      $status_label = '<span class="label label-success">'.$local['res_booked_label'].'</span>';
   } elseif ($status == 'declined') {
      $status_label = '<span class="label label-danger">'.$local['res_declined_label'].'</span>';
   } elseif ($status == 'cancelled') {
      $status_label = '<span class="label label-grey">'.$local['res_cancelled_label'].'</span>';
   } elseif($status == 'no_activity') {
      $status_label = '<span class="label label-danger"> NO ACTIVITY </span>';
   } elseif($status == 'completed') {
      $status_label = '<span class="label label-success"> COMPLETE </span>';
   } elseif($status == 'confirmed') {
      $status_label = '<span class="label label-warning"> CONFIRMED </span>';
   } elseif($status == 'rejected') {
      $status_label = '<span class="label label-danger"> REJECTED </span>';
   } 

   return $status_label;
}

function homey_reservation_count($user_id) {
	global $current_user;
	wp_get_current_user();

        $args = array(
            'post_type'        =>  'homey_reservation',
            'posts_per_page'    => -1,
        );

        if( homey_is_renter() ) {
            $meta_query[] = array(
                'key' => 'listing_renter',
                'value' => $user_id,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;

	} elseif($current_user->roles[0] == 'homey_agents') { 
            $meta_query[] = array(
              'key' => 'reservation_status',
              'value' => array('under_review','in_progress','confirmed','available','no_activity'),
              'compare' => 'IN'
            );

	    $sub_metaquery['relation'] = 'OR'; 
	
            $sub_metaquery[] = array(
               'key' => 'homey_agents',
               'value' => $user_id,
               'compare' => '='
            );

	    $sub_metaquery[] = array( 
		'key' => 'homey_agents_notified', 
                'value' => "$user_id",
                'compare' => 'LIKE'
	    ); 	    

	    $meta_query[] = $sub_metaquery;  
            $args['meta_query'] = $meta_query;

	} elseif($current_user->roles[0] == 'homey_brokers') { 
           $meta_query[] = array(
              'key' => 'homey_brokers',
              'value' => $user_id,
              'compare' => '='
           );
           $meta_query[] = array(
              'key' => 'reservation_status',
              'value' => array('under_review','in_progress','available','confirmed','no_activity'),
              'compare' => 'IN'
           );
           $meta_query['relation'] = 'AND';

	   $sub_metaquery['relation'] = 'OR'; 
	
	   $sub_metaquery[] = array( 
		'key' => 'reservation_status_agent', 
                'value' => array('rejected'),
                'compare' => 'NOT IN'
	   ); 

	   $sub_metaquery[] = array( 
	   	'key' => 'reservation_status_agent', 
                'compare' => 'NOT EXISTS'
	   ); 	    

	   $meta_query[] = $sub_metaquery; 

           $args['meta_query'] = $meta_query;

	} elseif($current_user->roles[0] == 'homey_host') { 
	   //get all listings for the user
	   $listing_query_args = array(
            'post_type' => 'listing',
            'posts_per_page' => -1,
            'post_status' => 'publish',
	    'fields' => 'ids',
	    'author' => $user_id,
           );
	   $host_listings = new WP_Query( $listing_query_args );
	   $host_listing_ids = $host_listings->posts; //var_dump($host_listing_ids); 

	   if(!empty($host_listing_ids)){
	     $meta_query[] = array(
              	'key' => 'reservation_listing_id',
              	'value' => $host_listing_ids,
              	'compare' => 'IN'
             );
             $meta_query[] = array(
              	'key' => 'reservation_status',
              	'value' => array('in_progress','available','confirmed','no_activity'),
              	'compare' => 'IN'
             );
             $meta_query['relation'] = 'AND';
             $args['meta_query'] = $meta_query;

	   }else{
	    // this is to generate an empty set
            $meta_query[] = array(
                'key' => 'listing_owner',
                'value' => $user_id,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;
	   }

        } else {
            $meta_query[] = array(
                'key' => 'listing_owner',
                'value' => $user_id,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;
        }

        $Qry = new WP_Query($args); wp_reset_postdata();
        $founds = $Qry->found_posts;

	//check the reservation_selections key for hosts
	/*if($current_user->roles[0] == 'homey_host') {
	  global $post;
	  if ($Qry->have_posts()){ 
	     //get the reservation ids to set apart
	     $reservIds = wp_list_pluck($Qry->posts, 'ID'); 
	     //check if the id is present in selected keys 
	     $reservSelIds = array();
	     foreach($host_listing_ids as $h_list_id){ 
		//create the query and run that
		$hargs = array('post_type' => 'homey_reservation', 'posts_per_page' => -1, );
		$hmeta_query[] = array(
              		'key' => 'reservation_selections',
              		'value' => "$h_list_id",
              		'compare' => 'LIKE'
           	);
		$hmeta_query[] = array(
              		'key' => 'reservation_selections',
              		'compare' => 'EXISTS'
           	);
           	$hmeta_query[] = array(
              		'key' => 'reservation_status',
              		'value' => array('under_review','in_progress','available','confirmed'),
              		'compare' => 'IN'
           	);
           	$hmeta_query['relation'] = 'AND';
           	$hargs['meta_query'] = $hmeta_query;

		$hQry = new WP_Query($hargs); 
		if($hQry->have_posts()){ 
		  while ($hQry->have_posts()): $hQry->the_post();
		     $cpID = $post->ID;
		     if(!in_array($cpID,$reservSelIds)){ array_push($reservSelIds, $cpID); }
		  endwhile;
		}
		wp_reset_postdata();
	     }
	     //var_dump($reservIds); var_dump($reservSelIds); 
	     //join two arrays to a single array
	     $host_reserv_Ids = array_unique(array_merge($reservIds, $reservSelIds)); 
	     //var_dump($host_reserv_Ids);
	     $founds = sizeof($host_reserv_Ids);
	  }
	}*/

        return $founds;

}

function homey_child_listing_count($user_id) { 
	global $current_user;
	wp_get_current_user();
        $args = array(
            'post_type'        =>  'listing',
            'posts_per_page'    => -1,
    	    'post_status'      =>  'publish',
        );
       if($current_user->roles[0] == 'homey_agents'){
  	 $meta_query[] = array(
       		'key' => 'homey_assignedagent',
       		'value' => $user_id,
       		'compare' => '='
  	 );
  	 $meta_query[] = array(
       		'key' => 'homey_typeofpropertylisting',
       		'value' => array('public'),
       		'compare' => 'IN'
  	 );
  	 $meta_query[] = array(
       		'key' => 'homey_typeofpropertylisting',
       		'compare' => 'NOT EXISTS',
  	 );
  	 $meta_query['relation'] = 'OR'; 
  	 $args['meta_query'] = $meta_query;
       }
       $args = homey_listing_sort ( $args );
       $listing_qry = new WP_Query($args);

       $founds = $listing_qry->found_posts;
       return $founds;
}

function homey_child_listing_count_pending($user_id) { 
	global $current_user;
	wp_get_current_user();
        $args = array(
            'post_type'        =>  'listing',
            'posts_per_page'    => -1,
    	    'post_status'      =>  'pending',
        );
       /*if($current_user->roles[0] == 'homey_agents'){
  	 $meta_query[] = array(
       		'key' => 'homey_assignedagent',
       		'value' => $user_id,
       		'compare' => '='
  	 );
  	 $meta_query[] = array(
       		'key' => 'homey_typeofpropertylisting',
       		'value' => array('public'),
       		'compare' => 'IN'
  	 );
  	 $meta_query[] = array(
       		'key' => 'homey_typeofpropertylisting',
       		'compare' => 'NOT EXISTS',
  	 );
  	 $meta_query['relation'] = 'OR'; 
  	 $args['meta_query'] = $meta_query;
       }*/
       $args = homey_listing_sort ( $args );
       $listing_qry = new WP_Query($args);

       $founds = $listing_qry->found_posts;
       return $founds;
}

/* add excerpt metabox to reservation */
add_action( 'add_meta_boxes', 'add_homey_reservation_excerpt_meta_box' );
function add_homey_reservation_excerpt_meta_box(){ 
  add_meta_box("homey_reservation_status", "Status", "display_status", "homey_reservation", "normal", "high");
  add_meta_box('homey_postexcerpt', __( 'Lead Details', 'homey-child' ), 'homey_reservation_excerpt_meta_box', 
		'homey_reservation', 'normal', 'high' ); 
  add_meta_box("homey_postbroker", "Brokers", "list_brokers_leadtype", "homey_reservation", "normal", "high"); 
  add_meta_box("homey_postagent", "Agents", "list_agents", "homey_reservation", "normal", "high"); 
  add_meta_box("homey_bookedproperties", "Booked Property", "homey_listbookedproperties", "homey_reservation", "side", "high");
  add_meta_box("homey_acceptedproperties", "Approved Properties", "homey_listapprovedproperties", "homey_reservation", "side", "default");
  add_meta_box("homey_deniedproperties", "Declined Properties", "homey_listdeniedproperties", "homey_reservation", "side", "default");
  add_meta_box("homey_agentselectedproperties", "Properties Selected By Agent", "homey_listselectedproperties", "homey_reservation", "side", "default"); 
}
function display_status(){
  global $post; $status_label = '';
  $res_status = get_post_meta($post->ID, 'reservation_status', true); 
  if($res_status == 'booked') {
      $status_label = '<span class="label label-success"> BOOKED </span>';
  } elseif ($res_status == 'declined') {
      $status_label = '<span class="label label-danger"> DECLINED </span>';
  } elseif ($res_status == 'cancelled') {
      $status_label = '<span class="label label-grey"> CANCELLED </span>';
  } elseif($res_status == 'no_activity') {
      $status_label = '<span class="label label-danger"> NO ACTIVITY </span>';
  } elseif($res_status == 'completed') {
      $status_label = '<span class="label label-success"> COMPLETE </span>';
  } elseif($res_status == 'confirmed') {
      $status_label = '<span class="label label-warning"> CONFIRMED </span>';
  } elseif($res_status == 'rejected') {
      $status_label = '<span class="label label-danger"> REJECTED </span>';
  } elseif($res_status == 'under_review') {
      $status_label = '<span class="label label-danger"> NEW </span>'; 
  } elseif($res_status == 'in_progress') {
      $status_label = '<span class="label label-secondary"> IN PROGRESS </span>';
  }
  echo $status_label;
}
function homey_reservation_excerpt_meta_box($post)  
{  
  $settings = array( 'textarea_rows' => '10', 'media_buttons' => false, 'quicktags' => false, 'tinymce' => false );
  wp_editor(html_entity_decode(stripcslashes($post->post_excerpt)), 'homey_postexcerpt', $settings); 
  //echo '&lt;p&gt;&lt;em&gt;Excerpts are optional, hand-crafted, summaries of your content.&lt;/em&gt;&lt;/p&gt;'; 
}  
function list_brokers_leadtype(){
   global $post;
   $broker_id = get_post_meta($post->ID, 'homey_brokers', true); 
   $user_args  = array( 'role__in' => ['homey_brokers'], 'orderby' => 'display_name' );

   echo "<select name='homey-brokers'>"; 
   if($broker_id == 0){ echo '<option value=0 selected="selected">Admin</option>'; }
   else{ echo '<option value=0>Admin</option>'; }
   $brokers = get_users($user_args);
   foreach ($brokers as $broker) { 
     $broker_info = get_userdata($broker->ID);
     if($broker_id == $broker_info->ID) { $broker_selected = 'selected="selected"'; } else { $broker_selected = ''; }
     echo '<option value='.$broker_info->ID.' '.$broker_selected.'>'.$broker_info->display_name.'</option>';
   }
   echo "</select>";

   //for the lead types
   $lead_type = get_post_meta($post->ID, 'homey_lead_type', true); 
   echo "<select name='homey-lead-type' style='margin-left:10px;'>"; 
   if($lead_type == '' || $lead_type == 'soft_lead'){ $selected = 'selected="selected"'; }else{ $selected = ""; }
   echo '<option value="soft_lead" '.$selected.'>Soft Lead</option>'; 
   if($lead_type == 'client_lead'){ $selected = 'selected="selected"'; }else{ $selected = ""; }
   echo '<option value="client_lead" '.$selected.'>Client Lead</option>';
   echo "</select>";
}
function list_agents(){
   global $post;
   $agent_id = get_post_meta($post->ID, 'homey_agents', true);
   $user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' );
   $agents = get_users($user_args);
   echo "<select name='homey-agents'>";
   if($agent_id == 0){ echo '<option value=0 selected="selected">None Selected</option>'; }
   else{ echo '<option value="0">None Selected</option>'; }
   foreach ($agents as $agent) { 
     $agent_info = get_userdata($agent->ID);
     if($agent_id == $agent_info->ID) { $agent_selected = 'selected="selected"'; } else { $agent_selected = ''; }
     echo '<option value='.$agent_info->ID.' '.$agent_selected.'>'.$agent_info->display_name.'</option>';
   }
   echo "</select>";
}
function homey_listselectedproperties(){
  global $post; 
  $selections = get_post_meta($post->ID, 'reservation_selections', true); 
  if($selections != ''){
     $html = '<ul>';
     $selectedPropIds = explode(',',$selections);
     foreach($selectedPropIds as $selectedPropId){
	if($selectedPropId != ''){ 
	  $html .= '<li style="overflow:auto;">';
	  $html .= '<div style="width:30%;float:left;">';
	  $html .= get_the_post_thumbnail($selectedPropId, 'thumbnail', 
			array('title'=>get_the_title($selectedPropId), 'style' => 'width:70px;height:60px;'));
	  $html .= '</div>';
	  $html .= '<div style="width:70%;float:left;"><strong>';
	  $html .= get_the_title($selectedPropId);
	  $html .= '</strong></div>';	  
          $html .= '</li>';
	}
     }
     $html .= '</ul>';	
     echo $html;
  }
}
function homey_listapprovedproperties(){
  global $post; 
  $selections = get_post_meta($post->ID, 'approved_reservations', true); 
  if($selections != ''){
     $html = '<ul>';
     $selectedPropIds = explode(',',$selections);
     foreach($selectedPropIds as $selectedPropId){
	if($selectedPropId != ''){ 
	  $html .= '<li style="overflow:auto;">';
	  $html .= '<div style="width:30%;float:left;">';
	  $html .= get_the_post_thumbnail($selectedPropId, 'thumbnail', 
			array('title'=>get_the_title($selectedPropId), 'style' => 'width:70px;height:60px;'));
	  $html .= '</div>';
	  $html .= '<div style="width:70%;float:left;"><strong>';
	  $html .= get_the_title($selectedPropId);
	  $html .= '</strong></div>';	  
          $html .= '</li>';
	}
     }
     $html .= '</ul>';	
     echo $html;
  }
}
function homey_listdeniedproperties(){
  global $post; 
  $selections = get_post_meta($post->ID, 'denied_reservations', true); 
  if($selections != ''){
     $html = '<ul>';
     $selectedPropIds = explode(',',$selections);
     foreach($selectedPropIds as $selectedPropId){
	if($selectedPropId != ''){ 
	  $html .= '<li style="overflow:auto;">';
	  $html .= '<div style="width:30%;float:left;">';
	  $html .= get_the_post_thumbnail($selectedPropId, 'thumbnail', 
			array('title'=>get_the_title($selectedPropId), 'style' => 'width:70px;height:60px;'));
	  $html .= '</div>';
	  $html .= '<div style="width:70%;float:left;"><strong>';
	  $html .= get_the_title($selectedPropId);
	  $html .= '</strong></div>';	  
          $html .= '</li>';
	}
     }
     $html .= '</ul>';	
     echo $html;
  }
}
function homey_listbookedproperties(){
  global $post; 
  $selections = get_post_meta($post->ID, 'reservation_booked', true); 
  if($selections != ''){
     $html = '<ul>';
     $selectedPropIds = explode(',',$selections);
     foreach($selectedPropIds as $selectedPropId){
	if($selectedPropId != ''){ 
	  $html .= '<li style="overflow:auto;">';
	  $html .= '<div style="width:30%;float:left;">';
	  $html .= get_the_post_thumbnail($selectedPropId, 'thumbnail', 
			array('title'=>get_the_title($selectedPropId), 'style' => 'width:70px;height:60px;'));
	  $html .= '</div>';
	  $html .= '<div style="width:70%;float:left;"><strong>';
	  $html .= get_the_title($selectedPropId);
	  $html .= '</strong></div>';	  
          $html .= '</li>';
	}
     }
     $html .= '</ul>';	
     echo $html;
  }
}

add_action('save_post', 'save_broker_agent_list');
function save_broker_agent_list(){
  global $post;
  if ( ! empty($post) ) {
    if($post->post_type == 'homey_reservation'){
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
        return $post->ID;
      }
      update_post_meta($post->ID, "homey_brokers", $_POST["homey-brokers"]);
      update_post_meta($post->ID, "homey_agents", $_POST["homey-agents"]);
      update_post_meta($post->ID, "homey_lead_type", $_POST["homey-lead-type"]); 
    }
  }
}

/**
 * remove columns of the homey_reservation post
 */
add_filter('houzez_custom_post_listing_columns', 'modify_homey_reservation_columns');
function modify_homey_reservation_columns ( $columns ) 
{
    // 'unset' unwanted columns
    unset($columns['pic']);
    unset($columns['check_in']);       
    unset($columns['check_out']);
    unset($columns['res_guests']);
    unset($columns['pets']); 
    unset($columns['subtotal']);
    unset($columns['actions']); 
    unset($columns['date']);

    // add a new column
    if(is_array( $columns ) && ! isset( $columns['property_price'] ))
        $columns['property_price'] = 'Nightly Price';
    if(is_array( $columns ) && ! isset( $columns['received_dt'] ))
        $columns['received_dt'] = 'Received On'; 
    if(is_array( $columns ) && ! isset( $columns['broker_assigned'] ))
        $columns['broker_assigned'] = 'Brokers'; 
    if(is_array( $columns ) && ! isset( $columns['agent_assigned'] ))
        $columns['agent_assigned'] = 'Agents';
    if(is_array( $columns ) && ! isset( $columns['lead_type'] ))
        $columns['lead_type'] = 'Lead Type';
    if(is_array( $columns ) && ! isset( $columns['actions'] ))
        $columns['actions'] = 'Actions';
    if(is_array( $columns ) && ! isset( $columns['date'] ))
        $columns['date'] = 'Date';  

    return $columns;
} 

add_filter('homey_listing_admin_actions', 'modify_homey_reservation_actions');
function modify_homey_reservation_actions ( $admin_actions ) {
   //var_dump($admin_actions);
   if(isset($admin_actions['mark-paid'])){ 
	array_shift($admin_actions); 
   }
   return $admin_actions;
}

add_action('manage_homey_reservation_posts_custom_column', 'homey_reservation_posts_custom_column', 10, 2);
function homey_reservation_posts_custom_column( $column_name, $post_id ) 
{
    if ( $column_name == 'broker_assigned') {
      $broker_id = get_post_meta($post_id, "homey_brokers", true);
      if($broker_id == 0){ echo 'Admin'; }
      else{
	$user_args  = array( 'role__in' => ['homey_brokers'], 'orderby' => 'display_name' ); 
   	$brokers = get_users($user_args);
        foreach ($brokers as $broker) { 
          $broker_info = get_userdata($broker->ID);
          if($broker_id == $broker_info->ID) { echo $broker_info->display_name; break; } 
	}
      }
    }
    if ( $column_name == 'agent_assigned') {
      $agent_id = get_post_meta($post_id, "homey_agents", true);
      if($agent_id == 0){ echo esc_html('None'); }
      else{
	$user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' ); 
   	$agents = get_users($user_args);
        foreach ($agents as $agent) { 
          $agent_info = get_userdata($agent->ID);
          if($agent_id == $agent_info->ID) { echo $agent_info->display_name; break; } 
	}
      }
    }
    if ( $column_name == 'lead_type') {
       $lead_type = get_post_meta($post_id, "homey_lead_type", true); 
       if($lead_type == '' || $lead_type == 'soft_lead'){ 
         echo '<span style="background:#ef8495;color:#000;padding:3px;border:1px solid #aaa;border-radius:5px;">'.
		esc_html__('Soft Lead').'</span>'; 
       } else{ 
	 echo '<span style="background:#54c4d9;color:#000;padding:3px;border:1px solid #aaa;border-radius:5px;">'.
		esc_html__('Client Lead').'</span>'; 
       }
    }
    if ( $column_name == 'status') {
        $agstatus = get_post_meta($post_id, "reservation_status_agent", true);
	if(!empty($agstatus)){ 
	   echo '<br/>';
	   echo homey_get_reservation_label($agstatus);	   
	}
    }
    if ( $column_name == 'received_dt') { 
        $resv_post = get_post($post_id); 
	echo date('Y-m-d', strtotime($resv_post->post_date));
    }
    if( $column_name == 'property_price'){
	$reserv_listingid = get_post_meta($post_id, "reservation_listing_id", true);
        $nightly_price = get_post_meta($reserv_listingid, "homey_night_price", true); 
        echo homey_formatted_price($nightly_price).'/night';
    }
}


/** adding more filter for reservation post */
if (is_admin()){
add_action('restrict_manage_posts', 'homey_reservation_extra_filters', 10);
function homey_reservation_extra_filters($post_type){
    if($post_type != 'homey_reservation'){
      return; //filter your post
    }
    $sel_ag = isset($_GET['ag'])? $_GET['ag'] : '';
    $sel_lead = isset($_GET['lead'])? $_GET['lead'] : '';
    $sel_stat = isset($_GET['stat'])? $_GET['stat'] : '';

    // agent dropdown to filter by agent
    $user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' );
    $agents = get_users($user_args);
    echo "<select class='homey-searchbyagents' name='ag' style='width:20%;'>";
    echo '<option value="">'.esc_html__('Select Agent').'</option>';
    foreach ($agents as $agent) { $selected = '';
      $agent_info = get_userdata($agent->ID);
      if($agent_info->ID == $sel_ag){ $selected = 'selected="selected"'; }
      echo '<option value="'.$agent_info->ID.'" '.$selected.'>'.$agent_info->display_name.'</option>';
    }
    echo "</select>";

    // broker dropdown to filter by broker
    //$user_args  = array( 'role__in' => ['homey_brokers'], 'orderby' => 'display_name' );
    //$brokers = get_users($user_args);
    //echo "<select class='homey-searchbybrokers' name='homey-searchbybrokers' style='width:21%;'>";
    //echo '<option value="">'.esc_html__('Select Broker').'</option>';
    //foreach ($brokers as $broker) { 
      //$broker_info = get_userdata($broker->ID);
      //echo '<option value="'.$broker_info->ID.'">'.$broker_info->display_name.'</option>';
    //}
    //echo "</select>";

    // reservation type to filter by type
    echo "<select class='homey-searchbyleadtypes' name='lead' style='width:20%;'>";
    if($sel_lead == ''){ $selected = 'selected="selected"'; }else{ $selected = ''; }
    echo '<option value="" '.$selected.'>'.esc_html__('Select Type').'</option>';
    if($sel_lead == 'soft_lead'){ $selected = 'selected="selected"'; }else{ $selected = ''; }
    echo '<option value="soft_lead" '.$selected.'>'.esc_html__('Soft Lead').'</option>';
    if($sel_lead == 'client_lead'){ $selected = 'selected="selected"'; }else{ $selected = ''; }
    echo '<option value="client_lead" '.$selected.'>'.esc_html__('Client Lead').'</option>';
    echo "</select>";  

    // reservation type to filter by type
    echo "<select class='homey-searchbystatus' name='stat' style='width:19%;'>";
    if($sel_stat == ''){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="" '.$selected.'>'.esc_html__('Select Status').'</option>';
    if($sel_stat == 'booked'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="booked" '.$selected.'>'.esc_html__('Booked').'</option>';
    if($sel_stat == 'declined'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="declined" '.$selected.'>'.esc_html__('Declined').'</option>';
    if($sel_stat == 'cancelled'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="cancelled" '.$selected.'>'.esc_html__('Cancelled').'</option>';
    if($sel_stat == 'no_activity'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="no_activity" '.$selected.'>'.esc_html__('No Activity').'</option>';
    if($sel_stat == 'completed'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="completed" '.$selected.'>'.esc_html__('Completed').'</option>';
    if($sel_stat == 'confirmed'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="confirmed" '.$selected.'>'.esc_html__('Confirmed').'</option>';
    if($sel_stat == 'rejected'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="rejected" '.$selected.'>'.esc_html__('Rejected').'</option>';
    if($sel_stat == 'under_review'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="under_review" '.$selected.'>'.esc_html__('New').'</option>';
    if($sel_stat == 'in_progress'){ $selected = 'selected="selected"'; } else { $selected = ''; }
    echo '<option value="in_progress" '.$selected.'>'.esc_html__('In Progress').'</option>';
    echo "</select>"; 
}

/** add query variables */ 
add_filter( 'parse_query', 'homey_reservation_query_vars', 10);
function homey_reservation_query_vars($query){
   global $pagenow;
   $post_type = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';
   // for homey_reservation
   if ($post_type == 'homey_reservation' && $pagenow=='edit.php') { 
     $meta_query = array();
     if( isset($_GET['ag']) && !empty($_GET['ag']) ) { $query->query_vars['ag'] = $_GET['ag']; 
      $meta_query_args[] = array( 'key' => 'homey_agents', 'value' => $_GET['ag'], 'compare' => '=', ); 
     }
     if( isset($_GET['lead']) && !empty($_GET['lead']) ) { $query->query_vars['lead'] = $_GET['lead']; 
      $meta_query_args[] = array( 'key' => 'homey_lead_type', 'value' => $_GET['lead'], 'compare' => 'LIKE', );
     }
     if( isset($_GET['stat']) && !empty($_GET['stat']) ) { $query->query_vars['stat'] = $_GET['stat']; 
      $meta_query_args[] = array( 'key' => 'reservation_status', 'value' => $_GET['stat'], 'compare' => '=', );
     }
     if(isset($meta_query_args) && count($meta_query_args) > 1){ 
	$meta_query_args['relation'] = 'AND'; $meta_query[] = $meta_query_args;
     } elseif(isset($meta_query_args) && count($meta_query_args) == 1){ 
    	$meta_query[] = $meta_query_args;
     }
     $query->set('meta_query', $meta_query); //var_dump($query); exit;
     //$query->meta_query = $meta_query; var_dump($query); exit;
   }
}
} // endif admin


/* 
 * ========================================
 * Listing 
 * ========================================
 * Add extra meta boxes to listing post type
 * These are to be used by brokers to select agents.
 */
add_action( 'add_meta_boxes', 'add_homey_listing_meta_box' );
function add_homey_listing_meta_box(){ 
  add_meta_box("homey_listingagent", "Assigned Agent", "homey_agentslist", "listing", "normal", "default");
  add_meta_box("homey_typeofpropertylisting", "Type Of Property", "homey_listtypeofproperty", "listing", "normal", "default");
}

function homey_agentslist(){
   global $post;
   $agent_id = get_post_meta($post->ID, 'homey_assignedagent', true);
   $user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' );
   $agents = get_users($user_args);
   echo "<select name='homey-listagents'>";
   if($agent_id == 0){ echo '<option value=0 selected="selected">None Selected</option>'; }
   else{ echo '<option value="0">None Selected</option>'; }
   foreach ($agents as $agent) { 
     $agent_info = get_userdata($agent->ID);
     if($agent_id == $agent_info->ID) { $agent_selected = 'selected="selected"'; } else { $agent_selected = ''; }
     echo '<option value='.$agent_info->ID.' '.$agent_selected.'>'.$agent_info->display_name.'</option>';
   }
   echo "</select>";
}
function homey_listtypeofproperty(){
   global $post;
   $typeofproperty = get_post_meta($post->ID, 'homey_typeofpropertylisting', true);
   echo "<select name='homey-typeofpropertylisting'>";
   if($typeofproperty == ""){ echo '<option value="" selected="selected">None</option>'; }
   else{ echo '<option value="">None</option>'; }
   if($typeofproperty == 'public'){ echo '<option value="public" selected="selected">Public</option>'; 
   }else{ echo '<option value="public">Public</option>'; }
   if($typeofproperty == 'private'){ echo '<option value="private" selected="selected">Private</option>'; 
   }else{ echo '<option value="private">Private</option>'; }
   echo "</select>";
}

add_action('save_post', 'save_assigned_agent');
function save_assigned_agent(){
  global $post;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
    return $post->ID;
  }
  if(isset($_POST['homey-listagents']) && $_POST['homey-listagents'] != 0){
    update_post_meta($post->ID, "homey_assignedagent", $_POST["homey-listagents"]);
  }
  if(isset($_POST['homey-typeofpropertylisting']) && $_POST['homey-typeofpropertylisting'] != ""){
    update_post_meta($post->ID, "homey_typeofpropertylisting", $_POST["homey-typeofpropertylisting"]);
  }
}

/**
 * Add comments section for listing post type. 
 */
//add_action( 'init', 'enable_comments_for_listing', 11 );
//function enable_comments_for_listing() {
 //add_post_type_support( 'listing', 'comments' );
//}

/**
 * column arrangement of the homey listing post
 */
add_filter('homey_custom_post_listing_columns', 'modify_homey_listing_columns');
function modify_homey_listing_columns ( $columns ) 
{
    // 'unset' columns
    unset($columns['date']);
    unset($columns['homey_actions']); 

    // add new columns
    if(is_array( $columns ) && ! isset( $columns['created_by'] ))
        $columns['created_by'] = 'Created By';
    if(is_array( $columns ) && ! isset( $columns['assigned_agent'] ))
        $columns['assigned_agent'] = 'Agents';
    if(is_array( $columns ) && ! isset( $columns['property_listing_type'] ))
        $columns['property_listing_type'] = 'Type';
    if(is_array( $columns ) && ! isset( $columns['date'] ))
        $columns['date'] = 'Date';  
    return $columns;
} 
add_action('manage_listing_posts_custom_column', 'homey_listing_posts_custom_column', 10, 2);
function homey_listing_posts_custom_column( $column_name, $post_id ) 
{
    if ( $column_name == 'assigned_agent') {
      $agent_id = get_post_meta($post_id, "homey_assignedagent", true);
      if($agent_id == 0){ echo esc_html('None'); }
      else{
	$user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' ); 
   	$agents = get_users($user_args);
        foreach ($agents as $agent) { 
          $agent_info = get_userdata($agent->ID);
          if($agent_id == $agent_info->ID) { echo $agent_info->display_name; break; } 
	}
      }
    }
    if ( $column_name == 'created_by') {
      $cpost = get_post($post_id); 
      $cpost_author = $cpost->post_author; 
      if($cpost_author == 0){ echo esc_html('None'); }
      else{
        //get list of users and display name
        $listauthors = get_users();
        foreach ($listauthors as $author) { 
          $author_info = get_userdata($author->ID);
          if($cpost_author == $author_info->ID) { echo $author_info->display_name; break; } 
	}
      } // end if - else
    }
    if ( $column_name == 'property_listing_type') {
      $typeoflisting = get_post_meta($post_id, "homey_typeofpropertylisting", true);
      if($typeoflisting != ''){ echo strtoupper($typeoflisting);
      }else{ echo 'NONE'; }
    } 
}
