
///--custom.js
//--------------
//games page
//--------------
//uses the change region to revamp games table on home page
$('.game-section .select-region').change(function(){
   var getRegionID = $('.game-section .select-region').val(); //alert(getRegionID);
   var getRegionText = $('.game-section .select-region option:selected').text(); 

   // input region code to revamp the table
   $.ajax({  
      url: frontendajaxurl.ajaxurl,  
      method : "POST",  
      dataType: "html",
      data:{ 
	'action':'action_gamesrevamptable',
        'regionID':getRegionID,
      },
      success:function(response){
          console.log('success'); 
	  var tablehtml = getRegionText + ' Games';
	  $('.game-section .changetable').html(tablehtml);
	  $(".game-section").find(".changegametable").html( response );
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
$(document).on("click", '.game-table .table a', function(event) { 

 var id = $(this).attr('data-id');

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


///--functions.php
/**
 * revamping the games table
 */
add_action( 'wp_ajax_nopriv_action_gamesrevamptable', 'action_gamesrevamptable' );
add_action( 'wp_ajax_action_gamesrevamptable', 'action_gamesrevamptable' );
function action_gamesrevamptable() {
  $html = ''; // variable to return
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID;
  $current_date = date('j'); $number_of_days_curr_month = date('t'); $current_month = date('n'); 

  //create table from the current date to the current month
  for($cnt = $current_date; $cnt <= $number_of_days_curr_month; $cnt++){
    $cDate = date('Ymd',mktime(0,0,0,($current_month),$cnt,date('Y')));
 
    //get the list of posts for the query ordered by time
    $metaqry = array(); $metaqry["relation"] = "AND";  
    $intrim = array( 'key' => 'date', 'value' => $cDate, 'compare' => '==' ); array_push($metaqry, $intrim);
    $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		  'meta_key' => 'start_time', 'order_by' => 'meta_value_num', 'order' => 'ASC',
		  'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
					'terms' => $regionID, 'include_children' => false, ) ),
		  'meta_query' => $metaqry); 
    query_posts($args); 
    
    //if posts found list
    if(have_posts()) :
      $html .= '<div class="game-table table-responsive"><table class="table"><tr>';
      $html .= '<th>'.date('D M j',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</th>';
      $html .= '<th></th><th></th><th></th><th></th><th></th></tr>';
      while ( have_posts() ) : the_post();
     	$id = get_the_ID(); 
     	$noSeats = get_post_meta($id, 'number_of_seats', true); 
     	$currBookings = get_post_meta($id, 'current_bookings', true); 
        $html .= '<tr><td>'.date('F j, Y',mktime(0,0,0,($current_month),$cnt,date('Y'))).'</td>';
     	$html .= '<td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     	$html .= '<td>'.get_the_title().'</td>';
     	$html .= '<td>'.get_post_meta($id, 'price', true).'</td>';
     	$html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     	if($noSeats != ""){
       	  if($currBookings == "" || $noSeats > $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn play-game">PLAY GAME</a></td></tr>';
          }
          elseif($noSeats == $currBookings){
            $html .= '<td><a href="#" data-id='.$id.' class="btn game-full">FULL</a></td></tr>';
          }	
        } else{
           $html .= '<td><a href="#" data-id='.$id.' class="btn non-allocated" disabled="disabled">NO SETUP</a></td></tr>';
        }
      endwhile;
    endif;
    wp_reset_query(); 
  } // end for

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}



//theme functions
<?php 
  $terms = get_terms(array('taxonomy' => 'region', 'hide_empty' => false, 'order' => 'ASC', 'order_by' => 'term_id'));
  $regionID = $terms[0]->term_id; //echo "Region:".$regionID;
  $today = date('Ymd'); $number_of_days_curr_month = date('t'); $current_month = date('n'); 
  $end_of_month = date('Ymd',mktime(0,0,0,($current_month),$number_of_days_curr_month,date('Y'))); 
  $metaqry = array(); $metaqry["relation"] = "AND";  
  $intrim = array( 'key' => 'date', 'value' => array($today, $end_of_month), 'compare' => 'BETWEEN', 
		   'type'=>'DATE', 'order' => 'ASC', 'order_by' => 'meta_value_num'); 
  array_push($metaqry, $intrim);
  $args = array('post_type' => 'event_management', 'posts_per_page' => 10,
		'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
				      	     'terms' => $regionID, 'include_children' => false, ) ),
		'meta_query' => $metaqry); 
  query_posts($args);
  if(have_posts()) :
   while ( have_posts() ) : the_post(); 
     $id = get_the_ID(); echo $id;
   endwhile;
  else :
  endif;
  wp_reset_query();
?>
///----------------------------------------------------------------------------

//in functions.php
wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160816', true );
wp_enqueue_script( 'custom-script', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), false, true );
wp_localize_script( 'custom-script', 'frontendajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

/**
 * create the modal data based on the ID of the event
 */
add_action( 'wp_ajax_nopriv_action_modaldata', 'action_modaldata' );
add_action( 'wp_ajax_action_modaldata', 'action_modaldata' );
function action_modaldata(){
  $gameID = $_REQUEST['id']; //echo "Game Id:".$gameID;
  $currPost = get_post($gameID);
  $address = get_post_meta($gameID, 'address', true);
  $date = date("l F d",get_post_meta($gameID, 'date', true));
  $stime = date("g:i a", get_post_meta($gameID, 'start_time', true));
  $etime = date("g:i a", get_post_meta($gameID, 'end_time', true));
  $type = get_post_meta($gameID, 'type', true);
  $skill = get_post_meta($gameID, 'skill', true);
  $price = get_post_meta($gameID, 'price', true);
  $map = get_post_meta($gameID, 'map', true); 

  $html = '<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" 
           aria-hidden="true">';
  $html .= '<div class="modal-dialog" role="document"><div class="modal-content">';
  $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">'.get_the_title($gameID).'</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>';
  $html .= '<div class="modal-body">';
   
  $html .= $address .'<br/>';
  $html .= $date .'<br/>';
  $html .= $stime .' - '. $etime .'<br/>';
  $html .= $type .'<br/>';
  $html .= $skill .'<br/>';
  $html .= $price .'<br/>';
  $html .= $map .'<br/>'; 
  $html .= '<a href="'.bloginfo('url').'/register/" data-id='.$id.' class="btn game-full">Reserve A Spot</a>';

  $html .= '</div><div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             <button type="button" class="btn btn-primary">Save changes</button></div>';

  $html .= '</div></div></div>';

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}



//---custom.js
//checks the click on the anchor of the table
$(document).on("click", '.calendar-table .padding0 .sub-table a', function(event) { 
 var id = $(this).attr('data-id');
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

//template.php
<!-- Modal section - dynamically add modal-->
<section class="display_modal"></section>
<!-- end Modal section --> 


//-----------------------------
<?php $today = date('Ymd'); $metaqry = array(); $metaqry["relation"] = "AND";  
           $intrim = array( 'key' => 'date', 'value' => $today, 'compare' => '==' ); array_push($metaqry, $intrim);
	   $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
			 'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
					      	      'terms' => 36, 'include_children' => false, ) ),
			 'meta_query' => $metaqry); 
           query_posts($args); 
	   while ( have_posts() ) : the_post(); ?>
		<?php the_ID(); ?>
<?php endwhile; 
  wp_reset_query(); ?>

//------------------
//-- functions.php
wp_enqueue_script( 'custom-script', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), false, true );
wp_localize_script( 'custom-script', 'frontendajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

//--- custom.js
//---uses document ready to load change
$(document).ready(function(){
 //change event of the dropdown
 $('#select-region').trigger('change');
 alert('change event triggered'); 

});

//uses the change region to revamp games table on home page
$('#select-region').change(function(){
   var getRegionID = $('#select-region').val(); //alert(getRegionID);
   var getRegionText = $('#select-region option:selected').text(); 

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
	  //$('.calendar-section .changetable').html() 
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
//------------------------

//uses the change region to revamp games table on home page
$('#select-region').change(function(){
   var getRegionID = $('#select-region').val(); //alert(getRegionID);

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
	  $(".calendar-table").find(".padding0").html( response );
      }
   }); 

//-- functions.php
add_action( 'wp_ajax_nopriv_action_newrevamptable', 'action_newrevamptable' );
add_action( 'wp_ajax_action_newrevamptable', 'action_newrevamptable' );
function action_newrevamptable() {
  $regionID = $_REQUEST['regionID']; //echo "Region:".$regionID;

  $today = date('Ymd'); 
  $metaqry = array(); $metaqry["relation"] = "AND";  
  $intrim = array( 'key' => 'date', 'value' => $today, 'compare' => '==' ); array_push($metaqry, $intrim);
  $args = array('post_type' => 'event_management', 'posts_per_page' => 10, 
		'tax_query' => array( array( 'taxonomy' => 'region', 'field' => 'id',
				      	     'terms' => $regionID, 'include_children' => false, ) ),
		'meta_query' => $metaqry); 
  query_posts($args); 
  $html = '<table class="table sub-table">';
  if(have_posts()) :
   while ( have_posts() ) : the_post(); 
     $id = get_the_ID(); 
     $html .= '<tr><td>'.date("g:i a", get_post_meta($id, 'start_time', true)).'</td>'; 
     $html .= '<td>'.get_the_title().'</td>';
     $html .= '<td>'.get_post_meta($id, 'price', true).'</td>';
     $html .= '<td>'.get_post_meta($id, 'type', true).'</td>';
     $html .= '<td><a href="#" class="btn">PLAY GAME</a></td></tr>';
   endwhile;
  else :
     $html .= '<tr><td></td><td></td><td> No Games Scheduled. Please Check Back Soon. </td><td></td><td></td></tr>';
  endif;

  $html .= '</table>'; 
  wp_reset_query(); 

  echo $html;
  //echo 'revamping request successful';
  die(); // stop executing script
}

