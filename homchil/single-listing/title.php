<?php
global $post, $homey_prefix, $listing_author; 
global $current_user;

wp_get_current_user();

$address = homey_get_listing_data('listing_address');

$is_superhost = $listing_author['is_superhost'];

$rating = homey_option('rating');
$total_rating = get_post_meta( $post->ID, 'listing_total_rating', true );
?>
<div class="title-section">
    <div class="block block-top-title">
        <div class="block-body">
            <?php get_template_part('template-parts/breadcrumb'); ?>
            <h1 class="listing-title">
                <?php the_title(); ?> <?php homey_listing_featured(get_the_ID()); ?>    
            </h1>

            <?php if(is_user_logged_in()) { ?>
	    <?php if($current_user->roles[0] == "homey_brokers" || $current_user->roles[0] == "homey_agents") { ?>
	    <?php if(!empty($address)) { ?>
		<address><i class="fa fa-map-marker" aria-hidden="true"></i> 
		   <?php echo esc_attr($address); ?>
		</address>
            <?php }} else { /*for other users not brokers */?>
	    <?php if(!empty($address)) { $address_array = explode(',',$address); ?>
		<address><i class="fa fa-map-marker" aria-hidden="true"></i> 
		  <?php echo esc_attr($address_array[sizeof($address_array)-1]); ?>
		</address>
	    <?php }} ?>
	    <?php } else { /*user not logged in */
		if(!empty($address)) {
		$address_array = explode(',',$address); ?>
		<address><i class="fa fa-map-marker" aria-hidden="true"></i> 
		   <?php echo esc_attr($address_array[sizeof($address_array)-1]); ?>
		</address>
	    <?php }} ?>

            <div class="host-avatar-wrap avatar">
                <?php if($is_superhost) { ?>
                <span class="super-host-icon">
                    <i class="fa fa-bookmark"></i>
                </span>
                <?php } ?>
                <?php echo ''.$listing_author['photo']; ?>
            </div>

            <?php if($rating && ($total_rating != '' && $total_rating != 0 ) ) { ?>
            <div class="list-inline rating hidden-xs">
                <?php echo homey_get_review_stars($total_rating, true, true); ?>
            </div>
            <?php } ?>
        </div><!-- block-body -->
    </div><!-- block -->
</div><!-- title-section -->
