<?php 
global $homey_prefix, $homey_local, $post;
$listing_author = homey_get_author('40', '40', 'img-circle media-object avatar');

$received_date = date('Y-m-d', strtotime($post->post_date));
$check_in = get_post_meta(get_the_ID(), 'reservation_checkin_date', true);
$check_out = get_post_meta(get_the_ID(), 'reservation_checkout_date', true);
$reservation_guests = get_post_meta(get_the_ID(), 'reservation_guests', true);
$listing_id = get_post_meta(get_the_ID(), 'reservation_listing_id', true);
$listing_address    = get_post_meta( $listing_id, $homey_prefix.'listing_address', true );
$pets   = get_post_meta($listing_id, $homey_prefix.'pets', true);
$deposit = get_post_meta(get_the_ID(), 'reservation_upfront', true);
$total_amount = get_post_meta(get_the_ID(), 'reservation_total', true);
$reservation_status = get_post_meta(get_the_ID(), 'reservation_status', true);
$reservation_status_ag = get_post_meta(get_the_ID(), 'reservation_status_agent', true);

//additional variables
$lead_type = get_post_meta(get_the_ID(), 'homey_lead_type', true);
$agent_assigned_ids = get_post_meta(get_the_ID(), 'homey_agents', false); 
$agent_notified_ids = get_post_meta(get_the_ID(), 'homey_agents_notified', true); 

$dashboard_user = wp_get_current_user(); 
$dashboarduser_role = $dashboard_user->roles[0]; 

//listing address for other user will only be location and postcode.
if($dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents'){
  if(!empty($listing_address)){  
    $address_array = explode(',',$listing_address); 
    $listing_address = $address_array[sizeof($address_array)-1];
  }
}

if(homey_is_renter()) {
    $reservation_page_link = homey_get_template_link('template/dashboard-reservations.php');
} else {

    if(!homey_listing_guest(get_the_ID())) {
        $reservation_page_link = homey_get_template_link('template/dashboard-reservations.php');
    } else {
        $reservation_page_link = homey_get_template_link('template/dashboard-reservations2.php');
    }
}

$detail_link = add_query_arg( 'reservation_detail', get_the_ID(), $reservation_page_link );

$no_upfront = homey_option('reservation_payment');
$booking_hide_fields = homey_option('booking_hide_fields');

if($no_upfront == 'no_upfront') {
    $price = '';
} else {
    $price = $deposit;
}

$is_read = $status_label = '';

if($pets != 1) {
    $pets_allow = $homey_local['text_no'];
} else {
    $pets_allow = $homey_local['text_yes'];
}

if( !homey_is_renter() ) {
    if($reservation_status == 'under_review') {
        $is_read = 'msg-unread';
    }
}

if ( is_page_template( array('template/dashboard.php') ) ) {
    $is_read = '';
}

?>
<tr class="<?php echo esc_attr($is_read); ?>">
    <td data-label="<?php esc_html_e('Author', 'homey'); ?>">
        <?php if(!empty($listing_author['photo'])) { 
            echo '<a href="'.esc_url($listing_author['link']).'" target="_blank">'.$listing_author['photo'].'</a>';
        } 
        ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['id_label']); ?>">
        <?php echo '#'.get_the_ID(); ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['status_label']); ?>">
        <?php homey_reservation_label($reservation_status, get_the_ID()); echo '<br/>'; ?>
	<?php if(!empty($reservation_status_ag)){ ?>
	<?php homey_reservation_label($reservation_status_ag, get_the_ID()); ?>
	<?php } ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['date_label']); ?>">
        
        <?php esc_attr( the_time( get_option( 'date_format' ) ));?><br>
        <?php esc_attr( the_time( get_option( 'time_format' ) ));?>        
        
    </td>
    <td data-label="<?php echo esc_attr($homey_local['address']); ?>">
        <a href="<?php echo get_permalink($listing_id); ?>"><strong><?php echo get_the_title($listing_id); ?></strong></a>
        <?php if(!empty($listing_address)) { ?>
            <address><?php echo esc_attr($listing_address); ?></address>
        <?php } ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['check_in']); ?>">
        <?php echo homey_format_date_simple($check_in); ?>
    </td>

    <!--<td data-label="<?php //echo esc_attr($homey_local['check_out']); ?>">
        <?php //echo homey_format_date_simple($check_out); ?>
    </td>
    <?php //if($booking_hide_fields['guests'] != 1) {?>
    <td data-label="<?php //echo homey_option('glc_guests_label');?>">
        <?php //echo esc_attr($reservation_guests); ?>
    </td>
    <?php // } ?>
    <td data-label="<?php //echo esc_attr($homey_local['pets_label']);?>">
        <?php //echo esc_attr($pets_allow); ?>
    </td>
    <td data-label="<?php //echo esc_attr($homey_local['subtotal_label']); ?>">
        <strong><?php //echo homey_formatted_price($price); ?></strong>
    </td>-->

    <td data-label="<?php echo esc_html__('lead_type'); ?>">
	<?php $lead_style_client = 'style="color:#4d1a1a;background-color:#54c4d9;border-color:#54c4d9;';
	      $lead_style_client .= 'padding:7px;border-radius:5px;font-size:12px;"'; 
	      $lead_style_soft = 'style="color:#4d1a1a;background-color:#f15e75;border-color:#f15e75;';
	      $lead_style_soft .= 'padding:7px;border-radius:5px;font-size:12px;"'; 
	?>
        <?php if($lead_type == '' || $lead_type == 'soft_lead'){ ?>
	<span <?php echo $lead_style_soft; ?>><?php echo esc_html__('Soft Lead'); ?></span>
	<?php } else { ?>
	<span <?php echo $lead_style_client; ?>><?php echo esc_html__('Client Lead'); ?></span>
	<?php } ?>
    </td>

    <td data-label="<?php echo esc_html__('agent_assigned'); ?>">
	<?php if(empty($agent_assigned_ids) || $agent_assigned_ids[0] == 0){ ?>
	<span><?php echo esc_html__('None Assigned'); ?></span>
	<?php } else{ 
	       $agent_info = get_userdata($agent_assigned_ids[0]); 
	       $agent_displayname = $agent_info->display_name; ?>
	<span><?php echo $agent_displayname; ?></span>
	<?php } ?>
    </td>

    <td data-label="<?php echo esc_html__('agent_notified'); ?>">
	<?php if(empty($agent_notified_ids)){ ?>
	<span><?php echo esc_html__('None Notified'); ?></span>
	<?php } else{ 
	       $agents = explode(',',$agent_notified_ids);
	       $agent_names = ''; 
	       foreach($agents as $agId){
	        $agent_info = get_userdata($agId); 
	        $agent_names .= $agent_info->display_name."\n"; 
	       }
	?>
	<span><?php echo nl2br($agent_names); ?></span>
	<?php } ?>
    </td>

    <td data-label="<?php echo esc_attr($homey_local['actions_label']); ?>">
        <div class="custom-actions">
            <?php 
	    $btnstyle = 'style="padding:0px 8px;margin:2px;"';
            if( homey_listing_guest(get_the_ID()) ) {
                if($reservation_status == 'available') {
                    echo '<a href="'.esc_url($detail_link).'" class="btn btn-success" '.$btnstyle.'>'.$homey_local['res_paynow_label'].'</a>';
                } else {
                    echo '<a href="'.esc_url($detail_link).'" class="btn btn-secondary" '.$btnstyle.'>'.$homey_local['res_details_label'].'</a>';
                }
            } else {
                if($reservation_status == 'under_review') {
                    //echo '<a href="'.esc_url($detail_link).'" class="btn btn-success" '.$btnstyle.'>'.$homey_local['res_confirm_label'].'</a><br/>';
		    if($dashboarduser_role == 'homey_agents'){
                      echo '<a href="'.esc_url($detail_link).'" class="btn btn-secondary" '.$btnstyle.'>'.$homey_local['res_details_label'].'</a><br/>'; 
		      if($reservation_status == 'under_review'){
		  	echo '<button type="button" name="new-lead-agent-denial" class="btn btn-full-width" 
				style="font-size:11px;font-weight:bold;border:1px solid #f15e75;background-color:#f15e75;">'.esc_html__('Reject').'</button>';
 		      }
		    }
		    if($dashboarduser_role == 'homey_brokers'){ 
		      echo '<a href="'.esc_url($detail_link).'" class="btn btn-secondary" '.$btnstyle.'>'.$homey_local['res_details_label'].'</a><br/>'; 
		      $user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' );
   	              $agents = get_users($user_args);
                      echo "<select class='homey-selagents' name='homey-agents-brokers-dashboard' style='width:77%;'>";
                      echo '<option value=0>'.esc_html__('Assign').'</option>';
                      foreach ($agents as $agent) { 
     		        $agent_info = get_userdata($agent->ID);
                        echo '<option value="'.$agent_info->ID.','.$dashboard_user->ID.','.get_the_ID().'">'.$agent_info->display_name.'</option>';
   	              }
   	              echo "</select>";
		      echo '<div class="dashboard-agent-loader-spinner"></div>';
		    }
                } else {
                    echo '<a href="'.esc_url($detail_link).'" class="btn btn-secondary" '.$btnstyle.'>'.$homey_local['res_details_label'].'</a>';
                }
            }
            ?>
	   <input type="hidden" name="reservationid" value="<?php echo $post->ID; ?>"/>
        </div>
    </td>
</tr>
