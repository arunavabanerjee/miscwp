<?php
global $current_user, $homey_local;

wp_get_current_user();
$userID = $current_user->ID;
$meta_query = array(); 
$booking_hide_fields = homey_option('booking_hide_fields');
$reservation_page_link = homey_get_template_link('template/dashboard-reservations.php');
$mine_link = add_query_arg( 'mine', 1, $reservation_page_link );

$total_reservations = homey_posts_count('homey_reservation');


$listing_no   =  '9';
$paged        = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'        =>  'homey_reservation',
    'paged'             => $paged,
    'posts_per_page'    => $listing_no,
    'orderby'   => array( 'date' =>'DESC' ),
);

if( homey_is_renter() ) {
    $meta_query[] = array(
        'key' => 'listing_renter',
        'value' => $userID,
        'compare' => '='
    );
    $args['meta_query'] = $meta_query;

} else {
    if(homey_is_admin()) {
        if(isset($_GET['mine']) && $_GET['mine'] == 1) {
            $meta_query[] = array(
                'key' => 'listing_owner',
                'value' => $userID,
                'compare' => '='
            );
        } else {
            $meta_query[] = array(
                'key' => 'listing_owner',
            );
        }

    } elseif($current_user->roles[0] == 'homey_brokers') {
            $meta_query[] = array(
                'key' => 'homey_brokers',
                'value' => $userID,
                'compare' => '='
            );
            $meta_query[] = array(
                'key' => 'reservation_status',
                'value' => array('under_review','in_progress','confirmed','booked','available','no_activity'),
                'compare' => 'IN'
            );
            $meta_query['relation'] = 'AND';

	    $sub_metaquery['relation'] = 'OR'; 
	
	    $sub_metaquery[] = array( 
		'key' => 'reservation_status_agent', 
                'value' => array('rejected'),
                'compare' => 'NOT IN'
	    ); 

	    $sub_metaquery[] = array( 
		'key' => 'reservation_status_agent', 
                'compare' => 'NOT EXISTS'
	    ); 	    

	    $meta_query[] = $sub_metaquery;  

    } elseif($current_user->roles[0] == 'homey_agents') {
        $meta_query[] = array(
           'key' => 'reservation_status',
           'value' => array('under_review','in_progress','confirmed','booked','available','no_activity'),
           'compare' => 'IN'
        );
	$sub_metaquery['relation'] = 'OR'; 
	
        $sub_metaquery[] = array(
           'key' => 'homey_agents',
           'value' => $userID,
           'compare' => '='
        );

	$sub_metaquery[] = array( 
	   'key' => 'homey_agents_notified', 
           'value' => "$userID",
           'compare' => 'LIKE'
	); 	    

	$meta_query[] = $sub_metaquery;  
 
    } elseif($current_user->roles[0] == 'homey_host') {

	//get all listings for the user
	$listing_query_args = array(
        	'post_type' => 'listing',
        	'posts_per_page' => -1,
        	'post_status' => 'publish',
		'fields' => 'ids',
		'author' => $userID,
        );
	$host_listings = new WP_Query( $listing_query_args );
	$host_listing_ids = $host_listings->posts; //var_dump($host_listing_ids); 

	if(!empty($host_listing_ids)){
	  $meta_query[] = array(
        	'key' => 'reservation_listing_id',
        	'value' => $host_listing_ids,
        	'compare' => 'IN'
          );
          $meta_query[] = array(
        	'key' => 'reservation_status',
        	'value' => array('in_progress','available','confirmed','no_activity','booked','declined','completed','cancelled'),
        	'compare' => 'IN'
          );
          $meta_query['relation'] = 'AND';
          $args['meta_query'] = $meta_query;

	}else{
	    // this is generate an empty set
            $meta_query[] = array(
                'key' => 'listing_owner',
                'value' => $userID,
                'compare' => '='
            );
            $args['meta_query'] = $meta_query;
	}

    } else {
        $meta_query[] = array(
            'key' => 'listing_owner',
            'value' => $userID,
            'compare' => '='
        );
    }
    $args['meta_query'] = $meta_query;
}

$res_query = new WP_Query($args);
?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">
                        <div class="block">
                            <div class="block-title">
				<?php if($current_user->roles[0] == 'homey_brokers' || $current_user->roles[0] == 'homey_agents'){ ?>
                                <h2 class="title"><?php echo esc_html__('Manage Leads'); ?></h2> 
				<?php } else { ?>
                                <h2 class="title"><?php echo esc_attr($homey_local['manage_label']); ?></h2>
				<?php } ?>
                                <?php if(homey_is_admin()) { ?>
                                <div class="mt-10">
                                    <a class="btn btn-primary btn-slim" href="<?php echo esc_url($reservation_page_link); ?>"><?php esc_html_e('All', 'homey'); ?> (<?php echo $total_reservations; ?>)</a>
                                    <a class="btn btn-primary btn-slim" href="<?php echo esc_url($mine_link); ?>">
                                        <?php esc_html_e('Mine', 'homey'); ?> (<?php echo homey_reservation_count($userID); ?>)
                                    </a>
                                </div>
                                <?php } ?>
                            </div>

                            <?php
                            if( $res_query->have_posts() ): ?>
                            <div class="table-block dashboard-reservation-table dashboard-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><?php echo esc_attr($homey_local['id_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['status_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['date_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['address']); ?></th>
                                            <!--<th><?php //echo esc_attr($homey_local['check_in']); ?></th>
                                            <th><?php //echo esc_attr($homey_local['check_out']); ?></th>
                                            <?php //if($booking_hide_fields['guests'] != 1) {?>
                                            <th><?php //echo homey_option('glc_guests_label');?></th>
                                            <?php //} ?>
                                            <th><?php //echo esc_attr($homey_local['pets_label']);?></th>
                                            <th><?php //echo esc_attr($homey_local['subtotal_label']); ?></th>-->
					    <th><?php echo esc_html__('Received Dt'); ?></th>
                                            <th><?php echo esc_html__('Lead Type'); ?></th>
                                            <th><?php echo esc_html__('Agent Assigned'); ?></th>
                                            <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($res_query->have_posts()): $res_query->the_post(); 
                                            $is_hourly = get_post_meta(get_the_ID(), 'is_hourly', true);
                                            if($is_hourly == 'yes') {
                                                get_template_part('template-parts/dashboard/reservation/item-hourly');
                                            } else {
                                                get_template_part('template-parts/dashboard/reservation/item');
                                            }
                                        endwhile;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: 
                                    echo '<div class="block-body">';
                                    echo esc_attr($homey_local['reservation_not_found']);  
                                    echo '</div>'; 
                            endif; ?>
                        </div><!-- .block -->    
                    </div><!-- .dashboard-area -->

                    <?php homey_pagination( $res_query->max_num_pages, $range = 2 ); ?>

                </div><!-- col-lg-12 col-md-12 col-sm-12 -->
            </div>
        </div><!-- .container-fluid -->
    </div><!-- .dashboard-content-area -->    
</div><!-- .user-dashboard-right -->
