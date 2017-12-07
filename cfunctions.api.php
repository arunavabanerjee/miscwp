<?php
if ( ! isset( $content_width ) ) $content_width = 1170;

if( !function_exists( 'kutetheme_ovic_setup')){

	function kutetheme_ovic_setup(){
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on boutique, use a find and replace
		 * to change 'kute-boutique' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'kute-boutique', get_template_directory() . '/languages' );

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
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 825, 510, true );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus( array(
			'primary'             => esc_html__( 'Primary Menu',  'kute-boutique' ),
			'primary-menu-left'   => esc_html__( 'Primary Menu Left',  'kute-boutique' ),
			'primary-menu-right'  => esc_html__( 'Primary Menu Right',  'kute-boutique' ),
			'category'            => esc_html__( 'Category Menu', 'kute-boutique' ),
			'top-menu'            => esc_html__( 'Menu Top Right', 'kute-boutique' ),
			'top-left-menu'       => esc_html__( 'Top bar left menu', 'kute-boutique' ),
			'top-right-menu'      => esc_html__( 'Top bar right menu', 'kute-boutique' ),
			'header_sidebar_menu' => esc_html__( 'Header Sidebar menu', 'kute-boutique' )
		) );
        add_image_size ( 'kutetheme_ovic_post_270x270', 270, 270, true );
        add_image_size ( 'kutetheme_ovic_post_370x1670', 370, 167, true );
        add_image_size ( 'kutetheme_ovic_post_370x275', 370, 275, true );
        add_image_size ( 'kutetheme_ovic_attribute', 40, 40, true );
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
		) );

		//Support woocommerce
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
        add_theme_support( 'wc-product-gallery-zoom' );
	}
}

add_action( 'after_setup_theme', 'kutetheme_ovic_setup' );


/**
 * Register widget area.
 *
 * @since Boutique 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function kutetheme_ovic_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Widget Area', 'kute-boutique' ),
		'id'            => 'widget-area',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'kute-boutique' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Shop Widget Area', 'kute-boutique' ),
		'id'            => 'shop-widget-area',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'kute-boutique' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'kutetheme_ovic_widgets_init' );

// Load Google fonts
function kutetheme_ovic_google_fonts_url()
{
    $fonts_url = '';
    $font_families = array();
    $font_families[] = 'Roboto:300,100,100italic,300italic,400,400italic,500,500italic,700,700italic,900,900italic'; 
    $font_families[] = 'Montserrat:300,400,700';
    $font_families[] = 'Playfair+Display:400,400italic,700,700italic,900,900italic';
    $font_families[] = 'Great+Vibes';
    $font_families[] = 'Oswald:400,700,300';
    $font_families[] = 'Roboto+Slab:300,400,700';

    $query_args = array(
        'family' => urlencode(implode('|', $font_families )),
        'subset' => urlencode('latin,latin-ext')
    );
    $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    return esc_url_raw($fonts_url);
}
/**
 * Enqueue scripts and styles.
 *
 * @since Boutique 1.0
 */
function kutetheme_ovic_scripts() {
	// Load fonts
	wp_enqueue_style( 'kutetheme-ovic-googlefonts', kutetheme_ovic_google_fonts_url(), array(), null );

	// Load lib
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '20160105' );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '20160105' );
	wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/css/owl.carousel.min.css', array(), '20160105' );
	wp_enqueue_style( 'chosen', get_template_directory_uri() . '/css/chosen.min.css', array(), '20160105' );
	wp_enqueue_style( 'lightbox', get_template_directory_uri() . '/css/lightbox.min.css', array(), '20160105' );
	wp_enqueue_style( 'pe-icon-7-stroke', get_template_directory_uri() . '/css/pe-icon-7-stroke.min.css', array(), '20160105' );
	wp_enqueue_style( 'query-mCustomScrollbar', get_template_directory_uri() . '/css/jquery.mCustomScrollbar.min.css', array(), '20160105' );
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.min.css', array(), '20160105' );
	wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css', array(), '20160105' );
	// Load our main stylesheet.
	wp_enqueue_style( 'kutetheme_ovic_main_style', get_stylesheet_uri() );

	// Load theme stylesheet.
	wp_enqueue_style( 'kutetheme_ovic_style', get_template_directory_uri() . '/css/style.min.css', array(), '20160105' );
	wp_enqueue_style( 'kutetheme_ovic_woo', get_template_directory_uri() . '/css/woocommerce.min.css', array(), '20160105' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Load html5 js IE 9
	wp_enqueue_script( 'html5Shiv',  get_template_directory_uri() . '/js/html5.js' );
	wp_script_add_data( 'html5Shiv', 'conditional', 'lt IE 9' );
	// Load lib js
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '20160105', false );
	wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array( 'jquery' ), '20160105', false );
	wp_enqueue_script( 'chosen-jquery', get_template_directory_uri() . '/js/chosen.jquery.min.js', array( 'jquery' ), '20160105', false );
	wp_enqueue_script( 'Modernizr', get_template_directory_uri() . '/js/Modernizr.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'lightbox', get_template_directory_uri() . '/js/lightbox.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'jquery.plugin', get_template_directory_uri() . '/js/jquery.plugin.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), '20160105', true );

	wp_enqueue_script( 'masonry-pkgd', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'jquery-mCustomScrollbar-concat', get_template_directory_uri() . '/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'imagesloaded-pkgd', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array( 'jquery' ), '20160105', true );
	wp_enqueue_script( 'kutetheme_ovic_masonry', get_template_directory_uri() . '/js/masonry.min.js', array( 'jquery' ), '20160105', true );
	
    wp_enqueue_script( 'jquery-parallax', get_template_directory_uri() . '/js/jquery.parallax-1.1.3.js', array( 'jquery' ), '20160105', true );
    wp_enqueue_script( 'kutetheme_ovic_script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160105', true );
    
    
	wp_localize_script( 'kutetheme_ovic_script', 'kt_ajax_fontend', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'kt_ajax_fontend' ),
	) );

    $kt_enable_product_thumb_slide = kutetheme_ovic_option('kt_enable_product_thumb_slide','yes');
    wp_localize_script( 'kutetheme_ovic_script', 'boutique_fontend_global_script', array(
        'kt_enable_product_thumb_slide'=> $kt_enable_product_thumb_slide

    ) );

}

add_action( 'wp_enqueue_scripts', 'kutetheme_ovic_scripts' );

/**
 * wp_enqueue_style and rel other than stylesheet
 * 
 * @author AngelsIT
 * @since Boutique 1.0
 * */
if( ! function_exists( 'kutetheme_ovic_style_loader_tag_function' ) ){
    function kutetheme_ovic_style_loader_tag_function($tag, $handle) {
        global $wp_styles;
        $match_pattern = '/fonts.googleapis.com/';
        if ( preg_match( $match_pattern, $wp_styles->registered[$handle]->src ) ) {
			$handle = $wp_styles->registered[$handle]->handle;
			$media  = $wp_styles->registered[$handle]->args;
			$href   = $wp_styles->registered[$handle]->src . '?ver=' . $wp_styles->registered[$handle]->ver;
			$rel    = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
			$title  = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr( $wp_styles->registered[$handle]->extra['title'] ) . "'" : '';
			
			$tag    = "<link rel='dns-prefetch' id='$handle' $title href='$href' type='text/css' media='$media' />";
        }
        echo apply_filters( 'kt_style_loaded_tag', $tag ) ;
    }
}

if ( ! function_exists( 'kutetheme_ovic_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 * @author AngelsIT
 * @since Boutique 1.0
 */
function kutetheme_ovic_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'kute-boutique' ) );
	}

	$format = get_post_format();
	if ( current_theme_supports( 'post-formats', $format ) ) {
		printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
			sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'kute-boutique' ) ),
			esc_url( get_post_format_link( $format ) ),
			get_post_format_string( $format )
		);
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date(),
			esc_attr( get_the_modified_date( 'c' ) ),
			get_the_modified_date()
		);

		printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
			_x( 'Posted on', 'Used before publish date.', 'kute-boutique' ),
			esc_url( get_permalink() ),
			$time_string
		);
	}

	if ( 'post' == get_post_type() ) {
		if ( is_singular() || is_multi_author() ) {
			printf( '<span class="byline"><span class="author vcard"><span class="screen-reader-text">%1$s </span><a class="url fn n" href="%2$s">%3$s</a></span></span>',
				_x( 'Author', 'Used before post author name.', 'kute-boutique' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}

		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'kute-boutique' ) );
		if ( $categories_list && kt_categorized_blog() ) {
			printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Categories', 'Used before category names.', 'kute-boutique' ),
				$categories_list
			);
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'kute-boutique' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Tags', 'Used before tag names.', 'kute-boutique' ),
				$tags_list
			);
		}
	}

	if ( is_attachment() && wp_attachment_is_image() ) {
		// Retrieve attachment metadata.
		$metadata = wp_get_attachment_metadata();

		printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
			_x( 'Full size', 'Used before full size attachment link.', 'kute-boutique' ),
			esc_url( wp_get_attachment_url() ),
			$metadata['width'],
			$metadata['height']
		);
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( esc_html__( 'Leave a comment on %s', 'kute-boutique' ), get_the_title() ) );
		echo '</span>';
	}
}
endif;


/**
 * Determine whether blog/site has more than one category.
 * @author AngelsIT
 * @since Boutique 1.0
 *
 * @return bool True of there is more than one category, false otherwise.
 */
function kutetheme_ovic_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'kt_categorized_blog' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'kt_categorized_blog', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so kt_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so kt_categorized_blog should return false.
		return false;
	}
}

// Remove each style one by one
add_filter( 'woocommerce_enqueue_styles', 'kutetheme_ovic_dequeue_styles' );
function kutetheme_ovic_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
	unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
	unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
	return $enqueue_styles;
}

// Require Function
require_once(trailingslashit(get_template_directory()).'inc/class-tgm-plugin-activation.php');
require_once(trailingslashit(get_template_directory()).'inc/advance-functions.php');
require_once(trailingslashit(get_template_directory()).'inc/theme-functions.php');
require_once(trailingslashit(get_template_directory()).'inc/nav/nav.php');

if( kutetheme_ovic_is_wc() ){
    require_once(trailingslashit(get_template_directory()).'inc/woocommerce.php');
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


//-----------------------------------------
//search query to redirect to a template
//-----------------------------------------
add_action('init', function() {
  $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/'); 
  //var_dump($url_path); exit();
  if ( strstr($url_path,'product/search' ) ) {
     $load = locate_template('product/search.php', true);
     if ($load) {
        exit(); // just exit if template was found and loaded
     }
  }
  if ( strstr($url_path,'shop/search' ) ) {
     $load = locate_template('product/search-store.php', true);
     if ($load) {
        exit(); // just exit if template was found and loaded
     }
  }
});

add_action( 'template_redirect', 'fb_change_search_url_rewrite', 101 );
function fb_change_search_url_rewrite() {
 global $wp_query; $query_vars = $wp_query->query_vars; //echo 'templ'; var_dump($query_vars); exit; 
 $wp_query->is_404 = false;
 status_header( '200' );

 if ( is_search() && ! empty( $_GET['s'] )  && $query_vars["post_type"] == "any") {
   wp_redirect( home_url( "/product/search/?q=" ) . urlencode( get_query_var( 's' ) ) ); 
  exit();
 }
 if ( is_search() && empty( $_GET['s'] )  && $query_vars["post_type"] == "any") {
   wp_redirect( home_url( "/product/search/?q=" ) . urlencode( get_query_var( 's' ) ) ); 
  exit();
 }
 if ( ! empty( $_GET['seller_nick'] ) ) {
   wp_redirect( home_url( "/shop/search/?seller_nick=" ) . urlencode( $_GET['seller'] ) ); 
  exit();
 }
 if( $query_vars['post_type'] == "product" &&  $query_vars['product'] != "" && $query_vars['s'] == "" ){
   add_filter( 'template_include', function() {
      return get_template_directory() . '/product/single-product.php';
   }); 
 }	
}

/*add_action( 'pre_get_posts', 'query_products' );
function query_products( $query ) { //var_dump($query); echo '<br/><br/>'; 
  if( $query->is_main_query() && ! is_admin() && $query->is_search() ){ 
    if($query->post_type == 'product'){ 
       $query->set('posts_per_page', -1);
       $query->set('post_type', 'products');
    }
  }
  //exit;
  return $query;
}*/

//-----------------
//api functions
//-----------------
//item_search
function ombuyapi_item_search( $parameters ){ 
  $params = '';
  foreach($parameters as $key=>$param){
     if($key == 'q'){ 
       if(strpos(trim($param),' ')){ $param = str_replace(' ', '+',$param); }
       $params .= '&q='.$param; }
     if($key == 'cat'){ $params .= '&cat='.$param; }
     if($key == 'page'){ $params .= '&page='.$param; } 
     if($key == 'start'){ $params .= '&start_price='.$param; } 
     if($key == 'end'){ $params .= '&end_price='.$param; } 
     if($key == 'filter'){ $params .= '&filter='.$param; } 
     if($key == 'sort'){ $params .= '&sort='.$param; } 
     if($key == 'ppath'){ $params .= '&ppath='.$param; }
     if($key == 'lang'){ $params .= '&lang='.$param; } 
  }

  // these params will be added if not present
  if(! array_key_exists ('q',$parameters)){ $params .= '&q='; }
  if(! array_key_exists ('cat',$parameters)){ $params .= '&cat=0'; }
  if(! array_key_exists ('page',$parameters)){ $params .= '&page=1'; }
  if(! array_key_exists ('start',$parameters)){ $params .= '&start_price=0'; }
  if(! array_key_exists ('end',$parameters)){ $params .= '&end_price=0'; }
  if(! array_key_exists ('filter',$parameters)){ $params .= '&filter='; }
  if(! array_key_exists ('sort',$parameters)){ $params .= '&sort=sale'; }
  if(! array_key_exists ('ppath',$parameters)){ $params .= '&ppath='; }
  if(! array_key_exists ('lang',$parameters)){ $params .= '&lang='; }

  //other options
  $params .= '&page_size=40&seller_info=no';
  $params .= '&api_name=taobao_item_search&result_type=json&cache=no';  
  //full api 
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_details
function ombuyapi_item_details($numid){ 
  //ombuy url params generation
  $params = '&api_name=item_get&num_iid='.$numid.'&secret=&result_type=json&cache=no';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_review
function ombuyapi_item_review($numid){ 
  //ombuy url params generation
  $params = '&api_name=item_review&num_iid='.$numid.'&secret=&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_shop_search
function ombuyapi_item_search_shop($parameters){ 
  $params = '';
  foreach($parameters as $key=>$param){
     if($key == 'q'){ $params .= '&q='.$param; }
     if($key == 'cat'){ $params .= '&cid='.$param; }
     if($key == 'page'){ $params .= '&page='.$param; } 
     if($key == 'start'){ $params .= '&start_price='.$param; } 
     if($key == 'end'){ $params .= '&end_price='.$param; } 
     //if($key == 'filter'){ $params .= '&filter='.$param; } 
     if($key == 'sort'){ $params .= '&sort='.$param; } 
     if($key == 'ppath'){ $params .= '&ppath='.$param; } 
     if($key == 'seller_nick'){ $params .= '&seller_nick='.$param; }
  }

  // these params will be added if not present
  if(! array_key_exists ('q',$parameters)){ $params .= '&q='; }
  if(! array_key_exists ('cat',$parameters)){ $params .= '&cid=0'; }
  if(! array_key_exists ('page',$parameters)){ $params .= '&page=1'; }
  if(! array_key_exists ('start',$parameters)){ $params .= '&start_price=0'; }
  if(! array_key_exists ('end',$parameters)){ $params .= '&end_price=0'; }
  //if(! array_key_exists ('filter',$parameters)){ $params .= '&filter='; }
  if(! array_key_exists ('sort',$parameters)){ $params .= '&sort=sale'; }
  if(! array_key_exists ('sort',$parameters)){ $params .= '&ppath='; }
  if(! array_key_exists ('seller_nick',$parameters)){ $params .= '&seller_nick='; }

  //other options
  $params .= '&page_size=40&seller_info=no';
  $params .= '&api_name=item_search_shop&result_type=json&cache=no';  
  //full api 
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//cat_get
function ombuyapi_category_get($categoryId){ 
  //ombuy url params generation
  $params = '&api_name=cat_get&cid='.$categoryId.'&secret=&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_review
function ombuyapi_item_similar($numid){ 
  //ombuy url params generation
  $params = '&api_name=item_search_similar&num_iid='.$numid.'&secret=&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_review
function ombuyapi_item_similar_bypage($numid,$pg){ 
  //ombuy url params generation
  $params = '&api_name=item_search_similar&num_iid='.$numid.'&page='.$pg.'&sort=&page_size=4&secret=&result_type=json&cache=no';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//add product to cart
//POptions: 541261281561?sku-properties=20549:473680452;1627207:5845003;&skuId=34124798862760
add_action( 'wp_ajax_nopriv_action_addProductToCart', 'action_addProductToCart' );
add_action( 'wp_ajax_action_addProductToCart', 'action_addProductToCart' );
function action_addProductToCart() {
  global $woocommerce; $wcproductID = 52265;
  $poptions = $_REQUEST['options']; //echo "POptions: ".$poptions;
  $pSegOptions = explode('?',$poptions);
 
  //get item details from api
  $api_item = ombuyapi_item_details($pSegOptions[0]); 
  $cart_item_data = array(
	'price' => $api_item['item']['price'], 
	'nick'  => $api_item['item']['nick'],
	'num_iid' => $api_item['item']['num_iid'], 
	'detail_url' => $api_item['item']['detail_url'],
	'item_weight' => $api_item['item']['item_weight'],
	'cid' => $api_item['item']['cid'],
	'rootCatId' => $api_item['item']['rootCatId'], 
	'buylink' => $api_item['item']['detail_url'].$_REQUEST['options'],
	'title' => $api_item['item']['title'],
        'pic_url' => $api_item['item']['pic_url']
  );
  $woocommerce->cart->add_to_cart( $wcproductID, 1, '', array(), $cart_item_data);
}

add_action( 'woocommerce_before_calculate_totals', 'add_custom_values' );
function add_custom_values( $cart ) { 
  $wcproductID = 52265; var_dump($cart);
  foreach ( $cart->cart_contents as $key => $value ) {
    $product_id = $value['product_id']; $_wcproduct = $value['data'];
    if($wcproductID == $product_id ){ 
       $_wcproduct->set_price( $value['price'] ); 
       $_wcproduct->set_name( $value['title'] );
    }       
  }
}


