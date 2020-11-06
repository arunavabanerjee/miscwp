<?php

/**
 * add additional roles and posts as per requirement 
 */
require_once(get_stylesheet_directory() . '/inc/additional_roles.php');
require_once(get_stylesheet_directory() . '/inc/additional_posts.php');
//require_once(get_stylesheet_directory() . '/inc/guesty-integration-classes.php');
require_once(get_stylesheet_directory() . '/inc/additional_tools.php');

/**
 * homey child theme actions
 */
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_agents.php');
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_brokers.php');
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_forms.php');
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_listings.php');
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_reservations.php');
require_once(get_stylesheet_directory() . '/inc/homey_child_actions_search.php');


/**
 * Actions for scripts and styles
 */
add_action('wp_enqueue_scripts', 'homey_enqueue_styles'); 
function homey_enqueue_styles() {
    // enqueue parent styles
    wp_enqueue_style('homey-parent-theme', get_template_directory_uri() .'/style.css');
    // enqueue child styles
    wp_enqueue_style('homey-child-theme', get_stylesheet_directory_uri() .'/style.css', array('homey-parent-theme'));
    // enqueue script
    wp_enqueue_script('homey-child-script', get_stylesheet_directory_uri() . '/js/homey-child.js', array('jquery'), '', true);
}

if (is_admin() ){
 add_action('admin_enqueue_scripts', 'homey_child_admin_scripts');
 function homey_child_admin_scripts(){
    //admin child scripts
    wp_enqueue_style('homey-child-admin-theme', get_stylesheet_directory_uri() .'/css/admin/admin.css', array('homey-admin.css'));   
 }
}

/**
 * Action to defer script loading
 */
//add_filter( 'script_loader_tag', 'homey_child_defer_scripts', 10, 3 );
//function homey_child_defer_scripts( $tag, $handle, $src ) {
//  $defer = array( 'google-reCaptcha', 'google-map', 'stripe' );
//  if ( in_array( $handle, $defer ) ) {
//     return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
//  }
//  return $tag;
//}


/**
 * Overriding Redux Options
 */
//function add_another_section_bl($sections){
    //$sections = array(); // Delete this if you want to keep original sections! 
    /*$sections[] = array(
        'title' => __('A Section added by hook', 'swift-framework-admin'),
        'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'swift-framework-admin'),
        // Redux ships with the glyphicons free icon pack, included in the options folder.
        // Feel free to use them, add your own icons, or leave this blank for the default.
        'icon' => trailingslashit(get_template_directory_uri()) . 'options/img/icons/glyphicons_062_attach.png',
        // Leave this as a blank section, no options just some intro text set above.
        'fields' => array()
    );*/
    //get the section for changing the options
    //$mod_section = $sections[49]; 
    //$mod_section['fields'][494]['default'] = ""; //var_dump($mod_section); 
    //$sections[49] = array(); 
    //$sections[49] = $mod_section;
    //return $sections;
//}
//add_filter("redux/options/homey_options/sections", 'add_another_section_bl');

/*
 * override redux options in child theme
 */
/*function override_User_Roles_For_Registration($sections){
    //print_r($sections[13]); 
    $sections[13]['fields'][307]['default'] = 'Home-Owner';
    $sections[13]['fields'][308]['default'] = 'Renter'; 
    //print_r($sections[13]); 

    return $sections;
}*/
//add_filter('redux/options/homey_options/sections', 'override_User_Roles_For_Registration'); 

//function override_User_Roles_For_Registration($field){ var_dump($field);
//   $field["default"] = "Home-Owner";
//   return $field;
//}
//add_filter('redux/options/homey_options/field/host_role', 'override_User_Roles_For_Registration', 307);

//function override_User_Roles_For_Registration($data){ var_dump($data); 
    //print_r($sections[13]); 
    //$sections[13]['fields'][307]['default'] = 'Home-Owner';
    //$sections[13]['fields'][308]['default'] = 'Renter'; 
    //print_r($sections[13]); 
    //return $data;
//}
//add_filter('redux/field/homey_options/fieldset/before/renter_role', 'override_User_Roles_For_Registration');

//add_action('after_setup_theme', 'child_load_redux_settings');
//function child_load_redux_settings(){
//    if ( class_exists( 'Redux' ) ) {
//        $opt_name = "homey_options";
//	$field = Redux::getField($opt_name, 'host_role');
//	$field["default"] = 'Home-Owner'; //var_dump($field); 
//	Redux::setField($opt_name, $field);
//    }
//}

/**
 * Filter hook for filtering the args. 
 * Good for child themes to override or add to the args array. 
 * Can also be used in other functions.
 */
// Change the arguments after they've been declared, 
// but before the panel is created
//add_filter('redux/options/homey_options/args', 'change_arguments', 10);
//function change_arguments( $args ) {
//   $args['dev_mode'] = true;
//   return $args;
//}

//add_filter('redux/field/homey_options/fieldset/before/homey_options', 'override_User_Roles_For_Registration', 100);
//function override_User_Roles_For_Registration($data){ 
//  if($data['id'] == 'host_role'){
//     $data['default'] = 'Home-Owner';
//  }
//  var_dump($data); 
//  return $data;
//}

//add_filter('redux/field/homey_options/fieldset/after/homey_options', 'override_User_Roles_For_Registration', 200);
//function override_User_Roles_For_Registration($data){ 
//  if($data['id'] == 'host_role'){
//     $data['default'] = 'Home-Owner';
//  }
//  var_dump($data); 
//  return $data;
//}

//add_filter('redux/field/homey_options/render/before', 'override_User_Roles_For_Registration', 200);
//function override_User_Roles_For_Registration($data){ 
//  if($data['id'] == 'host_role'){
//     $data['default'] = 'Home-Owner';
//  }
//  var_dump($data); 
//  return $data;
//}

?>
