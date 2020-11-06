<?php
$dashboard = homey_get_template_link_2('template/dashboard.php');
$all_clients = add_query_arg( 'page', 'clients', $dashboard );
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

$dashboard_user_id = $dashboard_user->ID; 

/** query to find the list of leads for the logged in users */
$listing_no   =  '12';
$paged        = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'        => 'homey_reservation',
    'paged'            => $paged,
    'posts_per_page'   => $listing_no,
);

if($dashboarduser_role == 'homey_brokers'){
   $meta_query[] = array(
        'key' => 'homey_brokers',
        'value' => $dashboard_user_id,
        'compare' => '='
    );
   $meta_query[] = array(
        'key' => 'listing_renter',
        'value' => 0,
        'compare' => '='
    );
   $meta_query['relation'] = 'AND';
   $args['meta_query'] = $meta_query;

}elseif($dashboarduser_role == 'homey_agents'){
   $meta_query[] = array(
        'key' => 'homey_agents',
        'value' => $dashboard_user_id,
        'compare' => '='
    );
   $meta_query[] = array(
        'key' => 'listing_renter',
        'value' => 0,
        'compare' => '='
    );
   $meta_query['relation'] = 'AND';
   $args['meta_query'] = $meta_query; 
} 
$res_query = new WP_Query($args); //var_dump($res_query); 

?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">

			<!-- -------------------------------------------------- 
			---- Clients Created By Brokers 
			---------------------------------------------------- -->			
			<?php if($dashboarduser_role == 'homey_brokers') { ?>
			<?php $args['meta_query'] = array( 
			       array( 'key' => 'created_by', 'value' => $dashboard_user_id, 'compare' => '=' ), );
			      $users_created = new WP_User_Query( $args ); ?>
			<!-- .block -->
                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php esc_html_e('View: Clients Created', 'homey'); ?></h2>
                                </div>
                                <div class="block-right">
                                    <div class="dashboard-form-inline">
                                        <form method="get" action="<?php echo esc_url($all_clients); ?>" class="form-inline">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="<?php esc_html_e('Search', 'homey'); ?>">
                                            </div>
                                            <input type="hidden" name="page" value="clients">
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

					    <?php if(homey_is_admin()){ ?>
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
                                        if ( ! empty( $users_created->get_results() ) ) {
                                            foreach ( $users_created->get_results() as $user ) {
                                                $user_id = $user->ID; 
                                                $user_data = homey_get_author_by_id('40', '40', 'img-circle', $user_id);
                                                $is_superhost = $user_data['is_superhost'];

                                                $doc_verified = $user_data['doc_verified'];
                                                $user_document_id = $user_data['user_document_id'];
                                                $doc_verified_request = $user_data['doc_verified_request'];

                                                //$user_role = homey_get_role_name($user_id);

						$user_role = $user->roles[0];

						//get all listings for the client
						if($dashboarduser_role == 'homey_brokers'){ 
						  $client_listings = array(); 
						  $largs = array( 'post_type' => 'listing', 'posts_per_page' => -1, 
								  'post_status' => 'publish', 'author' => $user_id); 
						  $lqry_res = new WP_Query($largs); 
						  if ( $lqry_res->have_posts() ){
						  foreach($lqry_res->posts as $prop){ 
						    array_push($client_listings, $prop->ID);
						  }} 
					          wp_reset_postdata(); $lmeta_query=[];
						}

						//get all leads for the client
						if($dashboarduser_role == 'homey_brokers'){ 
						  $client_leads = array(); 
						  $rargs = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
						  $rmeta_query[] = array( 'key' => 'listing_renter', 'value' => $user_id, 'compare' => '=' );
						  $rargs['meta_query'] = $rmeta_query;
						  $rqry_res = new WP_Query($rargs); //var_dump($rqry_res);
						  if ( $rqry_res->have_posts() ){
						  foreach($rqry_res->posts as $res1){ 
						    array_push($agent_leads, $res1->ID);
						  }}
						  wp_reset_postdata(); $rmeta_query=[];
						}
                                                $single_user_link = add_query_arg( array( 'page' => 'users', 'user-id' => $user_id, ), $dashboard );

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
                                                        <?php if($user_role == 'homey_host'){ echo 'Host'; } else { echo 'Renter'; } ?>
                                                    </td>

						    <?php if($dashboarduser_role == 'homey_brokers'){ ?>
                                                    <td data-label="<?php echo esc_html__('Listings', 'homey-child'); ?>">
							<?php if (!empty($client_listings)){ ?>
                                                        <ul style="padding-left:0;">
							<?php foreach($client_listings as $listingId){ 
							  echo '<li style="list-style:none;float:left;padding:5px;background:#EFBFCF;border-radius:6px;margin-right:2px;">';
							  echo '<a href="'.get_permalink($listingId).'">'.$listingId.'</a></li>';
							} ?>
							</ul>
							<?php } else { ?>
							<span><?php echo esc_html__('None', 'homey-child'); ?></span>
							<?php } ?>
                                                    </td>

                                                    <td data-label="<?php echo esc_html__('Reservations', 'homey-child'); ?>">
							<?php if (!empty($client_leads)){ ?>
							<?php $mainUrl = site_url().'/reservations/?reservation_detail='; ?>
                                                        <ul style="padding-left:0;">
							<?php foreach($client_leads as $leadId){ 
							  echo '<li style="list-style: none;float: left;padding: 5px;background: #EFBFCF;border-radius: 6px;margin-right:2px;">';
							  echo '<a href="'.$mainUrl.$leadId.'">'.$leadId.'</a></li>';
							} ?>
							</ul>
							<?php } else { ?>
							<span><?php echo esc_html__('None', 'homey-child'); ?></span>
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
							    <?php /*if($dashboarduser_role == 'homey_brokers'){ ?>
							    <button class="btn-action btn-delete-agent"  name="btn-delete-agent" data-toggle="tooltip" 
							   data-placement="top" data-original-title="<?php echo esc_html__('Delete Agent','homey-child'); ?>"
							   style="background:#f15e75;border-color:#f15e75;color:#FFF;padding:2px 5px;font-weight: bold;">
							    Delete</button>
							    <input type="hidden" name="agentid" value="<?php echo $user_id; ?>"/> 
							    <input type="hidden" name="brokerid" value="<?php echo $dashboard_user->ID; ?>"/>
							    <?php }*/ ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php        
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">';
                                            echo esc_html__('No Clients Created Yet.', 'homey');
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .block -->
			<?php } ?>

			<!-- -------------------------------------------------- 
			---- Registered Users For Brokers And Agents
			---------------------------------------------------- -->			
			<?php if($dashboarduser_role == 'homey_brokers') { ?>
			<?php $exclude = array(); 
			  $args = array('fields' => array('ID'), 'role__in' => array('homey_host', 'homey_renter') );
			  $args['meta_query'] = array( 
			       array( 'key' => 'created_by', 'value' => $dashboard_user_id, 'compare' => '=' ), 
			  );
			  $userQry = new WP_User_Query( $args );  //var_dump($userQry); 
			  if ( !empty( $userQry->get_results() ) ) {
                             foreach ( $userQry->get_results() as $user ) { array_push($exclude, $user->ID); }
			  } $args = []; } //end if brokers. 

			  //generate query for all registered users 
			  if(isset($exclude) && !empty($exclude)){ 
			    $args = array('role__in' => array('homey_host', 'homey_renter'), 'exclude' => $exclude );
			  } else{ $args = array('role__in' => array('homey_host', 'homey_renter') ); }
			  $users_registered = new WP_User_Query( $args );  //var_dump($userQry);
			?>
			<!-- .block -->
                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php esc_html_e('View: Registered Clients', 'homey'); ?></h2>
                                </div>
                                <div class="block-right">
                                    <div class="dashboard-form-inline">
                                        <form method="get" action="<?php echo esc_url($all_clients); ?>" class="form-inline">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="<?php esc_html_e('Search', 'homey'); ?>">
                                            </div>
                                            <input type="hidden" name="page" value="clients">
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

					    <?php if(homey_is_admin()){ ?>
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
                                        if ( ! empty( $users_registered->get_results() ) ) {
                                            foreach ( $users_registered->get_results() as $user ) {
                                                $user_id = $user->ID; 
                                                $user_data = homey_get_author_by_id('40', '40', 'img-circle', $user_id);
                                                $is_superhost = $user_data['is_superhost'];

                                                $doc_verified = $user_data['doc_verified'];
                                                $user_document_id = $user_data['user_document_id'];
                                                $doc_verified_request = $user_data['doc_verified_request'];

                                                //$user_role = homey_get_role_name($user_id);

						$user_role = $user->roles[0];

						//get all listings for the client
						if($dashboarduser_role == 'homey_brokers' || $dashboarduser_role == 'homey_agents'){ 
						  $client_listings = array(); 
						  $largs = array( 'post_type' => 'listing', 'posts_per_page' => -1, 
								  'post_status' => 'publish', 'author' => $user_id); 
						  $lqry_res = new WP_Query($largs); 
						  if ( $lqry_res->have_posts() ){
						  foreach($lqry_res->posts as $prop){ 
						    array_push($client_listings, $prop->ID);
						  }} 
					          wp_reset_postdata(); $lmeta_query=[];
						}

						//get all leads for the client
						if($dashboarduser_role == 'homey_brokers' || $dashboarduser_role == 'homey_agents'){ 
						  $client_leads = array(); 
						  $rargs = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
						  $rmeta_query[] = array( 'key' => 'listing_renter', 'value' => $user_id, 'compare' => '=' );
						  $rargs['meta_query'] = $rmeta_query;
						  $rqry_res = new WP_Query($rargs); //var_dump($rqry_res);
						  if ( $rqry_res->have_posts() ){
						  foreach($rqry_res->posts as $res1){ 
						    array_push($client_leads, $res1->ID);
						  }}
						  wp_reset_postdata(); $rmeta_query=[];
						}
                                                $single_user_link = add_query_arg( array( 'page' => 'users', 'user-id' => $user_id, ), $dashboard );

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
                                                        <?php if($user_role == 'homey_host'){ echo 'Host'; } else { echo 'Renter'; } ?>
                                                    </td>

						    <?php if($dashboarduser_role == 'homey_brokers' || $dashboarduser_role == 'homey_agents'){ ?>
                                                    <td data-label="<?php echo esc_html__('Listings', 'homey-child'); ?>">
							<?php if (!empty($client_listings)){ ?>
                                                        <ul style="padding-left:0;">
							<?php foreach($client_listings as $listingId){ 
							  echo '<li style="list-style:none;float:left;padding:5px;background:#EFBFCF;border-radius:6px;margin-right:2px;">';
							  echo '<a href="'.get_permalink($listingId).'">'.$listingId.'</a></li>';
							} ?>
							</ul>
							<?php } else { ?>
							<span><?php echo esc_html__('None', 'homey-child'); ?></span>
							<?php } ?>
                                                    </td>

                                                    <td data-label="<?php echo esc_html__('Reservations', 'homey-child'); ?>">
							<?php if (!empty($client_leads)){ ?>
							<?php $mainUrl = site_url().'/reservations/?reservation_detail='; ?>
                                                        <ul style="padding-left:0;">
							<?php foreach($client_leads as $leadId){ 
							  echo '<li style="list-style: none;float: left;padding: 5px;background: #EFBFCF;border-radius: 6px;margin-right:2px;">';
							  echo '<a href="'.$mainUrl.$leadId.'">'.$leadId.'</a></li>';
							} ?>
							</ul>
							<?php } else { ?>
							<span><?php echo esc_html__('None', 'homey-child'); ?></span>
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
							    <?php /*if($dashboarduser_role == 'homey_brokers'){ ?>
							    <button class="btn-action btn-delete-agent"  name="btn-delete-agent" data-toggle="tooltip" 
							   data-placement="top" data-original-title="<?php echo esc_html__('Delete Agent','homey-child'); ?>"
							   style="background:#f15e75;border-color:#f15e75;color:#FFF;padding:2px 5px;font-weight: bold;">
							    Delete</button>
							    <input type="hidden" name="agentid" value="<?php echo $user_id; ?>"/> 
							    <input type="hidden" name="brokerid" value="<?php echo $dashboard_user->ID; ?>"/>
							    <?php }*/ ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php        
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">';
                                            echo esc_html__('No Clients Created Yet.', 'homey');
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
			   <div class="pagination">
    			   <?php 
        			echo paginate_links( array(
            				'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            				'total' => $users_registered->max_num_pages,
            				'current' => max( 1, get_query_var( 'paged' ) ),
            				'format'  => '?paged=%#%',
            				'show_all' => false,
            				'type'  => 'plain',
            				'end_size' => 2,
            				'mid_size' => 1,
            				'prev_next' => true,
            				'prev_text' => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
            				'next_text' => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
            				'add_args'  => false,
            				'add_fragment' => '',
        			) );
    				?>
			  </div>
                        </div><!-- .block -->

			<!-- -------------------------------------------------- 
			---- Non-Registered Users For Brokers And Agents
			---------------------------------------------------- -->
			<!-- .block -->
                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php esc_html_e('View: Non-registered Users', 'homey'); ?></h2>
                                </div>
                                <div class="block-right">
                                    <div class="dashboard-form-inline">
                                        <form method="get" action="<?php echo esc_url($all_clients); ?>" class="form-inline">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="<?php esc_html_e('Search', 'homey'); ?>">
                                            </div>
                                            <input type="hidden" name="page" value="clients">
                                            <button type="submit" class="btn btn-primary btn-search-icon"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-block dashboard-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo esc_html__('Name', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Email', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Phone', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Role', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Lead Id', 'homey'); ?></th>
                                            <th><?php echo esc_html__('Actions', 'homey'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ( ! empty( $res_query->have_posts() ) ) {
                                           foreach ( $res_query->posts as $lead ) { 
					      $client_details = explode('%0A', urlencode($lead->post_excerpt)); 
					      foreach($client_details as $det){ 
						if(strstr($det,'Name')){ $data = urldecode($det); $clientname = explode(':',$data); } 
						if(strstr($det,'Email')){ $data = urldecode($det); $clientemail = explode(':',$data); } 
						if(strstr($det,'Phone')){ $data = urldecode($det); $clientph = explode(':',$data); } 							if(strstr($det,'Message')){ $data = urldecode($det); $clientmsg = explode(':',$data); } 					      }	
	   				      $clientrole = "Visitor"; 
					      //$single_client_link = add_query_arg( 
					      //			array( 'page' => 'clients', 'cid' => 'L'.$lead->ID,), $dashboard );
					      $single_client_link = '';
                                            ?>
                                                <tr>
                                                    <td data-label="<?php echo esc_html__('Name', 'homey'); ?>">
                                                        <?php echo $clientname[1]; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Email', 'homey'); ?>">
                                                        <?php echo $clientemail[1]; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Phone', 'homey'); ?>">
                                                        <?php echo $clientph[1]; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Role', 'homey'); ?>">
                                                        <?php echo $clientrole; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Lead Id', 'homey'); ?>">
							<?php $mainUrl = site_url().'/reservations/?reservation_detail=';
							  $leadId = $lead->ID; ?>
							<?php echo '<a href="'.$mainUrl.$leadId.'" style="float:left;padding:5px;background:#EFBFCF;border-radius:6px;margin-right:2px;">'.$leadId.'</a>'; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Actions', 'homey'); ?>">
                                                        <div class="custom-actions">
                                                            <a class="btn" href="javascript:void(0)"><?php esc_html_e('No Detail', 'homey'); ?></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php        
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">';
                                            echo esc_html__('No Clients Found For The User.', 'homey-child');
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
