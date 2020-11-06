<?php
global $wpdb, $current_user, $userID, $homey_local, $homey_threads, $author;
global $dashboarduser_role;

$reservation_page_link = homey_get_template_link('template/dashboard-reservations.php');
$submission_page_link = homey_get_template_link('template/dashboard-submission.php');
$wallet_page_link = homey_get_template_link('template/dashboard-wallet.php');
$dashboard_listings = homey_get_template_link_dash('template/dashboard-listings.php');
$total_earnings = $author['total_earnings'];
$enable_wallet = homey_option('enable_wallet');
if($enable_wallet != 0) {
    $block_class = "block-col-33";
} else {
    $block_class = "block-col-50";
}

//-------------------------------------------------------
// generate query for pending listings
//-------------------------------------------------------
$pending_qargs = array(
     'post_type'      => 'homey_reservation',
     //'author'       => $userID,
     'posts_per_page' => -1,
     'post_status'    => 'pending', 
     'fields'	      => 'ids',
);
$pending_qargs = homey_listing_sort ($pending_qargs);
$pending_props = new WP_Query($pending_qargs); 
$meta_query = []; wp_reset_postdata();

?>
<div class="block">

    <div class="block-head">
	<?php if(homey_is_renter()) { ?>
	<div style="width:100%;float:left;">
           <h2 class="title text-center"><?php echo esc_attr($homey_local['welcome_back_text']); ?> <?php echo esc_attr($author['name']); ?> </h2>
	</div>
        <!--<div style="width:15%;float:left;">
	   <input type="hidden" name="renterid" value="<?php echo $userID; ?>"/>
	   <button type="button" name="become-a-host" class="btn btn-full-width" 
		   style="width:100%;font-size:12px;background-color:#f15e75;"><?php echo esc_html__('BECOME A HOST'); ?></button>
	   <div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i> 
		<?php echo esc_html__('Change Profile To Host', 'homey-child'); ?></div>
	</div>-->
        <?php }else{ ?>
	  <h2 class="title text-center"><?php echo esc_attr($homey_local['welcome_back_text']); ?> <?php echo esc_attr($author['name']); ?> </h2>
	<?php } ?>
    </div>
    <div class="block-verify">
	<?php if(!homey_is_renter()) { ?>
        <div class="block-col <?php echo esc_attr($block_class); ?>">
            <h3><?php echo esc_attr($homey_local['pr_listing_label']); ?></h3>
	    <?php if($dashboarduser_role == 'homey_brokers'){ ?>
            <p class="block-big-text"><?php echo homey_child_listing_count($userID); ?></p>
	    <p style="color:#FF0000;">
		<a href="<?php echo esc_url($dashboard_listings); ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		<?php esc_html_e('Approval Pending: ', 'homey'); ?><?php echo homey_child_listing_count_pending($userID); ?>
	    </p> 
            <a href="<?php echo esc_url($dashboard_listings); ?>"><?php esc_html_e('Manage', 'homey'); ?></a>
	    <?php }elseif($dashboarduser_role == 'homey_agents') { ?>
            <p class="block-big-text"><?php echo homey_child_listing_count($userID); ?></p>
            <a href="<?php echo esc_url($dashboard_listings); ?>"><?php esc_html_e('Manage', 'homey'); ?></a>
	    <?php } else { ?>
            <p class="block-big-text"><?php echo esc_attr($author['listing_count']); ?></p>
            <a href="<?php echo esc_url($submission_page_link); ?>"><?php esc_html_e('Add New', 'homey'); ?></a>
	    <?php } ?>
        </div>
	<?php } ?>
        <div class="block-col <?php echo esc_attr($block_class); ?>">
	   <?php if($dashboarduser_role == 'homey_agents' || $dashboarduser_role == 'homey_brokers') { ?>
            <h3><?php echo esc_html__('Active Leads'); ?></h3>
            <p class="block-big-text"><?php echo homey_reservation_count($userID); ?></p>
	    <?php if($dashboarduser_role == 'homey_brokers') { ?>
	    <p style="color:#FF0000;">
		<?php esc_html_e('Pending Leads: ', 'homey'); ?><?php echo count($pending_props->posts); ?>
		<a href="<?php echo esc_url($reservation_page_link); ?>?type=pending"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
	    </p> 
	    <?php } ?>
            <a href="<?php echo esc_url($reservation_page_link); ?>"><?php esc_html_e('Manage', 'homey'); ?></a>
	  <?php } else { ?>
            <!--<h3><?php //echo esc_attr($homey_local['pr_resv_label']); ?></h3>-->
	    <h3><?php echo esc_html__('Bookings'); ?></h3>
            <p class="block-big-text"><?php echo homey_reservation_count($userID); ?></p>
            <a href="<?php echo esc_url($reservation_page_link); ?>"><?php esc_html_e('Manage', 'homey'); ?></a>
	  <?php } ?>
        </div>
        <?php if($enable_wallet != 0 && homey_is_admin()) { ?>
        <div class="block-col <?php echo esc_attr($block_class); ?>">
            <h3><?php esc_html_e('Earnings', 'homey'); ?></h3>
            <p class="block-big-text">
                <?php 
                if($total_earnings != 0) {
                    echo homey_formatted_price($total_earnings); 
                } else {
                    echo homey_simple_currency_format($total_earnings);
                }
                ?>
            </p>
            <a href="<?php echo esc_url($wallet_page_link); ?>"><?php esc_html_e('Wallet', 'homey'); ?></a>
        </div>
        <?php } ?>
    </div>
</div>
