<?php

/**
 * addition of new post-types as per requirment.
 */ 

/**-------------------------------
 * Post type for routing
 * Brokers to create routes.
 *---------------------------------*/

function custom_post_routing() {
  $labels = array(
    'name'               => _x( 'Routings', 'post type general name' ),
    'singular_name'      => _x( 'Routing', 'post type singular name' ),
    //'add_new'            => _x( 'Add New', 'routing' ),
    //'add_new_item'       => __( 'Add New Routing' ),
    'edit_item'          => __( 'Edit Routing' ),
    //'new_item'           => __( 'New Routing' ),
    'all_items'          => __( 'All Routings' ),
    'view_item'          => __( 'View Routing' ),
    'search_items'       => __( 'Search Routings' ),
    'not_found'          => __( 'No routings found' ),
    'not_found_in_trash' => __( 'No routings found in Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Routings'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'View Routings',
    'public'        => true,
    'exclude_from_search' => true, 
    'menu_position' => 22,
    'capability_type' => 'post',
    'capabilities' => array( 'create_posts' => false, ), 
    'map_meta_cap' => true, 
    'hierarchical' => false, 
    'supports'      => array( 'title', 'author' ),
    'has_archive'   => true,
    'can_export' => true, 
    'delete_with_user' => false,
  );
  register_post_type( 'homey_routing', $args );
}
add_action( 'init', 'custom_post_routing' );

/*-------------------------------------------
 * Manage custom columns for the post type
 *-------------------------------------------*/
//add_filter('manage_routing_posts_columns' , array($this, 'custom_post_type_columns'));
add_filter('manage_homey_routing_posts_columns' , 'custom_post_type_columns');
function custom_post_type_columns($columns){
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      'post_id' =>__( 'ID'),
      "title" => __( 'Title','homey-child' ),
      'status' => __( 'Status','homey-child' ),
      "rout_date" => __('Generated On','homey-child'),
      "city" 	=> __('City','homey-child'), 
      "postcode" => __( 'PostCode','homey-child' ), 
      "prange" 	=> __('Price Range','homey-child'), 
      "keywords" => __('Keywords', 'homey-child' ), 
      "agent" => __( 'Agent','homey-child' ),
      "date" => __('Created On','homey-child'),
    );
   return $columns; 
}

//add_action( 'manage_routing_posts_custom_column' , array($this,'fill_custom_post_type_columns'), 10, 2 );
add_action( 'manage_homey_routing_posts_custom_column', 'fill_custom_post_type_columns', 10, 2 );
function fill_custom_post_type_columns( $column, $post_id ) {
  $cpost = get_post($post_id);
  switch ( $column ) {
     case 'status' :
	$cstatus = get_post_meta( $post_id , 'homey_routing_status' , true ); 
        if($cstatus == 'disabled'){ 
	  echo '<span style="background:#ef8495;color:#000;padding:3px;border:1px solid #aaa;border-radius:5px;">'.
	    esc_html__('Disabled').'</span>'; }
        if($cstatus == 'enabled'){ 
	  echo '<span style="background:#59ca6d;color:#000;padding:3px;border:1px solid #aaa;border-radius:5px;">'.
	    esc_html__('Enabled').'</span>'; }
	break;
     case 'rout_date' :
	echo date('Y-m-d', strtotime($cpost->post_date));
	break;
     case 'post_id' :
	echo $post_id; 
	break;
     case 'city' :
	echo get_post_meta( $post_id, 'homey_routing_city', true );
	break;
     case 'postcode' :
	echo get_post_meta( $post_id, 'homey_routing_pcode', true );
	break;
     case 'agent' :
	$agID = get_post_meta( $post_id, 'homey_routing_agent', true );
        $agent = get_userdata($agID); 
	if($agent){ echo $agent->display_name; }
	break;
     case 'prange' :
	echo get_post_meta( $post_id, 'homey_routing_prange', true );
	break;
     case 'keywords' :
	echo get_post_meta( $post_id, 'homey_routing_keywords', true );
	break;
  }
}

/**
 * Add Metaboxes to Routing to display the post variables. 
 */
add_action( 'add_meta_boxes', 'add_routing_meta_boxes' );
function add_routing_meta_boxes(){ 
  add_meta_box('homey-routing-meta', __( 'Routing Details', 'homey-child' ), 'homey_routing_meta_box', 'homey_routing', 'normal', 'high' ); 
}
function homey_routing_meta_box($post) {  
  $settings = array( 'textarea_rows' => '10', 'media_buttons' => false, 'quicktags' => false, 'tinymce' => false );
  wp_editor(html_entity_decode(stripcslashes($post->post_excerpt)), 'homey_postexcerpt', $settings); 
  //echo '&lt;p&gt;&lt;em&gt;Excerpts are optional, hand-crafted, summaries of your content.&lt;/em&gt;&lt;/p&gt;'; 
}  


/** 
 * Save Post From Admin Panel.
 */
//add_action('save_post', 'save_broker_agent_list');
//function save_broker_agent_list(){
//  global $post;
//  if ( ! empty($post) ) {
//    if($post->post_type == 'homey_reservation'){
//      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { 
//        return $post->ID;
//      }
//      update_post_meta($post->ID, "homey_brokers", $_POST["homey-brokers"]);
//      update_post_meta($post->ID, "homey_agents", $_POST["homey-agents"]);
//      update_post_meta($post->ID, "homey_lead_type", $_POST["homey-lead-type"]); 
//    }
//  }
//}


/**
 * Update the messages for custom post
 */
add_filter( 'post_updated_messages', 'cpost_updated_messages' );
function cpost_updated_messages( $messages ) {
  global $post, $post_ID;
  $messages['homey_routing'] = array(
    0 => '', 
    1 => sprintf( __('Routing updated. <a href="%s">View Routing</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Routing updated.'),
    5 => isset($_GET['revision']) ? sprintf( __('Routing restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Routing published. <a href="%s">View Routing</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Routing saved.'),
    8 => sprintf( __('Routing submitted. <a target="_blank" href="%s">Preview Routing</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Routing scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Routing</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Routing draft updated. <a target="_blank" href="%s">Preview Routing</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
  return $messages;
}


