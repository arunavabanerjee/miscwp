
<?php 
get_header();
?>

<?php if(have_posts()) : the_post(); 
$category_food = get_post_meta(get_the_ID(), '_cmb2_category_food', true); 
$theme_option = get_option('theme_option');
$currency_menu = $theme_option['currency_menu'];
$page_search = $theme_option['page_search'];
$currency_menu = $theme_option['currency_menu'];
if($currency_menu == 'GBP'){ $currency_menu = '&pound;'; }
$select_pages_checkout = $theme_option['select_pages_checkout'];
$restaurant_store = get_post_meta(get_the_id(), '_cmb2_restaurant_store', true); 

// check if the restaurant has filled all orders. 
$restaurant = get_post($restaurant_store); 
$orders = $wpdb->get_results('SELECT * FROM `quickfood_order` WHERE `restaurant_id` = "'.$restaurant->ID.'" ORDER BY ids DESC LIMIT 1', ARRAY_A); 
$lastOrderTime = $orders[0]['createds']; $currentTime = time();
 
$orderPerHr = get_field('orders_per_hour', $restaurant->ID); 
$maxOrderPerHr = get_field('max_no_order_per_hour', $restaurant->ID); 
$delivery_time = get_field('delivery_time', $restaurant->ID); 


$takeaway = get_post_meta($restaurant_store, '_cmb2_select_take_away', true); 
$delivery = get_post_meta($restaurant_store, '_cmb2_select_delivery', true); 

$minorder = get_post_meta($restaurant_store, '_cmb2_minimum_order', true);



//echo $takeawayno . $takeawayyes ;

?>
<?php 
$restaurant_args = array(
	'post_type' => 'restaurant',
	'p'   => $restaurant_store,
	);
 $query_restaurant = new WP_Query($restaurant_args);
 if($query_restaurant->have_posts()) :  $query_restaurant->the_post(); 

$logo_store = get_post_meta(get_the_ID(), '_cmb2_logo_store', true);
    $paramsa = array( 'width' => 120 , 'height' => 120 );
    $image_logo = bfi_thumb( $logo_store, $paramsa );
$phone_number = get_post_meta(get_the_ID(), '_cmb2_phone_number',true);
$working_period = get_post_meta(get_the_ID(), '_cmb2_working_period', true);

$url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(),'category') );
    $params = array( 'width' => 1400 , 'height' => 470 );
    $image_restaurant = bfi_thumb( $url, $params );
$area_store = get_post_meta(get_the_ID(), '_cmb2_area_store', true);
$address_store = get_post_meta(get_the_ID(), '_cmb2_address_store', true);
$delivery_charge = get_post_meta(get_the_ID(), '_cmb2_delivery_charge', true);
$free_over = get_post_meta(get_the_ID(), '_cmb2_free_over', true);

$select_Monday = get_post_meta(get_the_ID(), '_cmb2_select_Monday', true);
$open_Monday = get_post_meta(get_the_ID(), '_cmb2_open_Monday', true);
$close_Monday = get_post_meta(get_the_ID(), '_cmb2_close_Monday', true);
$select_Tuesday = get_post_meta(get_the_ID(), '_cmb2_select_Tuesday', true);
$open_Tuesday = get_post_meta(get_the_ID(), '_cmb2_open_Tuesday', true);
$close_Tuesday = get_post_meta(get_the_ID(), '_cmb2_close_Tuesday', true);
$select_Wednesday = get_post_meta(get_the_ID(), '_cmb2_select_Wednesday', true);
$open_Wednesday = get_post_meta(get_the_ID(), '_cmb2_open_Wednesday', true);
$close_Wednesday = get_post_meta(get_the_ID(), '_cmb2_close_Wednesday', true);
$select_Thursday = get_post_meta(get_the_ID(), '_cmb2_select_Thursday', true);
$open_Thursday = get_post_meta(get_the_ID(), '_cmb2_open_Thursday', true);
$close_Thursday = get_post_meta(get_the_ID(), '_cmb2_close_Thursday', true);
$select_Friday = get_post_meta(get_the_ID(), '_cmb2_select_Friday', true);
$open_Friday = get_post_meta(get_the_ID(), '_cmb2_open_Friday', true);
$close_Friday = get_post_meta(get_the_ID(), '_cmb2_close_Friday', true);
$select_Saturday = get_post_meta(get_the_ID(), '_cmb2_select_Saturday', true);
$open_Saturday = get_post_meta(get_the_ID(), '_cmb2_open_Saturday', true);
$close_Saturday = get_post_meta(get_the_ID(), '_cmb2_close_Saturday', true);
$select_Sunday = get_post_meta(get_the_ID(), '_cmb2_select_Sunday', true);
$open_Sunday = get_post_meta(get_the_ID(), '_cmb2_open_Sunday', true);
$close_Sunday = get_post_meta(get_the_ID(), '_cmb2_close_Sunday', true);
?>	<!-- SubHeader =============================================== -->
<section class="parallax-window" data-parallax="scroll" data-image-src="<?php echo esc_url($image_restaurant);?>" data-natural-width="1400" data-natural-height="470">
    <div id="subheader">
	<div id="sub_content">
    	<div id="thumb"><img src="<?php echo esc_url($image_logo);?>" alt=""></div>
         <?php //quickfood_rating();?>
        <h1><?php the_title();?></h1>
        <div><em><?php echo esc_attr( $area_store );?></em></div>
        <div><!--<i class="icon_pin"></i> <?php echo esc_attr($address_store );?> - --><strong><?php echo esc_html__( 'Delivery charge', 'quickfood' );?>:</strong> <?php echo esc_attr($currency_menu);?><?php echo esc_attr($delivery_charge);?>, <?php 
            if(!empty( $free_over)){
         echo esc_html__( 'free over', 'quickfood' );?> <?php echo esc_attr($currency_menu );?><?php echo esc_attr( $free_over );}else{}?></div>
    </div><!-- End sub_content -->
</div><!-- End subheader -->
</section><!-- End section -->
<?php wp_reset_postdata(); endif;
                    ?>
<!-- End SubHeader ============================================ -->

    <div id="position">
        <div class="container">
            <?php quickfood_breadcrumbs();?>
        </div>
    </div><!-- Position -->

<div class="container desktop-txt">
  <div class="row">
    <div class="col-md-12">
    <?php date_default_timezone_set('UTC');
                  $curnt_date = date("l");?>
    <?php if($curnt_date == "Monday"){ ?>
                      <?php if($select_Monday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Monday);?> TO <?php echo ($close_Monday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif ($curnt_date == "Tuesday") { ?>
                      <?php if($select_Tuesday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Tuesday);?> TO <?php echo ($close_Tuesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Wednesday"){ ?>
                      <?php if($select_Wednesday != "close"){ ?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Wednesday);?> TO <?php echo ($close_Wednesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Thursday"){?>
                      <?php if($select_Thursday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Thursday);?> TO <?php echo ($close_Thursday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Friday"){?>
                      <?php if($select_Friday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Friday);?> TO <?php echo ($close_Friday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Saturday"){?>
                      <?php if($select_Saturday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Saturday);?> TO <?php echo ($close_Saturday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Sunday"){?>
                      <?php if($select_Sunday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Sunday);?> TO <?php echo ($close_Sunday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }}?>  
      <p class="kfcPara"><?php echo get_the_content();?></p>
    </div>
  </div>
</div>

<div class="container margin_30">
		<div class="row">
        
			<div class="col-md-3">
				<?php $args = array(
					'post_type' => 'page',
					'p' => $page_search,
				);
				$query_page = new WP_Query($args);
				if($query_page-> have_posts()): $query_page->the_post();
				?>


            		<p>	
            		<?php if(isset($query_page) && !empty($query_page)){?>
            		<a href="<?php the_permalink();?>" class="btn_side">
            			<?php }else{?>
            		<a href="#" class="btn_side">
            			<?php }?>
            			Back to search
            		</a>
            		</p>
            		<?php endif; ?>
				<div class="box_style_1">
					<?php
					//$args = array( 'taxonomy' => 'foodcat', 'include' => $category_food, ); 
					$args = array( 'taxonomy' => 'foodcat', 'orderby'=> 'include', 'include' => $category_food, ); 
					//$terms = get_terms( 'foodcat', array('include' => $category_food, ));
					$terms = get_terms( $args );  
					?>
					<ul id="cat_nav">
						<?php foreach($terms as $term){ ?>
						<?php //foreach($termOrdering as $term){ ?>

							<li><a href="#<?php echo esc_attr($term->slug);?>" class="active"><?php echo esc_attr($term->name);?><span>(<?php echo esc_attr($term->count);?>)</span></a></li>

						<?php } wp_reset_postdata();?>
					</ul>
				</div><!-- End box_style_1 -->
                
				<div class="box_style_2 hidden-xs" id="help">
					<i class="icon_lifesaver"></i>
					<h4>Need <span>Help?</span></h4>
					 <?php $str = str_replace( array(' ') ,'', $phone_number);
			                    $str2 = str_replace( '+', '00', $str ); ?>
					<a href="tel://<?php echo esc_attr($str2);?>" class="phone"><?php echo esc_attr($phone_number );?></a>
					<small><?php echo esc_attr($working_period);?></small>
				</div>

        <div class="mobile-txt">
  <div class="row">
    <div class="col-md-12">
    <?php date_default_timezone_set('UTC');
                  $curnt_date = date("l");?>
    <?php if($curnt_date == "Monday"){ ?>
                      <?php if($select_Monday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Monday);?> TO <?php echo ($close_Monday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif ($curnt_date == "Tuesday") { ?>
                      <?php if($select_Tuesday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Tuesday);?> TO <?php echo ($close_Tuesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Wednesday"){ ?>
                      <?php if($select_Wednesday != "close"){ ?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Wednesday);?> TO <?php echo ($close_Wednesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Thursday"){?>
                      <?php if($select_Thursday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Thursday);?> TO <?php echo ($close_Thursday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Friday"){?>
                      <?php if($select_Friday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Friday);?> TO <?php echo ($close_Friday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Saturday"){?>
                      <?php if($select_Saturday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Saturday);?> TO <?php echo ($close_Saturday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Sunday"){?>
                      <?php if($select_Sunday != "close"){?>
                      <span class="opening kfcHdn"><?php echo esc_html__( 'AVAILABLE TODAY FROM', 'quickfood' );?> <?php echo ($open_Sunday);?> TO <?php echo ($close_Sunday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }}?> 
      <p class="kfcPara"><?php echo get_the_content();?></p>
    </div>
  </div>
</div>
			</div><!-- End col-md-3 -->
            
			<div class="col-md-6">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
				<div class="box_style_2" id="main_menu">
					<h2 class="inner">Menu</h2>
					<?php
					$terms = get_terms( 'foodcat', array( 'orderby'=> 'include', 'include' => $category_food, )); 
					?>
					<?php 
						$i=1;
					foreach($terms as $term){ ?>
					<h3 <?php if($i=1){?>class="nomargin_top"<?php }else{}?> id="<?php echo esc_attr($term->slug);?>"><?php echo esc_attr($term->slug);?></h3>
					<p>
						<?php echo esc_attr($term->description); ?>
					</p>
					<table class="table table-striped cart-list">
					<thead>
					<!--<tr>
						<th><?php echo esc_html__( 'Item', 'quickfood' );?></th>
						<th><?php echo esc_html__( 'Price', 'quickfood' );?></th>
						<th><?php echo esc_html__( 'Order', 'quickfood' );?></th>
					</tr>-->
					</thead>
					<tbody>
					<?php 
					 $food_arr = new WP_Query(
						array( 'post_type' => 'food',
							'posts_per_page' => -1,
							'tax_query' => array(
						    		array(
								    'taxonomy' => 'foodcat',
								    'field' => 'id',
								    'terms' => $term->term_id, 
						                    'include_children' => false, 
								)
							 )
						)
					 );
					$ii=1;
					if($food_arr->have_posts()) : while($food_arr->have_posts()) : $food_arr->the_post();
					 $textmoney = get_post_meta(get_the_id(), '_cmb2_textmoney', true);  
					 $mainId = get_the_ID(); $itemArray = array($mainId);
					?>

					<tr id="detail-<?php the_id();?>">
						<input type="hidden"  value="<?php the_id();?>">
						<td colspan="3" class="no-padding">
             <table width="100%" class="popup-box">
               <tr class="open-details detail-<?php the_id();?>">
                  <td>
                    <h5 class="col-xs-12"><?php //echo esc_attr($ii);?>
		      <span class="name-<?php the_id();?> item_name"><?php the_title();?></span>
                      <input type="hidden" id="name-<?php the_id();?>" value="<?php the_title();?>">
                    </h5>
                    <p><?php the_content();?></p>
                  </td>
                  <td class="text-right price-td">
                    <strong class="col-xs-12">
                        <span class="price-<?php the_id();?> price food-price"> <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?> <?php echo esc_attr( $textmoney );?></span>
                        <div class="clearfix"></div>
                        <i class="arrow_carrot-down"></i>
                    </strong>
                    <input type="hidden" id="price-<?php the_id();?>" value="<?php echo esc_attr( $textmoney );?>">
                  </td>
                  <?php date_default_timezone_set('UTC');
                        $curnt_date = date("l");?>
                  <!--<td class="options text-right"></td>-->
               </tr>
               <tr class="popup-box-td">
		          
                  <td colspan="2">
                    <!-- popup strat-->
                                                        
                    <div class="clearfix popup-box-div">
                      <hr>
                        <div class="box-details clearfix">
                        <?php //$term_children = get_term_children( $term->term_id, 'foodcat' ); ?>
 			<?php $term_children = get_field('submenu', $mainId ); ?>
                        <?php if(!empty($term_children)){ 
				foreach($term_children as $termch){ ?>
                        <?php    $submenu_arr = new WP_Query(
                                        array('post_type' => 'food', 'posts_per_page' => -1,
                                        'tax_query' => array(
                                            array('taxonomy' => 'foodcat', 'field' => 'id', 
                                                  'terms' => $termch, 'include_children'=>false )
                                        ),
                                        'order' => 'DESC', 'orderby' => 'title' 

                                        )); ?>
                        <?php if($submenu_arr->have_posts()) : ?>     
                        <?php $chooseOpt = get_field('choose_option', 'foodcat_'.$termch); ?>
                        <div class="clearfix sub-options optionBox <?php echo $chooseOpt ?>" data-option-type="select-one">
			     
           <?php //echo get_cat_name( $termch ); ?>
                             <p class="option-choice-header col-xs-12"><?php echo $chooseOpt ?></p>
                             <?php while( $submenu_arr->have_posts() ) : $submenu_arr->the_post(); 
				    $textmoney = get_post_meta(get_the_id(), '_cmb2_textmoney', true);
				?>
			     <div class="col-md-6 col-sm-6 col-xs-12 chk-bx">
                                <label>
				<input type="checkbox" class="name-<?php echo get_cat_name( $termch ); ?>" id="name-<?php the_id();?>" name="name-<?php echo get_cat_name( $termch ); ?>" value="<?php the_id();?>" />&nbsp;&nbsp;<?php the_title(); ?><?php if( $textmoney != "0.00" ){ ?><span class="price-<?php the_id();?> price"> (<?php if(isset($currency_menu) && !empty($currency_menu)){ echo '+'.esc_attr($currency_menu); }else{ } ?><?php echo esc_attr( $textmoney ); ?>)<?php } ?></span>
				</label>
                    		<input type="hidden" id="price-<?php the_id();?>" value="<?php echo esc_attr( $textmoney );?>">
                              </div>
                            <?php endwhile; ?>
                        </div>
                       <?php endif; ?>
                        <?php }} //wp_reset_postdata(); ?>
			  
                            <div class="clearfix"></div>  
                            <hr>
                            <div class="col-xs-12">
                              <div class="qty pull-left">
                                  <div class="input-group">
                                      <input type="text" name="" value="1" class="qtynty input-number">
                                  </div>
                              </div>                                                       

                              <div class="pull-right <?php if(!empty($term_children)){ 
                                if($chooseOpt == "choose one"){?>option
                                <?php } elseif($chooseOpt == "choose any"){?>any
                                <?php } elseif($chooseOpt == "choose two"){?>option
                                <?php }} else{?>no-option<?php }?>">


                              
                                <!--<a type="submit" class="addToCart pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add to cart</a>-->
                                <?php if($curnt_date == "Monday"){ ?>
                                <?php if($select_Monday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif ($curnt_date == "Tuesday") { ?>
                                <?php if($select_Tuesday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif($curnt_date == "Wednesday"){ ?>
                                <?php if($select_Wednesday != "close"){ ?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif($curnt_date == "Thursday"){?>
                                <?php if($select_Thursday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif($curnt_date == "Friday"){?>
                                <?php if($select_Friday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif($curnt_date == "Saturday"){?>
                                <?php if($select_Saturday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }} elseif($curnt_date == "Sunday"){?>
                                <?php if($select_Sunday != "close"){?>
                                <a href="#0" class="addToCart add-to-cart-button" id="<?php echo $mainId; ?>"><i class="icon_plus_alt2"></i> Add to cart</a>
                                <?php }else{?>
                                <span class="label label-danger">Closed</span>
                                <?php }}?> 
                              </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    
                    <!-- popup end-->     
                  </td>
               </tr>
             </table>                                 
                               
            </td>                                    
                
	  </tr>
          <script>
            $(document).ready(function(){
        
              $(".detail-<?php echo $mainId; ?>").click(function(){
                  $("#detail-<?php echo $mainId; ?> .popup-box").toggleClass("open");
              });
        
            });
          </script>

					<?php $ii++; endwhile;endif;?>
					</tbody>
					</table>
					<hr>
					<?php } ?>
				</div><!-- End box_style_1 -->
			</div><!-- End col-md-6 -->
            
			<div class="col-md-3 mobile-order-fix" id="sidebar">
            		<div class="theiaStickySidebar">
				<div id="cart_box" >
					<h3>Your order <i class="icon_cart_alt pull-right"></i>
                                            <span class="up-arrow">
                                                <i class="arrow_carrot-up"></i>
                                                <i class="arrow_carrot-down"></i>
                                            </span>
                                        </h3>
					<table class="table table_summary" >
					<script type="text/javascript">
		jQuery(function($){		

		$(document).ready(function() {
	
		var Arrays=new Array(); 
	
		$('.no-option .add-to-cart-button').click(function(){
		
		var thisID = $(this).attr('id'); 

		var itemname  = document.getElementsByClassName('name-'+thisID)[0].innerHTML;
		var itemprice  = $('#price-'+thisID).val();
                
		/*if(include(Arrays,thisID))
		{
			var price = $('#each-'+thisID).children(".shopp-price").find('em').html(); 
			var quantity = $('#each-'+thisID).children(".shopp-quantity").html();
			//quantity = parseFloat(quantity)+parseFloat(1);
			var addedquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			quantity = parseFloat(quantity)+parseFloat(addedquantity);
			var total = parseFloat(itemprice)*parseInt(quantity);

			total = total.toFixed(2);
			$('#each-'+thisID).children(".shopp-price").find('em').html(total);
			$('#each-'+thisID).children(".shopp-price").find('.innershopp-quantity').html(quantity);
			$('#each-'+thisID).children(".shopp-quantity").html(quantity);
			
			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges)-parseFloat(price);
			prev_charges = prev_charges.toFixed(2);
			prev_charges = parseFloat(prev_charges)+parseFloat(total);
			prev_charges = prev_charges.toFixed(2);
			$('.cart-total span').html(prev_charges);
			
			$('#total-hidden-charges').val(prev_charges);
		}
		else
		{*/
			Arrays.push(thisID);
			
			var iniquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			//no option in this section
			var optionprice = 0; var opthtml = ''; 

			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges);
			var curr_charges = parseFloat(itemprice)*parseInt(iniquantity); 
			prev_charges += parseFloat(curr_charges);

			prev_charges = prev_charges.toFixed(2); 

			$('.cart-total span').html(prev_charges);
			$('#total-hidden-charges').val(prev_charges);
			
		$('#name_foodid').append('<tr><td class="each-name">' +itemname+'</td></tr>');
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
	if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
		prev_charges = prev_charges.toFixed(2);
	}else{	prev_charges = parseFloat(prev_charges1); }
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('#total-hidden-charges2').val(prev_charges);
$('.total-hidden-charges2').val(prev_charges);

var tot_quantity = parseInt($('.mobile-item-display .left span').text()) + parseInt(iniquantity);
$('.mobile-item-display .left span').text(tot_quantity);
$('.mobile-item-display .right span').text(prev_charges);

//Get price, name item, quantity
	var myValues = [] , nameValues = [], quantityValues = [];
	 $('em.price_order').each( function() {
		myValues.push($(this).text()); 
		console.log(myValues);
	});
	$("#arrprice").val(myValues);

	 $('.each-name').each( function() {
		nameValues.push($(this).text());
		console.log(nameValues);
	});

	$("#arrname").val(nameValues);

	 $('.shopp-quantity').each( function() {
		quantityValues.push($(this).text());
		console.log(quantityValues);
	});
	$("#number_quantity").val(quantityValues);

	//}// endfor subArrays 
	$('.two input:checkbox').removeAttr('disabled');
        $('.two input:checkbox').removeAttr('checked');
        $('.two .chk-bx').removeClass('selected2');
        $(this).val('uncheck all');
	});	


        $('.any .add-to-cart-button').click(function(){
		
		var thisID = $(this).attr('id'); 

		var itemname  = document.getElementsByClassName('name-'+thisID)[0].innerHTML;
		var itemprice  = $('#price-'+thisID).val();
                
		/*if(include(Arrays,thisID))
		{
			var price = $('#each-'+thisID).children(".shopp-price").find('em').html(); 
			var quantity = $('#each-'+thisID).children(".shopp-quantity").html();
			//quantity = parseFloat(quantity)+parseFloat(1);
			var addedquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			quantity = parseFloat(quantity)+parseFloat(addedquantity);
			var total = parseFloat(itemprice)*parseInt(quantity);

			total = total.toFixed(2);
			$('#each-'+thisID).children(".shopp-price").find('em').html(total);
			$('#each-'+thisID).children(".shopp-price").find('.innershopp-quantity').html(quantity);
			$('#each-'+thisID).children(".shopp-quantity").html(quantity);
			
			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges)-parseFloat(price);
			prev_charges = prev_charges.toFixed(2);
			prev_charges = parseFloat(prev_charges)+parseFloat(total);
			prev_charges = prev_charges.toFixed(2);
			$('.cart-total span').html(prev_charges);
			
			$('#total-hidden-charges').val(prev_charges);
		}
		else
		{*/
			Arrays.push(thisID);
			
			var iniquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			$('#name_foodid').append('<tr><td class="each-name">' +itemname+'</td></tr>');

			//foreach option add the price
			var optionprice = 0; var opthtml = ''; 
			$('#detail-'+thisID+' .popup-box-td .sub-options input[type=checkbox]').each(function(index, element){ 
			   var checkId = $(this).val(); 
			   var optprice = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').val(); 
			   var optnamestr = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').parent().text();
         		   var optname = optnamestr.split("(");
			   if( $(this).is(':checked') ){ //alert(checkId); alert(optprice);
			      opthtml += '<div class="more-option"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName">'+optname[0]+'</div>';
            if( optprice != '0.00' ){ style = 'style="display:block;"'; } else { style = 'style="display:none;"'; }
            opthtml +='<div class="pull-right shopp-price"'+style+'>x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+optprice+'</em></div>';
            opthtml +='</div>';
			      optionprice += parseFloat(optprice); 
			     $('#name_foodid').append('<tr><td class="each-name">' +optname[0]+'</td></tr>');
			   }	
			});

			var prev_charges = $('.cart-total span').html(); 
			prev_charges = parseFloat(prev_charges);			
			var curr_charges = parseFloat(itemprice)*parseInt(iniquantity);
			prev_charges += curr_charges;
      var opt_charges = parseFloat(optionprice);
      prev_charges += parseFloat(opt_charges);
			prev_charges = prev_charges.toFixed(2); 


			$('.cart-total span').html(prev_charges);
			$('#total-hidden-charges').val(prev_charges);
			
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
	if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
		prev_charges = prev_charges.toFixed(2);
	}else{	prev_charges = parseFloat(prev_charges1); }
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('#total-hidden-charges2').val(prev_charges);
$('.total-hidden-charges2').val(prev_charges);

var tot_quantity = parseInt($('.mobile-item-display .left span').text()) + parseInt(iniquantity);
$('.mobile-item-display .left span').text(tot_quantity);
$('.mobile-item-display .right span').text(prev_charges);
//Get price, name item, quantity
	var myValues = [] , nameValues = [], quantityValues = [];
	 $('em.price_order').each( function() {
		myValues.push($(this).text()); 
		console.log(myValues);
	});
	$("#arrprice").val(myValues);

	 $('.each-name').each( function() {
		nameValues.push($(this).text());
		console.log(nameValues);
	});

	$("#arrname").val(nameValues);

	 $('.shopp-quantity').each( function() {
		quantityValues.push($(this).text());
		console.log(quantityValues);
	});
	$("#number_quantity").val(quantityValues);

	//}// endfor subArrays 
	$('.two input:checkbox').removeAttr('disabled');
        $('.two input:checkbox').removeAttr('checked');
        $('.two .chk-bx').removeClass('selected2');
        $(this).val('uncheck all');
	});
	 
        $('.option .add-to-cart-button').click(function(){

		var thisID = $(this).attr('id'); 

		var itemname  = document.getElementsByClassName('name-'+thisID)[0].innerHTML;
		var itemprice  = $('#price-'+thisID).val();
                if (!$('.one input[ type = "checkbox" ]').is(':checked')) {
                    $('.one .option-choice-header').css({'background':'#F00','color':'#FFF'  })
                    return false;
                }
                else{
                $('.option-choice-header').css({'background':'none','color':'#A1CAF1' })
                }

		/*if(include(Arrays,thisID))
		{
			var price = $('#each-'+thisID).children(".shopp-price").find('em').html(); 
			var quantity = $('#each-'+thisID).children(".shopp-quantity").html();
			//quantity = parseFloat(quantity)+parseFloat(1);
			var addedquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			quantity = parseFloat(quantity)+parseFloat(addedquantity);
			var total = parseFloat(itemprice)*parseInt(quantity);

			total = total.toFixed(2);
			$('#each-'+thisID).children(".shopp-price").find('em').html(total);
			$('#each-'+thisID).children(".shopp-price").find('.innershopp-quantity').html(quantity);
			$('#each-'+thisID).children(".shopp-quantity").html(quantity);
			
			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges)-parseFloat(price);
			prev_charges = prev_charges.toFixed(2);
			prev_charges = parseFloat(prev_charges)+parseFloat(total);
			prev_charges = prev_charges.toFixed(2);
			$('.cart-total span').html(prev_charges);
			
			$('#total-hidden-charges').val(prev_charges);
		}
		else
		{*/
			Arrays.push(thisID);
			
			var iniquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();

			$('#name_foodid').append('<tr><td class="each-name">' +itemname+'</td></tr>');
			//foreach option add the price
			var optionprice = 0; var opthtml = ''; 
			$('#detail-'+thisID+' .popup-box-td .sub-options input[type=checkbox]').each(function(index, element){ 
			   var checkId = $(this).val(); 
			   var optprice = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').val(); 
			   var optnamestr = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').parent().text();
         var optname = optnamestr.split("(");
			   if( $(this).is(':checked') ){ //alert(checkId); alert(optprice);
			      opthtml += '<div class="more-option"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName">'+optname[0]+'</div>';
            if( optprice != '0.00' ){ style = 'style="display:block;"'; } else { style = 'style="display:none;"'; }
            opthtml +='<div class="pull-right shopp-price"'+style+'>x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+optprice+'</em></div>';
            opthtml +='</div>';
			      optionprice += parseFloat(optprice)*parseInt(iniquantity); 
			     $('#name_foodid').append('<tr><td class="each-name">' +optname[0]+'</td></tr>');
			   }	
			});

			var prev_charges = $('.cart-total span').html(); 
			prev_charges = parseFloat(prev_charges);
			var curr_charges = parseFloat(itemprice)*parseInt(iniquantity);
			prev_charges += parseFloat(curr_charges);
			var opt_charges = parseFloat(optionprice);
			prev_charges += parseFloat(opt_charges);
			prev_charges = prev_charges.toFixed(2);

			$('.cart-total span').html(prev_charges);
			$('#total-hidden-charges').val(prev_charges);	
		
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
        if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
		prev_charges = prev_charges.toFixed(2);
	}else{	prev_charges = parseFloat(prev_charges1); }
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('#total-hidden-charges2').val(prev_charges);
$('.total-hidden-charges2').val(prev_charges);

var tot_quantity = parseInt($('.mobile-item-display .left span').text()) + parseInt(iniquantity);
$('.mobile-item-display .left span').text(tot_quantity);
$('.mobile-item-display .right span').text(prev_charges);

//Get price, name item, quantity
	var myValues = [] , nameValues = [], quantityValues = [];
	 $('em.price_order').each( function() {
		myValues.push($(this).text()); 
		console.log(myValues);
	});
	$("#arrprice").val(myValues);

	 $('.each-name').each( function() {
		nameValues.push($(this).text());
		console.log(nameValues);
	});

	$("#arrname").val(nameValues);



	 $('.shopp-quantity').each( function() {
		quantityValues.push($(this).text());
		console.log(quantityValues);
	});
	$("#number_quantity").val(quantityValues);

	//}// endfor subArrays 
	$('.one input:checkbox').removeAttr('disabled');
        $('.one input:checkbox').removeAttr('checked');
        $('.one .chk-bx').removeClass('selected2');
        $(this).val('uncheck all')
	});	

        $('.if-cart').hide(); 
	$('#cart_box .btn_full').attr('disabled', 'true');
        $('.add-to-cart-button').click(function(){
            $('.if-cart').show()
            var price = $('span.price-sub'),
                    priceValue = Number(price.text().replace(/[^0-9\.]+/g,"")),
                    priceLimit = parseFloat(<?php echo $minorder ; ?>)-parseFloat(0.01),
                    shipping = $(".if-cart");
          $('#cart_box .btn_full').attr('disabled', 'true');

                if (priceValue > priceLimit) {
                    shipping.hide();
          $('#cart_box .btn_full').removeAttr('disabled');

                }
        });

	
	$('.remove_item').livequery('click', function() {
		
		//var deduct = $(this).parent().children(".shopp-price").find('em').html(); 
		var parentID = $(this).parent().attr('id');
		var deduct = $(this).parent().find('em').html(); 
		var qdeduct = $(this).parent().find('.shopp-quantity').html();  

		var optdeduct = 0;
		if( $('#'+parentID+' .more-option') != null ){
		$('#'+parentID+' .more-option').each(function(index, element){ 
		   var opdeduct = $(this).find('em').html(); 
		   var opqdeduct = $(this).find('.shopp-quantity').html(); 
		   optdeduct += parseFloat(opdeduct)*parseInt(opqdeduct);
		}); 
		}
		var prev_charges = $('.cart-total span').html();
		
		var thisID = $(this).parent().attr('id').replace('each-','');
		
		var pos = getpos(Arrays,thisID);
		Arrays.splice(pos,1,"0")
		
		prev_charges = parseFloat(prev_charges)- (parseFloat(deduct) * parseInt(qdeduct));
		prev_charges = parseFloat(prev_charges) - parseFloat(optdeduct);
		prev_charges = prev_charges.toFixed(2);
		$('.cart-total span').html(prev_charges);
		$('#total-hidden-charges').val(prev_charges);


		var prev_charges1  = $('#total-hidden-charges').val();
	//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
	//}else{$("#check-hidden").val("delivery");}
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
	if((prev_charges1 > 0)){
	prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
	prev_charges = prev_charges.toFixed(2);
	}else{prev_charges = parseFloat(prev_charges1)}
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('#total-hidden-charges2').val(prev_charges);
$('.total-hidden-charges2').val(prev_charges);

var tot_quantity = parseInt($('.mobile-item-display .left span').text()) - parseInt(qdeduct);
$('.mobile-item-display .left span').text(tot_quantity);
$('.mobile-item-display .right span').text(prev_charges);
//
var prev_charges1  = $('#total-hidden-charges').val();
	//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
	//}else{$("#check-hidden").val("delivery");}
	//if((prev_charges1 > 0) && $("#check1").is(":checked")){
	if((prev_charges1 > 0)){ prev_charges = prev_charges1 ;
	}else{prev_charges1 = 0}
		
	$(this).parent().remove();

   //check if the minimum amount is reached.
    priceLimit = parseFloat(<?php echo $minorder ; ?>)-parseFloat(0.01);
    prev_charges1 = parseFloat(prev_charges1);
    if( prev_charges1 < priceLimit ){  
      $('.if-cart').show(); $('#cart_box .btn_full').attr('disabled', 'true');  
    }
		
	});	

	$(this).parent().remove();

	});
	});	
		
function include(arr, obj) {
  for(var i=0; i<arr.length; i++) {
    if (arr[i] == obj) return true;
  }
}

function getpos(arr, obj) {
  for(var i=0; i<arr.length; i++) {
    if (arr[i] == obj) return i;
  }
}

function Check1(){


jQuery(function($){		
	
	var prev_charges1  = $('#total-hidden-charges').val();
if( $("#check").is(":checked")){
	$("#check-hidden").val("delivery");
}else{$("#check-hidden").val("takeaway");}

	if((prev_charges1 > 0) && $("#check").is(":checked")){
	prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>);
	var prev_chargesma = parseFloat(prev_charges1);
	prev_charges = prev_charges.toFixed(2);
	}else{prev_charges = 0}
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('.total-hidden-charges2').val(prev_chargesma);
$('.total-hidden-charges2').val(prev_chargesma);		
 
 });
}

function Check2(){
	jQuery(function($){	
		
	var prev_charges2  = $('#total-hidden-charges').val();
	if($("#check1").is(":checked")){
	$("#check-hidden").val("takeaway");
}else{$("#check-hidden").val("delivery");}
	if((prev_charges2 > 0) && $("#check1").is(":checked")){
	var prev_charges = parseFloat(prev_charges2);
	}else{prev_charges = 0}
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('.total-hidden-charges2').val(prev_charges);
		});
}




						</script> 
					<tbody  id="mycart">
						
					
					</tbody>
					<tbody  id="name_foodid">
						
					
					</tbody>
					</table>
					<hr>
					<div class="row" id="options_2">
						
					</div><!-- Edn options 2 -->
					<?php if ($delivery == 'yes'){ ?>
					<label class="delivery-time">
	                    <!--<input type="radio" id="check" checked ="checked"  onclick = "Check1()" value=""  name="option_3">-->
			    <?php //echo esc_html__('Delivery', 'quickfood' );?>
			    <?php echo esc_html__('Delivery Time: ', 'quickfood').$delivery_time.' mins';  ?>

                	</label>
                	<?php }?>
                	<?php if ($takeaway == 'yes'){ ?>
                	<label>
                    	<input type="radio" id="check1"  onclick = "Check2()" value=""  name="option_3">
                		<?php echo esc_html__('Take Away', 'quickfood' );?>
                	</label>
                	<?php }?>
					<hr>
					<table class="table table_summary">
					<tbody>
					<tr>
						<td>
							 
							  <div class="cart-total"><?php echo esc_html__('Subtotal', 'quickfood');?> <em class="pull-right"><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><span class="price-sub">0<span></em>
                                                          <div class="if-cart">Minimum &pound; <?php echo $minorder; ?></div>
							 <input type="hidden" name="total-hidden-charges" id="total-hidden-charges" value="0" />
               <?php //echo $minorder; ?>
							 </div>
						</td>
					</tr>
					<tr>
						<td>
							 Delivery fee <span class="pull-right"><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><?php echo esc_attr($delivery_charge );?></span>
						</td>
					</tr>
					<tr class="mobile-item-hiden">
						<td class="total">
							 <div class="cart-total2">TOTAL <em class="pull-right"><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?>&nbsp;<span>0<span></em>

							 
							 </div>
						</td>
						
					</tr>
					</tbody>
					</table>
                                        
                                        <div class="mobile-item-display">
                                                <div class="left">Items<span>0</span></div>
                                                <div class="right">Total 
                                                    <div class="price-sign pull-right">
                                                    <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><span> 0.00</span>
                                                    </div>
                                                </div>
                                        </div>
                                        
					<hr>
					<form action="<?php if(isset($select_pages_checkout)){ echo get_page_link($select_pages_checkout); }else{echo "#";}?>" method="POST">
				 	<input type="hidden" name="id_menu"  id="id_menu" value="<?php echo get_the_ID();?>" />
				 	<input type="hidden" name="delivery_charge"  id="deliverry" value="<?php echo esc_attr($delivery_charge );?>" />
					<input type="hidden" name="total-hidden-charges2"  class="total-hidden-charges2" value="0" />
					<input type="hidden" name="check"  id="check-hidden" value="0" />
					<input type="hidden" name="phone-store"  id="phonenumber" value="<?php echo esc_attr($phone_number);?>" />
					<input type="hidden" name="working-period"  id="workingperiod" value="<?php echo esc_attr($working_period);?>" />
					<input type="hidden" name="arrprice"  id="arrprice" value="" />
					<input type="hidden" name="arrname"  id="arrname" value="" />
					<input type="hidden" name="number_quantity"  id="number_quantity" value="" />
          <input type="hidden" name="restaurant_id"  id="restaurant_id" value="<?php echo esc_attr($restaurant_store); ?>" />
          <input type="hidden" name="order_time"  id="order_time" value="<?php echo time();?>" />

			<?php if($orderPerHr < $maxOrderPerHr){ ?>
			   <button type="submit" class="btn_full"><?php echo esc_html__( 'Checkout', 'quickfood' );?></button>
			<?php } else{ // if orderperhr == maxorderperhr ?>
                        <?php $diff = $currentTime - $lastOrderTime; //echo $diff; ?>
			<?php if( $diff > 3600 ){ ?> 
			   <button type="submit" class="btn_full"><?php echo esc_html__( 'Checkout', 'quickfood' );?></button>
			<?php } else { ?>
			<p style="color:red; background:yellow;">
			  Due to high demand we're currently not able to take orders at the moment, <br/> 
                          Please try again on the hour' - Sorry for any inconvenience <br/>
			</p>
			<?php }} ?>
						
					</form>
					
				</div><!-- End cart_box -->
                </div><!-- End theiaStickySidebar -->
			</div><!-- End col-md-3 -->
            
		</div><!-- End row -->
</div><!-- End container -->
<!-- End Content =============================================== -->
<script>
$(function() {

    $(".qty .input-group").append('<div class="inc button btn btn-default control"><span class="glyphicon glyphicon-plus"><span>+</span></span></div>');
$(".qty .input-group").prepend('<div class="dec button btn btn-default control"><span class="glyphicon glyphicon-minus"></span></div>');

});
$(document).ready(function(){
$(".button").on("click", function() {

  var $button = $(this);
  var oldValue = $button.parent().find("input").val();

  if ($button.text() == "+") {
	  var newVal = parseFloat(oldValue) + 1;
	} else {
   // Don't allow decrementing below zero
    if (oldValue > 0) {
      var newVal = parseFloat(oldValue) - 1;
    } else {
      newVal = 0;
    }
  }

  $button.parent().find("input").val(newVal);

});



});
$('.one :checkbox').click(function() {
   var $checkbox = $(this), checked = $checkbox.is(':checked');
   $checkbox.closest('.optionBox').find(':checkbox').prop('disabled', checked).closest(".chk-bx").toggleClass('selected2');
   $checkbox.prop('disabled', false).closest(".chk-bx").toggleClass('selected');

});


//var theCheckboxes = $(".two input[type='checkbox']");
//theCheckboxes.click(function()
//{
   // if (theCheckboxes.filter(":checked").length > 2)
        //theCheckboxes.closest('.optionBox').find(':checkbox').removeAttr("checked");
//});

$(".two input:checkbox").click(function() {
  var bol = $(".two input:checkbox:checked").length >= 2;     
  $(".two input:checkbox").not(":checked").attr("disabled",bol);
  $(".two input:checkbox").not(":checked").closest('.chk-bx').toggleClass("selected2",bol);
});

$(this).siblings('.msg-area').find('ul.me').each(function() {
    console.log('me');
});


</script>

<?php
endif;?>
<?php get_footer();?>


