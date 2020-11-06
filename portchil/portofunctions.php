<?php 

/**
 * Actions for Generation of Different Dropdowns Beginning From Step 1
 *
 * @author Capital Numbers Infotech 
 */

/** 
 * Action for Selection of Material On Selection of Brand 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_material', 'porto_child_fetch_material' );  
add_action( 'wp_ajax_porto_child_fetch_material', 'porto_child_fetch_material' );  
function porto_child_fetch_material() { 
    $brandValue = $_POST['brandVal'];
    $materialhtml = ''; $materialArr = array();

    //generate query using brandval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($brandValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $products = new WP_Query($args); //var_dump($products); exit;

	$materialhtml .= '<option value="">Select Material</option>';
    foreach($products->posts as $product){ 
      $materials = wp_get_post_terms( $product->ID, 'pa_material', array('orderby'=>'name',) );
      // create material html 
      foreach( $materials as $material ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($material->term_id, $materialArr)){
	   array_push($materialArr, $material->term_id);
	   $materialhtml .= '<option value="'.$material->slug.'">'.$material->name.'</option>';
	}
      }
    } 
    //var_dump($materialhtml); exit;

    //return material html
    $response = $materialhtml;
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** 
 * Action for Selection of Color On Selection of Material 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_color', 'porto_child_fetch_color' );  
add_action( 'wp_ajax_porto_child_fetch_color', 'porto_child_fetch_color' );  
function porto_child_fetch_color() { 
    $brandValue = $_POST['brandVal']; $materialValue = $_POST['materialVal'];
    $colorhtml = ''; $colorArr = array();

    //generate query using brandval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($brandValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'pa_material', 'field' => 'slug', 'terms' =>  array($materialValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $products = new WP_Query($args); //var_dump($products); exit;

    $colorhtml .= '<option value="">Select Color</option>';
    foreach($products->posts as $product){ 
      $colors = wp_get_post_terms( $product->ID, 'pa_color', array('orderby'=>'name',) ); 
      // create color html 
      foreach( $colors as $color ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($color->term_id, $colorArr)){
	   array_push($colorArr, $color->term_id);
	   $colorhtml .= '<option value="'.$color->slug.'">'.$color->name.'</option>';
	}
      }
    } 
    //var_dump($colorhtml); exit;

    //return material html
    $response = $colorhtml;
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** 
 * Action for Selection of Cleaner On Selection of Color 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_cleaner', 'porto_child_fetch_cleaner' );  
add_action( 'wp_ajax_porto_child_fetch_cleaner', 'porto_child_fetch_cleaner' );  
function porto_child_fetch_cleaner() { 
    $brandValue = $_POST['brandVal']; $materialValue = $_POST['materialVal'];
    $colorValue = $_POST['colorVal'];
    $cleanerhtml = ''; $cleanerArr = array();

    //generate query using brandval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($brandValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'pa_material', 'field' => 'slug', 'terms' =>  array($materialValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_color', 'field' => 'slug', 'terms' =>  array($colorValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $products = new WP_Query($args); //var_dump($products); exit;

	$cleanerhtml .= '<option value="">Select Cleaner</option>';
    foreach($products->posts as $product){ 
      $cleaners = wp_get_post_terms( $product->ID, 'pa_cleaner', array('orderby'=>'name',) ); 
      // create cleaner html 
      foreach( $cleaners as $cleaner ){      
	//if cleaner is not in array, create the option for dropdown
	if(!in_array($cleaner->term_id, $cleanerArr)){
	   array_push($cleanerArr, $cleaner->term_id);
	   $cleanerhtml .= '<option value="'.$cleaner->slug.'">'.$cleaner->name.'</option>';
	}
      }
    } 
    //var_dump($cleanerhtml); exit;

    //return material html
    $response = $cleanerhtml;
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** 
 * Action for Selection of Curing On Selection of Cleaner 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_curing', 'porto_child_fetch_curing' );  
add_action( 'wp_ajax_porto_child_fetch_curing', 'porto_child_fetch_curing' );  
function porto_child_fetch_curing() { 
    $brandValue = $_POST['brandVal']; $materialValue = $_POST['materialVal'];
    $colorValue = $_POST['colorVal']; $cleanerValue = $_POST['cleanerVal'];
    $curinghtml = ''; $curingArr = array(); 

    //generate query using brandval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($brandValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'pa_material', 'field' => 'slug', 'terms' =>  array($materialValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_color', 'field' => 'slug', 'terms' =>  array($colorValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_cleaner', 'field' => 'slug', 'terms' =>  array($cleanerValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $products = new WP_Query($args); //var_dump($products); exit;

	$curinghtml .= '<option value="">Select Curing</option>';
    foreach($products->posts as $product){ 
      $curingmethods = wp_get_post_terms( $product->ID, 'pa_curing-method', array('orderby'=>'name',) ); 
      // create curing methods html 
      foreach( $curingmethods as $curingmethod ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($curingmethod->term_id, $curingArr)){
	   array_push($curingArr, $curingmethod->term_id);
	   $curinghtml .= '<option value="'.$curingmethod->slug.'">'.$curingmethod->name.'</option>';
	}
      }
    } 
    //var_dump($cleanerhtml); exit;

    //return material html
    $response = $curinghtml;
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** 
 * Action for Selection of PDF On Selection of Cleaner 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_pdf', 'porto_child_fetch_pdf' );  
add_action( 'wp_ajax_porto_child_fetch_pdf', 'porto_child_fetch_pdf' );  
function porto_child_fetch_pdf() { 
    $brandValue = $_POST['brandVal']; $materialValue = $_POST['materialVal'];
    $colorValue = $_POST['colorVal']; $cleanerValue = $_POST['cleanerVal'];
    $curingValue = $_POST['curingVal']; $html = '';  

  if(!empty($brandValue) && !empty($materialValue) && !empty($colorValue) && !empty($cleanerValue) && !empty($curingValue)){
    //generate query using brandval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($brandValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'pa_material', 'field' => 'slug', 'terms' =>  array($materialValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_color', 'field' => 'slug', 'terms' =>  array($colorValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_cleaner', 'field' => 'slug', 'terms' =>  array($cleanerValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_curing-method', 'field' => 'slug', 'terms' =>  array($curingValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $products = new WP_Query($args); //var_dump($products); exit;

    if( !$products->have_posts() ){
	$html .= 'Error: Product For The Following Combination Not Found-';
	$html .= 'Printer: '.$brandValue.' Material: '.$materialValue.' Color: '.$colorValue;
	$html .= 'Cleaners: '.$cleanerValue.' Curing Method: '.$curingValue; 
        $response = $html;
        echo json_encode( array('success' => false, 'msg' => $response) );
        wp_die();
    }else{
	//the first product will be considered if there are many.
	$product = $products->posts[0]; 
	//check if product display pdf value is set to Y 
	$displayPDF = get_post_meta($product->ID, 'display_pdf', true); 
	if($displayPDF == 'N' || empty($displayPDF)){
	   $html .= 'Data Not Available - Contact OEM For Printer Settings And Workflow Details.';
           $response = $html;
           echo json_encode( array('success' => false, 'msg' => $response) );
           wp_die();
	} else{ 
	   $displayPDFLink = get_post_meta($product->ID, 'display_pdf_link', true); 
	   $html .= $displayPDFLink;
           $response = $html;
           echo json_encode( array('success' => true, 'msg' => $response) );
           wp_die();
	}
     }
   }else{
      $html .= 'Error: All Steps Are Required To Select Printers.';
      $response = $html;
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
   }
}

/** 
 * Action for Selection of PDF On Selection of Cleaner 
 */
add_action( 'wp_ajax_nopriv_porto_child_fetch_validation_material', 'porto_child_fetch_validation_material' );  
add_action( 'wp_ajax_porto_child_fetch_validation_material', 'porto_child_fetch_validation_material' );  
function porto_child_fetch_validation_material() { 
  $validationValue = $_POST['validationVal']; $html = ''; //var_dump($validationValue);

  if(!empty($validationValue)){
    //get all terms like validation value
    $validationValues = array(); 
    $v_args = array('taxonomy' => 'pa_validated-resins', 'hide_empty' => true); $v_terms = get_terms( $v_args ); 
    foreach($v_terms as $term){ if(strstr($term->slug, $validationValue)){ array_push($validationValues, $term->slug); }}
    //var_dump($validationValues); //exit;

    //get all terms like pending values
    $pendingValues = array();
    $p_args = array('taxonomy' => 'pa_pending-resins', 'hide_empty' => true); $p_terms = get_terms( $p_args ); 
    foreach($p_terms as $term){ if(strstr($term->slug, $validationValue)){ array_push($pendingValues, $term->slug); }}
    //var_dump($pendingValues); exit;

    //generate query using validationval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    //$tax_query[] = array('taxonomy' => 'pa_validated-resins', 'field' => 'slug', 'terms' =>  array($validationValue), 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'pa_validated-resins', 'field' => 'slug', 'terms' =>  $validationValues, 'operator' => 'IN',); 
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printers'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $validated_products = new WP_Query($args); $tax_query = []; $args = array(); //var_dump($validated_products); //exit;

    //if($validated_products->have_posts()){ $vhtml = '';
    //  foreach($validated_products->posts as $product){ $vhtml .= $product->ID.','; } 
    //  echo $vhtml;
    //}

    //generate query using validationval 
    $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
    //$tax_query[] = array('taxonomy' => 'pa_pending-resins', 'field' => 'slug', 'terms' =>  array($validationValue), 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'pa_pending-resins', 'field' => 'slug', 'terms' =>  $pendingValues, 'operator' => 'IN',);
    $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printers'), 'operator' => 'IN',);
    $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query; 
    $pending_products = new WP_Query($args); $tax_query = []; $args = array(); //var_dump($pending_products); exit;

    //if($pending_products->have_posts()){ $phtml = '';
    //  foreach($pending_products->posts as $product){ $phtml .= $product->ID.','; }
    //  echo $phtml;
    //}

    if( !$validated_products->have_posts() && !$pending_products->have_posts() ){
	$html .= 'Error: Product For The Following Combination Not Found-';
	$html .= 'Printer: '.$brandValue.' Material: '.$materialValue.' Color: '.$colorValue;
	$html .= 'Cleaners: '.$cleanerValue.' Curing Method: '.$curingValue; 
        $response = $html;
        echo json_encode( array('success' => false, 'msg' => $response) );
        wp_die();
    }else{
	//get the ids of each products
	$html = ''; $cproducts = array();
	//check validated and pending products
	if($validated_products->have_posts()){ 
	  foreach($validated_products->posts as $product){ 
	    if(!in_array($product->ID, $cproducts)){ array_push($cproducts, $product->ID); $html .= $product->ID.','; }
	  }
	}
	if($pending_products->have_posts()){	
	  foreach($pending_products->posts as $product){ 
	    if(!in_array($product->ID, $cproducts)){ array_push($cproducts, $product->ID); $html .= $product->ID.','; }
	  }
	} //var_dump($html); exit;
        $response = $html;
        echo json_encode( array('success' => true, 'msg' => $response) );
        wp_die();	
     }

   }else{ // will reset the filter

      $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', );
      $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printers'), 'operator' => 'IN',);
      $args['tax_query'] = $tax_query;
      $all_products = new WP_Query($args); $tax_query = []; $args = array(); //var_dump($all_products); //exit;
      foreach($all_products->posts as $product){ $html .= $product->ID.','; }
      $response = $html;
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();	

      //$html .= 'Error: Selection Of Material Is Required.';
      //$response = $html;
      //echo json_encode( array('success' => false, 'msg' => $response) );
      //wp_die();
   }
}


/** ===========================================================================*
 *  Function for placing logo image for products
 * ============================================================================*/
/** Add Metaboxes to WPRSS FEED SOURCE to display the default thumbnails.*/
add_action( 'post_edit_form_tag' , 'post_edit_form_tag' );
function post_edit_form_tag( ) {
  global $post; 
  $postType = get_post_type( $post->ID ); 
  //if post type == product
  if( $postType == 'product' ){ 
	echo ' enctype="multipart/form-data"';
  }
}
add_action( 'add_meta_boxes', 'product_default_logo_image' );
function product_default_logo_image(){ 
  add_meta_box('custom_admin_image_mbox', __( 'Logo Image For Product', 'woocommerce' ),
               'product_show_logo_image_callback', 'product', 'normal', 'low' ); 
}
function product_show_logo_image_callback(){
  global $post; $html = '';
  // display the uploader form 
  $html .= '<div><p><span><b>Select A Logo Image: </b></span>';
  $html .= '<input type="file" name="product_logoimg" id="product_logoimg" /></p>';
  $html .= '<p><strong>Note: Image Size Should Be Less Than 1 MB - Upload JPG, PNG image</strong></p>';
  $html .= '</div>';
  $selImageID = get_post_meta($post->ID, 'admin_product_logoimg', true);
  if($selImageID != ''){ 
     $arr_existing_image = wp_get_attachment_image_src($selImageID, 'large');
     $existing_image_url = $arr_existing_image[0];
     $html .= '<div><img src="'.$existing_image_url.'" style="width:200px;height:auto;" /></div>'; 
  } 
  echo $html;
}
add_action('save_post', 'product_logo_image_save');
function product_logo_image_save(){ //var_dump($_POST); exit;
  global $post;  
  if ( ! empty($post) ) {
    $postType = $_POST['post_type']; $postAction = $_POST['action'];
    if( $postType == 'product' && $postAction == 'editpost' ){ 
      if( !empty($_FILES["product_logoimg"]) ){
	 //if filesize is less than 1 MB, upload logo 
	 if($_FILES["product_logoimg"]['size'] < 1000000){ 
	    //upload file directly to uploads folder
	    $upload_overrides = array( 'test_form' => false );
	    $uploaded_file = wp_handle_upload( $_FILES["product_logoimg"], $upload_overrides ); 
	    if(isset($uploaded_file['file'])) { 
	       $product_logofile = $uploaded_file['file']; 
	       $product_default_title = 'Logo Image For '.$_POST["post_title"];
	       $attachment = array(
		     'post_mime_type' => $_FILES["product_logoimg"]['type'],
                     'post_title' => $product_default_title,
                     'post_content' => '',
                     'post_status' => 'inherit' );
               $attach_id = wp_insert_attachment( $attachment, $product_logofile );
               require_once(ABSPATH . "wp-admin" . '/includes/image.php');
               $attach_data = wp_generate_attachment_metadata( $attach_id, $product_logofile );
               wp_update_attachment_metadata($attach_id,  $attach_data);
               $product_prevImage = (int) get_post_meta($post->ID, 'admin_product_logoimg', true);
               if(is_numeric($product_prevImage)) { wp_delete_attachment($product_prevImage); }
	       update_post_meta($post->ID,'admin_product_logoimg',$attach_id);
	    } // end if isset($uploaded_file['file']) 
         } else { // end if($_FILES["product_logoimg"]['size'] < 1000000)
	    add_action( 'admin_notices', function(){  
		$html = '<div class="notice notice-error is-dismissible">';
		$html .= '<p style="padding:10px;font-weight:502;font-size:13px;">';
		$html .= _e( 'Logo Image Upload Error : Size Of Logo Image Will Need To Be Less Than 1MB ', 'woocommerce' ); 
		$html .= '</p></div>';
		echo $html; 
	    }); 
 	 }
      } // end if(!empty($_FILES["product_logoimg"]))
   } // end if posttype product
 } // endif !empty($post)
}


