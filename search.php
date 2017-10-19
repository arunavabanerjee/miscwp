
 ["query_vars"]=> array(64) { 
 ["numberposts"]=> int(-1) ["category"]=> int(0) 
 ["orderby"]=> string(4) "date" ["order"]=> string(4) "DESC" ["include"]=> array(0) { } 
 ["exclude"]=> array(0) { } ["meta_key"]=> string(0) "" ["meta_value"]=> string(0) "" 
 ["post_type"]=> string(4) "cptm" ["suppress_filters"]=> bool(false) 
 ["post_status"]=> string(7) "publish" ["posts_per_page"]=> int(-1) 
 ["ignore_sticky_posts"]=> bool(true) ["no_found_rows"]=> bool(true) ["error"]=> string(0) "" 
 ["m"]=> string(0) "" ["p"]=> int(0) ["post_parent"]=> string(0) "" ["subpost"]=> string(0) "" 
 ["subpost_id"]=> string(0) "" ["attachment"]=> string(0) "" ["attachment_id"]=> int(0) 
 ["name"]=> string(0) "" ["static"]=> string(0) "" ["pagename"]=> string(0) "" ["page_id"]=> int(0) 
 ["second"]=> string(0) "" ["minute"]=> string(0) "" ["hour"]=> string(0) "" ["day"]=> int(0) 
 ["monthnum"]=> int(0) ["year"]=> int(0) ["w"]=> int(0) ["category_name"]=> string(0) "" 
 ["tag"]=> string(0) "" ["cat"]=> string(0) "" ["tag_id"]=> string(0) "" ["author"]=> string(0) "" 
 ["author_name"]=> string(0) "" ["feed"]=> string(0) "" ["tb"]=> string(0) "" ["paged"]=> int(0) 
 ["preview"]=> string(0) "" ["s"]=> string(0) "" ["sentence"]=> string(0) "" ["title"]=> string(0) "" 
 ["fields"]=> string(0) "" ["menu_order"]=> string(0) "" ["embed"]=> string(0) "" ["category__in"]=> array(0) { } 
 ["category__not_in"]=> array(0) { } ["category__and"]=> array(0) { } ["post__in"]=> array(0) { } 
 ["post__not_in"]=> array(0) { } ["post_name__in"]=> array(0) { } ["tag__in"]=> array(0) { } 
 ["tag__not_in"]=> array(0) { } ["tag__and"]=> array(0) { } ["tag_slug__in"]=> array(0) { } 
 ["tag_slug__and"]=> array(0) { } ["post_parent__in"]=> array(0) { } ["post_parent__not_in"]=> array(0) { } 
 ["author__in"]=> array(0) { } ["author__not_in"]=> array(0) { } 
 }
 
 -----------------------------------------------------------------------
 
 add_action( 'pre_get_posts', 'query_products' );
function query_products( $query ) {

    var_dump($query);

    // Check that it is the query we want to change: front-end search query
    if( $query->is_main_query() && ! is_admin() && $query->is_search() ) {

        // Change the query parameters
        $query->set( 'posts_per_page', 3 );

    }
}

------------------------------------------------------------------------

add_filter('template_include', 'product_search_template', 100);
function product_search_template( $template ) {
 if ( is_search() ) { 
   global $wp_query; $query_vars = $wp_query->query_vars;
   if($query_vars['post_type'] == "product"){      
     $ct = locate_template('product/search.php', false, false);
     if ( $ct ) $template = $ct;
   }
 }
 return $template;
}

/*add_action( 'template_redirect', 'fb_change_search_url_rewrite', 101 );
function fb_change_search_url_rewrite() {
 if ( is_search() && ! empty( $_GET['s'] ) ) {
  wp_redirect( home_url( "/product/search/?q=" ) . urlencode( get_query_var( 's' ) ) );
  exit();
 }	
}*/

add_action( 'pre_get_posts', 'query_products' );
function query_products( $query ) { //var_dump($query);
  // Check that it is the query we want to change: front-end search query
  if( $query->is_main_query() && ! is_admin() && $query->is_search() ){ 
    if($query->post_type == 'product'){ 
      $query->set('posts_per_page', -1);
    }
  }
}

//-----------------
//api functions
//-----------------
function ombuyapi_integrate(){
  //ombuy url params generation
  $params = '&start_price=0&end_price=0&cat=51030012&page=1&seller_info=no&api_name=taobao_item_search&result_type=json';  
  $ombuyURL = 'http://api.onebound.cn/taobao/api_call.php?key=omybuy.com&version=&q=';
  $ombuyURL .= $params; //var_dump($ombuyURL);
  //fetch the data
  $result = file_get_contents($ombuyURL);
  $result_array = json_decode($result, true);
  //return the result as array
  return $array_result;
}






 
 
 
 
 
 
 
 
