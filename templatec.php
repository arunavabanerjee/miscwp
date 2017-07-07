
//--- functions.php
/**
 * redirect user to a custom login page on login
 */
function quickfood_login_redirect( $redirect_to, $request, $user ){
  //var_dump($user);
  $login_dashboard = site_url().'/'.'dashboard.php';
  return ( is_array( $user->roles ) && in_array( 'customer', $user->roles ) ) ? $login_dashboard : admin_url();
}
add_filter( 'login_redirect', 'quickfood_login_redirect', 10, 3 );

