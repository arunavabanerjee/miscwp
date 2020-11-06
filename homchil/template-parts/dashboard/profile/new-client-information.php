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

<div class="homey-new-client-add-wrapper">
<form id="homey-add-new-agent" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="broker_email" value="<?php echo $user_email; ?>">
<input type="hidden" name="homey_addnewclient_security" value="<?php echo wp_create_nonce('homey-new-client-create'); ?>"/>
<input type="hidden" name="redirect_to" value="<?php echo $dashboard_link.'?page=clients'?>" />
<div class="block">
    <div class="block-title">
	<div class="homey_add_new_client_messages_top"></div>
        <div class="block-left">
            <h2 class="title"><?php echo 'Enter Client '.esc_attr($homey_local['information']); ?></h2>
        </div>
    </div>
    <div class="block-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="username"><?php echo esc_attr($homey_local['fusername_label']); ?>(As System Loginname)</label>
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

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="phone"><?php echo esc_html__('Phone/Mobile'); ?></label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="<?php echo esc_html__('Enter Phone'); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="age"><?php echo esc_html__('Age'); ?></label>
		    <select class="form-control select" id="age" name="age" style="padding:0 5px;">
			<option value="">Select Age</option><option value="25-30">25-30</option>
			<option value="30-35">30-35</option><option value="35-40">35-40</option>
			<option value="40-45">40-45</option><option value="45-50">45-50</option>
			<option value="50-55">50-55</option><option value="55-60">55-60</option>
			<option value="60-65">60-65</option><option value="65-70">65-70</option>
			<option value="70-75">70-75</option><option value="75-80">75-80</option>
	   	    </select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="occupation"><?php echo esc_html__('Occupation'); ?></label>
                    <input type="text" id="occupation" name="occupation" class="form-control" placeholder="<?php echo esc_html__('Enter Occupation'); ?>">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="age"><?php echo esc_html__('Gender'); ?></label>
		    <select class="form-control select" id="gender" name="gender" style="padding:0 5px;">
			<option value="">Select Gender</option>
			<option value="M">Male</option><option value="F">Female</option>
	   	    </select>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="acctn-type"><?php echo esc_html__('Account Type'); ?></label>
		    <select class="form-control select" id="acctn-type" name="acctn-type" style="padding:0 5px;">
			<option value="">Select Acctn</option>
			<option value="renter">Renter</option><option value="host">Host</option>
	   	    </select>
                </div>
            </div>

        </div>
    </div>
</div><!-- block -->
