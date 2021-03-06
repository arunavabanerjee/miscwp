<?php
/**
 * Custom BuddyPress Hooks
 * Since 3.0.6
 */
add_action('bp_before_activity_entry', 'thrive_apply_bp_current_component');

/**
 * Apply bp current component to pages that are using buddypress activity shortcode.
 * @return void.
 */
function thrive_apply_bp_current_component() {
	if ( function_exists('buddypress') ) {
		if ( false === bp_current_component() ) {
			buddypress()->current_component = 'activity';
		}
	}
}

add_filter('bp_core_get_js_strings', 'thrive_apply_bp_js_strings');

/**
 * Applies the missing strings and object parameters when in pages and post.
 * @return void
 */
function thrive_apply_bp_js_strings( $params ) {
	
	if ( false !== bp_current_component() ) {
		return $params;
	}

	$activity_params = array(
		'user_id'     => bp_loggedin_user_id(),
		'object'      => 'user',
		'backcompat'  => (bool) has_action( 'bp_activity_post_form_options' ),
		'post_nonce'  => wp_create_nonce( 'post_update', '_wpnonce_post_update' ),
	);

	$user_displayname = bp_get_loggedin_user_fullname();

	if ( buddypress()->avatar->show_avatars ) {

		$width  = bp_core_avatar_thumb_width();
		$height = bp_core_avatar_thumb_height();

		$activity_params = array_merge( $activity_params, array(
			'avatar_url'    => bp_get_loggedin_user_avatar( array(
				'width'  => $width,
				'height' => $height,
				'html'   => false,
			) ),
			'avatar_width'  => $width,
			'avatar_height' => $height,
			'user_domain'   => bp_loggedin_user_domain(),
			'avatar_alt'    => sprintf(
				/* translators: %s = member name */
				__( 'Profile photo of %s', 'thrive-nouveau' ),
				$user_displayname
			),
		) );
	}

	$activity_buttons = apply_filters( 'bp_nouveau_activity_buttons', array() );

	if ( ! empty( $activity_buttons ) ) {

		$activity_params['buttons'] = bp_sort_by_key( $activity_buttons, 'order', 'num' );
		// Enqueue Buttons scripts and styles
		foreach ( $activity_params['buttons'] as $key_button => $buttons ) {
			if ( empty( $buttons['handle'] ) ) {
				continue;
			}
			if ( wp_style_is( $buttons['handle'], 'registered' ) ) {
				wp_enqueue_style( $buttons['handle'] );
			}
			if ( wp_script_is( $buttons['handle'], 'registered' ) ) {
				wp_enqueue_script( $buttons['handle'] );
			}
			unset( $activity_params['buttons'][ $key_button ]['handle'] );
		}
	}
	
	$activity_strings = array(
		'whatsnewPlaceholder' => sprintf( __( "What's new, %s?", 'thrive-nouveau' ), bp_get_user_firstname( $user_displayname ) ),
		'whatsnewLabel'       => __( 'Post what\'s new', 'thrive-nouveau' ),
		'whatsnewpostinLabel' => __( 'Post in', 'thrive-nouveau' ),
		'postUpdateButton'    => __( 'Post Update', 'thrive-nouveau' ),
		'cancelButton'        => __( 'Cancel', 'thrive-nouveau' ),
	);

	if ( bp_is_group() ) {
		$activity_params = array_merge(
			$activity_params,
			array(
				'object'  => 'group',
				'item_id' => bp_get_current_group_id(),
			)
		);
	}
	$params['activity'] = array(
		'params'  => $activity_params,
		'strings' => $activity_strings,
	);
	return $params;
}



/**
 * Get current BuddyPress component page id.
 * @return integer The id of the page which current bp component is living.
 */
function bp_get_current_component_page_id() {

	$id = 0;
	
	if ( function_exists('bp_current_component') ) {
		if ( bp_is_directory() ) {

			$component = bp_current_component();
		
			if ( ! empty( $component ) ) {
				$bp_pages = get_option( 'bp-pages' );
				if ( isset( $bp_pages[$component]) ) {
					$id = $bp_pages[$component];
				}
			}
		}
	}

	return $id;

}

add_action('bp_member_header_actions', 'thrive_bp_member_header_actions');
if ( ! function_exists( 'thrive_bp_member_header_actions') ):
	function thrive_bp_member_header_actions(){
		?>
		<?php if ( ! is_user_logged_in() ): ?>
			<div class="generic-button">
				<a href="<?php echo wp_login_url(); ?>" title="<?php esc_attr_e('Login', 'thrive-nouveau'); ?>">
					<?php esc_html_e('Login', 'thrive-nouveau'); ?>
				</a>
			</div>
			<?php $users_can_register = get_option('users_can_register'); ?>
			<?php if ( $users_can_register ): ?>
				<div class="generic-button">
					<a href="<?php echo wp_registration_url(); ?>" title="<?php esc_attr_e('Register', 'thrive-nouveau'); ?>">
						<?php esc_html_e('Register', 'thrive-nouveau'); ?>
					</a>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}
endif;


/** ===========================================================================
 *  Function for placing blog posts on user timeline
 * ============================================================================*/
//if(class_exists('Youzer_Wall')){
//  remove_filter('bp_activity_entry_content', array( Youzer_Wall::get_instance(), 'get_post'), 10);
//}

// acion for blog post youzer template
add_filter('bp_get_activity_action', 'func_bp_get_activity_action', 10, 3);
function func_bp_get_activity_action( $caction, $activity, $args ){
    if(!empty($activity->item_id)){ 
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$target = ' target="_blank" ';
		$title = get_the_title($activity->secondary_item_id);
		$item_link = '<a href="' . $activity->primary_link . '" ' . $target . '>' . $title . '</a>';
		$activity_action = sprintf(__('Blog Posted by Administrator: %1$s'), $item_link ); 
		$caction = $activity_action;
	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $caction;
}

// activity content for youzer template
add_filter('bp_get_activity_content_body', 'func_bp_get_activity_content_body', 10, 2);
function func_bp_get_activity_content_body( $content, $activity ){ 
    if(!empty($activity->item_id)){ //var_dump($activity->secondary_item_id);
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$current_content = $content; $pcontent = ''; 
		$post = get_post( $activity->secondary_item_id ); 

		//check if post contains empty values 
		if(!empty($post->post_content)){ $pcontent = $post->post_content; }
		elseif(!empty($post->post_excerpt)){ $pcontent = $post->post_excerpt; }
		if(empty($pcontent)){ return; }	
		$categories = get_the_category_list( ', ', ' ', $post->ID ); 
		$doc = new DOMDocument(); $doc->loadHTML($pcontent); 
		try{ $docImgs = $doc->getElementsByTagName('img'); } 
		catch(Exception $e){ echo $e->getMessage(); return; }

		// if there are images in the content 
		if($docImgs->length > 0) { 
		    //if( has_post_thumbnail($post->ID) ){
			$xpath = new DOMXpath($doc); 
			$ad_image_tag = $xpath->query("//img/@src"); 
			$ad_image = $ad_image_tag->item(0)->nodeValue; 
			//echo 'Ad-Img:';var_dump($ad_image); 
			//if(strstr($ad_image, 'http')){ $ad_image_img = preg_replace('/^https?:/','http:', $ad_image); }
			//else{ $ad_image_img = $ad_image; } 

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ad_image);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			$picture = curl_exec($ch); curl_close($ch); //var_dump($picture); exit;

			//create thumbnail for the post which will be used by Youzer
			$extimgfile = basename($ad_image); $filename = $extimgfile; 
			//$upload = wp_upload_bits($filename , null, file_get_contents($ad_image, FILE_USE_INCLUDE_PATH));
			$upload = wp_upload_bits($filename , null, $picture);
			$image_file = $upload['file'];
			$file_type = wp_check_filetype($image_file, null);
			$attachment = array(
				'post_mime_type' => $file_type['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit' );
			$attachment_id = wp_insert_attachment( $attachment, $image_file, $post->ID );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			set_post_thumbnail( $post->ID, $attachment_id );
		     //}
		} else{ 
			return; 
		}
	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $current_content ;
}

/**
 * Function for placing blog feed onto user timeline 
 */ 
add_filter('bp_blogs_format_activity_action_new_blog_post', 'func_blogs_format_activity_action_new_blog_post', 10, 2); 
function func_blogs_format_activity_action_new_blog_post($action, $activity){ 
	if(empty($activity->action)){ return $action; }
	else{ return $activity->action; }
}

add_action('wp_loaded', 'func_bp_timeline_user_login');
function func_bp_timeline_user_login(){ 
   $no_of_feed_items = 6; $fetch = 0; 
   $no_of_blog_items_to_display = 3; 
   /* the timeline action will happen only for logged in user */
   if(bp_is_active() && is_user_logged_in()){
	$loggedInUser = wp_get_current_user(); 
	$uroles = $loggedInUser->roles;
	/* feed on timeline will only happen for suscriber users */
	if(in_array('subscriber', $uroles)){ //$fetch = 1;
		//check if user has been updated today 
		$updatedOn = get_user_meta($loggedInUser->ID, 'userfeed_updated', true); 
		if(empty($updatedOn)){ $fetch = 1; }
		else{ 
		   $updatedDt = date('Y-m-d', $updatedOn); 
		   $currentDt = date('Y-m-d'); 
		   if($updatedDt < $currentDt){ $fetch = 1; }
		}
		//echo $updatedDt.','.$currentDt; echo 'Fetch:'.$fetch;
		$userkeys = array(); $user_display_feedIds = array();	
		if ( function_exists('buddypress')  && $fetch == 1) { 
			global $bp, $wpdb;

  			//query BP to get the keys from user signup. 
			$signups_table  = $bp->members->table_name_signups; 
        		$sql = "SELECT * FROM {$signups_table} WHERE user_login ='".$loggedInUser->user_login."'"; 
			$user_details = $wpdb->get_results( $sql, OBJECT ); 
			if(empty($user_details)){ return; }
			$umeta = unserialize($user_details[0]->meta); //var_dump($umeta); 
			//get the keys for the user. 
			$primary_sport = $umeta["field_19"]; $secondary_sport = $umeta["field_21"]; 

			//create the user keys array 
			if(is_string($primary_sport)){ array_push($userkeys, $primary_sport); }
			if(is_array($secondary_sport)){ $userkeys = array_merge($userkeys, $secondary_sport); }
			$userkeys = array_unique($userkeys); //var_dump($userkeys);

	   		//get all feed item ids that have been fetched.
	   		$pargs = array( 'post_type' => 'wprss_feed_item', 'post_status' =>'publish', 'posts_per_page' => -1, 
					'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' );
			$wp_rss_feed = new WP_Query($pargs); $queryposts = $wp_rss_feed->posts; shuffle($queryposts); 
			$count=0; //var_dump($queryposts); 
			// set feed items according to user preference.
			foreach($queryposts as $postId){
		  		$posttitle = get_the_title($postId);
		  		foreach($userkeys as $userkey){ 
		     			if(stristr($posttitle, $userkey)){ 
					   array_push($user_display_feedIds, $postId); $count++; break; 
		     			}
		  		}
		  		if($count == $no_of_blog_items_to_display){ break; } 
			}// end foreach
			//var_dump($user_display_feedIds); exit; 

			//display the posts on logged in usertimeline. 
			if(!empty($user_display_feedIds)){ 
		  	   //fetch all activity for the user for blogs
		   	   $bpActivitiesIds = array();
 		   	   if ( function_exists( 'bp_activity_get' ) )
		      	      $activityfeed = bp_activity_get( 
						array('filter' => array( 'user_id' => $loggedInUser->ID, 'object' => 'blogs' )));
		   	   //var_dump($activityfeed); //exit;
		   	   if( !empty($activityfeed['activities']) ){ 
		        	foreach( $activityfeed['activities'] as $activity ){ 
			 	    array_push($bpActivitiesIds, $activity->secondary_item_id);
		      		}
		   	   } 
		   	   //var_dump($bpActivitiesIds); exit; 
		   	   foreach($user_display_feedIds as $feedItemId){ 
				$feedpost = get_post($feedItemId); 
				$item_permalink = get_post_meta($feedItemId, 'wprss_item_permalink', true);
				$feedId = get_post_meta($feedItemId, 'wprss_feed_id', true); 
				$title = get_the_title($feedItemId);
			
				//if feeditemid is not already present earlier
				if(!empty($bpActivitiesIds)){
					// this is for new post not posted earlier
			   		if(!in_array($feedItemId, $bpActivitiesIds)){
						$usernam = bp_core_get_user_displayname( $loggedInUser->ID ); 
						$target = ' target="_blank" ';
						$item_link = '<a href="' . $item_permalink . '" ' . $target . '>' . $title . '</a>';
						//$activity_action = sprintf(__('Imported Blog by %1$s: %2$s'), $usernam, $item_link );
						$activity_action = sprintf(__('Blog Posted by Administrator: %1$s'), $item_link );
			        		/** Add the posts that have been found onto the user timeline **/
			   			$activity_data = array(
							'user_id' 	=> $loggedInUser->ID,
							'component' 	=> 'blogs',
							'type'		=> 'new_blog_post',
							'action' 	=> $activity_action,
							'primary_link'	=> $item_permalink,
							'recorded_time'	=> date( "Y-m-d H:i:s" ),
							'hide_sitewide'	=> false,
							'content' 	=> $feedpost->post_excerpt,
							'item_id' 	=> $feedId,
							'secondary_item_id' => $feedpost->ID,
			    			);
			    			bp_activity_add( $activity_data );

			   		} else { //if exists, then fetch activity id 
										
						$activityid = bp_activity_get_activity_id( 
								array( 'user_id' => $loggedInUser->ID, 'component' => 'blogs', 
						       			'item_id' => intval($feedId), 'secondary_item_id' => $feedItemId )); 
						//var_dump($activityid); exit;
						$usernam = bp_core_get_user_displayname( $loggedInUser->ID ); 
						$target = ' target="_blank" ';
						$item_link = '<a href="' . $item_permalink . '" ' . $target . '>' . $title . '</a>';
						//$activity_action = sprintf(__('Imported Blog by %1$s: %2$s'), $usernam, $item_link );
						$activity_action = sprintf(__('Blog Posted by Administrator: %1$s'), $item_link );
						$activity_data = array(
							'id'  => $activityid,
							'user_id' => $loggedInUser->ID,
							'component' => 'blogs',
							'type' => 'new_blog_post',
							'action' => $activity_action,
							'primary_link' => $item_permalink,
							'recorded_time'	=> date( "Y-m-d H:i:s" ),
							'hide_sitewide'	=> false,
							'content' => $feedpost->post_excerpt,
							'item_id' => $feedId,
							'secondary_item_id' => $feedpost->ID,
			     			);
			     			bp_activity_add( $activity_data );
			   		}
				} else { // when there are no feeds for the user before
			   		$usernam = bp_core_get_user_displayname( $loggedInUser->ID ); 
					$target = ' target="_blank" ';
			   		$item_link = '<a href="' . $item_permalink . '" ' . $target . '>' . $title . '</a>';
			   		//$activity_action = sprintf(__('Imported Blog by %1$s: %2$s'), $usernam, $item_link );
					$activity_action = sprintf(__('Blog Posted by Administrator: %1$s'), $item_link );
			   		/** Add the posts that have been found onto the user timeline **/
			   		$activity_data = array(
						'user_id' 	=> $loggedInUser->ID,
						'component' 	=> 'blogs',
						'type'		=> 'new_blog_post',
						'action' 	=> $activity_action,
						'primary_link'	=> $item_permalink,
						'recorded_time'	=> date( "Y-m-d H:i:s" ),
						'hide_sitewide'	=> false,
						'content' 	=> $feedpost->post_excerpt,
						'item_id' 	=> $feedId,
						'secondary_item_id' => $feedpost->ID,
			    		);
			    		bp_activity_add( $activity_data );

				} // end if(!empty($bpActivitiesIds)){
		   	} // end foreach
			//update user meta 
			update_user_meta($loggedInUser->ID, 'userfeed_updated', time()); $fetch=0;
		   } // end if user_display_feedids
	     } // end function buddypress and fetchis1
	} // function will be activated 
   }//end if bp active or user logged in
}


