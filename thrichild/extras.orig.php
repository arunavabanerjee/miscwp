<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package thrive
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function thrive_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( thrive_is_rtl() ) {
		$classes[] = 'rtl';
	}

	if ( ! is_user_logged_in() ) {
		$classes[] = 'logged-out';
	}

	if ( is_single() ) {
		global $post;
		if ( 'post' === $post->post_type ) {
			$classes[] = 'single-blog';
		}
	}

	return $classes;
}

add_filter( 'body_class', 'thrive_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function thrive_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'thrive-nouveau' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'thrive_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function thrive_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'thrive_render_title' );
endif;


/**
 * Converts Hex to RGBA
 */
function thrive_hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
          return $default;

	//Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}

function thrive_comments($comment, $args, $depth){
$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<<?php echo thrive_handle_empty_var( $tag ) ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div itemscope itemtype="http://schema.org/Comment" id="div-comment-<?php comment_ID() ?>" class="comment-body row">
	<?php endif; ?>


	<div class="comment-author vcard col-xs-2 col-sm-1 no-pd-right">
		<div class="clearfix">
			<?php echo get_avatar( $comment, 64 ); ?>
		</div>
	</div>

	<div class="comment-content col-xs-10 col-sm-11">

		<div class="comment-content-context">

		<?php if ( $comment->comment_approved == '0' ) : ?>
		<p>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'thrive-nouveau'); ?></em>
		</p>
		
		<?php endif; ?>

		<div class="row">
			<span class="sr-only" itemprop="author">
				<?php echo get_comment_author(); ?>
			</span>
			<div class="comment-author-name mg-bottom-10 col-xs-12 col-sm-5 ">
				<?php printf( __( '<span class="type-strong fn">%s</span>', 'thrive-nouveau' ), get_comment_author_link() ); ?>
			</div>
			<div class="comment-meta commentmetadata col-sm-7 col-xs-12">
				<span class="pull-right mg-left-10">
					<?php edit_comment_link( __( '(Edit)', 'thrive-nouveau' ), '  ', '' );?>
				</span>
				<a class="type-strong dark_disabled pull-right" 
					href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
					<span itemprop="dateCreated">
						<?php printf( __('%1$s at %2$s', 'thrive-nouveau'), get_comment_date(),  get_comment_time() ); ?>
					</span>
				</a>

				<div class="clearfix"></div>
			</div>
		</div>

		<div class="comment-text" itemprop="text">
			<?php comment_text(); ?>
		</div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
		</div>
	</div>

	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php
}
add_filter( 'get_the_archive_title', 'thrive_archive_title');

function thrive_archive_title() {

	$title = "";

    if ( is_category() ) {

    	$title  = '<span class="archive-type">' . __('Category', 'thrive-nouveau') . '</span>';
        $title .= '<span class="archive-title">' . single_cat_title( '', false ) . '</span>';

    } elseif ( is_tag() ) {

    	$title  = '<span class="archive-type">' . __('Tag', 'thrive-nouveau') . '</span>';
        $title .= '<span class="archive-title">' . single_tag_title( '', false ) . '</span>';


    } elseif ( is_author() ) {

    	$title  = '<span class="archive-type">' . __('Author', 'thrive-nouveau') . '</span>';
        $title .= '<span class="archive-title vcard">' . get_the_author( '', false ) . '</span>';

    } elseif ( is_tax() ) {

    	global $wp_query;

    	$tax_name = '';
    	$term = $wp_query->get_queried_object();

    	if (!empty($term)) {
    		$tax_name = $term->name;
    	}

   		$title  = '<span class="archive-type">' . __('Archive', 'thrive-nouveau') . '</span>';
        $title .= '<span class="archive-title vcard">' . esc_attr($tax_name) . '</span>';

    }

    return $title;

}

/**
 * Changes default BuddyDrive Label
 */
add_filter('buddydrive_get_name', 'thrive_buddydrive_set_name');

/**
 * Callback function to 'buddydrive_get_name' filter
 */
function thrive_buddydrive_set_name() {
	return  __('Files', 'thrive-nouveau');
}

add_filter('body_class', 'thrive_layout_body_class');

function thrive_layout_body_class( $classes_collection ) {

	$classes_collection[] = 'thrive-layout-' . thrive_customizer_radio_mod( 'thrive_layouts_customize', '2_columns' );

	return $classes_collection;

}

function thrive_layout_class( $section = '' ) {

	if ( empty( $section ) ) {
		return '';
	}

	$classes['sidenav'] = '';
	$classes['content'] = 'col-xs-12 full-content-layout';

	if ( ! array_key_exists( $section, $classes ) ) {
		return '';
	}

	return $classes[ $section ];
}

add_action( 'thrive_after_body', 'thrive_wisechat_support' );

function thrive_wisechat_support() {

    $wise_chat_pro_options = get_option( 'wise_chat_options_name' );
    $fb_mode = '';

	if ( function_exists('wise_chat') ) {

		if ( is_user_logged_in() ) {

			if ( isset( $wise_chat_pro_options['mode'] ) ) {
            	if ( 1 === intval( $wise_chat_pro_options['mode'] ) ) {
                	$fb_mode = 'fb-mode-enabled ';
            	}
            }

			do_action('before_wisechat_support');

			echo '<div id="thrive-wisechat-support" class="inactive has-wise-chat ' . $fb_mode . '">';
				echo '<div id="thrive-wisechat-support-close-btn">
						<span id="thrive-chat-icon">
							<i class="material-icons">chat</i>
							<em id="thrive-chat-icon-label">'.__('Chat', 'thrive-nouveau').'</em>
						</span>
						<span id="thrive-chat-label">
							<i class="material-icons">add</i>
						</span>
						<i id="thrive-wisechat-support-close-btn-icon" class="material-icons">close</i></div>';
						wise_chat();
			echo '</div>';

			do_action('after_wisechat_support');

		}

	}

	return;
}

/**
 * RTMedia Updates
 */

add_filter('bp_get_activity_show_filters_options', 'thrive_add_media_show_filter', 10, 2);

if ( ! function_exists('thrive_add_media_show_filter') ) {

	function thrive_add_media_show_filter( $filters, $context ) {

	    $filters['rtmedia_update'] = __('Media Updates', 'thrive-nouveau');

	    return $filters;

	}

}

/**
 * Branding Height
 */
add_action('thrive_branding_height', 'thrive_branding_height');

function thrive_branding_height() {

	$branding_height = 100;
	$thememod_branding_height = get_theme_mod( 'thrive_branding_height', false );

	if ( !empty( $thememod_branding_height ) ) {
		$branding_height = $thememod_branding_height;
	}

	echo sprintf( 'style="height: %dpx;"', intval( $branding_height ) );

	return;
}

/**
 * Customizer Radio Selector Wrapper
 */
function thrive_customizer_radio_mod( $mod_name = '', $default = '') {

	$theme_mod_value = get_theme_mod( $mod_name );

	if ( empty ( $theme_mod_value ) ) {

		return $default;

	}

	return $theme_mod_value;

}

add_filter('bp_members_widget_separator', 'thrive_groups_widget_separator');
add_filter('bp_groups_widget_separator', 'thrive_groups_widget_separator');

function thrive_groups_widget_separator() {
	return '&raquo;';
}

add_filter('ld_course_list', 'thrive_fix_ld_container_issue');

function thrive_fix_ld_container_issue( $output ){
	return '<div class="thrive-ld-courses-wrapper">' . $output . '</div>';
}

/**
 * Add new button for user to view his/her profile as different user
 */
add_action('thrive_bp_view_profile_as', 'thrive_bp_view_profile_as_cb');

function thrive_bp_view_profile_as_cb() {

	if ( ! function_exists('buddypress')) {
		return;
	}


	if ( ! function_exists('bp_is_user') ) {
		return;
	}

	if ( bp_is_user() ) {

		// Get the current viewed user's id.
		$current_viewed_user_id = absint(buddypress()->displayed_user->id);

		// Get the current logged-in user's id.
		$current_user_id = get_current_user_id();

		if ( $current_viewed_user_id === $current_user_id ) {
			?>
				<div id="item-nav-user-settings-btn">
					<a href="<?php echo esc_url( bp_loggedin_user_domain() ); ?>settings" class="button" title="<?php echo esc_attr__('Settings', 'thrive-nouveau'); ?>">
						<?php esc_attr_e('Settings', 'thrive-nouveau'); ?>
					</a>
				</div>
			<?php
		}
	}
	return;
}

function thrive_sanity_check( $mixed_data ) {
	if ( ! empty ( $mixed_data ) ) {
		return $mixed_data;
	}
	return "";
}


add_action('wp_head', 'thrive_render_customizer_css');

function thrive_render_customizer_css() {

	$css = get_theme_mod( 'thrive_user_define_css', '' );

	if ( ! empty( $css ) ) {
		echo thrive_sanity_check( '<style>'. $css . '</style>' );
	}

	return;

}

/**
 * Thrive document wrappers
 */

if ( ! function_exists('thrive_document_wrapper_additional_classes') ) {
	function thrive_document_wrapper_additional_classes( $classes ) {
		if ( isset( $_COOKIE['thrive-layout'] ) && '1-column' === $_COOKIE['thrive-layout'] )  {
			return $classes . ' active';
		}
	}
	add_filter('thrive-document-wrapper', 'thrive_document_wrapper_additional_classes');
}

/**
 * Change BuddyPress Profile Labels
 */
add_filter('bp_get_send_public_message_button', 'thrive_bp_get_send_public_message_button');

function thrive_bp_get_send_public_message_button( $args ) {
	$args['link_text'] = esc_html__('@Mention', 'thrive-nouveau');
	return $args;
}

add_filter('bp_get_send_message_button_args', 'thrive_bp_get_send_message_button_args');

function thrive_bp_get_send_message_button_args( $args ) {
	$args['link_text'] = esc_html__('Message', 'thrive-nouveau');
	return $args;
}

function thrive_bp_single_group_random_member( $group_id, $limit ) {

	global $wpdb;

	$stmt = $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}bp_groups_members
		WHERE group_id = %d ORDER BY rand() LIMIT %d", $group_id, $limit );

	$results = $wpdb->get_results( $stmt );

	if ( ! empty( $results ) ) {
		foreach( $results as $member ) {
			echo get_avatar( $member->user_id, 48 );
		}
	}

}

if ( ! function_exists('thrive_bp_check_is_user_online') ) {
	/**
	 * Check if the member is online or not.
	 *
	 * @param  integer $user_id The user id.
	 * @return mixed Negative One if BuddyPress is not installed otherwise,
	 *         boolean, true if the user is online otherwise, false.
	 */
	function thrive_bp_check_is_user_online( $user_id )
	{
		if ( function_exists( 'bp_has_members' ) ) {
			if ( bp_has_members( 'type=online&include='. absint( $user_id ) ) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return -1;
		}
	}
}

/**
 * Returns the numeric id of the given menu slug.
 * @param  string $menu_id The slug of the menu e.g. 'header'.
 * @return integer The menu id.
 */
function thrive_get_navigation_id( $menu_id = '' ) {
	$menu = wp_get_nav_menu_object( $menu_id );
	if ( ! empty ( $menu ) ) {
		return $menu->term_id;
	}
	return 0;
}

add_action( 'wp_ajax_thrive_dashboard_widgets_reposition', 'thrive_dashboard_widgets_reposition' );
add_action( 'wp_ajax_thrive_dashboard_widgets_reposition_reset', 'thrive_dashboard_widgets_reposition_reset' );

/**
 * Updates user meta record for widgets position
 * @return void
 */
function thrive_dashboard_widgets_reposition()
{

    if ( is_user_logged_in() )
    {
    	$user_id = get_current_user_id();
    	$requested_user_widget_position = filter_input( INPUT_POST, 'widgetsOrder', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
    	$requested_user_widget_position_packery = filter_input( INPUT_POST, 'packeryOrder', FILTER_DEFAULT );
    	update_user_meta( $user_id, 'thrive_user_dashboard_widget_position', $requested_user_widget_position);
    	update_user_meta( $user_id, 'thrive_user_dashboard_widget_position_packery', $requested_user_widget_position_packery);
    }

    wp_die();
}

function thrive_dashboard_widget_dynamic_sidebar( $index = 1 )
{

    global $wp_registered_sidebars, $wp_registered_widgets;

    if ( is_int( $index ) ) {
        $index = "sidebar-$index";
    } else {
        $index = sanitize_title( $index );
        foreach ( (array) $wp_registered_sidebars as $key => $value ) {
            if ( sanitize_title( $value['name'] ) == $index ) {
                $index = $key;
                break;
            }
        }
    }

    $sidebars_widgets = wp_get_sidebars_widgets();

    if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] )
    	|| ! is_array( $sidebars_widgets[ $index ] ) ) {
            /** This action is documented in wp-includes/widget.php */
            do_action( 'dynamic_sidebar_before', $index, false );
            /** This action is documented in wp-includes/widget.php */
            do_action( 'dynamic_sidebar_after',  $index, false );
            /** This filter is documented in wp-includes/widget.php */
            return apply_filters( 'dynamic_sidebar_has_widgets', false, $index );
    }

    do_action( 'dynamic_sidebar_before', $index, true );
    $sidebar = $wp_registered_sidebars[$index];

    $did_one = false;

    $user_dashboard_widget = (array)get_user_meta(get_current_user_id(), 'thrive_user_dashboard_widget_position');


    $final_widgets = array_unique(
    	array_merge(
    		array_shift( $user_dashboard_widget ),
    		(array) $sidebars_widgets[$index]
    	)
    );

    foreach ( $final_widgets as $id ) {

            if ( !isset($wp_registered_widgets[$id]) ) continue;

            $params = array_merge(
                    array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
                    (array) $wp_registered_widgets[$id]['params']
            );

            // Substitute HTML id and class attributes into before_widget
            $classname_ = '';

            foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn )
            {
                if ( is_string($cn) )
                        $classname_ .= '_' . $cn;
                elseif ( is_object($cn) )
                        $classname_ .= '_' . get_class($cn);
            }

            $classname_ = ltrim($classname_, '_');
            $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
            $params = apply_filters( 'dynamic_sidebar_params', $params );
            $callback = $wp_registered_widgets[$id]['callback'];

            do_action( 'dynamic_sidebar', $wp_registered_widgets[ $id ] );

            if ( is_callable($callback) ) {
                call_user_func_array($callback, $params);
                $did_one = true;
            }
    }

    do_action( 'dynamic_sidebar_after', $index, true );

    return apply_filters( 'dynamic_sidebar_has_widgets', $did_one, $index );
}

/**
 * Resets widgets position
 */

function thrive_dashboard_widgets_reposition_reset() {

	if ( is_user_logged_in() )
	{

		$user_id = get_current_user_id();

		$widgets_position = get_user_meta($user_id,
			'thrive_user_dashboard_widget_position');

		$widgets_position_packery = get_user_meta($user_id,
			'thrive_user_dashboard_widget_position_packery');

		if ( ! empty($widgets_position) && ! empty($widgets_position_packery) )
		{
			delete_user_meta( $user_id, 'thrive_user_dashboard_widget_position' );
			delete_user_meta( $user_id, 'thrive_user_dashboard_widget_position_packery' );
		}
	}

	$redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_SPECIAL_CHARS );

	if ( empty ( $redirect ) ) {
		$redirect = get_home_url();
	}


	wp_safe_redirect($redirect, 302);

	exit;

}

/**
 * This function returns the right sidebar for buddypress pages.
 * @return string the name of the sidebar
 */
function thrive_get_bp_sidebar(){

	$sidebar = esc_html__('BuddyPress', 'thrive-nouveau');

	// BuddyPress Profile Sidebar.
	if ( bp_is_user() ) {
		$sidebar = esc_html__('BuddyPress Profile', 'thrive-nouveau');
	}

	// BuddyPress Members Page Sidebar.
	if ( bp_is_members_directory() ) {
		$sidebar = esc_html__('BuddyPress Members', 'thrive-nouveau');
	}

	// BuddyPress Single Group Sidebar.
	if ( bp_is_group() ) {
		$sidebar = esc_html__('BuddyPress Single Group', 'thrive-nouveau');
	}

	// BuddyPress Groups Page Sidebar.
	if ( bp_is_groups_directory() ) {
		$sidebar = esc_html__('BuddyPress Groups', 'thrive-nouveau');
	}

	// Overwrite the sidebar when page meta is set.
	$id = bp_get_current_component_page_id();


	if ( ! empty( $id ) ) 
	{
		// Get the page layout.
		$page_layout = get_post_meta( $id, 'thrive_page_layout', true );
		// Get the sidebar.
		$get_sidebar = get_post_meta( $id, 'thrive_sidebar', true );
		
		if( ! empty ( $get_sidebar ) ) {
			$sidebar = $get_sidebar;
		}
		if ( "sidebar-content" === $page_layout ) {
			$get_sidebar = get_post_meta( $id, 'thrive_sidebar_left', true );
			if( ! empty ( $get_sidebar ) ) {
				$sidebar  = $get_sidebar;	
			}
			
		}
	}

	return $sidebar;
}

/**
 * Add no-header class to <body> when registration page has header disabled.
 */
add_action('body_class', 'thrive_no_header_class');

function thrive_no_header_class($classes) {
	if ( function_exists('bp_is_register_page') ) {
		if ( bp_is_register_page() ) {
			$is_header_disabled = get_theme_mod('thrive_register_disable_header', false);
			if ( $is_header_disabled ) {
				$classes[] = 'thrive-register-header-disabled';
			}
		}
	}

	return $classes;
}

/**
 * Overwrite RTMedia CSS Collisions
 */
add_action('body_class', 'thrive_fix_rtmedia');


function thrive_fix_rtmedia($classes) {
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( function_exists('rtmedia_autoloader') ) 
	{
		$classes[] = 'thrive-rt-media-active';
	}

	return $classes;
}

/**
 * Overwrite Gears Default Cover Photo Sizes
 */
 add_filter('gears_bcp_max_width', 'thrive_gears_bcp_max_width');
 function thrive_gears_bcp_max_width(){
	 return 1400;
 }

 add_filter('gears_bcp_max_height', 'thrive_gears_bcp_max_height');
 function thrive_gears_bcp_max_height(){
 	return 240;
 }

 add_filter('gears_bcp_thumb_max_width', 'thrive_gears_bcp_thumb_max_width');
 function thrive_gears_bcp_thumb_max_width(){
	 return 700;
 }

 add_filter('gears_bcp_thumb_max_height', 'thrive_gears_bcp_thumb_max_height');
 function thrive_gears_bcp_thumb_max_height(){
 	return 120;
 }
add_filter('gears_bcp_aspect_ratio', 'thrive_gears_aspect_ratio');

function thrive_gears_aspect_ratio(){
	return 35/6;
}

/**
 * Social link
 */
if ( ! function_exists('thrive_social_link(')) {
function thrive_social_link() {
    global $post;
    ?>
        <div class="entry-share">
            <span><?php esc_attr_e('SHARE', 'thrive-nouveau' ); ?></span>
            <ul>
                <li class="facebook-share">
                    <a id="thrive-nouveau-facebook-share" href="#" title="<?php esc_attr_e('Share on Facebook', 'thrive-nouveau');?>"></a>
                </li>
                <li class="twitter-share">
                    <a id="thrive-nouveau-twitter-share" href="#" title="<?php esc_attr_e('Share on Twitter', 'thrive-nouveau');?>"></a>
                </li>
                <li class="linkedin-share">
                    <a id="thrive-nouveau-linkedin-share" href="#" title="<?php esc_attr_e('Share on LinkedIn', 'thrive-nouveau');?>"></a>
                </li>
                <li class="google-plus-share">
                    <a id="thrive-nouveau-gplus-share" href="#" title="<?php esc_attr_e('Share on Google+', 'thrive-nouveau');?>"></a>
                </li>
                <li class="reddit-share">
                    <a id="thrive-nouveau-reddit-share" href="#" title="<?php esc_attr_e('Share on Reddit', 'thrive-nouveau');?>"></a>
                </li>
                <li class="whatsapp-share">
                    <a id="thrive-nouveau-whatsapp-share" href="#" title="<?php esc_attr_e('WhatsApp', 'thrive-nouveau');?>"></a>
                </li>
                <?php $mail_link = sprintf( "mailto:?&subject=%s&body=%s", esc_attr( get_the_title() ), get_the_permalink() ); ?>
                <li class="email-share">
                    <a id="thrive-nouveau-email-share" href="<?php echo esc_url( $mail_link ); ?>" title="<?php esc_attr_e('E-mail to friend', 'thrive-nouveau');?>"></a>
                </li>
            </ul>

            <?php if ( get_the_author_meta( 'user_url', $post->author_id ) ) {?>
                <div class="entry-website-link">
                    <span class="fa fa-external-link"></span>
                    <a href="<?php echo nl2br( get_the_author_meta( 'user_url', $post->author_id ) ); ?>">
                        <?php echo nl2br( get_the_author_meta( 'user_url', $post->author_id ) ); ?>
                    </a>
                </div>
            <?php } ?>

        </div>
    <?php
    return;
 }
 }
add_action('wp_enqueue_scripts', 'nouveau_fb_article_sharer');
function nouveau_fb_article_sharer() {
    if ( is_single() ) {
        $title = get_the_title();
        $permalink = get_the_permalink();
        $fb_sharer_url = sprintf("https://www.facebook.com/sharer/sharer.php?u=%s", $permalink );
        $tw_sharer_url = sprintf("http://twitter.com/share?text=%s&url=%s", esc_attr( $title ), $permalink );
        $li_sharer_url = sprintf("https://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s", $permalink, esc_attr( $title ) );
        $gp_sharer_url = sprintf("https://plus.google.com/share?url=%s", $permalink );
        $rd_sharer_url = sprintf("http://www.reddit.com/submit?url=%s&title=%s", $permalink, esc_attr( $title ) );
        $whatsapp_sharer_url = sprintf("https://api.whatsapp.com/send?text=%s", esc_attr( $title ) );
        // Localize the script with new data.
        $translation_array = array(
            'fb_sharer_url' => $fb_sharer_url,
            'tw_sharer_url' => $tw_sharer_url,
            'li_sharer_url' => $li_sharer_url,
            'gp_sharer_url' => $gp_sharer_url,
            'rd_sharer_url' => $rd_sharer_url,
            'whatsapp_sharer_url' => $whatsapp_sharer_url,
        );
        // Attach localisation to our main script.
        wp_localize_script( 'thrive-script', 'thrive_nouveau_sharer_js_vars', $translation_array );
        // Enqueued script with localized data.
        wp_enqueue_script( 'thrive_nouveau_sharer_js_vars' );
    }
    return;
}

add_action('body_class', 'thrive_add_archive_class');

function thrive_add_archive_class($class) {
	if ( is_category() || is_author() || is_tag() || is_date() ) {
		$class[] = 'wp-archives';
	}
	return $class;
}

/**
 * Show cart contents / total Ajax
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'thrive_woocommerce_header_add_to_cart_fragment' );

function thrive_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start();

	?>
	<a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'thrive-nouveau' ); ?>">
		<i class="material-icons">shopping_cart</i>
		<?php if (WC()->cart->get_cart_contents_count() >=1) { ?>
		<span class="thrive-user-nav-bubble">
			<?php echo WC()->cart->get_cart_contents_count(); ?>
		</span>
		<?php } ?>
		<span class="visible-xs">
			<?php esc_html_e('Shopping Cart', 'thrive-nouveau'); ?>
		</span>
	</a>
	<?php
	$fragments['a.cart-customlocation'] = ob_get_clean();
	return $fragments;
}

add_action('body_class', 'thrive_bp_user_has_vertical_nav');

function thrive_bp_user_has_vertical_nav( $classes ) {
	if ( function_exists( 'bp_is_user' ) ) {
		if ( bp_is_user() ) {
			$is_user_nav_vertical = bp_nouveau_get_appearance_settings('user_nav_display'); 
			if ( $is_user_nav_vertical ) {
				$classes[] = 'thrive-bp-user-is-vertical-nav';
			} else {
				$classes[] = 'thrive-bp-user-is-horizontal-nav';
			}
		}
	}
	return $classes;
}

/**
 * OEmbed
 */
add_filter( 'bp_embed_oembed_html', 'thrive_bp_embed_oembed_html', 10, 4 ); 

function thrive_bp_embed_oembed_html( $html, $url, $attr, $rawattr ) {
    // Wrap the embed with a <div> tag.
    $html = '<div class="embed-wrapper">' . str_replace( array('422', '563'), '278', $html ) . '</div>';
    return $html;
}

add_action( 'body_class', 'thrive_apply_grid_archive');

/**
 * Adds .thrive-grid-archive to body class to support grid view in archives.
 * @param  array $classes The classes.
 * @return array The classes.
 */
function thrive_apply_grid_archive( $classes ) {
	
	
	$is_wp_default_tax = is_tax() || is_category() || is_tag() || is_date() || is_author() || is_home();

	if ( $is_wp_default_tax ) {
		$classes[] = 'thrive-grid-archive';
	}

	$supported_taxonomies = apply_filters('thrive_apply_grid_archive_supported_taxonomies', 
		array('ld_course_category', 'ld_course_tag') );

	$supported_post_types = apply_filters('thrive_apply_grid_archive_supported_post_type', 
		array('sfwd-courses', 'ld_course_tag') );


	if ( is_tax( $supported_taxonomies ) ) {
		$classes[] = 'thrive-grid-archive';
	}

	if ( is_post_type_archive( $supported_post_types ) ) {
		$classes[] = 'thrive-grid-archive';
	}

	return $classes;

}

/**
 * Add [thrive-home-url] keyword to the menu
 * @return void
 */
add_filter('nav_menu_link_attributes', 'thrive_nav_menu_link_attributes', 10, 3);

function thrive_nav_menu_link_attributes( $attr, $items, $args ) {

	$home_url = str_replace(array('https://', 'http://'), '', get_home_url());

	$attr['href'] = str_replace('[thrive-home-url]', $home_url, $attr['href']);

	return $attr;
}

add_filter('nav_menu_css_class', 'thrive_nav_menu_css_class', 10, 3);

function thrive_nav_menu_css_class( $classes, $item, $args ) {

	global $wp;

	$user = wp_get_current_user();
	
	$actual_link = trailingslashit( get_home_url() ) . $wp->request;

	$home_url = str_replace(array('https://', 'http://'), '', get_home_url());

	$menu_url = str_replace('[thrive-home-url]', $home_url, $item->url);

	$final_url = str_replace('/me/', '/'.$user->display_name.'/', $menu_url);

	if ( trailingslashit( $actual_link )  == trailingslashit( $final_url )  ) 
	{
		$classes[] = 'current-menu-item';
	}

	return $classes;

}

function thrive_get_user_link( $user_id ) {

	if ( function_exists( 'bp_core_get_user_domain' ) ) 
	{
		return bp_core_get_user_domain( $user_id );
	}

	return $user_id . get_author_posts_url( $user_id );

}

// Registers a widget.
function thrive_widget( $widget ) 
{
	global $wp_widget_factory;

    $wp_widget_factory->register( $widget );
    
}


