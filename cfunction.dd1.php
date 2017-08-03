
function msieversion() 
{
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0) // If Internet Explorer, return version number
    {
        alert(parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    else  // If another browser, return 0
    {
        alert('otherbrowser');
    }

    return false;
}

---------------------------------

function msieversion() {

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
    {
        alert(parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    else  // If another browser, return 0
    {
        alert('otherbrowser');
    }

    return false;
}

------------------------------------




<script type="text/javascript">
jQuery(function($){		
	$(document).ready(function() {
		var Arrays=new Array();
    
	  /* no option */
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
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ &pound;<em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
	if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(3.95) ;
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
            opthtml +='<div class="pull-right shopp-price"'+style+'>x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ &pound;<em class="price_order">'+optprice+'</em></div>';
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
			
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ &pound;<em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
	if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(3.95) ;
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
            opthtml +='<div class="pull-right shopp-price"'+style+'>x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ &pound;<em class="price_order">'+optprice+'</em></div>';
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
		
			$('#mycart').append('<tr><td id="each-'+thisID+'"><a href="#0" class="remove_item "><i class="icon_minus_alt"></i></a><div class="prcD"><strong class="shopp-quantity">'+iniquantity+'</strong><div class="prName"> ' +itemname+'</div><div class="pull-right shopp-price">x <strong class="innershopp-quantity">'+iniquantity+'</strong>@ &pound;<em class="price_order">'+itemprice+'</em></div></div>'+opthtml+'</td></tr>');
			
		/*}*/
		//
		var prev_charges1  = $('#total-hidden-charges').val();
		//if($("#check1").is(":checked")){ $("#check-hidden").val("takeaway");
		//}else{ $("#check-hidden").val("delivery"); }
	//if((prev_charges1 > 0) && $("#check").is(":checked")){
        if((prev_charges1 > 0)){
		prev_charges = parseFloat(prev_charges1) + parseFloat(3.95) ;
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
                    priceLimit = parseFloat(10)-parseFloat(0.01),
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
	prev_charges = parseFloat(prev_charges1) + parseFloat(3.95) ;
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
    priceLimit = parseFloat(10)-parseFloat(0.01);
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
	prev_charges = parseFloat(prev_charges1) + parseFloat(3.95);
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
										<label class="delivery-time">
	                    <!--<input type="radio" id="check" checked ="checked"  onclick = "Check1()" value=""  name="option_3">-->
			    			    Delivery Time: 30 - 60 mins
                	</label>
                	                						<hr>
					<table class="table table_summary">
					<tbody>
					<tr>
						<td>
							 
							  <div class="cart-total">Subtotal <em class="pull-right">&pound;<span class="price-sub">0<span></em>
                                                          <div class="if-cart">Minimum &pound; 10</div>
							 <input type="hidden" name="total-hidden-charges" id="total-hidden-charges" value="0" />
               							 </div>
						</td>
					</tr>
					<tr>
						<td>
							 Delivery fee <span class="pull-right">&pound;3.95</span>
						</td>
					</tr>
					<tr class="mobile-item-hiden">
						<td class="total">
							 <div class="cart-total2">TOTAL <em class="pull-right">&pound;&nbsp;<span>0<span></em>

							 
							 </div>
						</td>
						
					</tr>
					</tbody>
					</table>
                                        
                                        <div class="mobile-item-display">
                                                <div class="left">Items<span>0</span></div>
                                                <div class="right">Total 
                                                    <div class="price-sign pull-right">
                                                    &pound;<span> 0.00</span>
                                                    </div>
                                                </div>
                                        </div>
                                        
					<hr>
					<form action="https://doordropdeliveries.co.uk/your-details/" method="POST">
				 	<input type="hidden" name="id_menu"  id="id_menu" value="776" />
				 	<input type="hidden" name="delivery_charge"  id="deliverry" value="3.95" />
					<input type="hidden" name="total-hidden-charges2"  class="total-hidden-charges2" value="0" />
					<input type="hidden" name="check"  id="check-hidden" value="0" />
					<input type="hidden" name="phone-store"  id="phonenumber" value="07934856800" />
					<input type="hidden" name="working-period"  id="workingperiod" value="" />
					<input type="hidden" name="arrprice"  id="arrprice" value="" />
					<input type="hidden" name="arrname"  id="arrname" value="" />
					<input type="hidden" name="number_quantity"  id="number_quantity" value="" />
          <input type="hidden" name="restaurant_id"  id="restaurant_id" value="40" />
          <input type="hidden" name="order_time"  id="order_time" value="1501681798" />

						   <button type="submit" class="btn_full">Checkout</button>
									
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

 <footer>
        <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-3 ">
            <div class="widget widget_text"><h3>Secure Payments With</h3>			<div class="textwidget"><p>
<img src="https://doordropdeliveries.co.uk/wp-content/themes/quickfood/img/cards.png" alt="" class="img-responsive">
</p></div>
		</div>            </div>
             <div class="col-md-3 col-sm-3 ">
            <div class="widget widget_nav_menu"><h3>About</h3><div class="menu-menu-footer-container"><ul id="menu-menu-footer" class="menu"><li id="menu-item-475" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-475"><a href="https://doordropdeliveries.co.uk/about_us/">About</a></li>
<li id="menu-item-477" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-477"><a href="https://doordropdeliveries.co.uk/faq-2/">FAQ</a></li>
<li id="menu-item-476" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-476"><a href="https://doordropdeliveries.co.uk/contacts/">Contact</a></li>
<li id="menu-item-1025" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1025"><a href="https://doordropdeliveries.co.uk/submit_resturent/">Work with us</a></li>
<li id="menu-item-1026" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1026"><a href="https://doordropdeliveries.co.uk/submit_driver/">Deliver with us</a></li>
</ul></div></div>            </div>
            <div class="col-md-3 col-sm-3 " id="newsletter">
            <div class="widget widget_search"><h3>Search</h3>
   <form class="search-form" method="get" id="searchform" action="https://doordropdeliveries.co.uk/">
    <div id="custom-search-input-blog">
        <div class="input-group col-md-12">
          <input type="text" class="form-control input-lg" name="s" value="" placeholder="Search">
            <span class="input-group-btn">
                <button class="btn btn-info btn-lg" type="submit">
                    <i class="icon-search-1"></i>
                </button>
            </span>
        </div>
    </div>
    </form></div>            </div>
            <div class="col-md-2 col-sm-3">
                <h3>Settings</h3>                                                <div class="styled-select">
                    <select class="form-control" name="lang" id="lang">
                                                    <option value="English">English</option>
                                                 
                    </select>
                </div>
                                                                <div class="styled-select">
                    
                    <select class="form-control" name="currency" id="currency">
                                                     <option value="GBP" >GBP</option>
                                             </select>
                </div>
                            </div>
        </div><!-- End row -->
        <div class="row">
            <div class="col-md-12">
                <div id="social_footer">
                    <ul>

                        <li><a href="http://facebook.com/doordropuk"><i class="icon-facebook"></i></a></li>
                        <li><a href="http://twitter.com/doordropuk"><i class="icon-twitter"></i></a></li>
                        <li><a href="https://plus.google.com/u/0/"><i class="icon-google"></i></a></li>
                        <li><a href="http://instagram.com/doordropuk"><i class="icon-instagram"></i></a></li>
                        <li><a href="#0"><i class="icon-pinterest"></i></a></li>
                        <li><a href="#0"><i class="icon-vimeo"></i></a></li>
                        <li><a href="https://www.youtube.com/channel/UCRx80tmJYbNNYK-JSmC8iyA"><i class="icon-youtube-play"></i></a></li>
                    </ul>
                    <p>© Munch on Deck Ltd 2016</p>                </div>
            </div>
        </div><!-- End row -->
    </div><!-- End container -->
    </footer>
    <!-- End Footer =============================================== -->

<div class="layer"></div><!-- Mobile menu overlay mask -->

<!-- Login modal -->   
<div class="modal fade" id="login_2" tabindex="-1" role="dialog" aria-labelledby="myLogin" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content modal-popup">
			<a href="#" class="close-link"><i class="icon_close_alt2"></i></a>
			
               
                
        <form  class="popup-form" name="loginform" id="myLogin loginform" action="https://doordropdeliveries.co.uk/wp-login.php" method="post">
            
            <div class="login_icon"><i class="icon_lock_alt"></i></div>
           
                
                <input placeholder="Username" type="text" name="log" id="user_login" class="input form-control form-white" value="" />
                <input placeholder="Password" type="password" name="pwd" id="user_pass" class="input form-control form-white" value="" />

            
            <p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> Remember Me</label></p>
            
            <button type="submit" class="btn btn-submit">Submit</button>
        </form>           
		</div>
	</div>
</div><!-- End modal -->   
    
<!-- Register modal -->   
<div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="myRegister" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content modal-popup">
			<a href="#" class="close-link"><i class="icon_close_alt2"></i></a>
			
                                    <!--display error/success message-->
            <div>
                                            </div>
            <form method="post" class="popup-form" id="register-form"  role="form">
                      <div class="login_icon"><i class="icon_lock_alt"></i></div>
                    <input type="text" id="username" name="username" class="form-control form-white" placeholder="User name">
                    <input type="text" id="lastname" name="lastname" class="form-control form-white" placeholder="Last Name">
                    <input type="email" id="email" name="email"  class="form-control form-white" placeholder="Email">
                    <input type="text" name="pwd1" id="pwd1" class="form-control form-white" placeholder="Password"  >
                    <input type="text" name="pwd2" id="pwd2" class="form-control form-white" placeholder="Confirm password">
                    <div id="pass-info" class="clearfix"></div>
                    <div class="checkbox-holder text-left">
                        <div class="checkbox">
                            <input type="checkbox" value="accept_2" id="check_2" name="check_2" />
                            <label for="check_2"><span>I Agree to the <strong>Terms &amp; Conditions</strong></span></label>
                        </div>
                    </div>
                    <input type="hidden" id="post_nonce_field" name="post_nonce_field" value="f1f7d42edb" /><input type="hidden" name="_wp_http_referer" value="/menu/mcdonalds/" />                    <button type="submit" class="btn btn-submit">Register</button>
                    <input type="hidden" name="task" value="register" />


                <div class="modal-footer mt-10">
                    
                    
                    <div class="text-left">
                            Already have account? <button id="register_login_btn" type="button" class="btn btn-link">Sign-in</button>
                    </div>
                    
                </div>                
            </form>
                
            <div class="notification-login">
                            </div>
		</div>
	</div>
</div><!-- End Register modal -->

<div class="layer"></div><!-- Mobile menu overlay mask -->
    

<div class="modal fade" id="myReview" tabindex="-1" role="dialog" aria-labelledby="review" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-popup">
                <a href="#" class="close-link"><i class="icon_close_alt2"></i></a>
                                       
                    <form method="post" action="https://doordropdeliveries.co.uk/review-restaurant/" name="review" id="review" class="popup-form">  
                    <div class="login_icon"><i class="icon_comment_alt"></i></div>
                     <input name="restaurant_name" id="restaurant_name" type="hidden" value="Mexican Taco Mex"> 
                        <div class="row" >
                            <div class="col-md-6">
                            <input name="name_review" id="name_review" type="text" placeholder="Name" class="form-control form-white">          
                            </div>
                            <div class="col-md-6">
                                <input name="email_review" id="email_review" type="email" placeholder="Your email" class="form-control form-white">
                            </div>
                        </div><!-- End Row --> 
                        
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control form-white" name="food_review" id="food_review">
                                    <option value="">Food Quality</option>
                                    <option value="Low">Low</option>
                                    <option value="Sufficient">Sufficient</option>
                                    <option value="Good">Good</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Superb">Super</option>
                                    <option value="Not rated">I don&#039;t know</option>
                                </select>                            
                            </div>
                            <div class="col-md-6">
                                <select class="form-control form-white"  name="price_review" id="price_review">
                                    <option value="">Price</option>
                                    <option value="Low">Low</option>
                                    <option value="Sufficient">Sufficient</option>
                                    <option value="Good">Good</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Superb">Super</option>
                                    <option value="Not rated">I don&#039;t know</option>
                                </select>
                            </div>
                        </div><!--End Row -->    
                        
                        <div class="row">
                            <div class="col-md-6">
                                    <select class="form-control form-white"  name="punctuality_review" id="punctuality_review">
                                    <option value="">Punctuality</option>
                                    <option value="Low">Low</option>
                                    <option value="Sufficient">Sufficient</option>
                                    <option value="Good">Good</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Superb">Super</option>
                                    <option value="Not rated">I don&#039;t know</option>
                                </select>                       </div>
                            <div class="col-md-6">
                                <select class="form-control form-white"  name="courtesy_review" id="courtesy_review">
                                    <option value="">Courtesy</option>
                                    <option value="Low">Low</option>
                                    <option value="Sufficient">Sufficient</option>
                                    <option value="Good">Good</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Superb">Super</option>
                                    <option value="Not rated">I don&#039;t know</option>
                                </select>
                            </div>
                        </div><!--End Row -->     
                        <textarea name="review_text" id="review_text" class="form-control form-white" style="height:100px" placeholder="Write your review"></textarea>
                        <input type="text" id="verify_review" class="form-control form-white" placeholder="Are you human? 3 + 1 = ">
                    <input type="submit" value="Submit" class="btn btn-submit" id="submit-review">
                </form>
                            <div id="message-review"></div>
            </div>
        </div>
    </div><!-- End Register modal -->   
<!-- COMMON SCRIPTS -->
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/plugins/contact-form-7/includes/js/jquery.form.min.js?ver=3.51.0-2014.06.20'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var _wpcf7 = {"recaptcha":{"messages":{"empty":"Please verify that you are not a robot."}}};
/* ]]> */
</script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=4.7'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/common_scripts_min.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/functions.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/assets/validate.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/cat_nav_mobile.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/detail-page-menu.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/theia-sticky-sidebar.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-content/themes/quickfood/js/jquery.livequery.js?ver=4.7.5'></script>
<script type='text/javascript' src='https://doordropdeliveries.co.uk/wp-includes/js/wp-embed.min.js?ver=4.7.5'></script>

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="http://lab-3.sketchdemos.com/P-879-woocommerce/wp-content/themes/quickfood/js/bootstrap.min.js" type="text/javascript"></script>-->

<script>
//plugin bootstrap minus and plus
//http://jsfiddle.net/laelitenetwork/puJ6G/
$('.btn-number').click(function(e){
    e.preventDefault();
    
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    
    
});
$(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });    
    
    $(document).ready(function(){
        $('#cart_box h3').on('click', function(){
            $("#cart_box").toggleClass( "mycart-open" );
    //$("#cart_box").addClass('mycart-open').siblings().removeClass('mycart-open');
});
//        $('#cart_box h3').click(function(event) {
//            $(this).addClass('open');
//            $("#cart_box").addClass("mycart-open");
//        }); 
        
//        $('#cart_box h3').click(function() {
//            if($(this).hasClass('red'))
//            {
//                $(this).addClass('blue').removeClass('red');
//            }
//            else
//            {
//               $(this).addClass('red').removeClass('blue');
//            }
//        });
            
   });
    
</script>
