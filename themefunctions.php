
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

