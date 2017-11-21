
//-------------------------------------------------------------------------functions.php

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
  $params .= '&page_size=48&seller_info=no';
  $params .= '&api_name=taobao_item_search&result_type=json&cache=no';  
  //full api 
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}

//item_details
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
  $params .= '&page_size=48&seller_info=no';
  $params .= '&api_name=item_search_shop&result_type=json&cache=no';  
  //full api 
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=';
  $ombuyURL .= $params; var_dump($ombuyURL); 
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
  $ombuyURL .= $params; var_dump($ombuyURL); 
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $array_result = json_decode($result, true); 
  //return the result as array
  return $array_result;
}



//------------------------------------------------------------------------search.php

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
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author 	WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

get_header();
//var_dump($_GET);
$data = ombuyapi_item_search($_GET);
//echo '<pre>'; var_dump($data); echo '</pre>';

if($_GET['q']){ 
 $currUrl = get_pagenum_link(); $pgUrl = explode("&", $currUrl); 
} else {
 $currUrl = get_pagenum_link(); $pgUrlCat = explode("?", $currUrl); 
}
//echo 'pgurl'; var_dump( $pgUrl ); 
//echo 'pgcat:'; var_dump( $pgUrlCat );
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
                <nav class="woocommerce-breadcrumb" style="float:left;padding: 0;width: 100%;margin: 10px auto;">
		 <a href="<?php echo home_url('/'); ?>" style="font-weight:bold;">Home</a>
		 <?php if( count($data['items']['breadcrumbs']['catpath']) > 0 ) : ?>
		  <?php $breadcrumbscategory = $data['items']['breadcrumbs']['catpath']; ?>	
		  <?php $ii = 1; foreach ($breadcrumbscategory as $value): //var_dump($value); ?>
		  <?php if($value["catid"] != 0): ?>
		  <?php if($ii == 1){ echo '/ <strong>Category:</strong> '; } else { echo '/'; } ?>
		  <?php if($_GET['q']){ ?>
		    <a href="<?php echo $pgUrl[0]; ?>&cat=<?php echo $value["catid"]; ?>"><?php echo $value['name']; ?></a>
		  <?php } else { ?>
                    <a href="<?php echo $pgUrlCat[0]; ?>?cat=<?php echo $value["catid"]; ?>"><?php echo $value['name']; ?></a>
		  <?php } ?>
		  <?php $ii++; ?>
		  <?php endif; endforeach;?>
		 <?php endif; ?>

		 <?php // only appears for query value ?>
		 <?php if($_GET['q']){ ?> 
                 <?php if($ii > 1){ ?>
		   <strong>& Search for: </strong>
		   <input type="text" id="search-key" name="search-key" style="width:7%;height:1%;padding:1px;border:0;" value="<?php echo $data['items']['keyword']; ?>"/> <button type="button" id="btnSearch" name="btnSearch" value="pOK" style="height:26px;font-size:12px;display:none;">OK</button>
		 <?php } else { ?>
		   <strong>/ Search for: </strong>
		   <input type="text" id="search-key" name="search-key" style="width:7%;height:1%;padding:1px;border:0;" value="<?php echo $data['items']['keyword']; ?>"/> <button type="button" id="btnSearch" name="btnSearch" value="pOK" style="height:26px;font-size:12px;display:none;">OK</button>		 
		 <?php } ?>
		 <?php }else{ ?>
		   <strong>/ Search for: </strong>
		   <input type="text" id="search-key" name="search-key" style="width:7%;height:1%;padding:1px;border:0;" value="<?php echo 'search...'; ?>"/> <button type="button" id="btnSearch" name="btnSearch" value="pOK" style="height:26px;font-size:12px;display:none;">OK</button>	
		 <?php } ?>
                 <?php //ppath values; ?>
		 <?php if( count($data['items']['breadcrumbs']['propSelected']) > 0 ) : ?>
		  <?php $breadcrumbsppath = $data['items']['breadcrumbs']['propSelected']; ?>
		  <ul class="bcrumbppath" style="margin-top: -25px;margin-left: 263px;margin-bottom:31px;">	
		  <?php foreach ($breadcrumbsppath as $ppathvalue): //var_dump($ppathvalue); ?>
		  <?php   echo '<li style="float: left;list-style: none; padding:1px; border:1px solid #eee; margin-right:3px;" data-ppath="'.$ppathvalue['value'].'">'.$ppathvalue['text'].":"; ?>
		  <?php   $ppathsubVal = $ppathvalue['sub']; //var_dump($ppathsubVal); ?>
		  <?php   foreach($ppathsubVal as $subVal){  
			   if(count($ppathsubVal) > 1){ echo $subVal['text'].','; } else { echo $subVal['text']; } } ?>
		  <h2 name="btnRemove" class="btnRemove" value="pRemove" style="font-size:9px;float:right;margin:6px 3px 0 8px;font-weight: bold;
cursor: pointer;">X</h2>	
                  <?php   echo '</li>'; ?> 
		  <?php endforeach;?> 
                  </ul>
		 <?php endif; ?>
		</nav>
		<div class="clear"></div> 
		<div class="shop-price" style="width:100%">
		<p class="price-class">
		  <span>Price Range: </span>
		  <input type="text" name="price1" id="price1" style="width:5%;height:27px;padding:3px;border-radius:15px;" value="<?php if($_GET['start']){ echo $_GET['start']; } else{ echo '0'; } ?>"/> - <input type="text" name="price2" id="price2" style="width:5%;height: 27px;padding: 3px;border-radius: 15px;" value="<?php if($_GET['end']){ echo $_GET['end']; } else{ echo '0'; } ?>"/>
		  <button type="button" id="btnPrice" name="btnPrice" value="pOK" style="height:1%">OK</button>
                  <span>Filter By: </span>
		  <select id="filterby" name="filterby">
		   <option value="" <?php if($_GET['filter'] == "" || ! $_GET['filter']){?> selected="selected" <?php }?>>TMALL & TAOBAO</option>
    		   <option value="filter_tianmao:tmall" <?php if($_GET['filter'] == "filter_tianmao:tmall"){ ?> selected="selected" <?php } ?>>TMALL ONLY</option>
		  </select>  
		  <span>Sort By: </span>
		  <select id="sortby" name="sortby">
    		    <option value="bid" <?php if($_GET['sort'] == "bid"){ ?> selected="selected" <?php } ?>>Price+Shipping: Lowest First</option> 
		    <option value="_bid" <?php if($_GET['sort'] == "_bid"){ ?> selected="selected" <?php } ?>>Price+Shipping: Highest First</option> 
    		    <option value="bid2" <?php if($_GET['sort'] == "bid2"){ ?> selected="selected" <?php } ?>>Price:Lowest First</option> 
    		    <option value="_bid2" <?php if($_GET['sort'] == "_bid2"){ ?> selected="selected" <?php } ?>>Price:Highest First</option> 
    		    <option value="_sale" <?php if($_GET['sort'] == "_sale" || ! $_GET['sort'] ){ ?> selected="selected" <?php } ?>>Best Selling</option> 
    		    <!--<option value="sale" <?php if($_GET['sort'] == "sale"){ ?> selected="selected" <?php } ?>>Sales(Ascending)</option>--> 
    		    <option value="_credit" <?php if($_GET['sort'] == "_credit"){ ?> selected="selected" <?php } ?>>Popularity</option> 
    		    <!--<option value="credit" <?php if($_GET['sort'] == "credit"){ ?> selected="selected" <?php } ?>>Seller Credit(Ascending)</option>--> 
		  </select> 
		
		  <ul id="grid-rows" name="grid-rows" style="float:right;margin-top:-60px;">
		   <li id="3items" style="float:left;padding:5px;list-style: none;"><img src="<?php echo get_template_directory_uri(); ?>/images/3-grid-img.png" alt="" class="grid-img"></li>
		   <li id="4items" style="float:left;padding:5px;list-style: none;"><img src="<?php echo get_template_directory_uri(); ?>/images/4-grid-img.png" alt="" class="grid-img"></li>
		  </ul>
	        </p>
		</div>
		<div class="clear"></div> 
		<!-- ---- respective filters for the results ---- -->
		<hr style="margin-top:5px; margin-bottom:5px;"/>
                <?php if(count($data['items']['navs']) > 0 ){ ?>
		<div class="shop-top-cat-ppath" style="width:100%;height:auto;overflow:hidden;transition: all 0.8s;">
		<?php //categories ?>
		<?php $tprops = 1; foreach($data['items']['navs'] as $key=>$navigation){ //var_dump($key); //var_dump($navigation); ?> 
		<?php foreach($navigation as $navi){ ?>
		<?php //ppath ?>
		<?php if( $navi["data_param_type"] == "ppath" ){ $tprops++; ?>
                <div class="select-row">
		<div class="col-md-1 col-sm-1"><h2 class="shop-title" style="font-weight:bold;"><?php echo $navi["title"]; ?></h2></div>
		<div class="col-md-11 col-sm-11 div-ppath" style="height:50px;overflow:hidden;">
		<?php if(count($navi["item"]) > 10 ){ ?>
                    <h3 class="more-btn" style="font-size: 10px;text-align: right;margin-top: -3px;margin-bottom: 0px;width:3%;float:right;">+More</h3> 
                <?php } ?>
		<ul class="navigation ppath-nav <?php if($key == "adv"){ echo 'ppath-adv'; } ?>" style="margin:-10px 0; border-top:0; padding:0;">
		<?php foreach($navi["item"] as $navValue){ ?>
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>" data-ppath="<?php echo $navValue["param_value"]; ?>">
		 <?php if($key != "adv"){ ?>
		 <input type="checkbox" id="<?php echo $navValue["param_value"]; ?>" name="<?php echo $navValue["param_value"]; ?>" value="<?php echo $navValue["param_value"]; ?>" /><?php } ?>
		 <a href="#"><?php echo $navValue["title"]; ?></a>
		 </li>
		<?php } ?></ul>
		<?php if($key != "adv"){ ?>
		<div class="buttons" style="text-align:center;">
		  <button type="button" class="btnConfirm" name="btnConfirm" value="pConfirm" style="height:26px;font-size:11px;margin-top:10px;">Confirm</button>
	          <button type="button" class="btnCancel" name="btnCancel" value="pCancel" style="height:26px;font-size:11px;margin-top:10px;">Cancel</button>
		</div>
		<?php } ?>
		</div>
                </div>
		<div class="clear" style="border-top:1px solid #eee; margin-bottom:5px;"></div>	
		<?php } ?>
		<?php //cat ?>
		<?php if( $navi["data_param_type"] == "cat" ){ $tprops++; //echo  $navi["data_param_type"]; ?>
		<div class="select-row">
                <div class="col-md-1 col-sm-1"><h2 class="shop-title" style="font-weight:bold;"><?php echo $navi["title"]; ?></h2></div>
		<div class="col-md-11 col-sm-11 div-cat" style="height:50px;overflow:hidden;">
                <?php if(count($navi["item"]) > 10 ){ ?><h3 class="more-btn" style="font-size: 10px;text-align: right;margin-top: -3px;margin-bottom: 0px;">+More</h3><?php } ?>
		<ul class="navigation cat-nav" style="margin:5px 0; border-top:0; padding:0;">
                <?php $pagiCUrl = get_pagenum_link(); $pagiCUrlArr = explode("?", $pagiCUrl); ?>
		<?php $mOptions = str_replace('&',';',$pagiCUrlArr[1]); $mOptions = str_replace('#038;','',$mOptions); ?>
		<?php $optionsArr = explode(';',$mOptions); $optUrl = ''; ?>
		<?php foreach($optionsArr as $keyA => $option){ if(strstr($option, 'cat')){ array_splice($optionsArr,$keyA,1); }} 
                      foreach($optionsArr as $keyA => $option){ if($keyA == 0){ $optUrl .= '?'. $option; } else{ $optUrl .= '&'. $option; }}?> 
		<?php $reconCatUrl = $pagiCUrlArr[0].$optUrl; //echo $reconUrl; ?>	
		<?php foreach($navi["item"] as $navValue){ ?>
		 <?php if($key == "common"){ ?>	
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>">
		  <?php if(count($optionsArr) > 0){ ?>
		  <a href="<?php echo $reconCatUrl; ?>&cat=<?php echo $navValue["param_value"]; ?>"><?php echo $navValue["title"];?></a>
		  <?php } else { ?>
		  <a href="<?php echo $reconCatUrl; ?>?cat=<?php echo $navValue["param_value"]; ?>"><?php echo $navValue["title"];?></a>
		  <?php } ?>
		 </li>
                 <?php } else { ?>
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>">
		  <a href="<?php echo $pagiCUrlArr[0]; ?>?cat=<?php echo $navValue["param_value"]; ?>"><?php echo $navValue["title"];?></a>
		 </li>
 	         <?php } ?>
		<?php } ?></ul></div>
                </div>
		<div class="clear" style="border-top:1px solid #eee; margin-bottom:20px;"></div>	
		<?php } ?>
		<?php }} ?>
		</div>
		<?php if ($tprops > 5){ //echo $tprops; ?>
                <h5 class="more-all" style="font-size:17px;text-align:center;margin-top:10px;margin-bottom:0px;color:#000;"><span class="allMoreTxt">+More</span><span class="allLessTxt">-Less</span></h5>
                <?php } ?>
		<?php } ?>
	      </div>

		<div class="product-list-grid desktop-columns-4 tablet-columns-2 mobile-columns-1 mobile-ts-columns-1">
		<ul class="products">
		<?php foreach( $data['items']['item'] as $pItem ){ //var_dump($pItem); echo '<br/>'; ?>
		<?php $itemlinks = explode('?', $pItem["detail_url"]); $attributes = explode('&', $itemlinks[1]); 
                      $ids = explode('=', $attributes[0]); $productId = $ids[1];  ?>

		<li class="product-item flash1 style1 col-md-3 col-sm-6 col-xs-12 col-ts-12 post-1378 product type-product status-publish  instock shipping-taxable product-type-simple">
		<div class="product-inner">
		<div class="product-thumb">
		  <div class="status"></div>
		  <div class="thumb-inner">
		    <?php if( $pItem["promotion_price"] <  $pItem["price"] ) { ?>
		    <img src="<?php echo get_template_directory_uri(); ?>/product/sale-clipart.png" width="30" height="30" alt="Sale" /> 
		    <?php } ?>
		    <a class="thumb-link" href="<?php echo get_home_url().'/product/'.$productId; ?>">
		      <img class="attachment-post-thumbnail wp-post-image" src="<?php echo 'http:'.$pItem["pic_url"].'_300x300.jpg'; ?>" alt="" width="350" height="350">
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
		  <?php if( $pItem["promotion_price"] <  $pItem["price"] ) { ?> <del style="font-size:23px"> <?php } ?>
		  <span class="price" style="padding:10px;">
		      <span class="woocommerce-Price-currencySymbol">¥</span><?php echo $pItem["price"]; ?>
		  </span>
		  <?php if( $pItem["promotion_price"] <  $pItem["price"] ) { ?> </del> <?php } ?>
		</h3>
		<div class="rating" title="Rated 0 out of 5">
			<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
			<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
		</div>
		<!--<a rel="nofollow" href="<?php echo get_home_url().'/product/'.$productId; ?>" data-quantity="1" data-product_id="1378" data-product_sku="" class="button product_type_simple ajax_add_to_cart">Read more</a>-->	
		</div>
		</div>	
		</li>
		<?php } ?>

		</ul>
		</div>

		<?php /* ================ pagination =============================== */ ?>
		<div style="float:left;width:100%;text-align:center;border-top:1px solid #eee;">
		<?php $pagiCUrl = get_pagenum_link(); $pagiCUrlArr = explode("?", $pagiCUrl); ?>
		<?php $mOptions = str_replace('&',';',$pagiCUrlArr[1]); $mOptions = str_replace('#038;','',$mOptions); ?>
		<?php $optionsArr = explode(';',$mOptions); $optUrl = ''; //var_dump($optionsArr); ?>
		<?php foreach($optionsArr as $key => $option){ if(strstr($option, 'page')){ array_splice($optionsArr,$key,1); }}
                      foreach($optionsArr as $key => $option){ if($key == 0){ $optUrl .= '?'. $option; } else{ $optUrl .= '&'. $option; }}?> 
		<?php $reconUrl = $pagiCUrlArr[0].$optUrl; //echo $reconUrl; ?>
	        <?php $pagination = paginate_links(array(
                    			'base' => $reconUrl .'&page='.'%_%', 
					'format' => "%#%",
                    			'current' => $data['items']['page'], 
					'total' => $data['items']['pagecount'],
					'prev_next' => false,
					'mid_size' => 4, 
					'end_size' => 4,
					'type' => 'array', )); ?>		
		<?php if($pagination){ ?>
		<ul class="pagination">
		<?php foreach($pagination as $pageLinkUrl){ $pgValue = strip_tags($pageLinkUrl); //var_dump($pgValue); ?>
		<?php  if($_GET['page']){ ?>
		  <li class="<?php if($_GET['page'] == $pgValue){ echo 'active'; } ?>"><?php echo $pageLinkUrl; ?></li>
		<?php  } else { ?>
		  <li class="<?php if($pgValue == 1){ echo 'active'; } ?>"><?php echo $pageLinkUrl; ?></li>	
		<?php  }} ?>		
		</ul>
		<?php } ?>
		</div>
		<?php /* ================ pagination =============================== */ ?>


	<?php else : ?>


		<?php do_action( 'woocommerce_no_products_found' ); ?>


	<?php endif; ?>
       </div>

   </div> <!--- end row -->	
</div>
</div>

<script type="text/javascript">
var clicked = 0;
jQuery( "#clickme" ).click(function() {
  jQuery( "#navi-ppath" ).toggle( "fast", function(){ 
    //Animation complete.
  });
});
//jQuery( ".shop-top h5" ).click(function() {
// jQuery(this).toggle("slow", function() { 
//   if( jQuery(this).text() == "+More" ){
//     jQuery(".shop-top-cat-ppath").css('height','auto');
//     jQuery(".shop-top-cat-ppath").css('overflow','visible');
//     jQuery(this).text("-Less");
//     jQuery(this).css("display","");	
//   } else {    
//     jQuery(".shop-top-cat-ppath").css('height','109px');
//     jQuery(".shop-top-cat-ppath").css('overflow','hidden');
//     jQuery(this).text("+More");
//     jQuery(this).css("display","");
//   }
// });
//});
//jQuery( ".div-ppath h3" ).click(function() {
// jQuery(this).toggle("fast", function() { 
//   if( jQuery(this).text() == "+More" ){
//     jQuery(this).parent().css('height','auto');
//     jQuery(this).parent().css('overflow', 'visible');
//     jQuery(this).text("-Less");
//     jQuery(this).css("display","");	
//   } else {    
//     jQuery(this).parent().css('height','50px');
//     jQuery(this).parent().css('overflow', 'hidden');
//     jQuery(this).text("+More");
//     jQuery(this).css("display","");
//   }
// });
//});
//jQuery( ".div-cat h3" ).click(function() {
// jQuery(this).toggle("fast", function() { 
//   if( jQuery(this).text() == "+More" ){
//     jQuery(this).parent().css('height','auto');
//     jQuery(this).parent().css('overflow', 'visible');
//     jQuery(this).text("-Less");
//     jQuery(this).css("display","");	
//   } else {    
//     jQuery(this).parent().css('height','50px');
//     jQuery(this).parent().css('overflow', 'hidden');
//     jQuery(this).text("+More");
//     jQuery(this).css("display","");
//   }
// });
//});
jQuery('#btnPrice').click(function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0]; 
  var options = splitUrl[1].split('&'); //console.log(options);
  if(jQuery('#price1').val() == ""){ jQuery('#price1').val( '0' ); }
  if(jQuery('#price2').val() == ""){ jQuery('#price2').val( '0' ); }
  if(parseInt(jQuery('#price1').val()) == 0){
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('start') > -1){ options.splice(cnt, 1); }
   }
  } 
  if(parseInt(jQuery('#price2').val()) == 0){
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('end') > -1){ options.splice(cnt, 1); }
   }
  }
  //check if the options are there in the url, else add 
  if(parseInt(jQuery('#price1').val()) > 0){ 
   var replaced = 0; 
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('start') > -1){ 
     options[cnt] = 'start='+ jQuery('#price1').val(); replaced = 1;
    }
   }
   if( replaced == 0){ options.push( 'start='+ jQuery('#price1').val() ); }
  }
  if(parseInt(jQuery('#price2').val()) > 0){
   var replaced = 0; 
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('end') > -1){ 
      options[cnt] = 'end='+ jQuery('#price2').val(); replaced = 1;
    }
   }
   if( replaced == 0){ options.push( 'end='+ jQuery('#price2').val() ); }
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);
});
jQuery('#filterby').change(function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0];
  var options = splitUrl[1].split('&'); //console.log(options);
  if(jQuery('#filterby option:selected').val() != ""){ 
   var replaced = 0; 
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('filter') > -1){ 
      options[cnt] = 'filter='+ jQuery('#filterby option:selected').val(); replaced = 1;
    }
   }
   if( replaced == 0){ options.push( 'filter='+ jQuery('#filterby option:selected').val() ); }
  }
  if(jQuery('#filterby option:selected').val() == ""){ 
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('filter') > -1){ options.splice(cnt, 1); }
   }
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);
});
jQuery('#sortby').change(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0];
  var options = splitUrl[1].split('&'); //console.log(options);
  if(jQuery('#sortby option:selected').val() != ""){
   var replaced = 0; 
   for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('sort') > -1){ 
      options[cnt] = 'filter='+ jQuery('#sortby option:selected').val(); replaced = 1;
    }
   }
   if( replaced == 0){ options.push( 'sort='+ jQuery('#sortby option:selected').val() ); }
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);
});
jQuery('.ppath-adv li a').click(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0];
  var options = splitUrl[1].split('&'); //console.log(options);
  var replaced = 0; 
  for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('ppath') > -1){ 
      options[cnt] = options[cnt] + jQuery(this).parent().attr('data-ppath') + ';'; replaced = 1;
    }
  }
  if( replaced == 0){ options.push( 'ppath='+ jQuery(this).parent().attr('data-ppath') + ';' ); }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);  
});
/*jQuery('#search-key').on('focus',function(){ 
  if( jQuery('#search-key').val() != "search..." ){ jQuery('#search-key').val(''); }
});
jQuery('#search-key').on('focusout',function(){ 
  if( jQuery('#search-key').val() != "search..." ){ jQuery('#search-key').val(''); }
});*/
jQuery('#btnSearch').on('click',function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0]; 
  var options = splitUrl[1].split('&'); //console.log(options);
  if( jQuery('#search-key').val() == "" ){ 
   for(cnt=0;cnt<options.length;cnt++){ 
     if(options[cnt].indexOf('q') > -1){ options.splice(cnt, 1); }
   }
  }
  if( (jQuery('#search-key').val() != "") && (jQuery('#search-key').val() != "search...") ){ 
   var replaced = 0; 
   for(cnt=0;cnt<options.length;cnt++){ 
     if(options[cnt].indexOf('q') > -1){ 
      options[cnt] = 'q='+ jQuery('#search-key').val(); replaced = 1;
     }
   }
   if( replaced == 0){ options.push( 'q='+ jQuery('#search-key').val() ); }
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);  
});
jQuery('#grid-rows > li').on('click', function(){
 var value = jQuery(this).attr('id'); //alert(value);
 if(value == "4items"){
  jQuery('.product-list-grid').removeClass('desktop-columns-3');
  jQuery('.product-list-grid').addClass('desktop-columns-4');
  jQuery('ul.products > li.product-item').removeClass('col-md-4');
  jQuery('ul.products > li.product-item').addClass('col-md-3');
 }
 if(value == "3items"){
  jQuery('.product-list-grid').removeClass('desktop-columns-4');
  jQuery('.product-list-grid').addClass('desktop-columns-3');
  jQuery('ul.products > li.product-item').removeClass('col-md-3');
  jQuery('ul.products > li.product-item').addClass('col-md-4');
 }    
});
jQuery('.btnConfirm').on('click',function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0]; var options = splitUrl[1].split('&'); //console.log(options);
  var replaced = 0; 
  for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('ppath') > -1){ 
      //construct ppath
      var ppathVal = '';
      var phtml = jQuery(this).parent().parent(); //alert(phtml);
      jQuery(phtml).find('.ppath-nav li input[type="checkbox"]:checked').each(function(){ 
         ppathVal += jQuery(this).val() + ';';
      }); //alert(ppathVal);
      options[cnt] = options[cnt] + ppathVal; replaced = 1;
    }
  }
  if( replaced == 0){ 
    var ppathVal = '';
    var phtml = jQuery(this).parent().parent();
    //var nChecked = jQuery(phtml).find('.ppath-nav li input[type="checkbox"]:checked').length;
    jQuery(phtml).find('.ppath-nav li input[type="checkbox"]:checked').each(function(){ 
         ppathVal += jQuery(this).val() + ';' ;
    }); //alert(ppathVal);
    options.push( 'ppath='+ ppathVal );
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl);
});
jQuery('.btnCancel').on('click',function(){ 
  var phtml = jQuery(this).parent().parent();
  jQuery(phtml).find('.ppath-nav li input[type="checkbox"]:checked').each(function(){ 
    jQuery(this).attr('checked', false);
  });
});
jQuery('#search-key').on('click',function(){ 
 //if(clicked == 0){ 
  jQuery('#search-key').css('border', '1px solid #000'); 
  jQuery('#btnSearch').css('display', ''); clicked = 1;
 //}
 //if(clicked == 1){ 
 // jQuery('#search-key').css('border', '0'); 
 // jQuery('#btnSearch').css('display', 'none'); clicked = 0;
 //}
});
jQuery('.bcrumbppath li .btnRemove').click(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0];
  var options = splitUrl[1].split('&'); //console.log(options);
  for(cnt=0;cnt<options.length;cnt++){ 
    if(options[cnt].indexOf('ppath') > -1){ 
     if( options[cnt].indexOf(';') > -1){
       options[cnt] = options[cnt].replace(jQuery(this).parent().attr('data-ppath')+';', ''); 
     } else {
       options[cnt] = options[cnt].replace(jQuery(this).parent().attr('data-ppath'), ''); 
     }
     if(options[cnt] === "ppath="){ options.splice(cnt,1); }	
    }
  }
  //reconstruct options 
  for(cnt=0;cnt<options.length;cnt++){
    if(cnt == 0){ cUrl += '?'+ options[cnt]; }
    else{ cUrl += '&'+ options[cnt]; }
  }
  //alert(cUrl);
  window.location.assign(cUrl); 
});










</script>
<script>
    jQuery(document).ready(function(){
        
    //jQuery(".more-btn").click(function(){
        //        jQuery(this).find('ul.ppath-nav').toggleClass("increaseHeight", 1000, "easeOutSine");
     //jQuery("ul.navigation").toggleClass("increaseHeight", 1000, "easeOutSine");
     //jQuery(".div-ppath").toggleClass("increaseHeight", 1000, "easeOutSine");
     //jQuery(".div-cat").toggleClass("increaseHeight", 1000, "easeOutSine");


        //});
        
        jQuery(".more-all").click(function(){
        jQuery(".shop-top-cat-ppath").toggleClass("increaseMainHeight");
        });
        
        $('.allLessTxt').hide();        
        
         
         $('.allMoreTxt').click(function() {
               $('.allLessTxt').show();
               $('.allMoreTxt').hide(); 
         });
         $('.allLessTxt').click(function() {
               $('.allLessTxt').hide();
               $('.allMoreTxt').show(); 
         });
         
         $(".div-ppath").click(function() {
             //$('.div-ppath').removeClass('increaseHeight');
        var $this = $(this);
        var $path = $(this).children( ".more-btn" )
        

        $this.toggleClass("increaseHeight");
        $path.toggleClass("increaseHeight");

        if ($path.hasClass("increaseHeight")) {
            $path.html("-Less");
        } else {
            $path.html("+More");
        }
        
    });
    
    $(".div-cat").click(function() {
             //$('.div-ppath').removeClass('increaseHeight');
        var $this = $(this);
        var $path = $(this).children( ".more-btn" )
        

        $this.toggleClass("increaseHeight");
        $path.toggleClass("increaseHeight");

        if ($path.hasClass("increaseHeight")) {
            $path.html("-Less");
        } else {
            $path.html("+More");
        }
        
    });
        
    });
    
</script>

<?php 
//get_footer( 'shop' ); 
get_footer(); 
?>

//------------------------------------------------------------------------
<?php
/**
 * The Template for displaying all single products
 *
 *
 * ["product"]=> "559275507450" 
 * ["post_type"]=> "product" 
 * ["name"]=> "559275507450"
 */
global $wp_query; 
$query_vars = $wp_query->query_vars; 
//var_dump($query_vars); 
$pdata = ombuyapi_item_details($query_vars['product']); 
//var_dump($pdata); 

//echo $pdata['item']['rootCatId']; echo '<br/>';
//echo $pdata['item']['cid']; echo '<br/>';

//$cdata=ombuyapi_category_get($pdata['item']['rootCatId']);
//var_dump($cdata); 

//$cadata=ombuyapi_category_get($pdata['item']['cid']);
//var_dump($cadata);


get_header( ); 

?>

<link rel="stylesheet" href="http://www.elevateweb.co.uk/wp-content/themes/radial/jquery.fancybox.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_url')?>/css/owl.carousel.min.css">

<!-- Slider Zooming related script Start-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->

<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="http://www.elevateweb.co.uk/wp-content/themes/radial/jquery.elevatezoom.min.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/owl.carousel.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

<div class="main-container shop-page left-slidebar">
<div class="container">
<div id="container">
<div id="content" role="main">
<nav class="woocommerce-breadcrumb">
<a href="<?php echo home_url('/'); ?>"><strong> Home </strong></a><?php echo '/ <strong> Category: </strong>'; ?> 
<?php if ($pdata['item']['rootCatId'] != '') { ?><a href="<?php echo home_url('/'); ?>product/search/?cat=<?php echo $pdata['item']['rootCatId']; ?>"><?php echo $pdata['item']['rootCatId']; ?></a><?php } ?>
<?php if ($pdata['item']['cid'] != '') { ?><?php echo '/'; ?><a href="<?php echo home_url('/'); ?>product/search/?cat=<?php echo $pdata['item']['cid']; ?>"><?php echo $pdata['item']['cid']; ?></a><?php } ?>
<?php //echo '/'; ?><?php //echo $pdata['item']['title']; ?>
</nav>	
	
<div class="row">
<div class="main-content col-md-12 col-sm-11"> 
<div itemscope="" itemtype="http://schema.org/Product" id="product-51575" class="single-product post-51575 product type-product status-publish has-post-thumbnail first instock sale sold-individually shipping-taxable purchasable product-type-simple">
<div class="row" id="single-product">

<!-- ---------------- -->
<!----- left column --->
<!-- ---------------- -->
<div class="col-sm-12 col-md-4 col-xs-12">
<div class="product-detail-image style2">
<?php if ( $pdata['item']['orginal_price'] > $pdata['item']['price'] ){ ?>
 <span class="onsale">Sale!</span>
<?php } ?>

<!-- Zooming Work Start From Here -->   
<div class="zoom-wrapper">
<div class="zoom-left">
<img style="border:1px solid #e8e8e6;" id="zoom_03" width="446" height="595" src="<?php echo $pdata['item']['pic_url'].'_350x350.jpg'; ?>" 
data-zoom-image="<?php echo $pdata['item']['pic_url'].'_800x800.jpg' ?>" />

<div id="gallery_01" style="width:500px; float:left; margin-top: 70px;">
<?php foreach( $pdata['item']["item_imgs"] as $itemImg ) { ?> 
<a href="#" class="elevatezoom-gallery" data-image="<?php echo $itemImg["url"].'_800x800.jpg'; ?>" data-zoom-image="<?php echo $itemImg["url"].'_800x800.jpg'; ?>"><img id="thumb" src="<?php echo $itemImg["url"]. '_80x80.jpg'; ?>" /></a>
<?php } ?> 
</div></div>
<div style="clear:both;"></div>
</div>
<!-- Zooming Work End From Here -->
<script type="text/javascript">
jQuery(document).ready(function () {
  $("#zoom_03").elevateZoom({ 
	cursor: 'crosshair', 
	zoomType: "inner",
	imageCrossfade: true, 
	loadingIcon: "http://www.elevateweb.co.uk/spinner.gif" 
  });
});
jQuery('#gallery_01 a').click(function(){ 
 var dImg = jQuery(this).attr('data-image');
 var dZImg = jQuery(this).attr('data-zoom-image'); 
 jQuery('#zoom_03').attr('src', dImg);
 jQuery('#zoom_03').data('zoom-image', dZImg).elevateZoom({ 
        cursor: 'crosshair', 
	zoomType: "inner", 
	imageCrossfade: true, 
        loadingIcon: "http://www.elevateweb.co.uk/spinner.gif"
 });
});
</script>
</div>
</div>

<!-- ---------------- -->
<!----- right column --->
<!-- ---------------- -->
<div class="col-sm-12 col-md-8 col-xs-12">
<div class="summary entry-summary product-details-right style2">
 <h1 class="product_title entry-title"><?php echo $pdata['item']['title']; ?></h1>
 <!--<ul class="price-cont">
   <li><p class="price"><ins>
   <span class="woocommerce-Price-amount amount">
    <span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;<?php echo $pdata['item']['price']; ?>
   </span></ins>
   </p>out of 5 based on</li>
     <li>customer rating</li>
 </ul>-->
 <!----- Dummy customer review --->
 <!--<p class="cust-revw">
   <span class="amount">3.5</span> out of "5" based on <span class="amount">1</span> customer rating 
 </p>
 <p class="cust-revw">( 1 customer review )</p>-->

 <div class="clear"></div>
 <p class="shipping" style="margin-top:10px;">
   <span class="orig-price">
     <span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;<?php echo round($pdata['item']['price'],2); ?>
   </span>
   <ins>
   <?php if ( $pdata['item']['orginal_price'] > $pdata['item']['price'] ){ ?>
    <span class="dis-price">
      <span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;<?php echo round($pdata['item']['orginal_price'],2); ?> 
    </span>
   <?php } ?>
   <span class="woocommerce-Shipping-amount amount">Shipping : 
   <span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;
    <?php if( $pdata['item']['post_fee'] == 0 && $pdata['item']['express_fee'] == 0 && $pdata['item']['ems_fee'] == 0 ){ ?> Free 
    <?php } else { ?>
    <?php $prices = array( $pdata['item']['post_fee'], $pdata['item']['express_fee'], $pdata['item']['ems_fee'] ); ?>
    <?php asort($prices); foreach($prices as $val){ if($val != 0){ echo $val; break; } } } ?>
   </span>
   </ins>
 </p>

 <div class="prod-props">
 <?php $cnt = 1; $propIndx = 0; 
       foreach($pdata['item']['props_list'] as $key => $value){ $keyArr = explode(':', $key); ?>
 <?php //if propIndx is not same ?>
 <?php if($propIndx != $keyArr[0]){ $cnt = 1; ?>
 <?php if($propIndx != 0){ ?></ul><div style="clear:both;margin-bottom:10px;"></div><?php } ?>
 <?php if($cnt == 1){ $propIndx = $keyArr[0]; $valArr = explode(':',$value); ?>
  <p style="margin-bottom:3px;font-weight:800;"><?php echo $valArr[0]; ?></p><ul>
    <?php if( array_key_exists($key,$pdata['item']["props_img"])) { ?>
     <?php if( $pdata['item']["props_img"]["$key"] ){ //checks if image is not there ?>
     <li data-prop="<?php echo $key;?>" style="border:1px solid #000;float:left;margin:3px;padding:5px;list-style:none;width:7%;height:50px;text-align:center;"><a  href="#" class="elevatezoom-gallery" data-image="<?php echo $pdata['item']["props_img"]["$key"].'_800x800.jpg'; ?>" data-zoom-image="<?php echo $pdata['item']["props_img"]["$key"].'_800x800.jpg'; ?>"><img src="<?php echo $pdata['item']["props_img"]["$key"] . '_50x50.jpg'; ?>" alt="<?php echo $valArr[1]; ?>" title="<?php echo $valArr[1]; ?>" /></a></li>
     <?php } else { ?>
     <li data-prop="<?php echo $key;?>" style="border:1px solid #000;float:left;margin:3px;padding:1px;list-style:none;width:7%;height:50px;text-align:center;font-size:10px;color:#f00;"><?php echo $valArr[1]; ?></li>
     <?php } ?>
    <?php } else { ?>
     <li style="border:1px solid #000;float:left;margin:3px;padding:10px 0;list-style:none;width:7%;height:50px;text-align:center;font-size:11px;" data-prop="<?php echo $key; ?>"><?php echo $valArr[1]; ?></li>
    <?php }} ?>
 <?php } else { // if propIndex is the same ?>
  <?php $valArr = explode(':',$value); ?>
    <?php if( array_key_exists($key,$pdata['item']["props_img"])) { ?>
     <?php if( $pdata['item']["props_img"]["$key"] ){ //checks if image is not there ?>
     <li data-prop="<?php echo $key;?>" style="border:1px solid #000;float:left;margin:3px;padding:5px;list-style:none;width:7%;height:50px;text-align:center;"><a  href="#" class="elevatezoom-gallery" data-update="" data-image="<?php echo $pdata['item']["props_img"]["$key"].'_800x800.jpg'; ?>" data-zoom-image="<?php echo $pdata['item']["props_img"]["$key"].'_800x800.jpg'; ?>"><img src="<?php echo $pdata['item']["props_img"]["$key"] . '_50x50.jpg'; ?>" alt="<?php echo $valArr[1]; ?>" title="<?php echo $valArr[1]; ?>" /></a></li>
     <?php } else { ?>
     <li data-prop="<?php echo $key;?>" style="border:1px solid #000;float:left;margin:3px;padding:1px;list-style:none;width:7%;height:50px;text-align:center;font-size:10px;color:#f00;"><?php echo $valArr[1]; ?></li>
     <?php } ?>
    <?php } else { ?>
     <li style="border:1px solid #000;float:left;margin:3px;padding: 5px;list-style: none;width: auto;height:50px;text-align:center;font-size:11px;" data-prop="<?php echo $key; ?>"><?php echo $valArr[1]; ?></li>
    <?php } ?>
 <?php } $cnt++; } ?> 
 <?php if($propIndx != 0){ ?></ul><div style="clear:both;margin-bottom:10px;"></div><?php } ?>    
 </div>
 <!--<hr style="padding:10px"/>-->
<script type="text/javascript">
jQuery('.prod-props li a').click(function(){ 
 var dImg = jQuery(this).attr('data-image');
 var dZImg = jQuery(this).attr('data-zoom-image'); 
 jQuery('#zoom_03').attr('src', dImg);
 jQuery('#zoom_03').data('zoom-image', dZImg).elevateZoom({ 
        cursor: 'crosshair', 
	zoomType: "inner", 
	imageCrossfade: true, 
        loadingIcon: "http://www.elevateweb.co.uk/spinner.gif"
 });
});
</script>

<div class="data-hidden" style="display: none;">
<ul>
<?php foreach($pdata["item"]["skus"]["sku"] as $skdata){ ?>
<li data-price="<?php echo $skdata['price'];?>" data-original-price="<?php echo $skdata['orginal_price'];?>" 
   data-properties="<?php echo $skdata['properties'];?>" data-quantity="<?php echo $skdata["quantity"];  ?>">
  <?php echo $skdata["sku_id"]; ?>
</li>
<?php }?>
</ul>
</div>
<div class="prop-hidden" style="display:none" sku-properties="" sku-id=""></div>

</div>
<div class="clear"></div>
    
<form class="cart" method="post" enctype="multipart/form-data">
    <div class="quanty-box">
        <div class="quantity">
        
        <input class="minus quantity-minus" type="button" value="-">
         
          <input type="number" data-step="1" data-min="1" data-max="1" name="quantity" value="1" title="Qty" class="input-text qty text" size="4">
        <input class="plus quantity-plus" type="button" value="+">
        
        </div>
        <span class="qty-availabel">
	<p> (<?php echo $pdata['item']['num'];?> Available) </p>
        </span>
    </div>
      
  <!--<button type="submit" name="add-to-cart" value="51575" class="single_add_to_cart_button button alt">Add to cart</button>-->
  <button type="button" name="add-to-cart" value="51575" class="single_add_to_cart_button button alt">Add to cart</button>
</form>

<div class="yith-wcwl-add-to-wishlist add-to-wishlist-51575">
<div class="yith-wcwl-add-button show" style="display:block"> 
<!--<a href="/P1013_API_integration/product/51575/?add_to_wishlist=51575" rel="nofollow" data-product-id="51575" data-product-type="simple" class="add_to_wishlist">Add to Wishlist</a>-->
<a href="#" rel="nofollow" data-product-id="51575" data-product-type="simple" class="add_to_wishlist"><i class="fa fa-heart" aria-hidden="true"></i></a>
<a href="#" rel="nofollow" data-product-id="51575" data-product-type="simple" class="add_to_wishlist"><i class="fa fa-exchange" aria-hidden="true"></i></a>
<img src="http://lab-1.sketchdemos.com/P1013_API_integration/wp-content/plugins/yith-woocommerce-wishlist/assets/images/wpspin_light.gif" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden">
</div>

<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><span class="feedback">Product added!</span>
<a href="http://lab-1.sketchdemos.com/P1013_API_integration/product/51575/" rel="nofollow"> Browse Wishlist</a></div>

<div class="yith-wcwl-wishlistexistsbrowse hide" style="display:none"><span class="feedback">The product is already in the wishlist!</span>
<a href="http://lab-1.sketchdemos.com/P1013_API_integration/product/51575/" rel="nofollow"> Browse Wishlist </a></div>

<div style="clear:both"></div>
<div class="yith-wcwl-wishlistaddresponse"></div>
	
</div>

<div class="clear"></div>

<div class="share">
  <a class="print" data-element="single-product" href="javascript:print();"><i class="fa fa-print"></i> Print</a>
  <a data-productid="51575" class="send-to-friend" href="#"><i class="fa fa-envelope-o"></i> Send to a friend</a>
</div>
        
</div><!-- .summary -->
</div>
</div>

	
<div class="woocommerce-tabs wc-tabs-wrapper tab-details-product">

<!-- Tab Implement -->    

<div class="">
 
    <div class="pro-desc-tab">   
        <ul class="nav nav-pills">
          <li class="active"><a data-toggle="pill" href="#desc">Description</a></li>
          <li><a data-toggle="pill" href="#addi">Additional information</a></li>    
        </ul>
    
         <div class="tab-content" id="description">
    <div id="desc" class="tab-pane fade in active">
        <div class = "col-md-4 col-xs-12 text-right desc-left">
      <p><a href="<?php echo get_home_url().'/shop/search/?seller_nick='.$pdata['item']["seller_info"]["nick"]; ?>">VISIT STORE</a></p>
      <?php if( ($pdata['item']['seller_info']['item_score']!='') || ($pdata['item']['seller_info']['score']!='') || ($pdata['item']['seller_info']['delivery_score']!='') ){ ?>
      <p class="heading">ratings on taobao/tmall</p>
      <?php } ?>
      <!--<p><img height="100" width="200" src="<?php bloginfo('template_url')?>/images/api-imgpsh_fullsize.1.png"/> </p> -->

     
     
      <div class="scores" name="scores" style="float: right;">
           <?php if($pdata['item']['seller_info']['item_score']!=''){ ?>
	<div class="rating"> <p>PRODUCT</p> <div id="rateYo1"></div> </div>
        <?php } ?>
        
         <?php if($pdata['item']['seller_info']['score']!=''){ ?>
        <div class="rating"> <p>SELLER</p> <div id="rateYo2"></div> </div> <?php } ?>
        
        <?php if($pdata['item']['seller_info']['delivery_score']!=''){ ?>
        <div class="rating"> <p>SHIPPING</p> <div id="rateYo3"></div></div> <?php }?>
      </div>
     
      <?php// var_dump($pdata);?>	
      <?php //echo $pdata['item']['seller_info']['score'];?>

      <?php if ( $pdata['item']["sales"] ): ?> 
       <div class="qsold" style="width:100%;float:right; margin-top: 15px;">	
        <p class="heading">QUANTITY SOLD ON TAOBAO/TMALL</p>
        <p class="total-sold"><?php echo $pdata['item']["sales"]; ?></p>
       </div>
      <?php endif; ?>
    </div>
    <div class = "col-md-8 col-xs-12 text-left desc-rht">
        <p class="heading">PRODUCT INFORMATION</p>
      
      <?php $addvalue=$pdata['item']["props"]; //var_dump($addvalue); ?>
      <?php for($acnt=0; $acnt<count($addvalue); $acnt++){ ?>	
      <p><?php echo $addvalue[$acnt]['name']; ?>: <?php echo $addvalue[$acnt]['value']; ?></p>
      <?php } ?>
    </div>
    </div>
        
    <div id="addi" class="tab-pane fade">
      <?php //echo $pdata['item']["desc"]; ?>
    </div>      
  </div>
  
    </div>
  
</div>
<!-- End Tab Implement -->    

<!-- Related Product Slider -->
<?php
$relat=ombuyapi_item_similar($pdata['item']['num_iid']); 
echo '<br/>'.'NoPageCnt: '.count( $relat['items']['item'] ).'<br/>';
$relat1=ombuyapi_item_similar_bypage($pdata['item']['num_iid'], 1); 
echo '<br/>'.'ByPageCnt: '.count( $relat1['items']['item'] ).'<br/>'; 
//var_dump($relat);
?>
<?php if ( count( $relat['items']['item'] ) > 0 ) { ?>
 <div class="container mrgn-top">
        <div class="service-section text-center">
            <div class="row">
                <div class="col-sm-12 col-xs-12 section-title">                    
                    <h3>Related Products</h3>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="service-slider">
                        <div class="owl-carousel owl-theme owlTwo">
                            <?php foreach($relat['items']['item'] as $value_re) { ?>
                            <div class="item">
                                <div class="service-box">
                                <div class="img-box">                                    
                                 <a href="<?php echo home_url('/').'product/'.$value_re['num_iid']; ?>"> <img src="<?php echo $value_re['pic_url'].'_270x270.jpg'; ?>"/></a>
                                </div>
                                <p><?php echo $value_re['title']; ?></p>
                                <p class="price">  
				<span class="orig-price">
     				<span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;<?php echo round($value_re['promotion_price'],2); ?>
                                <?php //echo $value_re['price']. ',' .$value_re['promotion_price'] ; ?>
   				</span>
   				<?php if ( $value_re['price'] > $value_re['promotion_price'] ){ ?>
    				<span class="dis-price" style="text-decoration:line-through;color:#999;">
      				<span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;<?php echo round($value_re['price'],2); ?> 
    				</span>
   				<?php } ?>
				</p>
                                </div>
                            </div>
                            <?php }?>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- End Related Product slider -->
</div>

<meta itemprop="url" content="http://lab-1.sketchdemos.com/P1013_API_integration/product/51575/">

</div><!-- #product-51575 -->
</div>

</div>
</div>
</div>
</div>
</div>

<script>           
$('.owlTwo').owlCarousel({
    loop:true,
    lazyLoad : true,
    margin:20,
    nav:true,
    lazyLoad: true,
    slideBy:4,
    navText : ['<i class="fa fa-long-arrow-left" aria-hidden="true"></i>','<i class="fa fa-long-arrow-right" aria-hidden="true"></i>'],    
    responsive:{
        0:{
            items:1
        },
        600:{
            items:4
        },
        1000:{
            items:4
        }
    }   
});
</script>

<script type="text/javascript">
jQuery(document).ready(function($){
   $('.quantity').on('click', '.plus', function(e) {
       $input = $(this).prev('input.qty');
       var val = parseInt($input.val());
       $input.val( val+1 ).change();
   });
 
   $('.quantity').on('click', '.minus', 
       function(e) {
        $input = $(this).next('input.qty');
        var val = parseInt($input.val());
        if (val > 0) {
          $input.val( val-1 ).change();
        } 
   });

   $('a[data-toggle=pill]').click(function(){ //alert('this.id');
      var target = $(this).attr('href'); //alert(target); 
      if( target == "#addi" ){ 
        var desc = '<?php $desc = $pdata["item"]["desc"]; 
        $description = trim(preg_replace('/\n/', '', $desc)); echo $description; ?>';
        $('#addi').html( desc );
      } 
   });

});
</script>

<script>
jQuery(function () { 
  jQuery("#rateYo1").rateYo({
    rating: <?php if($pdata['item']['seller_info']['item_score']){echo $pdata['item']['seller_info']['score'];} else{ echo '0';} ?>,
    readOnly: true
  });
  jQuery("#rateYo2").rateYo({
    rating: <?php if($pdata['item']['seller_info']['score']){ echo $pdata['item']['seller_info']["score"]; } else { echo '0'; } ?>,
    readOnly: true
  });
  jQuery("#rateYo3").rateYo({
    rating: <?php if($pdata['item']['seller_info']['delivery_score']){ echo $pdata['item']['seller_info']["delivery_score"]; } else { echo '0'; } ?>,
    readOnly: true
  });
}); 
</script>

<script>
jQuery('.prod-props ul li').click(function(){ 
  var nskuProp = ''; var nskuID = '';
  var mainphtml = jQuery(this).parent().parent();
  var liphtml = jQuery(this).parent();
  jQuery(liphtml).find('li').each(function(){ jQuery(this).css('border','1px solid #000'); });
  jQuery(this).css('border','2px solid #FF9633'); 

  //get the values of the respective properties
  jQuery(mainphtml).find('ul').each(function(){ 
    var ulphtml = jQuery(this); 
    jQuery(ulphtml).find('li').each(function(){ 
     if( jQuery(this).css('border') == '2px solid rgb(255, 150, 51)' ){
      nskuProp += jQuery(this).attr('data-prop') + ';'; 
     }
    });
  }); //alert(nskuProp);
  var skuPropArr = nskuProp.split(';'); var nskuPropN = '';
  for(cnt=0;cnt<skuPropArr.length;cnt++){ 
   if(skuPropArr[cnt] != ''){ 
    if((cnt+1) == skuPropArr.length){ nskuPropN += skuPropArr[cnt]; } 
    else{ nskuPropN += skuPropArr[cnt] + ';'; }
   }
  }
  jQuery('.prop-hidden').attr('sku-properties', nskuPropN);
  //check if the property is present in the list
  var found = 0; 
  jQuery('.data-hidden ul li').each(function(){ 
    if( nskuPropN.indexOf(jQuery(this).attr('data-properties')) > -1 ){ nskuID = jQuery(this).text(); //alert(nskuID);
     jQuery('.shipping .orig-price').html('<span class="woocommerce-Price-currencySymbol">¥</span>&nbsp;&nbsp;'+ parseFloat(jQuery(this).attr('data-price')).toFixed(2));
     jQuery('.quanty-box .qty-availabel p').text( '( ' + jQuery(this).attr('data-quantity') + ' Available )' );
     if(jQuery(this).attr('data-quantity') == 0){ 
        jQuery('.single_add_to_cart_button').attr('disabled', true); 
        jQuery('.single_add_to_cart_button').attr('background-color', '#eee'); 
     } 
     jQuery('.prop-hidden').attr('sku-id', nskuID); found = 1;
    }
  });
  //change the url for the product
  if( found == 0 ){ //the url does not have any params 
      var cUrl = '?sku-properties=' + jQuery('.prop-hidden').attr('sku-properties'); //alert(cUrl);
      history.pushState(null,null,cUrl);
  } else { //the url contains params
      var cUrl = '?sku-properties=' + jQuery('.prop-hidden').attr('sku-properties') 
		 + '&skuId=' + jQuery.trim( jQuery('.prop-hidden').attr('sku-id') ); //alert(cUrl);
      history.pushState(null,null,cUrl);
  }
});
</script>
     
<style>
.minus { border:none; color:#fff; background-color:purple; height:30px; width:30px; }
.plus { border:none; color:#fff; background-color:purple; height:30px; width:30px; }
.qty { border:1px solid purple; color:purple; height:30px; }
</style>

<?php get_footer( ); ?>

