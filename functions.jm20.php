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
	 * @link https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
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

	/* translators: If there are characters in your language that are not supported by Merriweather, translate this to 'off'. 
	   Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Merriweather font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Merriweather:400,700,900,400italic,700italic,900italic';
	}

	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. 
	   Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Montserrat:400,700';
	}

	/* translators: If there are characters in your language that are not supported by Inconsolata, translate this to 'off'. 
	   Do not translate into your own language. */
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

	if ( 840 <= $width ) {
		$sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';
	}

	if ( 'page' === get_post_type() ) {
		if ( 840 > $width ) {
			$sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
		}
	} else {
		if ( 840 > $width && 600 <= $width ) {
			$sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		} elseif ( 600 > $width ) {
			$sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
		}
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
 * @return array The filtered attributes for the image markup.
 */
function twentysixteen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			$attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		} else {
			$attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
		}
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentysixteen_post_thumbnail_sizes_attr', 10 , 3 );

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size
 * and use list format for better accessibility.
 *
 * @since Twenty Sixteen 1.1
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array The filtered arguments for tag cloud widget.
 */
function twentysixteen_widget_tag_cloud_args( $args ) {
	$args['largest']  = 1;
	$args['smallest'] = 1;
	$args['unit']     = 'em';
	$args['format']   = 'list'; 

	return $args;
}
add_filter( 'widget_tag_cloud_args', 'twentysixteen_widget_tag_cloud_args' );

// Register Custom Navigation Walker
require_once get_template_directory() . '/bs4navwalker.php';



function add_faqcategory_taxonomy_to_post(){

   //set the name of the taxonomy
   $taxonomy = 'faqcategory';
   //set the post types for the taxonomy
   $object_type = 'faq';
   
   //populate our array of names for our taxonomy
   $labels = array(
       'name'               => 'FaqCategory',
       'singular_name'      => 'FaqCategory',
       'search_items'       => 'Search FaqCategory',
       'all_items'          => 'All FaqCategory',
       'parent_item'        => 'Parent FaqCategory',
       'parent_item_colon'  => 'Parent FaqCategory:',
       'update_item'        => 'Update FaqCategory',
       'edit_item'          => 'Edit FaqCategory',
       'add_new_item'       => 'Add New FaqCategory',
       'new_item_name'      => 'New FaqCategory Name',
       'menu_name'          => 'FaqCategory'
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
       'rewrite'           => array('slug' => 'faqcategory')
   );
   
   //call the register_taxonomy function
   register_taxonomy($taxonomy, array( 'faq', 'faqcategory' ), $args);
   //register_taxonomy( 'product-category', array( 'reviews', 'inspiration', 'manuals' ), $args );
}
add_action('init','add_faqcategory_taxonomy_to_post'); 

/**
 * Add customized table to u-member-plugin 
 * 
 * Same table as in um-actions-account.php
 */ 
function customized_agent_policy_table(){ 
	global $wpdb;
	$user_id = get_current_user_id();
	$list = $wpdb->get_results("SELECT * FROM `ins_policy_applicants` WHERE `userID`= $user_id ORDER BY id DESC");	
?>
<div class="um-account-heading uimob340-hide uimob500-hide">
<i class="um-faicon-user"></i>Insurance</div>
<table id="example1" class="display" style="width:100%">
<thead>
    <tr><th>Applicant's Name</th><th>Maid's Name</th>
        <th>Package</th><th>Plan Selection</th>
		<!--<th>Downloaded</th>-->
		<th>Entry Date</th>
	</tr>
</thead>
<tbody>
	<?php $array_list = array("name","nric_no","plan" );
	foreach($list as $listValue){
        if($listValue->policy_id==1){$pol1='Domestic Maid Insurance'; }
        if($listValue->policy_id==8){$pol1='Foreign Worker Medical Insurance';}
        if($listValue->policy_id==4){$pol1='Philippines Embassy Bond'; }
        if($listValue->policy_id==3){
            $pol1='Work Injury Compensation Insurance'; } 
    ?>
    <tr>
    <td><?php echo ucfirst($listValue->ins_party_nm);?></td>
    <td><?php echo $listValue->ins_person;?></td>
    <td><?php echo $pol1;?></td>
    <td><?php echo $listValue->ins_class;?>
        <?php echo $listValue->plan_selection;?></td>
    <!--<td><a href="#">Policy Downloaded</a></td>-->
    <td><?php echo $listValue->entry_date;?></td>
    </tr>
    <?php } ?>
</tbody>       
</table>
<?php
} 
//add_action('um_before_form','customized_agent_policy_table'); 
//add_action('um_account_page_hidden_fields','customized_agent_policy_table'); 


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
	return site_url();
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

add_action( 'woocommerce_after_cart_table', 'woo_add_continue_shopping_button_to_cart' );
function woo_add_continue_shopping_button_to_cart() {
  $shop_page_url = get_site_url();
  echo '<div class="woocommerce-info">';
  echo ' <a href="'.$shop_page_url.'" class="button" style="float:left;"> Continue Shopping </a>';
  echo '</div>';
}

add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); 
function woo_custom_order_button_text() {
    return __( 'Submit Application', 'woocommerce' ); 
}

add_filter( 'wc_add_to_cart_message', 'remove_add_to_cart_message' );
function remove_add_to_cart_message() {
    return;
}

add_action( 'admin_menu', 'remove_woocommerce_product_menus', 999 );
function remove_woocommerce_product_menus(){
    remove_menu_page( 'edit.php?post_type=product' );
}

add_filter('woocommerce_cart_item_permalink','__return_false');
add_filter( 'woocommerce_order_item_permalink', '__return_false' );

add_action( 'woocommerce_init', 'action_woocommerce_init' );
function action_woocommerce_init(){ $pid = '356'; 
  if( !is_admin()){
   if(WC()->session->get('wcpid') == NULL){WC()->session->set('wcpid',$pid);}
   //set the policyid from the request variables 
   if(WC()->session->get('policyids') == NULL){WC()->session->set('policyids','');}
  } 
}

add_action('wp_ajax_add_custom_data_woocommerce_session', 'func_add_custom_data_woocommerce_session');
add_action('wp_ajax_nopriv_add_custom_data_woocommerce_session', 'func_add_custom_data_woocommerce_session');
function func_add_custom_data_woocommerce_session() {
 $policyid = $_REQUEST['ins']; $pid = '356'; //var_dump($policyid);
 //reset the session variable 
 if(WC()->cart->get_cart_contents_count() == 0){WC()->session->set('policyids','');} 
 //set policy ids in session
 $policyids = WC()->session->get('policyids'); //var_dump($policyids);
 if($policyids != ''){ 
   $arr_policyids = explode(',', $policyids); 
   if(!in_array($policyid, $arr_policyids)){ 
     array_push($arr_policyids, $policyid); 
     //$arr_policyids_diff = array_diff($arr_policyids,['']); 
     //$policyids = implode(',', $arr_policyids_diff);        
     $policyids = implode(',', $arr_policyids); 
     WC()->session->set('policyids', $policyids);
   }
 }else{
   $policyids = $policyid.','; 
   WC()->session->set('policyids', $policyids);
 }
 die();
}

add_filter( 'woocommerce_add_cart_item_data', 'filter_woocommerce_add_cart_item_data', 10, 2 );
function filter_woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
  global $wpdb;
  $policyidsStr = WC()->session->get('policyids'); 
  if($policyidsStr == ''){ $policyidsStr = $_REQUEST['pl'].','; }
  $policyids = explode(',',$policyidsStr); //var_dump($policyids); 
  $items = WC()->cart->get_cart(); //var_dump($items); exit; 
  foreach($policyids as $policyid){ //echo $policyid;
    if($policyid != ''){ $flag = 0;
    //check if policy id is in cart
    foreach($items as $cItem){ //var_dump($cItem['plan-id']);
        if(strstr($cItem['plan-id'],$policyid)){ $flag = 1; break; }}
    if($flag == 0){ 
          //echo $flag; echo 'Inside else'; 
          $unique_cart_item_key = md5( microtime() . rand() );
          $cart_item_data['unique_key'] = $unique_cart_item_key; 
          $post_id = $wpdb->get_row("SELECT * from ins_policy_applicants where id=$policyid");//print_r($post_id);
          $postall_id = $wpdb->get_results("SELECT * from ins_policy_applicants where insumem_ID=$policyid"); 
          //if set post_id
          if($post_id){ 			  
    	   $cart_item_data['name-of-appln'] = $post_id->ins_party_nm;
    	   $cart_item_data['contact-person'] = $post_id->contact_person;
           $cart_item_data['email'] = $post_id->email;
	       $cart_item_data['phone'] = $post_id->ph_no;
	       //will be added to for multiple items
	        $cart_item_data['plan-id'] = $post_id->id;
	        $cart_item_data['person-name'] = $post_id->ins_person;
	        $cart_item_data['plan-selected'] = $post_id->ins_class;
	        $cart_item_data['plan-price'] = $post_id->Total_premium_amt; 
	        $cart_item_data['gstamt'] = $post_id->gstamt;
          }
          //if set postall_id
          if($postall_id){
            foreach($postall_id as $postal_id){
	            $plan_id = $cart_item_data['plan-id']; $nplan_id = $plan_id.','.$postal_id->id; 
	            $cart_item_data['plan-id'] = $nplan_id;
	            $person_name = $cart_item_data['person-name']; $nperson = $person_name.','.$postal_id->ins_person; 
	            $cart_item_data['person-name'] = $nperson; 
	            $plan_price = $cart_item_data['plan-price']; $nplan_price = $plan_price.','.$postal_id->Total_premium_amt;
	            $cart_item_data['plan-price'] = $nplan_price;
	            $gstamt = $cart_item_data['gstamt']; $ngstamt = $gstamt.','.$post_id->gstamt;
	            $cart_item_data['gstamt'] = $ngstamt;
            }
          }
    }//if flag
    }//if $policyid 
  }//end foreach
  return $cart_item_data;
}

add_filter( 'woocommerce_get_item_data', 'filter_woocommerce_get_item_data', 10, 2);
function filter_woocommerce_get_item_data( $data, $cartItem ) { 
    //var_dump($cartItem);
    if ( isset( $cartItem['plan-id'] ) ) { 
	  $plan_name = '';	
      if($cartItem['plan-selected'] == 'WCS'){ $plan_name = 'Work Injury Compensation Insurance'; }
      if($cartItem['plan-selected'] == 'FWMI'){ $plan_name = 'S$15,000 Foregin worker medical insurance'; }
	  //philippines embassy bond 	
      if($cartItem['plan-selected'] == 'S2A' ){ $plan_name = 'EMBASSY BOND ($2000)'; } 
	  if($cartItem['plan-selected'] == 'S2D'){ $plan_name = 'EMBASSY BOND ($7000)'; } 
	  //domestic maid insurance	
      if($cartItem['plan-selected'] == 'S5A' ){ $plan_name = 'Bond & Insurance Plan A'; }
	  if($cartItem['plan-selected'] == 'S5B' ){ $plan_name = 'Bond & Insurance Plan B'; } 
	  if($cartItem['plan-selected'] == 'S5C' ){ $plan_name = 'Bond & Insurance Plan C'; } 
	  if($cartItem['plan-selected'] == 'S5D' ){ $plan_name = 'Bond & Insurance Plan D'; }
	  //domestic maid insurance	
	  if($cartItem['plan-selected'] == 'S5AP' ){ $plan_name = 'Bond,Insurance & Reimbursement of Indemnity Plan A'; }
	  if($cartItem['plan-selected'] == 'S5BP' ){ $plan_name = 'Bond,Insurance & Reimbursement of Indemnity Plan B'; }	
	  if($cartItem['plan-selected'] == 'S5CP' ){ $plan_name = 'Bond,Insurance & Reimbursement of Indemnity Plan C'; }
      if($cartItem['plan-selected'] == 'S5DP' ){ $plan_name = 'Bond,Insurance & Reimbursement of Indemnity Plan D'; }		
        $data[] = array( 'name' => 'Insured: ', 'value' => $cartItem['name-of-appln'] );
        //$data[] = array( 'name' => 'Contact Person: ', 'value' => $cartItem['contact-person'] );
        //$data[] = array( 'name' => 'Email: ', 'value' => $cartItem['email'] );
        //$data[] = array( 'name' => 'Phone: ', 'value' => $cartItem['phone'] );
        $data[] = array( 'name' => 'Selected Plan: ', 'value' => $plan_name );
	//use for multiple selections
    //$data[] = array( 'name' => 'Plan Id: ', 'value' => $cartItem['plan-id'] );
	if(strstr($cartItem['person-name'],',')){
	  $persons = explode(',',$cartItem['person-name']);
      foreach($persons as $person){
	    $data[] = array( 'name' => 'Insured For: ', 'value' => $person );
	  }
	} else { 
          $data[] = array( 'name' => 'Insured For: ', 'value' => $cartItem['person-name'] );
	}
    }
    return $data;
}

add_action('woocommerce_before_calculate_totals', 'action_woocommerce_before_calculate_totals');
function action_woocommerce_before_calculate_totals( $cart ) { 
  //var_dump(WC()->session);
  global $wpdb; 
  foreach ( $cart->cart_contents as $key => $value ) { //var_dump($key); var_dump($value); 
   if(!isset($value["plan-id"])){ $cart->remove_cart_item($key); }
    $_wcproduct = $value['data']; //var_dump($value['data']);
    //set title
    if($value['plan-selected'] == 'WCS'){ $_wcproduct->set_name('Work Injury Compensation Insurance'); }
    if($value['plan-selected'] == 'FWMI'){ $_wcproduct->set_name('Foreign Worker Medical Insurance'); }
    if($value['plan-selected'] == 'S2A' || $value['plan-selected'] == 'S2D'){ 
		$_wcproduct->set_name('Phillipines Embassy Bond Insurance'); 
	}
    if($value['plan-selected'] == 'S5A' || $value['plan-selected'] == 'S5B' || 
	$value['plan-selected'] == 'S5C' || $value['plan-selected'] == 'S5D' ){ 
		$_wcproduct->set_name('Domestic Maid Insurance'); 
	}
    if($value['plan-selected'] == 'S5AP' || $value['plan-selected'] == 'S5BP' || 
	$value['plan-selected'] == 'S5CP' || $value['plan-selected'] == 'S5DP' ){ 
		$_wcproduct->set_name('Domestic Maid Insurance'); 
	}

    //add price and gst amt 
    $cprice = $value['plan-price'];
    $gstamt = $value['gstamt'];
    if(! strstr($value['plan-price'],',')){
      if($value['gstamt'] !== ''){
        $gstamt = $value['gstamt'];
        $gstval = ($cprice*$gstamt)/100;
        $_wcproduct->set_price($cprice+$gstval);
      } else {
        $_wcproduct->set_price($cprice);
      }
    } else {
	$arr_prices = explode(',',$cprice); $arr_gstamt = explode(',',$gstamt);
 	$mainprice = ''; foreach($arr_prices as $arr_price){ $mainprice += $arr_price; }
        if($arr_gstamt[0] !== ''){
          $gstamt = $arr_gstamt[0];
          $gstval = ($mainprice*$gstamt)/100;
          $_wcproduct->set_price($mainprice+$gstval);
        } else {
          $_wcproduct->set_price($mainprice);
        }
    }
  }//end foreach
}

/*add_action('woocommerce_cart_updated','action_woocommerce_cart_updated');
function action_woocommerce_cart_updated($cart){
  var_dump($_REQUEST); var_dump($_SERVER);
  if($cart != null){    
    $count = $cart->cart_contents_count;
    if($count == 0){ 
        $url = get_bloginfo('url').'/cart';
        wp-redirect($url);
    }
  }    
}*/

add_filter('woocommerce_checkout_fields','func_woocommerce_set_input_attrs');
function func_woocommerce_set_input_attrs( $fields ) { 
  global $woocommerce, $wpdb; 
  if(!is_user_logged_in()){
    //get cart from $woocommerce
    $items = $woocommerce->cart->get_cart(); //var_dump($items);
    $lastkey = ''; foreach($items as $key => $value){ $lastkey = $key; }
    $cartItem =  $items[$lastkey]; //var_dump($cartItem);
    $post_id = $wpdb->get_row("SELECT * from ins_policy_applicants where id='".$cartItem['plan-id']."'");
    //set the checkout fields
    //var_dump($fields);
    $fields['billing']['billing_first_name']['default'] = $post_id->ins_party_nm;
    $fields['billing']['billing_last_name']['default'] = $post_id->contact_person;
    $fields['billing']['billing_company']['default'] = $post_id->ins_party_nm;
    $fields['billing']['billing_address_1']['default'] = $post_id->addr1;
    $fields['billing']['billing_address_2']['default'] = $post_id->addr2;
    $fields['billing']['billing_city']['default'] = $post_id->country;
    $fields['billing']['billing_postcode']['default'] = $post_id->postal;
    $fields['billing']['billing_country']['default'] = $post_id->country;
    $fields['billing']['billing_state']['default'] = $post_id->country;
    $fields['billing']['billing_email']['default'] = $post_id->email;
    $fields['billing']['billing_phone']['default'] = $post_id->ph_no;
  }
  return $fields;
}

add_filter( 'woocommerce_available_payment_gateways', 'filter_payment_gateways_asper_agent' );
function filter_payment_gateways_asper_agent( $available_gateways ) {
 global $woocommerce, $ultimatemember;
 $wpuser = wp_get_current_user(); $userid = 'user_'.$wpuser->ID;
 $payment_option = get_field('payment_option', $userid); //var_dump($payment_option);
 $memrole = $ultimatemember->user->get_role(); 
 if($memrole == 'agents' && $payment_option == 'cash'){
  /*if(isset( $available_gateways['bacs'])){ unset( $available_gateways['bacs']); }      
  if(isset( $available_gateways['paypal'])){ unset( $available_gateways['paypal']); } 
  if(isset( $available_gateways['advanced'])){ unset( $available_gateways['advanced']); }*/ 
  if(isset( $available_gateways['cheque'])){ unset( $available_gateways['cheque']); }
  if(isset( $available_gateways['cod'])){ unset( $available_gateways['cod']); }   
 }elseif($memrole == 'agents' && $payment_option == 'credit'){ 
  if(isset( $available_gateways['bacs'])){ unset( $available_gateways['bacs']); }      
  if(isset( $available_gateways['paypal'])){ unset( $available_gateways['paypal']); } 
  if(isset( $available_gateways['advanced'])){ unset( $available_gateways['advanced']); } 
  if(isset( $available_gateways['cheque'])){ unset( $available_gateways['cheque']); }
  /*if(isset( $available_gateways['cod'])){ unset( $available_gateways['cod']); }*/ 
 }elseif($memrole == 'agents' && $payment_option == 'cash-credit'){
  /*if(isset( $available_gateways['bacs'])){ unset( $available_gateways['bacs']); }      
  if(isset( $available_gateways['paypal'])){ unset( $available_gateways['paypal']); } 
  if(isset( $available_gateways['advanced'])){ unset( $available_gateways['advanced']); }*/ 
  if(isset( $available_gateways['cheque'])){ unset( $available_gateways['cheque']); }
  /*if(isset( $available_gateways['cod'])){ unset( $available_gateways['cod']); }*/ 
 }else{
  if(isset( $available_gateways['cod'])){ unset( $available_gateways['cod']); }
 }
return $available_gateways;
}

add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );
function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
 $wp_new_user_notification_email['subject'] = sprintf('InsureAsia Portal Log In', $blogname, $user->user_login );
 $message .= __( 'Please note the following username to login:' ). "\r\n\r\n";
 $message .= sprintf(__( 'Your username: %s' ), $user->user_login ) . "\r\n";
 $message .= __( 'To set your password, visit the following address: http://sketchwebsolutions.in/P1121-Joomla/login/' ) . "\r\n\r\n";
 $message .= sprintf(__( 'You will need to reset your password using the "Forgot Password Link')) . "\r\n\r\n";
 $message .= sprintf(__( 'If you have any problems, please contact us at info@windev.com.sg')) . "\r\n\r\n";
 $wp_new_user_notification_email['message'] = $message;
 $wp_new_user_notification_email['headers'] = "From: InsureAsia <info@windev.com.sg>";
return $wp_new_user_notification_email;
}

/**
 * Add custom code to the thank-you page
 */
add_action( 'woocommerce_thankyou', 'add_final_status_update' );
function add_final_status_update( $order_id ) {
  global $wpdb; //var_dump(WC()->session);
  $table_name = 'ins_policy_applicants';
  $policyids = WC()->session->get('policyids'); //var_dump($policyids); 
  $arr_policyids = explode(',', $policyids); //var_dump($arr_policyids);
  foreach($arr_policyids as $policyid){ 
    if($policyid != ''){ 
	  $data = array('export_type'=>'new'); 
      $where = array('id' => $policyid);
	  $updt_result = $wpdb->update($table_name, $data, $where); 
	  //if there are more records update
	  $postall_id = $wpdb->get_results("SELECT * from ins_policy_applicants where insumem_ID=$policyid");
      foreach($postall_id as $row){
        $data = array('export_type'=>'new');
        $where = array('id' => $row->id);
	    $updt_result = $wpdb->update($table_name, $data, $where);
      }
    }
  }//endforeach    
  WC()->session->__unset('policyids');
}

/**
 * logout clear cart data
 */
add_action('wp_login', 'clear_cart_on_login_logout'); 
add_action('wp_logout', 'clear_cart_on_login_logout');
function clear_cart_on_login_logout(){
  if( function_exists('WC') ){
    WC()->cart->empty_cart();
    WC()->session->__unset('wcpid', $pid); 
    WC()->session->__unset('policyids'); 
  }
} 

add_filter( 'woocommerce_checkout_fields' ,'my_override_checkout_fields' );
function my_override_checkout_fields( $fields ) {
     unset($fields['billing']['billing_company']);
     return $fields;
}

// rename the coupon field on the checkout page 
function woocommerce_rename_coupon_field_on_checkout( $translated_text, $text, $text_domain ) { 
// bail if not modifying frontend woocommerce text 
	if ( is_admin() || 'woocommerce' !== $text_domain ) { return $translated_text; } 
	if ( 'Coupon code' === $text ) { $translated_text = 'Promo Code'; } 
	elseif ( 'Apply coupon' === $text ) { $translated_text = 'Apply Code'; } 
	return $translated_text; } 
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_checkout', 10, 3 );

//set wc session expiry
add_filter('wc_session_expiring', 'filter_ExtendSessionExpiring' );
add_filter('wc_session_expiration' , 'filter_ExtendSessionExpired' );
function filter_ExtendSessionExpiring($seconds) {
    return 60 * 60 * 2;
}
function filter_ExtendSessionExpired($seconds) {
   return 60 * 60 * 2;
}

