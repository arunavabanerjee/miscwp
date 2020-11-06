<?php
$dashboard = homey_get_template_link_2('template/dashboard.php');
$all_users = add_query_arg( 'page', 'users', $dashboard );
$args = array();

$role = isset($_GET['role']) ? $_GET['role'] : ''; 
if(!empty($role)) {
    $args['role'] = $role;
} else {
    $args['role'] = '';
}

$search_term = isset($_GET['search']) ? $_GET['search'] : ''; 

$dashboard_user = wp_get_current_user(); 
$dashboarduser_role = $dashboard_user->roles[0]; 

if(!empty($search_term)) {

    if(is_email($search_term)) {
        $args['search']       = $search_term;
        $args['search_columns'] = array( 'user_login', 'user_email', 'display_name', 'user_nicename' );
    } else {
        $args['search'] = '*' . esc_attr( $search_term ) . '*';
        $args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key'     => 'first_name',
                'value'   => $search_term,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'last_name',
                'value'   => $search_term,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'description',
                'value'   => $search_term ,
                'compare' => 'LIKE'
            )
        );
    }
}

if($dashboarduser_role == 'homey_brokers'){
   $args['role'] = 'homey_agents'; 
}

$user_query = new WP_User_Query( $args );

$users_count = count_users();
?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">
                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php esc_html_e('Manage', 'homey'); ?></h2>
				    <?php if(homey_is_admin()) { ?>
                                    <div class="mt-10">
                                        <a class="btn btn-primary btn-slim" href="<?php echo esc_url($all_users); ?>"><?php esc_html_e('All', 'homey'); ?> (<?php echo esc_attr($users_count['total_users']); ?>)</a>
                                        <?php
                                        foreach($users_count['avail_roles'] as $role => $count) {
                                            $user_link = add_query_arg( array( 'page' => 'users', 'role' => $role, ), $dashboard );
                                            if($role != 'none') {
                                            	echo '<a class="btn btn-primary btn-slim" href="'.esc_url($user_link).'">'.homey_get_role_text($role).' ('.esc_attr($count).')</a>'.' ';
                                            }
                                        }
                                        ?>
                                    </div>
				    <?php } ?>
                                </div>
                                <div class="block-right">
                                    <div class="dashboard-form-inline">
                                        <form method="get" action="<?php echo esc_url($dashboard); ?>" class="form-inline">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="<?php esc_html_e('Search', 'homey'); ?>">
                                            </div>
                                            <input type="hidden" name="page" value="users">
                                            <button type="submit" class="btn btn-primary btn-search-icon"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-block dashboard-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo esc_html__('Avatar', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Name', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Email', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Role', 'homey'); ?></th>

					    <?php if($dashboarduser_role != 'homey_brokers'){ ?>
                                            <th><?php echo esc_html__('Email Verification', 'homey'); ?></th>
                                            <th><?php echo esc_html__('ID Verification', 'homey'); ?></th>
					    <?php } else { ?>
                                            <th><?php echo esc_html__('Properties', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('Leads', 'homey-child'); ?></th>
					    <?php } ?>

                                            <th><?php echo esc_html__('Actions', 'homey'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ( ! empty( $user_query->get_results() ) ) {
                                            foreach ( $user_query->get_results() as $user ) {
                                                $user_id = $user->ID;  
                                                $user_data = homey_get_author_by_id('40', '40', 'img-circle', $user_id);
                                                $is_superhost = $user_data['is_superhost'];

                                                $doc_verified = $user_data['doc_verified'];
                                                $user_document_id = $user_data['user_document_id'];
                                                $doc_verified_request = $user_data['doc_verified_request'];

                                                $user_role = homey_get_role_name($user_id);

						//get all listings for the agent
						if($dashboarduser_role == 'homey_brokers'){ 
						  $agent_listings = array(); 
						  $largs = array( 'post_type' => 'listing', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
						  $lmeta_query[] = array( 'key' => 'homey_assignedagent', 
									 'value' => $user_id, 'compare' => '=' );
						  $largs['meta_query'] = $lmeta_query;
						  $lqry_res = new WP_Query($largs); 
						  if ( $lqry_res->have_posts() ){
						  foreach($lqry_res->posts as $prop){ 
						    array_push($agent_listings, $prop->ID);
						  }} 
					          wp_reset_postdata(); $lmeta_query=[];
						}

						//get all listings for the agent
						if($dashboarduser_role == 'homey_brokers'){ 
						  $agent_leads = array(); 
						  $rargs = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
						  $rmeta_query[] = array( 'key' => 'homey_agents', 
									 'value' => $user_id, 'compare' => '=' );
						  $rargs['meta_query'] = $rmeta_query;
						  $rqry_res = new WP_Query($rargs); //var_dump($rqry_res);
						  if ( $rqry_res->have_posts() ){
						  foreach($rqry_res->posts as $res1){ 
						    array_push($agent_leads, $res1->ID);
						  }}
						  wp_reset_postdata(); $rmeta_query=[];
						}

                                                $single_user_link = add_query_arg( array(
                                                    'page' => 'users',
                                                    'user-id' => $user_id,
                                                ), $dashboard );

                                            ?>
                                                <tr>
                                                    <td data-label="<?php echo esc_html__('Avatar', 'homey'); ?>">
                                                        <?php echo $user_data['photo']; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Name', 'homey'); ?>">
                                                        <?php echo $user_data['name']; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Email', 'homey'); ?>">
                                                        <?php echo $user_data['email']; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Role', 'homey'); ?>">
                                                        <?php if($is_superhost) { ?>
                                                        <i class="fa fa-bookmark host_role" aria-hidden="true"></i> 
                                                        <?php } ?>
                                                        <?php if($user_role == 'homey_agents'){ echo 'Agents'; } else { echo $user_role; } ?>
                                                    </td>

						    <?php if($dashboarduser_role == 'homey_brokers'){ ?>
                                                    <td data-label="<?php echo esc_html__('Listings', 'homey-child'); ?>">
							<?php if (!empty($agent_listings)){ ?>
                                                        <p><ul style="padding-left:0;">
							<?php foreach($agent_listings as $listingId){ 
							  echo '<li style="list-style:none;float:left;padding:5px;background:#EFBFCF;border-radius:6px;margin-right:2px;">';
							  echo '<a href="'.get_permalink($listingId).'">'.$listingId.'</a></li>';
							} ?>
							</ul></p>
							<?php } else { ?>
							<p><?php echo esc_html__('Not Assigned Yet', 'homey-child'); ?></p>
							<?php } ?>
                                                    </td>

                                                    <td data-label="<?php echo esc_html__('Reservations', 'homey-child'); ?>">
							<?php if (!empty($agent_leads)){ ?>
							<?php $mainUrl = site_url().'/reservations/?reservation_detail='; ?>
                                                        <p><ul style="padding-left:0;">
							<?php foreach($agent_leads as $leadId){ 
							  echo '<li style="list-style: none;float: left;padding: 5px;background: #EFBFCF;border-radius: 6px;margin-right:2px;">';
							  echo '<a href="'.$mainUrl.$leadId.'">'.$leadId.'</a></li>';
							} ?>
							</ul></p>
							<?php } else { ?>
							<p><?php echo esc_html__('Not Assigned Yet', 'homey-child'); ?></p>
							<?php } ?>
                                                    </td>
						    <?php } else { ?>
                                                    <td data-label="<?php echo esc_html__('Email Verification', 'homey'); ?>">
                                                        <span class="label label-success"><?php esc_html_e('VERIFIED', 'homey'); ?></span>
                                                    </td>

                                                    <td data-label="<?php echo esc_html__('ID Verification', 'homey'); ?>">
                                                        <?php 
                                                        if($doc_verified) { 
                                                            echo '<span class="label label-success">'.esc_html__('VERIFIED', 'homey').'</span>';
                                                        } else {
                                                            if($doc_verified_request == '') {
                                                                echo '<span>-</span>';
                                                            } else {
                                                                echo '<span class="label label-warning">'.esc_html__('PENDING', 'homey').'</span>';
                                                            }
                                                        }
                                                        ?>
                                                    </td>
						    <?php } ?>

                                                    <td data-label="<?php echo esc_html__('Actions', 'homey'); ?>">
                                                        <div class="custom-actions">
                                                            <a class="btn btn-success" href="<?php echo esc_url($single_user_link); ?>"><?php esc_html_e('Detail', 'homey'); ?></a>
							    <?php if($dashboarduser_role == 'homey_brokers'){ ?>
							    <button class="btn-action btn-delete-agent"  name="btn-delete-agent" data-toggle="tooltip" 
							   data-placement="top" data-original-title="<?php echo esc_html__('Delete Agent','homey-child'); ?>"
							   style="background:#f15e75;border-color:#f15e75;color:#FFF;padding:2px 5px;font-weight: bold;">
							    Delete</button>
							    <input type="hidden" name="agentid" value="<?php echo $user_id; ?>"/> 
							    <input type="hidden" name="brokerid" value="<?php echo $dashboard_user->ID; ?>"/>
							    <?php } ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php        
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">';
                                            echo esc_html__('No users found.', 'homey');
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .block -->
                        <?php //include 'inc/listing/pagination.php';?>
                    </div><!-- .dashboard-area -->
                </div><!-- col-lg-12 col-md-12 col-sm-12 -->
            </div>
        </div><!-- .container-fluid -->
    </div><!-- .dashboard-content-area --> 
</div><!-- .user-dashboard-right -->
