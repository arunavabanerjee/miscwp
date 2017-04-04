//---add to cart 

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
			//foreach option add the price
			var optionprice = 0; var opthtml = ''; 
			$('#detail-'+thisID+' .popup-box-td .sub-options input[type=checkbox]').each(function(index, element){ 
			   var checkId = $(this).val(); 
			   var optprice = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').val(); 
			   var optname = $('#detail-'+thisID+' .popup-box-td .sub-options #price-'+checkId+'').parent().text();
			   if( $(this).is(':checked') ){ //alert(checkId); alert(optprice);
			      opthtml += '<div class="more-option"><strong class="shopp-quantity">'+iniquantity+'</strong> '+optname+'<div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+optprice+'</em></div></div>';
			      optionprice += parseFloat(optprice); 
			   } 	
			});

			var prev_charges = $('.cart-total span').html();
			prev_charges = parseFloat(prev_charges)+parseFloat(itemprice);
			prev_charges = parseFloat(prev_charges)*parseInt(iniquantity);
			opt_charges = parseFloat(optionprice)*parseInt(iniquantity);
			prev_charges += opt_charges;
			prev_charges = prev_charges.toFixed(2); 

			$('.cart-total span').html(prev_charges);
			$('#total-hidden-charges').val(prev_charges);
			
		$('#name_foodid').append('<tr><td class="each-name">' +itemname+'</td></tr>');
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a> <strong class="shopp-quantity">'+iniquantity+'</strong> ' +itemname+'<div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ <?php if(isset($currency_menu) && !empty($currency_menu)){ echo esc_attr($currency_menu);}else{}?><em class="price_order">'+itemprice+'</em></div>'+opthtml+'</td></tr>');
			
		/*}*/
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
	
	$('.remove_item').livequery('click', function() {
		
		var deduct = $(this).parent().children(".shopp-price").find('em').html();
		var prev_charges = $('.cart-total span').html();
		
		var thisID = $(this).parent().attr('id').replace('each-','');
		
		var pos = getpos(Arrays,thisID);
		Arrays.splice(pos,1,"0")
		
		prev_charges = parseFloat(prev_charges)-parseFloat(deduct);
		prev_charges = prev_charges.toFixed(2);
		$('.cart-total span').html(prev_charges);
		$('#total-hidden-charges').val(prev_charges);


			var prev_charges1  = $('#total-hidden-charges').val();
	if($("#check1").is(":checked")){
	$("#check-hidden").val("takeaway");
}else{$("#check-hidden").val("delivery");}
	if((prev_charges1 > 0) && $("#check").is(":checked")){
	prev_charges = parseFloat(prev_charges1) + parseFloat(<?php echo esc_attr($delivery_charge );?>) ;
	prev_charges = prev_charges.toFixed(2);
	}else{prev_charges = parseFloat(prev_charges1)}
	
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
		$(this).parent().remove();
		
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



