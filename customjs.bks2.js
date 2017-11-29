/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

$(document).ready(function(){
 $('.calendar-section #select-region').trigger('change');
});

$(document).on("click", '#exampleModalLong .game-full', function(event) {
   if( $('#exampleModalLong input[type="checkbox"][name="acceptRules"]').prop('checked') == false ){
     alert('Please Agree To the Rules and Regulations and Waiver.'); return false;
   }
});

//view button click on home page
$('.page-template-frontpage-tmp .calendar-section .calendar-table .view-btn').click(function(){ 
  var getSelRegion = $('.page-template-frontpage-tmp .calendar-section #select-region').val(); //alert(getSelRegion);
  var siteurl = window.location.href; 
  window.location.href = siteurl + "games/?region=" + getSelRegion;
});

//games page on load
$('body .page-template-games-tmpl').ready(function(){ 
 //if the url contains a parameter 
 if( window.location.href.indexOf("?") == -1 ){
   $('.game-section .calendar .calendar-day-np input[type="checkbox"]').each(function(){ 
      $(this).css('display', 'none'); 
   });  
   $('.game-section .select-region').trigger('change'); 
   //reset all dates
   $('.game-section .calendar .calendar-day-np input[type="checkbox"][name="dates"]:checked').each(function(){ 
     $(this).prop('checked', false); 
   });
 }
 if( window.location.href.indexOf("?") > -1 ){
   var searchReg = window.location.search.substr(1);
   var region = searchReg.split('='); //alert(region[1]);
   $('.game-section .select-region').val(region[1]).change();
   $('.game-section .calendar .calendar-day-np input[type="checkbox"]').each(function(){ 
      $(this).css('display', 'none'); 
   });  
   $('.game-section .select-region').trigger('change'); 
   //reset all dates
   $('.game-section .calendar .calendar-day-np input[type="checkbox"][name="dates"]:checked').each(function(){ 
     $(this).prop('checked', false); 
   });
 }
});

//changes to the table on games page
$('.game-section .calendar .calendar-day-np').click(function(){ 
  var $chkbox = $(this).find('input[type="checkbox"]');
  var status = $chkbox.prop('checked'); //alert(status); 
  if(status == true){ $chkbox.prop('checked', false); $(this).css('background', '#ffbea2'); }
  if(status == false){ $chkbox.prop('checked', true); $(this).css('background', '#66b8cd'); }
  $('.game-section .select-region').trigger('change');
});

//changes to the table on games page
$('.game-section .calendar .calendar-day-np').mouseenter(function(){ 
  var $chkbox = $(this).find('input[type="checkbox"]');
  var status = $chkbox.prop('checked'); //alert(status); 
  if(status == false){ 
    if($(this).attr('disabled') == 'disabled'){ return false; }
    $(this).css('background', '#ECECEC');
  }
});

//changes to the table on games page
$('.game-section .calendar .calendar-day-np').mouseleave(function(){ 
  var $chkbox = $(this).find('input[type="checkbox"]');
  var status = $chkbox.prop('checked'); //alert(status); 
  if(status == false){ 
    if($(this).attr('disabled') == 'disabled'){ return false; }
    $(this).css('background', '#ffbea2');
  }
});


//------------
// home page
//------------
//uses the change region to revamp games table on home page
$('.calendar-section #select-region').change(function(){
   var getRegionID = $('.calendar-section #select-region').val(); //alert(getRegionID);
   var getRegionText = $('.calendar-section #select-region option:selected').text(); 

   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method : "POST",  
      dataType: "html",
      data:{ 
	'action':'action_newrevamptable',
        'regionID':getRegionID,
      },
      success:function(response){
          console.log('success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.calendar-section .changetable').html(tablehtml);
	  $(".calendar-table").find(".padding0").html( response );
      }
   }); 

   //ajax_url ='http://lab-1.sketchdemos.com/P943_Basketball/wp-admin/admin-ajax.php';
   /*ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
   $.post(ajax_url, {
        action: 'action_revamptable',  
        type:"POST",
        regionID:getRegionID, 
        }, 
	function (response) { 
          $(".calendar-table").find(".padding0").html( response );    
          //console.log(response); //return false;
          //$('.right-ber').html(response);                    
   	}
    );*/
});

//checks the click on the anchor of the table
$(document).on("click", '.calendar-table .padding0 .sub-table a', function(event) { 

 var id = $(this).attr('data-id');
 //if disabled, return
 if($(this).attr('disabled') == 'disabled'){ return false; }

 //enter id to revamp the modal
 $.ajax({  
   url: frontendajaxurl.ajaxurl,  
   method : "POST",  
   dataType: "html",
   data:{ 
	'action':'action_modaldata',
        'id':id,
   },
   success:function(response){
     console.log('modal success'); 
     $('.display_modal').html(response); 
     $('.display_modal #exampleModalLong').modal('show');
   }
 }); 
});

//--------------
//games page
//--------------
//uses the change region to revamp games table on home page
$('.game-section .select-region').change(function(){
   var getRegionID = $('.game-section .select-region').val(); //alert(getRegionID);
   var getRegionText = $('.game-section .select-region option:selected').text(); 
   var checkboxes = $('input[type="checkbox"][name="dates"]:checked'); var dates = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); dates += chkbx.value + ','; } //alert(dates);
   var flag = 0; 

   // if no dates selected then revamp table
   if(dates == ""){ 
     flag = 1;  
     $('.game-section .calendar .calendar-day-np input[type="checkbox"][name="dates"]').each(function(){ 
       if( $(this).prop('disabled') != true ){  $(this).prop('checked', true); }
     });
     var checkboxes = $('input[type="checkbox"][name="dates"]:checked'); dates = '';
     for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); dates += chkbx.value + ','; } //alert(dates);
   }

   //refresh the skill level and game type parameters.
   $('input[type="checkbox"][name="skill-level"]:checked').each(function(){ $(this).removeAttr('checked'); });
   $('input[type="checkbox"][name="game-type"]:checked').each(function(){ $(this).removeAttr('checked'); }); 
   
   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method : "POST",  
      dataType: "html",
      data:{ 
	'action':'action_gamesrevamptable',
        'regionID':getRegionID,
	'dates':dates,
      },
      success:function(response){
          console.log('gametable revamp success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.game-section .changetable').html(tablehtml);
	  $(".game-section").find(".changegametable").html( response ); 
	  //reset the table after data is fetched 	         
         if(flag == 1){ 
          flag = 0; 
     	  $('.game-section .calendar .calendar-day-np input[type="checkbox"][name="dates"]').each(function(){ 
            if( $(this).prop('disabled') != true ){  $(this).prop('checked', false); }
          });           
         }

	// calling ajax for the location updation
	$.ajax({  
      		url: frontendajaxurl.ajaxurl,  
		method : "POST",  
      		dataType: "html",
      		data:{ 
		   'action':'action_locationrevamptable',
        	   'regionID':getRegionID,
      		},
      		success:function(response){
          	   console.log('location revamp success');
	  	   $('.game-section').find('#2a .game-tab-cont').html(response);
		}
 	});

      }
   });
});


$(document).on("click", 'input[type="checkbox"][name="locations"]', function(event) { 
   var getRegionID = $('.game-section .select-region').val(); //alert(getRegionID);
   var getRegionText = $('.game-section .select-region option:selected').text(); 
   var checkboxes = $('input[type="checkbox"][name="dates"]:checked'); var dates = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); dates += chkbx.value +','; } //alert(dates);
   //locations that have been checked
   var checkboxes = $('input[type="checkbox"][name="locations"]:checked'); var locations = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); locations += chkbx.value + ','; } //alert(locations);
   //refresh the skill level and game type parameters.
   $('input[type="checkbox"][name="skill-level"]:checked').each(function(){ $(this).removeAttr('checked'); });
   $('input[type="checkbox"][name="game-type"]:checked').each(function(){ $(this).removeAttr('checked'); }); 

   if(locations == ""){
     $('.game-section .select-region').trigger('change'); 
   } else {
   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method : "POST",  
      dataType: "html",

      data:{ 
	'action':'action_gamesrevamptableLocations',
        'regionID':getRegionID,
        'dates':dates,
	'locations':locations,
      },
      success:function(response){
          console.log('locations success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.game-section .changetable').html(tablehtml);
	  $(".game-section").find(".changegametable").html( response );
      }
   });
   }
 
});


$('input[type="checkbox"][name="skill-level"]').click(function(){ 
   var getRegionID = $('.game-section .select-region').val(); //alert(getRegionID);
   var getRegionText = $('.game-section .select-region option:selected').text(); 
   var checkboxes = $('input[type="checkbox"][name="dates"]:checked'); var dates = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); dates += chkbx.value +','; } //alert(dates);
   var checkboxes = $('input[type="checkbox"][name="skill-level"]:checked'); var skilllevels = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); skilllevels += chkbx.value +','; } //alert(skilllevels);  
   var checkboxes = $('input[type="checkbox"][name="game-type"]:checked'); var gametypes = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); gametypes += chkbx.value +','; } //alert(gametypes); 

   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method : "POST",  
      dataType: "html",
      data:{ 
	'action':'action_gamesrevamptableGametypes',
        'regionID':getRegionID,
	'dates':dates,
	'skills':skilllevels,
	'gametypes':gametypes,
      },
      success:function(response){
          console.log('success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.game-section .changetable').html(tablehtml);
	  $(".game-section").find(".changegametable").html( response );
      }
   }); 
});

$('input[type="checkbox"][name="game-type"]').click(function(){
   var getRegionID = $('.game-section .select-region').val(); //alert(getRegionID);
   var getRegionText = $('.game-section .select-region option:selected').text(); 
   var checkboxes = $('input[type="checkbox"][name="dates"]:checked'); var dates = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); dates += chkbx.value +','; } //alert(dates); 
   var checkboxes = $('input[type="checkbox"][name="skill-level"]:checked'); var skilllevels = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); skilllevels += chkbx.value +','; } //alert(skilllevels);  
   var checkboxes = $('input[type="checkbox"][name="game-type"]:checked'); var gametypes = '';
   for (i=0; i < checkboxes.length; i++){ var chkbx = checkboxes.get(i); gametypes += chkbx.value +','; } //alert(gametypes); 

   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method: "POST",  
      dataType: "html",
      data:{ 
	'action':'action_gamesrevamptableGametypes',
        'regionID':getRegionID,
	'dates':dates,
	'skills':skilllevels,
	'gametypes':gametypes,
      },
      success:function(response){
          console.log('game success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.game-section .changetable').html(tablehtml);
	  $(".game-section").find(".changegametable").html( response );
      }
   }); 
});


//checks the click on the anchor of the table
$(document).on("click", '.game-table .table a', function(event) { 
 var id = $(this).attr('data-id');
 //if disabled, return
 if($(this).attr('disabled') == 'disabled'){ return false; }
 //enter id to revamp the modal
 $.ajax({  
   url: frontendajaxurl.ajaxurl,  
   method : "POST",  
   dataType: "html",
   data:{ 
	'action':'action_modaldata',
        'id':id,
   },
   success:function(response){
     console.log('modal success'); 
     $('.games_display_modal').html(response); 
     $('.games_display_modal #exampleModalLong').modal('show');
   }
 }); 
});

//click on "reserve a guest" button
$(document).on("click", '.games_display_modal #exampleModalLong .guest-checkout', function(event) { 
   //check if accept rules is checked
   if( $('#exampleModalLong input[type="checkbox"][name="acceptRules"]').prop('checked') == false ){
     alert('Please Agree To the Rules and Regulations and Waiver.'); return false;
   }
   //add the price of game to cart
   var gID = $('.games_display_modal #exampleModalLong .modal-footer #gid').val(); //alert(gID);
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method: "POST",  
      dataType: "html",
      data:{ 
	 'action':'action_checkoutAsGuest',
         'gameID':gID,
      },
      success:function(response){
        console.log('Guest Checkout Added To Cart'); 
        alert('Success: Reservation For Guest Added');
        window.location.assign('http://lab-1.sketchdemos.com/P943_Basketball/cart');
      }
   });
});


//--------------------------
//- customer dashboard
//--------------------------
$(document).ready(function(){  
 var cURL = window.location.href; 
 if( (cURL.indexOf("gameid") > 0) && (cURL.indexOf("userid") > 0) ){
   var optArr = cURL.split('?'); var options = optArr[1].split('&'); 
   var game = options[0].split('='); var user = options[1].split('=');
   var gameid = game[1]; var userid = user[1]; //alert(gameid + ' ' + userid);

   // input gameid and userid to revamp games table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method: "POST",  
      dataType: "html",
      data:{ 
	'action':'action_gamestableCustomerDashboard',
        'gameid':gameid,
	'userid':userid,
      },
      success:function(response){
          console.log('success game table in customer dashboard'); 
	  //var tablehtml = getRegionText + ' Games';
	  //$('.game-section .changetable').html(tablehtml); 
          if(response.indexOf('Error') > -1){
            alert(response);
          }else{ 
	    alert('The Game Has Been Added To Scheduled Games'); 
          }
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #5a").html( response ); 
          setTimeout(function(){ 
	     window.location.assign(optArr[0]); 
	  }, 2000);
      }
   }); 
 }
});  	

//scheduled games on load
$(".page-template-my-account-tmpl .dashboard-section .nav-pills li").click(function(){
    //for the scheduled games section
    if ( $(this).find('a').prop('href').indexOf('#5a') !==  -1 ){ 
      //input the game table based on the games for the user
      gameid=''; userid='';
      $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_gamestableCustomerDashboard',
        	'gameid':gameid,
		'userid':userid,
      	},
      	success:function(response){
          console.log('success friendslist table in customer dashboard'); 
	  //var tablehtml = getRegionText + ' Games';
	  //$('.game-section .changetable').html(tablehtml);
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #5a").html( response );
        }
      });  
    }

    // for the friends section
    if ( $(this).find('a').prop('href').indexOf('#6a') !==  -1 ){ 
      //input the game table based on the games for the user
       var friendId = ''; //alert(friendId);
      $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_addUserToGroupInCustomerDashboard',
        	'friendId':friendId,
      	},
      	success:function(response){
          console.log('success - friends table in customer dashboard'); 
	  //var tablehtml = getRegionText + ' Games';
	  //$('.game-section .changetable').html(tablehtml);
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-list-wrapper").html( response );
        }
      });  
    }
});


//friends list addition on click 
$(".page-template-my-account-tmpl .dashboard-section .user-list-wrapper .user-box #add-friends").click(function(){
    //get friend id to be added 
    var friendId = $(this).attr('data-id');
    $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_addUserToGroupInCustomerDashboard',
        	'friendId':friendId,
      	},
      	success:function(response){
          console.log('success friends table in customer dashboard'); 
          alert('Success: Friend Added To Friend List');
	  //var tablehtml = getRegionText + ' Games';
	  //$('.game-section .changetable').html(tablehtml);
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-list-wrapper").html( response );
        }
   });  
});


//friends list removal on click 
$(document).on("click", '.page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-list-wrapper .user-box .remove', function(event) {
    //get friend id to be added 
    var friendId = $(this).attr('id')	; //alert(friendId); console.log($(this));
    $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_removeUserFromGroupInCustomerDashboard',
        	'friendId':friendId,
      	},
      	success:function(response){
          console.log('success remove from friends table in customer dashboard'); 
          //alert('Success: Friend Removed From Friend List');
	  //var tablehtml = getRegionText + ' Games';
	  //$('.game-section .changetable').html(tablehtml);
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-list-wrapper").html( response );
        }
   });
});


//add money to wallet
$(".page-template-my-account-tmpl .dashboard-section .tab-content .addmoney-cont #add-money").click(function(){
  var amount = $(".page-template-my-account-tmpl .dashboard-section .tab-content .addmoney-cont .add-money-cont input").val();
  if( amount != ''){
    amount = parseFloat(amount); alert(amount); 
    $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_addAmountToWallet',
        	'amount':amount,
      	},
      	success:function(response){
          console.log('Success: Wallet Amount Added To Cart'); 
          alert('Success: Wallet Amount Added To Cart');
          window.location.assign('http://lab-1.sketchdemos.com/P943_Basketball/cart');
        }
   });  
  }
});


//cancel a game button 
$(document).on("click", '.page-template-my-account-tmpl .dashboard-section .tab-content .game-table #cancel-button', function(event) {
   var gameId = $(this).attr('data-id');
   $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_cancelGameAddAmountWallet',
        	'gameid':gameId,
      	},
      	success:function(response){
          console.log('Success: Game Cancelled - Amount To Wallet'); 
          //console.log(response);
          alert('Game Cancelled, Amount To Be Refunded If Eligible');
          window.location.reload();
        }
   });  
});


//search user section
$(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .userlist-cont .user-srch-cont #search-user").click(function(){
  var username = $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .userlist-cont .user-srch-cont input").val();
  //if(username != ''){ //alert(username);
   $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_searchUserInCustomerDashboard',
        	'username':username,
      	},
      	success:function(response){
          console.log('Success: User Search Done'); 
          //alert('Game Cancelled, Amount Added To Wallet');
          //window.location.reload();
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .userlist-cont:first .user-list-wrapper").html( response );
        }
   });
 //}
});


//search user section
$(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-srch-cont #search-friend").click(function(){
  var username = $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-srch-cont input").val();
  //if(username != ''){
   $.ajax({  
      	url: frontendajaxurl.ajaxurl,  
      	method: "POST",  
      	dataType: "html",
      	data:{ 
		'action':'action_searchFriendInCustomerDashboard',
        	'username':username,
      	},
      	success:function(response){
          console.log('Success: Friend Search Done'); 
	  $(".page-template-my-account-tmpl .dashboard-section .tab-content #6a .frndlist-cont .user-list-wrapper").html( response );
        }
   });
  //}
});
