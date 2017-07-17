jQuery(function($){

$(document).ready(function(){

	$('button#paypal_payment').click(function(){

		var payment_type = $('.form_type').val(); 
		var name_buyer = $('.firstname_order').val() + ' ' + $('.lastname_order').val();
		var buyer_email = $('.buyer_email').val();
		var price = $('.price').val();
		var currency = $('.currency').val();
		var detailfood = $('.detailfood').val();
		var item_number = $('#item_number').val();
		var note = $('.note').val(); 
		var restaurantID = $('.restaurantid').val(); 

		// address details
		var addressVal =  $('#paypal .address_order').val(); 
		var cityVal = $('#paypal .city_order').val(); 
		var pCodeVal = $('#paypal .pcode_order').val();	
		var telCodeVal = $('#paypal .tel_order').val();	

		var deliveryfee = $('#paypal .deliveryfee').val();	

		//enter details in the table payment.
		$.ajax({  
                url: ajaxurl,  
                method : "GET",
                dataType: "text",  
                data:{ 
                	'action' :'action_newquickfood',
                	'name_buyer' : name_buyer,
                	'buyer_email':buyer_email,
                	'price':price,
                	'currency':currency,
                	'detailfood':detailfood,
                	'item_number':item_number, 
			'payment_type': payment_type,
			'restaurantID': restaurantID, 
			'addressVal': addressVal,
			'cityVal': cityVal,
			'pCodeVal': pCodeVal,
			'telCodeVal': telCodeVal,
			'deliveryfee': deliveryfee
                }
           	}); 

	});

	$('button#barclay_payment').click(function(){

		var payment_type = $('.bform_type').val(); 
		var name_buyer = $('.firstname_order').val() + ' ' + $('.lastname_order').val();
		var buyer_email = $('.buyer_email').val();
		var price = $('.price').val();
		var currency = $('.currency').val();
		var detailfood = $('.detailfood').val();
		var item_number = $('#item_number').val();
		var note = $('.note').val(); 
		var restaurantID = $('.restaurantid').val(); 

		// address details
		var addressVal =  $('#barclays .address_order').val(); 
		var cityVal = $('#barclays .city_order').val(); 
		var pCodeVal = $('#barclays .pcode_order').val();
		var telCodeVal = $('#barclays .tel_order').val();

		var deliveryfee = $('#barclays .deliveryfee').val();		

		//enter details in the table payment.
		$.ajax({  
                url: ajaxurl,  
                method : "GET",
                dataType: "text",  
                data:{ 
                	'action' :'action_newquickfood',
                	'name_buyer' : name_buyer,
                	'buyer_email':buyer_email,
                	'price':price,
                	'currency':currency,
                	'detailfood':detailfood,
                	'item_number':item_number, 
			'payment_type': payment_type,
			'restaurantID': restaurantID,
			'addressVal': addressVal,
			'cityVal': cityVal,
			'pCodeVal': pCodeVal,
			'telCodeVal': telCodeVal,
			'deliveryfee': deliveryfee
                }
           	}); 

	});


	$('button#paywithcash').click(function(){ 
              
		var name_buyer = $('.firstname_order').val() + ' ' + $('.lastname_order').val();
		var buyer_email = $('.email_orders').val();
		var price = $('.priceorder').val();
		var currency = $('.currency').val();
		var detailfood = $('.detailfood').val();
		var item_number = $('.item_number').val();
		var note = $('.note').val(); 
		var restaurantID = $('.restaurantid').val(); 

		// address details
		var addressVal =  $('#payCash .address_order').val(); 
		var cityVal = $('#payCash .city_order').val(); 
		var pCodeVal = $('#payCash .pcode_order').val();
		var telCodeVal = $('#payCash .tel_order').val();

		var deliveryfee = $('#payCash .deliveryfee').val();	

		$.ajax({  
                	url: ajaxurls,  
                	method : "GET",
                	dataType: "json",  
                	data:{ 
                		'action' :'actionorder_newquickfood',
                		'name_buyer' : name_buyer,
                		'buyer_email':buyer_email,
                		'price':price,
                		'currency':currency,
                		'detailfood':detailfood,
                		'item_number':item_number,
                		'note':note,
				'restaurantID':restaurantID,
				'addressVal': addressVal,
				'cityVal': cityVal,
				'pCodeVal': pCodeVal,
				'telCodeVal': telCodeVal,
				'deliveryfee': deliveryfee
                	}
           	});

	});

    $('.page-template-checkout #creditCrd #payment-form button[type="submit"]').click(function(){
    	var payment_type = $('#paypalSec .form_type').val(); 
		var name_buyer = $('#paypalSec .firstname_order').val() + ' ' + $('#paypalSec .lastname_order').val();
		var buyer_email = $('#paypalSec .buyer_email').val();
		var price = $('#paypalSec .price').val();
		var currency = $('#paypalSec .currency').val();
		var detailfood = $('#paypalSec .detailfood').val();
		var item_number = $('#paypalSec #item_number').val();
		var note = $('#paypalSec .note').val(); 
		var restaurantID = $('#paypalSec .restaurantid').val(); 

		// address details
		var addressVal =  $('#paypalSec .address_order').val(); 
		var cityVal = $('#paypalSec .city_order').val(); 
		var pCodeVal = $('#paypalSec .pcode_order').val();	
		var telCodeVal = $('#paypalSec .tel_order').val();	
		var deliveryfee = $('#paypalSec .deliveryfee').val();	

		//enter details in the table payment.
		$.ajax({  
                url: ajaxurl,  
                method : "GET",
                dataType: "text",  
                data:{ 
                	'action' :'action_newquickfood',
                	'name_buyer' : name_buyer,
                	'buyer_email':buyer_email,
                	'price':price,
                	'currency':currency,
                	'detailfood':detailfood,
                	'item_number':item_number, 
					'payment_type': payment_type,
					'restaurantID': restaurantID, 
					'addressVal': addressVal,
					'cityVal': cityVal,
					'pCodeVal': pCodeVal,
					'telCodeVal': telCodeVal,
					'deliveryfee': deliveryfee
                }
        }); 
      
    });

});
});
