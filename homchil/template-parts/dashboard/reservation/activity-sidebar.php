<?php 

if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}
global $current_user, $post;

wp_get_current_user();
$userID = $current_user->ID;
$currentuser_role = $current_user->roles[0];
?>
<div class="block">
<?php
$reservationID = isset($_GET['reservation_detail']) ? $_GET['reservation_detail'] : ''; 
if(!empty($reservationID)) {
    $post = get_post($reservationID); 
    $check_in = get_post_meta($reservationID, 'reservation_checkin_date', true);
    $check_out = get_post_meta($reservationID, 'reservation_checkout_date', true);
    $guests = get_post_meta($reservationID, 'reservation_guests', true);
    $listing_id = get_post_meta($reservationID, 'reservation_listing_id', true);
    $res_meta   = get_post_meta($reservationID, 'reservation_meta', true); 
    $res_selections = get_post_meta($reservationID, 'reservation_selections', true);
    if(!empty($res_selections)){ $selections = explode(',', $res_selections);
    } else{ $selections = array(); }

if($currentuser_role == 'homey_brokers' || $currentuser_role == 'homey_agents'){ 
//----------------------------------------
// generate a query for suggested listings
//----------------------------------------
$no_of_listing   =  '9';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$suggested_qargs = array(
    'post_type'        =>  'listing',
    //'author'         =>  $userID,
    'paged'            => $paged,
    'posts_per_page'   => $no_of_listing,
    'post_status'      =>  'publish'
);
if(isset($res_meta['location'])){
$meta_query[] = array( 'key' => 'homey_zip','value' => $res_meta['location'],'compare' => '=');
}
if(isset($res_meta['no_of_bedrooms'])){
//$meta_query[] = array( 'key' => 'homey_listing_bedrooms','value' =>$res_meta['no_of_bedrooms'],'compare' => '>=');
$meta_query[] = array( 'key' => 'homey_listing_bedrooms','value' =>1,'compare' => '>=');
}
if(isset($res_meta['no_of_bathrooms'])){
//$meta_query[] = array( 'key' => 'homey_baths','value' =>$res_meta['no_of_bathrooms'],'compare' => '>=');
$meta_query[] = array( 'key' => 'homey_baths','value' =>1,'compare' => '>=');
}
if(isset($guests) && $guests > 0){
$meta_query[] = array( 'key' => 'homey_guests','value' => $guests,'compare' => '>=');
}
if(isset($meta_query) && !empty($meta_query)){ 
   $meta_query['relation'] = 'AND'; $suggested_qargs['meta_query'] = $meta_query; 
}
$suggested_qargs = homey_listing_sort ($suggested_qargs);
$suggestedlisting_qry = new WP_Query($suggested_qargs);
$meta_query = []; wp_reset_postdata();

//----------------------------------------
// generate a query for available listings
//----------------------------------------
$no_of_listing   =  '9';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$available_qargs = array(
    'post_type'        => 'listing',
    //'author'         => $userID,
    'paged'            => $paged,
    'posts_per_page'   => $no_of_listing,
    'post_status'      => 'publish'
);
if($currentuser_role == 'homey_agents'){
$meta_query[] = array('key' => 'homey_assignedagent','value' => $userID,'compare' => '=' );
$meta_query[] = array('key' => 'homey_typeofpropertylisting','compare' => 'NOT EXISTS' );
$meta_query[] = array('key' => 'homey_typeofpropertylisting','value' => 'public','compare' => '=' );
$meta_query['relation'] = 'OR';
}
/*if(isset($res_meta['location'])){
 $meta_query[] = array( 'key' => 'homey_zip','value' => $res_meta['location'],'compare' => '=');
}*/
if(isset($meta_query) && !empty($meta_query)){ $available_qargs['meta_query'] = $meta_query; }
$available_qargs = homey_listing_sort ($available_qargs);
$availablelisting_qry = new WP_Query($available_qargs);
$meta_query = []; wp_reset_postdata(); 

}else{ // if host, not agent or broker 

//approved listings
$res_approvedSel = get_post_meta($reservationID, 'approved_reservations', true);
if(!empty($res_approvedSel)){ $app_selections = explode(',', $res_approvedSel);
} else{ $app_selections = array(); }
// denied listings
$res_deniedSel = get_post_meta($reservationID, 'denied_reservations', true);
if(!empty($res_deniedSel)){ $deny_selections = explode(',', $res_deniedSel);
} else{ $deny_selections = array(); }

//----------------------------------------------
// generate query for selected houses listings
//----------------------------------------------
$no_of_listing   =  '9';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_qargs = array(
       'post_type'        =>  'listing',
       //'author'           =>  $userID,
       'paged'             => $paged,
       'posts_per_page'    => $no_of_listing,
       'post_status'      =>  'publish', 
       'post__in' => $selections
);
$selected_qargs = homey_listing_sort ($selected_qargs);
$selected_props_qry = new WP_Query($selected_qargs);
$meta_query = []; wp_reset_postdata();

}}
?>

<!-- Nav tabs -->
<ul class="nav nav-pills" id="detailsTab" role="tablist">
<?php if($currentuser_role == 'homey_brokers' || $currentuser_role == 'homey_agents'){ ?>
  <li class="nav-item active" style="border:1px solid #EEECEC;border-radius:5px;">
    <a class="nav-link active" id="suggested-houses-tab" data-toggle="pill" data-target="#suggested-houses" role="tab" aria-controls="suggested-houses" aria-selected="true">Suggested Houses</a>
  </li>
  <li class="nav-item" style="border:1px solid #EEECEC;border-radius:5px;">
    <a class="nav-link" id="availability-check-tab" data-toggle="pill" data-target="#available-houses" role="tab" aria-controls="available-houses" aria-selected="false">Available Houses</a>
  </li>
  <li class="nav-item" style="border:1px solid #EEECEC;border-radius:5px;">
    <a class="nav-link" id="selected-check-tab" data-toggle="pill" data-target="#selected-houses" role="tab" aria-controls="selected-houses" aria-selected="false">Selected Houses <span style="font-size:9px;">(Availability Check)</span></a>
  </li>
<?php } else { ?>
  <li class="nav-item active" style="border:1px solid #EEECEC;border-radius:5px;">
    <a class="nav-link active" id="selected-check-tab-host" data-toggle="pill" data-target="#selected-houses-host" role="tab" aria-controls="selected-houses-host" aria-selected="true">Selected Houses <span style="font-size:9px;">(Agent)</span></a>
  </li>
<?php } ?>
</ul>


<!-- Tab panes -->
<div class="tab-content">

<?php if($currentuser_role == 'homey_brokers' || $currentuser_role == 'homey_agents'){ ?>
  <!-- suggested houses tab -->
  <div class="tab-pane active" id="suggested-houses" role="tabpanel" aria-labelledby="suggested-houses-tab">
	<!--<div class="block-title"><h3 class="title"><?php //echo esc_html__('Suggested Houses', 'homey-child'); ?></h3></div>-->
	<div class="block-body" style="padding:2% 2%">
	<div class="suggested-houses">
	  <div class="suggested-houses-form">
	  <form class="clearfix" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"> 
	  <div style="height:32px;">
	   <label for="form" style="float:left;margin-right:8px;">Search Houses: </label>
	   <input type="hidden" id="limit" name="limit" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="12"/>
	   <input type="hidden" id="paged" name="paged" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="1"/>
           <input type="hidden" id="sort_by" name="sort_by" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="a_date"/>
	   <input type="hidden" id="postcode" name="postcode" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="<?php echo $res_meta['location']; ?>"/>

	   <input type="text" id="search-text" name="search-text" style="float:left;width:35%;height:28px;margin-right:8px;" autocomplete="off" class="form-control input-search" value="" placeholder="Enter Value To Search"/>
	   <!--<select class="form-control select" id="state" name="state" style="float:left;width:25%;height:28px;margin-right:2px;">
	   <option value="">Select state</option><?php //echo homey_get_all_states(); ?></select>
	   <select class="form-control select" id="city" name="city" style="float:left;width:25%;height:28px;margin-right:2px;">
	   <option value="">Select city</option><?php //echo homey_get_all_cities(); ?></select>-->
	   <button type="submit" id="suggested-houses-search" name="suggested-houses-search" class="btn btn-primary" style="float:left;width:18%;height:28px;line-height:1px;padding:0 5px;"><?php echo esc_html__('Search'); ?></button>
	  </div>
	  </form>
	  </div>
	  <div class="suggested-houses-result"> <?php var_dump($selections); ?>
		<!-- this content will be replaced via ajax if search is performed --> 
		<?php if(isset($suggestedlisting_qry)){ ?>
		<?php if($suggestedlisting_qry->have_posts()): ?>
		  <div class="table-block dashboard-listing-table dashboard-table">
                  <table class="table table-hover">
		  <tbody id="suggested_houses_listings">
		  <?php while ($suggestedlisting_qry->have_posts()): $suggestedlisting_qry->the_post(); 
			$address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
			$approvedDt = get_post_meta( get_the_ID(), 'host_approval_till', true ); 
			//if(!empty($approvedDt)){ if( time() < strtotime($approvedDt)){ continue; }} ?>
	   	  <?php $style='';
	    	   if(!empty($selections)){ if(in_array($post->ID, $selections)){ $style = 'style="background:#d0c8c8;"'; }} 
	    	   if(!empty($approvedDt) && (!in_array($post->ID, $selections))){ 
		     if( time() <= strtotime($approvedDt)){ $style = 'style="background:#D6F4D0;"'; }} ?>
		   <tr id="tr-id-<?php echo $post->ID; ?>" <?php echo ' '.$style; ?>>			
		    <td data-label="<?php echo esc_html__('thumbnail'); ?>">
        		<a href="<?php the_permalink(); ?>">
        		<?php
        		if( has_post_thumbnail( $post->ID ) ) {
            		  the_post_thumbnail( 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        		}else{ homey_image_placeholder( 'homey-listing-thumb' ); } ?>
        		</a>
    		    </td>
		    <td data-label="<?php echo esc_html__('title-address'); ?>">
			<div style="float:left;width:75%">
        		<a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
        		<?php if(!empty($address)) { ?><address><?php echo esc_attr($address); ?></address><?php } ?>
			</div>
			<div style="float:left;width:25%">
			<input type="hidden" name="listingid" value="<?php echo $post->ID; ?>"/>
			<?php if(!empty($approvedDt)){ ?>
		    	<?php if(time() <= strtotime($approvedDt)){ ?>
		    	 <button type="button" class="btn btn-full-width add-property-to-approval" 
		    	 <?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo ' disabled=true '; }} ?>
			     style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('AddTo Approval'); ?>
		    	 </button>
		    	 <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
				<?php echo esc_html__('Add To Approved', 'homey-child'); ?>
		    	 </div>
		    	<?php } else { ?>
		    	 <button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" 
		    	 <?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo 'disabled=true'; }} ?>
			     style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('Check Availability'); ?>
		    	 </button>
		    	 <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
				<?php echo esc_html__('Send Message To Host', 'homey-child'); ?>
		    	 </div>
		    	<?php } ?>
		  	<?php } else { ?>
		  	<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" 
		  	<?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo 'disabled=true'; }} ?>
				style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('Check Availability'); ?>
		  	</button>
		  	<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
				<?php echo esc_html__('Send Message To Host', 'homey-child'); ?>
		  	</div>
		  	<?php } ?>
			</div>
    		    </td>
		   </tr>
		  <?php endwhile; ?>
		  </tbody>
                  </table>
                  </div>
		  <?php //homey_pagination( $suggestedlisting_qry->max_num_pages, $range = 2 ); ?>
		<?php 
		else:
                   echo '<div class="">'; echo esc_html__('No Houses Match The Criteria', 'homey-child'); echo '</div>';      
                endif; ?>
		<?php } ?>
	  </div><!-- suggested-houses-result -->
	  <input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
	</div><!-- suggested-houses --> 
	</div><!-- block-body -->
  </div><!-- suggested houses tab -->

  <!-- available houses tab -->
  <div class="tab-pane" id="available-houses" role="tabpanel" aria-labelledby="available-houses-tab">
     <!--<div class="block-title"><h3 class="title"><?php //echo esc_html_e('Available Houses', 'homey-child'); ?></h3></div>-->
     <div class="block-body" style="padding:2% 2%">
	<h2> Available Houses </h2>
	<div class="available-houses-form">
	  <form id="frm-available-houses" class="clearfix" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"> 
	  <div style="height:32px;">
	   <label for="form" style="margin-right:8px;">Search Houses: </label><label id="avail-form-err" for="form-error"></label><br/>
	   <input type="hidden" id="avail-limit" name="avail-limit" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="12"/>
	   <input type="hidden" id="avail-paged" name="avail-paged" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="1"/>
           <input type="hidden" id="avail-sort_by" name="avail-sort_by" style="float:left;width:2%;height:28px;" autocomplete="off" class="form-control input-search" value="a_date"/>
	   <select class="form-control select" id="avail-search-city" name="avail-search-city" style="float:left;width:19%;height:28px;margin-right:3px;">
	     <option value="">Select city</option><?php echo homey_get_all_cities(); ?>
	   </select>
	   <input type="text" id="avail-search-pcode" name="avail-search-pcode" style="float:left;width:16%;height:28px;margin-right:3px;" autocomplete="off" class="form-control input-search" value="" placeholder="Postcode"/>
	   <input type="text" id="avail-search-nbeds" name="avail-search-nbeds" style="float:left;width:11%;height:28px;margin-right:3px;" autocomplete="off" class="form-control input-search" value="" placeholder="Beds"/>
	   <input type="text" id="avail-search-nbaths" name="avail-search-nbaths" style="float:left;width:11%;height:28px;margin-right:3px;" autocomplete="off" class="form-control input-search" value="" placeholder="Baths"/>
	   <input type="text" id="avail-search-text" name="avail-search-text" style="float:left;width:16%;height:28px;margin-right:3px;" autocomplete="off" class="form-control input-search" value="" placeholder="Keyword"/>
	   <button type="submit" id="avail-houses-search" name="avail-houses-search" class="btn btn-primary" style="float:left;width:15%;height:28px;line-height:1px;padding:0 5px;"><?php echo esc_html__('Search'); ?></button>
	  </div>
	  </form>
	</div>
	<div class="available-houses-result">
	 <!-- the contents of the div are replaced via ajax -->
	<?php if(isset($availablelisting_qry)){ ?>
	<?php if($availablelisting_qry->have_posts()): ?>
	<div class="table-block dashboard-listing-table dashboard-table">
           <table class="table table-hover">
	   <tbody id="available_houses_listings">
	   <?php while ($availablelisting_qry->have_posts()): $availablelisting_qry->the_post(); 
		$address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
		$approvedDt = get_post_meta( get_the_ID(), 'host_approval_till', true ); 
		//if(!empty($approvedDt)){ if( time() < strtotime($approvedDt) ){ continue; }} ?>
	   <?php $style='';
	    if(!empty($selections)){ if(in_array($post->ID, $selections)){ $style = 'style="background:#d0c8c8;"'; }} 
	    if(!empty($approvedDt) && (!in_array($post->ID, $selections))){ 
		if( time() <= strtotime($approvedDt)){ $style = 'style="background:#D6F4D0;"'; }} ?>
	   <tr id="tr-id-<?php echo $post->ID; ?>" <?php echo ' '.$style; ?>>			
		<td data-label="<?php echo esc_html__('thumbnail'); ?>">
        	  <a href="<?php the_permalink(); ?>">
        	  <?php if( has_post_thumbnail( $post->ID ) ) {
            		  the_post_thumbnail( 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        	   }else{ homey_image_placeholder( 'homey-listing-thumb' ); } ?>
        	  </a>
    		</td>
		<td data-label="<?php echo esc_html__('title-address'); ?>">
		<div style="float:left;width:75%">
        	  <a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
        	  <?php if(!empty($address)) { ?><address><?php echo esc_attr($address); ?></address><?php } ?>
		</div>
		<div style="float:left;width:25%">
		  <input type="hidden" name="listingid" value="<?php echo $post->ID; ?>"/>
		  <?php if(!empty($approvedDt)){ ?>
		    <?php if(time() <= strtotime($approvedDt)){ ?>
		     <button type="button" class="btn btn-full-width add-property-to-approval" 
		     <?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo ' disabled=true '; }} ?>
			     style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('AddTo Approval'); ?>
		     </button>
		     <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
			<?php echo esc_html__('Add To Approved', 'homey-child'); ?>
		     </div>
		    <?php } else { ?>
		     <button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" 
		     <?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo 'disabled=true'; }} ?>
			     style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('Check Availability'); ?>
		     </button>
		     <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
			<?php echo esc_html__('Send Message To Host', 'homey-child'); ?>
		     </div>
		    <?php } ?>
		  <?php } else { ?>
		  <button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" 
		  <?php if(!empty($selections)){ if(in_array($post->ID, $selections)){ echo 'disabled=true'; }} ?>
				style="font-size:10px;border: 1px solid #d0c8c8;"><?php echo esc_html__('Check Availability'); ?>
		  </button>
		  <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
			<?php echo esc_html__('Send Message To Host', 'homey-child'); ?>
		  </div>
		  <?php } ?>
		</div>
    		</td>
	   </tr>
	   <?php endwhile; ?>
	   </tbody>
           </table>
        </div>
	<?php //homey_pagination( $availablelisting_qry->max_num_pages, $range = 2 ); ?>
	<?php else:
           echo '<div class="">'; echo esc_html__('No Houses Match The Criteria', 'homey-child'); echo '</div>';      
           endif; ?>
	<?php } ?>
	</div><!-- available-houses --> 
 	<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
     </div><!-- block-body -->
  </div><!-- available houses tab -->

  <!-- selected houses tab -->
  <div class="tab-pane" id="selected-houses" role="tabpanel" aria-labelledby="selected-houses-tab">
     <!--<div class="block-title"><h3 class="title"><?php //echo esc_html_e('Available Houses', 'homey-child'); ?></h3></div>-->
     <div class="block-body" style="padding:2% 2%">
	<h2> Selected Houses </h2>
	<h6>(To Select A House Need To Click On "Check Availability" Button)</h6>
	<div class="selected-houses-result">
	</div><!-- selectd-houses-result --> 
 	<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
     </div><!-- block-body -->
  </div><!-- selected houses tab -->

<?php } else { ?>

  <!-- selected houses host tab -->
  <div class="tab-pane active" id="selected-houses-host" role="tabpanel" aria-labelledby="selected-houses-host-tab">
     <!--<div class="block-title"><h3 class="title"><?php echo esc_html__('Houses Selected By Agents', 'homey-child'); ?></h3></div>-->
     <div class="block-body" style="padding:2% 2%">
	<h2> Selected Houses </h2>
	<div class="selected-houses-host-result">
	<?php if(isset($selected_props_qry)){  
	      if($selected_props_qry->have_posts()): ?>
	<div class="table-block dashboard-listing-table dashboard-table">
        <table class="table table-hover"><tbody id="selected_houses_listings">
	<?php while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	  $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	  $approvedDt = get_post_meta( get_the_ID(), 'host_approval_till', true ); ?>
	  <tr <?php if(!empty($app_selections)){ if(in_array($post->ID, $app_selections)){ echo 'style="background:#d0c8c8;"'; }} ?>
	      <?php if(!empty($deny_selections)){ if(in_array($post->ID, $deny_selections)){ echo 'style="background:#d0c8c8;"'; }} ?>>
	  <td data-label="'.esc_html__('thumbnail').'"><a href="'.get_the_permalink($post->ID).'">
          <?php if( has_post_thumbnail( $post->ID ) ) { 
            the_post_thumbnail('homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) ); 
          }else{ homey_image_placeholder( 'homey-listing-thumb' ); } ?>
          </a></td>
          <td data-label="'.esc_html__('title-address').'">
	  <div style="float:left;width:100%"><a href="<?php the_permalink() ?>"><strong><?php the_title() ?></strong></a>
          <?php if(!empty($address)) { echo '<address>'.esc_attr($address).'</address>'; } ?></div>
	  <?php if ($post->post_author == $userID ){ ?>
	  <?php if(empty($approvedDt) || (time() > strtotime($approvedDt))){ ?>
          <div style="float:left;width:100%">
          <button type="button" class="btn btn-full-width accepted" 
		<?php if(!empty($app_selections)){ if(in_array($post->ID, $app_selections)){ echo 'disabled=true'; }} ?>
		<?php if(!empty($deny_selections)){ if(in_array($post->ID, $deny_selections)){ echo 'disabled=true'; }} ?>
		style="font-size:10px;border:1px solid #d0c8c8;background:#85c341;width:16%;float:left;line-height:25px;margin-right:3px;">
	  <?php echo esc_html__('Accepted'); ?></button>
	  <button type="button" class="btn btn-full-width denied" 
		<?php if(!empty($app_selections)){ if(in_array($post->ID, $app_selections)){ echo 'disabled=true'; }} ?>
		<?php if(!empty($deny_selections)){ if(in_array($post->ID, $deny_selections)){ echo 'disabled=true'; }} ?>
		style="font-size:10px;border:1px solid #d0c8c8;background:#f15e75;width:16%;float:left;line-height:25px;">
	  <?php echo esc_html__('Denied'); ?></button>
	  <input type="hidden" name="listingid" value="<?php echo $post->ID; ?>"/>
	  </div>
	  <div class="text-center text-small" style="font-size:10px;float:left;"><i class="fa fa-info-circle"></i>
	  <?php echo esc_html__('Confirm/Deny Property For Booking - One Click Allowed', 'homey-child'); ?>
	  </div>
	  <?php } else { ?>
	  <div class="text-center text-small" style="font-size:10px;float:left;"><b><i class="fa fa-info-circle"></i>
	  <?php echo esc_html__('Property Already Approved Earlier', 'homey-child'); ?></b>
	  </div>
	  <?php }} ?>
	  </td></tr>
	<?php endwhile; ?>
	</tbody></table>
	</div>
	<?php //homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); ?>
	<?php else: ?>
          <div class="">'; 
	  <?php echo esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); ?>
	  </div>     
	<?php endif; ?>
	<?php } ?>
	</div><!-- selectd-houses-host-result --> 
 	<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
     </div><!-- block-body -->
  </div><!-- selected houses host tab -->

<?php } ?>
</div> <!-- end tab content -->
</div> <!-- end block -->
