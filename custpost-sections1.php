====== type1 =========
<?php

/**
 * Create Post Type - Partners
 */
function post_type_partners() {

    $args = array(
        'labels' => array(
            'name' => 'Our Partners',        
         ),
        'hierarchical' => true,
        'menu_position' => 30,        
        'public' => true,
        'has_archive' => true, 
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'custom-fields', 'page-attributes'), 
        'capability_type'  => 'post',
        'rewrite' => array('slug' => '/about-us/our-partners'),
    );
    register_post_type('partners', $args); 
     /* IMPORTIONT: Remember this line! */
    //flush_rewrite_rules(); 
}
add_action('init', 'post_type_partners'); 

/**
 * Add Meta Boxes for Partner Post
 */
add_action( 'add_meta_boxes', 'add_meta_boxes_to_partners_post' );
function add_meta_boxes_to_partners_post(){ 
  add_meta_box("select_partner_post_level", __( "Select Level Of Post To Display", 'textdomain'), "select_partner_post_level", 
  		"partners", "side", "low"); 		   
}

function select_partner_post_level(){
   global $post;
   $curr_post_level = get_post_meta($post->ID, 'partners_post_level_for_display', true); 
   if(empty($curr_post_level)){ $curr_post_level = 'Level 0'; }  			       
   echo '<select name="sel_partner_post_level" style="width:100%;">';
   if($curr_post_level == 'Level 0'){ echo '<option value="Level 0" selected="selected">Level 0</option>'; }
   else{ echo '<option value="Level 0">Level 0</option>'; }    
   if($curr_post_level == 'Level 1'){ echo '<option value="Level 1" selected="selected">Level 1</option>'; }
   else{ echo '<option value="Level 1">Level 1</option>'; }    
   if($curr_post_level == 'Level 2'){ echo '<option value="Level 2" selected="selected">Level 2</option>'; }
   else{ echo '<option value="Level 2">Level 2</option>'; }    
   echo "</select>";
}

add_action('save_post', 'on_click_partner_post_save');
function on_click_partner_post_save(){
  global $post;
  if ( !empty($post) ) { 
   if($post->post_type == 'partners'){ 
     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
     if($_POST["sel_partner_post_level"] != -1){ 
       update_post_meta($post->ID, "partners_post_level_for_display", $_POST["sel_partner_post_level"]); 
     } 
   }//endif posttype == partner_listing
  }//endif !empty post 
} 

/**
 * Add ACF Fields to the above custom post type. 
 * Will be used by ACF
 */
if(function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_62f0af49d92ed',
	'title' => 'Our Partners - Common',
	'fields' => array(
		/*array(
			'key' => 'field_62f38610cb0fc',
			'label' => 'Select Level Of Post To Display',
			'name' => 'partners_post_level_for_display',
			'type' => 'select',
			'instructions' => 'Select Level Of Post for displaying in Main Post Page',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Level 0' => 'Level 0',
				'Level 1' => 'Level 1',
				'Level 2' => 'Level 2',
			),
			'default_value' => 'Level 0',
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),*/
		array(
			'key' => 'field_62f10b663d029',
			'label' => 'SubTitle - For Our Partners Page Only',
			'name' => 'partners_subtitle',
			'type' => 'text',
			'instructions' => 'The field will only be available for "Our Partners" page.	Values on other pages have no effect.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f150484395e',
			'label' => 'Excerpt Title - To Display On LeftNav Menu',
			'name' => 'summary_menu_title',
			'type' => 'text',
			'instructions' => 'The Title To Be Displayed On "Our Partners" Page Left Nav Menu, Leave Blank To Use Post Title.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f0edf175458',
			'label' => 'Excerpt Title - To Display On Our Partners Page',
			'name' => 'summary_title',
			'type' => 'text',
			'instructions' => 'The Title To Be Displayed On "Our Partners" Page Content Section, Leave Blank If Title Same As Post Title.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f0c01e29b7f',
			'label' => 'Excerpt - To Display On Our Partners Page',
			'name' => 'summary',
			'type' => 'wysiwyg',
			'instructions' => 'Write a small snippet to display on Our Partners Page Listing',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 0,
			'delay' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'partners',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

acf_add_local_field_group(array(
	'key' => 'group_62f615c2aec79',
	'title' => 'Our Partners - philanthropic',
	'fields' => array(
		array(
			'key' => 'field_62f616c40fb52',
			'label' => 'Address Icon',
			'name' => 'our_partners_address_icon',
			'type' => 'text',
			'instructions' => 'Image icon for the image next to details',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-write.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f617ce0fb53',
			'label' => 'Address Title',
			'name' => 'our_partners_address_title',
			'type' => 'text',
			'instructions' => 'The address name or headline.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Lynn Moore, Institutional Giving',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f618360fb54',
			'label' => 'Address Details',
			'name' => 'our_partners_address_details',
			'type' => 'wysiwyg',
			'instructions' => 'Address Details including street name, pincode.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Consumer Reports<br>101 Truman Ave<br>Yonkers, NY 10703<br><br>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_62f6195743cea',
			'label' => 'Call Us Icon',
			'name' => 'our_partners_call_us_icon',
			'type' => 'text',
			'instructions' => 'Icon for call us section',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-call.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f619c8e2298',
			'label' => 'Call Us Title',
			'name' => 'our_partners_call_us_title',
			'type' => 'text',
			'instructions' => 'Call Us Title',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Call Us',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61a5ae2299',
			'label' => 'Call Us Details',
			'name' => 'our_partners_call_us_details',
			'type' => 'wysiwyg',
			'instructions' => 'Details / Phone numbers for Call Us',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '<a href="tel:914-378-2958">914-378-2958</a> (Direct Line)<br>
<a href="tel:877-275-3425">877-275-3425</a><br><br>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_62f61aef8d45d',
			'label' => 'Email Icon',
			'name' => 'our_partners_email_icon',
			'type' => 'text',
			'instructions' => 'Icon for the email',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-email.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61b318d45e',
			'label' => 'Email Title',
			'name' => 'our_partners_email_title',
			'type' => 'text',
			'instructions' => 'Title of the email',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Email Us',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61c0f8d45f',
			'label' => 'Email Details',
			'name' => 'our_partners_email_details',
			'type' => 'email',
			'instructions' => 'Email for contact.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'development@cr.consumer.org',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_62f62259e0642',
			'label' => 'Disclaimer',
			'name' => 'our_partners_philanthropic_disclaimer',
			'type' => 'wysiwyg',
			'instructions' => 'Disclaimer Text',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '<i>Note: As the Ford Foundation says on its <a href="https://www.fordfoundation.org/about/about-ford/">website</a>, it is not connected to the Ford Motor Company. They are two separate and legally unrelated entities.</i>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'partners',
			),
			array(
				'param' => 'post',
				'operator' => '==',
				'value' => '1025',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

endif;	


/**
 * Create post types for adding partner listings 
 */
/*function post_type_partner_listings(){

    // partner listings
    $args = array(
        'labels' => array(
            'name' => __('Add Partner Listings', 'textdomain'),
        ),
        'hierarchical' => false,
        'public' => true,
        'has_archive' => false, 
        'show_in_menu' => 'edit.php?post_type=partners',  
        'can_export'  => true,              
        'menu_icon' => 'dashicons-id-alt',
        'supports' => array( 'title', 'editor', 'custom-fields'),
        'capability_type' => 'post',
        //'show_in_rest' => true        
    );
    register_post_type('partner_listing', $args);
}
add_action('init', 'post_type_partner_listings'); 

/*function add_submenu_partner_listings(){
 add_submenu_page('edit.php?post_type=partners','Partner Listings','Add Partner Listings',"manage_options",
 		   'edit.php?post_type=partner_listing','','');    
}
add_action('admin_menu', 'add_submenu_partner_listings');*


/**
 * Add Metaboxes for selection of partner custom post to add to. 
 *
add_action( 'add_meta_boxes', 'add_to_partners_post_meta_box' );
function add_to_partners_post_meta_box(){ 
  add_meta_box("list_main_partners", __( "Select Partner Post To Display", 'textdomain'), "list_partner_main_posts", "partner_listing", "side", "low"); 
  add_meta_box("partner_listing_url", __( "Partner Listing Url :", 'textdomain'), "partner_listing_url_details", "partner_listing", "normal", "low");   
}

function list_partner_main_posts(){
   global $post;
   $selected_partner_pid = get_post_meta($post->ID, 'partner_main_post_pid', true); 
   if(empty($selected_partner_pid)){ $selected_partner_pid = 0; } 
   // fetch all partner posts 
   $partnersloop = new WP_Query(array('post_type'=>'partners','posts_per_page'=>-1,
    	  			       'post_status'=>'publish','orderby'=>'title','order'=>'ASC'));  			       
   echo "<select name='sel_main_partner_post'>";
   if($selected_partner_pid == 0){ echo '<option value="0" selected="selected">None Selected</option>'; }
   else{ echo '<option value="0">None Selected</option>'; }   
   foreach ($partnersloop->posts as $partner) { 
     if($partner->post_name == "landingpage"){ continue; }
     if($selected_partner_pid == $partner->ID) { $sel_partner = 'selected="selected"'; } else { $sel_partner = ''; }
     echo '<option value="'.$partner->ID.'" '.$sel_partner.'>'.$partner->post_title.'</option>';
   }
   echo "</select>";
}

function partner_listing_url_details(){
   global $post;
   $partner_listing_url = get_post_meta($post->ID, 'partner_listing_url_det', true);
   if(empty($partner_listing_url)){
      echo '<input type="text" name="partner_listing_url_value" value="" style="width:100%;" />';  
   } else {
      echo '<input type="text" name="partner_listing_url_value" value="'.$partner_listing_url.'"  style="width:100%;"/>';  
   }  
}

add_action('save_post', 'partner_listing_save');
function partner_listing_save(){
  global $post;
  if ( !empty($post) ) {
   if($post->post_type == 'partner_listing'){
     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
     if($_POST["sel_main_partner_post"] != 0){ 
       update_post_meta($post->ID, "partner_main_post_pid", $_POST["sel_main_partner_post"]); 
     }
     if($_POST["partner_listing_url_value"] != ''){ 
	update_post_meta($post->ID, "partner_listing_url_det", $_POST["partner_listing_url_value"]); 
     }     
   }//endif posttype == partner_listing
  }//endif !empty post 
}
*/
------------------------------------------------
type 2
--------
<?php

/**
 * Create Post Type - Partners
 */
function post_type_partners() {

    $args = array(
        'labels' => array(
            'name' => 'Our Partners',        
         ),
        'hierarchical' => true,
        'menu_position' => 30,        
        'public' => true,
        'has_archive' => true, 
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'custom-fields', 'page-attributes'), 
        'capability_type'  => 'post',
        'rewrite' => array('slug' => '/about-us/our-partners'),
    );
    register_post_type('partners', $args); 
    
}
add_action('init', 'post_type_partners'); 

/**
 * Add Meta Boxes for Partner Post
 */
add_action( 'add_meta_boxes', 'add_meta_boxes_to_partners_post' );
function add_meta_boxes_to_partners_post(){ 
  add_meta_box("select_partner_post_level", __( "Select Level Of Post To Display", 'textdomain'), 
  		"select_partner_post_level", "partners", "side", "low"); 		   
}

function select_partner_post_level(){
   global $post;
   $curr_post_level = get_post_meta($post->ID, 'partners_post_level_for_display', true); 
   if(empty($curr_post_level)){ $curr_post_level = 'Level 0'; }  			       
   echo '<select name="sel_partner_post_level" style="width:100%;">';
   if($curr_post_level == 'Level 0'){ echo '<option value="Level 0" selected="selected">Level 0</option>'; }
   else{ echo '<option value="Level 0">Level 0</option>'; }    
   if($curr_post_level == 'Level 1'){ echo '<option value="Level 1" selected="selected">Level 1</option>'; }
   else{ echo '<option value="Level 1">Level 1</option>'; }    
   if($curr_post_level == 'Level 2'){ echo '<option value="Level 2" selected="selected">Level 2</option>'; }
   else{ echo '<option value="Level 2">Level 2</option>'; }    
   echo "</select>";
}

add_action('save_post', 'on_click_partner_post_save');
function on_click_partner_post_save(){
  global $post;
  if ( !empty($post) ) { 
   if($post->post_type == 'partners'){ 
     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
     if($_POST["sel_partner_post_level"] != -1){ 
       update_post_meta($post->ID, "partners_post_level_for_display", $_POST["sel_partner_post_level"]); 
     } 
   }//endif posttype == partner_listing
  }//endif !empty post 
} 

/*-------------------------------------------
 * Custom columns manage
 *-------------------------------------------*/
add_filter('manage_partners_posts_columns' , 'partners_custom_post_columns');
function partners_custom_post_columns($columns){
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "title" => __( 'Title','textdomain' ),
      'status' => __( 'Status','textdomain' ), 
      'level' => __('Level', 'textdomain'), 
      'order' => __('Menu Order', 'textdomain'),	      
      "date" => __('Created On','homey-child'),
    );
   return $columns; 
}

add_action( 'manage_partners_posts_custom_column', 'load_partners_custom_post_columns', 10, 2 );
function load_partners_custom_post_columns( $column, $post_id ) {
  $cpost = get_post($post_id);
  switch ( $column ) { 
     case 'status' : 
     	echo $cpost->post_status;
     	break; 
     case 'level' : 
     	$clevel = get_post_meta( $post_id, 'partners_post_level_for_display', true); 
     	$html = '<select name="modify_current_level" id="id_modify_current_level">'; 
     	if($clevel === 'Level 0'){ $html .= '<option value="Level 0" data-pid="'.$post_id.'" selected="selected">Level 0</option>'; }
     	else{ $html .= '<option value="Level 0" data-pid="'.$post_id.'">Level 0</option>'; }
     	if($clevel === 'Level 1'){ $html .= '<option value="Level 1" data-pid="'.$post_id.'" selected="selected">Level 1</option>'; }
     	else{ $html .= '<option value="Level 1" data-pid="'.$post_id.'">Level 1</option>'; } 
     	if($clevel === 'Level 2'){ $html .= '<option value="Level 2" data-pid="'.$post_id.'" selected="selected">Level 2</option>'; }
     	else{ $html .= '<option value="Level 0" data-pid="'.$post_id.'">Level 2</option>'; } 	
     	$html .= '</select>'; 
     	echo $html; 
     	break; 
     case 'order' : 
     	echo $cpost->menu_order; 
     	break;	
  }
}

add_action('admin_footer', 'add_partners_script_to_admin_footer');
function add_partners_script_to_admin_footer(){ 
  global $pagenow; 
  if(!is_admin()){ return; }
  $post_type = (isset($_GET['post_type'])) ? trim($_GET['post_type']) : '';
  if($post_type == 'partners' && $pagenow=='edit.php'){
?>
<script type="text/javascript">
jQuery(document).on('change','select[name="modify_current_level"]',function(){ 
  var $admin_ajaxurl = ajaxurl; 
  var selectedEle = jQuery(this);
  var selectVal = jQuery(this).val(); var postid = 0;
  var options = jQuery(this).children(); 
  jQuery.each( options, function(key, val){ 
    if(jQuery(val).text() === selectVal){ postid = jQuery(val).attr('data-pid'); }
  }); 
  console.log(selectVal); console.log(postid);
  jQuery.ajax({
     url: $admin_ajaxurl, 
     method: 'POST',
     data: { 
      'selectval': selectVal,
      'pid': postid, 
      'action': 'modify_partner_post_level', 
     },
     dataType: "json", 
     beforeSend: function(){ console.log(jQuery(this));
	jQuery(selectedEle).parent().find('p.loading').remove();
	jQuery(selectedEle).parent().append('<p class="loading" style="color:#000;font-size:11px;">Saving...</p>');
     },
     success: function(response){ //console.log(response);
       if(response.success){ 
	 jQuery(selectedEle).parent().find('p.loading').remove();
	 jQuery(selectedEle).parent()
	 	.append('<p class="loading" style="color:#000;font-size:11px;font-weight:bold;">'+response.msg+'</p>'); 
       }else{
	 jQuery(selectedEle).parent().find('p.loading').remove();
	 jQuery(selectedEle).parent().append('<p class="loading" style="color:#F00;font-size:11px;">'+response.msg+'</p>');
       }
     },
     error: function(xhr, status, error){
	var err = eval("(" + xhr.responseText + ")");
        console.log(err.Message);
     },
     complete: function(){
	setTimeout(function(){
	   jQuery(selectedEle).parent().find('p.loading').remove();  
        }, 3000);	            
     },
   }); //end ajax
});
</script>
<?php
  } //endif post_type=='partners' && pagenow=='edit.php'
}

//add_action( 'wp_ajax_nopriv_modify_partner_post_level', 'func_modify_partner_post_level' );  
add_action( 'wp_ajax_modify_partner_post_level', 'func_modify_partner_post_level' );  
function func_modify_partner_post_level() { //var_dump($_POST); exit; 
  $level = isset($_POST['selectval']) ? trim($_POST['selectval']) : ''; 
  $postid = isset($_POST['pid']) ? trim($_POST['pid']) : ''; 
  if(!empty($level) && !empty($postid)){ 
     $cpost = get_post($postid); 
     update_post_meta($cpost->ID, 'partners_post_level_for_display', $level);
     $response = 'Updated';
     echo json_encode( array('success' => true, 'msg' => $response));
  }else{   
    $response = 'Error: Level Or PostId Empty';
    echo json_encode( array('success' => false, 'msg' => $response));
  }
  die(); 
}  


/*add_filter( 'manage_edit-partners_sortable_columns', 'set_partners_custom_sort_columns' );
function set_partners_custom_sort_columns($columns){ 
  $columns['level'] = 'level';
  return $columns;
}

add_action( 'pre_get_posts', 'partners_posts_orderby_level' );
function partners_posts_orderby_level($query) {
  if(!is_admin() || !$query->is_main_query()){ return; }
  if ($query->get( 'orderby') == 'level'){ 
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'partners_post_level_for_display' );
    $query->set( 'meta_type', 'char' );
  }
}*/

/**-----------------------------------*
 * Add custom filters to partners
 *------------------------------------*/
add_action( 'restrict_manage_posts', 'partners_manage_post_filters');
function partners_manage_post_filters(){ 
  if (!is_admin()){ return; } 
  global $wpdb;
  $post_type = (isset($_GET['post_type'])) ? trim($_GET['post_type']) : '';
  if ($post_type == 'partners'){
    $options = array(); $html = '';
    $results = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'partners'", OBJECT); 
    foreach($results as $cpid){ 
      $cpost = get_post($cpid);
      $mvalue = get_post_meta($cpost->ID, 'partners_post_level_for_display', true); 
      if(!in_array($mvalue,$options)){ $options["$mvalue"] = $mvalue; }
    }
    //add the filter element
    ksort($options); 
    $curr_get_val = isset($_GET['filter_partners_by_level']) ? $_GET['filter_partners_by_level'] : 0; 
    $html .= '<select name="filter_partners_by_level" id="id_filter_partners_by_level">'; 
    if($curr_get_val == 0){ $html .= '<option value="" selected="selected">All Levels</option>'; }
    else{ $html .= '<option value="">All Levels</option>'; }
    foreach($options as $key => $value) {
      if($value === $curr_get_val){ $html .= '<option value="'.$key.'" selected="selected">'.$value.'</option>'; }
      else{ $html .= '<option value="'.$key.'">'.$value.'</option>'; }
    }
    $html .= '</select>'; 
    echo $html;   
  }     
}

add_filter( 'parse_query', 'partners_parse_query_for_levels' );
function partners_parse_query_for_levels($query){
  if(!is_admin()){ return; }
  global $pagenow;
  $post_type = (isset($_GET['post_type'])) ? trim($_GET['post_type']) : '';
  if($post_type == 'partners' && $pagenow=='edit.php' && 
     isset($_GET['filter_partners_by_level']) && !empty($_GET['filter_partners_by_level'])) { 
    if($_GET['filter_partners_by_level'] !== 0){
      $query->query_vars['meta_key'] = 'partners_post_level_for_display'; 
      $query->query_vars['meta_value'] = trim($_GET['filter_partners_by_level']); 
    }	 
  }
}

/**
 * Add ACF Fields to the above custom post type. 
 * Will be used by ACF
 */
if(function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_62f0af49d92ed',
	'title' => 'Our Partners - Common',
	'fields' => array(
		/*array(
			'key' => 'field_62f38610cb0fc',
			'label' => 'Select Level Of Post To Display',
			'name' => 'partners_post_level_for_display',
			'type' => 'select',
			'instructions' => 'Select Level Of Post for displaying in Main Post Page',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Level 0' => 'Level 0',
				'Level 1' => 'Level 1',
				'Level 2' => 'Level 2',
			),
			'default_value' => 'Level 0',
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),*/
		array(
			'key' => 'field_62f10b663d029',
			'label' => 'SubTitle - For Our Partners Page Only',
			'name' => 'partners_subtitle',
			'type' => 'text',
			'instructions' => 'The field will only be available for "Our Partners" page.	Values on other pages have no effect.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f150484395e',
			'label' => 'Excerpt Title - To Display On LeftNav Menu',
			'name' => 'summary_menu_title',
			'type' => 'text',
			'instructions' => 'The Title To Be Displayed On "Our Partners" Page Left Nav Menu, Leave Blank To Use Post Title.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f0edf175458',
			'label' => 'Excerpt Title - To Display On Our Partners Page',
			'name' => 'summary_title',
			'type' => 'text',
			'instructions' => 'The Title To Be Displayed On "Our Partners" Page Content Section, Leave Blank If Title Same As Post Title.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f0c01e29b7f',
			'label' => 'Excerpt - To Display On Our Partners Page',
			'name' => 'summary',
			'type' => 'wysiwyg',
			'instructions' => 'Write a small snippet to display on Our Partners Page Listing',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 0,
			'delay' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'partners',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

acf_add_local_field_group(array(
	'key' => 'group_62f615c2aec79',
	'title' => 'Our Partners - philanthropic',
	'fields' => array(
		array(
			'key' => 'field_62f616c40fb52',
			'label' => 'Address Icon',
			'name' => 'our_partners_address_icon',
			'type' => 'text',
			'instructions' => 'Image icon for the image next to details',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-write.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f617ce0fb53',
			'label' => 'Address Title',
			'name' => 'our_partners_address_title',
			'type' => 'text',
			'instructions' => 'The address name or headline.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Lynn Moore, Institutional Giving',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f618360fb54',
			'label' => 'Address Details',
			'name' => 'our_partners_address_details',
			'type' => 'wysiwyg',
			'instructions' => 'Address Details including street name, pincode.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Consumer Reports<br>101 Truman Ave<br>Yonkers, NY 10703<br><br>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_62f6195743cea',
			'label' => 'Call Us Icon',
			'name' => 'our_partners_call_us_icon',
			'type' => 'text',
			'instructions' => 'Icon for call us section',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-call.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f619c8e2298',
			'label' => 'Call Us Title',
			'name' => 'our_partners_call_us_title',
			'type' => 'text',
			'instructions' => 'Call Us Title',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Call Us',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61a5ae2299',
			'label' => 'Call Us Details',
			'name' => 'our_partners_call_us_details',
			'type' => 'wysiwyg',
			'instructions' => 'Details / Phone numbers for Call Us',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '<a href="tel:914-378-2958">914-378-2958</a> (Direct Line)<br>
<a href="tel:877-275-3425">877-275-3425</a><br><br>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
		array(
			'key' => 'field_62f61aef8d45d',
			'label' => 'Email Icon',
			'name' => 'our_partners_email_icon',
			'type' => 'text',
			'instructions' => 'Icon for the email',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'https://cdn.cr.org/etc/designs/cr/images/contactoptions/ICN-ART-email.svg',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61b318d45e',
			'label' => 'Email Title',
			'name' => 'our_partners_email_title',
			'type' => 'text',
			'instructions' => 'Title of the email',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Email Us',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_62f61c0f8d45f',
			'label' => 'Email Details',
			'name' => 'our_partners_email_details',
			'type' => 'email',
			'instructions' => 'Email for contact.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'development@cr.consumer.org',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_62f62259e0642',
			'label' => 'Disclaimer',
			'name' => 'our_partners_philanthropic_disclaimer',
			'type' => 'wysiwyg',
			'instructions' => 'Disclaimer Text',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '<i>Note: As the Ford Foundation says on its <a href="https://www.fordfoundation.org/about/about-ford/">website</a>, it is not connected to the Ford Motor Company. They are two separate and legally unrelated entities.</i>',
			'tabs' => 'all',
			'toolbar' => 'full',
			'media_upload' => 1,
			'delay' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'partners',
			),
			array(
				'param' => 'post',
				'operator' => '==',
				'value' => '1025',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

endif;	

=============================================
 functions.php 
-------------------
if(isset($_POST['action'])){
  if($_POST['action'] == 'fetchFilterDataFrom'){ func_fetchFilterDataFromCRO(); }
  if($_POST['action'] == 'fetchFilters'){ fetchFilters(); }  
  if($_POST['action'] == 'fetchListingPage'){ fetchListingPage(); } 
  //search 
  if($_POST['action'] == 'fetchSolrSearchResultsByPage'){ func_fetchSolrSearchResultsByPage(); }
  if($_POST['action'] == 'fetchSearchPage'){ fetchSearchPages(); }  
}
----------------
function searchsorter($a, $b){ 
  $fEle = strtolower($a->name); $nEle = strtolower($b->name); 
  if($fEle < $nEle){ return -1; } 
  if($fEle > $nEle){ return 1; }
  return 0;
} 

//add_action('wp_ajax_nopriv_fetchSearchResultsByPage', 'func_fetchSearchResultsByPage');  
//add_action('wp_ajax_fetchSearchResultsByPage', 'func_fetchSearchResultsByPage');  
function func_fetchSearchResultsByPage(){ 
	$tagHtml = ''; $yearHtml = ''; 
	$searchUrl = $_POST['searchUrl']; 
	$OrigFilters = $_POST['filters']; 
	$MOrigFilters = unserialize(stripslashes($_POST['filters'])); 
  	//fetch the data from apiUrl
	if(!empty($searchUrl)){ 
  		$wpCurl = new WP_Http_Curl(); $reqArray = [];
  		for($cnt=1; $cnt<4; $cnt++){
  		  $reqArray = $wpCurl->request($searchUrl,array('timeout'=>60,'redirection'=>60)); 
  		  if(is_wp_error($reqArray)){ continue; }else{ break; }
  		}	   		
   		if(!is_wp_error($reqArray)){
     	  if(!empty($reqArray['body'])){ 
     		$result = json_decode($reqArray['body']);
     		if(($result->status == 'success') && ($result->count > 0)){
			  if(!empty($result->refinements)){     		
     		  	$filters = $result->refinements; 
     		  	$filters_json = serialize($filters);
     		  	$totalCnt = $result->count;
     		  	foreach($filters as $findex => $filtersec){ 
     		  	  $selection_name = ''; 
     		  	  if($filtersec->name == 'Date'){ 
     		  	     $selection_name = 'search_date_filter'; 
     		  	     usort($filtersec->values, "searchsorter");
     		  	  } else if($filtersec->name == 'Area'){ 
     		  	     $selection_name = 'search_category_filter'; 
     		  	     usort($filtersec->values, "searchsorter");
     		  	  }  		  	  
     		  	  foreach($filtersec->values as $filter){ 
     		  	    if($selection_name == 'search_category_filter'){
	  				  $tagHtml .= '<li><span class="tag-select">';
	  				  $tagHtml .= '<input type="checkbox" name="search_category_filter" id="'.$filter->name.'" search-link="'.$filter->uri.'" data-text="'.$filter->name.'" nrec="'.$filter->count.'" value="'.$filter->uri.'"/></span>';
	  				  $tagHtml .= '<label class="tag-name" for="'.$filter->name.'">'.$filter->name.'</label></li>';     		  		
     		  	    }
     		  	    if($selection_name == 'search_date_filter'){    		  	   
	  				  $yearHtml .= '<li><span class="tag-select">';
	  				  $yearHtml .= '<input type="checkbox" name="search_date_filter" id="'.$filter->name.'" search-link="'.$filter->uri.'" data-text="'.$filter->name.'"   nrec="'.$filter->count.'" value="'.$filter->uri.'"/></span>';
	  				  $yearHtml .= '<label class="tag-name" for="'.$filter->name.'">'.$filter->name.'</label></li>'; 
				    }
     		  	  }
     		    }//end foreach
     		    if($tagHtml == ''){ 
     		      $tagHtml .= '<li><span class="tag-select">';
	  			  $tagHtml .= '<label class="tag-name"> No Available Tag Filters</label></li>';  
	  			}
     		    if($yearHtml == ''){ 
	  			  $yearHtml .= '<li><span class="tag-select">';
			  	  $yearHtml .= '<label class="tag-name"> No Available Date Filters</label></li>';     		    
     		    }
     		  }//endif (!empty($result->refinements) 
			  if(!isset($result->refinements)){ //contains selections 
     		  	$totalCnt = $result->count; 
     		  	foreach($MOrigFilters as $findex => $filtersec){ 
     		  	  usort($filtersec->values, "searchsorter");           		  	
     		  	  foreach($filtersec->values as $filter){ 
     		  	    if($findex == 0){ 
	  				  $tagHtml .= '<li><span class="tag-select">';
	  				  $tagHtml .= '<input type="checkbox" name="search_category_filter" id="'.$filter->name.'" search-link="'.$filter->uri.'" data-text="'.$filter->name.'" nrec="'.$filter->count.'" value="'.$filter->uri.'"/></span>';
	  				  $tagHtml .= '<label class="tag-name" for="'.$filter->name.'">'.$filter->name.'</label></li>';      		  		
     		  	    }
     		  	    if($findex == 1){      		  	   
	  				  $yearHtml .= '<li><span class="tag-select">';
	  				  $yearHtml .= '<input type="checkbox" name="search_date_filter" id="'.$filter->name.'" search-link="'.$filter->uri.'" data-text="'.$filter->name.'"   nrec="'.$filter->count.'" value="'.$filter->uri.'"/></span>';
	  				  $yearHtml .= '<label class="tag-name" for="'.$filter->name.'">'.$filter->name.'</label></li>'; 
				    }
     		  	  }
     		    }//end foreach 
     		    if($tagHtml == ''){ 
     		      $tagHtml .= '<li><span class="tag-select">';
	  			  $tagHtml .= '<label class="tag-name"> No Available Tag Filters</label></li>';  
	  			}
     		    if($yearHtml == ''){ 
	  			  $yearHtml .= '<li><span class="tag-select">';
			  	  $yearHtml .= '<label class="tag-name"> No Available Date Filters</label></li>';     		    
     		    }     		     		    
     		  }  
     		}else{ //if search result is not success 
     			$totalCnt = $result->count;
	  			$tagHtml .= '<li><span class="tag-select">';
	  			$tagHtml .= '<label class="tag-name"> No Available Tag Filters</label></li>'; 
	  			$yearHtml .= '<li><span class="tag-select">';
			  	$yearHtml .= '<label class="tag-name"> No Available Date Filters</label></li>';
			  	$filters_json = ''; 	  			     		
     		}
	        echo json_encode(array('success' => true,'tags' => $tagHtml,'years' => $yearHtml,'count' => $totalCnt, 
	        						'filters' => $filters_json));
     	  }else {
	        $response = 'Filter Data Not Available !!!';
	        echo json_encode( array('success' => false, 'msg' => $response) );	 
     	  } 
   		}else{
	  	   $response = json_encode($reqArray->get_error_messages()); 
	  	   echo json_encode( array('success' => false, 'msg' => $response) );	      
   		} 
	} else{
	  $response = 'Error: Cannot Fetch, URL Not Valid'; 
	  echo json_encode( array('success' => false, 'msg' => $response) );	      
    } 
    die(); //wp_die();
}
--------------------------
//add_action('wp_ajax_nopriv_fetchFilterData', 'func_fetchFilterData');  
//add_action('wp_ajax_fetchFilterData', 'func_fetchFilterData'); 

function sorter($a, $b){ 
  $fEle = strtolower($a->title); $nEle = strtolower($b->title); 
  if($fEle < $nEle){ return -1; } 
  if($fEle > $nEle){ return 1; }
  return 0;
} 

function func_fetchFilterData(){ 
	$tagHtml = ''; $yearHtml = ''; 
	$fetchFilterUrl = $_POST['filterUrl']; 
  	//fetch the data from apiUrl
	if(!empty($fetchFilterUrl)){ 
  		$wpCurl = new WP_Http_Curl(); $reqArray = []; 
  		for($cnt=1; $cnt<4; $cnt++){
  		  $reqArray = $wpCurl->request($fetchFilterUrl,array('timeout'=>60,'redirection'=>60)); 
  		  if(is_wp_error($reqArray)){ continue; } else{ break; }
  		} 
   		if(!is_wp_error($reqArray)){
     	  if(!empty($reqArray['body'])){ 
     		$result = json_decode($reqArray['body']);
     		$totalCnt = $result[0]->count; 
			$tags = $result[1]->tags; 
			usort($tags, "sorter"); 			
			$years = $result[2]->years; 
			if(!empty($tags)){ 
			  foreach($tags as $tag){ 
			    if($tag->count > 0){			  
	  			 $tagHtml .= '<li><span class="tag-select">';
	  			 $tagHtml .= '<input type="checkbox" id="'.$tag->filter.'" name="category_filter" data-text="'.$tag->title.'" nrec="'.$tag->count.'" value="'.$tag->filter.'"/></span>';
	  			 $tagHtml .= '<label for="'.$tag->filter.'" class="tag-name">'.$tag->title.'</label></li>'; 
	  			} 				
			  }
			}
			if(!empty($years)){ 			
			  foreach($years as $year){ 
			   if($year->count > 0){			  			
	  			$yearHtml .= '<li><span class="tag-select">';
	  			$yearHtml .= '<input type="checkbox" id="'.$year->filter.'" name="date_filter" data-text="'.$year->filter.'" nrec="'.$year->count.'" value="'.$year->filter.'"/></span>';
	  			$yearHtml .= '<label class="tag-name" for="'.$year->filter.'">'.$year->filter.'</label></li>'; 
	  		   }	
			  }
			}
	        echo json_encode(array('success' => true,'tags' => $tagHtml,'years' => $yearHtml,'count' => $totalCnt, 'url'=> $fetchFilterUrl)); 
     	  }else {
	        $response = 'Filter Data Not Available !!!';
	        echo json_encode( array('success' => false, 'msg' => $response) );	 
     	  } 
   		}else{
	  	   $response = json_encode($reqArray->get_error_messages()); 
	  	   echo json_encode( array('success' => false, 'msg' => $response) );	      
   		} 
	} else{
	  $response = 'Error: Cannot Fetch, URL Not Valid'; 
	  echo json_encode( array('success' => false, 'msg' => $response) );	      
    }  
    die(); //wp_die();
}
-------------------------------
javascript 
------------
/**-------------------------------------------------
 * fetch news releases data from the api provided. 
 *--------------------------------------------------*/
var filterUrl = pressreleases_ajax.main_site; 
var documentUrl = pressreleases_ajax.main_site;
var searchapi = pressreleases_ajax.main_site'; 
var stageorprod = ''; var activePage = 0;
if(window.location.hostname.indexOf('testreports.org') > -1){ stageorprod = true; }
else if(window.location.hostname.indexOf('test-content') > -1){ stageorprod = true; }
else{ stageorprod = false; } 
var srchurl = ""; var docurl = ""; var fltrurl = "";

console.log('stageorprod - ' + stageorprod);

/** reset filters on page load */ 
jQuery(document).on('ready', function(){ 
  if(window.location.search == ''){
    docurl = documentUrl; fltrurl = filterUrl;
	//load_data_after_check();
	localStorage.setItem('tagfilter',''); localStorage.setItem('yearfilter',''); 
	fetch_filteranddocument_data(filterUrl,documentUrl,'page-load');	 
  }else if(window.location.search.indexOf('query') == -1){  
  	filterUrl = filterUrl+window.location.search;
  	documentUrl = documentUrl+window.location.search; 
  	docurl = documentUrl; fltrurl = filterUrl;
     // alert(filterUrl+" : "+documentUrl);
    fetch_filteranddocument_data(filterUrl,documentUrl,'page-reload');
  }else if(window.location.search.indexOf('query') > -1){ 
    let query = ''; let strslice = ''; 
    //check if valid string 
	let strlen = window.location.search.length; 
	let f_OccurAnd = window.location.search.indexOf('&');
	let f_OccurEq = window.location.search.indexOf('='); 
	if(f_OccurAnd == -1){ strslice = window.location.search.slice((f_OccurEq+1),strlen); }
	else{ strslice = window.location.search.slice((f_OccurEq+1),f_OccurAnd); } 
	if(strslice != ''){ 
	  if(window.location.search.indexOf('&') > -1){ //contains selections
        if(window.location.search.indexOf('query') > -1){ 
       	   query = window.location.search; 
        }
  	  	let search = searchapi+query+'&page=0'; //console.log(search);
      	fetch_filterandsearchdocument_data(search, 'page-reload'); 
      }else{		
	  	sessionStorage.setItem('osearchQuery', window.location.search); 
   	  	sessionStorage.setItem('searchtagfilter',''); 
      	sessionStorage.setItem('searchyearfilter',''); 	  	 	
      	if(window.location.search.indexOf('query') > -1){ 
      	   query = window.location.search; 
      	}
  	  	let search = searchapi+query+'&page=0'; //console.log(search);
      	fetch_filterandsearchdocument_data(search, 'page-load'); 
      }	
	}else{
	  console.log('search string empty.');
	  jQuery('#searchfilterButton').css({'display':'none'});
	  jQuery('.latest-items').empty()
	  		.append('<div class="no-item"><p>Please check your spelling or search for another keyword</p></div>'); 
	  return false;	 
	}   
  }//endif window.location.search.indexOf('query') 
});

/** on window load */
function load_data_after_check(){
  //check if previous data present 
  if(sessionStorage.getItem('tagfilter') !== null && sessionStorage.getItem('tagfilter') !== ""){
    fetch_filteranddocument_data(filterUrl,documentUrl,'page-reload');  	
  }else if(sessionStorage.getItem('yearfilter') !== null && sessionStorage.getItem('yearfilter') !== ""){
  	fetch_filteranddocument_data(filterUrl,documentUrl,'page-reload');	
  } else {
    fetch_filteranddocument_data(filterUrl,documentUrl,'page-load'); 
  }
}

jQuery(document).on('change', 'input[name="category_filter"]', function(){
  let cfilter = jQuery(this); 
  let ischecked = jQuery(cfilter).prop('checked'); 
  if(ischecked){ cfilter.css({'accentColor':'#000'}); }
});

jQuery(document).on('change', 'input[name="date_filter"]', function(){
  let cfilter = jQuery(this); 
  let ischecked = jQuery(cfilter).prop('checked'); 
  if(ischecked){ cfilter.css({'accentColor':'#000'}); }
});

/** apply filter segment */
jQuery(document).on('click', 'button[name="apply-filters"]', function(){ 
  var tagQuery = ''; var yearQuery = ''; var finalQuery = ''; var newFilterAdded = 0; 
  var btnEle = jQuery(this); 
  var mainparent = jQuery(btnEle).parents('.filtermodal-content'); 
  var checkedTags = jQuery(mainparent).find('input[name="category_filter"]:checked'); 
  var checkedYears = jQuery(mainparent).find('input[name="date_filter"]:checked'); 
  var filterlistingTop = jQuery('#filter-listings'); 
  if((checkedTags.length == 0) && (checkedYears.length == 0)){ 
  	jQuery("#pressreleases-filter").css({'display':'none'});  	
  }
  jQuery(filterlistingTop).empty();    
  if(checkedTags.length > 0){
  	jQuery.each(checkedTags, function(index, tagvalue){  
  	   let tagText = jQuery(tagvalue).attr('data-text'); 	
       if((index+1) == checkedTags.length){ tagQuery += tagvalue.defaultValue; 
       }else{ tagQuery += tagvalue.defaultValue +','; } 
       //list the filters on the top of page
       if(jQuery(filterlistingTop).children().length == 0) {
       	  jQuery(filterlistingTop).append('<div class="filter-div" name="'+tagvalue.defaultValue+'"><span class="filter-div-text">'+tagText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>');
	   }else{
	   	 let sel_filters = jQuery(filterlistingTop).children(); let found = 0;
		 jQuery.each(sel_filters, function(index, filter){ 
			if(jQuery(filter).attr('name') == jQuery(tagvalue).attr('value')){ found = 1; }
	     });
	     if(found == 0){
           jQuery(filterlistingTop).append('<div class="filter-div" name="'+tagvalue.defaultValue+'"><span class="filter-div-text">'+tagText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>');		     
	     }
	   }
  	}); 
  }
  if(checkedYears.length > 0){	
  	jQuery.each(checkedYears, function(index, yearval){ 
  	   let yearText = jQuery(yearval).attr('data-text');  
       if((index+1) == checkedYears.length){ yearQuery += yearval.defaultValue; 	  
       }else{ yearQuery += yearval.defaultValue +','; } 
       //list the filters on the top of page
       if(jQuery(filterlistingTop).children().length == 0) {
       	  jQuery(filterlistingTop).append('<div class="filter-div" name="'+yearval.defaultValue+'"><span class="filter-div-text">'+yearText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>'); 
	   }else{
	   	 let sel_filters = jQuery(filterlistingTop).children(); let found = 0; 
		 jQuery.each(sel_filters, function(index, filter){ 	
			if(jQuery(filter).attr('name') == jQuery(yearval).attr('value')){ found = 1; }
	     });
	     if(found == 0){
           jQuery(filterlistingTop).append('<div class="filter-div" name="'+yearval.defaultValue+'"><span class="filter-div-text">'+yearText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>'); 
         }  	     
	   }       
  	}); 
  }	
  selectedFilterTags = tagQuery;
  selectedFilterYears = yearQuery;
  if(tagQuery != '' && yearQuery != ''){ 
  	finalQuery = 'tag='+tagQuery+'&years='+yearQuery; 
  	localStorage.setItem('tagfilter', tagQuery);
  	localStorage.setItem('yearfilter', yearQuery);
  } else if(yearQuery == '' && tagQuery != ''){ 
  	finalQuery = 'tag='+tagQuery; 
  	localStorage.setItem('tagfilter', tagQuery);
  	localStorage.setItem('yearfilter', '');  	
  }else if(tagQuery == '' && yearQuery != ''){ 
  	finalQuery = 'years='+yearQuery; 
  	localStorage.setItem('tagfilter', '');
  	localStorage.setItem('yearfilter', yearQuery);   	
  }else{ 
  	finalQuery = '';   	
  	localStorage.setItem('tagfilter', '');
  	localStorage.setItem('yearfilter', '');  
  } //console.log(finalQuery); 
  if(finalQuery != ''){ //change url and call ajax 
  	window.history.pushState({name:'Filter'},'News Releases','?'+finalQuery); 
    if(filterUrl.indexOf('?') > -1){ filterUrl = filterUrl.substring(0,filterUrl.indexOf('?')); }
    if(documentUrl.indexOf('?') > -1){ documentUrl = documentUrl.substring(0, documentUrl.indexOf('?')); }   	
  	let finalFilterUrl = filterUrl+'?'+finalQuery;
  	let finalDocUrl = documentUrl+'?'+finalQuery;
   	fetch_filteranddocument_data(finalFilterUrl,finalDocUrl,'apply-filter');
  } else {
    window.history.pushState({name:'Clear Filter'},'News Releases',window.location.pathname);
    window.location.search = ''; 
    window.location.href=window.location.href.replace('/?','/');
    fetch_filteranddocument_data(filterUrl,documentUrl,'remove-filters');  
  }
  /*loadFilters().then(function(response){
     renderPagination();
     loadPage(0);
   });*/ 
}); 

jQuery(document).on('click', 'a.btn-clear', function(){ 
  var tagQuery = ''; var yearQuery = ''; var finalQuery = ''; 
  var btnEle = jQuery(this); 
  var mainparent = jQuery(btnEle).parents('.filtermodal-content'); 
  var checkedTags = jQuery(mainparent).find('input[name="category_filter"]:checked'); 
  var checkedYears = jQuery(mainparent).find('input[name="date_filter"]:checked'); 
  var filterlistingTop = jQuery('#filter-listings');    
  if(checkedTags.length > 0){
  	jQuery.each(checkedTags, function(index, tagvalue){ 
	   jQuery(tagvalue).prop('checked', false); 
  	   //remove the filter from the top of page 
  	   /*if(jQuery(filterlistingTop).children().length > 0) {
    	  let sel_filters = jQuery(filterlistingTop).children(); 
		  jQuery.each(sel_filters, function(index, filter){ 	
	  		if(jQuery(filter).attr('name') != jQuery(tagvalue).attr("name")){ jQuery(filter).remove(); } 
		  });
  	   }*/  		  		
  	}); 
  }
  if(checkedYears.length > 0){	
  	jQuery.each(checkedYears, function(index, yearval){ 
	   jQuery(yearval).prop('checked', false);
  	   //remove the filter from the top of page 
  	   /*if(jQuery(filterlistingTop).children().length > 0) {
    	 let sel_filters = jQuery(filterlistingTop).children(); 
		 jQuery.each(sel_filters, function(index, filter){ 	
	  	   if(jQuery(filter).attr('name') != jQuery(yearval).attr("name")){ jQuery(filter).remove(); } 
		 });
  	   }*/  		 		
  	});  
  }	
  /*if(window.location.search != ''){
  	localStorage.setItem('tagfilter','');
  	localStorage.setItem('yearfilter','');    
    window.history.pushState({name:'Clear Filter'},'News Releases',window.location.pathname);
    fetch_filteranddocument_data(filterUrl,documentUrl,'remove-filters'); 
  }*/
  //jQuery("#pressreleases-filter").css({'display':'none'});
  /*selectedFilterTags = ''; selectedFilterYears = '';
    loadFilters().then(function(response){
      renderPagination();
      loadPage(0);
   });*/ 
});

/** Modify filter based on cross button on filter list */
jQuery(document).on('click', 'i.filter-close', function(){
  var tagQuery = ''; var yearQuery = ''; var finalQuery = ''; 
  var filterCloseButton = jQuery(this); 
  var cfilter = jQuery(filterCloseButton).parent(); 
  var mainparent = jQuery(document).find('.filtermodal-content');
  var checkedTags = jQuery(mainparent).find('input[name="category_filter"]:checked'); 
  var checkedYears = jQuery(mainparent).find('input[name="date_filter"]:checked'); 
  var filterlistingTop = jQuery('#filter-listings'); 
  //remove the filter from the top of page 
  if(jQuery(filterlistingTop).children().length > 0) {
    let sel_filters = jQuery(filterlistingTop).children(); 
	jQuery.each(sel_filters, function(index, filter){ 
	  if(jQuery(filter).attr('name') == jQuery(cfilter).attr('name')){ 
	  	jQuery(filterlistingTop).find('div[name="'+jQuery(cfilter).attr('name')+'"]').remove();
	  } 
	});
  }  		   
  //deselect the filter
  if(checkedTags.length > 0){
  	jQuery.each(checkedTags, function(index, tagvalue){ 
  	  if(jQuery(cfilter).attr('name') == jQuery(tagvalue).attr('value')){ 
  	    jQuery(tagvalue).prop('checked', false); 
  	  }
  	}); 
  }
  if(checkedYears.length > 0){	
  	jQuery.each(checkedYears, function(index, yearval){ 
  	  if(jQuery(cfilter).attr('name') == jQuery(yearval).attr('value')){ 
  	    jQuery(yearval).prop('checked', false); 
  	  } 
  	}); 
  }
  //reset the filter segments after deselection
  checkedTags = jQuery(mainparent).find('input[name="category_filter"]:checked');
  checkedYears = jQuery(mainparent).find('input[name="date_filter"]:checked');      
  //resume normal process 
  if(checkedTags.length > 0){
  	jQuery.each(checkedTags, function(index, tagvalue){  
       if((index+1) == checkedTags.length){ tagQuery += tagvalue.defaultValue; 
       }else{ tagQuery += tagvalue.defaultValue +','; } 
  	}); 
  }
  if(checkedYears.length > 0){	
  	jQuery.each(checkedYears, function(index, yearval){  
       if((index+1) == checkedYears.length){ yearQuery += yearval.defaultValue; 	  
       }else{ yearQuery += yearval.defaultValue +','; } 
  	}); 
  } 	

  selectedFilterTags = tagQuery;
  selectedFilterYears = yearQuery;

  if(tagQuery != '' && yearQuery != ''){ 
  	finalQuery = 'tag='+tagQuery+'&years='+yearQuery; 
  	localStorage.setItem('tagfilter', tagQuery);
  	localStorage.setItem('yearfilter', yearQuery);  	
  }else if(yearQuery == '' && tagQuery != ''){ 
  	finalQuery = 'tag='+tagQuery; 
  	localStorage.setItem('tagfilter', tagQuery);
  	localStorage.setItem('yearfilter', '');  	
  }else if(tagQuery == '' && yearQuery != ''){ 
  	finalQuery = 'years='+yearQuery; 
  	localStorage.setItem('tagfilter', '');
  	localStorage.setItem('yearfilter', yearQuery);  	
  }else{ 
  	finalQuery = ''; 
  	localStorage.setItem('tagfilter', '');
  	localStorage.setItem('yearfilter', '');  	
  } //console.log(finalQuery); 
  if(finalQuery != ''){ //change url and call ajax 
  	window.history.pushState({name:'Filter'},'News Releases','?'+finalQuery); 
    if(filterUrl.indexOf('?') > -1){ filterUrl = filterUrl.substring(0,filterUrl.indexOf('?')); }
    if(documentUrl.indexOf('?') > -1){ documentUrl = documentUrl.substring(0, documentUrl.indexOf('?')); }
  	let finalFilterUrl = filterUrl+'?'+finalQuery;
  	let finalDocUrl = documentUrl+'?'+finalQuery;
      //alert(finalFilterUrl+" : "+finalDocUrl);
   	fetch_filteranddocument_data(finalFilterUrl,finalDocUrl,'change-filter');
  }else{
    if(filterUrl.indexOf('?') > -1){ filterUrl = filterUrl.substring(0,filterUrl.indexOf('?')); }
    if(documentUrl.indexOf('?') > -1){ documentUrl = documentUrl.substring(0, documentUrl.indexOf('?')); }  
    window.history.pushState({name:'Clear Filter'},'News Releases',window.location.pathname);
    fetch_filteranddocument_data(filterUrl,documentUrl,'change-filter');   
  }
  /*loadFilters().then(function(response){
     renderPagination();
     loadPage(0);
   });*/ 
});

/*closing the filter modal will reset filters*/
jQuery(document).on('click', '#closemodal', function(){
  var modalCloseButton = jQuery(this); 
  var mainparent = jQuery(document).find('.filtermodal-content');
  var checkedTagsVal = localStorage.getItem('tagfilter'); 
  var checkedYearVal = localStorage.getItem('yearfilter');
  var allTags = jQuery(mainparent).find('input[name="category_filter"]'); 
  var allYears = jQuery(mainparent).find('input[name="date_filter"]'); 
  var filterlistingTop = jQuery('#filter-listings');
  //reset the filters
  if(allTags.length > 0){ 
    if(checkedTagsVal !== null && checkedTagsVal !== ""){
      let checkedTagsValArr = checkedTagsVal.split(','); 
  	  jQuery.each(allTags, function(index, tagvalue){ 
  	    let currTagVal = jQuery(tagvalue).attr('value'); 
  	    if(checkedTagsValArr.indexOf(currTagVal) >= 0){ 
 	      jQuery(tagvalue).prop('checked', true);   	  
  	    }else{ jQuery(tagvalue).prop('checked', false); }
  	  });
  	}else{
  	  jQuery.each(allTags, function(index, tagvalue){ jQuery(tagvalue).prop('checked', false); });  	
  	}   
  }
  if(allYears.length > 0){
    if(checkedYearVal !== null && checkedYearVal !== ""){  
      let checkedYearValArr = checkedYearVal.split(','); 
  	  jQuery.each(allYears, function(index, yearval){ 
  	    let curryearval = jQuery(yearval).attr('value'); 
  	    if(checkedYearValArr.indexOf(curryearval) >= 0){ 
  	      jQuery(yearval).prop('checked', true); 
  	    }else{ jQuery(yearval).prop('checked', false); } 
  	  });
  	}else{
  	  jQuery.each(allYears, function(index, yearval){ jQuery(yearval).prop('checked', false); });    	
  	}   
  }
});

/** Functions to writing data to DOM. */
function fetch_filteranddocument_data(filterurl, documenturl, event){
	var totalDocs = 0; 
	if(filterurl.indexOf('years=') > -1){ filterurl = filterurl.replace('years','year'); }
	jQuery.ajax({
		//url: pressreleases_ajax.ajaxurl, 
        //method: 'POST',
        //data: {
        //	'filterUrl': filterurl,
		//	'action' : 'fetchFilterDataFromCRO', 
		//},
        //dataType: "json", 
		url: (stageorprod) ? filterurl : '',
        method: (stageorprod) ? 'GET' : 'POST',
        data: (stageorprod) ? '' : {
        	'filterUrl': filterurl,
			'action' : 'fetchFilterDataFromCRO', 
		},		
        dataType: (stageorprod) ? "" : "json",         
        beforeSend: function(){ 
        	if(event == 'page-load' || event == 'page-reload'){
        	  //jQuery('#filterButton').find('i.fa-spinner').remove(); 
        	  //jQuery('#filterButton').prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true" style="padding-right:2px;"></i>'); 
			  jQuery('#latest-items-container').empty().prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true" ></i>'); jQuery('#latest-items-container').css({'minHeight':'500px'});
        	  jQuery('div.pr-pagination').empty(); 
        	  jQuery('.latest-section-title .page-num').empty(); 
        	  jQuery('#filterButton').attr('disabled','true'); 
        	  jQuery('.pr-pagination').css({'display':'none'});    		         	           	          	           	  
        	}
        	if((event == 'apply-filter') || (event == 'change-filter') || (event == 'remove-filters')){
        	  document.getElementById("pressreleases-filter").style.display = "none";
        	  jQuery('#latest-items-container').empty().prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>'); jQuery('#latest-items-container').css({'minHeight':'500px'});
        	  jQuery('.latest-section-title').css({'display':'none'}); 
        	  jQuery('.pr-pagination').css({'display':'none'});            	  
        	}   
	    },
	    success: function(response){ //console.log(response);
	    	if(stageorprod){
	          let filterResponse = ''; 
	          let tagHtml = ''; let yearHtml = ''; let filtercount = '';
	    	  try{ 
	    	    filterResponse = JSON.parse(response); 
	    	    if(Array.isArray(filterResponse)){ 
	    	      let ctags = filterResponse[1].tags; 
	    	      ctags.sort(function(a, b){ 
	    	      	let fEle = a.title.toLowerCase(); 
	    	      	let nEle = b.title.toLowerCase(); 
	    	      	if( fEle < nEle ){ return -1; }
	    	      	if( fEle > nEle ){ return 1; }
	    	      	return 0; 
	    	      }); 	    	      
	    	      let cyears = filterResponse[2].years; 
	    	      filtercount = filterResponse[0].count; 
	    	      ctags.forEach(function(element, index){  
	    	      if(element.count > 0){ 	    	      
	  			   tagHtml += '<li><span class="tag-select">';
	  			   tagHtml += '<input type="checkbox" id="'+element.filter+'" name="category_filter" data-text="'+element.title+'" nrec="'+element.count+'" value="'+element.filter+'"/></span>';
	  			   tagHtml += '<label for="'+element.filter+'" class="tag-name">'+element.title+'</label></li>'; 
	  			  } 	    	   
	    	      });
	    	      cyears.forEach(function(element, index){ 
	    	       if(element.count > 0){ 	    	       
	  			    yearHtml += '<li><span class="tag-select">';
	  			    yearHtml += '<input type="checkbox" id="'+element.filter+'" name="date_filter" data-text="'+element.filter+'" nrec="'+element.count+'" value="'+element.filter+'"/></span>';
	  			    yearHtml += '<label class="tag-name" for="'+element.filter+'">'+element.filter+'</label></li>'; 
	  			   } 
	    	      });	    	   
			      if(event == 'page-load' || event == 'page-reload'){ 
				    jQuery('.tags-filter').find('ul').empty().append(tagHtml);
				    jQuery('.year-filter').find('ul').empty().append(yearHtml); 
				    jQuery('#filterButton').removeAttr('disabled'); 					  
			      }  
			      jQuery('input[name="nRecords"]').val(filtercount);
			      totalDocs = filtercount; 									
		  	    }else{ console.log(response); } 
	    	  }catch(err){ //console.log(err); 
	    	    if(Array.isArray(response)){ 
	    	      filterResponse = response; 
	    	      let ctags = filterResponse[1].tags; 
	    	      ctags.sort(function(a, b){ 
	    	      	let fEle = a.title.toLowerCase(); 
	    	      	let nEle = b.title.toLowerCase(); 
	    	      	if( fEle < nEle ){ return -1; }
	    	      	if( fEle > nEle ){ return 1; }
	    	      	return 0; 
	    	      }); 	    	      
	    	      let cyears = filterResponse[2].years; 
	    	      filtercount = filterResponse[0].count; 
	    	      ctags.forEach(function(element, index){ 
	    	      if(element.count > 0){ 	    	       
	  			   tagHtml += '<li><span class="tag-select">';
	  			   tagHtml += '<input type="checkbox" id="'+element.filter+'" name="category_filter" data-text="'+element.title+'" nrec="'+element.count+'" value="'+element.filter+'"/></span>';
	  			   tagHtml += '<label for="'+element.filter+'" class="tag-name">'+element.title+'</label></li>'; 
	  			  }  	    	   
	    	      });
	    	      cyears.forEach(function(element, index){ 
	    	       if(element.count > 0){ 	    	        
	  			    yearHtml += '<li><span class="tag-select">';
	  			    yearHtml += '<input type="checkbox" id="'+element.filter+'" name="date_filter" data-text="'+element.filter+'" nrec="'+element.count+'" value="'+element.filter+'"/></span>';
	  			    yearHtml += '<label class="tag-name" for="'+element.filter+'">'+element.filter+'</label></li>'; 
	  			   }  
	    	      });	    	   
			      if(event == 'page-load' || event == 'page-reload'){ 
				    jQuery('.tags-filter').find('ul').empty().append(tagHtml);
				    jQuery('.year-filter').find('ul').empty().append(yearHtml); 
				    jQuery('#filterButton').removeAttr('disabled'); 					  
			      }  
			      jQuery('input[name="nRecords"]').val(filtercount);
			      totalDocs = filtercount; 									
		  	    }else{ console.log(response); }	
	    	  }
	    	}else{	    
		  	if(response.success){ 
		  		if(event == 'page-load' || event == 'page-reload'){
				  jQuery('.tags-filter').find('ul').empty().append(response.tags);
				  jQuery('.year-filter').find('ul').empty().append(response.years); 
				  jQuery('#filterButton').removeAttr('disabled'); 					  
				}  
				jQuery('input[name="nRecords"]').val(response.count);
				totalDocs = response.count; 									
		  	}else{ console.log(response); }
		  	}
	    },
	    error: function(xhr, status, error){
	        //var err = eval("(" + xhr.responseText + ")"); console.log(err.Message);
	        console.log(xhr); console.log(status); console.log(error); 
	    },
	    complete: function(response){
	      if(event == 'page-load' || event == 'page-reload'){					    
		    /*setTimeout(function(){ 
		    	//jQuery('#filterButton').find('i.fa-spinner').remove(); 
		    	jQuery('#latest-items-container').find('i.fa-spinner').remove();		    	
		    }, 1500);*/
		  }
		  if(event == 'page-reload'){ //setup filters 
		  	let cUrl = new URL(window.location.href); 
		  	let urlSearchParams = cUrl.searchParams.toString();			  
    		let tagFilter = localStorage.getItem('tagfilter'); 
    		let yearFilter = localStorage.getItem('yearfilter');
  			let mainparent = jQuery(document).find('.filtermodal-content'); 
  			let filterTags = jQuery(mainparent).find('input[name="category_filter"]'); 
  			let filterYears = jQuery(mainparent).find('input[name="date_filter"]'); 
  			let finalQuery = ''; 
			//if urlparms not same as existing filters. 
			if(urlSearchParams.length > 0) {
			  if((urlSearchParams.indexOf('tag=') > -1) && (urlSearchParams.indexOf('years=') > -1)){
		  	  	for(let key of cUrl.searchParams.keys()) { 
		  	    	let val = cUrl.searchParams.get(key); 
		  	    	if( key == "tag"){ 
		  			  let pTags = localStorage.getItem('tagfilter');
		  			  if (pTags !== val){ 
		  			  	localStorage.setItem('tagfilter', val); 
		  			  	tagFilter = localStorage.getItem('tagfilter'); 
		  			  } 
		  	    	}else{
		  		  	  let pYears = localStorage.getItem('yearfilter');
		  		  	  if (pYears !== val){ 
		  		  		localStorage.setItem('yearfilter', val); 
		  		  		yearFilter = localStorage.getItem('yearfilter');
		  		  	  }		  		
		  	    	}  			
			  	}//end for
			   }else if((urlSearchParams.indexOf('tag=') > -1) && (urlSearchParams.indexOf('years=') == -1)){
		  	  	 for(let key of cUrl.searchParams.keys()) { 
		  	    	let val = cUrl.searchParams.get(key); 
		  	    	if( key == "tag"){ 
		  			  let pTags = localStorage.getItem('tagfilter');
		  			  if (pTags !== val){ 
		  			  	localStorage.setItem('tagfilter', val); 
		  			  	tagFilter = localStorage.getItem('tagfilter'); 
		  			  } 
		  	    	}		
			  	  }//end for			  
			      localStorage.setItem('yearfilter', ""); 
		  		  yearFilter = localStorage.getItem('yearfilter');			     
			    }else if((urlSearchParams.indexOf('years=') > -1) && (urlSearchParams.indexOf('tag=') == -1)){
		  	  	  for(let key of cUrl.searchParams.keys()) { 
		  	    	let val = cUrl.searchParams.get(key); 
		  	    	if( key == "years"){ 
		  			  let pTags = localStorage.getItem('yearfilter');
		  			  if (pTags !== val){ 
		  			  	localStorage.setItem('yearfilter', val); 
		  			  	yearFilter = localStorage.getItem('yearfilter'); 
		  			  } 
		  	    	} 					  
			      }// end for 
			      localStorage.setItem('tagfilter', "");
		  		  tagFilter = localStorage.getItem('tagfilter');			     
			    }//endif-else  
			} //endif urlSearchParams.length > 0 
  			//set filters in modal
  			if(tagFilter !== '' && tagFilter !== null){  
  	  		  let tagFilters = []; finalQuery += 'tag='+tagFilter;
  	  		  if(tagFilter.indexOf(',') > -1){ tagFilters = tagFilter.split(','); }
  	  		  else{ tagFilters[0] = tagFilter; } 
  	  		  jQuery.each(tagFilters, function(index, tagval){ 
  	  	 		jQuery.each(filterTags, function(indx, tagvalue){    	  
  	  			   if(jQuery(tagvalue).attr('value') == tagval){ 
  	  		  		 jQuery(tagvalue).prop('checked', true); 
  	  		  		 jQuery(tagvalue).css({'accentColor':'#000'});    	  		  		 
  	  		  		 return false;  	
  	  			   }
  	     		});
  	  		  }); 
  			}
  			if(yearFilter !== '' && yearFilter !== null){ 
  	  		  let yearFilters = [];
  	  		  if(finalQuery !== ''){ finalQuery += '&years='+yearFilter; }
  	  		  else{ finalQuery += 'years='+yearFilter; }    	  		  
  	  		  if(yearFilter.indexOf(',') > -1){ yearFilters = yearFilter.split(','); }
  	  		  else{ yearFilters[0] = yearFilter; } 
  	  		  jQuery.each(yearFilters, function(index, yearval){ 
  	  	 		jQuery.each(filterYears, function(indx, yearvalue){    	  
  	  			   if(jQuery(yearvalue).attr('value') == yearval){ 
  	  		  		 jQuery(yearvalue).prop('checked', true); 
  	  		  		 jQuery(yearvalue).css({'accentColor':'#000'});    	  		  		 
  	  		  		 return false;  	
  	  			   }
  	     		});
  	  		  });   			
  			}
			//set the filters on top 
  			var checkedTags = jQuery(mainparent).find('input[name="category_filter"]:checked'); 
  			var checkedYears = jQuery(mainparent).find('input[name="date_filter"]:checked'); 
  			var filterlistingTop = jQuery('#filter-listings'); 
  			if(checkedTags.length > 0){
  			 jQuery.each(checkedTags, function(index, tagvalue){  
  	   			let tagText = jQuery(tagvalue).attr('data-text'); 	
				//list the filters on the top of page
       			if(jQuery(filterlistingTop).children().length == 0) {
       	  		   jQuery(filterlistingTop).append('<div class="filter-div" name="'+tagvalue.defaultValue+'"><span class="filter-div-text">'+tagText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>');
	   			}else{
	   	 		   let sel_filters = jQuery(filterlistingTop).children(); let found = 0;
		 		   jQuery.each(sel_filters, function(index, filter){ 
					 if(jQuery(filter).attr('name') == jQuery(tagvalue).attr('value')){ found = 1; }
	     		   });
	     		   if(found == 0){
           			 jQuery(filterlistingTop).append('<div class="filter-div" name="'+tagvalue.defaultValue+'"><span class="filter-div-text">'+tagText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>');		     
	     		   }
	   		    }
  			 }); 
  		   } //endif checkedTags.length
  		   if(checkedYears.length > 0){	
  			 jQuery.each(checkedYears, function(index, yearval){ 
  	   			let yearText = jQuery(yearval).attr('data-text');  
				//list the filters on the top of page
       			if(jQuery(filterlistingTop).children().length == 0) {
       	  		  jQuery(filterlistingTop).append('<div class="filter-div" name="'+yearval.defaultValue+'"><span class="filter-div-text">'+yearText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>'); 
	   			}else{
	   	 		  let sel_filters = jQuery(filterlistingTop).children(); let found = 0; 
		 		  jQuery.each(sel_filters, function(index, filter){ 	
					if(jQuery(filter).attr('name') == jQuery(yearval).attr('value')){ found = 1; }
	     		  });
	     		  if(found == 0){
           			jQuery(filterlistingTop).append('<div class="filter-div" name="'+yearval.defaultValue+'"><span class="filter-div-text">'+yearText+'</span><i class="fa fa-times filter-close" aria-hidden="true"></i></div>'); 
         		  }  	     
	   			}       
  		     }); 
  		    } //endif checkedYears.length 
  		    if(window.location.search == ""){
  			  if(finalQuery != ''){  
  				window.history.pushState({name:'Filter'},'News Releases','?'+finalQuery);
			  }	  		      	
  		    }   		    
		  } //endif event		  
		  //fetch documents  
		  //fetch_documentdata(documenturl, totalDocs);
		  //fetch_documentdata(documenturl, totalDocs);
		  /**
		   * Add custom document fetch function 
		   * Use the variables available as 
		   * a. filterurl - filter url as default url or with filter attached.
		   * b. documenturl - document url as default url or with filter attached.
		   * c. totalDocs - as retrieved from filter api.  
		   */ 
		   selectedFilterTags = localStorage.getItem('tagfilter'); 
		   selectedFilterYears = localStorage.getItem('yearfilter'); 
		   activePage = 0;		   
		   if(selectedFilterTags === "null"){ selectedFilterTags = ''; }
		   if(selectedFilterYears === "null"){ selectedFilterYears = ''; }
		   docurl = documenturl; fltrurl = filterurl; 	   		  
		   loadFilters().then(function(response){
    		 renderPagination();
    		 loadPage(0); 
    		 setActivePage();    		 
    	   });    
        },
	}); // end of ajax 
}


/**--------------------------------------------------------*
 * Functions for search			 
 *---------------------------------------------------------*/
/** Modify filter based on category filter selection */
jQuery(document).on('change','input[name="search_category_filter"]',function(){ 
  let query = '';
  var cfilter = jQuery(this); 
  var ischecked = jQuery(cfilter).prop('checked'); 
  var mainparent = jQuery(cfilter).parents('.filtermodal-content');   
  var filterTags = jQuery(mainparent).find('input[name="search_category_filter"]');  
  var checkedYears = jQuery(mainparent).find('input[name="search_date_filter"]:checked'); 
  var filterlistingTop = jQuery('#search-filter-listings');
  if(ischecked){ 
    cfilter.css({'accentColor':'#000'});  
	//disable other filter tags
  	jQuery.each(filterTags, function(index, inputtag){ 
  		if(jQuery(inputtag).attr('search-link') != jQuery(cfilter).attr('search-link')){ 
  			jQuery(inputtag).attr('disabled',true);
  		} 
  	});   
  }else{ // if deselected
	//enable other filter tags
  	jQuery.each(filterTags, function(index, inputtag){ 
  		if(jQuery(inputtag).attr('search-link') != jQuery(cfilter).attr('search-link')){ 
  			jQuery(inputtag).removeAttr('disabled');
  		} 
  	});    
  }
});  

/** Modify filter based on date filter selection */
jQuery(document).on('change','input[name="search_date_filter"]',function(){ 
  let query = '';
  var cfilter = jQuery(this); 
  var ischecked = jQuery(cfilter).prop('checked'); 
  var mainparent = jQuery(cfilter).parents('.filtermodal-content');  
  var filterYears = jQuery(mainparent).find('input[name="search_date_filter"]');   
  var checkedTags = jQuery(mainparent).find('input[name="search_category_filter"]:checked');    
  var filterlistingTop = jQuery('#search-filter-listings');
  if(ischecked){ 
    cfilter.css({'accentColor':'#000'});   
	//disable other filter tags
  	jQuery.each(filterYears, function(index, inputtag){ 
  		if(jQuery(inputtag).attr('search-link') != jQuery(cfilter).attr('search-link')){ 
  			jQuery(inputtag).attr('disabled',true);
  		} 
  	}); 
  }else{ // if year deselected
	//disable other filter tags
  	jQuery.each(filterYears, function(index, inputtag){ 
  		if(jQuery(inputtag).attr('search-link') != jQuery(cfilter).attr('search-link')){ 
  			jQuery(inputtag).removeAttr('disabled');
  		} 
  	}); 
  }
});

/** apply search filters on button click */
jQuery(document).on('click', 'button[name="apply-search-filters"]', function(){
  var btnEle = jQuery(this); let query='';
  var mainparent = jQuery(btnEle).parents('.filtermodal-content'); 
  var searchTagFilters = jQuery(mainparent).find('input[name="search_category_filter"]:checked'); 
  var searchYearFilters = jQuery(mainparent).find('input[name="search_date_filter"]:checked'); 
  var filterlistingTop = jQuery('#search-filter-listings'); 
  //reset all values based on current search vals. 
  jQuery(filterlistingTop).empty(); let currSearch = ''; 
  currSearch = sessionStorage.getItem('osearchQuery'); 
  if(searchTagFilters.length > 0){  
  jQuery.each(searchTagFilters, function(index, inputtag){ 
     let cfiltername = jQuery(inputtag).attr('name'); 
     let cfiltertext = jQuery(inputtag).attr('data-text');
     let cfilterlink = jQuery(inputtag).attr('search-link');    	
     jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>'); 
	 let links = jQuery(inputtag).attr('search-link').split('&'); 
	 currSearch = currSearch+'&'+links[1]; 
  	 sessionStorage.setItem('searchtagfilter',jQuery(inputtag).attr('search-link'));  
  }); 
  }else{ sessionStorage.setItem('searchtagfilter', ''); } 
  if(searchYearFilters.length > 0){       
  jQuery.each(searchYearFilters, function(index, inputtag){ 
	 let cfiltername = jQuery(inputtag).attr('name'); 
     let cfiltertext = jQuery(inputtag).attr('data-text');
     let cfilterlink = jQuery(inputtag).attr('search-link');    	
     jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>'); 
	 let links = jQuery(inputtag).attr('search-link').split('&'); 
	 currSearch = currSearch+'&'+links[1]; 
  	 sessionStorage.setItem('searchyearfilter',jQuery(inputtag).attr('search-link')); 
  }); 
  }else{ sessionStorage.setItem('searchyearfilter', ''); }   
  sessionStorage.setItem('currsearchvalues',currSearch);   
  window.history.replaceState({name:'Search CategoryDate Filter'},'Search',currSearch);  
  jQuery("#searchpage-filter").css({"display":"none"});
  //hit search query
  if(window.location.search.indexOf('query') > -1){ 
  	query = window.location.search; 
  }   	
  let search = searchapi+query+'&page=0'; //console.log(search);
  fetch_filterandsearchdocument_data(search,'search-using-filters'); 
});

/** Modify filter based on cross button on filter list */
jQuery(document).on('click','i.filterdiv-close',function(){ 
  var query = '';  
  var filterCloseButton = jQuery(this); 
  var cfilter = jQuery(filterCloseButton).parent(); 
  var topparent = jQuery(document).find('#searchpage-filter');
  var mainparent = jQuery(topparent).find('.filtermodal-content');
  var filterTags = jQuery(mainparent).find('input[name="search_category_filter"]'); 
  var filterYears = jQuery(mainparent).find('input[name="search_date_filter"]'); 
  var filterlistingTop = jQuery('#search-filter-listings'); 
  //remove the filter from the top of page 
  if(jQuery(filterlistingTop).children().length > 0){
    let sel_filters = jQuery(filterlistingTop).children(); 
	jQuery.each(sel_filters, function(index, filter){ 
	  if(jQuery(filter).text() == jQuery(cfilter).text()){ 
	  	jQuery(filterlistingTop).find('div[name="'+jQuery(cfilter).attr('name')+'"]').remove();
	  } 
	});
  }  		   
  //deselect the filter
  if(filterTags.length > 0){
  	jQuery.each(filterTags, function(index, tagvalue){ 
  	  if(jQuery(cfilter).attr('name') == jQuery(tagvalue).attr('name')){ 
  	    jQuery(tagvalue).prop('checked',false); jQuery(tagvalue).removeAttr('disabled'); 	     
  	  }
  	}); 
  	sessionStorage.setItem('searchtagfilter','');  
  }
  if(filterYears.length > 0){	
  	jQuery.each(filterYears, function(index, yearval){ 
  	  if(jQuery(cfilter).attr('name') == jQuery(yearval).attr('name')){ 
  	    jQuery(yearval).prop('checked',false); jQuery(yearval).removeAttr('disabled');  
  	  } 
  	});
	sessionStorage.setItem('searchyearfilter','');  	 
  }
  let links = ''; let currSearch = ''
  if(window.location.search.indexOf('&') > -1){ 
      searchStrs = window.location.search.split('&'); 
      currSearch = searchStrs[0];
  }else{ currSearch = window.location.search; }	
  // checked tags and years
  var checkedTags = jQuery(mainparent).find('input[name="search_category_filter"]:checked'); 
  var checkedYears = jQuery(mainparent).find('input[name="search_date_filter"]:checked');   
  if(checkedTags.length > 0){ 	  	
    jQuery.each(checkedTags, function(index, tagval){ 
	   links = jQuery(tagval).attr('search-link').split('&'); 
	   currSearch = currSearch+'&'+links[1]; 
	   sessionStorage.setItem('searchtagfilter',jQuery(tagval).attr('search-link')); 
  	}); 	
  } 
  if(checkedYears.length > 0){ 	  	
  	jQuery.each(checkedYears, function(index, yearval){ 
	   links = jQuery(yearval).attr('search-link').split('&'); 
	   currSearch = currSearch +'&'+links[1]; 
	   sessionStorage.setItem('searchyearfilter',jQuery(yearval).attr('search-link'));  	   
  	}); 	
  }      
  window.history.replaceState({name:'Search CategoryDate Filter'},'Search',currSearch);
  sessionStorage.setItem('currsearchvalues',currSearch);  
  if(window.location.search.indexOf('query') > -1){ 
  	query = window.location.search; 
  }   	
  let search = searchapi+query+'&page=0'; //console.log(search);
  fetch_filterandsearchdocument_data(search,'search-using-filters');       
});

/** Modify filter based on clear all */
jQuery(document).on('click','a.btn-clear-search',function(){  
  var query = '';  
  var filterClearButton = jQuery(this); 
  var topparent = jQuery(document).find('#searchpage-filter');
  var mainparent = jQuery(topparent).find('.filtermodal-content');
  var filterTags = jQuery(mainparent).find('input[name="search_category_filter"]'); 
  var filterYears = jQuery(mainparent).find('input[name="search_date_filter"]'); 
  var filterlistingTop = jQuery('#search-filter-listings'); 
  //jQuery("#searchpage-filter").css({"display":"none"});  
  //remove the filter from the top of page 
  /*if(jQuery(filterlistingTop).children().length > 0){
    let sel_filters = jQuery(filterlistingTop).children(); 
	jQuery.each(sel_filters, function(index, filter){ 
	   jQuery(filterlistingTop).find('div[name="'+jQuery(filter).attr('name')+'"]').remove(); 
	});
  }*/  		   
  //deselect the filter
  if(filterTags.length > 0){
  	jQuery.each(filterTags, function(index, tagvalue){ 
    	jQuery(tagvalue).prop('checked',false); jQuery(tagvalue).removeAttr('disabled'); 
	    //sessionStorage.setItem('searchtagfilter','');    	 
  	}); 
  }
  if(filterYears.length > 0){	
  	jQuery.each(filterYears, function(index, yearval){ 
  		jQuery(yearval).prop('checked',false); jQuery(yearval).removeAttr('disabled'); 
	   	//sessionStorage.setItem('searchyearfilter',''); 		
  	}); 
  }
  /*let origSearchterm = sessionStorage.getItem('osearchQuery');  
  window.history.replaceState({name:'Search CategoryDate Filter'},'Search',origSearchterm); 
  sessionStorage.setItem('currsearchvalues',origSearchterm);  
  if(window.location.search.indexOf('query') > -1){ 
  	query = window.location.search; 
  }   	
  let search = searchapi+query+'&page=0'; //console.log(search);
  fetch_filterandsearchdocument_data(search,'search-using-filters');*/
});

/*closing the filter modal will reset search filters*/
jQuery(document).on('click', '#searchpage-filter .close', function(){ 
  var modalCloseButton = jQuery(this); 
  var mainparent = jQuery(document).find('.filtermodal-content');
  var checkedTagVal = sessionStorage.getItem('searchtagfilter'); 
  var checkedYearVal = sessionStorage.getItem('searchyearfilter'); 
  var filterTags = jQuery(mainparent).find('input[name="search_category_filter"]'); 
  var filterYears = jQuery(mainparent).find('input[name="search_date_filter"]');
  var filterlistingTop = jQuery('#search-filter-listings');
  //reset the filters
  if(filterTags.length > 0){ 
   if(checkedTagVal !== null && checkedTagVal !== ""){ 
  	 jQuery.each(filterTags, function(index, tagvalue){ 
  	   let currTagVal = jQuery(tagvalue).attr('search-link'); 
  	   if(currTagVal == checkedTagVal){ jQuery(tagvalue).prop('checked', true); jQuery(tagvalue).attr('disabled', false);}
  	   else{ jQuery(tagvalue).prop('checked', false); jQuery(tagvalue).attr('disabled', true); }
  	 });
   }else{
  	 jQuery.each(filterTags, function(index, tagvalue){ 
  	   jQuery(tagvalue).prop('checked', false); jQuery(tagvalue).attr('disabled', false);
  	 });   
   }	   	 
  }
  if(filterYears.length > 0){ 
   if(checkedYearVal !== null && checkedYearVal !== ""){ 	
  	 jQuery.each(filterYears, function(index, yearval){ 
  	   let curryearval = jQuery(yearval).attr('search-link'); 
  	   if(curryearval == checkedYearVal){ jQuery(yearval).prop('checked', true); jQuery(yearval).attr('disabled', false); }
  	   else{ jQuery(yearval).prop('checked', false); jQuery(yearval).attr('disabled', true); } 
  	 });
   }else{
  	 jQuery.each(filterYears, function(index, yearval){ 
  	   jQuery(yearval).prop('checked', false); jQuery(yearval).attr('disabled', false);
  	 });   
   }  	 
  }
});

/** Functions to writing filter data to DOM. */
function fetch_filterandsearchdocument_data(searchurl, event){
	var totalDocs = 0; 
	var filters = (localStorage.getItem('filters') != '') ? localStorage.getItem('filters') : ''; 
	jQuery.ajax({
		//url: pressreleases_ajax.ajaxurl, 
        //method: 'POST',
        //data: {
        //	'filters': filters,
        //	'searchUrl': searchurl,
		//	'action' : 'fetchSolrSearchResultsByPage', 
		//},
        //dataType: "json",
		url: (stageorprod) ? searchurl : '',
        method: (stageorprod) ? 'GET' : 'POST',
        data: (stageorprod) ? '' : {
        	'filters': filters,
        	'searchUrl': searchurl,
			'action' : 'fetchSolrSearchResultsByPage', 
		},
        dataType: (stageorprod) ? "" : "json",         
        beforeSend: function(){ 
        	if(event == 'page-load' || event == "page-reload" || event == 'search-using-filters'){
        	  //jQuery('#searchfilterButton').find('i.fa-spinner').remove(); 
        	  //jQuery('#searchfilterButton').prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true" style="padding-right:2px;"></i>'); 
        	  jQuery('#search-items-container').empty().prepend('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>'); 
        	  jQuery('div.pr-pagination').empty(); 
        	  jQuery('.latest-section-title .page-num').empty(); 
        	  if(jQuery('#searchfilterButton').css('display') == 'none'){ jQuery('#searchfilterButton').removeAttr('style'); } 
        	  jQuery('#searchfilterButton').attr('disabled',true);        	          	  
        	}
        	/*if((event == 'search-using-filters')){
        	  jQuery('#search-items-container').find('i.fa-spinner').remove(); 
        	  jQuery('#search-items-container').prepend('<i class="fa fa-spinner" aria-hidden="true" style="padding-right:2px;"></i>'); 
        	}*/   
	    },
	    success: function(response){ 
	    	if(stageorprod){
	    	  let filterResponse = ''; 
	          let tagHtml = ''; let yearHtml = ''; let filtercount = '';
	          try{ 
				filterResponse = JSON.parse(response);	
				console.log(filterResponse);          
	     		if(filterResponse.status == 'success'){ 
	     		  filtercount = filterResponse.count;
	     		  if(filterResponse.refinements){
			   	    let currentFilters = filterResponse.refinements; 
			   	    let serializeFilters = jQuery(currentFilters).serialize();   	
			   	    currentFilters.forEach(function(filterelement, index){ 
			   	 	  if(filterelement.name == 'Section'){ 
			   			sectionvalues = filterelement.values; 
	    	      				sectionvalues.sort(function(a, b){ 
	    	      				    let fEle = a.name.toLowerCase(); 
	    	      				    let nEle = b.name.toLowerCase(); 
	    	      				    if( fEle < nEle ){ return -1; }
	    	      				    if( fEle > nEle ){ return 1; }
	    	      				    return 0; 
	    	      				});  			   			
			   			sectionvalues.forEach(function(element, index){
	  				  	  tagHtml += '<li><span class="tag-select">';
	  				  	  tagHtml += '<input type="checkbox" name="search_category_filter" id="'+element.name+'" search-link="'+element.uri+'" data-text="'+element.name+'" nrec="'+element.count+'" value="'+element.url+'"/></span>';
	  				  	  tagHtml += '<label class="tag-name" for="'+element.name+'">'+element.name+'</label></li>'; 
			   			});
			   	 	  }	
			   	 	  if(filterelement.name == 'Article Publish Date'){ 
			   			datevalues = filterelement.values; 
	    	      				datevalues.sort(function(a, b){ 
	    	      				    let fEle = a.name.toLowerCase(); 
	    	      				    let nEle = b.name.toLowerCase(); 
	    	      				    if( fEle < nEle ){ return -1; }
	    	      				    if( fEle > nEle ){ return 1; }
	    	      				    return 0; 
	    	      				});  				   			
			   			datevalues.forEach(function(element, index){ 
	  				  	  yearHtml += '<li><span class="tag-select">';
	  				  	  yearHtml += '<input type="checkbox" name="search_date_filter" id="'+element.name+'" search-link="'+element.uri+'" data-text="'+element.name+'"   nrec="'+element.count+'" value="'+element.uri+'"/></span>';
	  				  	  yearHtml += '<label class="tag-name" for="'+element.name+'">'+element.name+'</label></li>'; 
			   			});
			   	 	  }				   
			   		});
     		    	if(tagHtml == ''){ 
     		      	  tagHtml += '<li><span class="tag-select">';
	  			  	  tagHtml += '<label class="tag-name"> No Available Tag Filters</label></li>';  
	  				}
     		    	if(yearHtml == ''){ 
	  			  	  yearHtml += '<li><span class="tag-select">';
			  	  	  yearHtml += '<label class="tag-name"> No Available Date Filters</label></li>';     		    
     		    	} 			   					   			
			   	   }//end if refinements 	
		  	   	   if(event == 'page-load' || event == "page-reload"){
				   	  if(event == 'page-load') { 
				   	  	if(response.filters){ localStorage.setItem('filters',serializeFilters); }
				   	  	if(tagHtml != ""){ sessionStorage.setItem('taghtml', tagHtml); }
				   	  	if(yearHtml != ""){ sessionStorage.setItem('yearhtml', yearHtml); }				   	     
				   	  }	 		  	   	   		  	   	   
		  		  	  if(filtercount > 0){	 
		  		  	     if(tagHtml == ""){ tagHtml = sessionStorage.getItem('taghtml'); }
		  		  	     if(yearHtml == ""){ yearHtml = sessionStorage.getItem('yearhtml');	}  		
				    	 jQuery('.search-tags-filter').find('ul').empty().append(tagHtml);
				    	 jQuery('.search-year-filter').find('ul').empty().append(yearHtml); 
				    	 jQuery('#searchfilterButton').removeAttr('disabled');					    
				  	  }else{ jQuery('#searchfilterButton').css({'display':'none'}); }  
				   }
				   jQuery('input[name="nRecords"]').val(filtercount);
				   totalDocs = totalRecords = filtercount;  
	    		  }else{ console.log(response); } 
	    		}catch(err){ console.log(response); //console.log(err);
	    		  if(response.status == 'success'){
	    		   filtercount = response.count; 
	    		   if(response.refinements){
			   		 let currentFilters = response.refinements; 
			   		 let serializeFilters = jQuery(currentFilters).serialize();  
			   		 filtercount = response.count; 	
			   		 currentFilters.forEach(function(filterelement, index){ 
			   	 		if(filterelement.name == 'Section'){ 
			   			  sectionvalues = filterelement.values; 
	    	      				  sectionvalues.sort(function(a, b){ 
	    	      				    let fEle = a.name.toLowerCase(); 
	    	      				    let nEle = b.name.toLowerCase(); 
	    	      				    if( fEle < nEle ){ return -1; }
	    	      				    if( fEle > nEle ){ return 1; }
	    	      				    return 0; 
	    	      				  }); 						   			  
			   			  sectionvalues.forEach(function(element, index){
	  				  		tagHtml += '<li><span class="tag-select">';
	  				  		tagHtml += '<input type="checkbox" name="search_category_filter" id="'+element.name+'" search-link="'+element.uri+'" data-text="'+element.name+'" nrec="'+element.count+'" value="'+element.url+'"/></span>';
	  				  		tagHtml += '<label class="tag-name" for="'+element.name+'">'+element.name+'</label></li>'; 
			   			  });
			   	 		}	
			   	 		if(filterelement.name == 'Article Publish Date'){ 
			   			  datevalues = filterelement.values; 
	    	      				  datevalues.sort(function(a, b){ 
	    	      				    let fEle = a.name.toLowerCase(); 
	    	      				    let nEle = b.name.toLowerCase(); 
	    	      				    if( fEle < nEle ){ return -1; }
	    	      				    if( fEle > nEle ){ return 1; }
	    	      				    return 0; 
	    	      				  }); 				  
			   			  datevalues.forEach(function(element, index){ 
	  				  		yearHtml += '<li><span class="tag-select">';
	  				  		yearHtml += '<input type="checkbox" name="search_date_filter" id="'+element.name+'" search-link="'+element.uri+'" data-text="'+element.name+'"   nrec="'+element.count+'" value="'+element.uri+'"/></span>';
	  				  		yearHtml += '<label class="tag-name" for="'+element.name+'">'+element.name+'</label></li>'; 
			   			  });
			   	 		}				   
			   		 });
     		    	 if(tagHtml == ''){ 
     		      		tagHtml += '<li><span class="tag-select">';
	  			  		tagHtml += '<label class="tag-name"> No Available Tag Filters</label></li>';  
	  				 }
     		    	 if(yearHtml == ''){ 
	  			  		yearHtml += '<li><span class="tag-select">';
			  	  		yearHtml += '<label class="tag-name"> No Available Date Filters</label></li>';     		    
     		    	 } 			   		 
			   		}//endif filter refinements 	
		  	   		if(event == 'page-load' || event == "page-reload"){
					   if(event == 'page-load') { 
				   		 if(response.filters){ localStorage.setItem('filters',serializeFilters); } 
				   		 if(tagHtml != ""){ sessionStorage.setItem('taghtml', tagHtml); }
				   	     if(yearHtml != ""){ sessionStorage.setItem('yearhtml', yearHtml); }	
					   }
		  		  	   if(filtercount > 0){	
		  		  	     if(tagHtml == ""){ tagHtml = sessionStorage.getItem('taghtml'); }
		  		  	     if(yearHtml == ""){ yearHtml = sessionStorage.getItem('yearhtml');	} 	  		
				    	 jQuery('.search-tags-filter').find('ul').empty().append(tagHtml);
				    	 jQuery('.search-year-filter').find('ul').empty().append(yearHtml); 
				    	 jQuery('#searchfilterButton').removeAttr('disabled');					    
				  	   }else{ jQuery('#searchfilterButton').css({'display':'none'}); }  
					}
					jQuery('input[name="nRecords"]').val(filtercount);
					totalDocs = totalRecords = filtercount;  
	    		  }else{ console.log(response); }  
	    		} //end catch	  	    	
	    	}else{ 
		  	  if(response.success){ 
		  		if(event == 'page-load' || event == "page-reload"){
		  		  if(response.count > 0){		  		
				    jQuery('.search-tags-filter').find('ul').empty().append(response.tags);
				    jQuery('.search-year-filter').find('ul').empty().append(response.years); 
				    jQuery('#searchfilterButton').removeAttr('disabled');					    
				  }else{ jQuery('#searchfilterButton').css({'display':'none'}); }  
				}
				if(event == 'page-load') { 
				   if(response.filters){ localStorage.setItem('filters',response.filters); } 
				}	 
				jQuery('input[name="nRecords"]').val(response.count);
				totalDocs = totalRecords = response.count; 									
		  	  }else{ console.log(response); } 
		  	}	
	    },
	    error: function(xhr, status, error){
	        //var err = eval("(" + xhr.responseText + ")"); console.log(err.Message);
	        console.log(xhr); console.log(status); console.log(error); 
	    },
	    complete: function(response){
	      if(event == 'page-load' || event == "page-reload" || event == 'search-using-filters'){ 					    
		    setTimeout(function(){ 
		    	//jQuery('#searchfilterButton').find('i.fa-spinner').remove(); 
		    	jQuery('#search-items-container').find('i.fa-spinner').remove(); 
		    	jQuery('#searchfilterButton').removeAttr('disabled');  		    			    	
		    }, 1500);
		  }
		  if(event == "page-reload"){
			//generate the respective selected items.    
   	  		let tagFilter = sessionStorage.getItem('searchtagfilter'); 
      		let yearFilter = sessionStorage.getItem('searchyearfilter');
      		let topparent = jQuery(document).find('#searchpage-filter');
  	  		let mainparent = jQuery(topparent).find('.filtermodal-content'); 
  	  		let filterTags = jQuery(mainparent).find('input[name="search_category_filter"]'); 
  	  		let filterYears = jQuery(mainparent).find('input[name="search_date_filter"]'); 
  	  		//set filters in modal
  	  		if(tagFilter != ''){ 
  	  		  jQuery.each(filterTags, function(indx, tagvalue){    	  
  	  	   		if(jQuery(tagvalue).attr('search-link') == tagFilter){ 
  	  		  	  jQuery(tagvalue).prop('checked', true); 
  	  		  	  jQuery(tagvalue).css({'accentColor':'#000'});    	  		  	   	
  	  	   		}else{ jQuery(tagvalue).attr('disabled',true); }
  	    	  });
  	  		}
  	  		if(yearFilter != ''){ 
 			  jQuery.each(filterYears, function(indx, yearvalue){    	  
		   		if(jQuery(yearvalue).attr('search-link') == yearFilter){ 
  	  		  	  jQuery(yearvalue).prop('checked', true); 
  	  		  	  jQuery(yearvalue).css({'accentColor':'#000'});    	  		  	   	
  	  	   		}else{ jQuery(yearvalue).attr('disabled',true); }
  	    	  });
  	  		}
	  		//set the filters on top 
  	  		var checkedTags = jQuery(mainparent).find('input[name="search_category_filter"]:checked'); 
  	  		var checkedYears = jQuery(mainparent).find('input[name="search_date_filter"]:checked'); 
  	  		var filterlistingTop = jQuery('#search-filter-listings'); 
  	  		if(checkedTags.length > 0){
  		 		jQuery.each(checkedTags, function(index, tagvalue){ 
    				let cfiltername = jQuery(tagvalue).attr('name'); 
    				let cfiltertext = jQuery(tagvalue).attr('data-text');
    				let cfilterlink = jQuery(tagvalue).attr('search-link');    
    				//list the filters on the top of page
    				if(jQuery(filterlistingTop).children().length == 0) {
       					jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>');
					}else{
	   					let sel_filters = jQuery(filterlistingTop).children(); 
	   					jQuery.each(sel_filters, function(index, filter){ 
	 	  					if(jQuery(filter).attr('name') == jQuery(tagvalue).attr('name')){ jQuery(filter).remove(); }
	   					});
       					jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>');	  
					}   		
  		 		});
  	   		 }//endif checkedTags.length
  	   		 if(checkedYears.length > 0){	
  		 		jQuery.each(checkedYears, function(index, yearval){ 
					let cfiltername = jQuery(yearval).attr('name'); 
    				let cfiltertext = jQuery(yearval).attr('data-text');
    				let cfilterlink = jQuery(yearval).attr('search-link');    
    				//list the filters on the top of page
    				if(jQuery(filterlistingTop).children().length == 0){
       					jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>');
					}else{
	   					let sel_filters = jQuery(filterlistingTop).children(); 
	   					jQuery.each(sel_filters, function(index, filter){ 
	 	 					if(jQuery(filter).attr('name') == jQuery(yearval).attr('name')){ jQuery(filter).remove(); }
	   					});
       					jQuery(filterlistingTop).append('<div class="search-filter-div" name="'+cfiltername+'"><span class="filter-div-text">'+cfiltertext+'</span><i class="fa fa-times filterdiv-close" aria-hidden="true"></i></div>');	  
					} 
  		 		}); 
  	   		 }//endif checkedYears.length 
		  }//endif event == "page-reload"   
        },//end complete 
	}); // end of ajax 
	//if search then use search url 
	if(getSearchQueryString()){ 
	  searchStr = searchurl.split('&'); 
	  pageid = (searchStr[searchStr.length -1]).split('=')[1];
	  activePage = 0;
	  srchurl = searchurl;	  	   
      loadSearch(pageid)
      .then(function(response){  
      	 renderSearchPagination();
      	 loadSearch(pageid);
      	 setActivePage();      	 
      });
	}
}


//var activePage = 0;
var start = 0;
var end = 0;
var limit = 5;
var perPage = 10;
var CurrentPageData = null;
var AllFilters = null;
var totalRecords = 0;
var is_search = false;
var selectedFilterTags = "";
var selectedFilterYears = "";

async function loadPage(id){
    if(docurl === ""){ docurl = documentUrl; }     
    if(id  == 'prev'){
        activePage = activePage - 1;
        if(activePage < 0) activePage = 0;
        loadPage(activePage);
    }else if(id == 'next'){
        activePage = activePage + 1;
        loadPage(activePage);
    }else{
        activePage = id;
    }

    if(stageorprod){
      var pageNo = 'p'+activePage; var regexp = /\.\p.*\./; 
	  docurl = docurl.replace(regexp, '.'+pageNo+'.'); 
	  if(docurl.indexOf('years=') > -1){ docurl = docurl.replace('years','year'); }		  
    }
    
     //jQuery('#filterButton').find('i.fa-spinner').remove(); 
     //jQuery('#filterButton').prepend('<i class="fa fa-spinner" aria-hidden="true" style="padding-right:2px;"></i>');
     jQuery.ajax({
         //'url':pressreleases_ajax.ajaxurl,
         //'method':'post',
         //'data':{page:activePage, tags:selectedFilterTags,years:selectedFilterYears, action:'fetchListingPage'}, 
         'url': (stageorprod) ? docurl : '',
         'method': (stageorprod) ? 'get' : 'post',
         'data': (stageorprod) ? '' : {page:activePage,tags:selectedFilterTags,years:selectedFilterYears,action:'fetchListingPage'}, 
     }).success(function(response){
     	try{      
          CurrentPageData = JSON.parse(response);
        }catch(err){ //console.log(err); 
		  if(Array.isArray(response)){         
           CurrentPageData = response;
          }else{console.log(response); } 
        } 
        renderPage(CurrentPageData);
        setActivePage();                   
        //jQuery('#filterButton').find('i.fa-spinner').remove(); 
        jQuery('#latest-items-container').find('i.fa-spinner').remove(); 
        jQuery('.latest-section-title').removeAttr('style');
        jQuery('.pr-pagination').removeAttr('style');          
     }).error(function(err){
         console.log(err);
     }).complete(function(){
         console.log('WActpg-',window.activePage);
         /*if(window.activePage == 0){ console.log('Custompagi');
        	jQuery('.pr-pagination').find('a').removeClass('active');           
        	jQuery('.pr-pagination #p0').removeAttr("style"); 
        	jQuery('.pr-pagination #p1').removeAttr("style");  
        	jQuery('.pr-pagination #p2').removeAttr("style"); 
        	jQuery('.pr-pagination #p3').removeAttr("style"); 
        	jQuery('.pr-pagination #p4').removeAttr("style"); 
        	jQuery('.pr-pagination #p0').addClass('active');         	        	        	       
         }else{ console.log('SetActPageSect'); setActivePage(); }*/ 
     });
}

function setActivePage(){
  var isSearch  = getSearchQueryString();
  var onClickFuncPrev = "";
  var onClickFuncNext = "";
  if(isSearch){
      onClickFuncPrev = 'loadSearch(\'prev\')';
      onClickFuncNext =  'loadSearch(\'next\')';
  }else{
      onClickFuncPrev = 'loadPage(\'prev\')';
      onClickFuncNext = 'loadPage(\'next\')';
  }
  var max = Math.floor(totalRecords/10);
  if ((totalRecords%10)){
      max++;
  }
    jQuery('.page-link').css({'display':'none'}).removeClass('active');
    
    start = activePage - 2;
    
    if(start < 0){
        start = 0;
    }
    if(activePage > (limit/2))
        end = activePage + 2;
    else
        end = limit;    

    for(var x=start-1; x<=end-1; x++){ 
        jQuery('#p'+x).css({'display':'inline-block'});
    }
    jQuery('#p'+activePage).addClass('active');

    jQuery('.page-link-prev , .page-link-next').css({'display':'inline-block'});

    if(activePage === 0){
      jQuery('.page-link-prev').addClass('disabled').attr('onclick', 'javascript:void(0)');
      
  }else if(activePage > 0){
      jQuery('.page-link-prev.disabled').removeClass('disabled').attr('onclick', onClickFuncPrev);
  }

  if(activePage === (max-1)){
      jQuery('.page-link-next').addClass('disabled').attr('onclick', 'javascript:void(0)');
  }else if(activePage < (max-1)){
      jQuery('.page-link-next').removeClass('disabled').attr('onclick', onClickFuncNext);
  }
}

async function loadFilters(){
    if(fltrurl === ""){ fltrurl = filterUrl; }   
    return new Promise(function(resolve,reject){
        jQuery.ajax({
            //'url':pressreleases_ajax.ajaxurl,
            //'method':'post',
            //'data':{action:'fetchFilters',tags:selectedFilterTags,years:selectedFilterYears},  
            'url': (stageorprod) ? fltrurl : '',
            'method': (stageorprod) ? 'get' : 'post',
            'data': (stageorprod) ? '' : {action:'fetchFilters',tags:selectedFilterTags,years:selectedFilterYears},                       
        }).success(function(response){
            console.log(response);
            try{            
              AllFilters = JSON.parse(response);
              totalRecords = AllFilters[0].count;
              // alert(totalRecords);
              resolve([AllFilters, totalRecords]);
			}catch(err){ //console.log(err); 
			 if(Array.isArray(response)){ 
               AllFilters = response;
               totalRecords = AllFilters[0].count;
               // alert(totalRecords);
               resolve([AllFilters, totalRecords]);	
             }else{ console.log(response); } 
			} 
			
        }).error(function(err){
            console.log(err);
            reject(err);
        })
    })
    
}

function renderPage(CurrentPageData){
    var container = jQuery('.latest-items');
    var data = CurrentPageData;
    container.html("");
    // scroll to top
    jQuery('html,body').animate({
        scrollTop: jQuery(".crux-page-title").offset().top
    },500);

    // render 1-10 of 1300 section
    var from = ((activePage*perPage)+1);
    var to = ((activePage+1)*perPage);
    if(to >= totalRecords){
        to = totalRecords;
    }
    var pagenum_text = from+"-"+to+" of "+totalRecords+" News Releases";
    if(totalRecords > 0){
        jQuery('.page-num').html(pagenum_text);
    }else{
        jQuery('.page-num').html("");
    }
        

    // render the fetched data
	if(data.length > 0){    
    data.forEach(function(item){
        var dt = new Date(item.pubDate).toLocaleDateString('en-US',{ year: 'numeric', month: 'long', day: 'numeric' });
        item = `<div class="item">
                    <div class="left-section">
                        <div class="article-title"><a href="${pressreleases_ajax.main_cro_site+item.link}">${item.title}</a></div>
                        <div class="crux-body-copy--small post-meta">${dt}</div>
                    </div>
                    <!--<div class="right-section">
                        <a href="${pressreleases_ajax.main_cro_site+item.link}"><img src="https://via.placeholder.com/354x199" alt="post-featured" /></a>
                    </div> -->
                </div>`;
                container.append(item);

    })
    }else{
		item = '<div class="no-item"><p>No data present for the filter. Please retry with another filter.</p></div>';
	    container.append(item);   
   }
    
}

/** Search Page Listing Functions */
function renderSearchPage(){
    var container = jQuery('.latest-items');
    var data = CurrentPageData;
    container.html("");
    // scroll to top
    jQuery('html,body').animate({
        scrollTop: jQuery(".crux-page-title").offset().top
    },500);

    // render 1-10 of 1300 section
    var keyword = getSearchQueryString(); 
    let dispKey = ''; let decodeKey = '';
    if(keyword.indexOf('&') > -1){ 
      dispKey = keyword.substr(0, keyword.indexOf('&')); 
      try { decodeKey = decodeURIComponent(dispKey.replace(/\+/g,'%20')); } 
      catch(e) { console.error(e); }
    } else{ 
      dispKey = keyword; 
      try { decodeKey = decodeURIComponent(dispKey.replace(/\+/g,'%20')); } 
      catch(e) { console.error(e); }
    }      
    var from = ((activePage*perPage)+1);
    var to = ((activePage+1)*perPage);
    if(to >= totalRecords){
        to = totalRecords;
    }
    var pagenum_text = from+"-"+to+" of "+totalRecords +" News Releases Matching \""+decodeKey+"\"";
    if(totalRecords > 0){
        jQuery('.page-num').html(pagenum_text);
    }else{
        jQuery('.page-num').html("");
    }
    

    if(data!=null){ 
    // render the fetched data
    data.forEach(function(item){
        var dt = new Date(item.publishedDate).toLocaleDateString('en-US',{ year: 'numeric', month: 'long', day: 'numeric' });
        item = `<div class="item">
                    <div class="left-section">
                        <div class="article-title"><a href="${pressreleases_ajax.main_cro_site+item.pageURL}">${item.title}</a></div>
                        <div class="crux-body-copy--small post-meta">${dt}</div>
                    </div>
                    <!--<div class="right-section">
                        <a href="${pressreleases_ajax.main_cro_site+item.pageURL}"><img src="https://via.placeholder.com/354x199" alt="post-featured" /></a>
                    </div> -->
                </div>`;
                container.append(item);

    })
}else{
    item = '<div class="no-item"><p>Please check your spelling or search for another keyword</p></div>';
    container.append(item);
}
}

function renderPagination(){

    //setTimeout(function(){
      var prev_link =  ''; 
      var next_link = '';  
      var page_links = '';
      var max = totalRecords/10;
      if ((totalRecords%10)){
          max++;
      }
  
      if(activePage == 0){
          prev_link = '<a class="page-link page-link-prev disabled" onClick="javascript:void(0);" id="p-prev"></a>';
          next_link = '<a class="page-link page-link-next" onClick="loadPage(\'next\')" id="p-next"></a>';
      }
      if(activePage == max){
          prev_link = '<a class="page-link page-link-prev" onClick="loadPage(\'prev\')" id="p-prev"></a>';
          next_link = '<a class="page-link page-link-next disabled" onClick="javascript:void(0);" id="p-next"></a>';
      }

    for(var i=1; i<=max; i++){
            page_links += '<a class="page-link" onClick="loadPage('+(i-1)+')" id="p'+(i-1)+'" style="display:none;">'+i+'</a>';
    }
    if(totalRecords > 10)
        jQuery('.pr-pagination').html(prev_link+page_links+next_link);
    else
        jQuery('.pr-pagination').html("");
    //},500);
}


function getSearchQueryString(){
    var queryText = window.location.search;
    if(srchurl === ""){ srchurl = searchapi+queryText+'&page=0'; } 
    
    // remove ? from querytext
    queryText = queryText.substr(1);
    queryKey = queryText.split('=')[0];
    //queryVal = queryText.split('=')[1];
    queryText = queryText.replace('query=',''); 
    queryVal = queryText;  
    //if(queryKey=="query")
    //    return queryVal;
    //else
    //    return false; 
    if(queryKey=="query"){ 
        return queryVal;
    }else{
        return false;
    }               
}

function loadSearch(id){

        var qval = getSearchQueryString();

        if(id  == 'prev'){
            activePage = parseInt(activePage) - 1;
            if(activePage < 0) activePage = 0;
            loadSearch(activePage);
        }else if(id == 'next'){
            activePage = parseInt(activePage) + 1;
            loadSearch(activePage);
        }else{
            activePage = id;
        }

    if(stageorprod){
      //var turl = srchurl.substr(0, (srchurl.length - 1)); 
	  //turl = turl+activePage; srchurl = turl; 
	  let lindx = srchurl.length;
	  if(srchurl.indexOf('&page=') > -1){ lindx = srchurl.indexOf('&page='); }  
      let turl = srchurl.substr(0, lindx); 
	  turl = turl+'&page='+activePage; srchurl = turl;	 
    }

    return new Promise(function(resolve,reject){
        jQuery.ajax({
            //'url':pressreleases_ajax.ajaxurl,
            //'method':'post',
            //'data':{page:activePage, query:qval, action:'fetchSearchPage'},
            'url': (stageorprod) ? srchurl : '',
            'method': (stageorprod) ? 'get' : 'post',
            'data': (stageorprod) ? '' : {page:activePage, query:qval, action:'fetchSearchPage'},             
        }).success(function(response){ 
           //SearchPageData = JSON.parse(response);
           //console.log(SearchPageData);
           let SearchPageData = ''; 
           try{ 
             SearchPageData = JSON.parse(response);
             console.log(SearchPageData);
            } catch(err){ 
              SearchPageData = response;
              console.log('Err', SearchPageData); 
            }           
           // check if records exists
           if(SearchPageData.hasOwnProperty('mediaRoom')){
            CurrentPageData = SearchPageData.mediaRoom.records;
           }else{
            CurrentPageData = null;
           }
           
           totalRecords = SearchPageData.count;
           renderSearchPage(CurrentPageData);
           setActivePage();
           resolve();
        }).error(function(err){
            console.log(err);
            reject(err);
        })
    })
    
}

function renderSearchPagination(){
    var prev_link = '';
    var next_link = '';
    var page_links = '';
    var max = totalRecords/10;
    if ((totalRecords%10)){
        max++;
    }

    if(activePage == 0){    
       prev_link = '<a class="page-link page-link-prev disabled" onClick="loadSearch(\'prev\')" id="p-prev"></a>';
       next_link = '<a class="page-link page-link-next" onClick="loadSearch(\'next\')" id="p-next"></a>';    
    }

    if(activePage == max){  
       prev_link = '<a class="page-link page-link-prev" onClick="loadSearch(\'prev\')" id="p-prev"></a>';
       next_link = '<a class="page-link page-link-next disabled" onClick="loadSearch(\'next\')" id="p-next"></a>'; 
    }   
  
    for(var i=1; i<=max; i++){
            page_links += '<a class="page-link" onClick="loadSearch('+(i-1)+')" id="p'+(i-1)+'" style="display:none;">'+i+'</a>';
    }
    if(totalRecords > 10)
        jQuery('.pr-pagination').html(prev_link+page_links+next_link);
    if(totalRecords <= 10)
    	jQuery('.pr-pagination').empty();      
}

// Initialize the Loading of data

setTimeout(function(){
    try{ 

        if( getSearchQueryString() ){
            loadSearch(0).then(function(response){
               renderSearchPagination();
               loadSearch(0);
               setActivePage();               
            });
        }
        if( window.location.search.indexOf('query') == -1 && 
        	window.location.search.indexOf('?') == -1 ) { 
            loadFilters().then(function(response){
                renderPagination();
                loadPage(0);
                setActivePage();
            }); 
        }
    
}catch(err){
    console.log(err);
}
},500);


/// searchbox focus blur effect

jQuery('document').ready(function(){
  jQuery('.search-box').on('focus',function(){
    jQuery(this).siblings('.icon-left').animate({'opacity':'0','width':'2px'},200);
    jQuery(this).siblings('.search-btn').animate({'opacity':'1'});
  })
  jQuery('.search-box').on('blur',function(){
    jQuery(this).siblings('.icon-left').animate({'opacity':'1','width':'40px'},200);
    jQuery(this).siblings('.search-btn').animate({'opacity':'0'});
  })
});

/*jQuery(document).ajaxComplete(function( event, xhr, settings ) { 
  if(window.location.href.indexOf('/press-releases') > -1){ 
    if(settings.hasContent){ 
      if(activePage == 0){ console.log('ACApg'); setActivePage(); } 
      //else{ console.log('AjaxCompActivepg1'); setActivePage(); }
    }
  }  
});*/  


  
  
  


