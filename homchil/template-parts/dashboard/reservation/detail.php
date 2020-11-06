<?php
global $current_user, $homey_local, $homey_prefix, $reservationID, $owner_info, $renter_info, $renter_id, $owner_id;
$blogInfo = esc_url( home_url('/') );
wp_get_current_user();
$userID = $current_user->ID;

$messages_page = homey_get_template_link_2('template/dashboard-messages.php');
$booking_hide_fields = homey_option('booking_hide_fields');

$reservationID = isset($_GET['reservation_detail']) ? $_GET['reservation_detail'] : '';
$reservation_status = $notification = $status_label = $notification = '';
$upfront_payment = $check_in = $check_out = $guests = $pets = $renter_msg = '';
$night_label = $payment_link = '';

if(!empty($reservationID)) {

    if(homey_is_renter()) {
        $back_to_list = homey_get_template_link('template/dashboard-reservations.php');
    } else {
        if(!homey_listing_guest($reservationID)) {
            $back_to_list = homey_get_template_link_2('template/dashboard-reservations.php');
        } else {
            $back_to_list = homey_get_template_link_2('template/dashboard-reservations2.php');
        }
    }

    $post = get_post($reservationID);   
    $excerpt = $post->post_excerpt;
    $received_date = date('Y-m-d', strtotime($post->post_date));
    $current_date = date( 'Y-m-d', current_time( 'timestamp', 0 ));
    $current_date_unix = strtotime($current_date );

    $reservation_status = get_post_meta($reservationID, 'reservation_status', true);
    $total_price = get_post_meta($reservationID, 'reservation_total', true);
    $upfront_payment = get_post_meta($reservationID, 'reservation_upfront', true);
    $upfront_payment = homey_formatted_price($upfront_payment);
    $payment_link = homey_get_template_link_2('template/dashboard-payment.php');

    $check_in = get_post_meta($reservationID, 'reservation_checkin_date', true);
    $check_out = get_post_meta($reservationID, 'reservation_checkout_date', true);
    $guests = get_post_meta($reservationID, 'reservation_guests', true);
    $listing_id = get_post_meta($reservationID, 'reservation_listing_id', true);
    $pets   = get_post_meta($listing_id, $homey_prefix.'pets', true);
    $res_meta   = get_post_meta($reservationID, 'reservation_meta', true);  
    $conf_reserv = get_post_meta($reservationID, 'reservation_confirmed', true);
    $conf_booked = get_post_meta($reservationID, 'reservation_booked', true);

    $extra_expenses = homey_get_extra_expenses($reservationID);
    $extra_discount = homey_get_extra_discount($reservationID);

    if(!empty($extra_expenses)) {
        $expenses_total_price = $extra_expenses['expenses_total_price'];
        $total_price = $total_price + $expenses_total_price;
    }

    if(!empty($extra_discount)) {
        $discount_total_price = $extra_discount['discount_total_price'];
        $total_price = $total_price - $discount_total_price;
    }

    if(homey_option('reservation_payment') == 'full') {
        $upfront_payment = homey_formatted_price($total_price); 
    }

    $renter_msg = isset($res_meta['renter_msg']) ? $res_meta['renter_msg'] : '';

    $renter_id = get_post_meta($reservationID, 'listing_renter', true);
    $renter_info = homey_get_author_by_id('60', '60', 'reserve-detail-avatar img-circle', $renter_id);

    $owner_id = get_post_meta($reservationID, 'listing_owner', true);
    $owner_info = homey_get_author_by_id('60', '60', 'reserve-detail-avatar img-circle', $owner_id);

    $payment_link = add_query_arg( array( 'reservation_id' => $reservationID, ), $payment_link );

    $chcek_reservation_thread = homey_chcek_reservation_thread($reservationID);

    if($chcek_reservation_thread != '') {
        $messages_page_link = add_query_arg( array('thread_id' => $chcek_reservation_thread ), $messages_page );
    } else {
        $messages_page_link = add_query_arg( array('reservation_id' => $reservationID, 'message' => 'new', ), $messages_page );
    }
    
    $guests_label = homey_option('cmn_guest_label');
    if($guests > 1) { $guests_label = homey_option('cmn_guests_label'); }

    //----------------------- approved listings
    $approved_props = get_post_meta($reservationID, 'approved_reservations', true);
    //----------------------------------------------
    // generate query for approved houses listings
    //----------------------------------------------
    $no_of_listing   =  '12';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $approvedPropsArray = explode(',',$approved_props);
    $approved_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $approvedPropsArray
    );
    $approved_qargs = homey_listing_sort ($approved_qargs);
    $approved_props_qry = new WP_Query($approved_qargs); 
    $meta_query = []; wp_reset_postdata();

    //------------------denied listings
    $denied_props = get_post_meta($reservationID, 'denied_reservations', true);
    //----------------------------------------------
    // generate query for denied houses listings
    //----------------------------------------------
    $no_of_listing   =  '9';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $deniedPropsArray = explode(',',$denied_props);
    $denied_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $deniedPropsArray
    );
    $denied_qargs = homey_listing_sort ($denied_qargs);
    $denied_props_qry = new WP_Query($denied_qargs);
    $meta_query = []; wp_reset_postdata();
}

//if( ($post->post_author != $userID) && homey_listing_guest($reservationID) ) {
//    echo('Are you kidding?');
    
//} else {
?>
<div class="user-dashboard-right dashboard-with-sidebar">
    <div class="dashboard-content-area" 
		<?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers' 
			 || $current_user->roles[0] == 'homey_host' ){ ?>
		 style="padding:30px 50% 30px 15px;"
		<?php }else{ ?> style="" <?php } ?>>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">
                        <input type="hidden" id="resrv_id" value="<?php echo intval($reservationID); ?>">
                        <?php homey_reservation_notification($reservation_status, $reservationID); ?>

                        <div class="block">
                            <div class="block-head" style="padding:15px;">
                                <div class="block-left">
				  <?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers'){ ?>
				    <h2 class="title"><?php echo esc_html__('Lead Details'); ?></h2>
				    <h2><?php echo 'ID: #'.$reservationID.' '.homey_get_reservation_label($reservation_status, $reservationID); ?></h2>
				  <?php } elseif($current_user->roles[0] == 'homey_host'){ ?>
				    <h2 class="title"><?php echo esc_html__('Booking'); ?></h2>
				    <h2><?php echo 'ID: #'.$reservationID.' '.homey_get_reservation_label($reservation_status, $reservationID); ?></h2>
				  <?php } else { ?>
                                    <h2 class="title"><?php echo esc_attr($homey_local['reservation_label']); ?></h2>
				    <h2><?php echo 'ID: #'.$reservationID.' '.homey_get_reservation_label($reservation_status, $reservationID); ?></h2>
				  <?php } ?>
                                </div><!-- block-left -->
                                <div class="block-right">
                                    <div class="custom-actions" style="float:right">
                                        <?php //if($reservation_status == 'booked' && $current_date_unix >= strtotime($check_in)) { ?>
                                        <?php if($reservation_status == 'booked' && $current_user->roles[0] != 'homey_agents' && $current_user->roles[0] != 'homey_brokers') { ?>
                                        <button class="btn-action" data-toggle="collapse" data-target="#review-form" aria-expanded="false" aria-controls="collapseExample" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['review_btn']); ?>"><i class="fa fa-pencil"></i></button>
                                        <?php } ?>

                                        <button onclick="window.print();" class="btn-action" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['print_btn']); ?>"><i class="fa fa-print"></i></button>

					<?php if($current_user->roles[0] == 'homey_brokers') { ?>
					<button class="btn-action btn-cancel" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Close Lead','homey-child'); ?>"><i class="fa fa-times-circle"></i></button>  

					<button class="btn-action btn-confirm" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Complete Lead','homey-child'); ?>"><i class="fa fa-check-circle"></i></button> 

					<button class="btn-action btn-no-activity" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('No Activity','homey-child'); ?>"><i class="fa fa-newspaper-o"></i></button> 

					<button class="btn-action btn-archive" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Cancel Lead','homey-child'); ?>"><i class="fa fa-trash"></i></button>
					<?php } ?>                       

                                        <?php if(homey_is_admin()) { ?>
                                        <a href="#" class="mark-as-paid btn-action" data-id="<?php echo esc_attr($reservationID); ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Mark as Paid', 'homey'); ?>"><i class="fa fa-money"></i></a>

					<a href="#" class="reservation-delete btn-action" data-id="<?php echo esc_attr($reservationID); ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Delete', 'homey'); ?>"><i class="fa fa-trash"></i></a>

					<a href="<?php echo esc_url($messages_page_link); ?>" class="btn-action" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['msg_send_btn']); ?>"><i class="fa fa-envelope-open-o"></i></a>
                                        <?php } ?>

                                        <a href="<?php echo esc_url($back_to_list); ?>" class="btn-action" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['back_btn']); ?>"><i class="fa fa-mail-reply"></i></a>
					<p class="messages"></p>
                                    </div><!-- custom-actions --> 
				    <?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers'){ ?>
				    	<div id="register-user" name="register-user" style="width:100%;">
					<?php $customer_details = explode('%0A', urlencode($excerpt)); 
					  $customer_name = explode(':', urldecode($customer_details[0]));
					  $customer_email = explode(':', urldecode($customer_details[1]));
					  $customer_phone = explode(':', urldecode($customer_details[2])); ?>
					<?php if(!email_exists($customer_email[1])) { ?>
					<form id="frm-register-user" class="clearfix" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	   				<input type="hidden" id="register-user-name" name="register-user-name" autocomplete="off" class="form-control input-search" value="<?php echo $customer_name[1]; ?>"/>
	   				<input type="hidden" id="register-user-email" name="register-user-email" autocomplete="off" class="form-control input-search" value="<?php echo $customer_email[1]; ?>"/>
	   				<input type="hidden" id="register-user-phone" name="register-user-phone" autocomplete="off" class="form-control input-search" value="<?php echo $customer_phone[1]; ?>"/>
					<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>				
					<button type="submit" id="create-new-user" name="create-new-user" class="btn btn-primary"  style="float:right;height:28px;line-height:1px;padding:0 5px;"><?php echo esc_html__('Register Customer'); ?></button>
					</form>
					<?php } ?>
				    	</div>
				    <?php } ?>
                                </div><!-- block-right -->
                            </div><!-- block-head -->

                            <?php 
                            if($reservation_status == 'booked' && homey_listing_guest($reservationID)) {
                                get_template_part('template-parts/dashboard/reservation/review-form'); 
                            } elseif($reservation_status == 'booked') {
                                get_template_part('template-parts/dashboard/reservation/review-host');
                            }
                            
			    if(homey_is_admin()) {
                            get_template_part('template-parts/dashboard/reservation/add-extra-expenses');
                            get_template_part('template-parts/dashboard/reservation/discount');

                            if($reservation_status == 'declined') {
                                get_template_part('template-parts/dashboard/reservation/declined');

                            } elseif($reservation_status == 'cancelled') {
                                get_template_part('template-parts/dashboard/reservation/cancelled');
                            } else {
                                    get_template_part('template-parts/dashboard/reservation/cancel-form');
                                
                                    if(!homey_listing_guest($reservationID)) {
                                        get_template_part('template-parts/dashboard/reservation/decline-form');
                                    }
                            }
			    } //if homey is admin

			    if(!empty($res_meta)){
                              if($res_meta['no_of_days'] > 1) {
                                $night_label = homey_option('glc_day_nights_label');
                              } else {
                                $night_label = homey_option('glc_day_night_label');
                              }
			    }
                                  
                            ?>
                            
                            <div class="block-section">
                                <div class="block-body">
                                    <div class="block-left">
                                        <ul class="detail-list">
                                            <li><strong><?php echo esc_attr($homey_local['date_label']); ?>:</strong></li>
                                            <li><?php echo esc_attr( get_the_date( get_option( 'date_format' ), $reservationID ));?>
                                            <br>
                                            <?php echo esc_attr( get_the_date( get_option( 'time_format' ), $reservationID ));?> </li>
                                        </ul>
                                    </div><!-- block-left -->
                                    <div class="block-right">
                                        <?php if(!empty($renter_info['photo'])) {
                                            echo '<a href="'.esc_url($renter_info['link']).'" target="_blank">'.$renter_info['photo'].'</a>';
                                        }?>
                                        <ul class="detail-list">
                                            <li><strong><?php esc_html_e('From', 'homey'); ?>:</strong> 
						<?php if($current_user->roles[0] != 'homey_agents' && $current_user->roles[0] != 'homey_brokers'){ ?>
                                                <a href="<?php echo esc_url($renter_info['link']); ?>" target="_blank">
                                                    <?php echo esc_attr($renter_info['name']); ?>
                                                </a>
						<?php } else { ?>
                                                <a href="#" style="color:#000;"><?php echo esc_attr($renter_info['name']); ?></a>
						<?php } ?>
                                            </li>
                                            <li><?php echo '<a href="'.get_permalink($listing_id).'">'.get_the_title($listing_id).'</a>'; ?></li>
                                        </ul>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section -->

                            <div class="block-section">
                                <div class="block-body">
                                    <div class="block-left">
                                        <h2 class="title"><?php esc_html_e('Details', 'homey'); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right">
                                        <ul class="detail-list detail-list-1-col">
                                            <li>
						<?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers'){ ?>
						<?php  echo esc_html__('Received On'); ?>:
						<?php } else { ?>
                                                <?php  echo esc_attr($homey_local['check_In']); ?>: 
						<?php } ?>
                                                <strong><?php echo homey_format_date_simple($received_date); ?></strong>
                                            </li>

					    <?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers'){ ?>
                                            <li>
                                                <strong><?php echo esc_html__('Contact Details'); ?>:</strong>
						<?php $contact_details = explode('%0A', urlencode($excerpt)); ?>
                                                <strong>
						<?php foreach($contact_details as $det){ 
							//echo '<br/>'; echo str_replace('+', ' ', urldecode($det)); 
							echo '<br/>'; echo urldecode($det); 
						} ?>
						</strong>
                                            </li>
					    <?php } ?>

					    <li><h3 style="margin-top:50px;"><?php echo esc_html__('Lead Details'); ?>:</h3></li>
                                            <li>
                                                <?php echo esc_attr($homey_local['check_In']); ?>: 
                                                <strong><?php echo homey_format_date_simple($check_in); ?></strong>
                                            </li>
                                            <li>
                                                <?php echo esc_attr($homey_local['check_Out']); ?>: 
                                                <strong><?php echo homey_format_date_simple($check_out); ?></strong>
                                            </li>
					    <?php if(isset($res_meta['no_of_days'])) { ?>
                                            <li>
                                                <?php echo esc_attr($night_label); ?>: 
                                                <strong><?php echo esc_attr($res_meta['no_of_days']); ?></strong>
                                            </li>
					    <?php } ?>
                                            <?php if($booking_hide_fields['guests'] != 1) {?>
                                            <li>
                                                <?php echo esc_attr($guests_label); ?>: 
                                                <strong><?php echo esc_attr($guests); ?></strong>
                                            </li>
                                            <?php } ?>


					    <?php if(isset($res_meta['no_of_bedrooms'])) { 
						$bedroom_label = homey_option('sn_bedrooms_label'); ?>
                                            <li>
                                                <?php echo esc_attr($bedroom_label); ?>: 
                                                <strong><?php echo esc_attr($res_meta['no_of_bedrooms']); ?></strong>
                                            </li>
					    <?php } else { 
						$bedroom_label = homey_option('sn_bedrooms_label'); ?>
					    <li>
                                                <?php echo esc_attr($bedroom_label); ?>: 
                                                <strong><?php echo esc_html__('Not Supplied'); ?></strong>
                                            </li>
					    <?php } ?>


					    <?php if(isset($res_meta['no_of_bathrooms'])) { 
						$bathroom_label = homey_option('sn_bathrooms_label'); ?>
                                            <li>
                                                <?php echo esc_attr($bathroom_label); ?>: 
                                                <strong><?php echo esc_attr($res_meta['no_of_bathrooms']); ?></strong>
                                            </li>
					    <?php } else { 
						$bathroom_label = homey_option('sn_bathrooms_label'); ?>
					    <li>
                                                <?php echo esc_attr($bathroom_label); ?>: 
                                                <strong><?php echo esc_html__('Not Supplied'); ?></strong>
                                            </li>
					    <?php } ?>


					    <?php if(isset($res_meta['location'])) { 
						$location_label = homey_option('ad_location'); ?>
                                            <li>
                                                <?php echo esc_attr($location_label); ?>: 
                                                <strong><?php echo esc_attr($res_meta['location']); ?></strong>
                                            </li>
					    <?php } else { 
						$location_label = homey_option('ad_location'); ?>
					    <li>
                                                <?php echo esc_attr($location_label); ?>: 
                                                <strong><?php echo esc_html__('Not Supplied'); ?></strong>
                                            </li>
					    <?php } ?>

                                            <?php if(!empty($res_meta['additional_guests'])) { ?>
                                            <li>
                                                <?php echo esc_attr($homey_local['addinal_guest_text']); ?>: 
                                                <strong><?php echo esc_attr($res_meta['additional_guests']); ?></strong>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section -->    

                            <div class="block-section approval-div">
                                <div class="block-body" style="padding:10px;">
                                    <div class="block-left" style="width:100%;">
                                        <h2 class="title"><?php esc_html_e('Approval Received', 'homey'); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right content" style="width:100%;">
					<!-- contents will be replaced via ajax -->
					<div class="approval-houses-result">
					<?php if(isset($approved_props_qry)){ ?>
					<?php if($approved_props_qry->have_posts()): ?>
					<div class="table-block dashboard-listing-table dashboard-table">
           				<table class="table table-hover">
	   				<tbody id="approval_houses_listings">
	   				<?php while ($approved_props_qry->have_posts()): $approved_props_qry->the_post(); 
						$address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
						if($current_user->roles[0] != 'homey_brokers' && $current_user->roles[0] != 'homey_agents'){
  						 if(!empty($address)){ $address_array = explode(',',$address); 
    						   $address = $address_array[sizeof($address_array)-1]; }} ?>
					<?php $style=""; 
					  if($conf_reserv != '' && $conf_booked == ''){ 
					    if($conf_reserv == $post->ID){ $style = 'style="background:#f7e1e4;"'; }
					    else{ $style = 'style="background:#dfdcdc;"'; }
					  }elseif($conf_reserv != '' && $conf_booked != ''){ 
					    if($conf_reserv == $post->ID){ $style = 'style="background:#a1cc72;"'; }
					    else{ $style = 'style="background:#dfdcdc;"'; } } ?>
	   				<tr <?php echo $style; ?>>			
					<td data-label="<?php echo esc_html__('thumbnail'); ?>">
        	  			<a href="<?php the_permalink(); ?>">
        	  			<?php if( has_post_thumbnail( $post->ID ) ) {
            		  			the_post_thumbnail( 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        	   			}else{ homey_image_placeholder( 'homey-listing-thumb' ); } ?>
        	  			</a>
    					</td>
					<td data-label="<?php echo esc_html__('title-address'); ?>">
					<div style="float:left;width:70%">
        	  			<a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
        	  			<?php if(!empty($address)) { ?><address><?php echo esc_attr($address); ?></address><?php } ?>
					</div>

					<?php $cust_det = explode('%0A', urlencode($excerpt)); 
					      $cust_email_array = explode(':', trim(urldecode($cust_det[1]))); 
					      $cust_email = trim($cust_email_array[1]);
					      $user_email = $current_user->user_email; ?>
					<div style="float:left;width:30%">
					<input type="hidden" name="listingid" value="<?php echo $post->ID; ?>"/>
					<?php //if customer is logged in - renter ?>
					<?php if($cust_email == $user_email){ ?>
					   <?php if( $conf_reserv == '' &&  $conf_booked == ''){ ?> 
		  				<button type="button" name="user-confirmation" class="btn btn-full-width" 
						        style="font-size:10px;border:1px solid #d0c8c8;background-color:#85c341">
						<?php echo esc_html__('Confirm Choice'); ?></button>
		  			        <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Confirm Choice For Agent To Book', 'homey-child'); ?></div>
					   <?php } elseif($conf_reserv != '' && $conf_reserv == $post->ID && $conf_booked == ''){ ?>
		  				<button type="button" name="user-confirmation" class="btn btn-full-width" disabled='true'
						        style="font-size:10px;border:1px solid #d0c8c8;background-color:#85c341">
						<?php echo esc_html__('Confirmed'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Confirmed, Will Be Booked By Agent', 'homey-child'); ?></div>
					   <?php } elseif($conf_reserv != '' && $conf_reserv != $post->ID){ ?>
		  				<button type="button" name="user-confirmation" class="btn btn-full-width" disabled='true'
						        style="font-size:10px;border:1px solid #d0c8c8;background-color:#dfdcdc;">
						<?php echo esc_html__('Confirm Choice'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Disabled Options', 'homey-child'); ?></div>
					   <?php } elseif( $conf_reserv != '' && $conf_reserv == $post->ID && $conf_booked != ''){ ?>
		  				<button type="button" name="user-confirmation" class="btn btn-full-width" disabled='true'
						        style="font-size:10px;border:1px solid #d0c8c8;background-color:#85c341">
						<?php echo esc_html__('BOOKED'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('BOOKED By Agent', 'homey-child'); ?></div>
					   <?php } ?>
					<?php } ?>
					<?php //if broker or agent has logged in ?>					
					<?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers'){ ?>
					   <?php if( $conf_reserv == '' &&  $conf_booked == ''){ ?> 
		  				<button type="button" name="agent-broker-confirmation" class="btn btn-full-width" 
							style="font-size:9px;border:1px solid #d0c8c8;background-color:#dfdcdc;">
						  <?php echo esc_html__('Customer To Confirm'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Customer To Confirm Lead', 'homey-child'); ?></div>
					   <?php } elseif($conf_reserv != '' && $conf_reserv == $post->ID && $conf_booked == ''){ ?>
		  				<button type="button" name="agent-broker-final-confirmation" class="btn btn-full-width" 
							style="font-size:10px;border:1px solid #d0c8c8;background-color:#85c341">
						  <?php echo esc_html__('Complete Booking'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Lead Will Be Booked', 'homey-child'); ?></div>
					   <?php } elseif($conf_reserv != '' && $conf_reserv != $post->ID){  ?>
		  				<button type="button" name="agent-broker-confirmation" class="btn btn-full-width" disabled="true"
							style="font-size:10px;border:1px solid #d0c8c8;background-color:#dfdcdc;">
						  <?php echo esc_html__('Not Confirmed'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('Disabled Options', 'homey-child'); ?></div>			
					   <?php } elseif( $conf_reserv != '' && $conf_reserv == $post->ID && $conf_booked != ''){ ?>
		  				<button type="button" name="user-confirmation" class="btn btn-full-width" disabled='true'
						        style="font-size:10px;border:1px solid #d0c8c8;background-color:#85c341">
						<?php echo esc_html__('BOOKED'); ?></button>
		  				<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
						<?php echo esc_html__('BOOKED By Agent', 'homey-child'); ?></div>
					   <?php } ?>
					<?php } ?>
					</div>
    					</td></tr>
	   				<?php endwhile; ?>
	   				</tbody></table>
        				</div>
					<?php homey_pagination( $approved_props_qry->max_num_pages, $range = 2 ); ?>
					<?php else:
           				echo '<div class="">'; echo esc_html__('No Houses Match The Criteria', 'homey-child'); echo '</div>';      
           				endif; ?>
					<?php } ?>
					</div><!-- approval-houses-result --> 
 					<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section --> 

                            <div class="block-section denial-div">
                                <div class="block-body" style="padding:10px;">
                                    <div class="block-left" style="width:100%;">
                                        <h2 class="title"><?php esc_html_e('Denial Received', 'homey'); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right content" style="width:100%;">
					<!-- contents will be replaced via ajax -->
					<div class="denial-houses-result">
					<?php if(isset($denied_props_qry)){ ?>
					<?php if($denied_props_qry->have_posts()): ?>
					<div class="table-block dashboard-listing-table dashboard-table">
				           <table class="table table-hover">
					   <tbody id="denial_houses_listings">
					   <?php while ($denied_props_qry->have_posts()): $denied_props_qry->the_post(); 
						$address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
						if($current_user->roles[0] != 'homey_brokers' && $current_user->roles[0] != 'homey_agents'){
  						 if(!empty($address)){ $address_array = explode(',',$address); 
    						   $address = $address_array[sizeof($address_array)-1]; 
						}} ?>
					   <tr>	
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
					   </div>
    					   </td></tr>
	   				   <?php endwhile; ?>
	  				   </tbody></table>
        				</div>
					<?php homey_pagination( $denied_props_qry->max_num_pages, $range = 2 ); ?>
					<?php else:
           					echo '<div class="">'; echo esc_html__('No Houses Match The Criteria', 'homey-child'); echo '</div>';      
           				endif; ?>
					<?php } ?>
					</div><!-- denial-houses-result --> 
 					<input type="hidden" name="reservationid" value="<?php echo $reservationID; ?>"/>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section --> 

                            <?php if(!empty($renter_msg)) { ?>
                            <div class="block-section">
                                <div class="block-body">
                                    <div class="block-left">
                                        <h2 class="title"><?php esc_html_e('Notes', 'homey'); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right">
                                        <p><?php echo esc_attr($renter_msg); ?></p>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section --> 
                            <?php } ?>
                            
			    <?php if(homey_is_admin()) { ?>
                            <div class="block-section">
                                <div class="block-body">
                                    <div class="block-left">
                                        <h2 class="title"><?php echo esc_attr($homey_local['payment_label']); ?></h2>
                                    </div><!-- block-left -->
                                    <div class="block-right">
                                        <?php echo homey_calculate_reservation_cost($reservationID); ?>
                                    </div><!-- block-right -->
                                </div><!-- block-body -->
                            </div><!-- block-section -->
			    <?php } ?>
                        </div><!-- .block -->
			<?php if(homey_is_admin()) { ?>
                        <div class="payment-buttons visible-sm visible-xs">
                            <?php homey_reservation_action($reservation_status, $upfront_payment, $payment_link, $reservationID, 'btn-half-width'); ?>
                        </div>
			<?php } ?>
                    </div><!-- .dashboard-area -->
                </div><!-- col-lg-12 col-md-12 col-sm-12 -->
            </div>
        </div><!-- .container-fluid -->
    </div><!-- .dashboard-content-area -->  

    <?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers' 
		  || $current_user->roles[0] == 'homey_host' ){ ?>
      <aside class="dashboard-sidebar" style="width:43%;overflow-y:scroll;height:500px;">
    <?php } else { ?>  
      <aside class="dashboard-sidebar" style="width:26%;overflow-y:scroll;height:500px;">
    <?php } ?>
	<?php if($current_user->roles[0] == 'homey_agents' || $current_user->roles[0] == 'homey_brokers' 
		  || $current_user->roles[0] == 'homey_host' ){ ?>
		<?php get_template_part('template-parts/dashboard/reservation/activity-sidebar'); ?>
	<?php } ?>
	<?php if(homey_is_admin()) {?>
          <?php get_template_part('template-parts/dashboard/reservation/payment-sidebar'); ?>
          <?php homey_reservation_action($reservation_status, $upfront_payment, $payment_link, $reservationID, 'btn-full-width'); ?>
	<?php } ?>
    </aside><!-- .dashboard-sidebar -->
</div><!-- .user-dashboard-right -->
<?php get_template_part('template-parts/dashboard/reservation/message'); ?>
<?php //} ?>
