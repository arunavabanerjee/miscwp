
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
  if($(this).attr('disabled') == 'disabled'){ return false; }
  $(this).css('background', '#ECECEC');
});

//changes to the table on games page
$('.game-section .calendar .calendar-day-np').mouseleave(function(){ 
  if($(this).attr('disabled') == 'disabled'){ return false; }
  $(this).css('background', '#ffbea2');
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

---------------------------------------------------------------------
	


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
  if($(this).attr('disabled') == 'disabled'){ return false; }
  $(this).css('background', '#ECECEC');
});

//changes to the table on games page
$('.game-section .calendar .calendar-day-np').mouseleave(function(){ 
  if($(this).attr('disabled') == 'disabled'){ return false; }
  $(this).css('background', '#ffbea2');
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


