<?php

/**
 * form for new agent details 
 */
global $current_user, $userID, $user_email, $homey_local;
global $dashboard_user; //var_dump($dashboard_user); exit;
global $dashboard_link;

$user_email = $dashboard_user->user_email; 
?>

<div class="user-dashboard-right dashboard-without-sidebar">
<div class="dashboard-content-area">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
<div class="dashboard-area">
<!--for dashboard -->

<div class="homey-new-agent-add-wrapper">
<form id="homey-add-new-agent" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="broker_email" value="<?php echo antispambot($user_email); ?>">
<input type="hidden" name="homey_addnewagent_security" value="<?php echo wp_create_nonce('homey-new-agent-create'); ?>"/>
<input type="hidden" name="redirect_to" value="<?php echo $dashboard_link.'?page=agents'?>" />
<div class="block">
    <div class="block-title">
	<div class="homey_add_new_agent_messages_top"></div>
        <div class="block-left">
            <h2 class="title"><?php echo 'Enter Agent '.esc_attr($homey_local['information']); ?></h2>
        </div>
    </div>
    <div class="block-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="username"><?php echo esc_attr($homey_local['fusername_label']); ?>(As System Username)</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="<?php echo esc_attr($homey_local['fusername_plac']); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="email"><?php echo esc_attr($homey_local['email_label']); ?></label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="<?php echo esc_attr($homey_local['email_plac']); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="firstname"><?php echo esc_attr($homey_local['fname_label']); ?></label>
                    <input type="text" id="firstname" name="firstname" class="form-control" placeholder="<?php echo esc_attr($homey_local['fname_plac']); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="lastname"><?php echo esc_attr($homey_local['lname_label']); ?></label>
                    <input type="text" id="lastname" name="lastname" class="form-control" placeholder="<?php echo esc_attr($homey_local['lname_plac']); ?>">
                </div>
            </div>
        </div>
    </div>
</div><!-- block -->
