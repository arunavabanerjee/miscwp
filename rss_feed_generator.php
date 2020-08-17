
/** ===========================================================================*
 *  Function for placing thumbnails to posts imported via RSS Aggragator
 * ============================================================================*/
/** will fire when a post is submitted */
add_action('wp_insert_post', 'rss_aggregator_importpost_update', 10, 3);
function rss_aggregator_importpost_update($post_id, $post, $update){ 
   $logfile = get_home_path().'rss_postimg_importer.log'; 
   //check the feed has been updated or inserted by admin
   $post_type = get_post_type($post_id);
   if( $post_type == 'wprss_feed' ){ 
	error_log('==============================================='.PHP_EOL, 3, $logfile);
	error_log(" Checking Items For FeedSource : ".$post->post_title.PHP_EOL, 3, $logfile);
	error_log(" Feed Id: ".$post_id.PHP_EOL, 3, $logfile);
	error_log('==============================================='.PHP_EOL, 3, $logfile);
	//fetch all items in the feed to add images 
	$qry = new WP_Query( array( 
	   'post_type' => 'wprss_feed_item', 'post_status' => 'publish',
	   'posts_per_page' => -1, 'fields' => 'ids',
	   'meta_query' => array( 
	      array('key' => 'wprss_feed_id', 'value' => $post_id, 'compare' => '='),  
	   ),
	));
	if( $qry->found_posts == 0){ return; } //var_dump($qry->posts); exit;
	//loop through and check the posts
	foreach( $qry->posts as $feedItemID ){ $updateImg = 0; 
	   //check if feeditem has been updated in a month
	   $updatedOn = get_post_meta($feedItemID, 'rssfeed_image_update', true); 
	   if(empty($updatedOn)){ $updateImg = 1; }
	   else{ 
	     $updatedDt = date('Y-m-d', $updatedOn); 
	     $datetomatch = date('Y-m-d', $updatedDt.' +1 day'); 
	     if($updatedDt < $datetomatch){ $updateImg = 1; }
	   }

	   // if image of post not updated earlier		
	   if( $updateImg == 1 ){
		//echo 'updating for'.$feedItemID; exit;
	    	$feedpost = get_post($feedItemID); $pcontent = '';

		//check if post contains empty values 
	    	if( !empty($feedpost->post_content) ){ $pcontent = $feedpost->post_content; }
	    	elseif( !empty($feedpost->post_excerpt) ){ $pcontent = $feedpost->post_excerpt; }
	    	if( empty($pcontent) ){ // if empty, continue 
		   error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
		   error_log(" Post Content Empty - FeedItemId: ".$feedItemID.PHP_EOL, 3, $logfile);
  		   error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
		   update_post_meta($feedItemID, 'rssfeed_image_update', time()); 
		   $updateImg = 0; continue; 
		}
		//if feed has content
	    	$doc = new DOMDocument(); $doc->loadHTML($pcontent); 
	    	try{ $docImgs = $doc->getElementsByTagName('img'); } 
	    	catch(Exception $e){ echo $e->getMessage();
		    update_post_meta($feedItemID, 'rssfeed_image_update', time());  
		    $updateImg = 0; continue; 
		}
		//if there are no images 
		if($docImgs->length == 0) {
		    //check for the value of wprss_item_permalink to fetch the image
		   error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
		   error_log(" No Images Found In Post Content - FeedItemId: ".$feedItemID.PHP_EOL, 3, $logfile);
  		   error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
		   update_post_meta($feedItemID, 'rssfeed_image_update', time());  
		   $updateImg = 0; continue;
		}		

		// if there are images
	    	if($docImgs->length > 0) {
		   $s_time = microtime(true);
		   $xpath = new DOMXpath($doc); 
		   $ad_image_tag = $xpath->query("//img/@src"); 
		   $ad_image = $ad_image_tag->item(0)->nodeValue;
		   error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
		   error_log(" Images In Post Content - FeedItemId: ".$feedItemID.PHP_EOL, 3, $logfile);
		   error_log(" First Image Link: ".$ad_image.PHP_EOL, 3, $logfile);
		   //echo 'FeedItem:'.$feedItemID.'--Ad-Img:'.$ad_image; exit;
		   //if(strstr($ad_image, 'http')){ $ad_image_img = preg_replace('/^https?:/','http:', $ad_image); }
		   //else{ $ad_image_img = $ad_image; } 
		   try{
		      $ch = curl_init();
		      curl_setopt($ch, CURLOPT_URL, $ad_image);
		      curl_setopt($ch, CURLOPT_HEADER, 0);
		      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		      curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		      $picture = curl_exec($ch); curl_close($ch); 
		      //var_dump($picture); exit;
		   }catch(Exception $e){ echo $e->getMessage(); 
			update_post_meta($feedItemID, 'rssfeed_image_update', time());
			$updateImg = 0; continue; 
		   }
		   error_log(" Image Fetched: ".$ad_image.PHP_EOL, 3, $logfile);

		   //check whether image file contains query
		   $ad_img_url = '';
		   if( strstr($ad_image, '?') ){ $ad_img_url = strstr($ad_image, '?', true); }
		   if(empty($ad_img_url)){ $ad_img_url = $ad_image; }
		   //create thumbnail for the post which will be used by Youzer
		   $extimgfile = basename($ad_img_url); $filename = $extimgfile; 
		   error_log(" Images Filename: ".$extimgfile.PHP_EOL, 3, $logfile);
		   //$upload = wp_upload_bits($filename , null, file_get_contents($ad_image, FILE_USE_INCLUDE_PATH));
		   $upload = wp_upload_bits($filename , null, $picture);
		   $image_file = $upload['file'];
		   $file_type = wp_check_filetype($image_file, null); 
		   $attachment = array(
			'post_mime_type' => $file_type['type'],
			'post_title' => sanitize_file_name( $filename ),
			'post_content' => '',
			'post_status' => 'inherit' );
		   $attachment_id = wp_insert_attachment( $attachment, $image_file, $feedItemID );
		   $attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
		   wp_update_attachment_metadata( $attachment_id, $attachment_data );
		   error_log(" Attachment Updated: ".$attachment_id.PHP_EOL, 3, $logfile);

		   set_post_thumbnail( $feedItemID, $attachment_id );
		   error_log(" Attachment Set To Post: ".$feedItemID.PHP_EOL, 3, $logfile);

		   update_post_meta($feedItemID, 'rssfeed_image_update', time()); $updateImg=0;
		   error_log(" Updated Meta FeedVal For Post: ".$feedItemID.PHP_EOL, 3, $logfile);

		   $e_time = microtime(true); $elapsed = $e_time - $s_time;
		   error_log(" Time Taken In Microsec: ".$elapsed.PHP_EOL, 3, $logfile);
		   error_log('==============================================='.PHP_EOL, 3, $logfile);

		 } //endif($docImgs->length > 0)
  		 //break;
	    } //endif( $updateImg == 1 )
	} //foreach $feedItemiD
   } //$post_type == 'wprss_feed'


   // if feed item added/updated from the rss feed source url
   /*if( $post_type == 'wprss_feed_item' ){ 
	echo '<div class="notice notice-success is-dismissible">';
	echo '<p>'._e('Checking On Feed Item: '.$post->ID.' Time: '.$elapsed, 'thrive-nouveau' ).'</p>';
	echo '</div>';
	//check if post contains empty values 
	if(!empty($post->post_content)){ $pcontent = $post->post_content; }
	elseif(!empty($post->post_excerpt)){ $pcontent = $post->post_excerpt; }
	if(empty($pcontent)){ return; }
	$doc = new DOMDocument(); @$doc->loadHTML($pcontent); 
	try{ $docImgs = $doc->getElementsByTagName('img'); } 
	catch(Exception $e){ echo $e->getMessage(); return; }

	// if there are images in the content 
	if($docImgs->length > 0) { $updateImg = 0;
	    if( has_post_thumbnail($post->ID) ){
		//check if thumbnail had been updated in a month  
		$updatedOn = get_post_meta($post->ID, 'rssfeed_image_update', true); 
		if(empty($updatedOn)){ $updateImg = 1; }
		else{ 
		   $updatedDt = date('Y-m-d', $updatedOn); 
		   $datetomatch = date('Y-m-d', $updatedDt.' +1 month'); 
		   if($updatedDt < $datetomatch){ $updateImg = 1; }
		}
	    } else { $updateImg = 1; }

	    // if isset updateImg, update the image
	    if( $updateImg == 1 ){
		$s_time = microtime(true);
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
		$picture = curl_exec($ch); curl_close($ch); 
		//var_dump($picture); exit;

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
		update_post_meta($post->ID, 'rssfeed_image_update', time()); $updateImg=0;
		$e_time = microtime(true); $elapsed = $e_time - $s_time;
		echo '<div class="notice notice-success is-dismissible">';
		echo '<p>'._e('Image Updated For Feed Item: '.$post->ID.' Time: '.$elapsed, 'thrive-nouveau' ).'</p>';
		echo '</div>';

	    } // endif( $updateImg == 1 )

	} // endif($docImgs->length > 0)

   } // endif $post_type == 'wprss_feed_item' */
}

=======================================================================================

=================================================================================================================
-------------
current code
-------------
/** ===========================================================================
 * Function for placing blog posts on user timeline
 * ============================================================================*/
if(class_exists('Youzer_Wall')){
  remove_filter('bp_activity_entry_content', array( Youzer_Wall::get_instance(), 'get_post'), 10);
}

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
    if(!empty($activity->item_id)){ var_dump($activity->item_id);
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
			$xpath = new DOMXpath($doc); 
			$ad_image_tag = $xpath->query("//img/@src"); 
			$ad_image = $ad_image_tag->item(0)->nodeValue; 
			//echo 'Ad-Img:';var_dump($ad_image); 
			//if(strstr($ad_image, 'http')){ $ad_image_img = preg_replace('/^https?:/','http:', $ad_image); }
			//else{ $ad_image_img = $ad_image; } 
			try{
			   $base64_encoded_image = base64_encode(file_get_contents($ad_image)); 
			   if( strstr($ad_image, '.jpg') ){ $base64_image = "data:image/jpg;base64,$base64_encoded_image"; }
			   elseif( strstr($ad_image, '.jpeg') ){ $base64_image = "data:image/jpeg;base64,$base64_encoded_image"; }
			   elseif( strstr($ad_image, '.gif') ){ $base64_image = "data:image/gif;base64,$base64_encoded_image"; }
			   elseif(strstr($ad_image, '.png')){ $base64_image = "data:image/png;base64,$base64_encoded_image"; }
			}catch(Exception $e){ echo $e->getMessage(); }
			echo 'Base64-Img:'; var_dump($base64_image);

			ob_start(); ?>
			<div class="yz-activity-embed">
	 		<div class="yz-wall-new-post">
				<?php /*if( isset($base64_image) && strlen($base64_image) > 0 ){ ?>
	 			<div class="yz-post-img"><a href="<?php echo $activity->primary_link; ?>">
				    <img src="<?php if($base64_image != NULL){ echo $base64_image; } ?>" width="350" height="310"  style="width:350px;height:310px;margin:0 auto;"/></a>
				</div>
				<?php }*/ ?>
	 			<div class="yz-post-img"><a href="<?php //echo $activity->primary_link; ?>">
				    <img src="<?php echo $ad_image; ?>" width=350 height=310 style="width:350px;height:310px;margin:0 auto;"/></a>
				</div>
	 			<div class="yz-post-inner">
		 			<div class="yz-post-head">
		 			<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 			<div class="yz-post-meta">
		 			<?php if ( ! empty( $categories ) ) : ?>
		 			<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 			<?php endif; ?>
		 			<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post->ID ); ?></div>
		 			<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 			</div>
					</div>
		 		</div>
		 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button"><span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span><span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span></a>
	 		</div>
	 		</div>
			<?php $current_content .= ob_get_contents(); ob_end_clean();

		}else{ // if there are no images in content
			ob_start(); ?>
			<div class="yz-activity-embed">
 			<div class="yz-wall-new-post">
			<div class="yz-post-inner">
	 		<div class="yz-post-head">
		 		<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 			<?php if ( ! empty( $categories ) ) : ?>
		 			<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 			<?php endif; ?>
		 			<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post->ID ); ?></div>
		 			<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
			   <span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
			   <span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 		</div>
	 		</div>
			</div>
			<?php $current_content .= ob_get_contents(); ob_end_clean();
		} // end if else 

	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $current_content ;
}


// action for blog post - default 
add_filter('bp_blogs_format_activity_action_new_blog_post', 'func_blogs_format_activity_action_new_blog_post', 10, 2); 
function func_blogs_format_activity_action_new_blog_post($action, $activity){ 
	if(empty($activity->action)){ return $action; }
	else{ return $activity->action; }
}

add_action('wp_loaded', 'func_bp_timeline_user_login');
function func_bp_timeline_user_login(){ 
   $no_of_feed_items = 6; $fetch = 0; 
   $no_of_blog_items_to_display = 4; 
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
			if(is_string($primary_sport) && !empty($primary_sport)){ array_push($userkeys, $primary_sport); }
			if(is_array($secondary_sport) && !empty($secondary_sport)){ $userkeys = array_merge($userkeys, $secondary_sport); }
			$userkeys = array_unique($userkeys); //var_dump($userkeys);

	   		//get all feed item ids that have been fetched.
	   		$pargs = array( 'post_type' => 'wprss_feed_item', 'post_status' =>'publish', 'posts_per_page' => -1, 
					'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' );
			$wp_rss_feed = new WP_Query($pargs); $queryposts = $wp_rss_feed->posts; shuffle($queryposts); 
			$count=0; //var_dump($queryposts); 
			// set feed items according to user preference.
			foreach($queryposts as $postId){
		  		//$posttitle = get_the_title($postId); 
				$postpermalink = get_post_meta($postId, 'wprss_item_permalink', true);
		  		foreach($userkeys as $userkey){ 
		     			if(stristr($postpermalink, $userkey)){ 
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
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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
					$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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

=====================================================================================================================================

 	<div class="yz-wall-new-post">
		<div class="yz-post-img"><a href="<?php echo $post_link; ?>"><?php echo $post_tumbnail; ?></a></div>

	 		<?php do_action( 'yz_after_wall_new_post_thumbnail', $post_id ); ?>

	 		<div class="yz-post-inner">

		 		<div class="yz-post-head">
		 			<div class="yz-post-title"><a href="<?php echo $post_link; ?>"><?php echo $post->post_title; ?></a></div>
		 			<div class="yz-post-meta">
		 				<?php if ( ! empty( $categories ) ) : ?>
		 				<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 				<?php endif; ?>
		 				<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 				<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 			</div>
		 		</div>
		 		<div class="yz-post-excerpt">
			        <p><?php echo yz_get_excerpt( $post->post_content, 40 ); ?></p>
		 		</div>
		 		<?php do_action( 'yz_activity_new_blog_post_before_read_more', $post ); ?>
		 		<a href="<?php echo $post_link; ?>" class="yz-post-more-button"><span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span><span class="yz-btn-title"><?php echo apply_filters( 'yz_wall_embed_blog_post_read_more_button', __( 'Read More', 'youzer' ) ); ?></span></a>
	 		</div>
	 	</div>

		<?php

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

==================================================================================

	$categories = get_the_category_list( ', ', ' ', $post->ID );

	//load image from post_content as there are no images as thumb
	$doc = new DOMDocument(); 
	$dom->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8')); 
	$imgs = $dom->getElementsByTagName('img'); echo 'Imgs'; var_dump($imgs) exit;

	//if the post contains no images
	if(empty($imgs)){ 
	ob_start();
	?>
 	<div class="yz-wall-new-post">
		<div class="yz-post-inner">
	 	<div class="yz-post-head">
		 	<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 		<?php if ( ! empty( $categories ) ) : ?>
		 		<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 		<?php endif; ?>
		 		<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 		<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
				<span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
				<span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 	</div>
	 	</div>
	</div>
	<?php $content = ob_get_contents(); ob_end_clean(); return $content; ?>
	<?php } 

===============================================================================================

add_action('bp_activity_entry_content', 'func_bp_activity_entry_content', 10, 2); 
function func_bp_activity_entry_content($args){
	global $activities_template;
	$activity = $activities_template->activity; //var_dump($activity); exit;
        $post = get_post( $activity->secondary_item_id ); //var_dump($post); exit;
	$categories = get_the_category_list( ', ', ' ', $post->ID ); 

	$doc = new DOMDocument();
	$doc->loadHTML($post->post_content); //var_dump($doc); exit;
	try{ 
	   $docImgs = $doc->getElementsByTagName('img'); 
	} catch(Exception $e){ echo $e->getMessage(); return; }

	if($docImgs->length != 0) { // if there are images in the content

		var_dump($docImgs); exit;

	}else{ ob_start(); ?>
 	<div class="yz-wall-new-post">
		<div class="yz-post-inner">
	 	<div class="yz-post-head">
		 	<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 		<?php if ( ! empty( $categories ) ) : ?>
		 		<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 		<?php endif; ?>
		 		<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 		<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
			   <span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
			   <span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 	</div>
	 	</div>
	</div>
	<?php $content = ob_get_contents(); ob_end_clean(); return $content; 
	} // end if else 
}

--------------------------------------------------------------------------------------------

// activity content for youzer template
add_filter('bp_get_activity_content_body', 'func_bp_get_activity_content_body', 10, 2);
function func_bp_get_activity_content_body( $content, $activity ){ 
    if(!empty($activity->item_id)){ 
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$current_content = $content; 
		$post = get_post( $activity->secondary_item_id ); //var_dump($post); exit;
		$categories = get_the_category_list( ', ', ' ', $post->ID ); 

		$doc = new DOMDocument();
		$doc->loadHTML($post->post_content); //var_dump($doc); exit;
		try{ 
	   	   $docImgs = $doc->getElementsByTagName('img'); 
		} catch(Exception $e){ echo $e->getMessage(); return; }
		if($docImgs->length != 0) { // if there are images in the content

			var_dump($docImgs); exit;

		}else{ ob_start(); ?>
 			<div class="yz-wall-new-post">
			<div class="yz-post-inner">
	 		<div class="yz-post-head">
		 		<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 			<?php if ( ! empty( $categories ) ) : ?>
		 			<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 			<?php endif; ?>
		 			<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 			<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
			   <span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
			   <span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 		</div>
	 		</div>
			</div>
			<?php $current_content .= ob_get_contents(); ob_end_clean();
		} // end if else 

	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $current_content ;
}

============================================================================================

$d = new DOMDocument();
$d->loadHTML($ads); // the variable $ads contains the HTML code above
$xpath = new DOMXPath($d);
$ls_ads = $xpath->query('//a');

foreach ($ls_ads as $ad) {
    // get ad url
    $ad_url = $ad->getAttribute('href');

    // set current ad object as new DOMDocument object so we can parse it
    $ad_Doc = new DOMDocument();
    $cloned = $ad->cloneNode(TRUE);
    $ad_Doc->appendChild($ad_Doc->importNode($cloned, True));
    $xpath = new DOMXPath($ad_Doc);

    // get ad title
    $ad_title_tag = $xpath->query("//div[@class='title']");
    $ad_title = trim($ad_title_tag->item(0)->nodeValue);

    // get ad image
    $ad_image_tag = $xpath->query("//img/@src");
    $ad_image = $ad_image_tag->item(0)->nodeValue;
}

============================================================================================

	global $activities_template;
	$activity = $activities_template->activity; //var_dump($activity); exit;
        $post = get_post( $activity->secondary_item_id ); //var_dump($post); exit;
	$categories = get_the_category_list( ', ', ' ', $post->ID ); 

	$doc = new DOMDocument();
	$doc->loadHTML($post->post_content); //var_dump($doc); exit;
	try{ 
	   $docImgs = $doc->getElementsByTagName('img'); 
	} catch(Exception $e){ echo $e->getMessage(); return; }

	if($docImgs->length != 0) { // if there are images in the content

		var_dump($docImgs); exit;

	}else{ ob_start(); ?>
 	<div class="yz-wall-new-post">
		<div class="yz-post-inner">
	 	<div class="yz-post-head">
		 	<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 		<?php if ( ! empty( $categories ) ) : ?>
		 		<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 		<?php endif; ?>
		 		<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 		<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
			   <span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
			   <span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 	</div>
	 	</div>
	</div>
	<?php $content = ob_get_contents(); ob_end_clean(); return $content; 
	} // end if else 

=========================================================================================
/** ===========================================================================
 * Function for placing blog posts on user timeline
 * ============================================================================*/
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
    if(!empty($activity->item_id)){ 
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$current_content = $content; 
		$post = get_post( $activity->secondary_item_id ); //var_dump($post); exit;
		$categories = get_the_category_list( ', ', ' ', $post->ID ); 

		$doc = new DOMDocument();
		$doc->loadHTML($post->post_content); 
		try{ 
	   	   $docImgs = $doc->getElementsByTagName('img'); 
		} catch(Exception $e){ echo $e->getMessage(); return; }
		if($docImgs->length > 0) { // if there are images in the content
			$xpath = new DOMXpath($doc); 
			$ad_image_tag = $xpath->query("//img/@src"); 
			$ad_image = $ad_image_tag->item(0)->nodeValue; //var_dump($ad_image); 
			//if(strstr($ad_image, 'http')){ $ad_image_img = preg_replace('/^https?:/','http:', $ad_image); }
			//else{ $ad_image_img = $ad_image; } 
			try{
			   $base64_encoded_image = base64_encode(file_get_contents($ad_image)); 
			   if( strstr($ad_image, '.jpg') ){ $base64_image = "data:image/jpg;base64,$base64_encoded_image"; }
			   elseif( strstr($ad_image, '.jpeg') ){ $base64_image = "data:image/jpeg;base64,$base64_encoded_image"; }
			   elseif( strstr($ad_image, '.gif') ){ $base64_image = "data:image/gif;base64,$base64_encoded_image"; }
			   elseif(strstr($ad_image, '.png')){ $base64_image = "data:image/png;base64,$base64_encoded_image"; }
			}catch(Exception $e){ echo $e->getMessage(); }

			ob_start(); ?>
			<div class="yz-activity-embed">
	 		<div class="yz-wall-new-post">
				<?php /*if( isset($base64_image) && strlen($base64_image) > 0 ){ ?>
	 			<div class="yz-post-img"><a href="<?php echo $activity->primary_link; ?>">
				    <img src="<?php if($base64_image != NULL){ echo $base64_image; } ?>" width="350" height="310"  style="width:350px;height:310px;margin:0 auto;"/></a>
				</div>
				<?php }*/ ?>
	 			<div class="yz-post-img"><a href="<?php //echo $activity->primary_link; ?>">
				    <img src="<?php echo $ad_image; ?>" width=350 height=310 style="width:350px;height:310px;margin:0 auto;"/></a>
				</div>
	 			<div class="yz-post-inner">
		 			<div class="yz-post-head">
		 			<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 			<div class="yz-post-meta">
		 			<?php if ( ! empty( $categories ) ) : ?>
		 			<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 			<?php endif; ?>
		 			<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 			<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 			</div>
					</div>
		 		</div>
		 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button"><span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span><span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span></a>
	 		</div>
	 		</div>
			<?php $current_content .= ob_get_contents(); ob_end_clean();

		}else{ ob_start(); ?>
			<div class="yz-activity-embed">
 			<div class="yz-wall-new-post">
			<div class="yz-post-inner">
	 		<div class="yz-post-head">
		 		<div class="yz-post-title"><a href="<?php echo $activity->primary_link; ?>"><?php echo $post->post_title; ?></a></div>
		 		<div class="yz-post-meta">
		 			<?php if ( ! empty( $categories ) ) : ?>
		 			<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 			<?php endif; ?>
		 			<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 			<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 		</div>
		 	</div>
	 		<a href="<?php echo $activity->primary_link; ?>" class="yz-post-more-button">
			   <span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span>
			   <span class="yz-btn-title"><?php echo __( 'Read More', 'youzer' ); ?></span>
			</a>
	 		</div>
	 		</div>
			</div>
			<?php $current_content .= ob_get_contents(); ob_end_clean();
		} // end if else 

	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $current_content ;
}


/** ===========================================================================
 * Function for cron functions
 * ============================================================================*/
/**
 * Run the function every 5mins
 */ 
//create a cron schedule. 
add_filter('cron_schedules','bp_timeline_cron_schedules');
function bp_timeline_cron_schedules($schedules){
    if(!isset($schedules["5min"])){
	$schedules["5min"] = array('interval' => 5*60, 'display' => __('Once every 5 minutes'));
    }
    //var_dump($schedules);
    return $schedules; 
}

//check if the hook has been scheduled
//if(!wp_next_scheduled('bp_timeline_schedule_hook')){
//   wp_schedule_event(time(), "thirty_min", 'bp_timeline_schedule_hook');
//}

//add_action('bp_timeline_schedule_hook', 'bp_timeline_cron_funct');
//add_action('init', 'bp_timeline_cron_funct');
//add_filter('bp_blogs_format_activity_action_new_blog_comment', 'func_blogs_format_activity_action_new_blog_comment', 10, 2); 
//function func_blogs_format_activity_action_new_blog_comment($action, $activity){ }


// action for blog post - default 
add_filter('bp_blogs_format_activity_action_new_blog_post', 'func_blogs_format_activity_action_new_blog_post', 10, 2); 
function func_blogs_format_activity_action_new_blog_post($action, $activity){ 
	if(empty($activity->action)){ return $action; }
	else{ return $activity->action; }
}

//add_action('wp_loaded', 'func_bp_timeline_user_login');
function func_bp_timeline_user_login(){ 
   $no_of_feed_items = 6; $fetch = 0; 
   $no_of_blog_items_to_display = 4; 
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
			if(is_string($primary_sport) && !empty($primary_sport)){ array_push($userkeys, $primary_sport); }
			if(is_array($secondary_sport) && !empty($secondary_sport)){ $userkeys = array_merge($userkeys, $secondary_sport); }
			$userkeys = array_unique($userkeys); //var_dump($userkeys);

	   		//get all feed item ids that have been fetched.
	   		$pargs = array( 'post_type' => 'wprss_feed_item', 'post_status' =>'publish', 'posts_per_page' => -1, 
					'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' );
			$wp_rss_feed = new WP_Query($pargs); $queryposts = $wp_rss_feed->posts; shuffle($queryposts); 
			$count=0; //var_dump($queryposts); 
			// set feed items according to user preference.
			foreach($queryposts as $postId){
		  		//$posttitle = get_the_title($postId); 
				$postpermalink = get_post_meta($postId, 'wprss_item_permalink', true);
		  		foreach($userkeys as $userkey){ 
		     			if(stristr($postpermalink, $userkey)){ 
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
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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
					$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
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




/** ===========================================================================
 * Function for placing activity posts on user timeline
 * ============================================================================*/
// activity action for user template
/*add_filter('bp_get_activity_action', 'func_bp_get_activity_action', 10, 3);
function func_bp_get_activity_action( $caction, $activity, $args ){
    if(!empty($activity->item_id)){ 
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$target = ' target="_blank" ';
		$title = get_the_title($activity->secondary_item_id);
		$item_link = '<a href="' . $activity->primary_link . '" ' . $target . '>' . $title . '</a>';
		$activity_action = sprintf(__('Activity Posted by Administrator: %1$s'), $item_link ); 
		$caction = $activity_action;
	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $caction;
}*/

// activity content for youzer template
/*add_filter('bp_get_activity_content_body', 'func_bp_get_activity_content_body', 10, 2);
function func_bp_get_activity_content_body( $content, $activity ){
    if(!empty($activity->item_id)){ 
	$post_type = get_post_type($activity->item_id); 
	if( $post_type == 'wprss_feed' ){
		$current_content = $content;
		if(stristr($current_content, 'Read more')){
		   $target = ' target="_blank" ';
		   $item_link = '<a href="' . $activity->primary_link . '" ' . $target . '> Read More >> </a>';	
		   $replaced_content = str_replace('Read more', $item_link, $current_content);	
		}elseif(strstr($current_content, '[...]')){
		   $target = ' target="_blank" ';
		   $item_link = '<a href="' . $activity->primary_link . '" ' . $target . '> Read More >> </a>';	
		   $replaced_content = str_replace('Read more', $item_link, $current_content);
		}
	} else { // do not change action
		return;
	}
    } else{ // if itemid is empty, then it is not admin activity
	return; 
    }
    return $replaced_content;
}*/

// activity action for buddypress template
add_filter('bp_activity_new_update_action', 'func_bp_activity_new_update_action', 10, 2); 
function func_bp_activity_new_update_action($action, $activity){ 
	if(empty($activity->action)){ return $action; }
	else{ return $activity->action; }
}

//add_action('init', 'func_bp_timeline_user_login_avtivity_feed');
function func_bp_timeline_user_login_avtivity_feed(){ 
   $no_of_feed_items = 6; $fetch = 0; 
   $no_of_blog_items_to_display = 3; 
   /* the timeline action will happen only for logged in user */
   if(bp_is_active() && is_user_logged_in()){
	$loggedInUser = wp_get_current_user(); 
	$uroles = $loggedInUser->roles;
	/* feed on timeline will only happen for suscriber users */
	if(in_array('subscriber', $uroles)){ $fetch = 1;
		//check if user has been updated today 
		$updatedOn = get_user_meta($loggedInUser->ID, 'userfeed_activity_updated', true); 
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
			if(is_string($primary_sport) && !empty($primary_sport)){ array_push($userkeys, $primary_sport); }
			if(is_array($secondary_sport) && !empty($secondary_sport)){ $userkeys = array_merge($userkeys, $secondary_sport); }
			$userkeys = array_unique($userkeys); //var_dump($userkeys);

	   		//get all feed item ids that have been fetched.
	   		$pargs = array( 'post_type' => 'wprss_feed_item', 'post_status' =>'publish', 'posts_per_page' => -1, 
					'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' );
			$wp_rss_feed = new WP_Query($pargs); $queryposts = $wp_rss_feed->posts; shuffle($queryposts); 
			$count=0; //var_dump($queryposts); 
			// set feed items according to user preference.
			foreach($queryposts as $postId){
		  		//$posttitle = get_the_title($postId);
				$postpermalink = get_post_meta($postId, 'wprss_item_permalink', true);
		  		foreach($userkeys as $userkey){ 
		     			if(stristr($postpermalink, $userkey)){ 
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
						array('filter' => array( 'user_id' => $loggedInUser->ID, 'object' => 'activity' )));
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
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
			        		/** Add the posts that have been found onto the user timeline **/
			   			$activity_data = array(
							'user_id' 	=> $loggedInUser->ID,
							'component' 	=> 'activity',
							'type'		=> 'activity_update',
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
								array( 'user_id' => $loggedInUser->ID, 'component' => 'activity', 
						       			'item_id' => intval($feedId), 'secondary_item_id' => $feedItemId )); 
						//var_dump($activityid); exit;
						$usernam = bp_core_get_user_displayname( $loggedInUser->ID ); 
						$target = ' target="_blank" ';
						$item_link = '<a href="' . $item_permalink . '" ' . $target . '>' . $title . '</a>';
						//$activity_action = sprintf(__('Imported Blog by %1$s: %2$s'), $usernam, $item_link );
						$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
						$activity_data = array(
							'id'  => $activityid,
							'user_id' => $loggedInUser->ID,
							'component' => 'activity',
							'type' => 'activity_update',
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
					$activity_action = sprintf(__('Posted by Administrator: %1$s'), $item_link );
			   		/** Add the posts that have been found onto the user timeline **/
			   		$activity_data = array(
						'user_id' 	=> $loggedInUser->ID,
						'component' 	=> 'activity',
						'type'		=> 'activity_update',
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
			update_user_meta($loggedInUser->ID, 'userfeed_activity_updated', time()); $fetch=0;
		   } // end if user_display_feedids
	     } // end function buddypress and fetchis1
	} // function will be activated 
   }//end if bp active or user logged in
}


