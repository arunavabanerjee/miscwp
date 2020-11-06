<?php
/**
 * Add Pages to Tools Menu 
 * API Key: 2ed0010c5ce1a8c46a6dd9c292ac412a
 * Secret Key: ee62df719388c3b1af76b151da5e63c1
 */
if( is_admin()) { 
  //list guesty objects
  add_management_page('Guesty Import & Export', 'Guesty Objects Import/Export', 'import', 'import-guesty-objects', 'func_import_guesty_objects', 78);
}

function func_import_guesty_objects(){  ?>
<div class="container-fluid">
<?php 
flush(); ob_flush();
if(count($_POST) > 0){ //var_dump($_POST); exit;
$dataItems = 2; //var_dump($_SERVER); exit;
if( isset($_POST['action']) && $_POST['action'] == 'import-from-guesty' ){
   $logfile = get_home_path().'guesty_importer.log'; 
?>
<p style="color:#F00;font-weight:502;margin:1em 1em;"><?php echo __('Output is also written to log file - guesty_importer.log', 'homey-child'); ?></p>
<div class="guesty-messages" style="width:98%;border:1px solid #999;background:#d2cdcdde;min-height:850px;overflow-y:scroll;margin:0 12px;">
<?php 
  // importing users from guesty
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** Import From Guesty Started On: ".date('Y-m-d H:i:s').PHP_EOL, 3, $logfile);
  error_log("*** Importing Users From Guesty ".PHP_EOL, 3, $logfile);  
  error_log("*** Existing Users Will Be Skipped ".PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  echo( "*** Import From Guesty Started On: ".date('Y-m-d H:i:s')."<br/>" );  
  echo( "*** Importing Users From Guesty "."<br/>" ); 
  echo( "*** Existing Users Will Be Skipped "."<br/>" ); 
  //$urlendpoint = 'accounts/me'; $retrievedItems = 0; 
  $urlendpoint = 'users'; $retrievedItems = 0; $paged = 1;
  flush(); ob_flush(); 
  /*--------------------------------*
   * import of users from guesty    *
   *--------------------------------*/
  do{ //start import of the set of listings from guesty. 
      $resultjson = fetch_guesty_data_from_api($paged, $urlendpoint, $logfile);  
      $resultusers = json_decode($resultjson, true); //var_dump($resultusers); exit; 
      error_log("*** No. Of Users Fetched: ".($retrievedItems + $resultusers['limit']).PHP_EOL, 3, $logfile);  
      error_log("*** Total Users: ".$resultusers['count'].PHP_EOL, 3, $logfile);
      echo( "*** No. Of Users Fetched: ".($retrievedItems + $resultusers['limit']).'<br/>' );  
      echo( "*** Total Users: ".$resultusers['count'].'<br/>' ); 
      error_log("*** Importing users to WP Site:..........................".PHP_EOL, 3, $logfile);
      echo( "*** Importing Users to WP Site.......................<br/>" ); flush(); ob_flush(); 
      //import the user data retrieved into wordpress 
      $cnt=0;foreach($resultusers['results'] as $guser){ 
	//check if the user already exits in DB. 
	error_log("*** Checking if User Exists: ".$guser['email'].PHP_EOL, 3, $logfile); 
	if( false === get_user_by( 'email', $guser['email'] )){
	   error_log("*** User Does Not Exist: ".$guser['email']." ...Importing".PHP_EOL, 3, $logfile); 
	   echo("*** User Does Not Exist: ".$guser['email']." ...Importing...</br/>"); flush(); ob_flush();
	   //generate the user args 
	   //if(stristr($guser["title"], 'director')){ $role = 'administrator'; }
	   //elseif(stristr($guser["title"], 'administrator')){ $role = 'administrator'; }
	   //elseif(stristr($guser["title"], 'agent')){ $role = 'homey_agents'; }	
	   //else{ $role = 'homey_brokers'; } 
           if(stristr($guser["title"], 'agent') || stristr($guser["title"], 'assistant')){ $role = 'homey_agents'; }
           else{ $role = 'homey_brokers'; }  
	   $uargs = array(
		'user_pass' => 'guesty_user@2020', 
		'user_login' => strtolower($guser["firstName"].$guser["lastName"]), 
		'user_nicename' => strtolower($guser["firstName"].'-'.$guser["lastName"]), 
		'user_email' => $guser['email'], 
		'display_name' => $guser["fullName"], 
		'first_name' => $guser["firstName"], 
		'last_name' => $guser["lastName"], 
		'description' => $guser["title"],
		'show_admin_bar_front' => false, 
		'role' => $role,
	   ); 
	   $user_id = wp_insert_user( $uargs ); 

	   //check roles for the listings. 
	   $assigned_listings_merged = array();
	   foreach($guser["roles"] as $urole){ 
	      if(array_key_exists("listingIds", $urole)){  
		  $assigned_listings_merged = array_merge($assigned_listings_merged, $urole["listingIds"]);
	      }
	   }
	   $assigned_listings = array_unique($assigned_listings_merged);
	   //set user meta with meta information of the user. 
	   update_user_meta($user_id, 'guesty_uid', $guser["_id"]); 
	   update_user_meta($user_id, 'guesty_acctnid', $guser["accountId"]); 
	   if(!empty($guser["preferredContactMethod"])){ update_user_meta($user_id, 'preferredContactMethod', $guser["preferredContactMethod"]); }
	   if(!empty($guser["emails"])){ update_user_meta($user_id, 'emails', serialize($guser["emails"])); }		
	   if(!empty($guser["phones"])){ update_user_meta($user_id, 'phones', serialize($guser["phones"])); } 
	   if(!empty($assigned_listings)){ update_user_meta($user_id, 'user_guesty_listings', serialize($assigned_listings)); } 

	   error_log("*** User Created: ".$guser['email']." ...Done ".PHP_EOL, 3, $logfile); 
	   echo("*** User Created: ".$guser['email']." ...Done.</br/>"); flush(); ob_flush();
	   if(++$cnt == $dataItems){ break; }

	}else{ 
	   error_log("*** User Already Exist: ".$guser['email']." ...Skipping ".PHP_EOL, 3, $logfile); 
	   echo("*** User Already Exist: ".$guser['email']." ...Skipping...</br/>"); flush(); ob_flush();
	}
	//stop the process after minimum import for testing purposes
      }
      //create the retrieved items and count items
      //$countItems = $resultarray['count'];
      //$retrievedItems += $resultarray['limit']; 
      //if($retrievedItems < $countItems){ $paged++; $continue = true; }
      //else{ $continue = false; }
      //if($retrievedItems == 50){ break; }
      //$continue = false;
      if($cnt < $dataItems){ $paged++; }
      elseif($cnt == $dataItems){ $continue = false; }
  } while( $continue == true ); //exit; 

  //importing guests from guesty 
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** Importing Guests From Guesty ".PHP_EOL, 3, $logfile);  
  error_log("*** Existing Guests Will Be Skipped ".PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  echo( "*** Importing Guests From Guesty "."<br/>" ); 
  echo( "*** Existing Guests Will Be Skipped "."<br/>" ); 
  //$urlendpoint = 'accounts/me'; $retrievedItems = 0; 
  $urlendpoint = 'guests'; $retrievedItems = 0; $paged = 1;
  flush(); ob_flush(); 
  /*--------------------------------*
   * import of guests from guesty    *
   *--------------------------------*/
   do{ //start import of the set of listings from guesty. 
	$resultjson = fetch_guesty_data_from_api($paged, $urlendpoint, $logfile);  
	$resultguests = json_decode($resultjson, true); //var_dump($resultguests); exit; 
        error_log("*** No. Of Guests Fetched: ".($retrievedItems + $resultguests['limit']).PHP_EOL, 3, $logfile);  
        error_log("*** Total Guests: ".$resultguests['count'].PHP_EOL, 3, $logfile);
        echo( "*** No. Of Guests Fetched: ".($retrievedItems + $resultguests['limit']).'<br/>' );  
        echo( "*** Total Guests: ".$resultguests['count'].'<br/>' ); 
	error_log("*** Importing Guests to WP Site:..........................".PHP_EOL, 3, $logfile);
        echo( "*** Importing Guests to WP Site.......................<br/>" ); flush(); ob_flush(); 
	//import the user data retrieved into wordpress 
	$cnt=0;foreach($resultguests['results'] as $guest){ 
	   //if firstname does not exist, then omit guest
	   if(!isset($guest["firstName"]) || empty($guest["firstName"])){ 
		error_log("*** Guest Does Not Have First Name: ".$guest["_id"].PHP_EOL, 3, $logfile);  
                error_log("*** Cannot Import ... Quitting To Next".PHP_EOL, 3, $logfile);
                echo( "*** Guest Does Not Have First Name: ".$guest["_id"].'<br/>' ); continue;
	   }
	   //check if the user already exits in DB. 
	   error_log("*** Checking if Guests Exists: ".$guest["firstName"].PHP_EOL, 3, $logfile); 
	   if( false === get_user_by( 'login', strtolower($guest["firstName"])) ){
		error_log("*** Guest Does Not Exist: ".$guest["firstName"]." ...Importing".PHP_EOL, 3, $logfile); 
		echo("*** Guest Does Not Exist: ".$guest["firstName"]." ...Importing...</br/>"); flush(); ob_flush();
		//generate the user args 
		//if(stristr($guser["title"], 'director')){ $role = 'administrator'; }
		//elseif(stristr($guser["title"], 'administrator')){ $role = 'administrator'; }
		//elseif(stristr($guser["title"], 'agent')){ $role = 'homey_agents'; }	
		//else{ $role = 'homey_brokers'; } 
		$role = 'homey_renter';
		$uargs = array(
			'user_pass' => 'guesty_guest@2020', 
			'user_login' => strtolower($guest["firstName"]), 
			'user_nicename' => strtolower($guest["firstName"]), 
			'user_email' => 'sample'.$guest["_id"].'@laestaterentals.com', 
			'display_name' => $guest["fullName"], 
			'first_name' => $guest["firstName"], 
			'last_name' => $guest["firstName"], 
			'description' => '',
			'show_admin_bar_front' => false, 
			'role' => $role,
		); 
		$user_id = wp_insert_user( $uargs ); 
		//set user meta with meta information of the user. 
		update_user_meta($user_id, 'guesty_uid', $guest["_id"]); 
		update_user_meta($user_id, 'guesty_acctnid', $guest["accountId"]); 
		update_user_meta($user_id, 'guesty_hometown', $guest["hometown"]); 
		if(!empty($guest["emails"])){ update_user_meta($user_id, 'emails', serialize($guest["emails"])); }		
		if(!empty($guest["phones"])){ update_user_meta($user_id, 'phones', serialize($guest["phones"])); } 

		error_log("*** Guest Created: ".$guest["firstName"]." ...Done ".PHP_EOL, 3, $logfile); 
		echo("*** Guest Created: ".$guest["firstName"]." ...Done.</br/>"); flush(); ob_flush();
	   	if(++$cnt == $dataItems){ break; }

	   }else{ 
		error_log("*** User Already Exist: ".$guest["firstName"]." ...Skipping ".PHP_EOL, 3, $logfile); 
		echo("*** User Already Exist: ".$guest["firstName"]." ...Skipping...</br/>"); flush(); ob_flush();
	   }
	   //stop the process after minimum import for testing purposes
	}
	//create the retrieved items and count items
	//$countItems = $resultarray['count'];
	//$retrievedItems += $resultarray['limit']; 
	//if($retrievedItems < $countItems){ $paged++; $continue = true; }
        //else{ $continue = false; }
	//if($retrievedItems == 50){ break; }
	//$continue = false;
        if($cnt < $dataItems){ $paged++; }
        elseif($cnt == $dataItems){ $continue = false; }
  } while( $continue == true ); //exit; 

  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** Importing Listings From Guesty Started On: ".date('Y-m-d H:i:s').PHP_EOL, 3, $logfile);
  error_log("*** Existing Listings Will Be Skipped ".PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  echo( "*** Import From Guesty Started On: ".date('Y-m-d H:i:s')."<br/>" );  
  echo( "*** Importing Listings From Guesty "."<br/>" ); 
  $urlendpoint = 'listings'; $retrievedItems = 0; $paged = 1;
  flush(); ob_flush(); 
  /*--------------------------------*
   * import of listings from guesty *
   *--------------------------------*/
   do{	//start import of the set of listings from guesty. 
	$resultjson = fetch_guesty_data_from_api($paged, $urlendpoint, $logfile);  
	$resultarray = json_decode($resultjson, true); //var_dump($resultarray); exit; 
        error_log("*** No. Of Listing Items Fetched: ".($retrievedItems + $resultarray['limit']).PHP_EOL, 3, $logfile);  
        error_log("*** Total Items: ".$resultarray['count'].PHP_EOL, 3, $logfile);
        echo( "*** No. Of Listing Items Fetched: ".($retrievedItems + $resultarray['limit']).'<br/>' );  
        echo( "*** Total Items: ".$resultarray['count'].'<br/>' );
	flush(); ob_flush();

	//import the data retrieved into wordpress 
	$cnt=0; foreach($resultarray['results'] as $item){ 
	   // create a query to check data 
   	   $qry = new WP_Query( array( 'post_type' => 'listing', 
	   			  'meta_query' => array( 
				      array('key' => 'homey_guesty_id', 'value' => $item["_id"], 'compare' => '='),
				   ), ));
   	   if($qry->found_posts > 0){ continue; }
	   else{  //var_dump($item); exit;
	     import_guestylistingitem_to_WP( $item, $logfile );
	     if(++$cnt == $dataItems){ break; }
	   }
	}
	//create the retrieved items and count items
	//$countItems = $resultarray['count'];
	//$retrievedItems += $resultarray['limit']; 
	//if($retrievedItems < $countItems){ $paged++; $continue = true; }
        //else{ $continue = false; }
	//if($retrievedItems == 6){ break; }
	//$continue = false;
        if($cnt < $dataItems){ $paged++; }
        elseif($cnt == $dataItems){ $continue = false; }
  } while( $continue == true ); //exit;

  echo( "<br/>=============================================================<br/>" ); 
  echo( "*** All Guesty Constructs Imported: ".date('Y-m-d H:i:s')."<br/>" );  
  echo( "=================================================================<br/>" ); 
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** All Guesty Constructs Imported ".date('Y-m-d H:i:s').PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);

  /*//importing reservations from guesty 
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** Importing Reservations From Guesty ".PHP_EOL, 3, $logfile);  
  error_log("*** Existing Reservations Will Be Skipped ".PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  echo( "*** Importing Reservations From Guesty "."<br/>" ); 
  echo( "*** Existing Reservations Will Be Skipped "."<br/>" ); 
  $urlendpoint = 'reservations'; $retrievedItems = 0; $paged = 1;
  flush(); ob_flush();
  /*-----------------------------------------------*
   * import of reservations from guesty            *
   *-----------------------------------------------*
  do{	//start import of the set of listings from guesty. 
	$resultjson = fetch_guesty_data_from_api($paged, $urlendpoint, $logfile);  
	$resultreserv = json_decode($resultjson, true); var_dump($resultreserv); exit; 
        //error_log("*** No. Of Listing Items Fetched: ".($retrievedItems + $resultarray['limit']).PHP_EOL, 3, $logfile);  
        //error_log("*** Total Items: ".$resultarray['count'].PHP_EOL, 3, $logfile);
        //echo( "*** No. Of Listing Items Fetched: ".($retrievedItems + $resultarray['limit']).'<br/>' );  
        //echo( "*** Total Items: ".$resultarray['count'].'<br/>' );
	//flush(); ob_flush();

	//import the data retrieved into wordpress 
	//foreach($resultarray['results'] as $item){ 
	   // create a query to check data 
   	  // $qry = new WP_Query( array( 'post_type' => 'listing', 
	  // 			'meta_query' => array( array('key' => 'homey_guesty_id', 'value' => $item["_id"], 'compare' => '='),),));
   	  // if($qry->found_posts > 0){ continue; } 
	  // var_dump($item); exit;
	//}
	//create the retrieved items and count items
	//$countItems = $resultarray['count'];
	//$retrievedItems += $resultarray['limit']; 
	//if($retrievedItems < $countItems){ $paged++; $continue = true; }
        //else{ $continue = false; }
	//if($retrievedItems == 50){ break; }
	//$continue = false;
  } while( $continue == true ); */
?>
</div>
<?php } //if import from guesty ?>

<?php 
if( isset($_POST['action']) && $_POST['action'] == 'export-to-guesty' ){
  $logfile = get_home_path().'guesty_exporter.log'; 
  $dataItems = 4;
?>
<p style="color:#F00;font-weight:502;margin:1em 1em;"><?php echo __('Output is also written to log file - guesty_importer.log', 'homey-child'); ?></p>
<div class="guesty-messages" style="width:98%;border:1px solid #999;background:#d2cdcdde;min-height:850px;overflow-y:scroll;margin:0 12px;">
<?php
  //exporting reservations to guesty 
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  error_log("*** Exporting Reservations To Guesty ".PHP_EOL, 3, $logfile);  
  error_log("*** Existing Reservations Will Be Sent ".PHP_EOL, 3, $logfile);  
  error_log('======================================================='.PHP_EOL, 3, $logfile);
  echo( "*** Exporting Reservations To Guesty "."<br/>" ); 
  echo( "*** Existing Reservations Will Be Sent "."<br/>" ); 
  $retrievedItems = 0; $paged = 1; $guesty_acctnId = '5cc2341480164a0024952bfe'; 
  // list all reservations 
  $reservargs = array( 'post_type' =>  'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish');
  $res_query = new WP_Query($reservargs); //var_dump($res_query); exit;
  $cnt = 0; foreach($res_query->posts as $reserv){ 
     $greservId = get_post_meta($reserv->ID, 'guesty_reserv_id', true); 
     if(!empty($greservId)){ 
        $status = '';
  	$dbstatus = get_post_meta($reserv->ID, 'reservation_status', true); 
        if($dbstatus == 'in_progress'){ $tatus = 'inquiry'; }
	if($dbstatus == 'booked'){ $tatus = 'reserved'; }
	if($dbstatus == 'confirmed'){ $tatus = 'confirmed'; }	
	if($dbstatus == 'declined'){ $tatus = 'declined'; }
	if($dbstatus == 'canceled'){ $tatus = 'canceled'; }
	if($dbstatus == 'closed'){ $tatus = 'closed'; }
	if(empty($tatus)){ $status = 'inquiry'; }
	//$renter_guesty_id = get_user_meta($renter_id, 'guesty_uid', true);
        $data = array('status' => $status); 
        $data_json = json_encode($data); //var_dump($data_json); exit;
        $returnVal = post_guesty_data_to_api($paged, 'reservations/'.$greservId, $data_json, $logfile ); 
        error_log("*** Changed Status For- ".$reserv->ID.' to Guesty Reserv Id - '.$greservId.PHP_EOL, 3, $logfile);  
        echo( "*** Changed Status For- ".$reserv->ID.' to Guesty Reserv Id - '.$greservId."<br/>" ); 
	if(++$cnt == $dataItems){ break; }else{ continue; }
     }
     //get the reservation listing id 
     $listing_id = get_post_meta($reserv->ID, 'reservation_listing_id', true); 
     //check if that is guesty rservation 
     $guesty_id = get_post_meta($listing_id, 'homey_guesty_id', true);
     if(!isset($guesty_id) && !empty($guesty_id)){ 
        $guesty_listingId = $guesty_id; $status = '';
        $checkInDateLocalized = get_post_meta($reserv->ID, 'reservation_checkin_date', true); 
        $checkOutDateLocalized = get_post_meta($reserv->ID, 'reservation_checkout_date', true);
  	$dbstatus = get_post_meta($reserv->ID, 'reservation_status', true); 
        if($dbstatus == 'in_progress'){ $tatus = 'inquiry'; }
	if($dbstatus == 'booked'){ $tatus = 'reserved'; }
	if($dbstatus == 'confirmed'){ $tatus = 'confirmed'; }	
	if($dbstatus == 'declined'){ $tatus = 'declined'; }
	if($dbstatus == 'canceled'){ $tatus = 'canceled'; }
	if($dbstatus == 'closed'){ $tatus = 'closed'; }
	if(empty($tatus)){ $status = 'inquiry'; }
        $money = array("fareAccommodation" => "500", "currency" => "USD");
	$renter_id = get_post_meta($reserv->ID, 'listing_renter', true);
        $renter = get_user_by('ID', $renter_id);
        //if renter has a guesty id 
	$renter_guesty_id = get_user_meta($renter_id, 'guesty_uid', true);
	if(!empty($renter_guesty_id )){ 
           //$guest = array("guestId"=> $renter_fname, "lastName"=> $renter_lname, "phone"=> $renter_phone, "email"=> $renter_email);
           $data = array('acccountId' => $guesty_acctnId, 'listingId' => $guesty_listingId, 
			'checkInDateLocalized' => $checkInDateLocalized, 'checkOutDateLocalized' => $checkOutDateLocalized, 
			'status' => $status, 'money' => $money, 'guestId' => $renter_guesty_id); 
	}else{ //create a renter and assign
	   $renter_fname = get_user_meta($renter_id, 'first_name', true ); 
           $renter_lname = get_user_meta($renter_id, 'last_name', true ); 
	   $renter_phone = "+7777777"; $renter_email = $renter->user_email;         
           $guest = array("firstName"=> $renter_fname, "lastName"=> $renter_lname, "phone"=> $renter_phone, "email"=> $renter_email);
           $data = array('acccountId' => $guesty_acctnId, 'listingId' => $guesty_listingId, 
			'checkInDateLocalized' => $checkInDateLocalized, 'checkOutDateLocalized' => $checkOutDateLocalized, 
			'status' => $status, 'money' => $money, 'guest' => $guest); 
	}
        $data_json = json_encode($data); //var_dump($data_json); exit;
        $returnVal = post_guesty_data_to_api($paged, 'reservations', $data_json, $logfile );
     } else {
	//[inquiry, declined, expired, canceled, closed, reserved, confirmed, checked_in, checked_out, awaiting_payment]
	$guesty_listingId = '5ce5839cf3411b005649c662'; $status = '';
        $checkInDateLocalized = get_post_meta($reserv->ID, 'reservation_checkin_date', true); 
        $checkOutDateLocalized = get_post_meta($reserv->ID, 'reservation_checkout_date', true);
  	$dbstatus = get_post_meta($reserv->ID, 'reservation_status', true); 
        if($dbstatus == 'in_progress'){ $tatus = 'inquiry'; }
	if($dbstatus == 'booked'){ $tatus = 'reserved'; }
	if($dbstatus == 'confirmed'){ $tatus = 'confirmed'; }	
	if($dbstatus == 'declined'){ $tatus = 'declined'; }
	if($dbstatus == 'canceled'){ $tatus = 'canceled'; }
	if($dbstatus == 'closed'){ $tatus = 'closed'; }
	if(empty($tatus)){ $status = 'inquiry'; }
        $money = array("fareAccommodation" => "500", "currency" => "USD");
	$renter_id = get_post_meta($reserv->ID, 'listing_renter', true);
        $renter = get_user_by('ID', $renter_id);
        $renter_fname = get_user_meta($renter_id, 'first_name', true ); 
        $renter_lname = get_user_meta($renter_id, 'last_name', true ); 
	$renter_phone = "+7777777"; $renter_email = $renter->user_email;         
        $guest = array("firstName"=> $renter_fname, "lastName"=> $renter_lname, "phone"=> $renter_phone, "email"=> $renter_email);
        $data = array('acccountId' => $guesty_acctnId, 'listingId' => $guesty_listingId, 
		'checkInDateLocalized' => $checkInDateLocalized, 'checkOutDateLocalized' => $checkOutDateLocalized, 
		'status' => $status, 'money' => $money, 'guest' => $guest); 
        $data_json = json_encode($data); //var_dump($data_json); exit;
        $returnVal = post_guesty_data_to_api($paged, 'reservations', $data_json, $logfile ); 
	//$return_Array = json_decode($returnVal, true); var_dump($return_Array); exit;
     }
     $return_Array = json_decode($returnVal, true); //var_dump($return_Array); exit; 
     //enter the guesty reservation id to the record
     update_post_meta($reserv->ID, 'guesty_reserv_id', $return_Array['id']); 
     error_log("*** Updated- ".$reserv->ID.' to Guesty Reserv Id - '.$return_Array['id'].PHP_EOL, 3, $logfile);  
     echo( "*** Updated- ".$reserv->ID.' to Guesty Reserv Id - '.$return_Array['id']."<br/>" ); 
     if(++$cnt == $dataItems){ break; }
  } // end foreach 
  header("Location: ".$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER["SERVER_NAME"].$_SERVER["REDIRECT_URL"]);
?>
</div>
<?php 
} //if export to guesty ?>
<?php } //if count($_POST); ?>

<div class="guesty-forms" style="border:1px solid #BBB;min-height:219px;margin:5px 12px;background:#0000001a;">
<!-- import objects from Guesty -->
<div class="guesty-import-form" style="width:42%;float:left;border:1px solid #AAA;background:#DDD;padding:6px;margin:12px;">
<form id="guesty-import-objects" method="POST">
   <input type="hidden" name="apikey" value="2ed0010c5ce1a8c46a6dd9c292ac412a" />
   <input type="hidden" name="secretkey" value="ee62df719388c3b1af76b151da5e63c1" />	
   <input type="hidden" name="action" value="import-from-guesty" />  
   <h4 style="text-align:center;"> To Start Importing Objects From Guesty, Please Click On The Button Below </h4>
   <h5 style="text-align:center;"> The Following Objects will be imported (if new) - Listings, Users, Leads </h5>
   <input type="button" name="btn-import-guesty" value="Import Objects From Guesty" 
	  style="margin:0 25%;padding:10px;border-radius:7px;background:#7ec3d0;border-color:#007cba;" />
   <p class="loader-div" style="text-align:center;"></p>	
</form></div>
<!-- export objects to Guesty -->
<div class="guesty-export-form" style="width:42%;float:left;border:1px solid #AAA;background:#DDD;padding:6px;margin:12px;">
<form id="guesty-export-objects" method="POST">
   <input type="hidden" name="apikey" value="2ed0010c5ce1a8c46a6dd9c292ac412a" />
   <input type="hidden" name="secretkey" value="ee62df719388c3b1af76b151da5e63c1" />
   <input type="hidden" name="action" value="export-to-guesty" />    
   <h4 style="text-align:center;"> To Start Exporting Objects To Guesty, Please Click On The Button Below </h4>
   <h5 style="text-align:center;"> The Following Objects will be exported (if new) - Leads (Status) </h5>
   <input type="button" name="btn-export-guesty" value="Export Objects To Guesty" 
	style="margin:0 25%;padding:10px;border-radius:7px;background:#efa3af;border-color:#007cba;" />	
</form></div>
</div>
</div>
<script>
jQuery(function($){
   /* import button on click */
   $('.guesty-import-form input[name="btn-import-guesty"]').click(function(){ 
	var $import_btn = $(this); 
	var $form = $import_btn.parent(); $form.submit();
   });
   /* export button on click */
   $('.guesty-export-form input[name="btn-export-guesty"]').click(function(){ 
	var $export_btn = $(this); 
	var $form = $export_btn.parent(); $form.submit();
   });
});
</script>
<?php  
}

/** fetch guesty data to API */
function post_guesty_data_to_api($paged = 1, $urlendpoint, $data, $logfile ){
   //using curl to fetch all objects from guesty db. 
   $mainurl = 'https://api.guesty.com/api/v2';
   $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
   $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
   $searchUrl = $mainurl.'/'.$urlendpoint; 
   $base64enc = base64_encode($apikey.':'.$secretkey);
   $authorization = "Basic $base64enc";  
   if($paged == 1){ $limit = 25; $skip = 0; } 
   else { $limit = 25; $skip = $limit * ($paged - 1); }

   if($paged == 1){ $searchUrlMod = $searchUrl; }
   if($paged > 1){ $searchUrlMod = $searchUrl.'?'.'skip='.$skip; } 
   error_log('*** Url EndPoint: '.$searchUrlMod.PHP_EOL, 3, $logfile);
   echo( "*** Url For Reservations: ".$searchUrlMod."<br/>" );  
   //$data = array('limit' => $limit, 'skip' => $skip); 
   //$data_string = json_encode($data); 
   $data_string = $data;
   //var_dump($data_string); //echo $searchUrlMod; //exit; 

   $s_time = microtime(true);
   $ch = curl_init($searchUrlMod); 
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
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
   $result = curl_exec($ch); //var_dump($result); exit; 
   if(curl_error($ch)) { 
	echo "==============================================================<br/>";
	echo '** Error : Unable To Connect To Guesty API - '; curl_error($ch);
        echo "<br/>============================================================<br/>"; 
	error_log( "=============================================================".PHP_EOL, 3, $logfile );
	error_log( '** Error : Unable To Connect To Guesty API - '.curl_error($ch).PHP_EOL, 3, $logfile );
        error_log( "=============================================================".PHP_EOL, 3, $logfile ); 
        curl_close($ch); exit;
   }
   curl_close($ch);
   //echo 'Listings:'; var_dump($result); exit; 
   $e_time = microtime(true); $elapsed = $e_time - $s_time; 
   echo 'Time Taken: '.round($elapsed,3).' secs'; echo '<br/><br/>';
   error_log( 'Time Taken: '.round($elapsed,3).' secs'.PHP_EOL, 3, $logfile );
   return $result; 
}

/** fetch guesty data from API */
function fetch_guesty_data_from_api($paged = 1, $urlendpoint, $logfile ){
   //using curl to fetch all objects from guesty db. 
   $mainurl = 'https://api.guesty.com/api/v2';
   $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
   $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 
   $searchUrl = $mainurl.'/'.$urlendpoint; 
   $base64enc = base64_encode($apikey.':'.$secretkey);
   $authorization = "Basic $base64enc";  
   if($paged == 1){ $limit = 25; $skip = 0; } 
   else { $limit = 25; $skip = $limit * ($paged - 1); }

   if($paged == 1){ $searchUrlMod = $searchUrl; }
   if($paged > 1){ $searchUrlMod = $searchUrl.'?'.'skip='.$skip; } 
   error_log('*** Url EndPoint: '.$searchUrlMod.PHP_EOL, 3, $logfile);
   echo( "*** Url For Listings: ".$searchUrlMod."<br/>" );  
   $data = array('limit' => $limit, 'skip' => $skip); 
   $data_string = json_encode($data); 
   //var_dump($data_string); //echo $searchUrlMod; //exit; 

   $s_time = microtime(true);
   $ch = curl_init($searchUrlMod); 
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
   if(curl_error($ch)) { 
	echo "==============================================================<br/>";
	echo '** Error : Unable To Connect To Guesty API - '; curl_error($ch);
        echo "<br/>============================================================<br/>"; 
	error_log( "=============================================================".PHP_EOL, 3, $logfile );
	error_log( '** Error : Unable To Connect To Guesty API - '.curl_error($ch).PHP_EOL, 3, $logfile );
        error_log( "=============================================================".PHP_EOL, 3, $logfile ); 
        curl_close($ch); exit;
   }
   curl_close($ch);
   //echo 'Listings:'; var_dump($result); exit; 
   $e_time = microtime(true); $elapsed = $e_time - $s_time; 
   echo 'Time Taken: '.round($elapsed,3).' secs'; echo '<br/><br/>';
   error_log( 'Time Taken: '.round($elapsed,3).' secs'.PHP_EOL, 3, $logfile );
   return $result; 
}

function import_guestylistingitem_to_WP( $item, $logfile ){
   global $wpdb;
   global $current_user; $prefix = 'homey_';
   wp_get_current_user(); $userID = $current_user->ID;

   error_log( "*** Import For Id: ".$item["_id"].PHP_EOL, 3, $logfile );	
   echo '*** Import For Id: '.$item["_id"].'<br/>'; flush(); ob_flush();
   // create a query to check data 
   $qry = new WP_Query( array( 'post_type' => 'listing', 
			'meta_query' => array( 
				array('key' => 'homey_guesty_id', 'value' => $item["_id"], 'compare' => '='), ), ));
   if( $qry->found_posts > 0){ return; } 
   // else start the new listing
   $s_time = microtime(true); 
   $new_listing = array( 'post_type' => 'listing' );
   // Title 
   if( isset( $item['title'] ) ) {  $new_listing['post_title'] = $item['title']; } 
   elseif( isset( $item["nickname"] ) ) {  $new_listing['post_title'] = $item["nickname"];  }
   // Description
   if( isset( $item["publicDescription"] ) ) {
	if( isset( $item["publicDescription"]["summary"] ) ){ 
	   $new_listing['post_content'] = wp_kses_post( $item["publicDescription"]["summary"] );
	   $new_listing['post_excerpt'] = wp_kses_post( $item["publicDescription"]["summary"] );
	}
	if( isset( $item["publicDescription"]["space"] ) ){ 				  
	   $new_listing['post_content'] = wp_kses_post( $item["publicDescription"]["space"] );
	   if( isset($item["publicDescription"]["houseRules"]) ){
		$new_listing['post_content'] .= wp_kses_post( $item["publicDescription"]["houseRules"] );
	   }
	}
   } // end descriptio
   // Author & status
   $new_listing['post_author'] = $userID; 
   $listing_id = 0; $new_listing['post_status'] = 'draft'; 
   $listing_id = wp_insert_post( $new_listing ); 

   error_log( "*** Listing Created For Id: ".$item["_id"].' System Listing Id: '.$listing_id.' Started Meta.'.PHP_EOL, 3, $logfile );
   echo "*** Listing Created For Id: ".$item["_id"].' System Listing Id: '.$listing_id.' Done. Started Meta.<br/>'; flush(); ob_flush();

   //generate all meta content for the listing. 
   update_post_meta( $listing_id, $prefix.'guesty_id', $item["_id"] );				
   update_post_meta( $listing_id, $prefix.'instant_booking', 0 );
   update_post_meta( $listing_id, $prefix.'guests', $item["accommodates"] ); 
   // currently bedrooms = number of rooms
   if(isset($item["bedrooms"])){ 
	update_post_meta( $listing_id, $prefix.'listing_bedrooms', $item["bedrooms"] ); 
	update_post_meta( $listing_id, $prefix.'listing_rooms', $item["bedrooms"] );
   }
   if(isset($item["beds"])){ update_post_meta( $listing_id, $prefix.'beds', $item["beds"] ); }
   if(isset($item["bathrooms"])){ update_post_meta( $listing_id, $prefix.'baths', $item["bathrooms"] ); }		
   //pricing - nightly and weekends
   if( isset($item["prices"]["basePriceUSD"]) ){
	update_post_meta( $listing_id, $prefix.'night_price', $item["prices"]["basePriceUSD"] );
	update_post_meta( $listing_id, $prefix.'weekends_price', $item["prices"]["basePriceUSD"] );
	update_post_meta( $listing_id, $prefix.'price_postfix', 'per night' );
   } elseif( isset($item["prices"]["basePrice"]) ){
	update_post_meta( $listing_id, $prefix.'night_price', $item["prices"]["basePrice"] ); 
	update_post_meta( $listing_id, $prefix.'weekends_price', $item["prices"]["basePrice"] );
	update_post_meta( $listing_id, $prefix.'price_postfix', 'per night' );
   }
   // weekend price
   if( isset($item["prices"]["monthlyPriceFactor"]) && isset($item["prices"]["basePriceUSD"]) ){
	$weekly_price = $item["prices"]["basePriceUSD"] * $item["prices"]["monthlyPriceFactor"];
	$monthly_price = $item["prices"]["basePriceUSD"] * $item["prices"]["monthlyPriceFactor"];
	//update_post_meta( $listing_id, $prefix.'weekends_price', $weekend_price ); 
	update_post_meta( $listing_id, $prefix.'priceWeek', $weekly_price ); 
        update_post_meta( $listing_id, $prefix.'priceMonthly', $monthly_price );
   }elseif( isset($item["prices"]["monthlyPriceFactor"]) && isset($item["prices"]["basePrice"]) ){
	$weekly_price = $item["prices"]["basePrice"] * $item["prices"]["monthlyPriceFactor"]; 
	$monthly_price = $item["prices"]["basePrice"] * $item["prices"]["monthlyPriceFactor"];
	//update_post_meta( $listing_id, $prefix.'weekends_price', $weekend_price ); 
	update_post_meta( $listing_id, $prefix.'priceWeek', $weekly_price );
        update_post_meta( $listing_id, $prefix.'priceMonthly', $monthly_price );
   }
   // allow additional guests
   if( isset($item["prices"]["guestsIncludedInRegularFee"]) ){ 
	if($item["prices"]["guestsIncludedInRegularFee"] == 1){
            update_post_meta( $listing_id, $prefix.'allow_additional_guests', 'yes' ); 
	}
   }
   // cleaning fee 
   if( isset($item["prices"]["cleaningFee"]) ){ 
       update_post_meta( $listing_id, $prefix.'cleaning_fee', $item["prices"]["cleaningFee"] );
       update_post_meta( $listing_id, $prefix.'cleaning_fee_type', 'per_stay' );
   }
   // security fee
   if( isset($item["prices"]["securityDepositFee"]) ){
	update_post_meta( $listing_id, $prefix.'security_deposit', $item["prices"]["securityDepositFee"] );
   }
   // account tax rate
   if( isset($item["accountTaxes"]) && !empty($item["accountTaxes"]) ){
	if( $item["accountTaxes"][0]["units"] == "PERCENTAGE"){
	     update_post_meta( $listing_id, $prefix.'tax_rate', $item["accountTaxes"][0]["amount"] ); 
	}
   }
   // weekend days 
   update_post_meta( $listing_id, $prefix.'weekends_days', 'fri_sat_sun' );
   // listing address 
   if( isset($item["address"]) && !empty($item["address"]) ){ $address = '';
	//update_post_meta( $listing_id, $prefix.'listing_address', $item["address"]["full"] ); 
	if(isset($item["address"]["street"])){ $address .= $item["address"]["street"].','; }
	if(isset($item["address"]["city"])){ $address .= $item["address"]["city"].','; }
	if(isset($item["address"]["state"])){ $address .= $item["address"]["state"].' '; }
	if(isset($item["address"]["zipcode"])){ $address .= $item["address"]["zipcode"]; }
	if(empty($address)){ $address .= $item["address"]["full"]; }				  
	update_post_meta( $listing_id, $prefix.'listing_address', $address ); 				  
   }
   if( isset($item["address"]["zipcode"]) ){
	update_post_meta( $listing_id, $prefix.'zip', $item["address"]["zipcode"] );
   }
   // Country
   if( isset($item["address"]["country"]) ){
   	$country_id = wp_set_object_terms( $listing_id, $item["address"]["country"], 'listing_country' );
   }
   // State
   if( isset( $item["address"]["state"] ) ) {
	$state_id = wp_set_object_terms( $listing_id, $item["address"]["state"], 'listing_state' );
	$homey_meta = array();
        $homey_meta['parent_country'] = isset( $item["address"]["country"] ) ? $item["address"]["country"] : '';
        if( !empty( $state_id) ) {
            update_option('_homey_listing_state_' . $state_id[0], $homey_meta);
        }
    }
    // City
    if( isset($item["address"]["city"] ) ) {
        $city_id = wp_set_object_terms( $listing_id, $item["address"]["city"], 'listing_city' );
	$homey_meta = array();
        $homey_meta['parent_state'] = isset( $item["address"]["state"] ) ? $item["address"]["state"] : '';
        if( !empty( $city_id) ) {
             update_option('_homey_listing_city_' . $city_id[0], $homey_meta);
        }
    }
    // Area
    if( isset( $item["address"]["neighborhood"] ) ) {
	$area_id = wp_set_object_terms( $listing_id, $item["address"]["neighborhood"], 'listing_area' );
	$homey_meta = array();
        $homey_meta['parent_city'] = isset( $item["address"]["city"] ) ? $item["address"]["city"] : '';
        if( !empty( $area_id) ) {
            update_option('_homey_listing_area_' . $area_id[0], $homey_meta);
        }
    }
    // cancellation policy and max and min nights
    if( isset($item["terms"]["cancellation"]) ){ 
	update_post_meta( $listing_id, $prefix.'cancellation_policy', $item["terms"]["cancellation"] );
    }
    if( isset($item["terms"]["minNights"]) ){ 
        update_post_meta( $listing_id, $prefix.'min_book_days', $item["terms"]["minNights"] );
    }
    if( isset($item["terms"]["maxNights"]) ){ 
        update_post_meta( $listing_id, $prefix.'max_book_days', $item["terms"]["maxNights"] );
    }
    // default checkout, checkin time
    if( isset($item["defaultCheckInTime"]) ){
        update_post_meta( $listing_id, $prefix.'checkin_after', $item["defaultCheckInTime"] );
    }
    if( isset($item["defaultCheckOutTime"]) ){
        update_post_meta( $listing_id, $prefix.'checkout_before', $item["defaultCheckOutTime"] );
    }
    // all other allowances
    if( isset($item["publicDescription"]["guestControls"]) ){
	if(isset($item["publicDescription"]["guestControls"]["allowsSmoking"])){ 
	  if($item["publicDescription"]["guestControls"]["allowsSmoking"] == false){
	     update_post_meta( $listing_id, $prefix.'smoke', 0 );
	  } else{
             update_post_meta( $listing_id, $prefix.'smoke', $item["publicDescription"]["guestControls"]["allowsSmoking"] );
	  }
	}
	if(isset($item["publicDescription"]["guestControls"]["allowsChildren"])){ 
	  if($item["publicDescription"]["guestControls"]["allowsChildren"] == false){
	     update_post_meta( $listing_id, $prefix.'children', 0 );
	  } else{
              update_post_meta( $listing_id, $prefix.'children', $item["publicDescription"]["guestControls"]["allowsChildren"] );
	  }
	}
	if(isset($item["publicDescription"]["guestControls"]["allowsPets"])){ 
	   if($item["publicDescription"]["guestControls"]["allowsPets"] == false){
	      update_post_meta( $listing_id, $prefix.'pets', 0 );
	   } else{
             update_post_meta( $listing_id, $prefix.'pets', $item["publicDescription"]["guestControls"]["allowsPets"] );
	   }
	}
	if(isset($item["publicDescription"]["guestControls"]["allowsEvents"])){ 
	   if($item["publicDescription"]["guestControls"]["allowsEvents"] == false){
	     update_post_meta( $listing_id, $prefix.'party', 0 );
	   } else{
             update_post_meta( $listing_id, $prefix.'party', $item["publicDescription"]["guestControls"]["allowsEvents"] );
	   }
	}
     }
     if( isset($item["publicDescription"]["houseRules"]) ){
    	  update_post_meta( $listing_id, $prefix.'additional_rules', $item["publicDescription"]["houseRules"] );
     }
     // opening hours and featured
     update_post_meta( $listing_id, $prefix.'mon_fri_closed', 0 );	
     update_post_meta( $listing_id, $prefix.'sat_closed', 0 );
     update_post_meta( $listing_id, $prefix.'sun_closed', 0 );
     update_post_meta( $listing_id, 'homey_featured', 0 );
     // map params
     if( ( isset($item["address"]["lat"]) && !empty($item["address"]["lat"]) ) 
	   && (  isset($item["address"]["lng"]) && !empty($item["address"]["lng"]) ) ) {
	$lat = $item["address"]["lat"]; $lng = $item["address"]["lng"];
        $lat_lng = $lat.','.$lng;
        update_post_meta( $listing_id, $prefix.'geolocation_lat', $lat );
        update_post_meta( $listing_id, $prefix.'geolocation_long', $lng );
        update_post_meta( $listing_id, $prefix.'listing_location', $lat_lng );
        update_post_meta( $listing_id, $prefix.'listing_map', '1' );   
        update_post_meta( $listing_id, $prefix.'show_map', '1' );               
        homey_insert_lat_long($lat, $lng, $listing_id); 
     }				
     // set room type and listing type
     if( isset($item["roomType"]) ){
	wp_set_object_terms( $listing_id, $item["roomType"], 'room_type' );
     }
     if( isset($item["propertyType"]) ){
        wp_set_object_terms( $listing_id, $item["propertyType"], 'listing_type' );
     }
     // Amenities
     if( isset( $item["amenities"] ) && !empty( $item["amenities"] ) ) { 
	$amenities_array = array(); 
	foreach( $item["amenities"] as $amenity ){ $amenities_array[] = $amenity; }
           wp_set_object_terms( $listing_id, $amenities_array, 'listing_amenity' );
     }

     error_log( "*** Added Data For Listing Id: ".$item["_id"].' System Listing Id: '.$listing_id.' Started Images.'.PHP_EOL, 3, $logfile );
     echo '*** Added Data For Listing Id: '.$item["_id"].' Done. Started Images.<br/>'; flush(); ob_flush();

     //upload featured image
     if(isset($item['picture']) && !empty($item['picture'])){ $featured = ''; 
	if(isset($item['picture']["large"])){ $featured = $item['picture']["large"]; }
	elseif(isset($item['picture']["regular"])){ $featured = $item['picture']["regular"]; }
	else{ $featured = $item['picture']["thumbnail"]; } 
	$imgfile = basename($featured); 
        if( pathinfo($imgfile, PATHINFO_EXTENSION) == '' ){ $imgfile .= '.jpg'; }
        if(strstr($featured,'https:')){ 
           if($_SERVER["REQUEST_SCHEME"] == "http"){
	      $remoteImageUrl = str_replace('https:', 'http:', $featured);
	   }else{ $remoteImageUrl = $featured; }
	}else{ $remoteImageUrl = 'https:'.$featured; } //var_dump($remoteImageUrl); //exit;
	$upload = wp_upload_bits($imgfile , null, file_get_contents($remoteImageUrl, FILE_USE_INCLUDE_PATH));
	$image_file = $upload['file'];  $file_type = wp_check_filetype($image_file, null);
	$attachment = array(
		'post_mime_type' => $file_type['type'],
		'post_title' => sanitize_file_name($imgfile),
		'post_content' => '',
		'post_status' => 'inherit' );
	$attachment_id = wp_insert_attachment( $attachment, $image_file );
	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
	wp_update_attachment_metadata( $attachment_id, $attachment_data );
 	update_post_meta( $listing_id, '_thumbnail_id', $attachment_id );
 	update_post_meta( $listing_id, 'homey_homeslider', 'yes' );
 	update_post_meta( $listing_id, 'homey_slider_image', $attachment_id );
      }

      error_log( "*** Featured Image Generated For Id: ".$item["_id"].' System Listing Id: '.$listing_id.' Started Other Images.'.PHP_EOL, 3, $logfile );
      echo '*** Featured Image Generated For Id: '.$item["_id"].' Done. Other Images.<br/>'; flush(); ob_flush();

      // upload other images
      if( isset($item['pictures']) && !empty($item['pictures']) ){ 
	 $imgcnt=0; foreach($item['pictures'] as $picture){ $selimage=''; 
	     if(isset($picture["large"])){ $selimage = $picture["large"]; }
	     elseif(isset($picture["regular"])){ $selimage = $picture["regular"]; }
	     else{ $selimage = $picture["thumbnail"]; } 
	     $imgfile = basename($selimage); 
             if( pathinfo($imgfile, PATHINFO_EXTENSION) == '' ){ $imgfile .= '.jpg'; }
	     if(strstr($featured,'https:')){ 
		if($_SERVER["REQUEST_SCHEME"] == "http"){
		  $remoteImageUrl = str_replace('https:', 'http:', $selimage);
		}else{ $remoteImageUrl = $selimage; }
	     } else{ $remoteImageUrl = 'https:'.$selimage; } //var_dump($remoteImageUrl); //exit;
	     $upload = wp_upload_bits($imgfile , null, file_get_contents($remoteImageUrl, FILE_USE_INCLUDE_PATH));
	     $image_file = $upload['file'];  $file_type = wp_check_filetype($image_file, null);
	     $attachment = array(
		 'post_mime_type' => $file_type['type'],
		 'post_title' => sanitize_file_name($imgfile),
		 'post_content' => '',
		 'post_status' => 'inherit' );
	     $attachment_id = wp_insert_attachment( $attachment, $image_file );
	     $attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
	     wp_update_attachment_metadata( $attachment_id, $attachment_data );
	     add_post_meta($listing_id, 'homey_listing_images', $attachment_id); 
	     if(++$imgcnt == 40){ break; } 
	  }
        }
	$e_time = microtime(true); $elapsed = $e_time - $s_time;
        error_log( "*** Gallery Images For Id: ".$item["_id"].' System Listing Id: '.$listing_id.' Done.'.PHP_EOL, 3, $logfile );
	echo '*** Gallery Images For Id: '.$item["_id"].' Done.<br/>'; flush(); ob_flush();

	echo '<div class="notice notice-success is-dismissible">';
	echo '<p>'._e('Listing Created From Guesty, Property ID: '.$item["_id"].' Time: '.$elapsed, 'homey-child' ).'</p>';
	echo '</div>';
}






