<?php
/**
 * Template Name: Dashboard
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}

global $homey_local, $homey_prefix;
$dashboard_link = homey_get_template_link('template/dashboard.php');

$upgrade_featured = $featured_success = false;
$all_users = $user_detail = $add_agent = $add_client = false;
$all_clients = $all_agents = $all_routing = $add_lead = false;

$dashboard_user = wp_get_current_user(); 
$dashboarduser_role = $dashboard_user->roles[0]; 

if(isset($_GET['page']) && $_GET['page'] == 'upgrade_featured') {
    $upgrade_featured = true;

}elseif(isset($_GET['page']) && $_GET['page'] == 'featured_success') {
    $featured_success = true;

} elseif( (isset($_GET['page']) && $_GET['page'] == 'users') && (isset($_GET['user-id']) && $_GET['user-id'] != '') ) {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents') {
        wp_redirect(  home_url('/') );
    }
    $user_detail = true;

} elseif(isset($_GET['page']) && $_GET['page'] == 'users') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents') {
        wp_redirect(  home_url('/') );
    }
    $all_users = true;

} elseif(isset($_GET['page']) && $_GET['page'] == 'agents') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents') {
        wp_redirect(  home_url('/') );
    }
    $all_agents = true;

} elseif(isset($_GET['page']) && $_GET['page'] == 'add_agent') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers') {
        wp_redirect(  home_url('/') );
    }
    $add_agent = true;

} elseif(isset($_GET['page']) && $_GET['page'] == 'add_client') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers') {
        wp_redirect(  home_url('/') );
    }
    $add_client = true;

/*} elseif(isset($_GET['page']) && $_GET['page'] == 'add_lead') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers') {
        wp_redirect(  home_url('/') );
    }
    $add_lead = true;*/

} elseif(isset($_GET['page']) && $_GET['page'] == 'clients') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents') {
        wp_redirect(  home_url('/') );
    }
    $all_clients = true;

} elseif(isset($_GET['page']) && $_GET['page'] == 'routing') {
    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers') {
        wp_redirect(  home_url('/') );
    }
    $all_routing = true;
} 
//elseif( (isset($_GET['page']) && $_GET['page'] == 'clients') && (isset($_GET['cid']) && $_GET['cid'] != '') ) {
//    if(!homey_is_admin() && $dashboarduser_role != 'homey_brokers' && $dashboarduser_role != 'homey_agents') {
//        wp_redirect(  home_url('/') );
//    }
//    $client_detail = true;
//}

get_header();
?>

<section id="body-area">

    <div class="dashboard-page-title">
        <h1>
        <?php 
        if($upgrade_featured) {
            echo esc_html__('Upgrade to featured', 'homey'); 

        } elseif($featured_success) {
            echo esc_html__('Payment Received', 'homey');

        } elseif($all_users) {
	    echo esc_html__('Users', 'homey');

        } elseif($all_agents) {
	    if($dashboarduser_role == 'homey_brokers' || $dashboarduser_role == 'homey_agents'){
		echo esc_html__('Agents', 'homey');
	    } 

	} elseif($add_agent) {
	    echo esc_html__('Add New Agent', 'homey-child');

        } elseif($user_detail) {
            echo esc_html__('User Profile', 'homey');

        } elseif($all_clients) {
            echo esc_html__('Clients', 'homey');

        //} elseif($client_detail) {
        //    echo esc_html__('Clients Details', 'homey');

	} elseif($add_client) {
	    echo esc_html__('Add New Client', 'homey-child');

	//} elseif($add_lead) {
	//    echo esc_html__('Add New Lead', 'homey-child');

	} elseif($all_routing) {
	    echo esc_html__('View Routings', 'homey-child');

        } else {
            the_title();
        } 
        ?>
        </h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <?php
    if($upgrade_featured) {
        get_template_part('template-parts/dashboard/upgrade-featured');

    } elseif($featured_success) {
        get_template_part('template-parts/dashboard/featured-success');

    } elseif($all_users) {
        get_template_part('template-parts/dashboard/users/users');

    } elseif($user_detail) {
        get_template_part('template-parts/dashboard/users/user-detail');

    } elseif($all_clients) {
        get_template_part('template-parts/dashboard/users/clients');

    } elseif($add_client) {
        get_template_part('template-parts/dashboard/profile/new-client-information');
        get_template_part('template-parts/dashboard/profile/new-client-address'); 

    //} elseif($client_detail) {
    //    get_template_part('template-parts/dashboard/users/client-detail');

    } elseif($all_agents) {
        get_template_part('template-parts/dashboard/users/agents');

    } elseif($add_agent) {
        get_template_part('template-parts/dashboard/profile/new-agent-information');
        get_template_part('template-parts/dashboard/profile/new-agent-address'); 

    //} elseif($add_lead) {
    //    get_template_part('template-parts/dashboard/profile/new-lead-information');
    //    get_template_part('template-parts/dashboard/profile/new-lead-properties'); 

    } elseif($all_routing) {
        get_template_part('template-parts/dashboard/routing/routing');

    } else {
        get_template_part('template-parts/dashboard/dashboard'); 
    }
    ?>

</section><!-- #body-area -->


<?php get_footer();?>
