<?php
/**
 * Template Name: Dashboard My Bookings
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !is_user_logged_in() ) {
    wp_redirect( home_url('/') );
}

global $homey_local, $current_user;
wp_get_current_user();
$userID = $current_user->ID;

$user_email   = $current_user->user_email;
$admin_email  = get_bloginfo('admin_email');
$allowed_html = array();

$dashboarduser_role = $current_user->roles[0]; 

if(isset($_GET['reservation_detail']) && $_GET['reservation_detail'] != "") {
  $reser_status = get_post_meta($_GET['reservation_detail'], 'reservation_status', true);
}

if ( isset($_GET['token']) && isset($_GET['PayerID']) ){
    $token    = wp_kses ( $_GET['token'], $allowed_html );
    $payerID  = wp_kses ( $_GET['PayerID'] ,$allowed_html);

    /* Get saved data in database during execution
     -----------------------------------------------*/
    $transfered_data     = get_option('homey_paypal_transfer');
    /*echo '<pre>';
    print_r($transfered_data[ $userID ]);
    wp_die();*/
    //if(!empty($transfered_data)) {

        $payment_execute_url = $transfered_data[ $userID ]['payment_execute_url'];
        $reservation_id      = $transfered_data[ $userID ]['reservation_id'];
        $token               = $transfered_data[ $userID ]['paypal_token'];
        $is_instance_booking = $transfered_data[ $userID ]['is_instance_booking'];
        $is_hourly           = $transfered_data[ $userID ]['is_hourly'];
        $extra_options       = $transfered_data[ $userID ]['extra_options'];
        $listing_id          = $transfered_data[ $userID ]['listing_id'];
        $check_in_date       = $transfered_data[ $userID ]['check_in_date'];
        $check_out_date      = isset($transfered_data[ $userID ]['check_out_date']) ? $transfered_data[ $userID ]['check_out_date'] : '';

        $check_in_hour = isset($transfered_data[ $userID ]['check_in_hour']) ? $transfered_data[ $userID ]['check_in_hour'] : '';
        $check_out_hour = isset($transfered_data[ $userID ]['check_out_hour']) ? $transfered_data[ $userID ]['check_out_hour'] : '';
        $start_hour = isset($transfered_data[ $userID ]['start_hour']) ? $transfered_data[ $userID ]['start_hour'] : '';
        $end_hour = isset($transfered_data[ $userID ]['end_hour']) ? $transfered_data[ $userID ]['end_hour'] : '';
        
        $guests              = $transfered_data[ $userID ]['guests'];
        $renter_message      = $transfered_data[ $userID ]['renter_message'];

        $payment_execute = array(
            'payer_id' => $payerID
        );

        $json           = json_encode( $payment_execute );
        $json_response  = homey_execute_paypal_request( $payment_execute_url, $json, $token );

        $transfered_data[$current_user->ID ]  =   array();
        update_option ('homey_paypal_transfer',$transfered_data);
        $paymentMethod = 'Paypal';

        //print_r($json_response);
        if( $json_response['state']=='approved' ) {

            $time = time();
            $date = date( 'Y-m-d g:i:s', current_time( 'timestamp', 0 ));

            if( $is_instance_booking == 0 ) {
                $listing_id = get_post_meta($reservation_id, 'reservation_listing_id', true );


                if($is_hourly  == 1) { 

                    //Book hours
                    $booked_days_array = homey_make_hours_booked($listing_id, $reservation_id);
                    update_post_meta($listing_id, 'reservation_booked_hours', $booked_days_array);

                    //Remove Pending Hours
                    $pending_dates_array = homey_remove_booking_pending_hours($listing_id, $reservation_id);
                    update_post_meta($listing_id, 'reservation_pending_hours', $pending_dates_array);

                } else {
                    //Book dates
                    $booked_days_array = homey_make_days_booked($listing_id, $reservation_id);
                    update_post_meta($listing_id, 'reservation_dates', $booked_days_array);

                    //Remove Pending Dates
                    $pending_dates_array = homey_remove_booking_pending_days($listing_id, $reservation_id);
                    update_post_meta($listing_id, 'reservation_pending_dates', $pending_dates_array);
                }
                

                // Update reservation status
                update_post_meta( $reservation_id, 'reservation_status', 'booked' );

            } elseif( $is_instance_booking == 1 ) {

                if($is_hourly == 1) {

                    $reservation_id = homey_add_hourly_instance_booking($listing_id, $check_in_date, $check_in_hour, $check_out_hour, $start_hour, $end_hour, $guests, $renter_message, $extra_options);
                } else {

                    $reservation_id = homey_add_instance_booking($listing_id, $check_in_date, $check_out_date, $guests, $renter_message, $extra_options);
                }
                
                //Create messages thread
                do_action('homey_create_messages_thread', $renter_message, $reservation_id);
            }

            $invoiceID = homey_generate_invoice( 'reservation','one_time', $reservation_id, $date, $userID, 0, 0, '', $paymentMethod );
            
            update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

            // Emails
            $listing_owner = get_post_meta($reservation_id, 'listing_owner', true);
            $listing_renter = get_post_meta($reservation_id, 'listing_renter', true);

            $renter = homey_usermeta($listing_renter);
            $renter_email = $renter['email'];

            $owner = homey_usermeta($listing_owner);
            $owner_email = $owner['email'];

            $email_args = array('reservation_detail_url' => reservation_detail_link($reservation_id) );
            homey_email_composer( $renter_email, 'booked_reservation', $email_args );
            homey_email_composer( $owner_email, 'admin_booked_reservation', $email_args );

            //Add host earning history
            homey_add_earning($reservation_id);

            //if( $is_instance_booking == 1 ) {
                
                if(homey_is_renter()) {
                    $reservation_page_link = homey_get_template_link('template/dashboard-reservations.php');
                } else {
                    $reservation_page_link = homey_get_template_link('template/dashboard-reservations2.php');
                }

                $return_link = add_query_arg( 'reservation_detail', $reservation_id, $reservation_page_link );
                wp_redirect( $return_link );
            //}
        }
    //} // $transfered_data
}
get_header();
?>

<section id="body-area">

    <div class="dashboard-page-title">
	<?php if($dashboarduser_role == 'homey_brokers' || $dashboarduser_role == 'homey_agents'){ ?>
	  <?php if(isset($_GET['type']) && $_GET['type'] == "history"){ ?>
	   <h1><?php echo esc_html__('Lead History', 'homey-child'); ?></h1>

	  <?php } elseif(isset($_GET['type']) && $_GET['type'] == "checkout"){ ?>
	   <h1><?php echo esc_html__('Lead Checkout', 'homey-child'); ?></h1>

	  <?php } elseif(isset($reser_status) && $reser_status == 'cancelled'){ ?>
	   <h1><?php echo esc_html__('Cancelled Leads', 'homey-child'); ?></h1>

	  <?php } elseif(isset($_GET['page']) && $_GET['page'] == "add_lead"){ ?>
	   <h1><?php echo esc_html__('Add New Lead', 'homey-child'); ?></h1>

    	  <?php } elseif(isset($_GET['type']) && $_GET['type'] == "pending"){ ?>
	   <h1><?php echo esc_html__('Pending Leads', 'homey-child'); ?></h1>

	  <?php } else { ?>
	   <h1><?php echo esc_html__('Active Leads', 'homey-child'); ?></h1>
	  <?php } ?>

	<?php } elseif($dashboarduser_role == 'homey_host'){ ?>
	   <h1><?php echo esc_html__('Bookings', 'homey-child'); ?></h1>

	<?php } else { ?>
        <h1><?php the_title(); ?></h1>

	<?php } ?>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <?php 
    if(isset($_GET['reservation_detail']) && $_GET['reservation_detail'] != "") {
        $resr_id = $_GET['reservation_detail'];
        $is_hourly = get_post_meta($resr_id, 'is_hourly', true);

        if($is_hourly == 'yes') {
            get_template_part('template-parts/dashboard/reservation/detail-hourly');
        } else {
            get_template_part('template-parts/dashboard/reservation/detail');
        }

    } elseif(isset($_GET['type']) && $_GET['type'] == "history"){
	get_template_part('template-parts/dashboard/reservation/list-history');

    } elseif(isset($_GET['type']) && $_GET['type'] == "pending"){
	get_template_part('template-parts/dashboard/reservation/list-pending');

    } elseif(isset($_GET['type']) && $_GET['type'] == "checkout"){
	get_template_part('template-parts/dashboard/reservation/list-checkout');

    } elseif(isset($_GET['page']) && $_GET['page'] == "add_lead"){
        get_template_part('template-parts/dashboard/profile/new-lead-information');
        get_template_part('template-parts/dashboard/profile/new-lead-clientinfo'); 

    } else {
        get_template_part('template-parts/dashboard/reservation/list');
    }
    ?>

</section><!-- #body-area -->


<?php get_footer();?>
