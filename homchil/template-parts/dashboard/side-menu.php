<?php
global $current_user, $post, $homey_local;
$current_user = wp_get_current_user(); 
$currentuser_role = $current_user->roles[0]; 

$enable_wallet = homey_option('enable_wallet');
$reservation_payment = homey_option('reservation_payment');
$offsite_payment = homey_option('off-site-payment');

$wallet_page_link = homey_get_template_link('template/dashboard-wallet.php');
$earnings_page_link = add_query_arg( 'page', 'earnings', $wallet_page_link );
$payout_request_link = add_query_arg( 'page', 'payout-request', $wallet_page_link );
$payouts_page_link = add_query_arg( 'page', 'payouts', $wallet_page_link );
//$payouts_setup_page = add_query_arg( 'page', 'payment-method', $wallet_page_link );
$security_deposits_page = add_query_arg( 'page', 'security-deposits', $wallet_page_link );

$dashboard = homey_get_template_link_dash('template/dashboard.php');
$dashboard_profile = homey_get_template_link_dash('template/dashboard-profile.php');
$payment_method_setup = add_query_arg( 'page', 'payment-method', $dashboard_profile );

$dashboard_listings = homey_get_template_link_dash('template/dashboard-listings.php');
$dashboard_add_listing = homey_get_template_link_dash('template/dashboard-submission.php');
$dashboard_favorites = homey_get_template_link_dash('template/dashboard-favorites.php');
$dashboard_search = homey_get_template_link_dash('template/dashboard-saved-searches.php');
$dashboard_reservations = homey_get_template_link_dash('template/dashboard-reservations.php');
$dashboard_host_reservations = homey_get_template_link_dash('template/dashboard-reservations2.php');
$dashboard_messages = homey_get_template_link_dash('template/dashboard-messages.php');
$dashboard_invoices = homey_get_template_link_dash('template/dashboard-invoices.php');
$dashboard_wallet = homey_get_template_link_dash('template/dashboard-wallet.php');
$home_link = home_url('/');

$all_users = add_query_arg( 'page', 'users', $dashboard );
$verification_page = add_query_arg( 'page', 'verification', $dashboard_profile );
$password_page = add_query_arg( 'page', 'password-reset', $dashboard_profile );

$all_agents = add_query_arg( 'page', 'agents', $dashboard );
$addnewagent_page = add_query_arg( 'page', 'add_agent', $dashboard );

$all_clients = add_query_arg( 'page', 'clients', $dashboard );
$addnewclient_page = add_query_arg( 'page', 'add_client', $dashboard );

$all_routings = add_query_arg( 'page', 'routing', $dashboard );

$addnewlead_page = add_query_arg( 'page', 'add_lead', $dashboard );

$ac_wallet = $ac_dash = $ac_profile = $ac_fav = $ac_listings = $ac_invoices = $ac_msgs = '';
$ac_submission = $ac_reserv = $ac_reserv_host = $ac_checkout = ''; 
$ac_users = $ac_agents = $ac_addusers = $ac_history = $ac_clients = $ac_addclients = '';
$ac_routing = '';

if( is_page_template( 'template/dashboard.php' ) && !isset($_GET['page'])) {
    $ac_dash = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-profile.php' ) ) {
    $ac_profile = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-listings.php' ) ) {
    $ac_listings = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-submission.php' ) ) {
    $ac_submission = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-favorites.php' ) ) {
    $ac_fav = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-invoices.php' ) ) {
    $ac_invoices = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-messages.php' ) ) {
    $ac_msgs = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'type=history' ) ){
    $ac_history = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'type=checkout' ) ){
    //$ac_checkout = 'board-panel-item-active';
    $ac_reserv = 'board-panel-item-active'; 
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=add_lead' ) ){
    //$ac_addlead = 'board-panel-item-active'; 
    $ac_reserv = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-reservations.php' ) ) {
    $ac_reserv = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-reservations2.php' ) ) {
    $ac_reserv_host = 'board-panel-item-active';
} elseif ( is_page_template( 'template/dashboard-wallet.php' ) ) {
    $ac_wallet = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=users' ) ){
    $ac_users = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=agents' ) ){
    $ac_agents = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=add_agent' ) ){
    //$ac_addusers = 'board-panel-item-active'; 
    $ac_agents = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=clients' ) ){
    $ac_clients = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=add_client' ) ){
    //$ac_addclients = 'board-panel-item-active';
    $ac_clients = 'board-panel-item-active';
} elseif( strstr($_SERVER["REQUEST_URI"], 'page=routing' ) ){
    $ac_routing = 'board-panel-item-active';
} 

?>
<div class="user-dashboard-left white-bg"> 
    <div class="navi" style="overflow-y: scroll; height: 600px;">
        <ul class="board-panel-menu">
            <?php 
            if( !empty($dashboard) ) {
                echo '<li class="'.esc_attr($ac_dash).'">
                        <a href="'.esc_url($dashboard).'">
                            '.$homey_local['m_dashboard_label'].'
                        </a>
                    </li>';
            }

            if( !empty($dashboard_profile) ) {
                echo '<li class="has-child '.esc_attr($ac_profile).'">
                    <a href="'.esc_url($dashboard_profile).'">
                        '.$homey_local['m_profile_label'].' <i class="fa fa-angle-down"></i>
                    </a>';

                    echo '<ul>';
                        if(!homey_is_admin()) {
                            echo '<li><a href="'.esc_url($verification_page).'">'.esc_html__('Verification', 'homey').'</a></li>';
                        }
                        echo '<li><a href="'.esc_url($password_page).'">'.esc_html__('Password', 'homey').'</a></li>';
                        /*if($offsite_payment != 0 || $enable_wallet != 0) {
                            echo '<li><a href="'.esc_url($payment_method_setup).'">'.esc_html__('Payment Method', 'homey').'</a></li>';
                        }*/
                    echo '</ul>';
                    
                echo '</li>';
                
            }

            if(homey_is_admin()) {
                if( !empty($all_users) ) {
                    echo '<li class="">
                        <a href="'.esc_url($all_users).'">'.esc_html__('Users', 'homey').'</a>
                    </li>';
                }
            } elseif($currentuser_role == 'homey_brokers'){
                if( !empty($all_agents) ) {
		    if( strstr($_SERVER["REQUEST_URI"], 'page=add_agent' )){ $extraclass = 'active'; } 
		    else { $extraclass = ''; }
                    echo '<li class="has-child '.esc_attr($ac_agents).' '.esc_attr($extraclass).'">
                           <a class="agent-link" href="'.esc_url($all_agents).'">'
				.esc_html__('View All Agents', 'homey').' <i class="fa fa-angle-down"></i>'.
			   '</a>';
 		    /*if(strstr($_SERVER['REQUEST_URI'], '?page=users')){ $style = 'style="display:block"'; }
		    else{ $style = 'style="display:none"'; }*/
                    //echo '<ul '.$style.'>';
                    echo '<ul>';
                    echo '<li><a href="'.esc_url($addnewagent_page).'">'.esc_html__('Add New Agent', 'homey').'</a></li>';
                    echo '</ul>';
                    echo '</li>';
                    //echo '<li class="'.esc_attr($ac_addusers).'">
		    //	   <a href="'.esc_url($addnewagent_page).'">'.esc_html__('Add New Agent', 'homey-child').'</a></li>';
                }
	    }

            if( !empty($dashboard_reservations) ) {
                if(homey_is_renter()) {
                    echo '<li class="'.esc_attr($ac_reserv).'">
                        <a href="'.esc_url($dashboard_reservations).'">'.$homey_local['m_reservation_label'].'</a>
                    </li>';

                } elseif(homey_is_admin()) {
                    echo '<li class="'.esc_attr($ac_reserv).'">
                        <a href="'.esc_url($dashboard_reservations).'">'.esc_html__('Bookings', 'homey').'</a>
                    </li>';

                } elseif( ($currentuser_role == 'homey_brokers') || ($currentuser_role == 'homey_agents')) {

		   if($currentuser_role == 'homey_brokers'){
		      if( strstr($_SERVER["REQUEST_URI"], 'type=checkout')){ $extraclass = 'active'; } 
		      else { $extraclass = ''; }
		      if( strstr($_SERVER["REQUEST_URI"], 'page=add_lead')){ $extraclass = 'active'; } 
		      else { $extraclass = ''; }
		      if( strstr($_SERVER["REQUEST_URI"], 'type=pending')){ $extraclass = 'active'; } 
		      else { $extraclass = ''; }

                      echo '<li class="has-child '.esc_attr($ac_reserv).' '.esc_attr($extraclass).'">
                          <a href="'.esc_url($dashboard_reservations).'">'
			    .esc_html__('Active Leads', 'homey').' <i class="fa fa-angle-down"></i></a>'; 

		      echo '<ul>';
                      echo '<li><a href="'.esc_url($dashboard_reservations.'?type=pending').'" style="color:#FF0000;">'.esc_html__('Pending Leads', 'homey').'</a></li>'; 
                      echo '<li><a href="'.esc_url($dashboard_reservations.'?type=checkout').'">'.esc_html__('Lead Checkout', 'homey').'</a></li>'; 
                      echo '<li><a href="'.esc_url($dashboard_reservations.'?page=add_lead').'">'.esc_html__('Add New Lead', 'homey').'</a></li>'; 
		      echo '</ul>';

		      echo '</li>';

		   } else { // for agents.
                     echo '<li class="'.esc_attr($ac_reserv).'">
                            <a href="'.esc_url($dashboard_reservations).'">'.esc_html__('My Active Leads', 'homey').'</a>
			  </li>';
		   }

                   echo '<li class="'.esc_attr($ac_history).'">
                        <a href="'.esc_url($dashboard_reservations.'?type=history').'">'.esc_html__('Lead History', 'homey').'</a>
                    </li>'; 

		} else {
                    echo '<li class="'.esc_attr($ac_reserv).'">
                        <a href="'.esc_url($dashboard_reservations).'">'.esc_html__('My Bookings', 'homey').'</a>
                    </li>';
                }
            }

            if(!homey_is_renter()) {
		if($currentuser_role == 'homey_brokers' || $currentuser_role == 'homey_agents'){
                  if( !empty($dashboard_listings) ) {
                    echo '<li class="'.esc_attr($ac_listings).'">
                        <a href="'.esc_url($dashboard_listings).'"> Current Listings </a>
                    </li>';
                  }

		  if($currentuser_role == 'homey_brokers'){
                  if( !empty($dashboard_add_listing) ) {
                    echo '<li class="'.esc_attr($ac_submission).'">
                        <a href="'.esc_url($dashboard_add_listing).'">'.$homey_local['m_add_listing_label'].'</a>
                    </li>';
                  }}

		} else {
                  if( !empty($dashboard_listings) ) {
                    echo '<li class="'.esc_attr($ac_listings).'">
                        <a href="'.esc_url($dashboard_listings).'">'.$homey_local['m_listings_label'].'</a>
                    </li>';
                  }

                  if( !empty($dashboard_add_listing) ) {
                    echo '<li class="'.esc_attr($ac_submission).'">
                        <a href="'.esc_url($dashboard_add_listing).'">'.$homey_local['m_add_listing_label'].'</a>
                    </li>';
                  }

		}
            }

            if( !empty($dashboard_host_reservations) && !homey_is_renter() && $currentuser_role != 'homey_brokers' && $currentuser_role != 'homey_agents') {
                echo '<li class="'.$ac_reserv_host.'">
                    <a href="'.esc_url($dashboard_host_reservations).'">'.esc_html__('My Reservations', 'homey').'</a>
                </li>';
            }

            /*if($enable_wallet != 0) {
                if($reservation_payment == 'percent' || $reservation_payment == 'full') {
                    if(homey_is_host()) {
                        if( !empty($dashboard_wallet) ) {
                            echo '<li class="'.esc_attr($ac_wallet).' has-child">
                                <a href="'.esc_url($dashboard_wallet).'">'.esc_html__('Wallet', 'homey').' <i class="fa fa-angle-down"></i></a>
                                <ul>
                                    <li><a href="'.esc_url($earnings_page_link).'">'.esc_html__('Earnings', 'homey').'</a></li>
                                    <li><a href="'.esc_url($payouts_page_link).'">'.esc_html__('Payouts', 'homey').'</a></li>
                                </ul>
                            </li>';
                        }
                    }

                    if(homey_is_renter()) {
                        if( !empty($dashboard_wallet) ) {
                            echo '<li class="'.esc_attr($ac_wallet).' has-child">
                                <a href="'.esc_url($dashboard_wallet).'">'.esc_html__('Wallet', 'homey').' <i class="fa fa-angle-down"></i></a>
                                <ul>
                                    <li><a href="'.esc_url($security_deposits_page).'">'.esc_html__('Security Deposit', 'homey').'</a></li>
                                    <li><a href="'.esc_url($payouts_page_link).'">'.esc_html__('Payouts', 'homey').'</a></li>
                                </ul>
                            </li>';
                        }
                    }

                    if(homey_is_admin()) {
                        if( !empty($dashboard_wallet) ) {
                            echo '<li class="'.esc_attr($ac_wallet).'">
                                <a href="'.esc_url($payouts_page_link).'">'.esc_html__('Payouts', 'homey').'</a>
                            </li>';
                        }
                    }
                }
            }*/

            if(!homey_is_renter()) {
		if($currentuser_role == 'homey_brokers' || $currentuser_role == 'homey_agents'){

		  if($currentuser_role == 'homey_brokers'){
		   if( strstr($_SERVER["REQUEST_URI"], 'page=add_client')){ $extraclass = 'active'; } 
		   else { $extraclass = ''; }

                   if( !empty($all_clients) ) {		     
                     echo '<li class="has-child '.esc_attr($ac_clients).' '.esc_attr($extraclass).'">
                            <a class="agent-link" href="'.esc_url($all_clients).'">'
				.esc_html__('View Clients', 'homey').' <i class="fa fa-angle-down"></i></a>'; ;

                     if( !empty($addnewclient_page) ) {
		       echo '<ul>';
                       echo '<li class="">
                              <a class="agent-link" href="'.esc_url($addnewclient_page).'">'.esc_html__('Add New Client', 'homey').'</a>';
                       echo '</li>';
		       echo '</ul>';
		     }

                     echo '</li>';

                   }

		  } else { // for agents

                   if( !empty($all_clients) ) {
                     echo '<li class="'.esc_attr($ac_clients).'">
                        <a class="agent-link" href="'.esc_url($all_clients).'">'.esc_html__('View Clients', 'homey').'</a>';
                     echo '</li>';
                   }

		  }

		}

		if($currentuser_role == 'homey_brokers'){
                  if( !empty($all_routings) ) {
                    echo '<li class="'.esc_attr($ac_routing).'">
                        <a class="agent-link" href="'.esc_url($all_routings).'">'.esc_html__('View Routings', 'homey').'</a>';
                    echo '</li>';
                  }
		}
	    }


            if( !empty($dashboard_messages) ) {
                echo '<li class="'.esc_attr($ac_msgs).'">
                    <a href="'.esc_url($dashboard_messages).'">'.$homey_local['m_messages_label'].'
                    '.homey_messages_notification().'
                    </a>
                </li>';
            }

            if( !empty($dashboard_invoices) ) {
                echo '<li class="'.esc_attr($ac_invoices).'">
                    <a href="'.esc_url($dashboard_invoices).'">'.$homey_local['m_invoices_label'].'</a>
                </li>';
            }


            if( !empty($dashboard_favorites) ) {
                echo '<li class="'.esc_attr($ac_fav).'">
                    <a href="'.esc_url($dashboard_favorites).'">'.$homey_local['m_favorites_label'].'</a>
                </li>';
            }


            echo '<li><a href="' . wp_logout_url(home_url('/')) . '">'.$homey_local['m_logout_label'].'</a></li>';

            ?>
        </ul>
    </div>
</div>

