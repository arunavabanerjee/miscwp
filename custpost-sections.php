<?php
/**
 * 
 * Plugin is used to fetch headers and footers from cda-global api. 
 * To Be Used For Wordpress Sites for Consumer Reports. 
 * main file
 */ 

/**
 * If wordpress is not loaded, exit.
 */
if ( ! defined( 'ABSPATH' )) { exit; }

/**
 * Defines the directory path for the plugin. 
 */
define( 'WP_CDAGLOBAL_PLUGIN', __FILE__ ); 
define( 'WP_CDAGLOBAL_PLUGIN_DIR', dirname( WP_CDAGLOBAL_PLUGIN ) ); 

require_once ( ABSPATH. 'wp-admin/includes/class-wp-filesystem-base.php' );
require_once ( ABSPATH. 'wp-admin/includes/class-wp-filesystem-direct.php' );
require_once WP_CDAGLOBAL_PLUGIN_DIR. '/inc/cdaglobalconstants.php'; 
require_once WP_CDAGLOBAL_PLUGIN_DIR. '/inc/cdaglobalfunctions.php';
require_once WP_CDAGLOBAL_PLUGIN_DIR. '/options/cdaglobal_customoptions.php'; 

/** 
 * Activaion Hook For The Plugin 
 * On Activation the following options are created.
 * a. options for incremental fetch from API. 
 */
register_activation_hook( __FILE__, 'wp_cdaglobal_activate' ); 
function wp_cdaglobal_activate(){ 
  /* create the options for fetch */
  update_option('cdaglobalassets_fetch', 'false'); 
  update_option('cdaglobalfooter_fetch', 'false');
  update_option('cdaglobalheader_fetch', 'false'); 
  update_option('cdaglobal_environment', 'QA-1'); 
  update_option('cdaglobal_styleembed', 'NO-STYLE'); 
  update_option('cdaglobal_domfield_header', 'header');
  update_option('cdaglobal_domfield_footer', 'footer');   
}

add_action('activated_plugin', 'func_cdaglobal_settings_redirection'); 
function func_cdaglobal_settings_redirection($pluginname){
  if( plugin_basename( __FILE__ ) == $pluginname ) {  
    // to the Settings > General Page
    exit(wp_redirect(admin_url('options-general.php?page=cdaglobal-options'))); 
  }  
}


/**
 * De-activate the Plugin
 * On Deactivation the following are removed - 
 * a. options for incremental fetch from API 
 * b. optons for the files that have been created. 
 * c. options for the endpoints 
 * 
 * To Do: 
 * Remove files and directory created for filestorage. 
 */
register_deactivation_hook( __FILE__, 'wp_cdaglobal_deactivate' );
function wp_cdaglobal_deactivate(){
  /* delete the options for fetch */
  //delete_option('cdaglobalassets_fetch'); delete_option('cdaglobalfooter_fetch');
  //delete_option('cdaglobalheader_fetch');
  /* delete the api endpoints */     
  //delete_option('cdaglobal_assets_endpointid'); delete_option('cdaglobal_footer_endpointid');
  //delete_option('cdaglobal_header_endpointid'); 
  /* delete the daia directory and files */
  //$fileSystemDirect = new WP_Filesystem_Direct(false);
  //$dir = WP_PLUGIN_DIR . '/wp_cdaglobal/data'; 
  //$fileSystemDirect->rmdir($dir, true);  
  /* delete the  file options */ 
  //delete_option('cdaglobal_assets_file'); delete_option('cdaglobal_footer_file'); 
  //delete_option('cdaglobal_header_file'); 
  /*delete environment*/
  //delete_option('cdaglobal_environment'); delete_option('cdaglobal_styleembed'); 
  //delete_option('cdaglobal_domfield_header');
  //delete_option('cdaglobal_domfield_footer');  
  //delete_option('cdaglobal_fetchdir');     
}

/**
 * Enqueue Scripts for Admin 
 * The admin scripts required will be loaded. 
 */ 
add_action('admin_enqueue_scripts', 'func_cdaglobal_register_scripts_and_styles');
function func_cdaglobal_register_scripts_and_styles() { 
  $screen = get_current_screen(); 
  if($screen->base == "settings_page_cdaglobal-options"){
    wp_register_style('cdaglobal_wpadmin_css', plugin_dir_url( __FILE__ ) .'/css/cdaglobal-wpadmin.css', 
    				  false, '1.0.0');
    wp_enqueue_style('cdaglobal_wpadmin_css'); 
  }  
} 

/**
 * Fetch cda global data on init 
 * If the respective data files does not exist.  
 */
add_action('admin_init', 'fetch_cdaglobaldata');
function fetch_cdaglobaldata() {
 if( is_admin() ){ 
   //initializes the cdaglobal settings on init.
   //the settings can be found in Settings > CdaGlobal Options.  
   cdaglobal_general_settings();
 }  
}


/**
 * Enqueue scripts for the frontend
 * The frontend scripts required in the header will be loaded.
 */
/*add_action('after_setup_theme', 'func_cdaglobal_add_scripts_and_styles_to_theme');
function func_cdaglobal_add_scripts_and_styles_to_theme(){
  if(! is_admin() ){ 
	//get the data from filepath 
	$fileDir = get_option('cdaglobal_fetchdir'); 
	if(substr($fileDir, -1) != '/'){ $fileDir = $fileDir.'/'; }
	$assetshtml = file_get_contents($fileDir.get_option('cdaglobal_assets_file'));
	$footerhtml = file_get_contents($fileDir.get_option('cdaglobal_footer_file')); 
	$headerhtml = file_get_contents($fileDir.get_option('cdaglobal_header_file')); 
   	wp_register_script('cdaglobal_front_js', plugin_dir_url( __FILE__ ) .'/front-js/cdaglobal-front.js', 
   						array('jquery'), '1.0.0', true); 
   	$args = array('asseturl' => $assetshtml, 'footerurl' => $footerhtml, 
   				  'headerurl' => $headerhtml, 
			      'embed_style' => get_option('cdaglobal_styleembed'), 
			      'replace_head' => get_option('cdaglobal_domfield_header'), 
			      'replace_footer' => get_option('cdaglobal_domfield_footer'), );			      			      			       
   	wp_localize_script( 'cdaglobal_front_js', 'opts', $args );  			  
   	wp_enqueue_script('cdaglobal_front_js'); 
  }
}*/ 

--------------------------------------------------------------------------------
//constants 
--------------------
/**
 * Defines all the api endpoints for the assets
 */
// QA 02 links  https://www.consumerreports.org/cro/a-to-z-index/products/index.htm
define( 'ASSET_URL', '' ); 
define( 'FOOTER_URL', '' );
define( 'HEADER_URL', '' );
------------------------------------------
functions 
-----------
require_once __DIR__.'/cdaglobalconstants.php';
/*==============================*
 * AjaxCalls From The Frontend  *
 *==============================*/
/** Action On Startup */
//add_action( 'wp_ajax_nopriv_cdaglobal_TestSystem_OnStartup', 'func_cdaglobal_TestSystem_OnStartup' );  
add_action( 'wp_ajax_cdaglobal_TestSystem_OnStartup', 'func_cdaglobal_TestSystem_OnStartup' );  
function func_cdaglobal_TestSystem_OnStartup(){ 
   $fetchArray = []; 
   /* check if write directory has been defined */ 
   $cdaglobal_fetch = get_option('cdaglobal_fetchdir'); 
   if($cdaglobal_fetch === false){ //if fetch directory not defined
	 $tempDir = ini_get('upload_tmp_dir');
     $uploadsDir = wp_upload_dir(null, true);
     
     /** Check if the uploads directory is writable */
     if($uploadsDir['error']){ //if uploads not writable,
   	   try{
   	  	 $result = wp_is_writable($tempDir); 
   	  	 if($result){ update_option('cdaglobal_fetchdir', $tempDir);
  	     $fetchArray['directory'] = 'temp';    	  	 
   	  	 }else{ 
		   $response .= "Uploads Dir And Temp Dir Not Writable";
	       echo json_encode( array('success' => false, 'msg' => $response) );
	       wp_die();   	  	 
   	  	 }
   	   }catch(Exception $e){ 
		 $response = $e->getMessage(); 
		 $response .= "&& Uploads Dir Not Writable";
	     echo json_encode( array('success' => false, 'msg' => $response) );
	     wp_die();
   	   }
   	 }else{ //if uploads is writable, set the baseUrl and baseDir 
	 	update_option('cdaglobal_fetchdir', $uploadsDir['basedir']);   	 	
   	    $fetchArray['directory'] = 'uploads'; 
   	 }  
   }         
   //get the values of the cdaglobal options
   $assetsFetch = get_option('cdaglobalassets_fetch');
   $footerFetch = get_option('cdaglobalfooter_fetch');
   $headerFetch = get_option('cdaglobalheader_fetch'); 
   //populate array
   if($assetsFetch == 'false'){ $fetchArray['assets'] = 0; } else { $fetchArray['assets'] = 1; }
   if($footerFetch == 'false'){ $fetchArray['footer'] = 0; } else { $fetchArray['footer'] = 1; }
   if($headerFetch == 'false'){ $fetchArray['header'] = 0; } else { $fetchArray['header'] = 1; } 
   //will generate whether it is a success or not.  
   $fetchArray['success'] = true;     
   echo json_encode( $fetchArray ); 
   wp_die();
}

/** Action For Retrieving Urls For Specific Environment*/
//add_action( 'wp_ajax_nopriv_cdaglobal_RetrieveEnvUrls', 'func_cdaglobal_RetrieveEnvUrls' );  
add_action( 'wp_ajax_cdaglobal_RetrieveEnvUrls', 'func_cdaglobal_RetrieveEnvUrls' );  
function func_cdaglobal_RetrieveEnvUrls(){ 
  $cdaglobalUrls = []; $envTok = $_POST['envTok']; 
  if($envTok == 'STAGE'){ 
    $cdaglobalUrls['assetUrl'] = CDAGLOBAL_ASSET_URL_STAGE;
    $cdaglobalUrls['footerUrl'] = CDAGLOBAL_FOOTER_URL_STAGE;
    $cdaglobalUrls['headerUrl'] = CDAGLOBAL_HEADER_URL_STAGE;        
  }
  //var_dump($cdaglobalUrls);
  if(!empty($cdaglobalUrls)){
	$response = json_encode($cdaglobalUrls);
	echo json_encode( array('success' => true, 'msg' => $response) );
  } else{ 
	$response = 'Error In Fetching Environment Urls'; 
	echo json_encode( array('success' => false, 'msg' => $response) );	      
  } 
  wp_die();   
}

/** Action to start fetching cda global data */
/** The function can be used can be at any stage*/ 
//add_action( 'wp_ajax_nopriv_cdaglobal_Fetch_Data', 'func_cdaglobal_Fetch_Data' );  
add_action( 'wp_ajax_cdaglobal_Fetch_Data', 'func_cdaglobal_Fetch_Data' );  
function func_cdaglobal_Fetch_Data(){ //var_dump($_POST); exit;
   $filepath = '';  
   //append a slash at the end of the string  
   $filepath = get_option('cdaglobal_fetchdir');  
   $fileDir = get_option('cdaglobal_fetchdir'); 
   if(substr($filepath, -1) != '/'){ 
   	 $filepath = $filepath.'/'; $fileDir = $fileDir.'/';
   }
   //if type is assetUrl, then create the assets file.  
   if(isset($_POST['type']) && $_POST['type'] == 'assetUrl'){
     $assetOpt = 'wp_cdaglobal/data/assets.html'; 
     $filepath .= $assetOpt;      
   }
   //if type is footerUrl, then create the footer file.    
   if(isset($_POST['type']) && $_POST['type'] == 'footerUrl'){       
     $footerOpt = 'wp_cdaglobal/data/footer.html'; 
     $filepath .= $footerOpt;            
   }
   //if type is headerUrl, then create the header file.    
   if(isset($_POST['type']) && $_POST['type'] == 'headerUrl'){  
     $headerOpt = 'wp_cdaglobal/data/header.html'; 
     $filepath .= $headerOpt;       
   }  
         
   //fetch the data from apiUrl
   $wpCurl = new WP_Http_Curl(); 
   $reqArray = $wpCurl->request($_POST['apiurl']);
   if(!is_wp_error($reqArray)){
     if(!empty($reqArray['body'])){ 
  	   try{  	   
     	  $fileDir .= 'wp_cdaglobal/data/';   		
		  if (!is_dir($fileDir)) { mkdir($fileDir, 0777, true); }
		  try{ 	   
		    file_put_contents($filepath, $reqArray['body']); 
		  }catch(Exception $e){ 
		    $response = $e->getMessage(); 
	        echo json_encode( array('success' => false, 'msg' => $response) ); 
	        wp_die();  		  
		  }  
	      $response = 'File Saved '.strtoupper($_POST['type']).' !!!'; 
	      if($_POST['type'] == 'assetUrl'){
	        update_option('cdaglobalassets_fetch', 'true'); 
	        update_option('cdaglobal_assets_endpointid', $_POST['apiurl']);
	        update_option('cdaglobal_assets_file', $assetOpt);	      
	      }
	      if($_POST['type'] == 'footerUrl'){
	        update_option('cdaglobalfooter_fetch', 'true'); 
	        update_option('cdaglobal_footer_endpointid', $_POST['apiurl']);
	        update_option('cdaglobal_footer_file', $footerOpt);	      
	      }	    
	      if($_POST['type'] == 'headerUrl'){
	        update_option('cdaglobalheader_fetch', 'true'); 
	        update_option('cdaglobal_header_endpointid', $_POST['apiurl']); 
	        update_option('cdaglobal_header_file', $headerOpt);	      
	      } 
	      
		  //change the env variable as per file present 
   		  if(strstr($_POST['apiurl'],'cda-global-stage')){
	 		update_option('cdaglobal_environment', 'STAGE'); 
   		  } else if(strstr($_POST['apiurl'],'cda-global-qa-01')){
	 		update_option('cdaglobal_environment', 'QA-1'); 
   		  } else if(strstr($_POST['apiurl'],'cda-global-qa-02')){
	 		update_option('cdaglobal_environment', 'QA-2');    
   		  } else if(strstr($_POST['apiurl'],'cda-global-qa-03')){
	 		update_option('cdaglobal_environment', 'QA-3');    
   		  } else { update_option('cdaglobal_environment', 'PROD'); }      	      
	     
	      echo json_encode( array('success' => true, 'msg' => $response) );  	     
  	   }catch(Exception $e){  
		  $response = $e->getMessage(); 
	      echo json_encode( array('success' => false, 'msg' => $response) );  	   
  	   }
     }else {
	     $response = 'File Could Not Be Fetched -'.strtoupper($_POST['type']).' !!!';
	     echo json_encode( array('success' => false, 'msg' => $response) );	 
     }
   }else{
	  $response = json_encode($reqArray->get_error_messages()); 
	  echo json_encode( array('success' => false, 'msg' => $response) );	      
   }   	 
   wp_die(); 
}  

---------------------------------------------------------------------------------------------
javascript
-----------
/**
 * CDA Global Footer Js
 */
jQuery(function($){ 
	var asseturl = opts.asseturl; 
	var footerurl = opts.footerurl; 
	var headerurl = opts.headerurl; 
	var embed_style = opts.embed_style;
	var headrepl = opts.replace_head;
	var footrepl = opts.replace_footer;
	
	$(document).ready(function(){ 
		//append head segment
		$('head').prepend(asseturl);
		//append header
		let docbody = $('body'); 
		let headerDiv = $(docbody).find(headrepl).first().css('display', 'none');
		$(headerurl).insertBefore(headerDiv);
		//append footer			
		let docfooter = $('footer').last(); 
		let footerdivs = $(docfooter).children();
	   	$.each(footerdivs, function(key, element){ 			
		   $(element).css('display', 'none'); 
		});
		$(docfooter).prepend(footerurl); 
	});

    
    if(embed_style == 'TOP-HEADER' || embed_style == 'TOP-HEADER-MOBILE'){
	 /**
	  * hovering on product reviews menu, generates the menu box 
	  * highlights the first menu item 
	  */ 
	 $(document).on('mouseover', 'span.gnav-sidebar__product-reviews--desktop', function(){ 
	   let prodReviewsDiv = $(this).parent().find('.gnav-sidebar__lists-wrapper');
	   $(prodReviewsDiv).addClass('d-block');
	   let menuitems = $(prodReviewsDiv).find('ul.gnav-sidebar__items-list').children();  
	   $.each(menuitems, function(key, element){ 
	     let anchor = $(element).find('a'); 
	     if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	   }); 	   
	   let divelements = $(prodReviewsDiv).find('div.gnav-sidebar__second-level-list');	   
	   $.each(divelements, function(key, element){ 
		 if($(element).hasClass('d-block')){ $(element).removeClass('d-block'); } 
	   }); 		   
	   $.each(divelements, function(key, element){ 
		 if($(element).attr('data-index') == 0){ 
		   $(element).addClass('d-block'); 
		   $(menuitems).find('a[data-index="0"]')
		  	 		   .css({"color":"#fff","text-decoration":"none","background-color":"#00ae4d"});
		   return false; 
		 } 
	   });
	 }); 
	}
	

    if(embed_style == 'TOP-HEADER' || embed_style == 'TOP-HEADER-MOBILE'){	
	 /**
	  * hovering on menuitems, 
	  * highlights the menu and displays the sub-menu items 
	  */  
	 $(document).on('mouseover', 'ul.gnav-sidebar__items-list > li', function(){
	   let element = $(this); 
	   let gnavsidebarMobile = element.parents('.gnav-sidebar').hasClass('d-block');
	   //for desktop 
       if(!gnavsidebarMobile) {	  
		 let menulisttop = $(element).parent();
	     let menuitemsli = $(menulisttop).children(); 
	  	 $.each(menuitemsli, function(key, element){ 
	    	let anchor = $(element).find('a'); 
	    	if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	  	 });
	  	 let menuIndx = $(element).find('a').attr('data-index');
	  	 let prodReviewDiv = $(element).parent().parent();
	  	 let divelements = $(prodReviewDiv).find('div.gnav-sidebar__second-level-list');	
	  	 $.each(divelements, function(key, element){ 
			if($(element).hasClass('d-block')){ $(element).removeClass('d-block'); } 
	  	 });  	  
	  	 $.each(divelements, function(key, element){ 
		   if($(element).attr('data-index') == menuIndx){ 
		     $(element).addClass('d-block'); 
		     $(element).parent().find('a[data-index="'+menuIndx+'"]')
		  	 		  .css({"color":"#fff","text-decoration":"none","background-color":"#00ae4d"});		  
		   } 
		   if(menuIndx == undefined){ 
		     if($(element).attr('data-index') == 7){ $(element).addClass('d-block'); } 
		     $(element).parent().find('a[data-index="7"]')
		  			  .css({"text-decoration":"none","background-color":"#cfcfcf"});		   
		   }
	  	  });
	    } else { //for mobile 
	     if( embed_style == 'TOP-HEADER-MOBILE' ){ 
	  	  let gnavsidebarTop = $(element).parents('.gnav-sidebar');
	  	  let toplevelListli = $(gnavsidebarTop).find('.gnav-sidebar__top-level-list').children();
	  	  $.each(toplevelListli, function(key, element){ 
		    if($(element).attr('style') !== undefined){ 
		      $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		    } 
	  	  }); 
	  	  let issueListli = $(gnavsidebarTop).find('.gnav__issues-links').children();
	  	  let issueListWrap = $(gnavsidebarTop).find('.gnav__issues-wrapper');	
	  	  $.each(issueListli, function(key, element){ 
		    if($(element).attr('style') !== undefined){ 
		      $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		    } 
	  	  });
	  	  if($(issueListWrap).find('.gnav__issues-label').attr('style') !== undefined){ 
	  	    $(issueListWrap).find('.gnav__issues-label').removeAttr('style');
	  	  }	    
		  let menulisttop = $(element).parent();
	      let menuitemsli = $(menulisttop).children(); 
	  	  $.each(menuitemsli, function(key, element){ 
	    	let anchor = $(element).find('a'); 
	    	if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	  	  });	  	 
	  	  let menuIndx = $(element).find('a').attr('data-index');
	  	  let prodReviewDiv = $(element).parent().parent();
	  	  let divelements = $(prodReviewDiv).find('div.gnav-sidebar__second-level-list');	
	  	  $.each(divelements, function(key, element){ 
			if($(element).hasClass('d-block')){ $(element).removeClass('d-block'); } 
	  	  });  	  
	  	  $.each(divelements, function(key, element){ 
		   if($(element).attr('data-index') == menuIndx){ 
		     $(element).addClass('d-block'); 
		     $(element).css({"left":"266px","transform":"none","font-size":"15px","padding-top":"0px"});
		     $(element).find('ul').css({'margin-bottom':"0px"});	
		     $(element).find('h3').css({'margin-top':"13px"});			    	    
		     $(element).parent().find('a[data-index="'+menuIndx+'"]')
		  			  .css({"color":"#fff","text-decoration":"none","background-color":"#00ae4d"});		  
		   } 
		   if(menuIndx == undefined){ 
		     if($(element).attr('data-index') == 7){ 
		    	$(element).addClass('d-block'); 
		    	$(element).css({"left":"266px","transform":"none","font-size":"15px","padding-top":"0px"});
		    	$(element).find('ul').css({'margin-bottom':"0px"});
		    	$(element).find('h3').css({'margin-top':"13px"});
		     } 
		     $(element).parent().find('a[data-index="7"]')
		  			  .css({"text-decoration":"none","background-color":"#cfcfcf"});		   
		   }
	  	  });	      
	     }//end if(embed_style == 'TOP-HEADER-MOBILE') 
	   } 	 
	 }); 
    }
    
    if(embed_style == 'TOP-HEADER' || embed_style == 'TOP-HEADER-MOBILE'){	    
     /**
      * Removes the menu after mouse exits
      */
	 $(document).on('mouseleave', '.gnav-sidebar__lists-wrapper', function(){ 
	   let prodReviewsDiv = $(this).parent().find('.gnav-sidebar__lists-wrapper');
	   $(prodReviewsDiv).removeClass('d-block');
	 });	

	 /**
	  * The following section happens only mobile version.
	  * On click of nav toggle, opens the sidebar
	  */
	 $(document).on('click', 'button.gnav__navbar-toggle', function(){ 
	   let btnNavToggle = $(this); 
	   let gnavTop = btnNavToggle.parents('.gnav-container'); 
	   gnavTop.find('div[id="navbarCollapse"]').addClass('d-block'); 
	   gnavTop.find('div[id="navbarOverlay"]').addClass('d-block'); 
	   let gnavSidebar = $(gnavTop).find('div[id="navbarCollapse"]');
	   let menuitemsli = $(gnavSidebar).find('.gnav-sidebar__items-list').children();	
	   $.each(menuitemsli, function(key, element){ 
	     let anchor = $(element).find('a'); 
	     if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	   }); 
	 });  

	 /**
	  * The following section happens only mobile version.
	  * On click of nav toggle, opens the sidebar
	  */
	 $(document).on('click', 'button.gnav-sidebar__close-btn', function(){ 
	   let btnNavClose = $(this); 
	   let gnavTop = btnNavClose.parents('.gnav-container'); 
	   gnavTop.find('div[id="navbarCollapse"]').removeClass('d-block'); 
	   gnavTop.find('div[id="navbarOverlay"]').removeClass('d-block'); 		  
	 });  
    }
    
    if(embed_style == 'TOP-HEADER-MOBILE'){	
	 /**
	  * hovering on other menuitems on mobile - not action page menus, 
	  * highlights the menu only.  
	  */  
	 $(document).on('mouseover', 'ul.gnav-sidebar__top-level-list > li', function(){ 
	   let element = $(this); 
	   let gnavsidebarMobile = element.parents('.gnav-sidebar').hasClass('d-block');
	   //for mobile
	   if(gnavsidebarMobile){ 
	  	 let gnavsidebarTop = $(element).parent().parent();
	  	 let divelements = $(gnavsidebarTop).find('div.gnav-sidebar__second-level-list');	
	  	 $.each(divelements, function(key, element){ 
		   if($(element).hasClass('d-block')){ $(element).removeClass('d-block'); } 
	  	 }); 
	  	 let menuitemsli = $(gnavsidebarTop).find('.gnav-sidebar__items-list').children();
	  	 $.each(menuitemsli, function(key, element){ 
	       let anchor = $(element).find('a'); 
	       if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	  	 });	
	  	 let issueListli = $(gnavsidebarTop).find('.gnav__issues-links').children();
	  	 let issueListWrap = $(gnavsidebarTop).find('.gnav__issues-wrapper');	
	  	 $.each(issueListli, function(key, element){ 
		   if($(element).attr('style') !== undefined){ 
		     $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		   } 
	  	 });
	  	 if($(issueListWrap).find('.gnav__issues-label').attr('style') !== undefined){ 
	  	   $(issueListWrap).find('.gnav__issues-label').removeAttr('style');
	  	 }	      	  	
	  	 let topLevelList = $(element).parent(); 
	  	 let topLevelListli = $(topLevelList).children(); 
	  	 $.each(topLevelListli, function(key, element){ 
		   if($(element).attr('style') !== undefined){ 
		     $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		   } 
	  	 });  	    
		 $(element).css({"background-color":"#00ae4d","padding":"10px 0 10px 10px"});
		 $(element).find('a').css({"text-decoration":"none","color":"#FFF"});			  
	   } 
	 });
	} 

    if(embed_style == 'TOP-HEADER-MOBILE'){	
	 /**
	  * hovering on action menuitems on mobile, 
	  * highlights the menu only as submenus are already displayed.  
	  */  
	 $(document).on('mouseover', 'ul.gnav__issues-links > li', function(){ 
	   let element = $(this); 
	   let gnavsidebarMobile = element.parents('.gnav-sidebar').hasClass('d-block');
	   //for mobile
	   if(gnavsidebarMobile){ 
	  	 let gnavsidebarTop = $(element).parents('.gnav-sidebar');
	  	 let divelements = $(gnavsidebarTop).find('div.gnav-sidebar__second-level-list');	
	  	 $.each(divelements, function(key, element){ 
		   if($(element).hasClass('d-block')){ $(element).removeClass('d-block'); } 
	  	 }); 
	  	 let menuitemsli = $(gnavsidebarTop).find('.gnav-sidebar__items-list').children();
	  	 $.each(menuitemsli, function(key, element){ 
	       let anchor = $(element).find('a'); 
	       if(anchor.attr('style') !== undefined){ anchor.removeAttr("style"); }
	  	 });
	  	 let toplevelListli = $(gnavsidebarTop).find('.gnav-sidebar__top-level-list').children();
	  	 $.each(toplevelListli, function(key, element){ 
		   if($(element).attr('style') !== undefined){ 
		     $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		   } 
	  	 }); 
	  	 let issueListli = $(gnavsidebarTop).find('.gnav__issues-links').children();
	  	 let issueListWrap = $(gnavsidebarTop).find('.gnav__issues-wrapper');	
	  	 $.each(issueListli, function(key, element){ 
		   if($(element).attr('style') !== undefined){ 
		     $(element).removeAttr('style'); $(element).find('a').removeAttr('style');
		   } 
	  	 });
	  	 if($(issueListWrap).find('.gnav__issues-label').attr('style') !== undefined){ 
	  	   $(issueListWrap).find('.gnav__issues-label').removeAttr('style');
	  	 }	    
		 $(element).css({"background-color":"#00ae4d","padding":"10px 0 10px 10px"});
		 $(element).find('a').css({"text-decoration":"none","color":"#FFF"}); 
	  	 $(issueListWrap).find('.gnav__issues-label')
	  					.css({"background-color":"#00ae4d","padding":"10px 0 10px 10px","color":"#FFF"});			  
	   } 
	 });
	} 

}); 
-------------------------------------
custom options page 
---------------------
<?php 
/**
 * A Custom Options Page For CdaGlobal. 
 */
add_action('admin_menu', 'cdaglobal_options_page');
function cdaglobal_options_page(){
	add_options_page( 
		__('CDA Global Options', 'wp_cdaglobal'),
		__('CDA Global Options', 'wp_cdaglobal'),
		'manage_options', 'cdaglobal-options',
		'cdaglobal_customized_settings' );
}

function cdaglobal_customized_settings() {
?>
<div class="wrap">
 <h1>CDA Global Settings</h1>
 <form method="POST" action="options.php">
 <?php
  settings_fields('cdaglobal-options');
  do_settings_sections('cdaglobal-options'); 
  submit_button();
 ?>
 </form>
</div> 
<?php
}


/** 
 * Functions to generate the cda-global options 
 * The settings will be generated on Settings > General.  
 */
add_action('admin_init', 'cdaglobal_general_settings'); 
function cdaglobal_general_settings() {    
  // CdaGlobal Settings Segment 
  add_settings_section('cdaglobal_settings_section', 
  						__('', 'wp_cdaglobal'),
					   'func_cdaglobal_settings_section', 
					   'cdaglobal-options');
						 
  // CdaGlobal Settings Field Segment 
  add_settings_field('cdaglobal_settings_field', 
  					__('CDA Global Setting Fields', 'wp_cdaglobal'),
   					 'func_cdaglobal_settings_field', 
   					 'cdaglobal-options', 
   					 'cdaglobal_settings_section');

  // CdaGlobal Settings Fields
  register_setting('cdaglobal-options', 'cdaglobal_assets_endpointid');
  register_setting('cdaglobal-options', 'cdaglobal_footer_endpointid');
  register_setting('cdaglobal-options', 'cdaglobal_header_endpointid');  
  register_setting('cdaglobal-options', 'cdaglobal_environment'); 
  register_setting('cdaglobal-options', 'cdaglobal_styleembed');   
  register_setting('cdaglobal-options', 'cdaglobal_domfield_header'); 
  register_setting('cdaglobal-options', 'cdaglobal_domfield_footer');      					 
}


/*============*
 * callbacks  *
 *============*/
/**
 * Generate the settings section  
 */  
function func_cdaglobal_settings_section() {
   echo '<ul class="cglobal-settings-title">';
   echo '<li>Settings For API Fetch From CDA Global</li>';
   echo '<li>Access header, assets, and footer from stage, prod, and other environments</li>';
   echo '</ul>';
}

/**
 * Generate the settings fields For Cda Global
 */
function func_cdaglobal_settings_field() {
    $cdaglobal_asset_endpoint = get_option('cdaglobal_assets_endpointid');
    $cdaglobal_footer_endpoint = get_option('cdaglobal_footer_endpointid');
    $cdaglobal_header_endpoint = get_option('cdaglobal_header_endpointid'); 
    $cdaglobal_environment = get_option('cdaglobal_environment'); 
    $cdaglobal_styleembed = get_option('cdaglobal_styleembed');
    $cdaglobal_domfield_header = get_option('cdaglobal_domfield_header'); 
    $cdaglobal_domfield_footer = get_option('cdaglobal_domfield_footer'); 
    
    // if STAGE for the environment is set
    if($cdaglobal_environment == 'STAGE'){ 
      if($cdaglobal_asset_endpoint == ''){ $cdaglobal_asset_endpoint = CDAGLOBAL_ASSET_URL_STAGE; }
      if($cdaglobal_footer_endpoint == ''){ $cdaglobal_footer_endpoint = CDAGLOBAL_FOOTER_URL_STAGE; }  
      if($cdaglobal_header_endpoint == ''){ $cdaglobal_header_endpoint = CDAGLOBAL_HEADER_URL_STAGE; } 
    }
  	if($cdaglobal_styleembed == ''){ $cdaglobal_styleembed = 'NO-STYLE'; } 
    if($cdaglobal_domfield_header == ''){ $cdaglobal_domfield_header = 'header'; }
    if($cdaglobal_domfield_footer == ''){ $cdaglobal_domfield_footer = 'footer'; }    
    // display settings page
    ?>
   <!-- cglobal-settings --> 
   <div class="cglobal-settings">
	<div class="env-fields">
      <div class="env-field-list">
        <div class="left-block"><label for="cdaglobal_environment">Environment:</label></div>
        <div class="right-block">      	
		  <label for="stage">Stage</label>
		  <input type="radio" id="stage" name="cdaglobal_environment" value="STAGE" 
		  	<?php if($cdaglobal_environment == 'STAGE'){?> checked="true" <?php } ?>/> 
		  <label for="stage">QA-1</label>
		  <input type="radio" id="qa-1" name="cdaglobal_environment" value="QA-1" 
		  	<?php if($cdaglobal_environment == 'QA-1'){?> checked="true" <?php } ?>/> 				  		
 		  <label for="qa-2">QA-2</label>
 		  <input type="radio" id="qa-2" name="cdaglobal_environment" value="QA-2"
 		  	<?php if($cdaglobal_environment == 'QA-2'){?> checked="true" <?php } ?>/>	
 		  <label for="qa-3">QA-3</label>
 		  <input type="radio" id="qa-3" name="cdaglobal_environment" value="QA-3" 
 		    <?php if($cdaglobal_environment == 'QA-3'){?> checked="true" <?php } ?>/>
		  <label for="prod">Prod</label>
		  <input type="radio" id="prod" name="cdaglobal_environment" value="PROD"
		  	<?php if($cdaglobal_environment == 'PROD'){?> checked="true" <?php } ?>/>
		</div> 
	  </div>	 
    </div>
    <div class="settings-fields">
      <div class="top-field">
        <div class="left-block"><label for="cdaglobal_assets_endpointid">Asset API:</label></div>
        <div class="right-block">
        <input type="text" name="cdaglobal_assets_endpointid" style="width: 50%;margin: 5px 0;"
    	   value="<?php echo isset( $cdaglobal_asset_endpoint ) ? esc_attr( $cdaglobal_asset_endpoint ) : ''; ?>"/>
	    <button type="button" class="cglobal-assetfile-status" id="cglobal-assetfile-status"></button>
	    </div>
	  </div>
	  <div class="mid-field"> 
	    <div class="left-block"><label for="cdaglobal_footer_endpointid">Footer API:</label></div>
	    <div class="right-block">  	
        <input type="text" name="cdaglobal_footer_endpointid" style="width: 50%;margin: 5px 0;"
    	   value="<?php echo isset( $cdaglobal_footer_endpoint ) ? esc_attr( $cdaglobal_footer_endpoint ) : ''; ?>"/>
        <button type="button" class="cglobal-footerfile-status" id="cglobal-footerfile-status"></button>
        </div>
      </div>
      <div class="lower-field">
        <div class="left-block"><label for="cdaglobal_header_endpointid">Header API:</label></div>
        <div class="right-block">  	
        <input type="text" name="cdaglobal_header_endpointid" style="width: 50%;margin: 5px 0;"
    	   value="<?php echo isset( $cdaglobal_header_endpoint ) ? esc_attr( $cdaglobal_header_endpoint ) : ''; ?>"/>
        <button type="button" class="cglobal-headerfile-status" id="cglobal-headerfile-status"></button>
        </div>
      </div>       
    </div>      
	<div class="settings-action-buttons">
	  <button type="button" class="cglobal-refetch" id="cglobal-refetch">Re-Generate</button>
	  <p class="messages"></p>      
    </div>
   </div>
   <!-- /cglobal-settings -->
   <hr style="margin-top:20px;"/>
   <!-- cglobal-settings-dom-style -->   
   <div class="cglobal-settings-dom-style">
    <div class="top-block"><label for="top-block">Style & DOM Settings For Embed Segments From API:</label></div>   
	<div class="style-embed-fields">
      <div class="embed-field-list">
        <div class="left-block"><label for="cdaglobal_styleembed">Style Formats:</label></div>
        <div class="right-block">      	
		  <label for="no-style">No Style</label>
		  <input type="radio" id="no-style" name="cdaglobal_styleembed" value="NO-STYLE" 
		  	<?php if($cdaglobal_styleembed == 'NO-STYLE'){?> checked="true" <?php } ?>/>
 		  <label for="top-header">Top Header Only</label>
 		  <input type="radio" id="top-header" name="cdaglobal_styleembed" value="TOP-HEADER"
 		  	<?php if($cdaglobal_styleembed == 'TOP-HEADER'){?> checked="true" <?php } ?>/>
 		  <label for="top-header-mobile">Top Header & Mobile</label>
 		  <input type="radio" id="top-header-mobile" name="cdaglobal_styleembed" value="TOP-HEADER-MOBILE" 
 		    <?php if($cdaglobal_environment == 'TOP-HEADER-MOBILE'){?> checked="true" <?php } ?>/>
		</div> 
	  </div>	 
    </div>
	<div class="dom-fields">
      <div class="dom-field-header">
        <div class="left-block"><label for="cdaglobal_domfield_header">DOM Node To Replace (Header) - ex: header</label></div>
        <div class="right-block">      	
        <input type="text" name="cdaglobal_domfield_header" style="width: 50%;margin: 5px 0;"
    	   value="<?php echo isset( $cdaglobal_domfield_header ) ? esc_attr( $cdaglobal_domfield_header ) : ''; ?>"/>
		</div>
	  </div>
      <div class="dom-field-footer">
        <div class="left-block"><label for="cdaglobal_domfield_footer">DOM Node To Replace (Footer) - ex:footer</label></div>
        <div class="right-block">      	
        <input type="text" name="cdaglobal_domfield_footer" style="width: 50%;margin: 5px 0;"
    	   value="<?php echo isset( $cdaglobal_domfield_footer ) ? esc_attr( $cdaglobal_domfield_footer ) : ''; ?>"/>
		</div>
	  </div>
    </div>  
   <!-- /cglobal-settings-dom-style --> 
   <hr style="margin-top:20px;"/>

<script>
jQuery(function($){ 
	var $admin_ajaxurl = ajaxurl; //alert($admin_ajaxurl); return false;
	var spinner = "<?php echo admin_url('images/loading.gif'); ?>"; 
	var mainparent = ''; var messages = ''; var filelist = ''; 

    $(document).ready(function(){
      console.log('Cda Global Initial Check');  
      mainparent = $('div.cglobal-settings'); 
      let actionBtnsDiv = $('div.cglobal-settings').find('.settings-action-buttons'); 
      messages = $(actionBtnsDiv).find('.messages'); 
      
      //on startup - checks whether files exist      
      let checkFileData = checkDataFetchFromUrlOnStartUp();
      //if assets file does not exist, then generate assets
      let generateAssetFile = checkFileData.then(function(result){  
        filelist = result; 
        if(filelist.assets == 0){
      	  let assetUrl = $(mainparent).find('input[name="cdaglobal_assets_endpointid"]').val();
      	  console.log('Fetching From Url- '+ assetUrl); 
      	  return generateCDAGlobalFile('assetUrl', assetUrl);
      	}	
      }).catch((error) =>{ 
      	 const obj = JSON.parse(error.msg); 
      	 messages.find('img').remove(); messages.find('span').remove();  
      	 messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>'); 
      });        
      //if footer file does not exist, then generate footer      
      let generateFooterFile = generateAssetFile.then(function(result){  
        console.log(filelist);
        if(filelist.footer == 0){
      	  let footerUrl = $(mainparent).find('input[name="cdaglobal_footer_endpointid"]').val();
      	  console.log('Fetching From Url- '+ footerUrl); 
      	  return generateCDAGlobalFile('footerUrl', footerUrl);
      	}
      }).catch((error) => {
      	 const obj = JSON.parse(error.msg); 
      	 messages.find('img').remove(); messages.find('span').remove();  
      	 messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
      });       
      //if header file does not exist, then generate header              
      let generateHeaderFile = generateFooterFile.then(function(result){  
        console.log(filelist);
        if(filelist.header == 0){
      	  let headerUrl = $(mainparent).find('input[name="cdaglobal_header_endpointid"]').val();
      	  console.log('Fetching From Url- '+ headerUrl); 
      	  return generateCDAGlobalFile('headerUrl', headerUrl);
      	}
      }).catch((error) => { 
      	 const obj = JSON.parse(error.msg); 
      	 messages.find('img').remove(); messages.find('span').remove();  
      	 messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
	  });
	  //After all files have been pulled from API      
      let generateHeaderFileFinal = generateHeaderFile.then(function(result){ 
        console.log('All Files Generated'); console.log(filelist); 
      }).catch((error) => { 
      	 const obj = JSON.parse(error.msg); 
      	 messages.find('img').remove(); messages.find('span').remove();  
      	 messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
      });
   });

   /**
    * Action for clicking on "Re-Generate" button 
    */ 
    $('button.cglobal-refetch').click(function(){
    	console.log('Regenerate Header & Footer'); 
        mainparent = $('div.cglobal-settings'); 
        let actionBtnsDiv = $(mainparent).find('.settings-action-buttons'); 
        messages = $(actionBtnsDiv).find('.messages');
		//check the option that is checked.        
		let envfieldlist = $(mainparent).find('div.env-field-list');
		let envOpt = $(envfieldlist).find('input[type="radio"]:checked').val();
		console.log('Will Update From Env: ',envOpt); 

        //fetches the respective urls based on Environment      
        let fetchFileDataForEnvOpt = fetchEnvUrlsForEnvToken(envOpt);
       
        //refetch the assets file, then generate assets        
      	let generateAssetFile = fetchFileDataForEnvOpt.then(function(result){ 
      	   filelist.assets = 0; console.log(filelist);
      	   let assetUrl = result.assetUrl; 	
      	   $(mainparent).find('.cglobal-assetfile-status').removeClass('found').addClass('not-found');
      	   $(mainparent).find('input[name="cdaglobal_assets_endpointid"]').val(assetUrl);  
      	   console.log('Fetching From Url- '+ assetUrl); 
      	   let returnRes = generateCDAGlobalFile('assetUrl', assetUrl)
      	   .then(function(res){ 
      	   	  let obj = "Fetched From AssetUrl. File Generated";
      	   	  messages.find('img').remove(); messages.find('span').remove();  
      	   	  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>'); 
      	   	  return result;       	   
      	   })
      	   .catch((error) => { 
      	     let obj = '';
      	     try{ obj = JSON.parse(error.msg); }catch{ /*if not json*/ obj = error; } 
      	     messages.find('img').remove(); messages.find('span').remove();  
      	     messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
      	   });
        });   
           	
        //refresh the footer file, then generate footer      
      	let generateFooterFile = fetchFileDataForEnvOpt.then(function(result){ 
		   filelist.footer = 0; console.log(filelist);
      	   let footerUrl = result.footerUrl;
      	   $(mainparent).find('.cglobal-footerfile-status').removeClass('found').addClass('not-found');
      	   $(mainparent).find('input[name="cdaglobal_footer_endpointid"]').val(footerUrl);  
      	   console.log('Fetching From Url- '+ footerUrl); 
      	   let returnRes = generateCDAGlobalFile('footerUrl', footerUrl)
      	   .then(function(res){ 
      	   	  let obj = "Fetched From FooterUrl. File Generated";
      	   	  messages.find('img').remove(); messages.find('span').remove();  
      	   	  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>'); 
      	   	  return result;       	   
      	   })
      	   .catch((error) => { 
      	     let obj = '';
      	     try{ obj = JSON.parse(error.msg); }catch{ /*if not json*/ obj = error; } 
      	     messages.find('img').remove(); messages.find('span').remove();  
      	     messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
      	   });
        });  
             	
        //refresh the header file, then generate header      
      	let generateHeaderFile = fetchFileDataForEnvOpt.then(function(result){  
		   filelist.header = 0; console.log(filelist);
      	   let headerUrl = result.headerUrl; 	
      	   $(mainparent).find('.cglobal-headerfile-status').removeClass('found').addClass('not-found');
      	   $(mainparent).find('input[name="cdaglobal_header_endpointid"]').val(headerUrl);  
      	   console.log('Fetching From Url- '+ headerUrl); 
      	   let returnRes = generateCDAGlobalFile('headerUrl', headerUrl)
      	   .then(function(res){ 
      	   	  let obj = "Fetched From HeaderUrl. File Generated";
      	   	  messages.find('img').remove(); messages.find('span').remove();  
      	   	  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>'); 
      	   	  return result;       	   
      	   })
      	   .catch((error) => { 
      	     let obj = '';
      	     try{ obj = JSON.parse(error.msg); }catch{ /*if not json*/ obj = error; } 
      	     messages.find('img').remove(); messages.find('span').remove();  
      	     messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+ obj +'</span>');
      	   });
      	}); 
    });   


   /**
    * Checks whether data from Url had been fetched earlier
    */
   function checkDataFetchFromUrlOnStartUp(){ 
	 return new Promise(function(resolve, reject){ 
	    /* check the file status */
	    $.ajax({
			url: $admin_ajaxurl, 
        	method: 'GET',
        	data: {
				'action' : 'cdaglobal_TestSystem_OnStartup', 
			},
        	dataType: "json", 
        	beforeSend: function(){
	      		messages.find('img').remove(); messages.find('span').remove();
	      		messages.append('<img src="'+ spinner +'" width=12 height=12 style="padding:0 3px 0 0;"/><span style="font-size:12px;color:#000; font-weight:600;">Checking...Earlier API Fetch.</span>');
	    	},
	    	success: function(response){ //console.log(response);
		  		if(response.success){ 
		    		//add not found class if not present
		    		if(response.assets == 0){ mainparent.find('.cglobal-assetfile-status').addClass('not-found'); }
		    		if(response.footer == 0){ mainparent.find('.cglobal-footerfile-status').addClass('not-found'); }
		    		if(response.header == 0){ mainparent.find('.cglobal-headerfile-status').addClass('not-found'); } 
		    		if(response.assets == 0 && response.footer == 0 && response.header == 0){
		    		  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">...Not Found.</span>'); 		    		    		}
		    		//add found class if fetched
		    		if(response.assets == 1){ mainparent.find('.cglobal-assetfile-status').addClass('found'); }
		    		if(response.footer == 1){ mainparent.find('.cglobal-footerfile-status').addClass('found'); }
		    		if(response.header == 1){ mainparent.find('.cglobal-headerfile-status').addClass('found'); } 
		    		if(response.assets == 1 && response.footer == 1 && response.header == 1){
		    		  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">...Found.</span>'); 		    		    					} 
		  		}else{ 
		  			messages.append('<span style="font-size:12px;color:#000; font-weight:600;">Error: Checking Error</span>'); 
		  			console.log(response);
		  			reject(response.responseJSON);
		  		}
	    	},
	    	error: function(xhr, status, error){
	        	//var err = eval("(" + xhr.responseText + ")"); console.log(err.Message);
	        	console.log(xhr); console.log(status); console.log(error); 
	    	},
	    	complete: function(response){				    
				setTimeout(function(){ 
           			messages.find('img').remove(); messages.find('span').remove(); 
           		}, 3000); 
           		resolve(response.responseJSON);	
            },
	 	}); // end of ajax 
	 }); // end promise   
   }

   /**
    * Checks whether data from Url had been fetched earlier
    */
   function generateCDAGlobalFile(type, apiurl){ 
	 return new Promise(function(resolve, reject){ 
		/* generate the respective files */
		$.ajax({
			url: $admin_ajaxurl, 
        	method: 'POST',
        	data: {
        		'apiurl': apiurl,
        		'type': type,
				'action': 'cdaglobal_Fetch_Data', 
			},
        	dataType: "json",
        	beforeSend: function(){
	      	  messages.find('img').remove(); messages.find('span').remove();
	      	  messages.append('<img src="'+ spinner +'" width=12 height=12 style="padding:0 3px 0 0;"/><span style="font-size:12px;color:#000; font-weight:600;">Generating Data From...'+type+'</span>');
	    	},        	 
	    	success: function(response){ //console.log(response);
		  		if(response.success){ 
		    	  messages.find('img').remove(); messages.find('span').remove();
				  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">'+response.msg+'</span>'); 
		    	  if(type =='assetUrl'){ mainparent.find('.cglobal-assetfile-status').addClass('found'); filelist.assets = 1; }  
		    	  if(type =='footerUrl'){ mainparent.find('.cglobal-footerfile-status').addClass('found'); filelist.footer = 1; } 
		    	  if(type =='headerUrl'){ mainparent.find('.cglobal-headerfile-status').addClass('found'); filelist.header = 1; }	
		  		}else{ 
		    	  messages.find('img').remove(); messages.find('span').remove();
				  messages.append('<span style="font-size:12px;color:#FFF; font-weight:600;">'+response.msg+'</span>'); 
		    	  if(type =='assetUrl'){ mainparent.find('.cglobal-assetfile-status').addClass('not-found'); filelist.assets = 0; }  
		    	  if(type =='footerUrl'){ mainparent.find('.cglobal-footerfile-status').addClass('not-found'); filelist.footer = 0; } 
		    	  if(type =='headerUrl'){ mainparent.find('.cglobal-headerfile-status').addClass('not-found'); filelist.header = 0; } 
		    	  reject(response);		  					
		  		}
	    	},
	    	error: function(xhr, status, error){
	        	var err = eval("(" + xhr.responseText + ")"); console.log(err.Message);
	    	},
	    	complete: function(response){
	   			setTimeout(function(){
	        		messages.find('img').remove(); messages.find('span').remove(); 
		    	}, 3500); 
		    	resolve(response.responseJSON);		    			  
	    	},
	 	}); // end of ajax
     }); // end of promise
   }  

   /**
    * Fetch Respective Api Urls for a Given Environment
    */
   function fetchEnvUrlsForEnvToken(envTok){ 
	 return new Promise(function(resolve, reject){ 
	    /* check the file status */
	    $.ajax({
			url: $admin_ajaxurl, 
        	method: 'POST',
        	data: {
        		'envTok': envTok,
				'action' : 'cdaglobal_RetrieveEnvUrls', 
			},
        	dataType: "json", 
        	beforeSend: function(){
	      		messages.find('img').remove(); messages.find('span').remove();
	      		messages.append('<img src="'+ spinner +'" width=12 height=12 style="padding:0 3px 0 0;"/>
                            <span style="font-size:12px;color:#000; font-weight:600;">Retrieving Files Based On Environment...</span>');
	    	},
	    	success: function(response){ 
 		  		if(response.success){ 
 		  		  let responseObj = JSON.parse(response.msg);
		    	  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">...Not Found.</span>');
		    	  resolve(responseObj);		    	    
		  		}else{ 
		  		  messages.append('<span style="font-size:12px;color:#000; font-weight:600;">');
		  		  messages.append('Error: Could Not Fetch CdaGlobal Urls</span>'); 
		  		  reject(response.responseJSON);
		  		}
	    	},
	    	error: function(xhr, status, error){
	        	var err = eval("(" + xhr.responseText + ")"); console.log(err.Message); 
	    	},
	    	complete: function(response){				    
				/*setTimeout(function(){ 
           			messages.find('img').remove(); messages.find('span').remove(); 
           		}, 3000);*/
            },
	 	}); // end of ajax 
	 }); // end promise   
   }

});
</script>
<?php
}

  
  
  
  

