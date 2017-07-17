<?php
/**
 * Setup quickfood Child Theme's textdomain.
 *
 * Declare textdomain for this child theme.
 * Translations can be filed in the /languages/ directory.
 */
function quickfood_child_theme_setup() {
	load_child_theme_textdomain( 'quickfood-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'quickfood_child_theme_setup' );


add_filter( 'wp_mail_from', 'your_email' );
function your_email( $original_email_address ) { return get_option('admin_email'); } 

add_filter('wp_mail_from_name', 'new_mail_from_name');
function new_mail_from_name($old) { return 'Doordrop Deliveries'; }

// Add Code is here.
remove_shortcode('subheader');

add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

//Subheader
add_shortcode('subheader', 'subheader_newfunc');
function subheader_newfunc($atts, $content = null){
  extract(shortcode_atts(array(
    'start_title'      => '',
    'title_subheader'  => '',
    'end_title'      => '',
    'style_title'      => '',
    'sub_subheader'    => '',
 ), $atts));
  ob_start(); 
  ?>
   <div id="subheader">
        <div id="sub_content">
           <?php if($style_title == 'text_rotating'){?>
            <h1>
             <?php echo esc_attr($start_title );?>
              <strong id="js-rotating"><?php echo esc_attr($title_subheader );?></strong>
                    <?php echo esc_attr($end_title );?>
            </h1>

            <script type="text/javascript">     
/*!
 * Morphext - Text Rotating Plugin for jQuery
 * https://github.com/MrSaints/Morphext
 *
 * Built on jQuery Boilerplate
 * http://jqueryboilerplate.com/
 *
 * Copyright 2014 Ian Lai and other contributors
 * Released under the MIT license
 * http://ian.mit-license.org/
 */

/*eslint-env browser */
/*global jQuery:false */
/*eslint-disable no-underscore-dangle */

( function($) {
    "use strict";

    var pluginName = "Morphext",
        defaults = {
            animation: "bounceIn",
            separator: ",",
            speed: 2000,
            complete: $.noop
        };

    function Plugin (element, options) {
        this.element = $(element);

        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._init();
    }

    Plugin.prototype = {
        _init: function () {
            var $that = this;
            this.phrases = [];

            this.element.addClass("morphext");

            $.each(this.element.text().split(this.settings.separator), function (key, value) {
                $that.phrases.push($.trim(value));
            });

            this.index = -1;
            this.animate();
            this.start();
        },
        animate: function () {
            this.index = ++this.index % this.phrases.length;
            this.element[0].innerHTML = "<span class=\"animated " + this.settings.animation + "\">" + this.phrases[this.index] + "</span>";

            if ($.isFunction(this.settings.complete)) {
                this.settings.complete.call(this);
            }
        },
        start: function () {
            var $that = this;
            this._interval = setInterval(function () {
                $that.animate();
            }, this.settings.speed);
        },
        stop: function () {
            this._interval = clearInterval(this._interval);
        }
    };

    $.fn[pluginName] = function (options) {
        return this.each(function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery);

( function($) {
    $("#js-rotating").Morphext({
                animation: "fadeIn", // Overrides default "bounceIn"
                separator: ",", // Overrides default ","
                speed: 2300, // Overrides default 2000
                complete: function () {
                    // Overrides default empty function
                }
            });
    })(jQuery);
            </script>
            
	<?php }else{?>

              <h1>
                
                <?php echo esc_attr($title_subheader );?>
              </h1>
            
	<?php }?>

            <p>
                <?php echo esc_attr($sub_subheader );?>
            </p>

          <?php $theme_option = get_option('theme_option');
            $page_search = $theme_option['page_search_address']; ?>

            <?php $args = array(
              'post_type' => 'page',
              'p' => $page_search,
            );

            $query_page_grid = new WP_Query($args);
            if($query_page_grid-> have_posts()): $query_page_grid->the_post();
            ?>

            <form method="post" action="<?php the_permalink();?>">
                <div id="custom-search-input">
                    <div class="input-group ">
                        <input type="text" name="address" class=" search-query" placeholder="<?php echo esc_html__('Your Postal Code','quickfood'); ?>">
                        <span class="input-group-btn">
                        <input type="submit" class="btn_search" value="submit">
                        </span>
                    </div>
                </div>
            </form>

          <?php endif;?>

        </div><!-- End sub_content -->
    </div><!-- End subheader -->
   <?php  return ob_get_clean();
} 


remove_shortcode('most_popular');
add_shortcode('most_popular', 'most_popular_newfunc');
function most_popular_newfunc($atts, $content = null){
  extract(shortcode_atts(array(
    'choose_order'     => '',
    'order_res'     => '',
    'number_restaurant'     => '',
 ), $atts));
  ob_start(); 
  
  ?> 

 <?php 
            $args = array(
              'post_type' => 'restaurant',
              'posts_per_page' => $number_restaurant,
              'orderby' => $choose_order,
              'order'   => $order_res,
          );
            $restaurant_list = new WP_Query($args);
            $i=0;
            if($restaurant_list->have_posts()) : while($restaurant_list -> have_posts()) : $restaurant_list->the_post();
        
            $area_store = get_post_meta(get_the_ID(), '_cmb2_area_store', true);
            $address_store = get_post_meta(get_the_ID(), '_cmb2_address_store', true);
            $popular_food = get_post_meta(get_the_ID(), '_cmb2_popular_food', true);
            $logo_store = get_post_meta(get_the_ID(), '_cmb2_logo_store', true);
        $logo_img = array( 'width' => 98 , 'height' => 98 );
        $img_logo_vc = bfi_thumb( $logo_store, $logo_img );
        $select_take_away = get_post_meta(get_the_ID(), '_cmb2_select_take_away', true);
        $select_delivery = get_post_meta(get_the_ID(), '_cmb2_select_delivery', true);
        $menu_store = get_post_meta(get_the_ID(),'_cmb2_menu_store', true );
        $minimum_order = get_post_meta(get_the_ID(),'_cmb2_minimum_order', true );
        //$open_Monday = get_post_meta(get_the_ID(), '_cmb2_open_Monday', true);

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

          ?>
<?php if(isset($popular_food) && ($popular_food == "show_popular")){
    $i++;
  ?>

<?php if(($i%2==1) &&($i>1)){?>
 <div class="col-md-6"  style="clear:both;padding-left:0px;">
  <?php }else{?> <div class="col-md-6"><?php }?>
          <a href="<?php the_permalink();?>" class="strip_list">
          <div class="ribbon_1"><?php echo esc_html__( 'Popular', 'quickfood' );?></div>
              <div class="desc">
                  <div class="thumb_strip">
                      <img src="<?php echo esc_url($img_logo_vc);?>" alt="">
                  </div>
                   
                  <h3><?php the_title();?></h3>
                  <div class="type">
                       <?php echo esc_attr($area_store );?>
                  </div>



                  <?php date_default_timezone_set('UTC');
                  $curnt_date = date("l");?>
                  <div class="location">
                    <?php echo esc_attr($address_store );?> 

                      <?php if($curnt_date == "Monday"){ ?>
                      <?php if($select_Monday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Monday);?>-<?php echo ($close_Monday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif ($curnt_date == "Tuesday") { ?>
                      <?php if($select_Tuesday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Tuesday);?>-<?php echo ($close_Tuesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Wednesday"){ ?>
                      <?php if($select_Wednesday != "close"){ ?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Wednesday);?>-<?php echo ($close_Wednesday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Thursday"){?>
                      <?php if($select_Thursday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Thursday);?>-<?php echo ($close_Thursday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Friday"){?>
                      <?php if($select_Friday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Friday);?>-<?php echo ($close_Friday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Saturday"){?>
                      <?php if($select_Saturday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Saturday);?>-<?php echo ($close_Saturday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }} elseif($curnt_date == "Sunday"){?>
                      <?php if($select_Sunday != "close"){?>
                      <span class="opening"><?php echo esc_html__( 'Open from', 'quickfood' );?> <?php echo ($open_Sunday);?>-<?php echo ($close_Sunday);?></span>
                      <?php }else{?>
                      <span class="label label-danger">Closed</span>
                      <?php }}?>  

                  </div>
                  <ul>
                      <?php if($select_take_away == "yes"){?>
                <li><?php echo esc_html__( 'Take away', 'quickfood' );?><i class="icon_check_alt2 ok"></i></li>
                <?php }else{?>
                <li><?php echo esc_html__( 'Take away', 'quickfood' );?><i class="icon_check_alt2 no"></i></li>
                <?php }?>

                <?php if($select_delivery == "yes"){?>
                <li><?php echo esc_html__( 'Delivery', 'quickfood' );?><i class="icon_check_alt2 ok"></i></li>
                <?php }else{?>
                <li><?php echo esc_html__( 'Delivery', 'quickfood' );?><i class="icon_check_alt2 no"></i></li>
                <?php }?>
                  </ul>
              </div><!-- End desc-->
          </a><!-- End strip_list-->
        
         
      </div><!-- End col-md-6-->
<?php }?>
<?php 



endwhile;endif;wp_reset_postdata();?>
 <?php  return ob_get_clean();
} 


remove_action( 'wp_ajax_nopriv_actionorder_quickfood', 'actionorder_quickfood' );
remove_action( 'wp_ajax_actionorder_quickfood', 'actionorder_quickfood' );
function actionorder_newquickfood() {
  $name_buyer     = $_GET['name_buyer'];
  $buyer_emails    = $_GET['buyer_email'];
  $admin_emails    = get_option('admin_email');
  $client_emails = 'doordropdeliveries@gmail.com';
  $price     = $_GET['price'];
  $detailfood     = $_GET['detailfood']; 
  $detailfood_array = explode(';',$detailfood);
  $detailfood1 = preg_replace('/(GBP)(\d*\.\d+)/', '', $detailfood);
  $detailfood1_array = explode(';',$detailfood1);
  $currency     = $_GET['currency']; 
  if($currency =='GBP'){ $current='&pound;'; }
  $item_number     = $_GET['item_number'];
  $note     = $_GET['note']; 
  $restaurantID = $_GET['restaurantID'];
  //$userinfo    = $data['userinfo'];  
  //$orderid = $data['orderid'];
  $addressVal = $_GET['addressVal'];
  $cityVal = $_GET['cityVal'];
  $pCodeVal = $_GET['pCodeVal']; 
  $bimgurl = get_stylesheet_directory_uri(); 
  $telCodeVal = $_GET['telCodeVal'];
  $deliveryfee = $_GET['deliveryfee']; 

  global $wpdb;

  $wpdb->query('CREATE TABLE IF NOT EXISTS `quickfood_order` (
            `ids` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `order_ids` TEXT NOT NULL,
            `name_buyers` TEXT NOT NULL,
            `email_buyer` TEXT NOT NULL,
            `detailfoods` TEXT NOT NULL,
            `prices` TEXT NOT NULL,
            `currencys` TEXT NOT NULL,
            `notes` TEXT NOT NULL,
            `createds` INT( 4 ) UNSIGNED NOT NULL,
            `restaurant_id` BIGINT UNSIGNED NOT NULL,
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;');

$price_plan_information = array(
    'order_ids' => $item_number,
    'name_buyers' => $name_buyer,
    'email_buyer' => $buyer_emails,
    'detailfoods'  =>$detailfood,
    'prices'       =>$price,
    'currencys'    => $currency,
    'notes'    => $note,
    'createds'     => time(), 
    'restaurant_id' => $restaurantID,
    );

  $insert_format = array('%s','%s', '%s', '%s', '%s', '%s', '%s','%s','%s');
  $wpdb->insert('quickfood_order', $price_plan_information, $insert_format);

  //emails sent to customer and delivery
  $body_email =  '<table cellspacing="0" cellpadding="0" border="0" width="98%" style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
        <tr>
            <td align="center" valign="top">
            <table cellspacing="0" cellpadding="0" border="0" width="550" height="594" style="background: #fff;">
                <tr>
                    <td background="'.$bimgurl.'/images/main-bg.jpg">                            
                    <table width="90%" height="auto" align="center" cellpadding="0" cell-sapcing="0" style="margin-top: 90px;">
                        <tr>';
  $body_email .= '<td>
                            <h1 style="color: #c18205;font-size: 26px; text-align: center;">Order ID: '.$item_number.'</h1>
                            <p style="font-size: 20px; line-height: 18px; color:#252525; text-align: center;">Buyer Information<br></p>
                            <p style="font-size: 22px; line-height: 18px; color:#2e2b2b; text-align: center;">Name: '.$name_buyer.'<br></p>
                            <p style="font-size: 14px; line-height: 18px; color:#2e2b2b; text-align: center;">Order detail: <br/>'; 
			    foreach( $detailfood_array as $food ){  $body_email .= $food.'<br/>';  }
  $body_email .= '<br/></p> 
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Delivery Fee : '.$deliveryfee.'<br></p>     
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Total Price: '.$current.' ' .$price.'<br></p>   
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Note: '.$note.'<br></p>    
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Address Details: <br/>'.
				$addressVal .', '. $cityVal .', '. $pCodeVal .'</p>  
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Phone No.: '.$telCodeVal.'<br></p>               
                            </td>';
  $body_email .= '</tr>
                        <tr><td>&nbsp;</td></tr>                                                                                             
                        <tr><td style="line-height: 38px !important; padding: 26px 0 0 0;"></td></tr>
                    </table>                            
                    </td>                        
                </tr>                   
            </table>
            </td>
        </tr>
    </table>';

  $other_body_email =  '<table cellspacing="0" cellpadding="0" border="0" width="98%" style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
        <tr>
            <td align="center" valign="top">
            <table cellspacing="0" cellpadding="0" border="0" width="550" height="594" style="background: #fff;">
                <tr>
                    <td background="'.$bimgurl.'/images/main-bg.jpg">                            
                    <table width="90%" height="auto" align="center" cellpadding="0" cell-sapcing="0" style="margin-top: 90px;">
                        <tr>';
  $other_body_email .= '<td>
                            <h1 style="color: #c18205;font-size: 26px; text-align: center;">Order ID: '.$item_number.'</h1>
                            <p style="font-size: 20px; line-height: 18px; color:#252525; text-align: center;">Buyer Information<br></p>
                            <p style="font-size: 22px; line-height: 18px; color:#2e2b2b; text-align: center;">Name: '.$name_buyer.'<br></p>
                            
			    <p style="font-size: 14px; line-height: 18px; color:#2e2b2b; text-align: center;">Order detail: <br/>';
			    foreach( $detailfood1_array as $food ){  $other_body_email .=  $food.'<br/>';  }
   $other_body_email .= '<br></p>    
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Delivery Address: '.
				$addressVal .', '. $cityVal .', '. $pCodeVal .'</p> 
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Phone No.: '.$telCodeVal.'<br/></p>                                         
                            </td>';
  $other_body_email .= '</tr>
                        <tr><td>&nbsp;</td></tr>                                                                                             
                        <tr><td style="line-height: 38px !important; padding: 26px 0 0 0;"></td></tr>
                    </table>                            
                    </td>                        
                </tr>                   
            </table>
            </td>
        </tr>
    </table>';
          
  $multiple_to_recipients = array($buyer_emails,$admin_emails,$client_emails);   
  $subject = __('DoordropDeliveries - Order details', 'quickfood');
  $body    = $body_email;
  $headers = __('From DoordropDeliveries', 'quickfood') . "\r\n";
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 

  $delivery_recipients = array($admin_emails,$client_emails);   
  $delivery_subject = __('DoordropDeliveries - Delivery details', 'quickfood');
  $delivery_body    = $other_body_email;
                              
  wp_mail($multiple_to_recipients, $subject, $body, $headers);
  wp_mail($delivery_recipients, $delivery_subject, $delivery_body, $headers);
  

  //echo 'true';
  die(); // stop executing script
}
// add_action( 'wp_ajax_action_quickfood', 'action_quickfood' ); // ajax for logged in users
// add_action( 'wp_ajax_nopriv_action_quickfood', 'action_quickfood' ); 
add_action( 'wp_ajax_nopriv_actionorder_newquickfood', 'actionorder_newquickfood' );
add_action( 'wp_ajax_actionorder_newquickfood', 'actionorder_newquickfood' );

// insert payments
remove_action( 'wp_ajax_nopriv_action_quickfood', 'action_quickfood' );
remove_action( 'wp_ajax_action_quickfood', 'action_quickfood' );
function action_newquickfood() {
  $name_buyer     = $_GET['name_buyer'];
  $buyer_emails     = $_GET['buyer_email']; 
  $client_emails = 'doordropdeliveries@gmail.com';
  $currency     = $_GET['currency'];
  if($currency =='GBP'){ $current='&pound;'; }
  $price     = $_GET['price'];
  $detailfood     = $_GET['detailfood'];
  $detailfood_array = explode(';',$detailfood);
  //$detailfood1     = preg_replace('/(GBP)/', $current, $detailfood);
  //$detailfood1_array = explode(';',$detailfood1);
  $detailfood2 = preg_replace('/(GBP)(\d*\.\d+)/', '', $detailfood);
  $detailfood2_array = explode(';',$detailfood2);
  $item_number     = $_GET['item_number'];
  //$userinfo    = $data['userinfo'];  
  //$orderid = $data['orderid']; 
  $restaurantID = $_GET['restaurantID'];
  $payment_type = $_GET['payment_type'];
  $admin_emails = get_option('admin_email');
  $addressVal = $_GET['addressVal'];
  $cityVal = $_GET['cityVal'];
  $pCodeVal = $_GET['pCodeVal']; 
  $bimgurl = get_stylesheet_directory_uri(); 
  $telCodeVal = $_GET['telCodeVal'];
  $deliveryfee = $_GET['deliveryfee']; 

  global $wpdb;

  $wpdb->query('CREATE TABLE IF NOT EXISTS `quickfood_payments` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `order_id` TEXT NOT NULL,
            `transaction_id` TEXT NOT NULL,
            `name_buyer` TEXT NOT NULL,
            `buyer_email` TEXT NOT NULL,
            `detailfood` TEXT NOT NULL,
            `price` TEXT NOT NULL,
            `currency` TEXT NOT NULL,
            `status` TEXT NOT NULL, 
	    `payment_type` TEXT NOT NULL,
            `created` INT( 4 ) UNSIGNED NOT NULL,
	    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;');

$price_plan_information = array(
    'order_id' => $item_number,
    'transaction_id' => '',
    'name_buyer' => $name_buyer,
    'buyer_email' => $buyer_emails,
    'price'       =>$price,
    'detailfood'  =>$detailfood,
    'currency'    => $currency,
    'status'      => 'Pending',
    'payment_type' => $payment_type,
    'created'     => time(),
    'restaurant_id' => $restaurantID,
    );

  $insert_format = array('%s','%s','%s', '%s', '%s', '%s', '%s', '%s', '%s','%s','%s');
  $wpdb->insert('quickfood_payments', $price_plan_information, $insert_format); 

  //emails sent to customer and delivery
  $body_email =  '<table cellspacing="0" cellpadding="0" border="0" width="98%" style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
        <tr>
            <td align="center" valign="top">
            <table cellspacing="0" cellpadding="0" border="0" width="550" height="594" style="background: #fff;">
                <tr>
                    <td background="'.$bimgurl.'/images/main-bg.jpg">                            
                    <table width="90%" height="auto" align="center" cellpadding="0" cell-sapcing="0" style="margin-top: 90px;">
                        <tr>';
  $body_email .= '<td>
                            <h1 style="color: #c18205;font-size: 26px; text-align: center;">Order ID: '.$item_number.'</h1>
                            <p style="font-size: 20px; line-height: 18px; color:#252525; text-align: center;">Buyer Information<br></p>
                            <p style="font-size: 22px; line-height: 18px; color:#2e2b2b; text-align: center;">Name: '.$name_buyer.'<br/></p>
                            <p style="font-size: 14px; line-height: 18px; color:#2e2b2b; text-align: center;">Order detail: <br/>'; 
			    foreach( $detailfood_array as $food ){  $body_email .= $food.'<br/>';  }
  $body_email .= '</p> 
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Delivery Fee : '.$deliveryfee.'<br/></p>   
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Total Price: '.$current .' '.$price.'<br/></p>  
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Note: '.$note.'<br/></p>    
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Address Details: <br/>'.
				$addressVal .', '. $cityVal .', '. $pCodeVal .'</p>  
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Phone No.: '.$telCodeVal.'<br/></p>               
                            </td>';
  $body_email .= '</tr>
                        <tr><td>&nbsp;</td></tr>                                                                                             
                        <tr><td style="line-height: 38px !important; padding: 26px 0 0 0;"></td></tr>
                    </table>                            
                    </td>                        
                </tr>                   
            </table>
            </td>
        </tr>
    </table>';

  $other_body_email =  '<table cellspacing="0" cellpadding="0" border="0" width="98%" style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
        <tr>
            <td align="center" valign="top">
            <table cellspacing="0" cellpadding="0" border="0" width="550" height="594" style="background: #fff;">
                <tr>
                    <td background="'.$bimgurl.'/images/main-bg.jpg">                            
                    <table width="90%" height="auto" align="center" cellpadding="0" cell-sapcing="0" style="margin-top: 90px;">
                        <tr>';
  $other_body_email .= '<td>
                            <h1 style="color: #c18205;font-size: 26px; text-align: center;">Order ID: '.$item_number.'</h1>
                            <p style="font-size: 20px; line-height: 18px; color:#252525; text-align: center;">Buyer Information<br></p>
                            <p style="font-size: 22px; line-height: 18px; color:#2e2b2b; text-align: center;">Name: '.$name_buyer.'<br></p>
                            
			    <p style="font-size: 14px; line-height: 18px; color:#2e2b2b; text-align: center;">Order detail: <br/>';
			    foreach( $detailfood2_array as $food ){  $other_body_email .=  $food.'<br/>';  }
   $other_body_email .= '</p>    
                            <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Delivery Address: '.
				$addressVal .', '. $cityVal .', '. $pCodeVal .'</p> 
			    <p style="font-size: 12px; line-height: 18px; color:#2e2b2b; text-align: center;">Phone No.: '.$telCodeVal.'<br/></p>                                         
                            </td>';
  $other_body_email .= '</tr>
                        <tr><td>&nbsp;</td></tr>                                                                                             
                        <tr><td style="line-height: 38px !important; padding: 26px 0 0 0;"></td></tr>
                    </table>                            
                    </td>                        
                </tr>                   
            </table>
            </td>
        </tr>
    </table>';
          
  $multiple_to_recipients = array($buyer_emails,$admin_emails,$client_emails);      
  $subject = __('DoordropDeliveries - Order details', 'quickfood');
  $body    = $body_email;
  $headers = __('From DoordropDeliveries', 'quickfood') . "\r\n";
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";  
                     
  $delivery_recipients = array($admin_emails,$client_emails);   
  $delivery_subject = __('DoordropDeliveries - Delivery details', 'quickfood');
  $delivery_body    = $other_body_email;
                              
  wp_mail($multiple_to_recipients, $subject, $body, $headers);
  wp_mail($delivery_recipients, $delivery_subject, $delivery_body, $headers);

  //echo 'true';
  die(); // stop executing script
}
add_action( 'wp_ajax_nopriv_action_newquickfood', 'action_newquickfood' );
add_action( 'wp_ajax_action_newquickfood', 'action_newquickfood' );


// Ajax Search
remove_action( 'wp_ajax_nopriv_ajax_filter', 'ajax_filter' );
remove_action( 'wp_ajax_ajax_filter', 'ajax_filter' );
function ajax_newfilter() { 
    $theme_option = get_option('theme_option');
    $type = (isset($_GET['types'])) ? $_GET['types'] : 0; 
    $options = (isset($_GET['option'])) ? $_GET['option'] : 0; 
    $irs_from = (isset($_GET['irs_from'])) ? $_GET['irs_from'] : 0; 
    $irs_to = (isset($_GET['irs_to'])) ? $_GET['irs_to'] : 0; 
    $irs_froms = substr($irs_from,3) ;
    $irs_tos = substr($irs_to,3) ;
    $vote = (isset($_GET['votes'])) ? $_GET['votes'] : 0;  
    $x = (explode(',', $type));
    $y = (explode(',', $options)); 


    if( !empty($type) && !empty($options) ){
                $array = array(
        'post_type' => 'restaurant',    
	'order' => 'asc',
        'posts_per_page' => -1,
        'order_by' => 'title',
        'tax_query' => array(
            'relation' => 'AND',
                array('taxonomy' =>'type',
                    'field' => 'slug',
                'terms' => $x,
                ),
                array('taxonomy' =>'option',
                'terms' => $y,
                'field' => 'slug',
                 'operator' => 'IN',
                ),
                
            )   
        );
    }elseif( !empty($type) && empty($options)){
                    $array = array(
        'post_type' => 'restaurant',
        'posts_per_page' => -1,
        'order' => 'asc',
        'order_by' => 'title',
        'tax_query' => array(
                array('taxonomy' =>'type',
                    'field' => 'slug',
                'terms' => $x,
                ),
                
            )   
        );
     }elseif( !empty($options) && empty($type) ){
                    $array = array(
        'post_type' => 'restaurant',
        'posts_per_page' => -1,
        'order' => 'asc',
        'order_by' => 'title',
        'tax_query' => array(
                array('taxonomy' =>'option',
                    'field' => 'slug',
                'terms' => $y,
                ),
                
            )   
        );
     }else{
                    $array = array(
        'post_type' => 'restaurant',    'order' => 'asc',
        'posts_per_page' => -1,
        'order_by' => 'title',
        
        );
    }
    $the_query = new WP_Query( $array ); 

    if($the_query -> have_posts()) : while ( $the_query -> have_posts() ) : $the_query -> the_post(); 

    $area_store = get_post_meta(get_the_ID(), '_cmb2_area_store', true);
                    $address_store = get_post_meta(get_the_ID(), '_cmb2_address_store', true);
                    $popular_food = get_post_meta(get_the_ID(), '_cmb2_popular_food', true);
                    $logo_store = get_post_meta(get_the_ID(), '_cmb2_logo_store', true);
                    $logo_img = array( 'width' => 110 , 'height' => 110 );
                    $img_logo = bfi_thumb( $logo_store, $logo_img );
                    $select_take_away = get_post_meta(get_the_ID(), '_cmb2_select_take_away', true);
                    $select_delivery = get_post_meta(get_the_ID(), '_cmb2_select_delivery', true);
                    $menu_store = get_post_meta(get_the_ID(),'_cmb2_menu_store', true );
                    $minimum_order = get_post_meta(get_the_ID(),'_cmb2_minimum_order', true );
                    $open_Monday = get_post_meta(get_the_ID(), '_cmb2_open_Monday', true);
                    $delivery_radius = get_post_meta(get_the_ID(), '_cmb2_delivery_radius', true); 
		    if($delivery_radius == ""){ $delivery_radius = 0; }  
                ?>

                <?php if ((int)$delivery_radius >= (int)$irs_froms && (int)$delivery_radius < (int)$irs_tos) { ?>
     <?php    $entries = get_post_meta( get_the_ID(),'_cmb2_reviews_group', true );
                            if(!empty($entries)):
                               
                                  $number_reviews = 0;
                                  $sum1 = 0;
                                  $sum2 = 0;
                                  $sum3 = 0;
                                  $sum4 = 0;
                               $tes=0;
                               $tes2=0;
                               $tes3=0;
                               $tes4=0;
                              foreach ( $entries as $entry ) {


                                    $email_review = $comment_cus = $food_quality = $price_quality = $punctuality_quality = $courtesy_quality = '';

                                    if ( isset( $entry['_cmb2_email_review'] ) )
                                        $email_review = esc_html( $entry['_cmb2_email_review'] );
                                    if ( isset( $entry['_cmb2_name_user'] ) )
                                        $name_user = esc_html( $entry['_cmb2_name_user'] );

                                    if ( isset( $entry['_cmb2_comment_cus'] ) )
                                        $comment_cus = wpautop( $entry['_cmb2_comment_cus'] );

                                    if ( isset( $entry['_cmb2_food_quality'] ) )
                                        $food_quality = esc_html( $entry['_cmb2_food_quality'] );

                                    if ( isset( $entry['_cmb2_price_quality'] ) )
                                        $price_quality = esc_html( $entry['_cmb2_price_quality'] );

                                    if ( isset( $entry['_cmb2_punctuality_quality'] ) )
                                        $punctuality_quality = esc_html( $entry['_cmb2_punctuality_quality'] );

                                    if ( isset( $entry['_cmb2_courtesy_quality'] ) )
                                        $courtesy_quality = esc_html( $entry['_cmb2_courtesy_quality'] ); ?>

                             
                                    <?php if($food_quality == 'sufficient'){
                                    $a= 2;
                                    }elseif($food_quality == 'good'){
                                        $a=3;
                                    }elseif($food_quality == 'excellent'){
                                        $a=4;
                                    }elseif($food_quality == 'super'){
                                        $a=5;
                                    }elseif($food_quality == 'low'){
                                        $a=1;
                                        $tes=0;
                                    }else{
                                        $a=0;
                                        $tes++;
                                    }

                                       if($price_quality == 'sufficient'){
                                    $b= 2;
                                    }elseif($price_quality == 'good'){
                                        $b=3;
                                    }elseif($price_quality == 'excellent'){
                                        $b=4;
                                    }elseif($price_quality == 'super'){

                                     $b=5;
                                     }elseif($price_quality == 'low'){
                                        $b=1;
                                        $tes2=0;
                                     }else{
                                        $b=0;
                                        $tes2++;
                                    }

                                    ?>
                                     <?php if($punctuality_quality == 'sufficient'){
                                    $c= 2;
                                    }elseif($punctuality_quality == 'good'){
                                        $c=3;
                                    }elseif($punctuality_quality == 'excellent'){
                                        $c=4;
                                    }elseif($punctuality_quality == 'super'){

                                     $c=5;
                                     }elseif($punctuality_quality == 'low'){
                                        $c=1;
                                        $tes3=0;
                                     }else{
                                        $c=0;
                                        $tes3++;
                                    }

                                    ?>


                                     <?php if($courtesy_quality == 'sufficient'){
                                    $d= 2;
                                    }elseif($courtesy_quality == 'good'){
                                        $d=3;
                                    }elseif($courtesy_quality == 'excellent'){
                                        $d=4;
                                    }elseif($courtesy_quality == 'super'){

                                     $d=5;
                                     }elseif($courtesy_quality == 'low'){
                                        $d=1;
                                        $tes4=0;
                                     }else{
                                        $d=0;
                                        $tes4++;
                                    }
                                       
                                         
                                    $sum1 = $sum1 + $a;
                                    $sum2 = $sum2 + $b; 
                                    $sum3 = $sum3 + $c; 
                                    $sum4 = $sum4 + $d;   

                                      $number_reviews++;

                                         }   // } <!--endforeach -->
                                    $averagee = $sum1/($number_reviews - $tes);
                                    $averagee2 = $sum2/($number_reviews - $tes2);
                                    $averagee3 = $sum3/($number_reviews - $tes3);
                                    $averagee4 = $sum4/($number_reviews - $tes4);
                                    if($averagee >= 0 && $averagee < 1.5){
                                    $average11 = 1;
                                     
                                         }elseif($averagee>= 1.5 && $averagee < 2.5){
                                                $average11 = 2;
                                    
                                        }elseif($averagee >= 2.5 && $averagee < 3.5){

                                            $average11 = 3;
                                         }elseif($averagee >= 3.5 && $averagee <= 4){

                                        $average11 = 4;
                                       }elseif($averagee > 4 && $averagee <= 5){
                                            $average11 = 5;
                                            }else{
                                           $average11 = 0;
                                         }

                                         if($averagee2>= 0 && $averagee2 < 1.5){
                                    $average22 = 1;
                                     
                                         }elseif($averagee2>= 1.5 && $averagee2 < 2.5){
                                                $average22 = 2;
                                    
                                        }elseif($averagee2 >= 2.5 && $averagee2 < 3.5){

                                            $average22 = 3;
                                         }elseif($averagee2 >= 3.5 && $averagee2 <= 4){

                                        $average22 = 4;
                                       }elseif($averagee2 > 4 && $averagee2 <= 5){
                                            $average22 = 5;
                                            }else{
                                           $average22 = 0;
                                         }

                                            if($averagee3>= 0 && $averagee3 < 1.5){
                                          $average33 = 1;
                                     
                                         }elseif($averagee3>= 1.5 && $averagee3 < 2.5){
                                                $average33 = 2;
                                    
                                        }elseif($averagee3 >= 2.5 && $averagee3 < 3.5){

                                            $average33 = 3;
                                         }elseif($averagee3 >= 3.5 && $averagee3 <= 4){

                                        $average33 = 4;
                                       }elseif($averagee3 > 4 && $averagee3 <= 5){
                                            $average33 = 5;
                                            }else{
                                           $average33 = 0;
                                         }

                                      if($averagee4>= 0 && $averagee4 < 1.5){
                                     $average44 = 1;
                                     
                                         }elseif($averagee4 >= 1.5 && $averagee4 < 2.5){
                                                $average44 = 2;
                                    
                                        }elseif($averagee4 >= 2.5 && $averagee4 < 3.5){

                                            $average44 = 3;
                                         }elseif($averagee4 >= 3.5 && $averagee4 <= 4){

                                        $average44 = 4;
                                       }elseif($averagee4 > 4 && $averagee4 <= 5){
                                            $average44 = 5;
                                            }else{
                                           $average44 = 0;
                                         }
                          
                            
                                     $averages = ($average11 + $average22 + $average33 + $average44)/4;
                                     endif;
                                         $fl = round($averages);
                                        ?>

    <?php 
    $arr = (string)$fl;
    ?>
    <?php if(!empty($vote)){?>
    <?php if(strpos( $vote , $arr) !== false){?>
                <div class="strip_list wow fadeIn" data-wow-delay="0.1s">
                    <?php if(isset($popular_food) && ($popular_food == "show_popular")){?>
                    <div class="ribbon_1">
                        <?php echo esc_html__('Popular','quickfood'); ?>
                    </div>
                    <?php }else{}?>
                    <div class="row">
                        <div class="col-md-9 col-sm-9">
                            <div class="desc">
                                <div class="thumb_strip">
                                    <a href="<?php the_permalink();?>"><img src="<?php echo esc_url($img_logo);?>" alt="<?php the_title();?>"></a>
                                </div>
                                <div class="rating">
                                    <?php
                                        for( $i=1 ; $i<=$fl; $i++){
                                    ?>
                                                <i class="icon_star voted"></i>
                                                <?php }?>
                                            <?php
                                        for( $j=1 ; $j<= 5-$fl; $j++){
                                    ?>
                                                <i class="icon_star "></i>
                                                <?php }?>
                                </div>
                                <h3><?php the_title();?></h3>
                                <div class="type">
                                    <?php echo esc_attr($area_store );?>
                                </div>
                                <div class="location">
                                    <?php echo esc_attr($address_store );?> <span class="opening"><?php echo esc_html__('Opens at','quickfood'); ?> <?php echo esc_attr($open_Monday);?>.</span> <?php echo esc_html__('Minimum order:','quickfood'); ?> <?php if(!empty($minimum_order)) {?>$<?php echo esc_attr($minimum_order);?><?php }else{}?>
                                </div>
                                <ul>
                                    
                                    <?php if($select_take_away == "yes"){?>
                                    <li><?php echo esc_html__('Take away','quickfood'); ?><i class="icon_check_alt2 ok"></i></li>
                                    <?php }else{?>
                                    <li><?php echo esc_html__('Take away','quickfood'); ?><i class="icon_check_alt2 no"></i></li>
                                    <?php }?>

                                    <?php if($select_delivery == "yes"){?>
                                    <li><?php echo esc_html__('Delivery','quickfood'); ?><i class="icon_check_alt2 ok"></i></li>
                                    <?php }else{?>
                                    <li><?php echo esc_html__('Delivery','quickfood'); ?><i class="icon_check_alt2 no"></i></li>
                                    <?php }?>
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="go_to">
                                <div>
                                    <?php $arr = array(
                                        'post_type' => 'menu',
                                        'p'        => $menu_store,
                                    );
                                        $query_menu = new WP_Query($arr);
                                        if($query_menu-> have_posts()) : $query_menu->the_post();
                                     ?>

                                    <a href="<?php the_permalink();?>" class="btn_1"><?php echo esc_html__('View Menu','quickfood'); ?></a>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- End row-->
                </div><!-- End strip_list-->
                <?php }else{/*echo 'No result';*/}?>
    <?php }elseif(empty($vote)){?>
        <div class="strip_list wow fadeIn" data-wow-delay="0.1s">
                    <?php if(isset($popular_food) && ($popular_food == "show_popular")){?>
                    <div class="ribbon_1">
                        <?php echo esc_html__('Popular','quickfood'); ?>
                    </div>
                    <?php }else{}?>
                    <div class="row">
                        <div class="col-md-9 col-sm-9">
                            <div class="desc">
                                <div class="thumb_strip">
                                    <a href="<?php the_permalink();?>"><img src="<?php echo esc_url($img_logo);?>" alt="<?php the_title();?>"></a>
                                </div>
                                <div class="rating">
                                    <?php
                                        for( $i=1 ; $i<=$fl; $i++){
                                    ?>
                                                <i class="icon_star voted"></i>
                                                <?php }?>
                                            <?php
                                        for( $j=1 ; $j<= 5-$fl; $j++){
                                    ?>
                                                <i class="icon_star "></i>
                                                <?php }?>
                                </div>
                                <h3><?php the_title();?></h3>
                                <div class="type">
                                    <?php echo esc_attr($area_store );?>
                                </div>
                                <div class="location">
                                    <?php echo esc_attr($address_store );?> <span class="opening"><?php echo esc_html__('Opens at','quickfood'); ?> <?php echo esc_attr($open_Monday);?>.</span> <?php echo esc_html__('Minimum order:','quickfood'); ?> <?php if(!empty($minimum_order)) {?>$<?php echo esc_attr($minimum_order);?><?php }else{}?>
                                </div>
                                <ul>
                                    
                                    <?php if($select_take_away == "yes"){?>
                                    <li><?php echo esc_html__('Take away','quickfood'); ?><i class="icon_check_alt2 ok"></i></li>
                                    <?php }else{?>
                                    <li><?php echo esc_html__('Take away','quickfood'); ?><i class="icon_check_alt2 no"></i></li>
                                    <?php }?>

                                    <?php if($select_delivery == "yes"){?>
                                    <li><?php echo esc_html__('Delivery','quickfood'); ?><i class="icon_check_alt2 ok"></i></li>
                                    <?php }else{?>
                                    <li><?php echo esc_html__('Delivery','quickfood'); ?><i class="icon_check_alt2 no"></i></li>
                                    <?php }?>
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="go_to">
                                <div>
                                    <?php $arr = array(
                                        'post_type' => 'menu',
                                        'p'        => $menu_store,
                                    );
                                        $query_menu = new WP_Query($arr);
                                        if($query_menu-> have_posts()) : $query_menu->the_post();
                                     ?>

                                    <a href="<?php the_permalink();?>" class="btn_1"><?php echo esc_html__('View Menu','quickfood'); ?></a>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- End row-->
                </div><!-- End strip_list-->
    <?php }?>
                <?php }else{} //end delivery radius ?>                    

    <?php endwhile; endif;
 // IMPORTANT: don't forget to "exit"
 exit();
}

add_action( 'wp_ajax_nopriv_ajax_newfilter', 'ajax_newfilter' );
add_action( 'wp_ajax_ajax_newfilter', 'ajax_newfilter' );


//grid filtering
remove_action( 'wp_ajax_nopriv_ajax_filter_grid', 'ajax_filter_grid' );
remove_action( 'wp_ajax_ajax_filter_grid', 'ajax_filter_grid' );
function ajax_newfilter_grid() { 
    $theme_option = get_option('theme_option');
    $type = (isset($_GET['types'])) ? $_GET['types'] : 0; 
    $options = (isset($_GET['option'])) ? $_GET['option'] : 0;
    $vote = (isset($_GET['votes'])) ? $_GET['votes'] : 0;  
    $irs_from = (isset($_GET['irs_from'])) ? $_GET['irs_from'] : 0; 
    $irs_to = (isset($_GET['irs_to'])) ? $_GET['irs_to'] : 0; 
    $irs_froms = substr($irs_from,3) ;
    $irs_tos = substr($irs_to,3) ;
    $vote = (isset($_GET['votes'])) ? $_GET['votes'] : 0;
    $x = (explode(',', $type));
    $y = (explode(',', $options));
    $z = (explode(',', $vote));

    if( !empty($type) && !empty($options) ){
                $array = array(
        'post_type' => 'restaurant',    'order' => 'asc',
        'posts_per_page' => -1,
        'order_by' => 'title',
        'tax_query' => array(
            'relation' => 'AND',
                array('taxonomy' =>'type',
                    'field' => 'slug',
                'terms' => $x,
                ),
                array('taxonomy' =>'option',
                'terms' => $y,
                'field' => 'slug',
                 'operator' => 'IN',
                ),
                
            )   
        );
            }elseif( !empty($type) && empty($options)){
                    $array = array(
        'post_type' => 'restaurant',
        'posts_per_page' => -1,
        'order' => 'asc',
        'order_by' => 'title',
        'tax_query' => array(
                array('taxonomy' =>'type',
                    'field' => 'slug',
                'terms' => $x,
                ),
                
            )   
        );
            }elseif( !empty($options) && empty($type) ){
                    $array = array(
        'post_type' => 'restaurant',
        'order' => 'asc',
        'posts_per_page' => -1,
        'order_by' => 'title',
        'tax_query' => array(
                array('taxonomy' =>'option',
                    'field' => 'slug',
                'terms' => $y,
                ),
                
            )   
        );
            }else{
                    $array = array(
        'post_type' => 'restaurant',    'order' => 'asc',
        'posts_per_page' => -1,
        'order_by' => 'title',
        
        );
            }
    $the_query = new WP_Query( $array );

            if($the_query -> have_posts()) : while ( $the_query -> have_posts() ) : $the_query -> the_post();
                $area_store = get_post_meta(get_the_ID(), '_cmb2_area_store', true);
                    $address_store = get_post_meta(get_the_ID(), '_cmb2_address_store', true);
                    $popular_food = get_post_meta(get_the_ID(), '_cmb2_popular_food', true);
                    $logo_store = get_post_meta(get_the_ID(), '_cmb2_logo_store', true);
                    $logo_img = array( 'width' => 110 , 'height' => 110 );
                    $img_logo = bfi_thumb( $logo_store, $logo_img );
                    $select_take_away = get_post_meta(get_the_ID(), '_cmb2_select_take_away', true);
                    $select_delivery = get_post_meta(get_the_ID(), '_cmb2_select_delivery', true);
                    $menu_store = get_post_meta(get_the_ID(),'_cmb2_menu_store', true );
                    $minimum_order = get_post_meta(get_the_ID(),'_cmb2_minimum_order', true );
                    $open_Monday = get_post_meta(get_the_ID(), '_cmb2_open_Monday', true);
                    $delivery_radius = get_post_meta(get_the_ID(), '_cmb2_delivery_radius', true);
                     if($delivery_radius == ""){ $delivery_radius = 0; } 
                ?>
    <?php if ((int)$delivery_radius >= (int)$irs_froms && (int)$delivery_radius < (int)$irs_tos) { ?>


     <?php    $entries = get_post_meta( get_the_ID(),'_cmb2_reviews_group', true );
                            if(!empty($entries)):
                               
                                  $number_reviews = 0;
                                  $sum1 = 0;
                                  $sum2 = 0;
                                  $sum3 = 0;
                                  $sum4 = 0;
                               $tes=0;
                               $tes2=0;
                               $tes3=0;
                               $tes4=0;
                              foreach ( $entries as $entry ) {


                                    $email_review = $comment_cus = $food_quality = $price_quality = $punctuality_quality = $courtesy_quality = '';

                                    if ( isset( $entry['_cmb2_email_review'] ) )
                                        $email_review = esc_html( $entry['_cmb2_email_review'] );
                                    if ( isset( $entry['_cmb2_name_user'] ) )
                                        $name_user = esc_html( $entry['_cmb2_name_user'] );

                                    if ( isset( $entry['_cmb2_comment_cus'] ) )
                                        $comment_cus = wpautop( $entry['_cmb2_comment_cus'] );

                                    if ( isset( $entry['_cmb2_food_quality'] ) )
                                        $food_quality = esc_html( $entry['_cmb2_food_quality'] );

                                    if ( isset( $entry['_cmb2_price_quality'] ) )
                                        $price_quality = esc_html( $entry['_cmb2_price_quality'] );

                                    if ( isset( $entry['_cmb2_punctuality_quality'] ) )
                                        $punctuality_quality = esc_html( $entry['_cmb2_punctuality_quality'] );

                                    if ( isset( $entry['_cmb2_courtesy_quality'] ) )
                                        $courtesy_quality = esc_html( $entry['_cmb2_courtesy_quality'] ); ?>

                             
                                    <?php if($food_quality == 'sufficient'){
                                    $a= 2;
                                    }elseif($food_quality == 'good'){
                                        $a=3;
                                    }elseif($food_quality == 'excellent'){
                                        $a=4;
                                    }elseif($food_quality == 'super'){
                                        $a=5;
                                    }elseif($food_quality == 'low'){
                                        $a=1;
                                        $tes=0;
                                    }else{
                                        $a=0;
                                        $tes++;
                                    }

                                       if($price_quality == 'sufficient'){
                                    $b= 2;
                                    }elseif($price_quality == 'good'){
                                        $b=3;
                                    }elseif($price_quality == 'excellent'){
                                        $b=4;
                                    }elseif($price_quality == 'super'){

                                     $b=5;
                                     }elseif($price_quality == 'low'){
                                        $b=1;
                                        $tes2=0;
                                     }else{
                                        $b=0;
                                        $tes2++;
                                    }

                                    ?>
                                     <?php if($punctuality_quality == 'sufficient'){
                                    $c= 2;
                                    }elseif($punctuality_quality == 'good'){
                                        $c=3;
                                    }elseif($punctuality_quality == 'excellent'){
                                        $c=4;
                                    }elseif($punctuality_quality == 'super'){

                                     $c=5;
                                     }elseif($punctuality_quality == 'low'){
                                        $c=1;
                                        $tes3=0;
                                     }else{
                                        $c=0;
                                        $tes3++;
                                    }

                                    ?>


                                     <?php if($courtesy_quality == 'sufficient'){
                                    $d= 2;
                                    }elseif($courtesy_quality == 'good'){
                                        $d=3;
                                    }elseif($courtesy_quality == 'excellent'){
                                        $d=4;
                                    }elseif($courtesy_quality == 'super'){

                                     $d=5;
                                     }elseif($courtesy_quality == 'low'){
                                        $d=1;
                                        $tes4=0;
                                     }else{
                                        $d=0;
                                        $tes4++;
                                    }
                                       
                                         
                                    $sum1 = $sum1 + $a;
                                    $sum2 = $sum2 + $b; 
                                    $sum3 = $sum3 + $c; 
                                    $sum4 = $sum4 + $d;   

                                      $number_reviews++;

                                         }   // } <!--endforeach -->
                                    $averagee = $sum1/($number_reviews - $tes);
                                    $averagee2 = $sum2/($number_reviews - $tes2);
                                    $averagee3 = $sum3/($number_reviews - $tes3);
                                    $averagee4 = $sum4/($number_reviews - $tes4);
                                    if($averagee >= 0 && $averagee < 1.5){
                                    $average11 = 1;
                                     
                                         }elseif($averagee>= 1.5 && $averagee < 2.5){
                                                $average11 = 2;
                                    
                                        }elseif($averagee >= 2.5 && $averagee < 3.5){

                                            $average11 = 3;
                                         }elseif($averagee >= 3.5 && $averagee <= 4){

                                        $average11 = 4;
                                       }elseif($averagee > 4 && $averagee <= 5){
                                            $average11 = 5;
                                            }else{
                                           $average11 = 0;
                                         }

                                         if($averagee2>= 0 && $averagee2 < 1.5){
                                    $average22 = 1;
                                     
                                         }elseif($averagee2>= 1.5 && $averagee2 < 2.5){
                                                $average22 = 2;
                                    
                                        }elseif($averagee2 >= 2.5 && $averagee2 < 3.5){

                                            $average22 = 3;
                                         }elseif($averagee2 >= 3.5 && $averagee2 <= 4){

                                        $average22 = 4;
                                       }elseif($averagee2 > 4 && $averagee2 <= 5){
                                            $average22 = 5;
                                            }else{
                                           $average22 = 0;
                                         }

                                            if($averagee3>= 0 && $averagee3 < 1.5){
                                          $average33 = 1;
                                     
                                         }elseif($averagee3>= 1.5 && $averagee3 < 2.5){
                                                $average33 = 2;
                                    
                                        }elseif($averagee3 >= 2.5 && $averagee3 < 3.5){

                                            $average33 = 3;
                                         }elseif($averagee3 >= 3.5 && $averagee3 <= 4){

                                        $average33 = 4;
                                       }elseif($averagee3 > 4 && $averagee3 <= 5){
                                            $average33 = 5;
                                            }else{
                                           $average33 = 0;
                                         }

                                      if($averagee4>= 0 && $averagee4 < 1.5){
                                     $average44 = 1;
                                     
                                         }elseif($averagee4 >= 1.5 && $averagee4 < 2.5){
                                                $average44 = 2;
                                    
                                        }elseif($averagee4 >= 2.5 && $averagee4 < 3.5){

                                            $average44 = 3;
                                         }elseif($averagee4 >= 3.5 && $averagee4 <= 4){

                                        $average44 = 4;
                                       }elseif($averagee4 > 4 && $averagee4 <= 5){
                                            $average44 = 5;
                                            }else{
                                           $average44 = 0;
                                         }
                          
                            
                                     $averages = ($average11 + $average22 + $average33 + $average44)/4;
       $fl = round($averages);
       endif;
                                        ?>

    <?php 
    $arr = (string)$fl;
    ?>
    <?php if(!empty($vote)){?>
    <?php if(strpos( $vote , $arr) !== false){?>


                <div class="col-md-6 col-sm-6 wow zoomIn" data-wow-delay="0.1s">
                        <a class="strip_list grid" href="<?php the_permalink();?>">
                            <?php if(isset($popular_food) && ($popular_food == "show_popular")){?>
                    <div class="ribbon_1">
                        <?php echo esc_html__('Popular','quickfood'); ?>
                    </div>
                    <?php }else{}?>
                            <div class="desc">
                                <div class="thumb_strip">
                                    <img src="<?php echo esc_url($img_logo);?>" alt="<?php the_title();?>">
                                </div>
                                <div class="rating">
                                    <?php if($averages > 1 && $averages < 1.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 1.5 && $averages < 2.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 2.5 && $averages < 3.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 3.5 && $averages < 4.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i>
                                            <?php }else{?>
                                                <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i>
                                            <?php }?>

                                </div>
                                <h3><?php the_title();?></h3>
                                <div class="type">
                                    <?php echo esc_attr($area_store );?>
                                </div>
                                <div class="location">
                                    <?php echo esc_attr($address_store );?>  <br><span class="opening"><?php echo esc_html__( 'Opens at', 'quickfood' );?> <?php echo esc_attr($open_Monday);?>.</span> <?php echo esc_html__( 'Minimum order:', 'quickfood');?> <?php if(!empty($minimum_order)) {?>$<?php echo esc_attr($minimum_order);?><?php }else{}?>
                                </div>
                                <ul>
                                    <li><?php echo esc_html__( 'Take away', 'quickfood' );?><i class="icon_check_alt2 ok"></i></li>
                                    <li><?php echo esc_html__( 'Delivery', 'quickfood' );?><i class="icon_check_alt2 ok"></i></li>
                                </ul>
                            </div>
                        </a><!-- End strip_list-->
                    </div><!-- End col-md-6-->
                   

                   <?php }else{/*echo 'No result';*/}?>
      <?php }elseif(empty($vote)){?>
    <div class="col-md-6 col-sm-6 wow zoomIn" data-wow-delay="0.1s">
                        <a class="strip_list grid" href="<?php the_permalink();?>">
                            <?php if(isset($popular_food) && ($popular_food == "show_popular")){?>
                    <div class="ribbon_1">
                        <?php echo esc_html__('Popular','quickfood'); ?>
                    </div>
                    <?php }else{}?>
                            <div class="desc">
                                <div class="thumb_strip">
                                    <img src="<?php echo esc_url($img_logo);?>" alt="<?php the_title();?>">
                                </div>
                                <div class="rating">
                                    <?php if($averages > 1 && $averages < 1.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 1.5 && $averages < 2.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 2.5 && $averages < 3.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i><i class="icon_star"></i>
                                            <?php }elseif($averages >= 3.5 && $averages < 4.5){?>

                                            <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star"></i>
                                            <?php }else{?>
                                                <i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i><i class="icon_star voted"></i>
                                            <?php }?>

                                </div>
                                <h3><?php the_title();?></h3>
                                <div class="type">
                                    <?php echo esc_attr($area_store );?>
                                </div>
                                <div class="location">
                                    <?php echo esc_attr($address_store );?>  <br><span class="opening">Opens at <?php echo esc_attr($open_Monday);?>.</span> Minimum order: <?php if(!empty($minimum_order)) {?>$<?php echo esc_attr($minimum_order);?><?php }else{}?>
                                </div>
                                <ul>
                                    <li>Take away<i class="icon_check_alt2 ok"></i></li>
                                    <li>Delivery<i class="icon_check_alt2 ok"></i></li>
                                </ul>
                            </div>
                        </a><!-- End strip_list-->
                    </div><!-- End col-md-6-->
      <?php }?>
                    <?php }else{}?>
                <?php endwhile; endif; wp_reset_postdata();
 // IMPORTANT: don't forget to "exit"
 exit();
} 
add_action( 'wp_ajax_nopriv_ajax_newfilter_grid', 'ajax_newfilter_grid' );
add_action( 'wp_ajax_ajax_newfilter_grid', 'ajax_newfilter_grid' );

/**
 * redirect user to a custom login page on login
 */
function quickfood_login_redirect( $redirect_to, $request, $user ){
  //var_dump($user);
  $login_dashboard = site_url().'/'.'dashboard.php';
  return ( is_array( $user->roles ) && in_array( 'customer', $user->roles ) ) ? $login_dashboard : admin_url();
}
add_filter( 'login_redirect', 'quickfood_login_redirect', 10, 3 );


