
//-- functions.php
//-----------------------------------------
//search query to redirect to a template
//-----------------------------------------
//add_filter('template_include', 'product_search_template', 100);
/*function product_search_template( $template ) {
 if ( is_search() ) { 
   global $wp_query; $query_vars = $wp_query->query_vars;
   if($query_vars['post_type'] == "product"){      
     $ct = locate_template('product/search.php', false, false);
     if ( $ct ) $template = $ct;
   }
 }
 return $template;
}*/

add_action('init', function() {
  $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/'); 
  if ( strstr($url_path,'/product/search' ) ) {
     $load = locate_template('product/search.php', true);
     if ($load) {
        exit(); // just exit if template was found and loaded
     }
  }
});

add_action( 'template_redirect', 'fb_change_search_url_rewrite', 101 );
function fb_change_search_url_rewrite() {
 global $wp_query; $query_vars = $wp_query->query_vars; //var_dump($query_vars); exit; 
 if ( is_search() && ! empty( $_GET['s'] )  && $query_vars["post_type"] == "product") {
   wp_redirect( home_url( "/product/search/?q=" ) . urlencode( get_query_var( 's' ) ) ); 
  exit();
 }	
}

add_action( 'template_redirect', 'load_product_template' );
function load_product_template() { 
  global $wp_query; 
  $query_vars = $wp_query->query_vars; //var_dump($query_vars); exit; 
  if( $query_vars['post_type'] == "product" &&  $query_vars['product'] != "" ){
    add_filter( 'template_include', function() {
       return get_template_directory() . '/product/single-product.php';
    });
  } 
}

add_action( 'pre_get_posts', 'query_products' );
function query_products( $query ) { //var_dump($query);
  // Check that it is the query we want to change: front-end search query
  if( $query->is_main_query() && ! is_admin() && $query->is_search() ){ 
    if($query->post_type == 'product'){ 
       $query->set('posts_per_page', -1);
    }
  }
}

//-----------------
//api functions
//-----------------
//item search
function ombuyapi_item_search($searchterm,$pageid=1){ 
  //ombuy url params generation
  $params = $searchterm;  
  $params .= '&start_price=0&end_price=0&cat=0&page='.$pageid.'&page_size=42&seller_info=no&api_name=taobao_item_search&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=&q=';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item details
function ombuyapi_item_details($numid){ 
  //ombuy url params generation
  $params = '&api_name=item_get&num_iid='.$numid.'&secret=&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com';
  $ombuyURL .= $params; //var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

-------------------------------------------------------------------------------------------------------------------

<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

get_header();
//$data = ombuyapi_item_search($_GET['s']); 
if($_GET['page']){ $data = ombuyapi_item_search( $_GET['q'], $_GET['page'] );
} else {  $data = ombuyapi_item_search($_GET['q']);  } 

?>

<div class="main-container shop-page left-slidebar">
<div class="container">

    <header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>

	<?php endif; ?>
    </header>

    <div class="row">

	<div class="main-content col-md-12 col-sm-12">

	<?php if ( count($data['items']['item']) > 0 ) : ?>

	    <div class="shop-top" style="margin-top:30px;">
                <nav class="woocommerce-breadcrumb" style="float: left;padding-top: 10px;width: 100%;padding-bottom: 10px;">
		 <a href="#">Home</a>
		 <?php if( count($data['items']['breadcrumbs']['catpath']) > 0 ) :?>
		  <?php $breadcrumbs = $data['items']['breadcrumbs']['catpath']; ?>	
		  <?php foreach ($breadcrumbs as $value ): echo '/'; ?><a href="#" ><?php echo $value['name']; ?></a><?php endforeach;?>
		 <?php endif; ?>
		</nav>	
		<div class="shop-top-left">
			<h1 class="shop-title">Search results for <?php echo $data['items']['keyword']; ?></h1>
		</div>
		<div class="clear"></div> 

		<!-- ---- respective filters for the results ---- -->
		<hr/>
                <?php if(count($data['items']['navs']) > 0 ){ ?>
		<div class="shop-top-brand" style="width:100%">
		<?php foreach($data['items']['navs'] as $navigation){ ?> 
		<?php foreach($navigation as $navi){ ?>
		<?php if( $navi["data_param_type"] == "ppath" ){ ?>
		<div class="col-md-3 col-sm-3"><h2 class="shop-title"><?php echo $navi["title"]; ?></h2></div>
		<div class="col-md-9 col-sm-9">
		<ul class="navigation" style="margin:5px 0; border-top:0; padding:0;">
		<?php foreach($navi["item"] as $navValue){ ?>
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>">
		 <input type="checkbox" id="<?php echo $navValue["param_value"]; ?>" name="<?php echo $navValue["param_value"]; ?>" />
		 <a href="#"><?php echo $navValue["title"]; ?></a>
		 </li>
		<?php } ?></ul></div>
		<div class="clear" style="border-top:1px solid #eee; margin-bottom:20px;"></div>	
		<?php }}} ?>
		</div>
		<?php } ?>
	    </div>

		<div class="product-list-grid desktop-columns-3 tablet-columns-2 mobile-columns-1 mobile-ts-columns-1">
		<ul class="products">
		<?php foreach( $data['items']['item'] as $pItem ){ //var_dump($pItem); echo '<br/>'; ?>
		<?php $itemlinks = explode('?', $pItem["detail_url"]); $attributes = explode('&', $itemlinks[1]); 
                      $ids = explode('=', $attributes[0]); $productId = $ids[1];  ?>

		<li class="product-item flash1 style1 col-md-4 col-sm-6 col-xs-12 col-ts-12 post-1378 product type-product status-publish  instock shipping-taxable product-type-simple">
		<div class="product-inner">
		<div class="product-thumb">
		  <div class="status"></div>
		  <div class="thumb-inner">
		    <a class="thumb-link" href="<?php echo get_home_url().'/product/'.$productId; ?>">
		      <img class="attachment-post-thumbnail wp-post-image" src="<?php echo 'http:'.$pItem["pic_url"]; ?>" alt="" width="350" height="350">
			</a>
			</div>
		</div>
		<div class="product-info">
		<h3 class="product-name short">
		  <a href="<?php echo get_home_url().'/product/'.$productId; ?>"><?php echo $pItem["title"]; ?></a>
		</h3>
		<h3 class="product-name short">
                  <?php if( $pItem["promotion_price"] <  $pItem["price"] ) { ?>
		   <span class="promo-price" style="padding:10px;">
			<span class="woocommerce-Price-currencySymbol">¥</span><?php echo $pItem["promotion_price"]; ?>
		   </span>
                  <?php } ?>
		  <span class="price" style="padding:10px;">
		      <span class="woocommerce-Price-currencySymbol">¥</span><?php echo $pItem["price"]; ?>
		  </span>
		</h3>
		<div class="rating" title="Rated 0 out of 5">
			<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
			<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
		</div>
		<a rel="nofollow" href="<?php echo get_home_url().'/product/'.$productId; ?>" data-quantity="1" data-product_id="1378" data-product_sku="" class="button product_type_simple ajax_add_to_cart">Read more</a>	
		</div>
		</div>	
		</li>
		<?php } ?>

		</ul>
		</div>

		<?php /* ================ pagination =============================== */ ?>
		<ul class="pagination">
		<?php if($_GET['page']){ ?>		
	        <?php echo paginate_links(array(
                    	'base' => substr(get_pagenum_link(1),0,strlen(get_pagenum_link(1)) - 8 ) . '%_%', 
			'format' => '&page=%#%',
                    	'current' => $data['items']['page'], 
			'total' => $data['items']['real_total_results'],
                    	'prev_text'    => __('« prev'), 
			'next_text'    => __('next »'), )); ?>
		<?php } else { ?>
	        <?php echo paginate_links(array(
                    	'base' => get_pagenum_link(1) . '%_%', 
			'format' => '&page=%#%',
                    	'current' => $data['items']['page'], 
			'total' => $data['items']['real_total_results'],
                    	'prev_text'    => __('« prev'), 
			'next_text'    => __('next »'), )); ?>
		<?php } ?>
		</ul>
		<?php /* ================ pagination =============================== */ ?>


	<?php else : ?>


		<?php do_action( 'woocommerce_no_products_found' ); ?>


	<?php endif; ?>
       </div>

   </div> <!--- end row -->	
</div>
</div>

<?php 
//get_footer( 'shop' ); 
get_footer(); 
?>

