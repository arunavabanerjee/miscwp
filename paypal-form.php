<?php
/**
 * Template Name: Paypal Payment
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>
<?php get_header();?>

<?php //if(count($_POST) == 0){ wp_redirect( home_url() ); exit; } ?>
<?php $the_query = new WP_Query( 'page_id=324' ); ?>
<?php while ($the_query -> have_posts()) : $the_query -> the_post(); ?>
<div class="inner-banner">
    <img src="<?php echo get_field('header_banner',$post->ID);?>" alt="">
    <div class="innerBannerCaption">
       <div class="container">
        <div class="row">
           <div class="col-lg-12">
               <h1>View Plan</h1>
           </div> 
        </div>
    </div>
    </div>
    <div class="bottom-img">
        <img src="<?php bloginfo('template_url')?>/images/bottom-bg-wht.png" alt="">
    </div>
</div>
<?php endwhile;?> 

<div class="clearfix"></div>

<?php $uid=$_REQUEST['ins']; //$uid = '177177';    
      $post_id = $wpdb->get_row("SELECT * from ins_policy_applicants where id=$uid");//print_r($post_id);
      $postall_id = $wpdb->get_results("SELECT * from ins_policy_applicants where insumem_ID=$uid");

      //business variables 
      $item_id = $post_id->id;
      $item_name =  $post_id->ins_class; 
      $item_value = $post_id->Total_premium_amt; 
      $item_currency = 'SGD'; 
      //$item_currency = 'USD';
      //$business_email = 'jonlau88-facilitator_api1.singnet.com.sg';
      $business_email = 'jonlau88-facilitator@singnet.com.sg';

      //customer variables
      $customer_name = $post_id->ins_party_nm;
      $customer_lname = $post_id->ins_party_nric;
      $customer_address_1 = $post_id->addr1; 
      $customer_address_2 = $post_id->addr2;
      $customer_country = $post_id->country; 
      $customer_zip = $post_id->postal;
      $customer_phone_a = $post_id->ph_no;
      $customer_email = $post_id->email;
?>

<div class="comprehensive-div">
 <div class="container">
 <div class="row">
    <div class="plansel panel_border text-center ">
    <h3>Payment Summary</h3>
    
    <!-- plan and amount -->
	<p><span class="plan_span"><strong>Plan Selected  :</strong></span>  
	    <?php echo $post_id->ins_class;?> : SGD <?php echo $post_id->Total_premium_amt;?></p>
	    
	<!-- customer details -->    
	<p><span class="plan_span"><strong>NRIC :</strong></span>  
	    <?php echo $post_id->ins_party_nric;?></p>
    <p><span class="plan_span"><strong>Employer Name :</strong></span>  
        <?php echo $post_id->ins_party_nm;?></p>
    <p><span class="plan_span"><strong>Address :</strong></span>  
        <?php echo $post_id->addr1 .' , '.$post_id->addr2;?></p>
    <p><span class="plan_span"><strong>Country  :</strong></span>  
        <?php echo $post_id->country;?></p>
    <p><span class="plan_span"><strong>Postal :</strong></span>  
        <?php echo $post_id->postal;?></p>    
    
    <!-- ph and email -->            
    <p><span class="plan_span"><strong>Phone No. :</strong></span>  
        <?php echo $post_id->ph_no;?></p>
    <p><span class="plan_span"><strong>E-mail :</strong></span> 
        <?php echo $post_id->email;?></p>    

    </div>
    </div> 
     
    <div class="row">     
    <!--<form action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
	 <input type="hidden" name="business" value="<?php echo $business_email; ?>">
 	 <input type="hidden" name="cmd" value="_xclick">

	 <!-- item specifics -->
	 <input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
	 <input type="hidden" name="item_number" value="<?php echo $item_id; ?>">
	 <input type="hidden" name="amount" value="<?php echo $item_value; ?>">
  	 <input type="hidden" name="tax" value="1">
  	 <input type="hidden" name="quantity" value="1">
	 <input type="hidden" name="currency_code" value="<?php echo $item_currency; ?>">

 	 <!--customer specifics -->
	 <input type="hidden" name="first_name" value="<?php echo $customer_name; ?>">
	 <input type="hidden" name="last_name" value="<?php echo $customer_lname; ?>">
	 <input type="hidden" name="address1" value="<?php echo $customer_address_1; ?>">
	 <input type="hidden" name="address2" value="<?php $customer_address_2; ?>">
	 <input type="hidden" name="country" value="<?php echo $customer_country; ?>">
	 <!--<input type="hidden" name="state" value="PA">-->
	 <input type="hidden" name="zip" value="<?php echo $customer_zip; ?>">
	 <input type="hidden" name="night_phone_a" value="<?php echo $customer_phone_a; ?>">
	 <input type="hidden" name="email" value="<?php $customer_email; ?>">

	 <!-- Specify URLs --
	 <input type='hidden' name='cancel_return' value='<?php echo home_url(); ?>/paypal-payment/'>
	 <input type='hidden' name='return' value='<?php echo home_url(); ?>/paypal-payment/'>-->

         <!-- paypal button -->
	 <input type="image" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online">
	 <img alt="" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif">
    </form>
    
    <script>
	//window.onload = function(){
	//  setTimeout(function(){
	//      document.forms['myForm'].submit()
	//  }, 2000);
	//}
    </script>
    
    <div class="clearfix"></div>
    </div>

 </div>
</div>

<?php
get_footer(); 
?>
