<?php
// https://developer.wordpress.org/reference/hooks/wp_new_user_notification_email/
add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );
function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
  
	$wp_new_user_notification_email['subject'] = sprintf( 'Acme Co Web Portal Log In', $blogname, $user->user_login );
	$message = __( 'You are receiving this email and log in information because you have active project/projects with Acme Co. If you would like, you can check the status of your project/projects by logging into the client portal and clicking on "My Projects". After you log in, "My Projects" is on the left hand side just below the "Dashboard" button.' ) . "\r\n\r\n";
	$message .= __( 'Also available in the client portal is the following:' ) . "\r\n\r\n";
	$message .= __( 'You can upload files to a team leader' ) . "\r\n";
	$message .= __( 'You can ask a precast designer technical questions' ) . "\r\n";
	$message .= __( 'If you\'re a subscription user of our software you can access the Suite (Current offerings: Round, Square, Oval)' ) . "\r\n";
	$message .= __( 'If you\'re a subscription user of our software you can make payments via credit card' ) . "\r\n\r\n";
	$message .= __( '--' ) . "\r\n\r\n";
	$message .= sprintf(__( 'Your username: %s' ), $user->user_login ) . "\r\n";
	$message .= __( 'To set your password, visit the following address: <https://example.com/my-account/lost-password/>' ) . "\r\n\r\n";
	$message .= sprintf(__( 'If you have any problems, please contact us at test@example.com')) . "\r\n\r\n";
	$message .= __('https://example.com/my-account/');
	$wp_new_user_notification_email['message'] = $message;
  
	$wp_new_user_notification_email['headers'] = "From: Acme Co <test@example.com>";
  
	return $wp_new_user_notification_email;
  
}
