
/**
 * redirect user to a custom login page on login
 */
function quickfood_login_redirect( $redirect_to, $request, $user ){
  //var_dump($user);
  $login_dashboard = site_url().'/'.'dashboard.php';
  return ( is_array( $user->roles ) && in_array( 'customer', $user->roles ) ) ? $login_dashboard : admin_url();
}
add_filter( 'login_redirect', 'quickfood_login_redirect', 10, 3 );


//--- breadcrumbs
function get_post_breadcrumbs(){
 $bcrumbs = array(); $html = ''; 
 $swisssitechkr = multisite_globalizer();
 $cpost = get_post(get_the_ID()); 
 // if cpost is null, get the id from the url 
 if($cpost == NULL){ $cpid = $_GET['id']; $cpost = get_post($cpid); }

 //create crumbs based on current post 
 for($id=$cpost->ID; $id != 0; ){ 
   $ppost = get_post($id);
   if($ppost->post_type == "page" || $ppost->post_type == "nav_menu_item"){ 
     $menuItem = wp_get_nav_menu_items('main-menu', 
                 array('posts_per_page' => -1,'meta_key' => '_menu_item_object_id','meta_value' => $ppost->ID ));
     if(!empty($menuItem)){
       array_unshift($bcrumbs, $menuItem); $id = $menuItem[0]->menu_item_parent; 
     } else { 
       array_unshift($bcrumbs, array(0=>$ppost)); $id = $ppost->post_parent;
     }
   }
   else{
     array_unshift($bcrumbs, array(0=>$ppost));
     //get page for the posts for breadcrumbs
     if($ppost->post_type == 'job_listing'){ $id = get_option('job_manager_jobs_page_id'); } 
     if($ppost->post_type == 'event' || $ppost->post_type == 'upcomingevent'){ 
       $pg = get_page_by_path('upcoming-events'); $id = $pg->ID; } 
     if($ppost->post_type == 'news'){ $pg = get_page_by_path('news'); $id = $pg->ID; }
     if($ppost->post_type == 'investmentzone'){ $pg = get_page_by_path('investment-zone'); $id = $pg->ID; } 
   }
 }

 //return html form of the breadcrumbs
 if(!empty($bcrumbs)){ 
  $html .= '<div class="inner_bredcrumb"><nav aria-label="You are here:" role="navigation"><ul class="breadcrumbs page_bredcrunb">';
  if ( $swisssitechkr==1 ) { $html .= '<li><a href="'.home_url().'">Home</a></li>'; }
  if ( $swisssitechkr==2 ) { $html .= '<li><a href="'.home_url().'">Beijing</a></li>'; }
  if ( $swisssitechkr==3 ) { $html .= '<li><a href="'.home_url().'">Shanghai</a></li>'; }
  if ( $swisssitechkr==4 ) { $html .= '<li><a href="'.home_url().'">Guangzhou</a></li>'; }
  if ( $swisssitechkr==5 ) { $html .= '<li><a href="'.home_url().'">Hong Kong</a></li>'; } 
  
  foreach($bcrumbs as $crumb){ 
   $title = $crumb[0]->post_title; 
   if(empty($title)){ $title = $crumb[0]->title; }
   $html .= '<li><a href="'.$crumb[0]->url.'">'.$title.'</a></li>';
  }
                     
  $html .= '</ul></nav></div>'; 
 }
 return $html;
}
/***/



