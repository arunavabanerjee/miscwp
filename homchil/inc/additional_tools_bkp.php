<?php

/**
 * Add Pages to Tools Menu 
 * API Key: 2ed0010c5ce1a8c46a6dd9c292ac412a
 * Secret Key: ee62df719388c3b1af76b151da5e63c1
 */
if( is_admin()) { 
  //list guesty objects
  add_management_page('Guesty Users - Import','Import Guesty Users', 'import', 'list-guesty-users', 'func_list_guesty_users', 78);
  add_management_page('Guesty Owners - Import','Import Guesty Owners', 'import', 'list-guesty-owners', 'func_list_guesty_owners', 78); 
  add_management_page('Guesty Bookings - Import','Import Guesty Bookings', 'import', 'list-guesty-bookings', 'func_list_guesty_bookings', 78);
  add_management_page('Guesty Listings - Import','Import Guesty Listings', 'import', 'list-guesty-listings', 'func_list_guesty_listings', 78);
  //add_management_page('Guesty Views - Import','Import Guesty Views', 'import', 'list-guesty-views', 'func_list_guesty_views', 78); 
  //add_management_page('Guesty Contacts - Import','Import Guesty Contacts', 'import', 'list-guesty-contacts', 'func_list_guesty_contacts', 78);
  //add_management_page('Guesty Integrations - Import','Import Guesty Integ', 'import', 'list-guesty-integs', 'func_list_guesty_integrations', 78);
  //add_management_page('Guesty Accounts - Import','Import Guesty Accounts', 'import', 'list-guesty-accounts', 'func_list_guesty_accounts', 78);
}

function func_list_guesty_accounts(){ 
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching accounts
  $data = array('q'=>null,'type'=>'accounts','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)){ echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Accounts:'; var_dump($result); 
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';  

  /** display the accounts in the following table. */
  $myListTable = new Guesty_Accounts_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-accounts-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}


function func_list_guesty_users(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'users','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Users:'; var_dump($result);  
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the users in the following table. */
  $myListTable = new Guesty_Users_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-users-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}

function func_list_guesty_contacts(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'contacts','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Contacts:'; var_dump($result);    
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the contacts in the following table. */
  $myListTable = new Guesty_Contacts_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-contacts-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}

function func_list_guesty_owners(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc";  
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'owners','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Owners:'; var_dump($result);  
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the owners in the following table. */
  $myListTable = new Guesty_Owners_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-owners-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}

function func_list_guesty_integrations(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'integrations','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Integrations:'; var_dump($result);  
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the integrations in the following table. */
  $myListTable = new Guesty_Integrations_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-integrations-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}

function func_list_guesty_listings(){
  /** display the listings in the following table. */
  $myListTable = new Guesty_Listing_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-listings-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}

function func_list_guesty_bookings(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'bookings','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Bookings:'; var_dump($result);  
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the bookings in the following table. */
  $myListTable = new Guesty_Booking_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-bookings-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;

}
 
function func_list_guesty_views(){
  //using curl to fetch all objects from guesty db. 
  $mainurl = 'https://api.guesty.com/api/v2';
  //$apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  //$secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
  $base64enc = base64_encode($apikey.':'.$secretkey);
  $authorization = "Basic $base64enc"; 
  $searchUrl = $mainurl.'/search';
  flush(); ob_flush();

  //generate a GET request for searching users
  $data = array('q'=>null,'type'=>'views','limit'=>25,'skip'=>0);
  $data_string = json_encode($data); $s_time = microtime(true);
  $ch = curl_init($searchUrl); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "WWW-Authenticate: Basic",
    "Authorization: $authorization",
    'Content-Type: application/json',
    'Content-Length: '.strlen($data_string),
  ));
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch); 
  if(curl_error($ch)) { echo 'Curl Error:'; var_dump(curl_error($ch)); }
  curl_close($ch); 
  echo 'Views:'; var_dump($result);  
  $e_time = microtime(true); $elapsed = $e_time - $s_time; 
  echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';

  /** display the bookings in the following table. */
  $myListTable = new Guesty_Views_List_Table(); 
  $myListTable->prepare_items();
  echo '<form id="guesty-views-table" method="GET">';
  echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>';
  $myListTable->views();
  $myListTable->search_box('search', 'search_id');
  $myListTable->display(); 
  echo '</form>';
  flush(); ob_flush(); sleep(2); //echo $data;
}























