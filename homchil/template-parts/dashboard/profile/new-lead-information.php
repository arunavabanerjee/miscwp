<?php

/**
 * form for new agent details 
 */
global $current_user, $userID, $user_email, $homey_local;
$reservation_link = homey_get_template_link('template/dashboard-reservations.php');

$prefilled = homey_get_dates_for_booking();

/* generate listing query for price-ranges*/ 
$pcode_args = array(
   'post_type'     => 'listing',
   'posts_per_page' => -1,
   'post_status'  =>  'publish', 
   'fields'	=> 'ids'	
);
$pcode_args = homey_listing_sort ($pcode_args);
$pcode_propsIds = new WP_Query($pcode_args);
$meta_query = []; wp_reset_postdata();

?>

<div class="user-dashboard-right dashboard-without-sidebar">
<div class="dashboard-content-area">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
<div class="dashboard-area">
<!--for dashboard -->

<div class="homey-new-lead-add-wrapper">
<form id="homey-add-new-lead" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="broker_email" value="<?php echo antispambot($user_email); ?>">
<input type="hidden" name="homey_addnewlead_security" value="<?php echo wp_create_nonce('homey-new-lead-create'); ?>"/>
<input type="hidden" name="redirect_to" value="<?php echo $reservation_link; ?>" />
<div class="block">
    <div class="block-title">
	<div class="homey_add_new_lead_messages_top"></div>
        <div class="block-left">
            <h2 class="title"><?php echo 'Enter Lead '.esc_attr($homey_local['information']); ?></h2>
        </div>
    </div>
    <div class="block-body">

        <div class="row" style="margin-bottom:25px;"> 
            <div class="col-sm-6">
		<?php //get all listings for the user
	     	 $listing_query_args = array( 'post_type' => 'listing', 'posts_per_page' => -1,
            	    			      'post_status' => 'publish', 'fields' => 'ids', );
	     	$prop_listings = new WP_Query( $listing_query_args );
	     	$prop_listing_ids = $prop_listings->posts; //var_dump($host_listing_ids); ?>
                <div class="form-group">
                    <label for="properties"><?php echo esc_html__('Select A Property From List'); ?></label>
		    <div class="form-control" style="height:230px;overflow-y:scroll;padding:0 2px 0 2px;width:99%;">
		    <ul class="form-control" id="properties" name="properties" style="padding:0;border;0px;">
			<?php $i=1; foreach($prop_listing_ids as $propId) { ?>
			<?php if($i%2==0) { ?>
			<li value="<?php echo $propId; ?>" 
			    style="list-style:none;height:55px;background:#EEE;padding:4px;margin:0 0 3px 0;color:#171212;">
			<?php } else { ?>
			<li value="<?php echo $propId; ?>" 
			    style="list-style:none;height:55px;background:#CCC;padding:4px;margin:0 0 3px 0;color:#171212;">
			<?php } ?>
			<div class="block-title" style="padding:0px;">
			<div class="block-left" style="padding-left:10px;">
			<?php if(has_post_thumbnail($propId)) {
                    	       echo get_the_post_thumbnail($propId, array(40,40), array('class'=>'img-responsive')); } ?>
			</div>
			<div class="block-right" style="float:left;padding-left:20px;">
			  <p style="margin:0px"><?php echo get_the_title($propId); ?></p>
			  <p style="margin:0px">
			    <?php $address = get_post_meta( $propId, 'homey_listing_address', true ); echo $address; ?>
			  </p>
			</div>
			</div>
			</li>
			<?php $i++; } ?>
	   	    </ul>
		    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="username"><?php echo esc_html__('Property Name'); ?></label>
                    <input type="text" id="where" name="where" class="form-control" placeholder="<?php echo esc_html__('Property Name'); ?>" readonly="true" style="background:#DDD;"/>
                </div>
                <div class="form-group">
                    <label for="username"><?php echo esc_html__('Where (PostCode)'); ?></label>
                    <input type="text" id="pcode" name="pcode" class="form-control" placeholder="<?php echo esc_html__('Location'); ?>" readonly="true" style="background:#DDD;"/>
                </div>
                <div class="form-group">
                    <label for="street_address"><?php echo esc_html__('Property Address', 'homey'); ?></label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="<?php echo esc_attr__('Full Address Including State, City, Postcode', 'homey'); ?>" readonly="true" style="background:#DDD;"/>
                </div>
		<input type="hidden" id="propertyid" name="propertyid" class="form-control" value="">
            </div>
	</div>

        <div class="row"> 
            <div class="col-sm-12">
		<label for="arrive-depart"><?php echo esc_html__('Check In / Check Out Dates:'); ?></label>		    
		<div id="single-listing-date-range" class="homey_notification search-date-range">
		    <div class="search-date-range-arrive">
			<input id="arrive" name="arrive" style="width:98%;" value="<?php echo esc_attr($prefilled['arrive']); ?>" readonly type="text" class="form-control check_in_date" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_arrive_label')); ?>">
		    </div>
		    <div class="search-date-range-depart">
			<input id="depart" name="depart" style="margin-left:10px;width:98%;" value="<?php echo esc_attr($prefilled['depart']); ?>" readonly type="text" class="form-control check_out_date" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_depart_label')); ?>">
		    </div>
		    <div id="single-booking-search-calendar" class="search-calendar single-listing-booking-calendar-js clearfix" style="display: none;">
			<?php homeyAvailabilityCalendar(); ?>
			<div class="calendar-navigation custom-actions">
                        <button class="listing-cal-prev btn btn-action pull-left disabled"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                        <button class="listing-cal-next btn btn-action pull-right"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
               		</div><!-- calendar-navigation -->	                
  		    </div>
		</div>
            </div>

            <div class="col-sm-6">
                <div class="form-group" style="margin-top:10px;">
                    <label for="n_guests"><?php echo esc_html__('No. Of Guests'); ?></label>
                    <input type="text" id="n_guests" name="n_guests" class="form-control" placeholder="<?php echo esc_html__('Guests'); ?>"/>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group" style="margin-top:10px;">
                    <label for="n_bedrooms"><?php echo esc_html__('No. Of Bedrooms'); ?></label>
                    <input type="text" id="n_bedrooms" name="n_bedrooms" class="form-control" placeholder="<?php echo esc_html__('Bedrooms'); ?>"/>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="n_bathrooms"><?php echo esc_html__('No. Of Bathrooms'); ?></label>
                    <input type="text" id="n_bathrooms" name="n_bathrooms" class="form-control" placeholder="<?php echo esc_html__('Bathrooms'); ?>"/>
                </div>
            </div>

	    <div class="col-sm-6">
		<div class="form-group">
		<label for="prange"><?php echo esc_html__('Price Range'); ?></label>
		<?php $npriceranges = array(); $html = '';
		 if ($pcode_propsIds->have_posts()) {
                   foreach ( $pcode_propsIds->posts as $lpostId ) { 
		     $nightprice = get_post_meta($lpostId, 'homey_night_price', true);
		     if($nightprice <= 100){ array_push($npriceranges, '<option value="0-100">Price: 0-100</option>'); }
		     if($nightprice > 100 && $nightprice <= 200){ array_push($npriceranges, '<option value="100-200">Price: 100-200</option>'); }
		     if($nightprice > 200 && $nightprice <= 300){ array_push($npriceranges, '<option value="200-300">Price: 200-300</option>'); }
		     if($nightprice > 300 && $nightprice <= 400){ array_push($npriceranges, '<option value="300-400">Price: 300-400</option>'); }
		     if($nightprice > 400 && $nightprice <= 500){ array_push($npriceranges, '<option value="400-500">Price: 400-500</option>'); }
		     if($nightprice > 500 && $nightprice <= 600){ array_push($npriceranges, '<option value="500-600">Price: 500-600</option>'); }
		     if($nightprice > 600 && $nightprice <= 700){ array_push($npriceranges, '<option value="600-700">Price: 600-700</option>'); }
		     if($nightprice > 700 && $nightprice <= 800){ array_push($npriceranges, '<option value="700-800">Price: 700-800</option>'); }
		     if($nightprice > 800 && $nightprice <= 900){ array_push($npriceranges, '<option value="800-900">Price: 800-900</option>'); }
		     if($nightprice > 900 && $nightprice <= 1000){ array_push($npriceranges, '<option value="900-1000">Price: 900-1000</option>'); }
		     if($nightprice > 1000 && $nightprice <= 1100){ array_push($npriceranges, '<option value="1000-1100">Price: 1000-1100</option>'); }
		   }
		 } // endif 
		 $pranges = array_unique($npriceranges); sort($pranges); ?>
		<select class="form-control select" id="n_prange" name="n_prange" style="min-width:120px;height:42px;padding:0;">
	     	  <option value="">Select A Price Range</option>
		  <?php foreach($pranges as $prange){ echo $prange; } ?>
	   	</select>
	      </div>
	    </div>

	    <div class="col-sm-6">
 	    	<div class="form-group">
		   <label for="keywords"><?php echo esc_html__('Keywords'); ?></label>
                   <input name="keywords" type="text" class="form-control" style="height:42px;padding:0 12px;"
			  		  	      placeholder="<?php esc_html_e('Keywords (comma separated)', 'homey'); ?>" />
                </div>
	    </div>

            <!--<div class="col-sm-6">
                <div class="form-group">
                    <label for="keywords"><?php //echo esc_html__('Keywords/Tags'); ?></label>
                    <input type="text" id="keywords" name="keywords" class="form-control" placeholder="<?php //echo esc_html__('Keywords (comma separated)'); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="budget-range"><?php //echo esc_html__('Budget Range'); ?></label>
                    <input type="text" id="budget_range" name="budget_range" class="form-control" placeholder="<?php //echo esc_html__('Budget Range'); ?>">
                </div>
            </div>-->

        </div>
    </div>
</div><!-- block -->
