<?php
global $wpdb, $current_user, $userID, $homey_local, $homey_threads, $author;
global $dashboarduser_role;

wp_get_current_user();
$userID = $current_user->ID;

$reservation_page = homey_get_template_link_dash('template/dashboard-reservations.php');
$messages_page = homey_get_template_link_dash('template/dashboard-messages.php');
$author = homey_get_author_by_id('100', '100', 'img-circle', $userID);
$user_post_count = count_user_posts( $userID , 'listing' );

$tabel = $wpdb->prefix . 'homey_threads';
$message_query = $wpdb->prepare( 
    "
    SELECT * 
    FROM $tabel 
    WHERE sender_id = %d OR receiver_id = %d
    ORDER BY seen ASC LIMIT 5
    ", 
    $userID,
    $userID
);

$homey_threads = $wpdb->get_results( $message_query );

$is_renter = false;
if(homey_is_renter()) {
    $is_renter = true;
}
$admin_class = '';
if(homey_is_admin()) {
    $admin_class = 'admin-top-banner';
}
?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area <?php echo esc_attr($admin_class); ?>">
        
                        <div class="block">
                            <?php /* if(homey_is_renter()) { ?>
                            <!--<div class="block-head text-center">
                            <h2 class="title">
				<?php //echo esc_attr($homey_local['welcome_back_text']); ?> 
				<?php //echo esc_attr($author['name']); ?>        
                            </h2>
                            </div>-->
                            <?php }*/ ?>
                            <?php
                            if(homey_is_admin()) { 
                                get_template_part('template-parts/dashboard/admin-stats');
                            //} elseif (!homey_is_admin() && !homey_is_renter()) {
			    } elseif (!homey_is_admin() ) {
                                get_template_part('template-parts/dashboard/host-stats');
                            }
                            ?>
                        </div><!-- .block -->

			<!-- Nav tabs -->
		<ul class="nav nav-pills" id="detailsTab" role="tablist">
		  <li class="nav-item" style="border:1px solid #EEECEC;border-radius:5px;background:#DDD;">
    		    <a class="nav-link active" id="recent-reservations-tab" data-toggle="pill" role="tab" aria-controls="recent-reservations" 
			aria-selected="true" data-target="#recent-reservations">Recent Leads/Bookings</a>
  		  </li>

		  <?php if($dashboarduser_role == 'homey_host'){ ?>
  		  <li class="nav-item" style="border:1px solid #EEECEC;border-radius:5px;background:#EEE;">
    		    <a class="nav-link" id="selected-properties-tab" data-toggle="pill" role="tab" aria-controls="selected-properties" 
			aria-selected="false" data-target="#selected-properties">Properties Selected (Agent)</a>
  		  </li>
		  <?php } ?>

		</ul>
              
		<!-- Tab panes -->
		<div class="tab-content">          

                        <div class="block tab-pane active" id="recent-reservations" role="tabpanel" aria-labelledby="recent-reservations-tab">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title">
                                        <?php 
                                        if($is_renter) {
                                            echo esc_attr($homey_local['my_resv']);
					} elseif($dashboarduser_role == 'homey_agents' || $dashboarduser_role == 'homey_brokers'){
					    echo esc_html__('Recent Leads'); 
					} elseif($dashboarduser_role == 'homey_host'){
					    echo esc_html__('Recent Bookings For Properties');  
                                        } else {
                                            echo esc_attr($homey_local['upcoming_resv']); 
                                        }
                                        ?>
                                    </h2>
                                </div>

                                <?php if(!empty($reservation_page)) { ?>
                                <div class="block-right">
                                    <a href="<?php echo esc_url($reservation_page); ?>" class="block-link pull-right">
                                        <?php echo esc_attr($homey_local['view_all_label']); ?> 
                                        <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <?php } ?>

                            </div>
                            
                            <?php 
                            $args = array(
                                'post_type'        =>  'homey_reservation',
                                'posts_per_page'   => 9,
                            );

                            if( $is_renter ) {
                                $meta_query[] = array(
                                    'key' => 'listing_renter',
                                    'value' => $userID,
                                    'compare' => '='
                                );
                                $args['meta_query'] = $meta_query;

			   } elseif($dashboarduser_role == 'homey_agents') { 
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
                                $args['meta_query'] = $meta_query;

			   } elseif($dashboarduser_role == 'homey_brokers') { 
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
                                $args['meta_query'] = $meta_query; 

			   } elseif($dashboarduser_role == 'homey_host') { 

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

			      }else { // this is to generate an empty set
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
                                $meta_query[] = array(
                                    'key' => 'reservation_status',
                                    'value' => 'under_review',
                                    'compare' => '='
                                );
                                $meta_count = count($meta_query);
                                if( $meta_count > 1 ) {
                                    $meta_query['relation'] = 'AND';
                                }
                                $args['meta_query'] = $meta_query;
                            }
			    $res_query = new WP_Query($args);

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
                                            <!--<th><?php echo esc_attr($homey_local['check_in']); ?></th>
                                            <th><?php echo esc_attr($homey_local['check_out']); ?></th>
                                            <th><?php echo homey_option('glc_guests_label');?></th>
                                            <th><?php echo esc_attr($homey_local['pets_label']);?></th>
                                            <th><?php echo esc_attr($homey_local['subtotal_label']); ?></th>-->
   					    <th><?php echo esc_html__('Received Dt'); ?></th>
                                            <th><?php echo esc_html__('Lead Type'); ?></th>
                                            <th><?php echo esc_html__('Agent Assigned'); ?></th>
                                            <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($res_query->have_posts()): $res_query->the_post(); 
                                            get_template_part('template-parts/dashboard/reservation/item');
                                        endwhile;
                                        wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: 
                                    echo '<div class="block-body">';
                                    echo esc_attr($homey_local['upcoming_reservation_not_found']);  
                                    echo '</div>'; 
                            endif;  
                            ?>
                        </div><!-- .block -->

			<?php if($dashboarduser_role == 'homey_host'){ ?>
                        <div class="block tab-pane" id="selected-properties" role="tabpanel" aria-labelledby="selected-properties-tab">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php echo esc_html__('Properties Selected For Leads (Agent):'); ?></h2>
                                </div>
                                <?php if(!empty($reservation_page)) { ?>
                                <div class="block-right">
                                    <a href="<?php echo esc_url($reservation_page); ?>" class="block-link pull-right">
                                      <?php echo esc_attr($homey_local['view_all_label']);?><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>

                            <?php 
			    // get all reservations/leads
                            $args = array(
                                'post_type'        =>  'homey_reservation',
                                'posts_per_page'   => -1,
				'fields'	   => 'ids',
                            );
			    $tres_query = new WP_Query($args);
			    if( $tres_query->have_posts() ): ?>
                            <div class="table-block dashboard-reservation-table dashboard-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><?php echo esc_attr($homey_local['id_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['status_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['date_label']); ?></th>
                                            <th><?php echo esc_attr($homey_local['address']); ?></th>
                                            <!--<th><?php echo esc_attr($homey_local['check_in']); ?></th>
                                            <th><?php echo esc_attr($homey_local['check_out']); ?></th>
                                            <th><?php echo homey_option('glc_guests_label');?></th>
                                            <th><?php echo esc_attr($homey_local['pets_label']);?></th>
                                            <th><?php echo esc_attr($homey_local['subtotal_label']); ?></th>-->
   					    <th><?php echo esc_html__('Received Dt'); ?></th>
                                            <th><?php echo esc_html__('Lead Type'); ?></th>
                                            <th><?php echo esc_html__('Agent Assigned'); ?></th>
                                            <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
					global $post; $count=0;
					foreach($tres_query->posts as $reservId){
					  //get the listing id for the reservation 
					  $listing_id = get_post_meta($reservId, 'reservation_listing_id', true); 
					  $author_id = get_post_field ('post_author', $listing_id); 
					  if($author_id != $userID){
					     $hosts_contacted = get_post_meta($reservId, 'reservation_host_contacts', true); 
					     if(!empty($hosts_contacted)){
					       $hosts_contacted_array = explode(',',$hosts_contacted);
					       if(in_array($userID, $hosts_contacted_array)){
					         $post = get_post($reservId); $count++;
                                                 get_template_part('template-parts/dashboard/reservation/item'); 
					       }
					     }
					  }
					}
					wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>
				<?php
				  if($count==0){ 
                                    echo '<div class="block-body">';
                                    echo esc_attr($homey_local['upcoming_reservation_not_found']);  
                                    echo '</div>'; 
				  } ?>
                            </div>
                            <?php else: 
                                    echo '<div class="block-body">';
                                    echo esc_attr($homey_local['upcoming_reservation_not_found']);  
                                    echo '</div>'; 
                            endif; ?>
			</div> <!-- end block -->
			<?php } ?>

		</div> <!-- end tab-content -->

                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php echo esc_attr($homey_local['recent_msg']); ?></h2>
                                </div>
                                <div class="block-right">
                                    <a href="<?php echo esc_url($messages_page); ?>" class="block-link pull-right"><?php echo esc_attr($homey_local['view_all_label']); ?> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="table-block dashboard-message-table">
                                <?php get_template_part('template-parts/dashboard/messages/messages'); ?>
                            </div>
                        </div><!-- .block -->

                    </div><!-- .dashboard-area -->
                </div><!-- col-lg-12 col-md-12 col-sm-12 -->
            </div> <!-- .row -->
        </div><!-- .container-fluid -->
    </div><!-- .dashboard-content-area -->    
</div><!-- .user-dashboard-right -->
