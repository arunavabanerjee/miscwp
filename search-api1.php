
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
//var_dump($_GET);
$data = ombuyapi_item_search($_GET);

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
		  <?php $breadcrumbs = $data['items']['breadcrumbs']['catpath']; ?>	
		  <?php $ii = 1; foreach ($breadcrumbs as $value): ?>
		  <?php if($value["catid"] != 0): ?>
		  <?php if($ii == 1){ echo '/ <strong>Category:</strong> '; } else { echo '/'; } ?>
		  <?php if($_GET['q']){ ?>
		    <?php $pagiCUrl = get_pagenum_link(); $pgCUrl = explode("&", $pagiCUrl); ?> 	
		    <a href="<?php echo $pgCUrl[0]; ?>&cat=<?php echo $value["catid"]; ?>" ><?php echo $value['name']; ?></a>
		  <?php } else { ?>
		    <?php $pagiCUrl = get_pagenum_link(); $pgCUrl = explode("?", $pagiCUrl); ?> 
                    <a href="<?php echo $pgCUrl[0]; ?>?cat=<?php echo $value["catid"]; ?>" ><?php echo $value['name']; ?></a>
		  <?php } ?>
		  <?php $ii++; ?>
		  <?php endif; endforeach;?>
		 <?php endif; ?>
		 <?php if($_GET['q']){ ?> 
                 <?php if($ii > 1){ ?>
		   <strong>& Search for </strong>
		   <input type="text" id="search-key" name="search-key" style="width:7%;height:1%;padding:3px;border:0;" value="<?php echo $data['items']['keyword']; ?>"/> <button type="button" id="btnSearch" name="btnSearch" value="pOK" style="height:26px;font-size:12px;display:none;">OK</button>
		 <?php } else { ?>
		   <strong>/ Search for </strong>
		   <input type="text" id="search-key" name="search-key" style="width:7%;height:1%;padding:3px;border:0;" value="<?php echo $data['items']['keyword']; ?>"/> <button type="button" id="btnSearch" name="btnSearch" value="pOK" style="height:26px;font-size:12px;display:none;">OK</button>		 
		 <?php } ?>
		 <?php } ?>
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
    		    <option value="sale" <?php if($_GET['sort'] == "sale"){ ?> selected="selected" <?php } ?>>Sales(Ascending)</option> 
    		    <option value="_credit" <?php if($_GET['sort'] == "_credit"){ ?> selected="selected" <?php } ?>>Popularity</option> 
    		    <option value="credit" <?php if($_GET['sort'] == "credit"){ ?> selected="selected" <?php } ?>>Seller Credit(Ascending)</option> 
		  </select> 
		
		  <ul id="grid-rows" name="grid-rows" style="float:right;margin-top:-60px;">
		   <li id="3items" style="float:left;padding:5px;list-style: none;">3InGrid</li>
		   <li id="4items" style="float:left;padding:5px;list-style: none;">4InGrid</li>
		  </ul>
	        </p>
		</div>
		<div class="clear"></div> 
		<!-- ---- respective filters for the results ---- -->
		<hr/>
                <?php if(count($data['items']['navs']) > 0 ){ ?>
		<?php $currUrl = get_pagenum_link(); $pgUrl = explode("&", $currUrl); ?>
		<?php $currUrl = get_pagenum_link(); $pgUrlCat = explode("?", $currUrl); ?>
		<div class="shop-top-cat-ppath" style="width:100%">
		<?php //categories ?>
		<?php foreach($data['items']['navs'] as $key=>$navigation){ //var_dump($key); //var_dump($navigation); ?> 
		<?php foreach($navigation as $navi){ ?>
		<?php //ppath ?>
		<?php if( $navi["data_param_type"] == "ppath" ){ ?>
		<div class="col-md-1 col-sm-1"><h2 class="shop-title" style="font-weight:bold;"><?php echo $navi["title"]; ?></h2></div>
		<div class="col-md-11 col-sm-11">
		<ul class="navigation ppath-nav <?php if($key == "adv"){ echo 'ppath-adv'; } ?>" style="margin:5px 0; border-top:0; padding:0;">
		<?php foreach($navi["item"] as $navValue){ ?>
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>" data-ppath="<?php echo $navValue["param_value"]; ?>">
		 <?php if($key != "adv"){ ?>
		 <input type="checkbox" id="<?php echo $navValue["param_value"]; ?>" name="<?php echo $navValue["param_value"]; ?>" value="<?php echo $navValue["param_value"]; ?>" /><?php } ?>
		 <a href="#"><?php echo $navValue["title"]; ?></a>
		 </li>
		<?php } ?></ul>
		<?php if($key != "adv"){ ?>
		<button type="button" id="btnConfirm" name="btnConfirm" value="pConfirm" style="height:26px;font-size:12px;">Confirm</button>
	        <button type="button" id="btnCancel" name="btnCancel" value="pCancel" style="height:26px;font-size:12px;">Cancel</button>
		<?php } ?>
		</div>
		<div class="clear" style="border-top:1px solid #eee; margin-bottom:20px;"></div>	
		<?php } ?>
		<?php //cat ?>
		<?php if( $navi["data_param_type"] == "cat" ){  //echo  $navi["data_param_type"]; ?>
		<div class="col-md-1 col-sm-1"><h2 class="shop-title" style="font-weight:bold;"><?php echo $navi["title"]; ?></h2></div>
		<div class="col-md-11 col-sm-11">
		<ul class="navigation cat-nav" style="margin:5px 0; border-top:0; padding:0;">
		<?php foreach($navi["item"] as $navValue){ ?>
		 <li style="float:left;" value="<?php echo $navValue["param_value"]; ?>">
		 <?php if($_GET['q']){ ?>
		  <a href="<?php echo $pgUrl[0]; ?>?cat=<?php echo $navValue["param_value"]; ?>"><?php echo $navValue["title"];?></a>
                 <?php } else { ?>
		  <a href="<?php echo $pgUrlCat[0]; ?>?cat=<?php echo $navValue["param_value"]; ?>"><?php echo $navValue["title"];?></a>
		 <?php } ?>
		 </li>
		<?php } ?></ul></div>
		<div class="clear" style="border-top:1px solid #eee; margin-bottom:20px;"></div>	
		<?php } ?>
		<?php }} ?>
		</div>
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
		<!--<a rel="nofollow" href="<?php echo get_home_url().'/product/'.$productId; ?>" data-quantity="1" data-product_id="1378" data-product_sku="" class="button product_type_simple ajax_add_to_cart">Read more</a>-->	
		</div>
		</div>	
		</li>
		<?php } ?>

		</ul>
		</div>

		<?php /* ================ pagination =============================== */ ?>
		<div style="float:left;width:100%;text-align:center;">
		<?php $pagiCUrl = get_pagenum_link(); $pgCUrl = explode("&", $pagiCUrl); ?>
		<ul class="pagination">
		<?php if($_GET['page']){ ?>		
	        <?php echo paginate_links(array(
                    	'base' => $pgCUrl[0] . '%_%', 
			'format' => '&page=%#%',
                    	'current' => $data['items']['page'], 
			'total' => $data['items']['real_total_results'],
                    	'prev_text'    => __('« prev'), 
			'next_text'    => __('next »'), )); ?>
		<?php } else { ?>
	        <?php echo paginate_links(array(
                    	'base' => $pgCUrl[0] . '%_%', 
			'format' => '&page=%#%',
                    	'current' => $data['items']['page'], 
			'total' => $data['items']['real_total_results'],
                    	'prev_text'    => __('« prev'), 
			'next_text'    => __('next »'), )); ?>
		<?php } ?>
		</ul>
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
  jQuery( "#navi-ppath" ).toggle( "fast", function() { 
    //Animation complete.
  });
});
jQuery('#btnPrice').click(function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0]; 
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  window.location.assign(cUrl);
});
jQuery('#filterby').change(function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0];
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  window.location.assign(cUrl);
});
jQuery('#sortby').change(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0];
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  window.location.assign(cUrl);
});
/*jQuery('.ppath-nav li input[type="checkbox"]').change(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0];
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  cUrl += '&ppath=' + jQuery('.ppath-nav li input[type="checkbox"]:checked').val();
  window.location.assign(cUrl);  
});*/
jQuery('.ppath-adv li a').click(function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0];
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  cUrl += '&ppath=' + jQuery(this).parent().attr('data-ppath');
  window.location.assign(cUrl);  
});
/*jQuery('#search-key').on('input',function(){*/
jQuery('#btnSearch').on('click',function(){
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('?'); 
  var cUrl = splitUrl[0]; //alert( jQuery('#search-key').val() ); alert ( splitUrl[1] );
  if( jQuery('#search-key').val() != "" ){ cUrl += '?q='+ jQuery('#search-key').val(); }
  else{ var urlOffQ = splitUrl[1].split('&'); cUrl += '?' + urlOffQ[1]; } 
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val(); //alert(cUrl); 
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
jQuery('#btnConfirm').on('click',function(){ 
  var pageUrl = window.location.href;
  var splitUrl = pageUrl.split('&'); 
  var cUrl = splitUrl[0];
  //construct ppath
  var ppathVal = '';
  var phtml = jQuery(this).parent();
  jQuery(phtml).find('.ppath-nav li input[type="checkbox"]:checked').each(function(){ 
    ppathVal += jQuery(this).val() + ';' ;
  });
  if(parseInt(jQuery('#price1').val()) > 0){ cUrl += '&start='+ jQuery('#price1').val(); }
  if(parseInt(jQuery('#price2').val()) > 0){ cUrl += '&end='+ jQuery('#price2').val(); }
  if(jQuery('#filterby option:selected').val() != ""){ cUrl += '&filter='+ jQuery('#filterby option:selected').val(); }
  cUrl += '&sort='+ jQuery('#sortby option:selected').val();
  cUrl += '&ppath=' + ppathVal;
  window.location.assign(cUrl); 
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
</script>

<?php 
//get_footer( 'shop' ); 
get_footer(); 
?>
