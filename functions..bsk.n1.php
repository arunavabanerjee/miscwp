<?php
/**
 * Twenty Sixteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/**
 * Twenty Sixteen only works in WordPress 4.4 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'twentysixteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * Create your own twentysixteen_setup() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentysixteen
	 * If you're building a theme based on Twenty Sixteen, use a find and replace
	 * to change 'twentysixteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentysixteen' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 *
	 *  @since Twenty Sixteen 1.2
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 240,
		'width'       => 240,
		'flex-height' => true,
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twentysixteen' ),
		'social'  => __( 'Social Links Menu', 'twentysixteen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'status',
		'audio',
		'chat',
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', twentysixteen_fonts_url() ) );

	// Indicate widget sidebars can use selective refresh in the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif; // twentysixteen_setup
add_action( 'after_setup_theme', 'twentysixteen_setup' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'twentysixteen_content_width', 840 );
}
add_action( 'after_setup_theme', 'twentysixteen_content_width', 0 );

/**
 * Registers a widget area.
 *
 * @link https://developer.wordpress.org/reference/functions/register_sidebar/
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'twentysixteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Content Bottom 1', 'twentysixteen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Content Bottom 2', 'twentysixteen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentysixteen_widgets_init' );

if ( ! function_exists( 'twentysixteen_fonts_url' ) ) :
/**
 * Register Google fonts for Twenty Sixteen.
 *
 * Create your own twentysixteen_fonts_url() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function twentysixteen_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/* translators: If there are characters in your language that are not supported by Merriweather, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Merriweather font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Merriweather:400,700,900,400italic,700italic,900italic';
	}

	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Montserrat:400,700';
	}

	/* translators: If there are characters in your language that are not supported by Inconsolata, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Inconsolata:400';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentysixteen_javascript_detection', 0 );

/**
 * Enqueues scripts and styles.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentysixteen-fonts', twentysixteen_fonts_url(), array(), null );

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.4.1' );

	// Theme stylesheet.
	wp_enqueue_style( 'twentysixteen-style', get_stylesheet_uri() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie', 'conditional', 'lt IE 10' );

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie8', get_template_directory_uri() . '/css/ie8.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie8', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'twentysixteen-style' ), '20160816' );
	wp_style_add_data( 'twentysixteen-ie7', 'conditional', 'lt IE 8' );

	// Load the html5 shiv.
	wp_enqueue_script( 'twentysixteen-html5', get_template_directory_uri() . '/js/html5.js', array(), '3.7.3' );
	wp_script_add_data( 'twentysixteen-html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentysixteen-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160816', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'twentysixteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20160816' );
	}

	wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160816', true );
        wp_enqueue_script( 'custom-script', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), false, true );
	wp_localize_script( 'custom-script', 'frontendajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        
	wp_localize_script( 'twentysixteen-script', 'screenReaderText', array(
		'expand'   => __( 'expand child menu', 'twentysixteen' ),
		'collapse' => __( 'collapse child menu', 'twentysixteen' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function twentysixteen_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to sites with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of no-sidebar to sites without active sidebar.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'twentysixteen_body_classes' );

/**
 * Converts a HEX value to RGB.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function twentysixteen_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentysixteen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentysixteen_content_image_sizes_attr', 10 , 2 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function twentysixteen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentysixteen_post_thumbnail_sizes_attr', 10 , 3 );

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @since Twenty Sixteen 1.1
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function twentysixteen_widget_tag_cloud_args( $args ) {
	$args['largest'] = 1;
	$args['smallest'] = 1;
	$args['unit'] = 'em';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'twentysixteen_widget_tag_cloud_args' );

// Register Custom Navigation Walker
require_once('wp-bootstrap-navwalker.php');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Theme Options -- Set the file path based on whether the Options Framework is in a parent theme or child theme 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ( STYLESHEETPATH == TEMPLATEPATH ) {
	define('OF_FILEPATH', TEMPLATEPATH);
	define('OF_DIRECTORY', get_bloginfo('template_directory'));
} else {
	define('OF_FILEPATH', STYLESHEETPATH);
	define('OF_DIRECTORY', get_bloginfo('stylesheet_directory'));
}

// These files build out the options interface.  Likely won't need to edit these. */

//require_once (OF_FILEPATH . '/admin/admin-functions.php');		// Custom functions and plugins
require_once (OF_FILEPATH . '/admin/admin-interface.php');		// Admin Interfaces (options,framework, seo)

/* These files build out the theme specific options and associated functions. */

require_once (OF_FILEPATH . '/admin/theme-options.php'); 		// Options panel settings and custom settings
require_once (OF_FILEPATH . '/admin/theme-functions.php'); 	// Theme actions based on options settings




//create a function that will attach our new 'member' taxonomy to the 'post' post type
function add_member_taxonomy_to_post(){

    //set the name of the taxonomy
    $taxonomy = 'region';
    //set the post types for the taxonomy
    $object_type = 'event_management';
    
    //populate our array of names for our taxonomy
    $labels = array(
        'name'               => 'Regions',
        'singular_name'      => 'Region',
        'search_items'       => 'Search Regions',
        'all_items'          => 'All Regions',
        'parent_item'        => 'Parent Region',
        'parent_item_colon'  => 'Parent Region:',
        'update_item'        => 'Update Region',
        'edit_item'          => 'Edit Region',
        'add_new_item'       => 'Add New Region', 
        'new_item_name'      => 'New Region Name',
        'menu_name'          => 'Region'
    );
    
    //define arguments to be used 
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'show_ui'           => true,
        'how_in_nav_menus'  => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'region')
    );
    
    //call the register_taxonomy function
    register_taxonomy($taxonomy, $object_type, $args); 
}
add_action('init','add_member_taxonomy_to_post');

/* Calender */

/**
 * Registering a new user.
 */
add_action('template_redirect', 'register_user');
 
function register_user(){
   
  if(isset($_GET['do']) && $_GET['do'] == 'register'):
    $errors = array();
    if(empty($_POST['first_name'])) 
       $errors[] = '<div class="errormsg">Please enter a First Name.</div>';
    if(empty($_POST['last_name'])) 
       $errors[] = '<div class="errormsg">Please enter a Last Name.</div>';
     
    if(empty($_POST['user'])) 
       $errors[] = '<div class="errormsg">Please enter a user name.</div>';
    
    if(empty($_POST['email'])) 
       $errors[] = '<div class="errormsg">Please enter a email.</div>';
    
    if(empty($_POST['address1'])) 
       $errors[] = '<div class="errormsg">Please enter a address1.</div>';
    
    //if(empty($_POST['address2'])) 
    //   $errors[] = '<div class="errormsg">Please enter a address2.</div>';
    
    if(empty($_POST['zip_code'])) 
       $errors[] = '<div class="errormsg">Please enter a zip code.</div>';
    
    if(empty($_POST['date_of_birth'])) 
       $errors[] = '<div class="errormsg">Please enter a Date Of birth.</div>';
   
     $dateArr = explode('/', $_POST['date_of_birth']);
     //print_r($dateArr);
   
     $date_convert=date('F j, Y', mktime(0,0,0,$dateArr[1],$dateArr[0],$dateArr[2]));
  
    //$then = strtotime('March 23, 2016');
    $then = strtotime($date_convert);
    //The age to be over, over +18
    $min = strtotime('+18 years', $then);
   
    if(time() < $min) 
    {
    $errors[] = '<div class="errormsg">You need to be 18 years and above to signup</div>';
    }
    
    if(empty($_POST['pass'])) 
       $errors[] = '<div class="errormsg">Please enter a password.</div>';
    if(empty($_POST['cpass'])) 
       $errors[] = '<div class="errormsg">Please enter a confirm password.</div>';
    
    if((!empty($_POST['cpass']) && !empty($_POST['pass'])) && ($_POST['pass'] != $_POST['cpass'])) 
       $errors[] = '<div class="errormsg">Entered password did not match.</div>';
    
    $first_name = esc_attr($_POST['first_name']);
    $last_name = esc_attr($_POST['last_name']);
    $user_login = esc_attr($_POST['user']);
    $user_email = esc_attr($_POST['email']);
    $user_address1 = esc_attr($_POST['address1']);
    $user_address2 = esc_attr($_POST['address2']);
    $user_city = esc_attr($_POST['city']);
    $user_state = esc_attr($_POST['state']);
    $user_zip_code = esc_attr($_POST['zip_code']);
    $user_date_of_birth = esc_attr($_POST['date_of_birth']);
    $user_pass = esc_attr($_POST['pass']);
    $user_confirm_pass = esc_attr($_POST['cpass']);
    $user_height = esc_attr($_POST['height']);
    $user_ft = esc_attr($_POST['ft']);
    $user_inch = esc_attr($_POST['inch']);
  
    
    $sanitized_user_login = sanitize_user($user_login);
    $user_email = apply_filters('user_registration_email', $user_email);
  
    if(!is_email($user_email)) 
       $errors[] = '<div class="errormsg">Invalid e-mail.</div>';
    elseif(email_exists($user_email)) 
       $errors[] = '<div class="errormsg">This email is already registered.</div>';
  
    if(empty($sanitized_user_login) || !validate_username($user_login)) 
       $errors[] = '<div class="errormsg">Invalid user name.</div>';
    elseif(username_exists($sanitized_user_login)) 
       $errors[] = '<div class="errormsg">User name already exists.</div>';
  
    if(empty($errors)):
      $user_id = wp_create_user($sanitized_user_login, $user_pass, $user_email);
  
    if(!$user_id):
      $errors[] = '<div class="errormsg">Registration failed</div>';
    else:
      update_user_option($user_id, 'default_password_nag', true, true);
      wp_new_user_notification($user_id, $user_pass);
      update_user_meta ($user_id, 'first_name', $first_name);
      update_user_meta ($user_id, 'last_name', $last_name);
      update_user_meta ($user_id, 'user_address1', $user_address1);
      update_user_meta ($user_id, 'user_address2', $user_address2);
      update_user_meta ($user_id, 'user_city', $user_city);
      update_user_meta ($user_id, 'user_state', $user_state);
      update_user_meta ($user_id, 'user_zip_code', $user_zip_code);
      update_user_meta ($user_id, 'user_date_of_birth', $user_date_of_birth);
      update_user_meta ($user_id, 'user_height', $user_height);
      update_user_meta ($user_id, 'user_ft', $user_ft);
      update_user_meta ($user_id, 'user_inch', $user_inch);
      
      
      wp_cache_delete ($user_id, 'users');
      wp_cache_delete ($user_login, 'userlogins');
      do_action ('user_register', $user_id);
      $user_data = get_userdata ($user_id);
      if ($user_data !== false) {
         wp_clear_auth_cookie();
         wp_set_auth_cookie ($user_data->ID, true);
         do_action ('wp_login', $user_data->user_login, $user_data);
         // Redirect user.
         wp_redirect ('?page_id=7');
         exit();
       }
      endif;
    endif;
  
    if(!empty($errors)) 
      define('REGISTRATION_ERROR', serialize($errors));
  endif;
}


/**************************  Games Releted Code ***********************************/

add_action( 'wp_ajax_nopriv_action_newrevamptable', 'action_newrevamptable' );
add_action( 'wp_ajax_action_newrevamptable', 'action_newrevamptable' );
function action_newrevamptable() {
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID;
  $html = '';
  $today = date('Ymd'); $cTime = current_time( 'timestamp' ); 

  $metaqry = array(); $metaqry["relation"] = "AND";  
  $intrim = array( 'key' => 'date', 'value' => $today, 'compare' => '==' ); array_push($metaqry, $intrim);
  $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
				      	     'terms' => $regionID, 'include_children' => false, ) ),
		'meta_query' => $metaqry); 
  query_posts($args); 

  $html .= '<table class="table sub-table">';
  if(have_posts()) :
   while ( have_posts() ) : the_post(); 
     $id = get_the_ID(); 
     $noSeats = get_post_meta($id, 'number_of_seats', true); 
     $currBookings = get_post_meta($id, 'current_bookings', true); 
     $rTime = get_post_meta($id, 'remove_game_time', true); 
     $pDate = get_post_meta($id, 'date', true); 
     $rTimestamp = strtotime(date('Y-m-d g:i a', strtotime($pDate.' '.$rTime))); 
 
     $html .= '<tr><td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     $html .= '<td>'.get_the_title().'</td>';
     $html .= '<td>'.get_post_meta($id, 'skill', true).'</td>';
     $html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     if($noSeats != ""){
       if($currBookings == "" || $noSeats > $currBookings){
         $html .= '<td><a href="#" data-id='.$id.' class="btn play-game"'; 
         if($rTimestamp < $cTime){ $html .= ' style="background:#919191;" disabled="true" '; }
         $html .= '>PLAY GAME</a></td></tr>';
       }
       elseif($noSeats == $currBookings){
         $html .= '<td><a href="#" data-id='.$id.' class="btn game-full" style="background:#EEEEEE;" disabled="true">FULL</a></td></tr>';
       }	
     } else{
       $html .= '<td><a href="#" data-id='.$id.' class="btn non-allocated" disabled="disabled">NO SETUP</a></td></tr>';
     }
   endwhile;
  else :
     $html .= '<tr><td></td><td></td><td> No Games Scheduled. Please Check Back Soon. </td><td></td><td></td></tr>';
  endif;

  $html .= '</table>'; 
  wp_reset_query(); 

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

/**
 * create the modal data based on the ID of the event
 */
add_action( 'wp_ajax_nopriv_action_modaldata', 'action_modaldata' );
add_action( 'wp_ajax_action_modaldata', 'action_modaldata' );
function action_modaldata(){
  $gameID = $_REQUEST['id']; //echo "Game Id:".$gameID;
  $userID = get_current_user_id();
  $currPost = get_post($gameID);
  $address = get_post_meta($gameID, 'address', true);
  $date = date("l F d",get_post_meta($gameID, 'date', true));
  $stime = date("g:i a", get_post_meta($gameID, 'start_time', true));
  $etime = date("g:i a", get_post_meta($gameID, 'end_time', true));
  $type = get_post_meta($gameID, 'type', true);
  $skill = get_post_meta($gameID, 'skill', true);
  $price = get_post_meta($gameID, 'price', true);
  $map = get_post_meta($gameID, 'map', true); 
  $noSeats = get_post_meta($gameID, 'number_of_seats', true); 
  $currBookings = get_post_meta($gameID, 'current_bookings', true); 

  $html = '<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" 
           aria-hidden="true">';
  $html .= '<div class="modal-dialog" role="document"><div class="modal-content">';
  $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">'.get_the_title($gameID).'</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>';
  $html .= '<div class="modal-body">';
   
  $html .= '<div class="game-pop-lft"><strong>Venue:</strong> '.$address.'<br/>';
  $html .= '<strong>Date:</strong> '.$date .'<br/>';
  $html .= '<strong>Time:</strong> '.$stime .' - '. $etime .'<br/>';
  $html .= '<strong>Game Type:</strong> '.$type .'<br/>';
  $html .= '<strong>Skill Level:</strong> '.$skill .'<br/>';
  $html .= '<strong>Price:</strong> '.$price .'<br/>';
  $html .= '<strong>Total Seats:</strong> '.$noSeats.' - '.'<strong>Seats Booked:</strong> '.$currBookings.'<br/><br/></div>';
  
  $html .= '<div class="game-pop-rht">'. $map .'<br/><br/></div>'; 
  $html .= '<div class="game-pop-bottom"><input type="checkbox" value="accept" name="acceptRules" />I have read, understand, and agreed to the <a href="'.site_url().'/rules-regulations/">Rules & Regulations and Wavier</a></div>';

  if( is_user_logged_in() ){
    $html .= '<div class="clearfix"></div><a href="'.site_url().'/my-account/?gameid='.$gameID.'&userid='.$userID.'" class="btn game-full">Reserve A Spot</a>';
  } else {
    $html .= '<div class="clearfix"></div><a href="'.site_url().'/signup/" class="btn game-full">Reserve A Spot</a>';
  }

  $html .= '</div><div class="modal-footer">';
             //<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             //<button type="button" class="btn btn-primary">Save changes</button>';
  $html .= '</div></div></div></div>';

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

/**
 * revamping the games table
 */
add_action( 'wp_ajax_nopriv_action_gamesrevamptable', 'action_gamesrevamptable' );
add_action( 'wp_ajax_action_gamesrevamptable', 'action_gamesrevamptable' );
function action_gamesrevamptable() {
  $html = ''; // variable to return
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID; 
  $dates = explode(",", $_REQUEST['dates'] ); 
  $cTime = current_time( 'timestamp' );
  //$current_date = date('j'); 
  //$number_of_days_curr_month = date('t'); 
  //$current_date = $_REQUEST['startDt']; //echo "Start Dt:".$stDt; 
  //$number_of_days_curr_month = $_REQUEST['endDt']; //echo "End Dt:".$endDt; 
  $current_month = date('n'); 

  //create table from the current date to the current month
  //for($cnt = $current_date; $cnt <= $number_of_days_curr_month; $cnt++){
  for($ii = 0; $ii <= sizeof($dates); $ii++){ 
    $cnt = $dates[$ii]; if(empty($cnt)){ continue; }
    $cDate = date('Ymd',mktime(0,0,0,($current_month),$cnt,date('Y')));
 
    //get the list of posts for the query ordered by time
    $metaqry = array(); $metaqry["relation"] = "AND";  
    $intrim = array( 'key' => 'date', 'value' => $cDate, 'compare' => '==' ); array_push($metaqry, $intrim);
    $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		  'meta_key' => 'start_time', 'order_by' => 'meta_value_num', 'order' => 'ASC',
		  'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
					'terms' => $regionID, 'include_children' => false, ) ),
		  'meta_query' => $metaqry); 
    query_posts($args); 
    
    //if posts found list
    if(have_posts()) :
      $html .= '<div class="game-table table-responsive"><table class="table"><tr>';
      $html .= '<th>'.date('D M j',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</th>';
      $html .= '<th>Time</th><th>Location</th><th>Skill</th><th>Type</th><th>Action</th></tr>';
      while ( have_posts() ) : the_post();
     	$id = get_the_ID(); 
     	$noSeats = get_post_meta($id, 'number_of_seats', true); 
     	$currBookings = get_post_meta($id, 'current_bookings', true); 
        $rTime = get_post_meta($id, 'remove_game_time', true); 
        $pDate = get_post_meta($id, 'date', true); 
        $rTimestamp = strtotime(date('Y-m-d g:i a', strtotime($pDate.' '.$rTime))); 

        $html .= '<tr><td>'.date('F j, Y',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</td>';
     	$html .= '<td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     	$html .= '<td>'.get_the_title().'</td>';
     	$html .= '<td>'.get_post_meta($id, 'skill', true).'</td>';
     	$html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     	if($noSeats != ""){
       	  if($currBookings == "" || $noSeats > $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn play-game"'; 
            if($rTimestamp < $cTime){ $html .= ' style="background:#919191;" disabled="true" '; }
            $html .= '>PLAY GAME</a></td></tr>';
          }
          elseif($noSeats == $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn game-full">FULL</a></td></tr>';
          }	
        } else{
           $html .= '<td><a href="#" data-id='.$id.' class="btn non-allocated" disabled="disabled">NO SETUP</a></td></tr>';
        }
      endwhile;
     $html .= '</table></div>';
    endif;
    wp_reset_query(); 
  } // end for

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

/**
 * revamping the games table
 */
add_action( 'wp_ajax_nopriv_action_gamesrevamptableGametypes', 'action_gamesrevamptableGametypes' );
add_action( 'wp_ajax_action_gamesrevamptableGametypes', 'action_gamesrevamptableGametypes' );
function action_gamesrevamptableGametypes() {
  $html = ''; // variable to return
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID; 
  $cTime = current_time( 'timestamp' );
  $dates = explode(",", $_REQUEST['dates'] ); 
  $skills = explode(",",$_REQUEST['skills']); 
  if(empty(array_filter($skills))){ $skills = array("1.0", "1.5","2.0","2.5","3,0","3,5","4.0","4.5","5.0"); }
  $gametypes = explode(",",$_REQUEST['gametypes']); 
  if(empty(array_filter($gametypes))){ $gametypes = array("4 v 4 Half","4 v 4 Full","5 v 5 Full","Open Run","3 v 3 Full","Practice"); }
  //$current_date = date('j'); //$number_of_days_curr_month = date('t'); 
  //$current_date = $_REQUEST['startDt']; //echo "Start Dt:".$stDt; 
  //$number_of_days_curr_month = $_REQUEST['endDt']; //echo "End Dt:".$endDt; 
  $current_month = date('n'); 
  //$html .= var_dump($skills); $html .= var_dump($gametypes);
  
  //create table from the current date to the current month
  //for($cnt = $current_date; $cnt <= $number_of_days_curr_month; $cnt++){
  for($ii = 0; $ii <= sizeof($dates); $ii++){ 
    $cnt = $dates[$ii]; if(empty($cnt)){ continue; }
    $cDate = date('Ymd',mktime(0,0,0,($current_month),$cnt,date('Y'))); $headerprint = 0;
 
    for($jj = 0; $jj <= sizeof($gametypes); $jj++){ 
      $gt1 = $gametypes[$jj]; if(empty($gt1)){ continue; } 

    for($kk = 0; $kk <= sizeof($skills); $kk++){ 
      $sk2 = $skills[$kk]; if(empty($sk2)){ continue; } $html .= $sk2;

    //get the list of posts for the query ordered by time
    $metaqry = array(); $metaqry["relation"] = "AND";  
    $intrim = array( 'key' => 'date', 'value' => $cDate, 'compare' => '==' ); array_push($metaqry, $intrim); 
    $intrimB = array( 'key' => 'type', 'value' => $gt1, 'compare' => 'LIKE' ); array_push($metaqry, $intrimB);
    $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		  'meta_key' => 'start_time', 'order_by' => 'meta_value_num', 'order' => 'ASC',
		  'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
					'terms' => $regionID, 'include_children' => false, ) ),
		  'meta_query' => $metaqry); 
    query_posts($args); 
    
    //if posts found list
    if(have_posts()) :
      if($headerprint == 0){
        $html .= '<div class="game-table table-responsive"><table class="table"><tr>';
        $html .= '<th>'.date('D M j',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</th>';
        $html .= '<th>Time</th><th>Location</th><th>Skill</th><th>Type</th><th>Action</th></tr>'; $headerprint = 1;
      }
      while ( have_posts() ) : the_post();
     	$id = get_the_ID(); 
        $skill = get_post_meta($id, 'skill', true); 
        
	//if skill contains a range
        if(strstr($skill,"-")){ $skillarr = explode("-", $skill); 
          if(!($sk2 >= $skillarr[0] && $sk2 <= $skillarr[1])){ continue; } 
        }elseif($skill == "Any"){ //accepts all
        }elseif(!in_array($skill,$skills)){ continue;}

     	$noSeats = get_post_meta($id, 'number_of_seats', true); 
     	$currBookings = get_post_meta($id, 'current_bookings', true); 
        $rTime = get_post_meta($id, 'remove_game_time', true); 
        $pDate = get_post_meta($id, 'date', true); 
        $rTimestamp = strtotime(date('Y-m-d g:i a', strtotime($pDate.' '.$rTime))); 

        $html .= '<tr><td>'.date('F j, Y',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</td>';
     	$html .= '<td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     	$html .= '<td>'.get_the_title().'</td>';
     	$html .= '<td>'.get_post_meta($id, 'skill', true).'</td>';
     	$html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     	if($noSeats != ""){
       	  if($currBookings == "" || $noSeats > $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn play-game"'; 
            if($rTimestamp < $cTime){ $html .= ' style="background:#919191;" disabled="true" '; }
            $html .= '>PLAY GAME</a></td></tr>';
          }
          elseif($noSeats == $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn game-full">FULL</a></td></tr>';
          }	
        } else{
           $html .= '<td><a href="#" data-id='.$id.' class="btn non-allocated" disabled="disabled">NO SETUP</a></td></tr>';
        }
      endwhile;
     $html .= '</table></div>';
    endif;
    wp_reset_query(); 
  }}}  // end for

  if($html == ""){ 
      $html .= '<div class="game-table table-responsive"><table class="table">';
      $html .= '<tr><td></td><td></td><td> No Games Scheduled. Please Check Back Soon. </td><td></td><td></td></tr>'; 
      $html .= '</table></div>';
  } 

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}


/**
 * revamping the location table for games
 */
add_action( 'wp_ajax_nopriv_action_locationrevamptable', 'action_locationrevamptable' );
add_action( 'wp_ajax_action_locationrevamptable', 'action_locationrevamptable' );
function action_locationrevamptable() {
  $html = ''; // variable to return
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID; 

  //setup the region name based on regionID
  $term = get_term($regionID, 'region'); 
  //setup the subregions based on the regionID
  $terms = get_terms(array('taxonomy' => 'region','hide_empty' => false,
				'order' => 'ASC','order_by' => 'term_id', 'child_of'=>$regionID ));

  $html .= '<div class="col-sm-12 col-xs-12">';
  $html .= '<h3>'.$term->name.'</h3><div class="loaction-box">'; 
  if(count($terms) > 0){
   foreach($terms as $aterm){ 
    $html .= '<div class="checkbox"><label><input type="checkbox" value="'.$aterm->term_id.'" name="locations">'. $aterm->name .'</label></div>'; 
   } 
  } else { 
    $html .= '<div class="checkbox"><label> No Sub Locations Found. </label></div>';
  }//end if
  $html .= '</div></div>';

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}


add_action( 'wp_ajax_nopriv_action_gamesrevamptableLocations', 'action_gamesrevamptableLocations' );
add_action( 'wp_ajax_action_gamesrevamptableLocations', 'action_gamesrevamptableLocations' );
function action_gamesrevamptableLocations() {
  $html = ''; // variable to return
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID; 
  $cTime = current_time( 'timestamp' );
  $dates = explode(",", $_REQUEST['dates'] ); 
  $locations = explode(",", $_REQUEST['locations'] ); 
  //$skills = explode(",",$_REQUEST['skills']); 
  //if(empty($skills)){ $skills = array("1.0", "1.5","2.0","2.5","3,0","3,5","4.0","4.5","5.0"); }
  //$gametypes = explode(",",$_REQUEST['gametypes']);
  //if(empty($gametypes)){ $gametypes = array("4 v 4 Half","4 v 4 Full","5 v 5 Full","Open Run","3 v 3 Full","Practice"); }
  //$current_date = date('j'); //$number_of_days_curr_month = date('t'); 
  //$current_date = $_REQUEST['startDt']; //echo "Start Dt:".$stDt; 
  //$number_of_days_curr_month = $_REQUEST['endDt']; //echo "End Dt:".$endDt; 
  $current_month = date('n'); 
  //$html .= var_dump($skills);
  
  //create table from the current date to the current month
  //for($cnt = $current_date; $cnt <= $number_of_days_curr_month; $cnt++){
  for($ii = 0; $ii <= sizeof($dates); $ii++){ 
    $cnt = $dates[$ii]; if(empty($cnt)){ continue; }
    $cDate = date('Ymd',mktime(0,0,0,($current_month),$cnt,date('Y'))); $headerprint = 0;
 
    for($jj = 0; $jj <= sizeof($locations); $jj++){ 
       $loc1 = $locations[$jj]; if(empty($loc1)){ continue; } 

    //for($kk = 0; $kk <= sizeof($skills); $kk++){ 
    //  $sk2 = $skills[$kk]; if(empty($sk2)){ continue; } $html .= $sk2;

    //get the list of posts for the query ordered by time
    $metaqry = array(); $metaqry["relation"] = "AND";  
    $intrim = array( 'key' => 'date', 'value' => $cDate, 'compare' => '==' ); array_push($metaqry, $intrim); 
    //$intrimB = array( 'key' => 'type', 'value' => $gt1, 'compare' => 'LIKE' ); array_push($metaqry, $intrimB);
    $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		  'meta_key' => 'start_time', 'order_by' => 'meta_value_num', 'order' => 'ASC',
		  'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
					'terms' => $loc1, 'include_children' => false, ) ),
		  'meta_query' => $metaqry); 
    query_posts($args); 
    
    //if posts found list
    if(have_posts()) :
      if($headerprint == 0){
        $html .= '<div class="game-table table-responsive"><table class="table"><tr>';
        $html .= '<th>'.date('D M j',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</th>';
        $html .= '<th>Time</th><th>Location</th><th>Skill</th><th>Type</th><th>Action</th></tr>'; $headerprint = 1;
      }
      while ( have_posts() ) : the_post();
     	$id = get_the_ID(); 
        $skill = get_post_meta($id, 'skill', true); 
        //if skill contains a range
        //if(strstr($skill,"-")){ $skillarr = explode("-", $skill); 
        //  if(!($sk2 >= $skillarr[0] && $sk2 <= $skillarr[1])){ continue; } 
        //}elseif($skill == "Any"){ //accepts all
        //}elseif(!in_array($skill,$skills)){ continue;}
     	$noSeats = get_post_meta($id, 'number_of_seats', true); 
     	$currBookings = get_post_meta($id, 'current_bookings', true); 
        $rTime = get_post_meta($id, 'remove_game_time', true); 
        $pDate = get_post_meta($id, 'date', true); 
        $rTimestamp = strtotime(date('Y-m-d g:i a', strtotime($pDate.' '.$rTime))); 

        $html .= '<tr><td>'.date('F j, Y',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</td>';
     	$html .= '<td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     	$html .= '<td>'.get_the_title().'</td>';
     	$html .= '<td>'.get_post_meta($id, 'skill', true).'</td>';
     	$html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     	if($noSeats != ""){
       	  if($currBookings == "" || $noSeats > $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn play-game"'; 
            if($rTimestamp < $cTime){ $html .= ' style="background:#919191;" disabled="true" '; }
            $html .= '>PLAY GAME</a></td></tr>';
          }
          elseif($noSeats == $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn game-full">FULL</a></td></tr>';
          }	
        } else{
           $html .= '<td><a href="#" data-id='.$id.' class="btn non-allocated" disabled="disabled">NO SETUP</a></td></tr>';
        }
      endwhile;
      $html .= '</table></div>';
    endif;
    wp_reset_query(); 
  }} //}  // end for

  if($html == ""){ 
      $html .= '<div class="game-table table-responsive"><table class="table">';
      $html .= '<tr><td></td><td></td><td> No Games Scheduled. Please Check Back Soon. </td><td></td><td></td></tr>'; 
      $html .= '</table></div>';
  } 

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

/*******************************************************
 *  customer dashboard 
 *******************************************************/
add_action( 'wp_ajax_nopriv_action_gamestableCustomerDashboard', 'action_gamestableCustomerDashboard' );
add_action( 'wp_ajax_action_gamestableCustomerDashboard', 'action_gamestableCustomerDashboard' );
function action_gamestableCustomerDashboard() {
  $html = ''; // variable to return
  $userid = $_REQUEST['userid']; $gameid = $_REQUEST['gameid'];

  //if the user and gameid is provided. 
  if($userid != '' && $gameid != ''){
   $cUser = get_userdata($userid); $roles = $cUser->roles; 
   $gameprice = get_post_meta($gameid, 'price', true); //$html .= $gameprice; 
   $currbookings = get_post_meta($gameid, 'current_bookings', true);
   $max_bookings = get_post_meta($gameid, 'number_of_seats', true);
   $wallet = get_user_meta($cUser->ID, "wc_wallet", true);

   if( $wallet != '' ){
   if(($currbookings != '') && ($currbookings == $max_bookings)){
     $html .= 'Error: Maximum No Of Bookings Done, No More Places.';   
   }else{
     if( $wallet > $gameprice ){ 
      $setwalletprice = $wallet - $gameprice; 
      //if user role is a subscriber
      if( in_array("subscriber", $roles) || in_array("customer", $roles) ){ 
        $user_games = get_user_meta($cUser->ID, 'gamesreg', true); 
        if(empty($user_games)){ 
           $user_games .= $gameid; update_user_meta( $userid, "wc_wallet", $setwalletprice );
           if($currbookings != ""){ $currbookings = $currbookings + 1; }else{ $currbookings = 1;}
           update_post_meta( $gameid, 'current_bookings', $currbookings);
           $html .= '<p> Success: Game Added To Scheduled Games Section. </p>';
        } else { 
            if(!strstr($user_games, $gameid )){ 
              $user_games .= ','.$gameid; update_user_meta( $userid, "wc_wallet", $setwalletprice );
              if($currbookings != ""){ $currbookings = $currbookings + 1; }else{ $currbookings = 1;}
              update_post_meta( $gameid, 'current_bookings', $currbookings);
              $html .= '<p> Success: Game Added To Scheduled Games Section. </p>'; 
            }
        }
        update_user_meta( $userid, 'gamesreg', $user_games );
      }
     } else {
        $html .= 'Error: Wallet Balance Insufficient To Play Game.';
     }
   }//end else
   }else{
     $html .= 'Error: Please Add Money In Your Wallet To Play Game.';
   }
  }

  //when both are blank, then the page is loaded
  if( $userid == '' && $gameid == ''){ 
    $cUser = get_userdata(get_current_user_id()); $roles = $cUser->roles; 
  }
 
  //if user role is a subscriber or customer
  if(!strstr($html, 'Error')){
  if( in_array("subscriber", $roles) || in_array("customer", $roles) ){ 
      $user_games = get_user_meta($cUser->ID, 'gamesreg', true); //echo 'Games:'.var_dump($user_games); 

      if( !empty($user_games) ){
      $games = explode(',',$user_games); 
      //create the table based on the games
      foreach($games as $gameID){
	$gameObj = get_post($gameID);
	//html tables for the games
        $html .= '<div class="league-table game-table table-responsive">';
        $html .= '<div class="league-title">GAME : '.get_the_title($gameID).'</div>';
	$html .= '<table class="table"><tr><th>Details</th><th></th><th></th>';
        $html .= '<th><button type="submit" class="btn" id="cancel-button" data-id="'.$gameID.'" style="padding:0 10px 0 10px;margin:0" aria-hidden="true">Cancel</button></th></tr>';
	$html .= '<tr class="heading"><td>DATE</td><td>TIME</td><td>COMPETITION</td><td>Scores</td></tr>';
	//game data printing
        $pDate = get_post_meta($gameID, 'date', true); 
        $pStrTime = strtotime(date('Y-m-d', strtotime($pDate)));
	$html .= '<tr><td>'.date('F j, Y',$pStrTime).'</td>';
        $html .= '<td>'.date("g:i a", get_post_meta($gameID,'start_time',true)).'</td>'; 
     	$html .= '<td>'.get_the_title($gameID).'</td>';
     	$html .= '<td>'.get_post_meta($gameID,'skill',true).'</td>';
	//table footer
        $html .= '</tr></table></div>';
      }
      } else{
	//html tables for the games
        $html .= '<div class="league-table game-table table-responsive">';
        $html .= '<div class="league-title"></div>';
	$html .= '<table class="table"><tr><th>Details</th><th></th><th></th><th></th></tr>';
	$html .= '<tr class="heading"><td>DATE</td><td>TIME</td><td>COMPETITION</td><td>Scores</td></tr>';
	//game data printing
	$html .= '<tr><td></td><td></td>'; 
     	$html .= '<td>Games Could Not Found</td><td></td>';
	//table footer
        $html .= '</tr></table></div>';
      }
  }
  }
  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}


add_action( 'wp_ajax_nopriv_action_addUserToGroupInCustomerDashboard', 'action_addUserToGroupInCustomerDashboard' );
add_action( 'wp_ajax_action_addUserToGroupInCustomerDashboard', 'action_addUserToGroupInCustomerDashboard' );
function action_addUserToGroupInCustomerDashboard() {
  $html = ''; // variable to return
  $fuserid = $_REQUEST['friendId']; 

  //if the user and gameid is provided. 
  if($fuserid != ''){
   $cUser = get_userdata(get_current_user_id()); $roles = $cUser->roles;
   //if user role is a subscriber
   if( in_array("subscriber", $roles) || in_array("customer", $roles) ){ 
    $user_friends = get_user_meta($cUser->ID, 'users_in_group', true); 
    if(empty($user_friends)){ $user_friends .= $fuserid; 
    } else { 
      if(!strstr($user_friends, $fuserid )){ $user_friends .= ','.$fuserid; }
    }
    update_user_meta( $cUser->ID, 'users_in_group', $user_friends );
   }
  } 

  //when both are blank, then the page is loaded
  if( $fuserid == ''){ 
    $cUser = get_userdata(get_current_user_id()); $roles = $cUser->roles; 
  }
  
  //if user role is a subscriber or customer
  if( in_array("subscriber", $roles) || in_array("customer", $roles) ){ 
      $user_friends = get_user_meta($cUser->ID, 'users_in_group', true); //$html .= 'Friends:'.var_dump($user_friends); 

      if( !empty($user_friends) ){
      $friends = explode(',',$user_friends); 
      //create the table based on the games
      foreach($friends as $friendID){
	$userObj = get_userdata($friendID);
        //user details
        $html .= '<div class="user-box"><div class="user-img">';
        $html .= get_avatar($friendID);
        $html .= '</div><div class="frnd-name">'.$userObj->user_nicename.'</div>';
        $html .= '<span class="samll-name">'.$userObj->user_login.'</span>';
        $html .= '<span class="remove"><i class="fa fa-times" aria-hidden="true"></i></span></div>';
      }
      } else{
	//html tables for the games
        $html .= '<div class="user-box"><div class="user-img"></div>';
        $html .= '<div class="frnd-name"> NO FRIENDS LIST FOUND</div>';
        $html .= '<span class="samll-name"></span>';
        $html .= '<span class="remove"></span></div>';
      }
  }
  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

add_action( 'wp_ajax_nopriv_action_addAmountToWallet', 'action_addAmountToWallet' );
add_action( 'wp_ajax_action_addAmountToWallet', 'action_addAmountToWallet' );
function action_addAmountToWallet() {
  $html = ''; // variable to return
  $amount = $_REQUEST['amount']; 
  global $woocommerce; $wcproductID = 548;
  $cUser = get_userdata(get_current_user_id()); $roles = $cUser->roles; 

  //get the current wallet price
  //$curramount = get_user_meta( $cUser->ID, "wc_wallet", true);
  //if($curramount == ''){ $curramount = 0.0; $curramount = $curramount + $amount; }
  //else{  $curramount = $curramount + $amount; }
  //update user meta
  //update_user_meta( $cUser->ID, "wc_wallet", $curramount ); 

  if(!empty($amount)){
    $cart_item_data = array('price' => $amount, 'title' => 'New Title Added to Cart Product');
    $woocommerce->cart->add_to_cart( $wcproductID, 1, '', array(), $cart_item_data);
  }   


  echo $html; 
  //echo 'revamping request successful';
  die(); // stop executing script
}


add_action( 'wp_ajax_nopriv_action_cancelGameAddAmountWallet', 'action_cancelGameAddAmountWallet' );
add_action( 'wp_ajax_action_cancelGameAddAmountWallet', 'action_cancelGameAddAmountWallet' );
function action_cancelGameAddAmountWallet() {
  $html = ''; // variable to return
  $gameid = $_REQUEST['gameid']; 
  $cUser = get_userdata(get_current_user_id()); $roles = $cUser->roles; 

  //get the user list of games
  $user_games = get_user_meta($cUser->ID, 'gamesreg', true); 
  $user_wallet = get_user_meta($cUser->ID, 'wc_wallet', true);
  //get the price of the game from the post
  $gameprice = get_post_meta($gameid, 'price', true);

  //remove the game from the list
  $user_games_arr = explode(',',$user_games); $array_key = '';
  foreach($user_games_arr as $key => $gid){ if ($gid == $gameid){ $array_key = $key; } }
  array_splice($user_games_arr,$array_key);
  $u_games = implode(',',$user_games_arr);
  update_user_meta( $cUser->ID, "gamesreg", $u_games);

  //return the money to wallet
  $u_wallet = $user_wallet + $gameprice;
  update_user_meta( $cUser->ID, "wc_wallet", $u_wallet); 

  //echo 'revamping request successful';
  die(); // stop executing script
}



/*******************************************************
 *  woocommerce
 *******************************************************/
add_action( 'init', 'jk_remove_wc_breadcrumbs' );
function jk_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );
function wc_empty_cart_redirect_url() {
	return site_url().'/store/';
}

add_filter( 'gettext', 'change_woocommerce_return_to_shop_text', 20, 3 );
function change_woocommerce_return_to_shop_text( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Return to shop' :
                $translated_text = __( 'Return to Store', 'woocommerce' );
                break;
        }
    return $translated_text;
}


add_action( 'woocommerce_before_calculate_totals', 'add_custom_price' );
function add_custom_price( $cart ) {
    foreach ( $cart->cart_contents as $key => $value ) { var_dump($value);
        $_wcproduct = $value['data'];
        $_wcproduct->set_price( $value['price'] );
        $_wcproduct->set_name( $value['title'] );
    }
}

//add post to cart
//add_filter('woocommerce_get_price','diff_posts_woocommerce_get_price',20,2);
//function diff_posts_woocommerce_get_price($price,$post){
//   if($post->post->post_type === 'event_management'){
//      $price = get_post_meta($post->id, "price", true);
//   }else{
//     $price = get_post_meta($post->id, '_regular_price', true);
//     $sale = get_post_meta( $post->id, '_sale_price', true);
//   }
//   return $price;
//}

