<?php
$dashboard = homey_get_template_link_2('template/dashboard.php');
$all_routings = add_query_arg( 'page', 'routing', $dashboard );

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

/* generate query for list of postcodes as per the property listings*/ 
$pcode_args = array(
   'post_type'     => 'listing',
   'posts_per_page' => -1,
   'post_status'  =>  'publish', 
   'fields'	=> 'ids'	
);
$pcode_args = homey_listing_sort ($pcode_args);
$pcode_propsIds = new WP_Query($pcode_args);
$meta_query = []; wp_reset_postdata();

/*generate query to find the list of agents in the system */
$user_args = array(
 'role' => 'homey_agents',
 'orderby' => 'ID',
 'order' => 'ASC'
);
$agents = get_users($user_args);

/** query to find the list of rules set by brokers */
$listing_no   =  '12';
$paged        = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'      => 'homey_routing',
    'paged'          => $paged,
    'posts_per_page' => $listing_no,
    //'post_author'    => $dashboard_user_id,
);
$rules_query = new WP_Query($args); //var_dump($rules_query); 

?>
<div class="user-dashboard-right dashboard-without-sidebar">
    <div class="dashboard-content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-area">
                        <div class="block">
                            <div class="block-title" style="padding:10px;">
                                <div class="block-body" style="padding:10px;">
                                    <h2 class="title" style=""><?php esc_html_e('Generate Routings', 'homey-child'); ?></h2>
                                </div>
                                <div class="block-body" style="padding:10px;background:#CFCFCF;">
                                    <div class="dashboard-form-inline" style="top:7px;">
                                        <form class="form-inline" method="POST">
					   <div class="form-group">
					    <select class="form-control select" id="routing-search-states" name="routing-search-states"
						    style="min-width:120px;height:35px;padding:0;">
	     				     <option value="*">All States</option><?php echo homey_get_all_states(); ?>
	   				    </select>
					   </div>
					   <div class="form-group">
					    <select class="form-control select" id="routing-search-city" name="routing-search-city"
						    style="min-width:120px;height:35px;padding:0;">
	     				     <option value="*">All Cities</option><?php echo homey_get_all_cities(); ?>
	   				    </select>
					   </div>
					   <div class="form-group">
					   <?php $pcodes = array(); $html = '';
					    if ($pcode_propsIds->have_posts()) {
                                               foreach ( $pcode_propsIds->posts as $lpostId ) { 
						  $pcode = get_post_meta($lpostId, 'homey_zip', true);
					          if(! in_array($pcode, $pcodes)){ 
						    $html .= '<option value="'.$pcode.'">'.$pcode.'</option>';
						    array_push($pcodes, $pcode); 
						  } 
					       }
					    } // endif 
					    ?>
					    <select class="form-control select" id="routing-search-pcodes" name="routing-search-pcodes"
						    style="min-width:130px;height:35px;padding:0;">
	     				     <option value="*">Select PostCodes</option><?php echo $html; ?>
	   				    </select>
					    </div>
					    <div class="form-group">
					     <?php $html = '';
					       if (! empty( $agents )){
                                                 foreach ( $agents as $agent ) { 
						    $html .= '<option value="'.$agent->ID.'">'.$agent->display_name.'</option>';
					         }
					       } //endif 
					      ?>
					      <select class="form-control select" id="routing-search-agents" name="routing-search-agents"
						      style="min-width:120px;height:35px;padding:0;">
	     				       <option value="*">All Agents</option><?php echo $html; ?>
	   				      </select>
					    </div>
					   <div class="form-group">
					   <?php $npriceranges = array(); $html = '';
					    if ($pcode_propsIds->have_posts()) {
                                               foreach ( $pcode_propsIds->posts as $lpostId ) { 
						  $nightprice = get_post_meta($lpostId, 'homey_night_price', true);
						  if($nightprice <= 100){ 
						     array_push($npriceranges, '<option value="0-100">Price: 0-100</option>'); }
						  if($nightprice > 100 && $nightprice <= 200){ 
						     array_push($npriceranges, '<option value="100-200">Price: 100-200</option>'); }
						  if($nightprice > 200 && $nightprice <= 300){ 
						     array_push($npriceranges, '<option value="200-300">Price: 200-300</option>'); }
						  if($nightprice > 300 && $nightprice <= 400){ 
						     array_push($npriceranges, '<option value="300-400">Price: 300-400</option>'); }
						  if($nightprice > 400 && $nightprice <= 500){ 
						     array_push($npriceranges, '<option value="400-500">Price: 400-500</option>'); }
						  if($nightprice > 500 && $nightprice <= 600){ 
						     array_push($npriceranges, '<option value="500-600">Price: 500-600</option>'); }
						  if($nightprice > 600 && $nightprice <= 700){ 
						     array_push($npriceranges, '<option value="600-700">Price: 600-700</option>'); }
						  if($nightprice > 700 && $nightprice <= 800){ 
						     array_push($npriceranges, '<option value="700-800">Price: 700-800</option>'); }
						  if($nightprice > 800 && $nightprice <= 900){ 
						     array_push($npriceranges, '<option value="800-900">Price: 800-900</option>'); }
						  if($nightprice > 900 && $nightprice <= 1000){ 
						     array_push($npriceranges, '<option value="900-1000">Price: 900-1000</option>'); }
						  if($nightprice > 1000 && $nightprice <= 1100){ 
						     array_push($npriceranges, '<option value="1000-1100">Price: 1000-1100</option>'); }
					       }
					    } // endif 
					    $pranges = array_unique($npriceranges); sort($pranges);
					    ?>
					    <select class="form-control select" id="routing-search-prices" name="routing-search-prices"
						    style="min-width:120px;height:35px;padding:0;">
	     				     <option value="*">All Prices</option>
					     <?php foreach($pranges as $prange){ echo $prange; } ?>
	   				    </select>
					    </div>
 	                                    <div class="form-group">
                                              <input name="keywords" type="text" class="form-control" 
						     style="min-width:269px;height:35px;padding:0 12px;margin:-21px 0 0 0"
						     placeholder="<?php esc_html_e('Keywords (comma separated)', 'homey'); ?>">
                                            </div>
					    <div class="form-group">
                                             <button type="submit" id="routing-rules-generate" name="routing-rules-generate" 
						     class="btn btn-primary" style="line-height:31px;padding:0 14px;right:auto;">
						<?php esc_html_e('Generate', 'homey-child'); ?>
					     </button>
					    </div>
                                        </form>
					<div class="routing-messages"></div>
                                    </div><!-- end div dashboard-form-inline -->
                                </div>
			    </div>
			</div>

                        <div class="block">
                            <div class="block-title">
                                <div class="block-left">
                                    <h2 class="title"><?php esc_html_e('View Rules', 'homey'); ?></h2>
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
                                            <th><?php echo esc_html__('Title', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('State', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('City', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('PCode', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('Price Range', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('Keywords', 'homey-child'); ?></th>
                                            <th><?php echo esc_html__('Agent', 'homey-child'); ?></th>
					    <th><?php echo esc_html__('Action', 'homey-child'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ( ! empty( $rules_query->have_posts() ) ) {
                                           foreach ( $rules_query->posts as $rule ) { 
						$rule_name = $rule->post_title; 
						$rule_author_id = $rule->post_author;
						$rule_state = get_post_meta($rule->ID, 'homey_routing_state', true);
						$rule_city = get_post_meta($rule->ID, 'homey_routing_city', true);
						$rule_pcode = get_post_meta($rule->ID, 'homey_routing_pcode', true);
						$rule_agentId = get_post_meta($rule->ID, 'homey_routing_agent', true);	
						$rule_prange = get_post_meta($rule->ID, 'homey_routing_prange', true); 
						$rule_keywords = get_post_meta($rule->ID, 'homey_routing_keywords', true);
						$rule_status = get_post_meta($rule->ID, 'homey_routing_status', true);

						$assignedAgent = get_userdata($rule_agentId);
						$single_client_link = '';
						//$single_client_link = add_query_arg( array( 'page' => 'clients', 
						//					    'cid' => $user_id, ), $dashboard );

                                            ?>
                                                <tr>
                                                    <td data-label="<?php echo esc_html__('Title', 'homey'); ?>">
                                                        <?php echo $rule_name; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('State', 'homey'); ?>">
                                                        <?php echo $rule_state; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('City', 'homey'); ?>">
                                                        <?php echo $rule_city; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('PCode', 'homey'); ?>">
                                                        <?php echo $rule_pcode; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Price Range', 'homey'); ?>">
                                                        <?php echo $rule_prange; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Keywords', 'homey'); ?>">
                                                        <?php echo $rule_keywords; ?>
                                                    </td>
                                                    <td data-label="<?php echo esc_html__('Agent', 'homey'); ?>">
                                                        <?php if($assignedAgent){ echo $assignedAgent->display_name; } ?>
                                                    </td>

						    <?php if($single_client_link != '') { ?>
                                                    <td data-label="<?php echo esc_html__('Actions', 'homey'); ?>">
                                                        <div class="custom-actions">
                                                            <a class="btn btn-success" href="<?php echo esc_url($single_client_link); ?>">
								<?php esc_html_e('Detail', 'homey'); ?></a>
                                                        </div>
                                                    </td>
						    <?php } else{ ?>
                                                    <td data-label="<?php echo esc_html__('Actions', 'homey'); ?>">
                                                      <div class="custom-actions">
							<?php if($rule_author_id == $dashboard_user_id){ ?>
							<?php if($rule_status == 'disabled'){ ?>
                                                          <a class="btn" name="btn_enable_rule" style="background:#96F489;border-radius:5px;border: 1px solid #ddd;" href="<?php echo esc_url('#'); ?>"><?php esc_html_e('Enable', 'homey-child'); ?></a>
							<?php } else { ?>
							  <a class="btn" name="btn_disable_rule" style="background:#ef8495;border-radius:5px;border: 1px solid #ddd;" href="<?php echo esc_url('#'); ?>"><?php esc_html_e('Disable', 'homey-child'); ?></a>
							<?php } ?>
							<input type="hidden" name="ruleid" value="<?php echo $rule->ID; ?>" /> 
							<?php } else { ?>
							  <a class="btn" name="btn_no_action_on_rule" style="background:#CCC;border-radius:5px;border: 1px solid #ddd;" href="javascript:void(0)"><?php esc_html_e('None', 'homey-child'); ?></a>
							<?php } ?>
                                                      </div>
                                                    </td>
						    <?php } ?>
                                                </tr>
                                        <?php        
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">';
                                            echo esc_html__('No Rules Found For Broker.', 'homey-child');
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
