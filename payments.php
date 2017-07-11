//--- checkout.php
<?php 
/*
*Template Name: Checkout
*/

get_header();
?>
<?php
$homeurl = get_bloginfo('url');
if(empty($_POST)){ echo '<meta http-equiv="refresh" content="0; url='.$homeurl.'">'; }
// will clear checkout after half-an-hour of wait
echo '<meta http-equiv="refresh" content="1800">';
$ctime = time(); $elapsedTime = $_POST['order_time'] + (30*60); 
if ( $ctime > $elapsedTime ){ '<meta http-equiv="refresh" content="0; url='.$homeurl.'">'; }
else{ echo '<meta http-equiv="refresh" content="0; url='.$homeurl.'">'; }

if(have_posts()) : the_post();
$theme_option = petsworld_option('general');  
if( $_POST['membership_type'] == "petowner"){ $fee = $theme_option['petownerfee']; }
if( $_POST['membership_type'] == "petsitter"){ $fee = $theme_option['petsitterfee']; }
if( $_POST['membership_type'] == "pettrainer"){ $fee = $theme_option['pettrainerfee']; }
if( $_POST['membership_type'] == "petseller"){ $fee = $theme_option['petsellerfee']; }
$gateway_selected = $_POST['payment_gateway'];
if( $_POST['payment_gateway'] == 'paypal' ){ 
   $payment_currency = $theme_option['paypalcurrency'];
   $payment_pp_email = $theme_option['paypalaccountemail'];
   $payment_pp_button = "Proceed To Paypal"; 
   $currency_menu = $payment_currency; 
   $current = $currency_menu ; 
} else {
   define('__ROOT__', dirname(dirname(__FILE__)));
   require_once(__ROOT__.'/petworld/stripe-php-v4.9.1/init.php');    
   $payment_currency = $theme_option['stripecurrency']; 
   $stripetpubkey = $theme_option['general']['stripetestpubkey'];
   $stripetseckey = $theme_option['general']['stripetestseckey'];
   $isCustom = 1; $showAddress = 0; $sendEmailReceipt = 1; $showCustomInput = 0; 
   $showButtonAmount = 1; $showEmailInput = 0;
   $formDoRedirect = ''; $formRedirectPostID == ''; $formRedirectUrl = ''; $formRedirectToPageOrPost= ''; 
   $currency_menu = $payment_currency; $current = $currency_menu ; 
   $stripe_button_title = "Proceed To Stripe"; 
   \Stripe\Stripe::setApiKey($stripetseckey);
}
//$currency_menu = $theme_option['currency_menu'];
//$media_payment =  $theme_option['media_payment']['url'];
//$select_pages_checkout = $theme_option['select_pages_checkout'];
//$select_pages_payment = $theme_option['select_pages_payment'];
$paymentpage = get_bloginfo('url').'/payment/'; $select_pages_payment = $paymentpage;
//$page_susses = $theme_option['page_susses'];
//$logoimg = $homeurl.'/wp-content/uploads/2017/04/FL-30-03-2017-08-09-201.jpg'; ?>
<?php 
if(strstr( $_SERVER['REQUEST_URI'],'membership') || strstr( $_SERVER['REQUEST_URI'],'checkout')){ 
//variables for the membership
$dem = 1; $quantityy[0] = 1; 
$arrpricess[0] = $fee; 
$arrnamess[0] = $_POST['membership_type'].' '.'membership';
$first_name = (isset($_POST['user_name'])) ? $_POST['user_name'] : 0; 
$lastname_order  = (isset($_POST['last_name'])) ? $_POST['last_name'] : 0;
$tel_order  = (isset($_POST['phno'])) ? $_POST['phno'] : 0; 
$email_order = (isset($_POST['user_email'])) ? $_POST['user_email'] : 0; 
$modeofpayment = 'membership'; 
$membership_type = $_POST['membership_type'];

if( $_POST['payment_gateway'] == 'paypal' ){ 
$sub_order = $quantityy[0] * $arrpricess[0]; $total = $quantityy[0] * $arrpricess[0]; }

if( $_POST['payment_gateway'] == 'stripe' ){
$sub_order = $quantityy[0] * $arrpricess[0]*100; $total = $quantityy[0] * $arrpricess[0]*100; }

}
?>
<?php 	
if(strstr( $_SERVER['REQUEST_URI'],'cart')){ 
  $prices = $_POST['priceorder'];
  $names = $_POST['nameorder'];
  $number_quantity = $_POST['number_quantity'];
  $sub_order =  $_POST['total_order'];
  $arrpricess = explode(',' ,$prices);
  $dem = count($arrpricess);
  $arrnamess = explode(',' ,$names);
  $quantityy = explode(',' ,$number_quantity);
  $quantityy = explode(',' ,$number_quantity);

  $first_name = (isset($_POST['firstname_order'])) ? $_POST['firstname_order'] : 0; 
  $lastname_order  = (isset($_POST['lastname_order'])) ? $_POST['lastname_order'] : 0;
  $tel_order  = (isset($_POST['tel_order'])) ? $_POST['tel_order'] : 0; 
  $address_order  = (isset($_POST['address_order'])) ? $_POST['address_order'] : 0; 
  $email_order = (isset($_POST['email_order'])) ? $_POST['email_order'] : 0; 
  $lastname_order = (isset($_POST['lastname_order'])) ? $_POST['lastname_order'] : 0; 
  $city_order = (isset($_POST['city_order'])) ? $_POST['city_order'] : 0;
  $pcode_order = (isset($_POST['pcode_oder'])) ? $_POST['pcode_oder'] : 0;
  $notes = (isset($_POST['notes_text'])) ? $_POST['notes_text'] : 0; 
  $restaurant_ID = (isset($_POST["restaurant_id"])) ? $_POST["restaurant_id"] : 0; 
  $takeaway = get_post_meta($restaurant_ID, '_cmb2_select_take_away', true); 
  $delivery = get_post_meta($restaurant_ID, '_cmb2_select_delivery', true); 
  $delivery_time = get_field('delivery_time', $restaurant_ID); 
} ?>

<?php
if($currency_menu =='USD'){ $current='USD'; 
}elseif($currency_menu=='EUR'){ $current='EUR';
}elseif($currency_menu=='GBP'){ $current='&pound;'; }
?>
<!--<section class="parallax-window"  id="short"  data-parallax="scroll" data-image-src="<?php echo esc_url($media_payment);?>" data-natural-width="1400" data-natural-height="350">
    <div id="subheader">
    	<div id="sub_content">
    	 <h1><?php echo esc_html__( 'Place your order', 'petsworld' );?></h1>
            <div class="bs-wizard">
                <div class="col-xs-4 bs-wizard-step complete">
                  <div class="text-center bs-wizard-stepnum"><strong>1.</strong> <?php echo esc_html__( 'Your details', 'petsworld' );?></div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="<?php if(isset($select_pages_checkout)){ echo get_page_link($select_pages_checkout); }else{}?>" class="bs-wizard-dot"></a>
                </div>
                               
                <div class="col-xs-4 bs-wizard-step active">
                  <div class="text-center bs-wizard-stepnum"><strong>2.</strong> <?php echo esc_html__( 'Payment', 'petsworld' );?></div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#0" class="bs-wizard-dot"></a>
                </div>
            
              <div class="col-xs-4 bs-wizard-step disabled">
                  <div class="text-center bs-wizard-stepnum"><strong>3.</strong> <?php echo esc_html__( 'Finish!', 'petsworld' );?></div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="<?php if(isset($page_susses)){ echo get_page_link($page_susses); }else{}?>" class="bs-wizard-dot"></a>
                </div>  
		</div><!-- End bs-wizard --
        </div><!-- End sub_content --
	</div><!-- End subheader --
</section>--><!-- End section -->
<!-- End SubHeader ============================================ -->
 
<!-- Content ================================================== -->
<div class="container">
<div class="row">           
	<div class="col-md-9">
	<div class="box_style_2">
	<div id="accordion" class="panel-group">
	<h2 class="inner">Payment methods</h2>

	<!-- Order detail -->
	<div  id="foodmycart" style="display:none;">
	<?php for($i = 0 ; $i< $dem; $i++ ){
		$ar_food .='<tr><td class="each-name-food"><strong class="shopp-quantity">' .$quantityy[$i]. '</strong>x ' .$arrnamess[$i] . '<div class="pull-right shopp-price"> ' . $current . '<em class="price_order">' . $arrpricess[$i] . '</em></div></td></tr><br>; '; }
	$aa = strip_tags($ar_food); ?>
	</div >

	<?php if($gateway_selected == "paypal" ){ ?>
	<div class="panel panel-default" id="paypalSec" >
	<div class="payment_select" id="paypal">
	<div><label class="collapsed">
		<input  type="radio" value="" name="payment_method" class="check" checked="checked"> Pay with paypal
	</label></div>
	<div id="collapseTwo" class="collapse" <?php if($gateway_selected == "paypal"){ ?> style="display:block;"<?php } ?>>
	<?php $handlingpayment = $theme_option['select_pages_handlingpayment'];?>
	<form id="paypal_course" name="paypal_form" action="<?php  echo $select_pages_payment;?>" method="post" data-form-type="paypal">
	    <?php for($i = 0 ; $i< $dem; $i++ ){
		$itemlist .= $quantityy[$i]. ' x ' . $arrnamess[$i] .' ' . $currency_menu .' ' . $arrpricess[$i] . ' ; '; } ?>
		<input type="hidden" name="cmd" value="_xclick"> 
		<input type="hidden" name="FORMID" id="FORMID" class="form_type" value="paypal" />
		<input type="hidden" name="business" value="<?php if(isset($payment_pp_email) && !empty($payment_pp_email)){ echo esc_attr( $payment_pp_email );}?>">
		<input type="hidden" name="first_name" class="firstname_order" value="<?php echo esc_attr($first_name );?>">
		<input type="hidden" name="last_name" class="lastname_order" value="<?php echo esc_attr($lastname_order );?>">
		<input type="hidden" name="address1" value="<?php echo esc_attr($email_order );?>">
		<br><br>
		<input type="hidden" name="tel_order" class="tel_order" value="<?php echo esc_attr($tel_order );?>">
		<input type="hidden" name="address_order" class="address_order" value="<?php echo esc_attr($address_order );?>">
		<input type="hidden" name="city_order" class="city_order" value="<?php echo esc_attr($city_order );?>">
		<input type="hidden" name="pcode_order" class="pcode_order" value="<?php echo esc_attr($pcode_order );?>">
		<br><br>
		<input type="hidden" name="item_name" value="<?php echo esc_attr( $itemlist );?>">
		<input type="hidden" name="email" data-bind="out:value" data-name="Email" class="buyer_email" value="<?php echo esc_attr($email_order );?>">
		<input type="hidden" name="address_order" data-bind="out:value" data-name="Address" value="<?php echo esc_attr($address_order );?>">
		<!-- <input type="hidden" name="item_name" data-bind="out:value" data-name="item" value="subprice">-->
		<input type="hidden" name="item_number" id="item_number" data-bind="out:value" data-name="sku" value="<?php echo uniqid();?>"> 
		<input type="hidden" name="amount" class="price" data-bind="out:value" data-name="amount" value="<?php echo esc_attr($total);?>">
		<input type="hidden" name="notes" class="note"   value="<?php echo esc_attr($notes);?>" />
		<input type="hidden" name="currency_code" class="currency" value="<?php echo esc_attr($currency_menu);?>">
		<input type="hidden" name="image_url" value="<?php echo $logoimg; ?>" >						
		<input type="hidden" class="detailfood" name="detailfood" value="<?php echo esc_attr($aa);?>">
		<input type="hidden" name="order_time"  id="order_time" value="<?php echo $_POST['order_time'];?>" />
    <input type="hidden" name="mode-of-payment"  id="mode-of-payment" value="<?php echo $modeofpayment;?>" />
    <input type="hidden" name="membership_type"  id="membership_type" value="<?php echo $membership_type;?>" />
    <!--======= BUTTONS =========-->
		<button id="paypal_payment" class="btn_full"><?php if(isset($payment_pp_button) && !empty($payment_pp_button)){ echo esc_attr( $payment_pp_button );}?></button>
	</form>
	</div>
	</div>
	</div> 
	<?php } ?>

	<?php if($gateway_selected == "stripe" ){ ?>
	<div class="panel panel-default" id="payCash">
	<div class="payment_select nomargin">
	<label class="collapsed"><input type="radio" value="" name="payment_method" class="check" checked="checked">Pay with Stripe</label>
	<i class="icon_wallet"></i>
	<div id="collapseThree" class="collapse" <?php if($gateway_selected == "stripe"){ ?> style="display:block;"<?php } ?>>
	<h4><span class="fullstripe-form-title"><?php echo __('Please Fill Up Payment Form') ?></span></h4>
	<?php $handlingpayment = $theme_option['select_pages_handlingpayment'];?> 
	<form action="<?php  echo $select_pages_payment ;?>" method="POST" id="payment-form-style">
	<input type="hidden" name="FORMID" id="FORMID" class="form_type" value="stripe" />
    	<input type="hidden" name="action" value="wp_full_stripe_payment_charge"/>
    	<input type="hidden" name="amount" value="<?php echo $sub_order ?>"/>
    	<input type="hidden" name="formName" value="<?php echo $first_name.' '.$lastname_order; ?>"/>
    	<input type="hidden" name="isCustom" value="<?php echo $isCustom; ?>"/>
      <input type="hidden" name="first_name" class="firstname_order" value="<?php echo esc_attr($first_name );?>">
      <input type="hidden" name="last_name" class="lastname_order" value="<?php echo esc_attr($lastname_order );?>">
      <input type="hidden" name="tel_order" class="tel_order" value="<?php echo esc_attr($tel_order );?>">
      <input type="hidden" name="email" data-bind="out:value" data-name="Email" class="buyer_email" value="<?php echo esc_attr($email_order );?>">
    	<input type="hidden" name="formDoRedirect" value="<?php echo $formDoRedirect; ?>"/>
    	<input type="hidden" name="formRedirectPostID" value="<?php echo $formRedirectPostID; ?>"/>
    	<input type="hidden" name="formRedirectUrl" value="<?php echo $formRedirectUrl; ?>"/>
    	<input type="hidden" name="formRedirectToPageOrPost" value="<?php echo $formRedirectToPageOrPost; ?>"/>
    	<input type="hidden" name="showAddress" value="<?php echo $showAddress; ?>"/>
    	<input type="hidden" name="sendEmailReceipt" value="<?php echo $sendEmailReceipt; ?>"/>
      <input type="hidden" name="mode-of-payment"  id="mode-of-payment" value="<?php echo $modeofpayment;?>" />
      <input type="hidden" name="membership_type"  id="membership_type" value="<?php echo $membership_type;?>" />
      <input type="hidden" class="detailfood" name="detailfood" value="<?php echo esc_attr($aa);?>">
      <input type="hidden" name="currency_code" class="currency" value="<?php echo esc_attr($currency_menu);?>">
    	<p class="payment-errors"></p>

    	<div class="_100 form-group" style="padding-bottom: 5px;">
        <img src="<?php echo plugins_url('../img/' . $creditCardImage, dirname(__FILE__)); ?>" alt="<?php _e('Credit Cards', 'petsworld'); ?>"/>
    	</div>
    	<div class="_50 form-group">
        <label class="control-label fullstripe-form-label"><?php _e( 'Name on card', 'petsworld' ); ?></label>
        <input type="text" name="fullstripe_name" class="form-control" id="fullstripe_name" placeholder="First and last name">
    	</div>
    	<div class="_50 form-group">
        <label class="control-label fullstripe-form-label"><?php _e( 'Card Number', 'petsworld' ); ?></label>
        <input type="text" name="card_number" autocomplete="off" class="form-control" size="20" data-stripe="number" placeholder="Card number">
    	</div>
    	<div class="_50 form-group card-form">
        <label class="control-label fullstripe-form-label"><?php _e( 'Card CVV', 'petsworld' ); ?></label><br/>
        <input type="password" name="cvc" autocomplete="off" size="4" class="form-control" data-stripe="cvc" style="width: 80px;"/>
    	</div>
    	<div class="date-form">
        <div class="_25 form-group month-form 123">
            <label class="control-label fullstripe-form-label"><?php _e( 'Month', 'petsworld' ); ?></label>
            <select data-stripe="exp-month" name="exp-month">
                <option value="01"><?php _e( 'January', 'petsworld' ); ?></option>
                <option value="02"><?php _e( 'February', 'petsworld' ); ?></option>
                <option value="03"><?php _e( 'March', 'petsworld' ); ?></option>
                <option value="04"><?php _e( 'April', 'petsworld' ); ?></option>
                <option value="05"><?php _e( 'May', 'petsworld' ); ?></option>
                <option value="06"><?php _e( 'June', 'petsworld' ); ?></option>
                <option value="07"><?php _e( 'July', 'petsworld' ); ?></option>
                <option value="08"><?php _e( 'August', 'petsworld' ); ?></option>
                <option value="09"><?php _e( 'September', 'petsworld' ); ?></option>
                <option value="10"><?php _e( 'October', 'petsworld' ); ?></option>
                <option value="11"><?php _e( 'November', 'petsworld' ); ?></option>
                <option value="12"><?php _e( 'December', 'petsworld' ); ?></option>
            </select>
        </div>
        <div class="_25 form-group year-form">
            <label class="control-label fullstripe-form-label"><?php _e( 'Year', 'petsworld' ); ?></label>
            <select data-stripe="exp-year" name="exp-year">
                <?php $startYear = date('Y'); $numYears = 20;
                for ($i = 0; $i < $numYears; $i++) { $yr = $startYear + $i;
                    echo "<option value='" . $yr . "'>" . $yr . "</option>"; } ?>
            </select>
        </div>
    	</div>
    	<div class="_100 form-group form-br">
        <br/>
    	</div>
    	<div class="_100 form-group form-sub">
        <?php if ( $isCustom == 1 ): ?>
            <button type="submit" class="btn_full"><?php echo  $stripe_button_title; ?>
		<?php if ( $showButtonAmount == 1 ) { printf( ' %s%0.2f', $currency_menu, $sub_order / 100.0 ); } ?>
	    </button>
        <?php else: ?>
            <button type="submit" class="btn_full"><?php echo $stripe_button_title; ?></button>
        <?php endif; ?>
    	</div>
	</form>
	</div>
	</div>
	</div>
	<?php } ?>

	</div>
    </div><!-- End box_style_1 -->
    </div><!-- End col-md-12 -->
            
    <div class="col-md-3" id="sidebar">
	<div class="theiaStickySidebar">
	<div id="cart_box">
	<h3><?php echo esc_html__( 'Your order', 'petsworld' );?> <i class="icon_cart_alt pull-right"></i></h3>					
	<table class="table table_summary">
		<tbody  id="mycart">
		<?php for($i = 0 ; $i< $dem; $i++ ){ ?>
		<?php  if( $arrpricess[$i] != '0.00' ){ $style = 'style="display:block;"'; } else { $style = 'style="display:none;"'; }?>
		<tr><td class="each-name"><strong class="shopp-quantity"><?php echo esc_attr($quantityy[$i]);?></strong> x <?php echo esc_attr($arrnamess[$i]);?><div class="pull-right shopp-price" <?php echo $style;?>><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($current);}else{}?><em class="price_order"><?php echo  $arrpricess[$i];  ?></em></div></td></tr>
		<?php } ?>
		</tbody >
		<hr>
		<tbody>
			<?php if ( $gateway_selected == 'paypal' ) { ?> <?php $subtal_orders = (float)$sub_order; ?> <?php } ?>
			<?php if ( $gateway_selected == 'stripe' ) { ?> <?php $subtal_orders = (float)$sub_order / 100.0; ?> <?php } ?>
			<tr><td>
			<?php echo esc_html__( 'Subtotal', 'petsworld' );?> <span class="pull-right"><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($current); }else{}?><?php echo esc_attr($subtal_orders); ?></span>
			</td></tr>
			<tr><td class="total">
			<?php $total_order = $subtal_orders; ?>
			<div class="cart-total"><?php echo esc_html__( 'TOTAL', 'petsworld' );?> <em class="pull-right"><?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($current);}else{}?><span><?php echo esc_attr($total_order);?><span></em>
				<input type="hidden" name="total-hidden-charges" id="total-hidden-charges" value="0" />
			</div>
			</td></tr>
		</tbody>
		</table>
		<hr>
<!-- <a class="btn_full" href="cart_3.html">Confirm your order</a> -->
</div><!-- End cart_box -->
</div><!-- End theiaStickySidebar -->
</div><!-- End col-md-3 -->
   
</div><!-- End row -->
</div><!-- End container -->
<?php endif;?>

<?php get_footer();?>
--------------------------------------------------------------

<?php 
/*
*Template Name: Payment
*/

$theme_option = petsworld_option('general'); //var_dump($theme_option);
$paypalapimode = "test";
$stripetpubkey = $theme_option['stripetestpubkey'];
$stripetseckey = $theme_option['stripetestseckey'];
$stripeapimode = $theme_option['stripeapimode'];
$stripelpubkey = $theme_option['stripelivepubkey'];
$stripelseckey = $theme_option['stripeliveseckey']; 
//$page_susses = $theme_option['page_susses']; 
//$page_return = $theme_option['select_pages_payment'];
$cur_dir = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
//$return_url = get_page_link($page_susses);
//$cancel_url = $cur_dir.'/template_part/checkout.php';
//$notify_url = $cur_dir.'http://localhost/quickfood/?page_id=424'; 
//$cancel_url = get_page_link($page_return);
//$notify_url = get_page_link($page_return);
$return_url = get_bloginfo('url').'/success/';
$cancel_url = get_bloginfo('url').'/checkout/';
$notify_url = get_bloginfo('url').'/checkout/';

$btest_url  = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp';
$blive_url  = 'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp'; 

if ( $_POST["FORMID"] == "stripe" ){
 define('__ROOT__', dirname(dirname(__FILE__))); 
 require_once(__ROOT__.'/petworld/stripe-php-v4.9.1/init.php'); 
}

//var_dump($_POST); exit;
//extract($_POST);
//$item_name = $exam_name; $item_amount = $amount; $userid = $userid;

//create user if membership request
if($_POST['mode-of-payment']  ==  'membership111'){
if(count($_POST)>0){
  $email_address = $_POST['email'];
if( null == username_exists( $_POST['first_name'])) {
 
  // Generate the password and create the user
  $password = wp_generate_password( 12, false );
  $user_id = wp_create_user( $_POST['first_name'], $password, $email_address );

  // Set the nickname
  wp_update_user(
    array(
      'ID'          =>    $user_id,
      'nickname'    =>    $_POST['first_name'],
      'user_nicename' =>  $_POST['first_name'],
      'display_name'  =>   $_POST['first_name'],
      'first_name'   =>    $_POST['first_name'],
      'last_name'    =>    $_POST['last_name']
    )
  );

  // Set the role
  $user = new WP_User( $user_id );
  $user->set_role( 'subscriber' );

  add_user_meta($user_id,'tel_order',$_POST['tel_order']);
  add_user_meta($user_id,'membership_type',$_POST['membership_type']);

  // Email the user
  wp_mail( $email_address, "Welcome!", 'Your Password: ' . $password );
  
  //echo "User Has Been Created. Please Check Email For Password."; 
  //$homeurl = get_bloginfo('url');
  //echo '<meta http-equiv="refresh" content="0; url='.$homeurl.'">'; 
  //echo '<meta http-equiv="refresh" content="0;URL=http://lab-1.sketchdemos.com/P916-Pet-Sitter/"/>';

}}
} // end if($_POST['mode-of-payment']  ==  'membership'){


if( $_POST["FORMID"] == "barclays" ){ 
$params = $_POST; 

$params['ACCEPTURL']=$return_url; 
$params['DECLINEURL']=$cancel_url; 
$params['EXCEPTIONURL']=$cancel_url; 
$params['CANCELURL']=$cancel_url; 

$postData = ''; $postData = '?';
foreach($params as $k => $v){ $postData .= $k . '='.$v.'&'; }
$postData = rtrim($postData, '&');
 
header('location:'.$btest_url.$postData);


} elseif( $_POST["FORMID"] == "paypal" ){

//$querystring = "?business=".urlencode($paypal_email)."&";
//$querystring .= "item_name=".urlencode($item_name)."&";
//$querystring .= "amount=".urlencode($item_amount)."&";

$querystring = '?';
foreach($_POST as $key => $value){
    $value = urlencode(stripslashes($value));
    $querystring .= "$key=$value&";
}
$querystring .= "return=".urlencode(stripslashes($return_url))."&";
$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
$querystring .= "notify_url=".urlencode($notify_url); 

if( $paypalapimode == "test" ){  
  header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
}else{
  header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
}
exit();


} elseif( $_POST["FORMID"] == "stripe" ){

if($stripeapimode == 'test'){
try{
\Stripe\Stripe::setApiKey($stripetseckey);
\Stripe\Stripe::$apiBase = "https://api-tls12.stripe.com";
//creating the token
$token = \Stripe\Token::create(array( "card" => array(
    		"number" => $_POST['card_number'], "exp_month" => $_POST['exp-month'],
    		"exp_year" => $_POST['exp-year'], "cvc" => $_POST['cvc'] ))); 
// create customer
$customer = \Stripe\Customer::create(array(
  "email" => $POST['email'],
  "source" => $token,
));
// Charge the user's card:
$charge = \Stripe\Charge::create(array(
	"amount" => $_POST['amount'], 
	"currency" => $_POST['currency_code'],
  	"description" => $_POST['detailfood'], 
	"source" => $token, 
));

$result = "success";

} catch(Stripe_CardError $e) {			
 	$error = $e->getMessage(); $result = "declined";
} catch (Stripe_InvalidRequestError $e) {
		$result = "declined";		  
} catch (Stripe_AuthenticationError $e) {
		$result = "declined";
} catch (Stripe_ApiConnectionError $e) {
		$result = "declined";
} catch (Stripe_Error $e) {
		$result = "declined";
} catch (Exception $e) {
    if ($e->getMessage() == "zip_check_invalid") {
		$result = "declined";
    } else if ($e->getMessage() == "address_check_invalid") {
		$result = "declined";
    } else if ($e->getMessage() == "cvc_check_invalid") {
		$result = "declined";
    } else {
		$result = "declined";
    }
}
} else { //stripe live credentials

}
header('location:'.$return_url.'?result='.$result);
exit();

}

?>
---------------------------------------------------------------------------------------
<?php
/*
*Template Name: Payment successful
*/

get_header();
if(have_posts()) : the_post();
$theme_option = petsworld_option('general');
$select_pages_checkout = $theme_option['select_pages_checkout'];
$select_pages_payment = $theme_option['select_pages_payment'];
$media_page_success = $theme_option['media_page_success']['url'];

global $wpdb;

 $restaurant = get_post($_POST['restaurant_id']); 
 $orderPerHr = get_field('orders_per_hour', $restaurant->ID); 
 $maxOrderPerHr = get_field('max_no_order_per_hour', $restaurant->ID);

 // update the field to check for max orders
 if($orderPerHr < $maxOrderPerHr){  
    $inc = $orderPerHr + 1; update_field('orders_per_hour', $inc , $restaurant->ID); 
 } else{  
    $inc = 1; update_field('orders_per_hour', $inc , $restaurant->ID);  
 }


 $stauts = $_POST['payment_status'];
 $transaction_id = $_POST['txn_id'];
 $item_number = $_POST['item_number'];
 $payer_email = $_POST['payer_email']; 

/* $wpdb->update( 
  'quickfood_payments', 
  array( 
    'transaction_id' => $transaction_id,  // string
    'status' =>'Completed', // integer (number) 
    'buyer_email' =>$payer_email // integer (number) 
  ), 
  array( 'order_id' => $item_number ), 
  array( 
    '%s', // value1
    '%s',  // value2
    '%s'  // value3
  ), 
  array( '%s' ) 
);*/

// $wpdb->query("UPDATE  quickfood_payments
//     SET transaction_id= '".$transaction_id."',
//     status = '".$stauts."',
// WHERE  order_id = '".$item_number."'" );
// die(); // stop executing script

?>

<!--<section class="parallax-window" id="short" data-parallax="scroll" data-image-src="<?php if(isset($media_page_success) && !empty($media_page_success)){ echo esc_url($media_page_success); }else{}?>" data-natural-width="1400" data-natural-height="350">
    <div id="subheader">
      <div id="sub_content">
       <h1>Place your order</h1>
            <div class="bs-wizard">
                <div class="col-xs-4 bs-wizard-step complete">
                  <div class="text-center bs-wizard-stepnum"><strong>1.</strong> Your details</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="<?php if(isset($select_pages_checkout)){ echo get_page_link($select_pages_checkout); }else{ echo "#";}?>" class="bs-wizard-dot"></a>
                </div>
                               
                <div class="col-xs-4 bs-wizard-step complete">
                  <div class="text-center bs-wizard-stepnum"><strong>2.</strong> Payment</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="<?php if(isset($select_pages_payment)){ echo get_page_link($select_pages_payment); }else{}?>" class="bs-wizard-dot"></a>
                </div>
            
              <div class="col-xs-4 bs-wizard-step complete">
                  <div class="text-center bs-wizard-stepnum"><strong>3.</strong> Finish!</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#0" class="bs-wizard-dot"></a>
                </div>  
    </div><!-- End bs-wizard --
        </div><!-- End sub_content --
  </div><!-- End subheader --
</section>--><!-- End section -->
<!-- End SubHeader ============================================ -->

<!-- Content ================================================== -->

<div class="container margin_60_35">
  <div class="row">
    <div class="col-md-offset-3 col-md-6">
      <div class="box_style_2">

	<?php if($_REQUEST['result'] == 'declined'){ ?>

        <h2 class="inner">Payment Not confirmed!</h2>
        <div id="confirm">
          <i class="icon_check_alt2"></i>
          <h3>The Payment was not confirmed.!!</h3>
          <p> Lorem ipsum dolor sit amet, nostrud nominati vis ex, essent conceptam eam ad. 
	      Cu etiam comprehensam nec. Cibo delicata mei an, eum porro legere no.
          </p>
        </div>

	<?php } elseif ($_REQUEST['result'] == 'success') { ?>

        <h2 class="inner">Order confirmed!</h2>
        <div id="confirm">
          <i class="icon_check_alt2"></i>
          <h3>Thank you!</h3>
          <p>
            Lorem ipsum dolor sit amet, nostrud nominati vis ex, essent conceptam eam ad. Cu etiam comprehensam nec. Cibo delicata mei an, eum porro legere no.
          </p>
        </div>


        <?php } elseif (empty($_REQUEST)) { ?> 

        <h2 class="inner">Order confirmed!</h2>
        <div id="confirm">
          <i class="icon_check_alt2"></i>
          <h3>Thank you!</h3>
          <p>
            Lorem ipsum dolor sit amet, nostrud nominati vis ex, essent conceptam eam ad. Cu etiam comprehensam nec. Cibo delicata mei an, eum porro legere no.
          </p>
        </div>
        <?php }?>
      </div>
    </div>
  </div><!-- End row -->
</div><!-- End container -->
<!-- End Content =============================================== -->
<?php endif;?>
<?php
  get_footer();
?>
-----------------------------------------------------------------------------------------------------






