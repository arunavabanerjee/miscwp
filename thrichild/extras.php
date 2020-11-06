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

/** ===========================================================================*
 *  Function for placing thumbnail metabox for feed import
 * ============================================================================*/
/** Add Metaboxes to WPRSS FEED SOURCE to display the default thumbnails.*/
add_action( 'post_edit_form_tag' , 'post_edit_form_tag' );
function post_edit_form_tag( ) {
  global $post; 
  $postType = get_post_type( $post->ID ); 
  //if post type == wprss_feed
  if( $postType == 'wprss_feed' ){ 
	echo ' enctype="multipart/form-data"';
  }
}
add_action( 'add_meta_boxes', 'wprss_feed_source_default_image' );
function wprss_feed_source_default_image(){ 
  add_meta_box('custom_admin_image_mbox', __( 'Default Image For Feed Source Items', WPRSS_TEXT_DOMAIN ),
               'wprss_show_admin_image_callback', 'wprss_feed', 'normal', 'low' ); 
}
function wprss_show_admin_image_callback(){
  global $post; $html = '';
  // display the uploader form 
  $html .= '<div><p><span><b>Select A Default Image: </b></span>';
  $html .= '<input type="file" name="wprss_defimg" id="wprss_defimg" /></p></div>';
  $selImageID = get_post_meta($post->ID, 'wprss_feedsource_imgdefault', true);
  if($selImageID != ''){ 
     $arr_existing_image = wp_get_attachment_image_src($selImageID, 'large');
     $existing_image_url = $arr_existing_image[0];
     $html .= '<div><img src="'.$existing_image_url.'" width="200" height="200" /></div>'; 
  } 
  echo $html;
}
add_action('save_post', 'wprss_save_default_image');
function wprss_save_default_image(){
  global $post; 
  $postType = $_POST['post_type']; $postAction = $_POST['action'];
  if( $postType == 'wprss_feed' && $postAction == 'editpost' ){ 
     if( !empty($_FILES["wprss_defimg"]) ){
	//upload file directly to uploads folder
	$upload_overrides = array( 'test_form' => false );
	$uploaded_file = wp_handle_upload( $_FILES["wprss_defimg"], $upload_overrides ); 
	if(isset($uploaded_file['file'])) { 
	   $wprss_defaultfile = $uploaded_file['file']; 
	   $wprss_default_title = 'Default Image For '.$_POST["post_title"];
	   $attachment = array(
		'post_mime_type' => $_FILES["wprss_defimg"]['type'],
                'post_title' => $wprss_default_title,
                'post_content' => '',
                'post_status' => 'inherit' );
           $attach_id = wp_insert_attachment( $attachment, $wprss_defaultfile );
           require_once(ABSPATH . "wp-admin" . '/includes/image.php');
           $attach_data = wp_generate_attachment_metadata( $attach_id, $wprss_defaultfile );
           wp_update_attachment_metadata($attach_id,  $attach_data);
           $wprss_prevImage = (int) get_post_meta($post->ID, 'wprss_feedsource_imgdefault', true);
           if(is_numeric($wprss_prevImage)) { wp_delete_attachment($wprss_prevImage); }
	   update_post_meta($post->ID,'wprss_feedsource_imgdefault',$attach_id);
	}
     }
  }
}


/** ===========================================================================*
 *  Function for placing weather information on user timeline
 * ============================================================================*/
add_action('bp_before_member_home_content', 'func_bp_before_member_home_content', 10); 
function func_bp_before_member_home_content(){ 
   $weatherapi_key = "00dfe2e70fa24f2ba0292245202408"; $html = '';
   $weatherapi_endpoint = 'http://api.weatherapi.com/v1/current.json';
   // check if user is logged in
   if(bp_is_active() && is_user_logged_in()){
	$loggedInUser = wp_get_current_user(); 
	$uroles = $loggedInUser->roles; //var_dump($uroles);
	//query BP to get the keys from user signup.
	if ( function_exists('buddypress')) { 
	   global $bp, $wpdb; 
	   $signups_table  = $bp->members->table_name_signups; 
           $sql = "SELECT * FROM {$signups_table} WHERE user_login ='".$loggedInUser->user_login."'"; 
	   $user_details = $wpdb->get_results( $sql, OBJECT ); 
	   if(empty($user_details)){ return; }
	   $umeta = unserialize($user_details[0]->meta); //var_dump($umeta); 
	   //get the postcode for the user. 
	   if(!array_key_exists("field_738", $umeta)){ return; }
	   $upostcode = $umeta["field_738"]; //var_dump($upostcode);
	   if( $upostcode != NULL && !empty($upostcode)){
	     //fetch results from the api
	     $weatherapi_url = $weatherapi_endpoint.'?key='.$weatherapi_key.'&q='.$upostcode;
	     try{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $weatherapi_url);
		curl_setopt($ch, CURLOPT_HEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json_result = curl_exec($ch); curl_close($ch); 
		//var_dump($json_result); //exit;
	     }catch(Exception $e){ //echo $e->getMessage(); 
		echo "*** Curl Error: ".$e->getMessage();
	     }
	     //get weatherinfo array
	     list($head, $body) = explode("\r\n\r\n", $json_result, 2); 
	     $weather_details = json_decode($body, true); //var_dump($weather_details); exit; 

	     //html widget 
	     $html .= '<div class="item-list-tabs no-ajax" id="weather-subnav" aria-label="Member weather widget" style="min-height:185px;">';
	     // header of the widget
	     $html .= '<div class="weather-widget-header" style="width:100%;text-align:center;background:#a5def2;color:#000;padding:5px;border-radius:4px;border:1px solid #4fc1e9;">';
	     $html .= '<h5 style="font-size:1.0em;">Location: '.$weather_details["location"]["name"].', '.$weather_details["location"]["region"].' - Current Weather</h5>';
	     $html .= '</div>';
	     // body of the widget
	     $html .= '<div class="weather-widget-body">';
	     // left element of the body segment 
	     $html .= '<div class="item-left" style="float:left;width:10%;background:#9cc1d0;border:1px solid #4fc1e9;text-align:center;color:#000;font-size:1.110em;padding:3px;">'; 
	     $html .= '<div class="condition-img"><img src="'.$weather_details["current"]["condition"]["icon"].'" width="90" height="90" /></div>';
	     $html .= '<div class="condition-text">'.$weather_details["current"]["condition"]["text"].'</div>'; 
	     $html .= '</div>';
	     // right element of the body segment
	     $html .= '<div class="item-right" style="width:90%;float:left;background:#8fcbe0;min-height:114px;padding:10px;color:#000;">';
	     // temperature div 
	     $html .= '<div class="current_temp" style="min-height:93px;width:20%;float:left;text-align:center;font-size:1.75em;border:2px solid #4fc1e9;">';
	     $html .= '<div class="current_temp_header" style="font-size:0.5em;background:#a5def2;padding:3px;border:1px solid #4fc1e9;"> Temp. ( &#8451 / &#8457 ) </div>';
	     $html .= '<div class="current_temp_body" style="padding:15px;">'.$weather_details["current"]["temp_c"].' / '.$weather_details["current"]["temp_f"].'</div>';
	     $html .= '</div>';
	     // humidity div 
	     $html .= '<div class="current_humidity" style="min-height:93px;width:20%;float:left;text-align:center;font-size:1.75em;border:2px solid #4fc1e9;">';
	     $html .= '<div class="current_humidity_header" style="font-size:0.5em;background:#a5def2;padding:3px;border:1px solid #4fc1e9;"> Humidity in % </div>';
	     $html .= '<div class="current_humidity_body" style="padding:15px;">'.$weather_details["current"]["humidity"].'</div>';
	     $html .= '</div>';
	     // windspeed div 
	     $html .= '<div class="current_windspeed" style="min-height:93px;width:20%;float:left;text-align:center;font-size:1.75em;border:2px solid #4fc1e9;">';
	     $html .= '<div class="current_windspeed_header" style="font-size:0.5em;background:#a5def2;padding:3px;border:1px solid #4fc1e9;"> Windspeed in kph </div>';
	     $html .= '<div class="current_windspeed_body">'; 
	     $html .= '<p style="margin:4px;font-size:0.7em;"><span style="font-size: 0.5em;"> Wind Direction : </span>'.$weather_details["current"]["wind_dir"].'</p>';
	     $html .= '<p><span style="font-size: 0.5em;"> Speed : </span>'.$weather_details["current"]["wind_mph"].'</p>';
	     $html .= '</div></div>';
	     // precipitation div 
	     $html .= '<div class="current_precipitation" style="min-height:93px;width:20%;float:left;text-align:center;font-size:1.75em;border:2px solid #4fc1e9;">';
	     $html .= '<div class="current_precipitation_header" style="font-size:0.5em;background:#a5def2;padding:3px;border:1px solid #4fc1e9;"> Precip. in inches </div>';
	     $html .= '<div class="current_precipitation_body" style="padding:15px;">'.$weather_details["current"]["precip_in"].'</div>';
	     $html .= '</div>';
	     // feels like 
	     $html .= '<div class="current_feelslike" style="min-height:93px;width:20%;float:left;text-align:center;font-size:1.75em;border:2px solid #4fc1e9;">';
	     $html .= '<div class="current_feelslike_header" style="font-size:0.5em;background:#a5def2;padding:3px;border:1px solid #4fc1e9;"> Feels Like ( &#8451 / &#8457 ) </div>';
	     $html .= '<div class="current_precipitation_body" style="padding:15px;">'.$weather_details["current"]["feelslike_c"].' / '.$weather_details["current"]["feelslike_f"].'</div>';
	     $html .= '</div>';

	     $html .= '</div></div>';	
	     $html .= '</div>';
	     echo $html; 

	   }// endif $upostcode != NULL
	} // end function buddypress
   }// end if bp_is_active
}


/** ===========================================================================*
 * Function for hitting python api, when user navigates to the menu 
 * ============================================================================*/
add_action( 'yz_add_new_profile_tabs', 'func_yz_add_new_profile_tabs', 10 );

function func_yz_add_new_profile_tabs(){
    global $bp;
    $args = array(
            'name' => __('Workout Logger', 'buddypress'),
            'slug' => 'workout-dashboard',
            'default_subnav_slug' => 'workout-dashboard',
            'position' => 50,
            'show_for_displayed_user' => false,
            'screen_function' => 'bp_custom_user_nav_item_screen',
            'item_css_id' => 'wdashboard'
    );
    bp_core_new_nav_item( $args );
}
function bp_custom_user_nav_item_screen() {

    require_once YZ_PUBLIC_CORE . 'tabs/yz-custom-tabs.php';

    $custom = new YZ_Custom_Tabs();

    add_action( 'bp_template_content', 'bp_custom_screen_content' );
    //add_action( 'bp_template_content', array( $custom, 'tab' ) );

    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_custom_screen_content() { 
   $registered_user = wp_get_current_user(); 

   // workout new api
   $checkuser_api = 'http://142.93.242.62/users/check/';
   $registration_api = 'http://142.93.242.62/users/register/';  
   $apikey_api = 'http://142.93.242.62/users/api/token/'; 
   $refresh_api = 'http://142.93.242.62/users/api/refresh/';
   //demo data api 
   $demoworkout_api = 'http://142.93.242.62/api/demo-workout-plan/';
   $demonutrition_api = 'http://142.93.242.62/api/demo-nutri-plan/';
   $demoweight_api = 'http://142.93.242.62/api/demo-weight-plan/'; 
   //exercise
   $exercise_api = 'http://142.93.242.62/api/exercise/';

   //check if wger access key exists for the user 
   $wger_refresh_key = get_user_meta($registered_user->ID, 'wger-refresh-key', true);
   $wger_access_key = get_user_meta($registered_user->ID, 'wger-access-key', true); 
   $wger_access_key_mtime = get_user_meta($registered_user->ID, 'wger-access-key-mtime', true);   

   if( $wger_refresh_key == NULL || empty($wger_refresh_key) ){ 
	//check if user exists in the system 
   	$apiurl = $checkuser_api; $apiurl .= $registered_user->user_login; 
   	$s_time = microtime(true);
   	$ch = curl_init(); 
   	curl_setopt($ch, CURLOPT_URL, $apiurl);
   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   	// Set HTTP Header for GET request 
   	curl_setopt($ch, CURLOPT_HTTPHEADER, 
     			array('Accept: application/json',));
   	$result = curl_exec($ch); //var_dump($result);
   	if( $result === false ){ echo curl_error($ch); exit; } 
   	$ustatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
   	curl_close($ch); $checkuser_array = json_decode($result, true); 
   	//var_dump($ustatusCode); var_dump($checkuser_array); exit; 

	// if user exists in the systemm regenerate all keys 
	// regenerate the keys as they do not exist
	if($ustatusCode == 200){ 
	   //generate user registration key 
   	   $apiurl = $apikey_api;
   	   $api_data =  array( 'username' => $registered_user->user_login,
			       'password' => 'cn@#123CN' );
   	   $api_postStr = json_encode( $api_data ); 
   	   //generate api access key and refresh key using post data 
   	   $s_time = microtime(true);
   	   $ch = curl_init($apiurl); 
   	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   	   curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
   	   //curl_setopt($ch, CURLOPT_FAILONERROR, true); 
   	   curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   	   curl_setopt($ch, CURLOPT_POST, true);
   	   curl_setopt($ch, CURLOPT_POSTFIELDS, $api_postStr);
   	   // Set HTTP Header for POST request 
   	   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      	   	   'Content-Type: application/json',
      	   	   'Content-Length: ' . strlen($api_postStr), 
		   'Accept: application/json', ));
   	   $result = curl_exec($ch); //var_dump($result); 
	   if( $result === false ){ echo curl_error($ch); exit; } 
   	   $accesskeysstatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
   	   curl_close($ch); $e_time = microtime(true); $elapsed = $e_time - $s_time; 
   	   //echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
	   $apikey_result = json_decode($result, true); //var_dump($apikey_result); exit;
	   //update db of the user
	   update_user_meta($registered_user->ID, 'wger-refresh-key', $apikey_result["refresh"]);
	   update_user_meta($registered_user->ID, 'wger-access-key', $apikey_result["access"]);	
	   update_user_meta($registered_user->ID, 'wger-access-key-mtime', time());

	}elseif($ustatusCode == 404) { 
		//register and generate refresh key and api key
		echo 'Registering New User For Workout Logger';
		$apiurl = $registration_api;
   		$api_data =  array( 'first_name' => get_user_meta($registered_user->ID, 'first_name', true), 
				'last_name' => get_user_meta($registered_user->ID, 'last_name', true),
				'email' => $registered_user->user_email, 
				'username' => $registered_user->user_login,
				'password' => 'cn@#123CN' ); 
   		$api_postStr = json_encode( $api_data ); 
   		//generate user in the Workout API using post data 
   		$s_time = microtime(true);
   		$ch = curl_init($apiurl); 
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   		curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
   		//curl_setopt($ch, CURLOPT_FAILONERROR, true); 
   		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   		curl_setopt($ch, CURLOPT_POST, true);
   		curl_setopt($ch, CURLOPT_POSTFIELDS, $api_postStr);
   		// Set HTTP Header for POST request 
   		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      				'Content-Type: application/json',
      				'Content-Length: ' . strlen($api_postStr),
				'Accept: application/json', ));
   		$result = curl_exec($ch); //var_dump($result); 
   		if( $result === false ){ echo curl_error($ch); exit; } 
   		$regstatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
   		curl_close($ch); 
		echo 'User Registered - Fetching Access Key and Refresh Key'; 

	   	//generate user registration key 
   	   	$apiurl = $apikey_api;
   	   	$api_data =  array( 'username' => $registered_user->user_login,
			       'password' => 'cn@#123CN' );
   	   	$api_postStr = json_encode( $api_data ); 
   	   	//generate api access key and refresh key using post data 
   	   	$s_time = microtime(true);
   	   	$ch = curl_init($apiurl); 
   	   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   	   	curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
   	   	//curl_setopt($ch, CURLOPT_FAILONERROR, true); 
   	   	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   	   	curl_setopt($ch, CURLOPT_POST, true);
   	   	curl_setopt($ch, CURLOPT_POSTFIELDS, $api_postStr);
   	   	// Set HTTP Header for POST request 
   	   	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      	   		'Content-Type: application/json',
      	   	   	'Content-Length: ' . strlen($api_postStr), 
		   	'Accept: application/json', ));
   	   	$result = curl_exec($ch); //var_dump($result); 
	   	if( $result === false ){ echo curl_error($ch); exit; } 
   	   	$accesskeysstatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
   	   	curl_close($ch); $e_time = microtime(true); $elapsed = $e_time - $s_time; 
   	   	//echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
	   	$apikey_result = json_decode($result, true); //var_dump($apikey_result); exit;
	   	//update db of the user
	   	update_user_meta($registered_user->ID, 'wger-refresh-key', $apikey_result["refresh"]);
	   	update_user_meta($registered_user->ID, 'wger-access-key', $apikey_result["access"]);	
	   	update_user_meta($registered_user->ID, 'wger-access-key-mtime', time());

	} // end if-elseif 

   }else{ // if wger_refresh_key exists, check time before refreshing
	   //generate user registration key 
   	   $apiurl = $apikey_api;
   	   $api_data =  array( 'username' => $registered_user->user_login,
			       'password' => 'cn@#123CN' );
   	   $api_postStr = json_encode( $api_data ); 
   	   //generate api access key and refresh key using post data 
   	   $s_time = microtime(true);
   	   $ch = curl_init($apiurl); 
   	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   	   curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
   	   //curl_setopt($ch, CURLOPT_FAILONERROR, true); 
   	   curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   	   curl_setopt($ch, CURLOPT_POST, true);
   	   curl_setopt($ch, CURLOPT_POSTFIELDS, $api_postStr);
   	   // Set HTTP Header for POST request 
   	   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      	   	   'Content-Type: application/json',
      	   	   'Content-Length: ' . strlen($api_postStr), 
		   'Accept: application/json', ));
   	   $result = curl_exec($ch); //var_dump($result); 
	   if( $result === false ){ echo curl_error($ch); exit; } 
   	   $accesskeysstatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
   	   curl_close($ch); $e_time = microtime(true); $elapsed = $e_time - $s_time; 
   	   //echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
	   $apikey_result = json_decode($result, true); //var_dump($apikey_result); exit;
	   //update db of the user
	   update_user_meta($registered_user->ID, 'wger-refresh-key', $apikey_result["refresh"]);
	   update_user_meta($registered_user->ID, 'wger-access-key', $apikey_result["access"]);	
	   update_user_meta($registered_user->ID, 'wger-access-key-mtime', time());
   }

    /** check for demo workouts and ntrition for ne users */
    //check for workouts 
    $apiurl = $demoworkout_api;
    $token = get_user_meta($registered_user->ID, 'wger-access-key', true);
    $s_time = microtime(true);
    $ch = curl_init($apiurl); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
    //curl_setopt($ch, CURLOPT_FAILONERROR, true); 
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   'Accept: application/json',
      	   'Content-Type: application/json',
	   "Authorization: Bearer $token", )
    );
    $result = curl_exec($ch); //var_dump($result); 
    if( $result === false ){ echo curl_error($ch); } 
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); $e_time = microtime(true); $elapsed = $e_time - $s_time; 
    //echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
    $demoworkout_result = json_decode($result, true); //var_dump($demoworkout_result);
 
    //check for nutritional plans 
    $apiurl = $demonutrition_api;
    $token = get_user_meta($registered_user->ID, 'wger-access-key', true);
    $s_time = microtime(true);
    $ch = curl_init($apiurl); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
    //curl_setopt($ch, CURLOPT_FAILONERROR, true); 
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   'Accept: application/json',
      	   'Content-Type: application/json',
	   "Authorization: Bearer $token", )
    );
    $result = curl_exec($ch); //var_dump($result); 
    if( $result === false ){ echo curl_error($ch); } 
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); 
    $e_time = microtime(true); $elapsed = $e_time - $s_time; 
    //echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
    $demonutrition_result = json_decode($result, true); //var_dump($demonutrition_result);  

    //fetch exercises result 
    $apiurl = $exercise_api;
    $token = get_user_meta($registered_user->ID, 'wger-access-key', true);
    $s_time = microtime(true);
    $ch = curl_init($apiurl); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
    //curl_setopt($ch, CURLOPT_FAILONERROR, true); 
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   'Accept: application/json',
      	   'Content-Type: application/json',
	   "Authorization: Bearer $token", )
    );
    $result = curl_exec($ch); //var_dump($result); 
    if( $result === false ){ echo curl_error($ch); } 
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); 
    $e_time = microtime(true); $elapsed = $e_time - $s_time; 
    //echo "Time Elapsed: ".$elapsed; //var_dump( $result ); exit; 
    $exercises_result = json_decode($result, true); //var_dump($exercises_result);    	

    /**
     * If workouts, nutrition and weights as demo are present display. 
     */ 
    if( !empty($demoworkout_result) && !empty($demonutrition_result) ){
	echo '<div class="exercises-title" '; 
	echo 'style="padding:5px 12px;border:1px solid #00F;margin:7px 0px;border-radius:8px;font-size:12px;">';
	echo '<h2> Workout Dashboard (Sample) </h2></div>';
	echo '<div class="row">';
	//workout plan
	echo '<div class="col-md-4 col-xs-12" style="background:#DDDEFD;margin:0px 10px 20px 10px;border: 2px solid #BBB;">';
	echo '<div class="row" style="padding:7px 7px;border-bottom:1px groove #AAA;background:#CECCCC;">';
	echo '<div class="tbl-top"><h4>Workout</h4></div></div>';
	foreach($demoworkout_result["workout_days"] as $workoutday){ 
	  echo '<div class="row" style="text-align:center;padding:10px;border-bottom: 1px groove #000;font-size:15px;">';
	  echo '<div class="col-md-2 col-xs-12 tbl-right">'.$workoutday[0].'</div>';
	  echo '<div class="col-md-10 col-xs-12 tbl-left" style="text-align:right;">'.$workoutday[1].'</div>';
	  echo '</div>';
	}
	echo '</div>';
	//nutrition plan
	echo '<div class="col-md-4 col-xs-12" style="background:#DDDDDD;margin:0px 10px 20px 5px;border: 2px solid #BBB;">';
	echo '<div class="row" style="padding:7px 7px;border-bottom:1px groove #AAA;background:#CECCCC;">';
	echo '<div class="tbl-top"><h4>Nutrition Plan</h4></div></div>';
	foreach($demonutrition_result['total'] as $key => $demo_nutriresult){ 
         if($key == 'energy'){ 
	   echo '<div class="row" style="text-align:center;padding:10px;border-bottom: 1px groove #000;font-size:15px;">';
	   echo '<div class="col-md-2 col-xs-12 tbl-right">'.ucfirst($key).'</div>';
	   echo '<div class="col-md-10 col-xs-12 tbl-left" style="text-align:right;">'.$demo_nutriresult.' kcal</div>';
	   echo '</div>';
	 }else{
	   echo '<div class="row" style="text-align:center;padding:10px;border-bottom: 1px groove #000;font-size:15px;">';
	   echo '<div class="col-md-2 col-xs-12 tbl-right">'.ucfirst($key).'</div>';
	   echo '<div class="col-md-10 col-xs-12 tbl-left" style="text-align:right;">'.$demo_nutriresult.' g</div>';
	   echo '</div>';
	 }
	}
	echo '</div>';
	// measurements plan
	echo '<div class="col-md-3 col-xs-12" style="background:#CCC;margin:0px 10px 20px 5px;border: 2px solid #BBB;">';
	echo '<div class="row" style="padding:7px 7px;border-bottom:1px groove #AAA;background:#CECCCC;">';
	echo '<div class="tbl-top"><h4>Measurements</h4></div></div>';
	echo '</div>';
	echo '</div><!-- end row -->'; 
	echo '<div class="row">';
	echo '<div class="col-md-4 col-xs-12" style="margin:-42px 10px 20px 10px;"><button type="button" style="background:#4fc1e9;color:#FFF;">Sample Workout - Add One</button></div>';
	echo '<div class="col-md-4 col-xs-12" style="margin:0px 10px 20px 5px;"><button type="button" style="background:#4fc1e9;color:#FFF;">Sample Nutrition - Add One</button></div>';
	echo '<div class="col-md-3 col-xs-12" style="margin:0px 10px 20px 5px;"></div>';
	echo '</div><!-- end row -->';
    } //end if( !empty($demoworkout_result) && !empty($demonutrition_result) ) 

    if(!empty($exercises_result)){
	echo '<div class="exercises-title" '; 
	echo 'style="background: #4fc1e9;padding: 7px;border: 1px solid #00F;margin: 7px;border-radius: 8px;font-size:12px;">';
	echo '<h2> Strengthing Exercises In Workout Logger </h2></div>';

	/*$i=1; foreach($exercise_list['results'] as $exercise){ */
	$i=1; foreach($exercises_result as $exercise){ 
    	  echo '<div class="exercises-det"'; 
	  if($i%2 ==0){
    	    echo 'style="padding: 20px;background: #F1F1F5;margin: 10px;color: #373535;font-size: 13px;';
    	    echo 'border-radius: 5px;broder: 1px solid #DDD;border: 1px solid #999DDD;min-height: 90px;overflow:hidden">'; 
	  }else{
    	    echo 'style="padding: 20px;background: #E9E5E5;margin: 10px;color: #373535;font-size: 13px;';
    	    echo 'border-radius: 5px;broder: 1px solid #DDD;border: 1px solid #999DDD;min-height: 90px;overflow:hidden">'; 
	  }
	  echo '<div style="float: left;width: 30%;padding: 15px;">';
    	  echo '<h3 style="font-size: 14px;">'.$exercise['name'].'</h3></div>';

	  echo '<div style="float: left;width: 70%;padding-left: 10px; border-left:1px solid #000;">';
    	  echo '<strong>Description: </strong><br/>'.$exercise['description'].'<br/>';
	  if(!empty( $exercise['muscles'] )){ $muscles = '';
	  	foreach( $exercise['muscles'] as $muscle){ $muscles .= $muscle['name']; }
    	  	echo '<strong>Muscles: </strong>'.$muscles.'<br/>'; 
	  }
	  if(!empty( $exercise["muscles_secondary"] )){ $secmuscles = '';
	  	foreach( $exercise["muscles_secondary"] as $muscle){ $secmuscles .= $muscle['name']; }
    	  	echo '<strong>Muscles: </strong>'.$secmuscles.'<br/>';
	  }
	  echo '</div>';
	  echo '</div>';
	  $i++; 
	  if($i == 20){ break; }
	}
     }

}




/** ===========================================================================*
 *  Function for placing thumbnails to posts imported via RSS Aggragator
 * ============================================================================*/
/** will fire when a post is submitted */
//add_action('wp_insert_post', 'rss_aggregator_importpost_update', 10, 3);
function rss_aggregator_importpost_update($post_id, $post, $update){ 
   set_time_limit(3600);    
   $logfile = get_home_path().'rss_postimg_importer.log'; 
   //check the feed has been updated or inserted by admin
   $post_type = get_post_type($post_id);
   if( $post_type == 'wprss_feed' ){ 
	error_log('==============================================='.PHP_EOL, 3, $logfile);
	error_log(" Checking Items For FeedSources: ".$post->post_title.PHP_EOL, 3, $logfile);
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
	   if( empty($updatedOn) ){ $updateImg = 1; }
	   else{ 
	     $updatedDt = date('Y-m-d', $updatedOn); 
	     $datetomatch = date('Y-m-d', $updatedDt.' +1 month'); 
	     if($updatedDt < $datetomatch){ $updateImg = 1; }
	   }
	   // if update is 0
	   if($updateImg == 0){ 
	      error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
	      error_log(" FeedItem: ".$feedItemID." Already Updated On: ".$updatedDt.PHP_EOL, 3, $logfile);
  	      error_log('-----------------------------------------------'.PHP_EOL, 3, $logfile);
	      $updateImg = 0; continue; 
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
	    	catch(Exception $e){ //echo $e->getMessage();
		    error_log(" Error On DocImgs: ".$e->getMessage().PHP_EOL, 3, $logfile);
		    update_post_meta($feedItemID, 'rssfeed_image_update', time());  
		    $updateImg = 0; continue; 
		}
		error_log(" No. Of Images:".$docImgs->length.PHP_EOL, 3, $logfile);
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
		   }catch(Exception $e){ //echo $e->getMessage(); 
		        error_log(" Curl Error: ".$e->getMessage().PHP_EOL, 3, $logfile);
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
		   error_log(" Error: ".$upload['error'].PHP_EOL, 3, $logfile);
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
}


/** ==============================================================================*
 *  create a user role named "coaches", that will be created by admin
 *  these users will be able to login from the frontend and see their dashboards
 * ===============================================================================*/
$awp_capabilities = get_role('contributor')->capabilities;
//$ext_capabilities = array( 'read_listing' => true, 'edit_listing' => true, 'create_listings' => true,
//        		'edit_listings' => true, 'edit_published_listings' => true, 'publish_listings' => true, );
//$coaches_capabilities = array_merge($awp_capabilities, $ext_capabilities);
$coaches_capabilities = $awp_capabilities;
add_role( 'coach', esc_html__( 'Coach', 'thrive-nouveau' ), $coaches_capabilities ); 


/** ==============================================================================*
 *  check if the user has mentioned yes for a coach
 *  this will fire after signup 
 * ===============================================================================*/
add_action( 'bp_core_signup_user' , 'func_bp_core_signup_user', 10, 5 );
function func_bp_core_signup_user($user_id, $user_login, $user_password, $user_email, $usermeta){
  //after user signup check whether coach is set to true
  $args = array( 'field' => 'Are You A Coach', 'user_id' => $user_id);  
  $_xprofile_coach_yes_no = bp_get_profile_field_data($args); 
  if( $_xprofile_coach_yes_no == 'Yes'){ 
	//change user role to coach 
	$wp_user = get_user_by('ID', $user_id); 
	$wp_user->remove_role('subscriber'); 
	$wp_user->add_role('coach');
  }
}

/** ==============================================================================*
 *  check if the user has mentioned yes for a coach
 *  this will fire after activation
 * ===============================================================================*/
add_action( 'bp_core_activated_user' , 'func_bp_core_activated_user', 10, 3 );
function func_bp_core_activated_user($user_id, $key, $user){
 //after user signup check whether coach is set to true
  $args = array( 'field' => 'Are You A Coach', 'user_id' => $user_id);  
  $_xprofile_coach_yes_no = bp_get_profile_field_data($args); 
  if( $_xprofile_coach_yes_no == 'Yes'){ 
	//change user role to coach 
	$wp_user = get_user_by('ID', $user_id); 
	$wp_user->remove_role('subscriber'); 
	$wp_user->add_role('coach');
  }
}

/** ===========================================================================*
 * Function for creating a page "Find A Coach" 
 * ============================================================================*/
add_action( 'yz_add_new_profile_tabs', 'func_yz_add_new_profile_tabs_find_coach', 10 );
function func_yz_add_new_profile_tabs_find_coach(){
    global $bp;
    $args = array(
	'name' => __('Find A Coach', 'thrive-nouveau'),
        'slug' => 'find-a-coach',
        'default_subnav_slug' => 'find-a-coach',
        'position' => 55,
        'show_for_displayed_user' => false,
        'screen_function' => 'bp_custom_user_nav_item_find_coach_screen',
        'item_css_id' => 'wdashboard'
    );
    bp_core_new_nav_item( $args );
}
function bp_custom_user_nav_item_find_coach_screen() {
    add_action( 'bp_template_content', 'bp_custom_screen_content_filter_coach' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) ); 
}
function bp_custom_screen_content_filter_coach() { 
   //$registered_user = wp_get_current_user(); 
   //echo $registered_user->user_email . '<br/>'; 
   //bp_get_template_part( 'buddypress/members/single/find-a-coach' ); 
   //if( count($_POST) > 0){ var_dump($_POST); exit;
   //}
   echo '<div class="filter-coach">';
   echo '<select name="filter_by_sport" style="float:right;">';
   echo '<option value="">FilterBy Sport</option>';
   echo '<option value="running">Running</option>';
   echo '<option value="cycling">Cycling</option>';
   echo '<option value="powerlifting">PowerLifting</option>';
   echo '<option value="weightlifting">WeightLifting</option>';
   echo '</select>'; 
   echo '<style> .nice-select{float: right;margin: 3px 13px;} </style>';
   echo '</div>'; 

   //get all coaches 
   $args = array('role' => 'coach', 'orderby' => 'date', 'order' => 'DESC');
   $user_query = new WP_User_Query( $args ); //var_dump($user_query); 
   echo '<div class="coach-title" '; 
   echo 'style="background: #4fc1e9;padding: 10px;border: 1px solid #00F;margin: 7px;border-radius: 8px;">';
   echo '<h2> Coaches In Maximon Global </h2></div>';
   // coach header
   echo '<div class="coach-header" style="width: 100%;border: 1px solid #ECECEC;height: 27px;margin: 0 8px;">'; 
   echo '<div style="float:left;width:13%;">';
   echo '<h5 style="background: #F0EEEE;border-radius: 3px;text-align: center;font-size: 13px;">Name</h5></div>';
   echo '<div style="float: left;width:35%;">';
   echo '<h5 style="background: #F0EEEE;border-radius: 3px;text-align: left;font-size: 13px;padding-left:20px;">Email</h5></div>';
   echo '<div style="float: left;width:15%;">';
   echo '<h5 style="background: #F0EEEE;border-radius: 3px;text-align: left;font-size: 13px;padding-left:13px;">Main Sport</h5></div>';
   echo '<div style="float: left;width: 35%;">';
   echo '<h5 style="background: #F0EEEE;border-radius: 3px;text-align: left;font-size: 13px;padding-left:20px;">Sec Sports</h5></div>';
   echo '</div>';
   // coach listings 
   $i=1; foreach($user_query->results as $coach){
    	echo '<div class="coach-det"'; 
	if($i%2 ==0){
    	  echo 'style="padding: 5px;background: #F1F1F5;margin: 10px;color: #373535;font-size: 13px;';
    	  echo 'border-radius: 5px;broder: 1px solid #DDD;border: 1px solid #999DDD;min-height: 90px;overflow:hidden">'; 
	}else{
    	  echo 'style="padding: 5px;background: #E9E5E5;margin: 10px;color: #373535;font-size: 13px;';
    	  echo 'border-radius: 5px;broder: 1px solid #DDD;border: 1px solid #999DDD;min-height: 90px;overflow:hidden">'; 
	} 
	//get user details from user - full name
	$fname = get_user_meta($coach->ID, 'first_name', true); 
	$lname = get_user_meta($coach->ID, 'last_name', true); 	
	$coach_full_name = $fname.' '.$lname; 
	echo '<div style="float:left;width:13%;padding:15px;">';
	echo bp_core_fetch_avatar(array('item_id' => $coach->ID, 'type' => 'full')); 
    	echo '<h3 style="font-size: 12px;">'.$coach_full_name.'</h3>';
	echo '</div>';

	//get email
	echo '<div style="float: left;width:35%;padding: 15px;">';
    	echo '<h3 style="font-size: 12px;">'.$coach->user_email.'</h3>';
	echo '</div>';

	//get xprofile prsport 
	$args = array( 'field' => 'Primary Sport', 'user_id' => $coach->ID);  
	$_xprofile_prsport = bp_get_profile_field_data($args); 
	echo '<div style="float: left;width:15%;padding: 15px;">';
    	echo '<h3 style="font-size: 13px;">';
	if( is_string($_xprofile_prsport) && !empty($_xprofile_prsport) ){ echo $_xprofile_prsport; }
	elseif( is_array($_xprofile_prsport) && !empty($_xprofile_prsport) ){ echo implode(',' , $_xprofile_prsport); }
	echo '</h3></div>';

	//get xprofile sec sport 
	$args = array( 'field' => 'Secondary Sports', 'user_id' => $coach->ID);  
	$_xprofile_secsport = bp_get_profile_field_data($args);	
	echo '<div style="float: left;width: 35%;padding: 15px;">';
    	echo '<h3 style="font-size: 13px;">'; 
	if( is_string($_xprofile_secsport) && !empty($_xprofile_secsport) ){ echo $_xprofile_secsport; }
	elseif( is_array($_xprofile_secsport) && !empty($_xprofile_secsport) ){ echo implode(',' , $_xprofile_secsport); }
	echo '</h3></div>';
	echo '</div>'; 
	$i++;
   }
   //echo '<script>';
   //echo '</script>'; 
}
