 
//--- select posts based on today date 
<?php $today = date('Ymd'); $metaqry = array(); $metaqry["relation"] = "AND";  
      $intrim = array( 'key' => 'date', 'value' => $today, 'compare' => '==' ); array_push($metaqry, $intrim);
  $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 'meta_query' => $metaqry); 
  query_posts($args); 
  while ( have_posts() ) : the_post(); ?>
	<?php the_ID(); ?>
<?php endwhile; 
wp_reset_query(); ?>


//---wordpress template
<!-- Container -->
<div class="container posts-conainer-loop <?php echo $layout; ?>">

<div class="twelve columns">
    <div class="extra-padding post-row">

    <?php if ( have_posts() ) : ?>

     <?php //get values of locations
           $field_key = "location"; $locations = array();
           $field = get_field_object($field_key);
           if( $field ){ foreach( $field['choices'] as $k => $v ){ array_push($locations, $v); }} ?>

     <?php foreach($locations as $loc){ ?>

      <?php
        $metaqry = array(); $metaqry["relation"] = "AND";  
        $intrim = array( 'key' => "location", 'value' => $loc, 'compare' => '==' ); array_push($metaqry, $intrim);
        $args = array('numberposts' => -1,'post_type' => 'post', 'cat' => 72, 'meta_query' => $metaqry); 
        query_posts($args); 
	//echo $GLOBALS['wp_query']->request;
	//global $wpdb; echo $wpdb->last_query;
      ?>

   <?php // display posts with meta value sorted
     $metaqry = array(); $metaqry["relation"] = "AND";  
     $intrim = array( 'key' => "location", 'value' => $loc, 'compare' => '==' ); array_push($metaqry, $intrim);
     //$args = array('numberposts' => -1,'post_type' => 'post', 'cat' => 72, 'meta_query' => $metaqry);
    $args = array('numberposts' => -1,'post_type' => 'post', 'cat' => 72, 'meta_query' => $metaqry, 'orderby'  => 'meta_value_num','meta_key' => 'price', 'order' => 'ASC');
     query_posts($args); //echo $GLOBALS['wp_query']->request;
   ?>
	    
      <?php global $wp_query; 
       if( $wp_query->found_posts > 0 ) { ?>

      <div class=""> 
       <p>Location: <?php echo $loc; ?> </p>

       <?php while ( have_posts() ) : the_post(); ?> 
           <?php
                /* Include the Post-Format-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                 */
                $format = get_post_format();
                if( false === $format  )  $format = 'standard';
                get_template_part( 'postformats/content', $format );
            ?>
        <?php endwhile; ?>

      </div>
      <div class="clearfix"></div>
      
      <?php wp_reset_query(); ?>

      <?php } // endif ?>

     <?php } // endforeach ?>

    <?php else : ?>
        <?php get_template_part( 'content', 'none' ); ?>
    <?php endif; ?>

        <div class="clearfix"></div>

        <!-- Pagination -->
        <div class="pagination-container">
            <?php if(function_exists('wp_pagenavi')) { ?>
            <nav class="pagination">
                 <?php wp_pagenavi(); ?>
            </nav>
            <?php
            } else {
            if ( get_next_posts_link() ||  get_previous_posts_link() ) : ?>
            <nav class="pagination">
                <ul>
                    <?php if ( get_previous_posts_link() ) : ?>
                        <li id="next"><?php previous_posts_link( ' ' ); ?></li>
                    <?php  endif; ?>
                    <?php if ( get_next_posts_link() ) : ?>
                        <li id="prev"><?php next_posts_link( ' ' ); ?></li>
                        <!-- <li><a href="#" class="next"></a></li> -->
                     <?php endif; ?>
                </ul>
            </nav>
           <?php endif;
           } ?>
        </div>

    </div>
</div>

<?php get_sidebar(); ?>

//----------------------------------------------------------------------------------------------------


// ACF field list
$field_key = "field_5039a99716d1d";
$field = get_field_object($field_key);
if( $field )
{
    echo '<select name="' . $field['key'] . '">';
        foreach( $field['choices'] as $k => $v )
        {
            echo '<option value="' . $k . '">' . $v . '</option>';
        }
    echo '</select>';
}

//------------------------------------------------------------------------

<script>
jQuery(document).ready(function(){ 

/*jQuery.ajax({
  method: "GET",
  url: "some.php",
  cache: false,
  data: { name: "John", location: "Boston" }
})
.done(function( msg ) {
    alert( "Data Saved: " + msg );
});*/

});
</script>

========================================================================

$vars = $_GET; $metaqry = array(); 
$metaqry["relation"] = "AND";
foreach($vars as $key => $val){
  if($key == 'earliest_arrivals' || $key == 'latest_returns' || $key == 'duration' || $key == 'post_type' ){ continue; }
  $intrim = array( 'key' => $key, 'value' => $val, 'compare' => '=' ); array_push($metaqry, $intrim); }
$args = array('numberposts' => -1,'post_type' => 'post', 'cat' => 72, 'meta_query' => $metaqry); //print_r($args);
query_posts($args); 

-------------------------------------------

$vars = $_GET; $metaqry = array(); 
$metaqry["relation"] = "AND";
foreach($vars as $key => $val){ 
  $intrim = array( 'key' => $key, 'value' => $val, 'compare' => '=' ); array_push($metaqry, $intrim); }
$args = array('numberposts' => -1,'post_type' => 'post','meta_query' => $metaqry);
query_posts($args);

echo $GLOBALS['wp_query']->request; 

---------------------------


//--mail being sent to admin email. 
        if(!empty($changes)){
         $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
	 $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
	 $message = '<!DOCTYPE HTML><html>
		    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Profile Changes</title></head>';
         $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
		     <tr><td><img src="images/logo.png" style="display:block"></td></tr>
		     <tr style="text-align:left;font-size:20px;color:#009fff">
			<td style="padding-top:30px;">Member Name:'.$family_name.'-'.$given_name.'</td></tr>
                     <tr style="text-align:left;font-size:18px;color:#009fff">
			<td style="padding-top:30px;padding-bottom:30px;"> The following changes have been made in Personal Section</td></tr>';
         $message .= '<hr/>'; 
         foreach($changes as $key => $change){ 
           $message .= '<tr style="text-align:left;font-size:16px;color:#aa8095">
			  <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
           $message .= $key.' : '. $change;
           $message .= '<hr/>';
	   $message .= '</td></tr>'; 
         }
         $message .= '<tr style="text-align:center;height:30px;"></tr>';							
         $message .= '<tr style="text-align:center;font-size:14px;">
		      <td style="height:30px; color:#fff; background:#306C9E; padding-top:20px; padding-bottom:20px;">All rights reserved,2016.Swiss Chinese Chamber of Commerce</td></tr>';
         $message .= '</table></body></html>';
						
	 $headers = "MIME-Version: 1.0" . "\r\n";
	 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
	 $headers .= "From: info@cham.org\r\n";
	 @mail($to, $subject, $message, $headers);
        }

//email templates
//--mail being sent to admin email. 
if(!empty($changes)){
   $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
   $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
   $message = '<!DOCTYPE HTML><html>
		    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Profile Changes</title></head>';
   $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
	     <tr><td><img src="images/logo.png" style="display:block"></td></tr>
	     <tr style="text-align:center;font-size:24px;color:#009fff">
		<td style="padding-top:30px;">Name:'.$family_name.'-'.$given_name.'</td></tr>
                <tr style="text-align:center;font-size:24px;color:#009fff">
		<td style="padding-top:30px;"> The following changes have been made in Personal Section</td></tr>';
  $message .= '<hr/>'; 
  foreach($changes as $key => $change){ 
    $message .= '<tr style="text-align:center;font-size:14px;color:#aa8095">
	  <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
    $message .= $key.' : '. $change;
    $message .= '<hr/>';
    $message .= '</td></tr>'; 
  }									
  $message .= '<tr style="background:#306C9E;color:#fff;text-align:center;font-size:14px;">
	      <td style="height:30px; color:#fff;">All rights reserved,2016.Swiss Chinese Chamber of Commerce </td></tr>';
  $message .= '</table></body></html>';
						
 $headers = "MIME-Version: 1.0" . "\r\n";
 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
 $headers .= "From: info@cham.org\r\n";
 @mail($to, $subject, $message, $headers);

}

// email templates
//--mail being sent to admin email. 
$changes = array();
//--get the user data from database
$selSql = "SELECT * FROM `sc_c_userdetails` WHERE `userId` = $userId"; $selRows = mysql_query($selSql);
if(mysql_num_rows($selRows) > 0){
  while ($row = mysql_fetch_assoc($selRows)) { 
   if($row['user_title'] != $user_title){ $changes["user_title"] = $user_title; }
   if($row['year_of_birth'] != $year_of_birth){ $changes["year_of_birth"] = $year_of_birth; }
   if($row['family_name'] != $family_name){ $changes["family_name"] = $family_name; }
   if($row['given_name'] != $given_name){ $changes["given_name"] = $given_name; }
   if($row['chinese_name'] != $chinese_name){ $changes["chinese_name"] = $chinese_name; }
   if($row['nationality'] != $nationality){ $changes["nationality"] = $nationality; }
   if($row['positionIn_company'] != $positionIn_company){ $changes["positionIn_company"] = $positionIn_company; }
   if($row['address_english'] != $address_english){ $changes["address_english"] = $address_english; }
   if($row['city_english'] != $city_english){ $changes["city_english"] = $city_english; } 
   if($row['province_area'] != $province_area){ $changes["province_area"] = $province_area; }
   if($row['address_chinese'] != $address_chinese){ $changes["address_chinese"] = $address_chinese; }
   if($row['city_chinese'] != $city_chinese){ $changes["city_chinese"] = $city_chinese; }
   if($row['zip_code_english'] != $zip_code_english){ $changes["zip_code_english"] = $zip_code_english; }
   if($row['direct_phone'] != $direct_phone){ $changes["direct_phone"] = $direct_phone; }
   if($row['mobile_phone'] != $mobile_phone){ $changes["mobile_phone"] = $mobile_phone; }
   //if($row['direct_email'] != $direct_email){ $changes["direct_email"] = $direct_email; }
   if($row['npo_organization_type'] != $npo_organization_type){ 
                                       $changes["npo_organization_type"] = $npo_organization_type; }
   if($row['npo_organization_description'] != $npo_organization_description){ 
                                       $changes["npo_organization_description"] = $npo_organization_description; }
   if($row['media_type'] != $media_type){ $changes["media_type"] = $media_type; }
   if($row['media_description'] != $media_description){ $changes["media_description"] = $media_description; }
  }
} 
if(!empty($changes)){
   $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
   $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
   $message = '<!DOCTYPE HTML><html>
	       <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
               <title>Profile Changes</title></head>';
   $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
		  <tr><td><img src="img/new_year_bg.jpg" style="display:block"></td></tr>
		  <tr style="text-align:center;font-size:24px;color:#009fff">
		  <td style="padding-top:30px;"> The following changes have been made in Personal Section</td></tr>'; 
   foreach($changes as $key => $change){ 
      $message .= '<tr style="text-align:center;font-size:14px;color:#aa8095">
	           <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
      $message .= $key.' : '. $change;
      $message .= '</td></tr>'; 
   }									
   $message .= '<tr style="background:#306C9E;color:#fff;text-align:center;font-size:14px;">
          	<td style="height:30px; color:#fff;">All rights reserved,2016.Swiss Chinese Chamber of Commerce </td></tr>';
   $message .= '</table></body></html>';
						
 $headers = "MIME-Version: 1.0" . "\r\n";
 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
 $headers .= "From: info@cham.org\r\n";
 @mail($to, $subject, $message, $headers);
}

//====================================================================================================
	
// check if the restaurant has filled all orders. 
$restaurant = get_post($restaurant_store); 
$orders = $wpdb->get_results('SELECT * FROM `quickfood_order` WHERE `restaurant_id` = "'.$restaurant->ID.'" ORDER BY ids DESC LIMIT 1', ARRAY_A); 
$lastOrderTime =  $orders[0]['createds'];  echo $lastOrderTime; 
$currentTime = time(); echo $currentTime; 

$orderPerHr = get_field('orders_per_hour', $restaurant->ID); 
$maxOrderPerHr = get_field('max_no_order_per_hour', $restaurant->ID);

// update the field to check for max orders
if($orderPerHr < $maxOrderPerHr){ 
   $inc = $orderPerHr + 1; update_field('orders_per_hour', $inc , $restaurant->ID); 
} else{ 
   $inc = 0; update_field('orders_per_hour', $inc , $restaurant->ID); 
}

//-------------------------------------------------------------------------------------------
	
/*
 * get all custom fields, loop through them and load the field object 
 * to create a label => value markup
 */
$fields = get_fields(get_the_ID()); var_dump($fields);
//if( $fields ){  
//  foreach( $fields as $field_name => $value ){
//     // get_field_object( $field_name, $post_id, $options )
//     // - $value has already been loaded for us, no point to load it again in the get_field_object function
//     $field = get_field_object($field_name, false, array('load_value' => false));
//     echo '<div>'; echo '<h3>' . $field['label'] . '</h3>'; echo $value; echo '</div>';
//  }
//}
	
	
------------------------------------------------------------------------------------------------
	
	
						<table class="table table_summary" >
					<script type="text/javascript">
		jQuery(function($){		

		$(document).ready(function() {
	
		var Arrays=new Array();
		//var subArrays = new Array();
	
		$('.add-to-cart-button').click(function(){
		
		var thisID = $(this).attr('id'); //subArrays.push(thisID);
		//sub-ids of the checkboxes
		//$('#detail-'+thisID+' .popup-box .popup-box-td .box-details .sub-options input[type=checkbox]').each( function(){ 
                //  if( $(this).is(':checked') ){ subArrays.push( $(this).val() ); } 
		//}); //alert(subArrays); console.log(subArrays); 

		//checking the length of the subarrays and looping through
		//var sublength = subArrays.length
		//for(i=0; i<sublength; i++){ var thisID = subArrays[i];

		var itemname  = document.getElementsByClassName('name-'+thisID)[0].innerHTML;
		var itemprice  = $('#price-'+thisID).val();
		
		if(include(Arrays,thisID))
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
		{
			Arrays.push(thisID);
			
			var iniquantity = $('#detail-'+thisID+' .popup-box .popup-box-td .input-number').val();
			//foreach option add the price
			var optionprice = 0; 
			$('#detail-'+thisID+' .popup-box-td .sub-options').each( function(index, element){ 
			   $('#detail-'+thisID+' .popup-box-td .sub-options input[type=checkbox]').each(function(index, element){ 
				var checkId = $(this).val();
				var optprice = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').val();
				if( $(this).is(':checked') ){ optionprice += parseFloat(optprice); } 
			   }); 	
			});

			alert( optionprice ); 


			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges)+parseFloat(itemprice);
			prev_charges = parseFloat(prev_charges)*parseInt(iniquantity);
			prev_charges = prev_charges.toFixed(2); 

			$('.cart-total span').html(prev_charges);
			$('#total-hidden-charges').val(prev_charges);
			
		$('#name_foodid').append('<tr><td class="each-name">' +itemname+'</td></tr>');
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a> <strong class="shopp-quantity">'+iniquantity+'</strong> ' +itemname+'<div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+itemprice+'</em></div></td></tr>');
			
		}
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		}else{ $("#check-hidden").val("delivery"); }
	if((prev_charges1 > 0) && $("#check").is(":checked")){
		prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
		prev_charges = prev_charges.toFixed(2);
	}else{	prev_charges = parseFloat(prev_charges1); }
	
$('.cart-total2 .pull-right span').text(prev_charges);
$('#total-hidden-charges2').val(prev_charges);
$('.total-hidden-charges2').val(prev_charges);

//
var prev_charges1  = $('#total-hidden-charges').val();
	if($("#check1").is(":checked")){
	$("#check-hidden").val("takeaway");
}else{$("#check-hidden").val("delivery");}
	if((prev_charges1 > 0) && $("#check1").is(":checked")){
	prev_charges = prev_charges1 ;
	}else{prev_charges1 = 0}
	
$('.cart-total2 .pull-right span').text(prev_charges);

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
		
	});	
	
	
	
	
