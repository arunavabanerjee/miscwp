<?php
global $post,
       $edit_link,
       $homey_local,
       $homey_prefix,
       $prop_address,
       $prop_featured,
       $payment_status;

$post_id    = get_the_ID();
$listing_images = get_post_meta( get_the_ID(), $homey_prefix.'listing_images', false );
$address        = get_post_meta( get_the_ID(), $homey_prefix.'listing_address', true );
$bedrooms       = get_post_meta( get_the_ID(), $homey_prefix.'listing_bedrooms', true );
$guests         = get_post_meta( get_the_ID(), $homey_prefix.'guests', true );
$beds           = get_post_meta( get_the_ID(), $homey_prefix.'beds', true );
$baths          = get_post_meta( get_the_ID(), $homey_prefix.'baths', true );
$night_price    = get_post_meta( get_the_ID(), $homey_prefix.'night_price', true );
$featured    = get_post_meta( get_the_ID(), $homey_prefix.'featured', true );

$agent_assigned_ids = get_post_meta(get_the_ID(), 'homey_assignedagent', false); 
$property_type = get_post_meta(get_the_ID(), 'homey_typeofpropertylisting', true);

$listing_price = homey_get_price_by_id($post_id);

$dashboard_user = wp_get_current_user(); 
$dashboarduser_role = $dashboard_user->roles[0]; 

$dashboard_listings = homey_get_template_link('template/dashboard-listing.php');
$edit_link  = add_query_arg( 'edit_listing', $post_id, $edit_link ) ;
$delete_link  = add_query_arg( 'listing_id', $post_id, $dashboard_listings ) ;
$property_status = get_post_status ( $post->ID ); 
$current_property_status = get_post_status ( $post->ID );
$check_listing_status = $property_status;
$dashboard = homey_get_template_link('template/dashboard.php');
$price_separator = homey_option('currency_separator');
$make_featured = homey_option('make_featured');

if($property_status == 'publish') {
    $property_status = esc_html__('Published', 'homey');
    $status_class = "label-success";
} elseif($property_status == 'pending') {
    $status_class = "label-warning";
    $property_status = esc_html__('Waiting for Approval', 'homey');
} elseif($property_status == 'draft') {
    $status_class = 'label-default';
} elseif($property_status == 'disabled') {
    $status_class = 'label-danger';
} else {
    $status_class = "label-success";
}

if($check_listing_status == 'publish') {
    $disable_list_text = esc_html__('Disable Listing', 'homey');
    $icon = 'fa-pause';
    $list_current_status = 'enabled';
} elseif($check_listing_status == 'disabled') {
    $disable_list_text = esc_html__('Enable Listing', 'homey');
    $list_current_status = 'disabled';
    $icon = 'fa-play';
}

$upgrade_link  = add_query_arg( array(
    'page' => 'upgrade_featured',
    'upgrade_id' => $post_id,
 ), $dashboard );
?>

<tr>
    <td data-label="<?php echo esc_attr($homey_local['thumb_label']); ?>">
        <a href="<?php the_permalink(); ?>">
        <?php
        if( has_post_thumbnail( $post->ID ) ) {
            the_post_thumbnail( 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        }else{
            homey_image_placeholder( 'homey-listing-thumb' );
        }
        ?>
        </a>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['address']); ?>">
        <a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
        <?php if(!empty($address)) { ?>
            <address><?php echo esc_attr($address); ?></address>
        <?php } ?>
    </td>
    <!-- <td data-label="ID">HY01</td> -->
    <td data-label="<?php echo homey_option('sn_type_label'); ?>"><?php echo homey_taxonomy_simple('listing_type'); ?></td>
    <td data-label="<?php echo esc_attr($homey_local['price_label']); ?>">
        <?php if(!empty($listing_price)) { ?>
        <strong><?php echo homey_formatted_price($listing_price, true); ?><?php echo esc_attr($price_separator); ?><?php echo homey_get_price_label_by_id($post_id); ?></strong><br>
        <?php } ?>
    </td>
    <td data-label="<?php echo homey_option('glc_bedrooms_label');?>"><?php echo esc_attr($bedrooms); ?></td>
    <td data-label="<?php echo homey_option('glc_baths_label');?>"><?php echo esc_attr($baths); ?></td>
    <td data-label="<?php echo homey_option('glc_guests_label');?>"><?php echo esc_attr($guests); ?></td>
    <td data-label="<?php echo homey_option('sn_id_label');?>"><?php echo get_the_ID(); ?></td>
    <td data-label="<?php echo esc_attr($homey_local['status_label']); ?>">
        <span class="label <?php echo esc_attr($status_class); ?>"><?php echo esc_html($property_status); ?></span><br/>
	<!-- agent segment -->
	<?php if(empty($agent_assigned_ids) || $agent_assigned_ids[0] == 0){ ?>
	<span class="label agent" style="color:#000;"><?php echo esc_html__('No Agent'); ?></span>
	<?php } else{ 
	       $agent_info = get_userdata($agent_assigned_ids[0]); 
	       $agent_displayname = $agent_info->display_name; ?>
	<span class="label agent" style="color:#000;"><?php echo 'Agent: '.$agent_displayname; ?></span>
	<?php } ?> <br/>
	<!-- property type -->
	<?php if($property_type == '' || $property_type == 'public'){ ?>
	   <span class="label property-type" style="color:#000;font-size:12px;"><?php echo esc_html__(strtoupper('Public')); ?></span>
	<?php } else{ ?>
	   <span class="label property-type" style="color:#000;font-size:12px;"><?php echo esc_html__(strtoupper('Private')); ?></span>
	<?php } ?>
    </td>
    <td data-label="<?php echo esc_attr($homey_local['actions_label']); ?>">
	<?php if($current_property_status == 'pending' && $dashboarduser_role == 'homey_brokers') { ?>
        <div class="custom-actions-top">
	   <button class="btn btn-primary" name="property-approval" style="padding:14px;line-height:1px;margin-bottom:3px;" 
		data-id="<?php echo intval($post->ID); ?>" data-toggle="tooltip" data-placement="top" 
		data-original-title="<?php echo esc_html__('Approve'); ?>"><?php echo esc_html__('Approve'); ?></button>
	</div>
	<?php } ?>
        <div class="custom-actions">
	    <?php if($dashboarduser_role != 'homey_agents'){ ?>

            <button class="btn-action" onclick="location.href='<?php echo esc_url($edit_link);?>';" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['edit_btn']); ?>"><i class="fa fa-pencil"></i></button>

            <?php if($featured != 1 && $make_featured != 0) { ?>
            <a href="<?php echo esc_url($upgrade_link); ?>" class="btn-action" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['upgrade_btn']); ?>"><i class="fa fa-star-o"></i></a>
            <?php } ?>
            
            <button class="btn-action delete-listing" data-id="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('delete_listing_nonce') ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['delete_btn']);?>">
                <i class="fa fa-trash"></i>
            </button>

            <?php if($check_listing_status == 'publish' || $check_listing_status == 'disabled') { ?>
            <button class="btn-action put_on_hold" data-id="<?php echo intval($post->ID); ?>" data-current="<?php echo esc_attr($list_current_status);?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($disable_list_text);?>">
                <i class="fa <?php echo esc_attr($icon); ?>"></i>
            </button>
            <?php } ?>

	    <?php if($property_type == '' || $property_type == 'public'){ //if public ?>
            <button class="btn-action change-listing-type" name="change-property-type" data-id="<?php echo intval($post->ID); ?>" data-val="private" data-nonce="<?php echo wp_create_nonce('change_listing_nonce') ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Make Private');?>"><i class="fa fa-lock"></i></button>
	    <?php } else { ?>
            <button class="btn-action change-listing-type" name="change-property-type" data-id="<?php echo intval($post->ID); ?>" data-val="public" data-nonce="<?php echo wp_create_nonce('change_listing_nonce') ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_html__('Make Public');?>"><i class="fa fa-unlock-alt"></i></button>
	    <?php } ?>

	    <?php } ?>

            <a href="<?php the_permalink(); ?>" target="_blank" class="btn-action" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($homey_local['view_btn']); ?>"><i class="fa fa-arrow-right"></i></a>

        </div>

	<?php if($dashboarduser_role == 'homey_brokers'){ ?>
	<div class="custom-actions-addln">
	 <?php
	   $user_args  = array( 'role__in' => ['homey_agents'], 'orderby' => 'display_name' );
   	   $agents = get_users($user_args);
           echo "<select class='homey-sellistingagents' name='homey-listingagents-brokers-dashboard' style='width:75%;'>";
           echo '<option value="0">'.esc_html__('Choose Agent').'</option>';
           foreach ($agents as $agent) { 
     	      $agent_info = get_userdata($agent->ID);
              echo '<option value="'.$agent_info->ID.','.$dashboard_user->ID.','.get_the_ID().'">'.$agent_info->display_name.'</option>';
   	   }
   	   echo "</select>";
	   echo '<div class="dashboard-agent-loader-spinner"></div>';
	?>
	</div>
	<?php } ?>
    </td>
</tr>
