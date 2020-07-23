var $jq = jQuery.noConflict();
$jq(document).ready(function(){

	var ajaxurl = HOMEY_ajax_vars.admin_url+ 'admin-ajax.php';
	var process_loader_spinner = HOMEY_ajax_vars.process_loader_spinner;
	var homey_reCaptcha = HOMEY_ajax_vars.homey_reCaptcha;
	var success_icon = HOMEY_ajax_vars.success_icon;
        var homey_date_format = HOMEY_ajax_vars.homey_date_format;
	var login_sending = HOMEY_ajax_vars.login_loading;

        /* ------------------------------------------------------------------------ */
        /* Set date format
        /* ------------------------------------------------------------------------ */
        var homey_convert_date = function(date) {

            if(date == '') {
                return '';
            }
     
            var d_format, return_date;
            
            d_format = homey_date_format.toUpperCase();

            var changed_date_format = d_format.replace("YY", "YYYY");
            var return_date = moment(date, changed_date_format).format('YYYY-MM-DD');

            return return_date;
         
        }

        /*--------------------------------------------------------------------------
         *  Contact listing host
         * -------------------------------------------------------------------------*/
        $jq( '.contact_listing_host').on('click', function(e) { //alert('this');
            e.preventDefault();

            var $this = $jq(this);
            var $host_contact_wrap = $this.parents( '.host-contact-wrap' );
            var $form = $this.parents( 'form' ); 
            var $messages = $host_contact_wrap.find('.homey_contact_messages');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'action' : 'homey_child_contact_list_host_submission',
		},
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.msg);
                        $form.find('input').val('');
                        $form.find('textarea').val('');
                    } else {
                        $messages.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        /*--------------------------------------------------------------------------
         *   Contact host form on host detail page
         * -------------------------------------------------------------------------*/
        /*$('#host_detail_contact').on('click', function(e) {
            e.preventDefault();
            var current_element = $(this);
            var $this = $(this);
            var $form = $this.parents( 'form' );

            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: $form.serialize(),
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    current_element.children('i').remove();
                    current_element.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function( res ) {
                    current_element.children('i').removeClass(process_loader_spinner);
                    if( res.success ) {
                        $('#form_messages').empty().append(res.msg);
                        current_element.children('i').addClass(success_icon);
                    } else {
                        $('#form_messages').empty().append(res.msg);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }

            });
        });*/

        /* ------------------------------------------------------------------------ */
        /*  Reservation Request
        /* ------------------------------------------------------------------------ */
         $jq('#homey_child_request_for_reservation').on('click', function(e){
            e.preventDefault();

            var $this = $jq(this);
            var extra_options = []; 
            var temp_opt;
	    var $checkform = 0;

	    var u_name = $jq('input[name="name"]').val(); 
	    var u_email = $jq('input[name="email"]').val();
	    var u_phone = $jq('input[name="phone"]').val(); 
	    //var u_location = $jq('select[name="city"]').val();

	    var u_bedrooms = $jq('input[name="no_of_bedrooms"]').val();
	    var u_bathrooms = $jq('input[name="no_of_bathrooms"]').val();

            var check_in_date = $jq('input[name="arrive"]').val();
            check_in_date = homey_convert_date(check_in_date);

            var check_out_date = $jq('input[name="depart"]').val();
            check_out_date = homey_convert_date(check_out_date);

            var guest_message = $jq('textarea[name="guest_message"]').val();
            var guests = $jq('input[name="guests"]').val();
            var listing_id = $jq('#listing_id').val();
            var security = $jq('#reservation-security').val();
            var notify = $this.parents('.homey_notification');
            notify.find('.notify').remove();

	    //check the required items entered
	    if(u_name == ''){ 
	     $jq('input[name="name"]').parent().children('p').remove();		  
	     $jq('input[name="name"]').parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
	     $checkform = 1;
	    }else{ $jq('input[name="name"]').parent().children('p').remove(); }
	    if(u_email == ''){ 
	     $jq('input[name="email"]').parent().children('p').remove();		  
	     $jq('input[name="email"]').parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
	     $checkform = 1;
	    }else{ $jq('input[name="email"]').parent().children('p').remove(); }
	    if(u_phone == ''){ 	
	     $jq('input[name="phone"]').parent().children('p').remove();	  
	     $jq('input[name="phone"]').parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
	     $checkform = 1;
	    }else{ $jq('input[name="phone"]').parent().children('p').remove(); }
	    if(guest_message == ''){ 
	     $jq('textarea[name="guest_message"]').parent().children('p').remove();		  
	     $jq('textarea[name="guest_message"]').parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
	     $checkform = 1;
	    }else{ $jq('textarea[name="guest_message"]').parent().children('p').remove(); }

	    if($checkform == 1){ return false; }

	    //alert( u_name + ' ' + u_email + ' ' + u_phone + ' ' + u_bedrooms + ' ' + u_bathrooms ); 
	    //alert( check_in_date + ' ' + check_out_date + ' ' + guest_message + ' ' + guests ); 
	    //alert( listing_id + ' ' + security ); return false;

            /*$jq('.homey_extra_price input').each(function() {
                if( ($jq(this).is(":checked")) ) {
                    var extra_name = $jq(this).data('name');
                    var extra_price = $jq(this).data('price');
                    var extra_type = $jq(this).data('type');
                    temp_opt    =   '';
                    temp_opt    =   extra_name;
                    temp_opt    =   temp_opt + '|' + extra_price;
                    temp_opt    =   temp_opt + '|' + extra_type;
                    extra_options.push(temp_opt);
                }
            });*/
            
            //if( parseInt( userID, 10 ) === 0 ) {
            //    $jq('#modal-login').modal('show');
            //} else {
                $jq.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'homey_child_add_reservation',
			'name' : u_name, 
			'email' : u_email, 
			'phone' : u_phone, 
			'bedrooms' : u_bedrooms,
			'bathrooms' : u_bathrooms,
                        'check_in_date': check_in_date,
                        'check_out_date': check_out_date,
                        'guests': guests,
                        'listing_id': listing_id,
                        'extra_options': extra_options,
                        'guest_message': guest_message,
                        'security': security
                    },
                    beforeSend: function( ) {
                        $this.children('i').remove();
                        $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                    },
                    success: function(data) {
                        if( data.success ) {
			    $jq('input[name="name"]').val(''); $jq('input[name="email"]').val('');
			    $jq('input[name="phone"]').val(''); $jq('textarea[name="guest_message"]').val('');
			    $jq('input[name="no_of_bedrooms"]').val(''); $jq('input[name="no_of_bathrooms"]').val('');
			    $jq('input[name="guests"]').val(''); $jq('.check_in_date, .check_out_date').val('');
                            notify.prepend('<div class="notify text-success text-center btn-success-outlined btn btn-full-width">'+data.message+'</div>');

                        } else {
                            notify.prepend('<div class="notify text-danger text-center btn-danger-outlined btn btn-full-width">'+data.message+'</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    },
                    complete: function(){
                        $this.children('i').removeClass(process_loader_spinner);
                    }

                }); 
            //}

         });


        /*--------------------------------------------------------------------------
         *  Save a New Agent created By Broker
         * -------------------------------------------------------------------------*/
        $jq('.homey_new_agent_save').on('click', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
            var $homey_addagent_wrap = $this.parents( '.homey-new-agent-add-wrapper' );
            var $form = $this.parents( 'form' );
	    var $checkform = 0;
            var $topmessages = $homey_addagent_wrap.find('.homey_add_new_agent_messages_top'); 
            var $bottommessages = $homey_addagent_wrap.find('.homey_add_new_agent_messages_bottom');
	    var $redirect = $homey_addagent_wrap.find('input[name="redirect_to"]').val();

	    //check data for each field
	    var $inputs = $homey_addagent_wrap.find( 'input' ); 
	    $inputs.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
		if($checkform == 0){ 
		  if($jq(this).attr('id') == 'postalcode'){ 
		    if(!/^[0-9]+$/.test($jq(this).val())){ 
		       $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Numeric Values For PostalCode [0-9]</span></p>');
		       $checkform = 1;
		    }
		  }
		  if($jq(this).attr('id') == 'email'){ 
		    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		    if(!reg.test($jq(this).val())){ 
		       $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Valid Email</span></p>');
		       $checkform = 1;
		    }
		  }
		}
	    }); 
	    var $selects = $homey_addagent_wrap.find( 'select' ); 
	    $selects.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).children('option:selected').val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
	    });
	    if($checkform == 1){ return false; }   
	    //alert('form ok submitting');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'action' : 'homey_child_add_new_agent_submission',
		},
                method: $form.attr('method'),
                dataType: "JSON",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $topmessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $form.find('input').val('');
			$form.find('select').val('');
                        //$form.find('textarea').val('');
			setTimeout(function(){ window.location.replace($redirect) }, 3000);
                    } else {
                        $topmessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }

            });

        });

        /*--------------------------------------------------------------------------
         *  Save a New Client created By Broker
         * -------------------------------------------------------------------------*/
        $jq('.homey_new_client_save').on('click', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
            var $homey_addagent_wrap = $this.parents( '.homey-new-client-add-wrapper' );
            var $form = $this.parents( 'form' );
	    var $checkform = 0;
            var $topmessages = $homey_addagent_wrap.find('.homey_add_new_client_messages_top'); 
            var $bottommessages = $homey_addagent_wrap.find('.homey_add_new_client_messages_bottom');
	    var $redirect = $homey_addagent_wrap.find('input[name="redirect_to"]').val();

	    //check data for each field
	    var $inputs = $homey_addagent_wrap.find( 'input' ); 
	    $inputs.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
		if($checkform == 0){ 
		  if($jq(this).attr('id') == 'postalcode'){ 
		    if(!/^[0-9]+$/.test($jq(this).val())){ 
		       $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Numeric Values For PostalCode [0-9]</span></p>');
		       $checkform = 1;
		    }
		  }
		  if($jq(this).attr('id') == 'email'){ 
		    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		    if(!reg.test($jq(this).val())){ 
		       $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Valid Email</span></p>');
		       $checkform = 1;
		    }
		  }
		}
	    }); 
	    var $selects = $homey_addagent_wrap.find( 'select' ); 
	    $selects.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).children('option:selected').val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
	    });
	    if($checkform == 1){ return false; }   
	    //alert('form ok submitting');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'action' : 'homey_child_add_new_client_submission',
		},
                method: $form.attr('method'),
                dataType: "JSON",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $topmessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $form.find('input').val('');
			$form.find('select').val('');
                        //$form.find('textarea').val('');
			setTimeout(function(){ window.location.replace($redirect) }, 3000);
                    } else {
                        $topmessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }

            });

        });


        /*--------------------------------------------------------------------------
         *  Assigning an agent for a lead by broker
         * -------------------------------------------------------------------------*/
        $jq('.custom-actions select').on('change', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
	    var $retrieved_ids = $this.val(); 
            if ($retrieved_ids == 0){ return false; }
	    //alert($retrieved_ids); return false;
	    var $parentDIV = $this.parents('tr');
            var $messages = $jq(this).parent().parent().find(".dashboard-agent-loader-spinner"); 

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'data' : $retrieved_ids, 
			'action' : 'homey_child_agent_allocation',
		},
                method: 'POST',
                dataType: "JSON",
                beforeSend: function( ) {
                    $messages.html('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.remove('i');
                        $messages.empty().html('<span style="background:#00FF00;color:#000;">Agent Notified!!</span>');
			$jq('.custom-actions select').val("0");
			$parentDIV.find('td[data-label="agent_notified"]').empty().append(response.msg);	
                    } else {
                        $messages.remove('i');
                        $messages.empty().append('<span style="background:#c31b1b;color:#FFF;">'+response.msg+'</span>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                //complete: function(){
                //    $this.children('i').removeClass(process_loader_spinner);
                //    $this.children('i').addClass(success_icon);
                //}
            });

        });


        /*--------------------------------------------------------------------------
         *  Suggested Houses From Agent and Broker Dashboard
         *  Suggested and Available Houses Search
         * -------------------------------------------------------------------------*/
        $jq( '.suggested-houses #suggested-houses-search').on('click', function(e) { //alert('this');
            e.preventDefault();

            var $this = $jq(this);
            var $suggsted_houses_wrap = $this.parents( '.suggested-houses' );
            var $form = $this.parents( 'form' ); 
            var $messages = $suggsted_houses_wrap.find('.suggested-houses-result');

	    //var $state = $form.find('#state').val(); 
	    //var $city = $form.find('#city').val();
	    var $searchtext = $form.find('#search-text').val(); 
	    var $postcode = $form.find('#postcode').val();
	    var $posts_limit = $form.find('#limit').val(); 
	    var $sort_by = $form.find('#sort_by').val();
            var $paged = $form.find('#paged').val(); 
	    var $reservationid = $suggsted_houses_wrap.find('input[name="reservationid"]').val();
	    //alert( $state + ' ' + $city + ' ' + $posts_limit + ' ' + $sort_by + ' ' + $paged ); 
	    //return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			//'state' : $state,
			//'city' : $city,
			'searchterm' : $searchtext,
			'postcode' : $postcode,
			'limit' : $posts_limit,
			'sort_by' : $sort_by,
			'paged' : $paged, 
			'stype' : 'suggested-houses',
			'action' : 'homey_child_loadmore_listings',
			'reservationid' : $reservationid,
		},
                method: $form.attr('method'),
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if(response.indexOf('No Properties') == -1){
                        $messages.html('<div class="container">'+response+'</div>'); //console.log(response);
			//$messages.html('<p>hello world</p>');
                        $form.find('select').val("");
                    } else {
                        $messages.html(response);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        $jq( '.available-houses-form #avail-houses-search').on('click', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
            var $available_houses_wrap = $this.parents( '.block-body' );
            var $form = $this.parents( 'form' ); 
            var $messages = $available_houses_wrap.find('.available-houses-result');
	    var $errmsgs = $jq('.available-houses-form').find('#avail-form-err');

	    //search variables and validation
	    //var $state = $form.find('#state').val(); 
	    var $city = $form.find('#avail-search-city').val();
	    var $postcode = $form.find('#avail-search-pcode').val();
	    var $nbeds = $form.find('#avail-search-nbeds').val();
	    var $nbaths = $form.find('#avail-search-nbaths').val();
	    var $searchtext = $form.find('#avail-search-text').val(); 
	    //paged variables
	    var $posts_limit = $form.find('#avail-limit').val(); 
	    var $sort_by = $form.find('#avail-sort_by').val();
            var $paged = $form.find('#avail-paged').val();

	    //checks the input based on the following criteria, else error message
	    if( $city == '' && $postcode == '' && $nbeds == '' && $nbaths == '' && $searchtext == '' ){ 
		$errmsgs.empty().append(' Enter At Least One Field To Search'); return false;
	    }
	    if($postcode != ''){ 
		if(!/^[0-9]+$/.test($postcode)){
		   $errmsgs.empty().append(' Enter Numeric Values For PostalCodes'); return false;
		}
	    }
	    if($nbeds != ''){ 
		if(!/^[0-9]+$/.test($nbeds)){
		   $errmsgs.empty().append(' Enter Numeric Values For No. Of Beds [1-9]'); return false;
		}  
	    }
	    if($nbaths != ''){ 
		if(!/^[0-9]+$/.test($nbaths)){
		   $errmsgs.empty().append(' Enter Numeric Values For No. of Baths [1-9]'); return false;
		}
	    } 
	    //alert($city + ' ' + $postcode + ' ' + $nbeds + ' ' + $nbaths + ' ' + $searchtext); 
	    //alert($posts_limit + ' ' + $sort_by + ' ' + $paged); return false;
	    var $reservationid = $available_houses_wrap.find('input[name="reservationid"]').val();

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			//'state' : $state,
			'city' : $city,
			'postcode' : $postcode,
			'beds'	: $nbeds,
			'baths' : $nbaths,
			'searchterm' : $searchtext,
			'limit' : $posts_limit,
			'sort_by' : $sort_by,
			'paged' : $paged,
			'stype' : 'available-houses',
			'action' : 'homey_child_loadmore_listings',
			'reservationid' : $reservationid,
		},
                method: $form.attr('method'),
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if(response.indexOf('No Properties') == -1){
                        $messages.html('<div class="container">'+response+'</div>'); //console.log(response);
			//$messages.html('<p>hello world</p>');
                        $form.find('#avail-search-city').val(""); 
			$form.find('#avail-search-pcode').val('');
			$form.find('#avail-search-nbeds').val('');
			$form.find('#avail-search-nbaths').val('');
			$form.find('#avail-search-text').val('');
                    } else {
                        $messages.html(response);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });


        /*--------------------------------------------------------------------------
         *  From Agent and Broker Dashboard Opens Modal 
	 *  Suggested and Available Houses
         * -------------------------------------------------------------------------*/
        $jq('.suggested-houses-result').on('click', '.openMessageModal', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
	    var $listingId = $this.parent().find('input[name="listingid"]').val();
	    var $suggsted_houses_wrap = $this.parents( '.suggested-houses' );
	    var $parent = $suggsted_houses_wrap.find('.suggested-houses-result');
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($listingId); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingId,
			'action' : 'homey_child_display_host_contact_modal',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if(response.indexOf('No Modal') == -1){
			$parent.children('.custom-modal-contact-host').remove();
			//console.log(response);
			$parent.append(response); 
			$parent.find('.custom-modal-contact-host')
			       .css({"display":"block","top":"143px","opacity":"0.94","background":"#484545"});
			$this.children('i').remove(); 
			$detailpane.find('span.label').text('IN PROGRESS');
			$detailpane.find('span.label').addClass('label-secondary').removeClass('label-danger');
                    } else {
                        console.log(response);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                //complete: function(){
                //    $this.children('i').removeClass(process_loader_spinner);
                //    $this.children('i').addClass(success_icon);
                //}
            });
        });

	/* -------------------------------------------------------------------------
	 * Suggested Houses tab Modal close on Close Button Click
	 * -------------------------------------------------------------------------*/
	$jq('.suggested-houses-result').on('click', '.custom-modal-contact-host .close', function(){ 

	    var $this = $jq(this); 
	    var $modal_parent = $this.parents( '.custom-modal-contact-host' );
	    //remoe the modal
	    $modal_parent.remove();
	});

        /*--------------------------------------------------------------------------
         *  Suggested Houses tab Contact listing host
         * -------------------------------------------------------------------------*/
        $jq( '.suggested-houses-result').on('click', '.contact_listing_host_ajax', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
            var $host_contact_wrap = $this.parents( '.host-contact-wrap' );
	    var $messages = $host_contact_wrap.find('.homey_contact_messages');
            var $form = $this.parents( 'form' ); 
	    var $listingid = $form.find('input[name="listingid"]').val(); 
	    var $topDiv = $jq('.suggested-houses-result #tr-id-'+$listingid);
	    var $reservationId = $jq( '.suggested-houses-result').parent().find('input[name="reservationid"]').val();
	    //alert($reservationId); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'reservationid' : $reservationId,
			'action' : 'homey_child_agent_contact_host_submission',
		},
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.msg); 
                        $form.find('input[name="phone"]').val('');
                        $form.find('textarea').val(''); 
			$topDiv.css('background','#d0c8c8');
			$topDiv.find('button').attr('disabled','true');
                    } else {
                        $messages.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        /*--------------------------------------------------------------------------
         *  From Agent and Broker Dashboard Opens Modal 
	 *  Available Houses tab
         * -------------------------------------------------------------------------*/
        $jq('.available-houses-result').on('click', '.openMessageModal', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
	    var $listingId = $this.parent().find('input[name="listingid"]').val();
	    var $available_houses_wrap = $this.parents( '.available-houses-result' );
	    var $parent = $available_houses_wrap;
	    //alert($listingId); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingId,
			'action' : 'homey_child_display_host_contact_modal',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if(response.indexOf('No Modal') == -1){
			$parent.children('.custom-modal-contact-host').remove();
			//console.log(response);
			$parent.append(response); 
			$parent.find('.custom-modal-contact-host')
			       .css({"display":"block","top":"143px","opacity":"0.94","background":"#484545"});
			$this.children('i').remove();
                    } else {
                        console.log(response);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                //complete: function(){
                //   $this.children('i').removeClass(process_loader_spinner);
                //   $this.children('i').addClass(success_icon);
                //}
            });
        });

	/* -------------------------------------------------------------------------
	 * Available Houses tab Modal close on Close Button Click
	 * -------------------------------------------------------------------------*/
	$jq('.available-houses-result').on('click', '.custom-modal-contact-host .close', function(){ 

	    var $this = $jq(this); 
	    var $modal_parent = $this.parents( '.custom-modal-contact-host' );
	    //remoe the modal
	    $modal_parent.remove();
	});

        /*--------------------------------------------------------------------------
         *  Available Houses tab Contact Listing Host
         * -------------------------------------------------------------------------*/
        $jq( '.available-houses-result').on('click', '.contact_listing_host_ajax', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
            var $host_contact_wrap = $this.parents( '.host-contact-wrap' );
            var $form = $this.parents( 'form' ); 
            var $messages = $host_contact_wrap.find('.homey_contact_messages'); 
	    var $listingid = $form.find('input[name="listingid"]').val(); 
	    var $topDiv = $jq('.available-houses-result #tr-id-'+$listingid);  
	    var $reservationId = $jq( '.available-houses-result').parent().find('input[name="reservationid"]').val();
	    //alert($reservationId); return false;
	    //console.log($topDiv); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'reservationid' : $reservationId,
			'action' : 'homey_child_agent_contact_host_submission',
		},
                method: $form.attr('method'),
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.msg);
                        $form.find('input[name="phone"]').val('');
                        $form.find('textarea').val('');
			$topDiv.css('background','#d0c8c8');
			$topDiv.find('button').attr('disabled','true');
                    } else {
                        $messages.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });
        });

        /*-----------------------------------------------------------*
         *  Selected Houses Tab On Click From Agent/Broker Dashboard
         * ----------------------------------------------------------*/
        $jq( '#selected-check-tab').on('click', function(e) { //alert('this');
            e.preventDefault();
	    
	    var $this = $jq(this);
	    var $reservationId = $jq('#selected-houses').find('input[name="reservationid"]').val();
	    //alert($reservationId); return false;
	    var $targetDIV = $jq('#selected-houses').find('.selected-houses-result');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationId,
			'action' : 'homey_child_fetch_selected_houses',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
                    $targetDIV.empty().append(response);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
        });

	/* --------------------------------------------------------------------------
	 * Button Click on Selected Houses Tab 
         * was done for Agent, now deprecated.
	 * -------------------------------------------------------------------------*/
	$jq('.selected-houses-result').on('click', '.accepted', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
            var $reservationid = $jq('#selected-houses').find('input[name="reservationid"]').val();
	    //alert($listingid + ' ' + $reservationid); return false;
	    var $targetDIV = $jq('.approval-div').find('.approval-houses-result');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(),
			'listingid' : $listingid,  
			'reservationid' : $reservationid,
			'type' : 'accepted',
			'action' : 'homey_child_reserv_modification_via_client_response',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
                    $targetDIV.empty().append(response);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	$jq('.selected-houses-result').on('click', '.denied', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
            var $reservationid = $jq('#selected-houses').find('input[name="reservationid"]').val();
	    //alert($listingid + ' ' + $reservationid); return false;
	    var $targetDIV = $jq('.denial-div').find('.denial-houses-result');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingid,
			'reservationid' : $reservationid,
			'type' : 'denied',
			'action' : 'homey_child_reserv_modification_via_client_response',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
                    $targetDIV.empty().append(response);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	/* -------------------------------------------------------------------------
	 * Button Click on Suggested and Available Houses Tab For Approved Houses
	 * -------------------------------------------------------------------------*/
	$jq('.suggested-houses-result').on('click', '.add-property-to-approval', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val(); 
	    var $suggsted_houses_wrap = $this.parents( '.suggested-houses' );
	    var $parent = $suggsted_houses_wrap.find('.suggested-houses-result');
	    var $topDiv = $jq('.suggested-houses-result #tr-id-'+$listingid);
	    var $reservationid = $jq('.suggested-houses-result').parent().find('input[name="reservationid"]').val();
	    var $targetDIV = $jq('.approval-div').find('.approval-houses-result');
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($reservationId); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(),
			'listingid' : $listingid,  
			'reservationid' : $reservationid,
			'type' : 'accepted',
			'action' : 'homey_child_reserv_modification_via_client_response', 
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
		    $topDiv.css('background','#d0c8c8');
		    $topDiv.find('button').attr('disabled','true'); 
		    $detailpane.find('span.label').text('IN PROGRESS');
		    $detailpane.find('span.label').addClass('label-secondary').removeClass('label-danger');
                    $targetDIV.empty().append(response);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	$jq('.available-houses-result').on('click', '.add-property-to-approval', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val(); 
	    var $topDiv = $jq('.available-houses-result #tr-id-'+$listingid);  
	    var $reservationid = $jq('.available-houses-result').parent().find('input[name="reservationid"]').val();
	    var $targetDIV = $jq('.approval-div').find('.approval-houses-result');
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($reservationId); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(),
			'listingid' : $listingid,  
			'reservationid' : $reservationid,
			'type' : 'accepted',
			'action' : 'homey_child_reserv_modification_via_client_response', 
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
		    $topDiv.css('background','#d0c8c8');
		    $topDiv.find('button').attr('disabled','true'); 
		    $detailpane.find('span.label').text('IN PROGRESS');
		    $detailpane.find('span.label').addClass('label-secondary').removeClass('label-danger');
                    $targetDIV.empty().append(response);
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	/* -------------------------------------------------------------------------
	 * Button Click on Selected Houses Host Tab
	 * -------------------------------------------------------------------------*/
	$jq('.selected-houses-host-result').on('click', '.accepted', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
            var $reservationid = $jq('#selected-houses-host').find('input[name="reservationid"]').val();
	    //alert($listingid + ' ' + $reservationid); return false;
	    var $targetDIV = $jq('.approval-div').find('.approval-houses-result'); 
	    var $parentDIV = $this.parents('tr'); 
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($reservationid + ' ' + $listingid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(),
			'listingid' : $listingid,  
			'reservationid' : $reservationid,
			'type' : 'accepted',
			'action' : 'homey_child_reserv_modification_via_client_response',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
                    $targetDIV.empty().append(response); 
		    $parentDIV.css('background','#d0c8c8');
		    $parentDIV.find('button').attr('disabled','true');
		    //$detailpane.find('span.label').text('CONFIRMED');
		    //$detailpane.find('span.label').addClass('label-warning').removeClass('label-secondary');
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	$jq('.selected-houses-host-result').on('click', '.denied', function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
            var $reservationid = $jq('#selected-houses-host').find('input[name="reservationid"]').val();
	    //alert($listingid + ' ' + $reservationid); return false;
	    var $targetDIV = $jq('.denial-div').find('.denial-houses-result');
	    var $parentDIV = $this.parents('tr');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingid,
			'reservationid' : $reservationid,
			'type' : 'denied',
			'action' : 'homey_child_reserv_modification_via_client_response',
		},
                method: 'POST',
                dataType: "html",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    //console.log(response);
                    $targetDIV.empty().append(response);
		    $parentDIV.css('background','#d0c8c8');
		    $parentDIV.find('button').attr('disabled','true');
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});


        /*--------------------------------------------------------------------------
         *  Assigning an agent for a property by broker
         * -------------------------------------------------------------------------*/
        $jq('.custom-actions-addln select[name="homey-listingagents-brokers-dashboard"]').on('change', function(e) { //alert('this');
            e.preventDefault();

            var $this = $jq(this);
	    var $retrieved_ids = $this.val(); 
	    // if ids = 0,return
            if ($retrieved_ids == 0){ return false; }
	    //alert($retrieved_ids); return false;
	    //alert($this.children("option:selected").text()); return false;
            var $messages = $jq(this).parent().parent().find(".dashboard-agent-loader-spinner"); 
	    var $mainpropDIV = $this.parents('tr');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'data' : $retrieved_ids, 
			'action' : 'homey_child_property_agent_allocation',
		},
                method: 'POST',
                dataType: "JSON",
                beforeSend: function( ) {
                    $messages.html('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.remove('i');
                        $messages.empty().html('<span style="color:#0000FF"><i class="fa fa-check"></i>Agent Assigned!!</span>');
			setTimeout(function(){ 
			  $mainpropDIV.find('span.agent').text($this.children("option:selected").text());
			  $jq('.custom-actions-addln select').val("0");
			  $messages.empty();
			}, 3000);
                    } else {
                        $messages.remove('i');
                        $messages.empty().append('<span style="color:#FF0000">Agent Could Not Be Assigned</span>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                //complete: function(){
                //    $this.children('i').removeClass(process_loader_spinner);
                //    $this.children('i').addClass(success_icon);
                //}

            });

        });

        /*--------------------------------------------------------------------------
         *  Change of Property type from Brokers Dashboard
         * -------------------------------------------------------------------------*/
        $jq('.custom-actions button[name="change-property-type"]').on('click', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
	    var $parentDIV = $this.parents('td'); 
            var $messages = $parentDIV.find(".dashboard-agent-loader-spinner"); 
	    var $retrieved_id = $this.attr('data-id');
	    var $retrieved_val = $this.attr('data-val'); 
	    var $mainpropDIV = $this.parents('tr');
	    //alert($retrieved_id + ' ' + $retrieved_val); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'data' : $retrieved_id, 
			'type' : $retrieved_val,
			'action' : 'homey_child_property_change_type',
		},
                method: 'POST',
                dataType: "JSON",
                beforeSend: function( ) {
                    $messages.html('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) { 
                    if( response.success ) {
                        $messages.remove('i');
                        $messages.empty().append('<span style="color:#0000FF"><i class="fa fa-check"></i>'+response.msg+'</span>');
			setTimeout(function(){
			  if($retrieved_val == 'private'){
			    $mainpropDIV.find('button.change-listing-type').children('i').addClass('fa-unlock-alt').removeClass('fa-lock');
			    $mainpropDIV.find('button.change-listing-type').attr('data-original-title', 'Make Public'); 			  
			    $mainpropDIV.find('button.change-listing-type').attr('data-val', 'public');
			    $mainpropDIV.find('span.property-type').text('PRIVATE'); $messages.empty();
			  }else{
			    $mainpropDIV.find('button.change-listing-type').children('i').addClass('fa-lock').removeClass('fa-unlock-alt');
			    $mainpropDIV.find('button.change-listing-type').attr('data-original-title', 'Make Private'); 			  
			    $mainpropDIV.find('button.change-listing-type').attr('data-val', 'private');
			    $mainpropDIV.find('span.property-type').text('PUBLIC'); $messages.empty();
			  }
			}, 3000);			
			//setTimeout(window.location.reload(),3000);
                    } else {
                        $messages.remove('i');
                        $messages.empty().append('<span style="color:#FF0000">Property Type Not Changed!!</span>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) { 
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                //complete: function(){
                    //$messages.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                //}
            });
        });

        /*--------------------------------------------------------------------------
         *  Change of Approval of Property from Brokers Dashboard
         * -------------------------------------------------------------------------*/
        $jq('.custom-actions-top button[name="property-approval"]').on('click', function(e) { 
            e.preventDefault();

            var $this = $jq(this);
	    var $parentDIV = $this.parents('td'); 
            var $messages = $parentDIV.find(".dashboard-agent-loader-spinner"); 
	    var $retrieved_id = $this.attr('data-id');
	    var $mainpropDIV = $this.parents('tr');
	    //alert($retrieved_id); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'data' : $retrieved_id, 
			'action' : 'homey_child_broker_property_approval',
		},
                method: 'POST',
                dataType: "JSON",
                beforeSend: function( ) {
                    $messages.html('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) { 
                    if( response.success ) {
                        $messages.remove('i');
                        $messages.empty().append('<span style="color:#000"><i class="fa fa-check"></i>'+response.msg+'</span>');
			$jq('.custom-actions-top').css({ 'display':'none' }); 
			setTimeout(function(){ 
			  $mainpropDIV.find('span.label-warning').text('Published'); 
			  $mainpropDIV.find('span.label-warning').addClass('label-success').removeClass('label-warning'); 
			  $messages.empty();
			}, 3000);
			//setTimeout(window.location.reload(),3000);
                    } else {
                        $messages.remove('i');
                        $messages.empty().append('<span style="color:#FF0000">Property Type Not Changed!!</span>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) { 
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                //complete: function(){
                    //$messages.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                //}
            });
        });


        /*--------------------------------------------------------------------------
         *  Reservation status As Assigned By Broker 
         *  This will override statuses set by Agents. 
         * -------------------------------------------------------------------------*/
        //change status to declined
	$jq('.btn-cancel').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this);
	    var $parent = $this.parents('.dashboard-area');
	    var $reservationid = $parent.find('#resrv_id').val(); 
	    var $statusval = 'declined';
	    var $messages = $this.parent().find('.messages');
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'statusval' : $statusval,			
			'action' : 'homey_child_change_reservation_status',
		},
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.message);
                    } else {
                        $messages.empty().append(response.message);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
		    setTimeout(window.location.reload(),2000);
                }
            });
	}); 

	//change status to confirm
	$jq('.btn-confirm').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this);
	    var $parent = $this.parents('.dashboard-area');
	    var $reservationid = $parent.find('#resrv_id').val(); 
	    var $statusval = 'completed';
	    var $messages = $this.parent().find('.messages');
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'statusval' : $statusval,
			'action' : 'homey_child_change_reservation_status',
		},
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().html(response.message);	
                    } else {
                        $messages.empty().html(response.message);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
		    setTimeout(window.location.reload(),2000);
                }
            });
	});

	//change status to confirm
	$jq('.btn-no-activity').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this);
	    var $parent = $this.parents('.dashboard-area');
	    var $reservationid = $parent.find('#resrv_id').val(); 
	    var $statusval = 'no_activity';
	    var $messages = $this.parent().find('.messages');
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'statusval' : $statusval,
			'action' : 'homey_child_change_reservation_status',
		},
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().html(response.message);	
                    } else {
                        $messages.empty().html(response.message);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
		    setTimeout(window.location.reload(),2000);
                }
            });
	});


	//change status to cancel and archive
	$jq('.btn-archive').on('click', function(e) { 
	    e.preventDefault();
            var $this = $jq(this);
	    var $parent = $this.parents('.dashboard-area');
	    var $reservationid = $parent.find('#resrv_id').val(); 
	    var $statusval = 'cancelled';
	    var $messages = $this.parent().find('.messages');
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'statusval' : $statusval,
			'action' : 'homey_child_change_reservation_status',
		},
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().html(response.message);
                    } else {
                        $messages.empty().html(response.message);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
		    setTimeout(window.location.reload(),2000);
                }
            });
	});

        /*--------------------------------------------------------------------------
         *  Create a new user from Agent Dashboard
         * -------------------------------------------------------------------------*/
        //change status to declined
	$jq('button[name="create-new-user"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this);
            var $form = $this.parents( 'form' ); 
	    var $customername = $form.find('input[name="register-user-name"]').val();
	    var $customeremail = $form.find('input[name="register-user-email"]').val();
	    var $customerphone = $form.find('input[name="register-user-phone"]').val();
	    var $reservationid = $form.find('input[name="reservationid"]').val();
            var $messages = $form.parent();
	    //alert($customername +''+ $customeremail +'' + $customerphone + '' + $reservationid); 
	    //return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'customername' : $customername,
			'customeremail' : $customeremail,
			'customerphone' : $customerphone,
			'reservationid' : $reservationid,			
			'action' : 'homey_child_agent_register_customer',
		},
                method: 'POST',
                dataType: "JSON",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.empty().append(response.msg);
                    } else {
                        $messages.empty().append(response.msg);
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                //complete: function(){
                //    $this.children('i').removeClass(process_loader_spinner);
                //    $this.children('i').addClass(success_icon);
		//    //setTimeout(window.location.reload(),2000);
                //}
            });
	}); 


        /*--------------------------------------------------------------------------
         *  Confiration from Renter on the approved properties
         * -------------------------------------------------------------------------*/
	$jq('button[name="user-confirmation"]').click(function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
	    var $parentDIV = $this.parents('.content');
	    var $cparent = $this.parents('tr');
            var $reservationid = $parentDIV.find('input[name="reservationid"]').val();
	    var $targetDIV = $this.parent().parent(); // the td element
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($listingid + ' ' + $reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingid,
			'reservationid' : $reservationid,
			'action' : 'homey_child_confirm_modification_via_customer_response',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
                       $targetDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>'); 
		       $targetDIV.find('button').text('Confirmed');
		       $targetDIV.find('button').attr('disabled', true);
		       $cparent.addClass('selected');
		       //set all other sections to disbled apart from the current
		       $parentDIV.find('tr').each(function(){ 
			  if($jq(this).hasClass('selected')){
			    $jq(this).css({ 'background':'#f7e1e4' });
			  } else {
			    $jq(this).css({ 'background':'#dfdcdc' });
			    $jq(this).find('button[name="user-confirmation"]').attr('disabled', true);
			    $jq(this).find('button[name="user-confirmation"]').css({ 'background':'#dfdcdc' });
			  }
		       });
		       $detailpane.find('span.label').text('CONFIRMED');
		       $detailpane.find('span.label').addClass('label-warning').removeClass('label-secondary');
		    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

        /*--------------------------------------------------------------------------
         *  Confiration from Broker/Agent After Renter Approval 
	 *  Will Book the lead.
         * -------------------------------------------------------------------------*/
	$jq('button[name="agent-broker-final-confirmation"]').click(function(){ 

	    var $this = $jq(this); 
	    var $listingid = $this.parent().find('input[name="listingid"]').val();
	    var $parentDIV = $this.parents('.content');
	    var $cparent = $this.parents('tr');
            var $reservationid = $parentDIV.find('input[name="reservationid"]').val();
	    var $targetDIV = $this.parent().parent(); // the td element
	    var $detailpane = $jq('.dashboard-area .block-head');
	    //alert($listingid + ' ' + $reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'listingid' : $listingid,
			'reservationid' : $reservationid,
			'action' : 'homey_child_final_confirmation_agent_response',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
                       $targetDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>'); 
		       $targetDIV.find('button').text('BOOKED');
		       $targetDIV.find('button').attr('disabled', true);
		       $cparent.css({'background':'#a1cc72'});
		       $detailpane.find('span.label').text('BOOKED');
		       $detailpane.find('span.label').addClass('label-success').removeClass('label-warning');
		    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});


        /* ------------------------------------------------------------------------ */
        /*  Homey regsiter
        /* ------------------------------------------------------------------------ */
        $jq('#modal-register .homey-child-register-button').on('click', function(e){ 
            e.preventDefault();
            var current = $jq(this); 
	    var $form = current.parents('form');
            var $messages = $jq('.homey_register_messages'); 

            $jq.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: $form.serialize(),
                beforeSend: function (){
                    $messages.empty().append('<p class="success text-success"> '+ login_sending +'</p>');
                },
                success: function( response ) {
                    if( response.success ) {
                        $messages.empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
			//reset the interface
			$form.find('input').val(''); 
	    		$form.find('ul li').each(function(){ 
	      		   if($jq(this).attr('data-original-index')  == 1){ $jq(this).find('a').trigger('click'); }
	    		});
			$form.find('input[name="term_condition"]').prop('checked',false);
                        $jq('.homey_login_messages').empty().append('<p class="success text-success"><i class="fa fa-check"></i> '+ response.msg +'</p>');
                        $jq('#modal-register').modal('hide');
                        $jq('#modal-login').modal('show');
			setTimeout(function(){ $jq('.homey_login_messages').empty() }, 8000);
                    } else {
                        $messages.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
		//complete:function(){
		//   $jq('.homey_login_messages').empty();
                //}
            });

        });

	/*---------------------------------------------------------------------------------------*
	 * Become A Host 
	 *---------------------------------------------------------------------------------------*/
	// on click of "become-a-host" button
	$jq('button[name="become-a-host"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $parentDIV = $this.parent(); 
	    var $renterid = $parentDIV.find('input[name="renterid"]').val(); 
	    //alert(renterid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'renterid' : $renterid,
			'action' : 'homey_child_renter_to_host_conversion',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
                       $parentDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>'); 
		       $parentDIV.find('button').text('CHANGED TO HOST');
		       $parentDIV.find('button').attr('disabled', true);
                    } else {
                       $parentDIV.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	/*---------------------------------------------------------------------------------------*
	 * Delete An Agent From Broker Dashboard 
	 *---------------------------------------------------------------------------------------*/
	// on click of "become-a-host" button
	$jq('button[name="btn-delete-agent"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $trDIV = $this.parents('tr');
	    var $parentDIV = $this.parent(); 
	    var $agentid = $parentDIV.find('input[name="agentid"]').val(); 
	    var $brokerid = $parentDIV.find('input[name="brokerid"]').val(); 
	    //alert($agentid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'agentid' : $agentid,
			'brokerid' : $brokerid,
			'action' : 'homey_child_broker_dashboard_agent_deletion',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $parentDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>');
		       setTimeout(function(){ $trDIV.remove() }, 4000);
                    } else {
                       $parentDIV.empty().append('<p class="error text-danger"><i class="fa fa-close"></i> '+ response.msg +'</p>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });

	});

	/*---------------------------------------------------------------------------------------*
	 * Reject A Lead - From Agent Dashboard 
	 *---------------------------------------------------------------------------------------*/
	$jq('button[name="new-lead-agent-denial"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $trDIV = $this.parents('tr');
	    var $parentDIV = $this.parent(); 
	    var $reservationid = $parentDIV.find('input[name="reservationid"]').val();
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'action' : 'homey_child_new_lead_agent_denial',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $parentDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>');
		       setTimeout(function(){ $trDIV.remove() }, 4000);
                    } else {
                       $parentDIV.append('<div class="error text-danger" style="font-size:9px;"><i class="fa fa-close"></i> '+ response.msg +'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
	});

	/*---------------------------------------------------------------------------------------*
	 * Accept A Lead - From Agent Dashboard 
	 *---------------------------------------------------------------------------------------*/
	$jq('button[name="new-lead-agent-accept"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $trDIV = $this.parents('tr');
	    var $parentDIV = $this.parent(); 
	    var $reservationid = $parentDIV.find('input[name="reservationid"]').val();
	    //alert($reservationid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			//'form_data' : $form.serialize(), 
			'reservationid' : $reservationid,
			'action' : 'homey_child_new_lead_agent_accept',
		},
                method: 'POST',
                dataType: "json",

                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $parentDIV.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>');
		       setTimeout(function(){ window.location.reload() }, 3000);
                    } else {
                       $parentDIV.append('<div class="error text-danger" style="font-size:9px;"><i class="fa fa-close"></i> '+ response.msg +'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
	});

	/*---------------------------------------------------------------------------------------*
	 * Generate a rule from Broker Dashboard 
	 *---------------------------------------------------------------------------------------*/
	$jq('button[name="routing-rules-generate"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $parentDIV = $this.parents('dashboard-form-inline'); 
	    var $form = $this.parents('form');
	    var $messages = $jq('.routing-messages');
	    //alert($form.serialize()); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'action' : 'homey_child_generate_routing_rules',
		},
                method: 'POST',
                dataType: "json",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
		    $messages.append('<div style="font-size:10px;float:right;color:#000;font-weight:bold;">Checking Rules...</div>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $form.find('input').val(''); 
		       $form.find('select').val("*");
		       $messages.empty().append(
			 '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>'
		       );
		       setTimeout(function(){ window.location.reload() }, 3000);
                    } else {
                       $messages.empty().append(
			 '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>'
		       );
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
	});

	/*---------------------------------------------------------------------------------------*
	 * Enable/ Disable A Rule From Broker Dashboard 
	 *---------------------------------------------------------------------------------------*/
	$jq('a[name="btn_enable_rule"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $parentDIV = $this.parent(); 
	    var $ruleid = $parentDIV.find('input[name="ruleid"]').val()
	    var $messages = $parentDIV;
	    //alert($ruleid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'ruleid' : $ruleid, 
			'status' : 'enabled',
			'action' : 'homey_child_rulestatus_change_broker_dashboard',
		},
                method: 'POST',
                dataType: "json",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $messages.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>');
		       setTimeout(function(){ window.location.reload() }, 3000);
                    } else {
                       $messages.append('<div class="error text-danger" style="font-size:9px;"><i class="fa fa-close"></i> '+ response.msg +'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
	});

	$jq('a[name="btn_disable_rule"]').on('click', function(e) { 
	    e.preventDefault();

	    var $this = $jq(this); 
	    var $parentDIV = $this.parent(); 
	    var $ruleid = $parentDIV.find('input[name="ruleid"]').val()
	    var $messages = $parentDIV;
	    //alert($ruleid); return false;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'ruleid' : $ruleid, 
			'status' : 'disabled',
			'action' : 'homey_child_rulestatus_change_broker_dashboard',
		},
                method: 'POST',
                dataType: "json",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response);
		       $messages.append('<div style="font-size:10px;color:#000;font-weight:bold;">'+response.msg+'</div>');
		       setTimeout(function(){ window.location.reload() }, 3000);
                    } else {
                       $messages.append('<div class="error text-danger" style="font-size:9px;"><i class="fa fa-close"></i> '+ response.msg +'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    //$this.children('i').addClass(success_icon);
                }
            });
	});

	/**----------------------------------------------------------------------* 
	 * Add a new lead sections from broker dashboard 
         * ----------------------------------------------------------------------*/
	 //on click of properties populate the other input fields. 
         $jq('.homey-new-lead-add-wrapper #properties li').click(function(e){ 
	    e.preventDefault();	

	    var $this = $jq(this); 
	    var $propid = $this.val(); 
	    //alert($propid); return false;
	    var $parentDIV = $this.parents('.form-group'); 
	    var $messages = $parentDIV;

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'propid' : $propid, 
			'request_type' : 'property_det',
			'action' : 'homey_child_add_lead_broker_dashboard',
		},
                method: 'POST',
                dataType: "json",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.append('<div class="loaderspin" style="float:right;margin-top:-50px;">'+
				 '<i class="fa-left '+process_loader_spinner+'"></i></div>');
                },
                success: function(response) {
		    if( response.success ) {
                       //console.log(response); 
		       $jq('.homey-new-lead-add-wrapper #where').val(response.nam); 
		       $jq('.homey-new-lead-add-wrapper #pcode').val(response.loc);
		       $jq('.homey-new-lead-add-wrapper #address').val(response.addr);
		       $jq('.homey-new-lead-add-wrapper #propertyid').val($propid);
                    } else {
                       $messages.append(
			  '<div class="error text-danger" style="font-size:9px;"><i class="fa fa-close"></i> '+ response.msg +'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                },
                complete: function(){
                    //$this.children('i').removeClass(process_loader_spinner);
		    $this.children('.loaderspin').remove();
                    //$this.children('i').addClass(success_icon);
                }
            });
         });

	 // check if dates are available. 
         $jq('.homey-new-lead-add-wrapper').on('click', 'button[name="homey_new_lead_save"]', function(e){ //alert('this');
	    e.preventDefault();	
	
	    var $this = $jq(this); 
            var $homey_addlead_wrap = $this.parents( '.homey-new-lead-add-wrapper' );
            var $form = $this.parents( 'form' ); var $checkform = 0;
            var $topmessages = $homey_addlead_wrap.find('.homey_add_new_lead_messages_top'); 
            var $bottommessages = $homey_addlead_wrap.find('.homey_add_new_lead_messages_bottom');
	    var $redirect = $homey_addlead_wrap.find('input[name="redirect_to"]').val();
	    //alert($form.serialize()); return false; 

	    //check data for each field
	    var $inputs = $homey_addlead_wrap.find( 'input' ); 
	    $inputs.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
	    }); 
	    var $selects = $homey_addlead_wrap.find( 'select' ); 
	    $selects.each(function(){ 
		$jq(this).parent().children('p').remove();
		if( $jq(this).children('option:selected').val() == '' ){
		  $jq(this).parent().append('<p><span style="color:#FF0000">Please Enter Value For The Field</span></p>');
		  $checkform = 1;
		}
	    });
	    if($checkform == 1){ return false; }   
	    //alert('form ok submitting'); alert($form.serialize()); return false; 

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'form_data' : $form.serialize(), 
			'request_type' : 'submit_form',			
			'action' : 'homey_child_add_lead_broker_dashboard',
		},
                method: $form.attr('method'),
                dataType: "JSON",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $topmessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div style="font-size:12px;color:#000;font-weight:bold;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                        $form.find('input').val('');
			$form.find('select').val('');
                        //$form.find('textarea').val('');
			setTimeout(function(){ window.location.replace($redirect) }, 3000);
                    } else {
                        $topmessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $bottommessages.empty().append(
			  '<div class="error text-danger" style="font-size:12px;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                    if(homey_reCaptcha == 1) {
                        homeyReCaptchaReset();
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
                }
            });

	 }); 

	/**-----------------------------------------------------------------------*
	 * Generate Lead Allocation Via Rules
	 *------------------------------------------------------------------------*/
	 // check if dates are available. 
         $jq('.dashboard-area button[name="allocate-reservation"]').click(function(e){ 
	    e.preventDefault();	
	    //alert('this'); return false;
	    var $this = $jq(this); 
	    var $messages = $jq('.dashboard-area .block-title');

            $jq.ajax({
                url: ajaxurl,
                data: { 
			'allocate' : true, 			
			'action'   : 'homey_child_allocate_reservation_byrules',
		},
                method: 'POST',
                dataType: "JSON",
                beforeSend: function( ) {
                    $this.children('i').remove();
                    $this.prepend('<i class="fa-left '+process_loader_spinner+'"></i>');
                },
                success: function(response) {
                    if( response.success ) {
                        $messages.append('<div style="font-size:12px;color:#000;font-weight:bold;margin-top:35px;text-align:right;"><i class="fa fa-check"></i>'+response.msg+'</div>');
                    } else {
                        $messages.append('<div class="error text-danger" style="font-size:12px;margin-top:35px;text-align:right;"><i class="fa fa-close"></i>'+response.msg+'</div>');
                        $this.children('i').removeClass(process_loader_spinner);
                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log('Error:'+err.Message);
                },
                complete: function(){
                    $this.children('i').removeClass(process_loader_spinner);
                    $this.children('i').addClass(success_icon);
		    setTimeout(function(){ window.location.reload(); }, 3000);
                }
            });

	 }); 


}); 
