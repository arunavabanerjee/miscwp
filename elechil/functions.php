<?php
/**
 * Electro Child
 *
 * @package electro-child
 */

/**
 * Include all your custom code here
 */
if(isset($_GET['action']) && !empty($_GET['action']) && isset($_GET['notifyCustomer']) && !empty($_GET['notifyCustomer'])){
  //require(__DIR__ . '/../../../wp-load.php');    
  $redirect_to = $_SERVER["HTTP_REFERER"];   
  $args = array( 'post_id'=>$_GET['post'], 'orderby'=>'comment_ID', 'order'=>'DESC', 
                 'approve'=>'approve', 'type'=>'order_note', 'number'=>2 );
  remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
  $notes = get_comments( $args );
  add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 ); 
  //get the note having the attachment. 
  $attachmentNote = $notes[1]->comment_content;  
  $matches = preg_match('/href=["\']?([^"\'>]+)["\']?/', $attachmentNote, $match); 
  //$info = parse_url($match[1]);
  if($matches == 1){ 
    $url = $match[1]; $noteHtml = file_get_contents($url); 
    $pOrder = get_post($_GET['post']); $pOrderMeta = get_post_meta($_GET['post']); 
    $customerEmail = $pOrderMeta['_billing_email']; 
    $sendEmailTo = $customerEmail[0]; 
    $subject = "Admin Message Regarding Order ID:".$_GET['post'];
    $body = $noteHtml;
    $headers = array('Content-Type: text/html; charset=UTF-8','From: Goorilas &lt;info@goorilas.com');
    //$sendMailResult = wp_mail($sendEmailTo, $subject, $body, $headers); 
    //echo '<script type="text/javascript">alert("Email Sent Successfully");window.location.href="'.$redirect_to.'";</script>';
	$out = fopen('php://output', 'w'); 
	$filename="customer_notice.html"; 
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$filename.";" );
	header("Content-Transfer-Encoding: binary");
	fputs($out, $noteHtml);	fclose($out); exit();	
  }else{
    echo '<script type="text/javascript">alert("Email Not Found, Mail Not Sent");window.location.href="'.$redirect_to.'";</script>';  
  }
}

add_action('after_setup_theme', 'admin_bar_removal');
function admin_bar_removal() {
 if (!current_user_can('administrator') && !is_admin()) {
   show_admin_bar(false);
 }
}

/*add_action('woocommerce_single_product_summary', 'func_woocommerce_display_shipping', 25);
function func_woocommerce_display_shipping(){
	$html = '<h3>'.esc_html__('Shipping Details', 'electro').'</h3>';
	$delivery_zones = WC_Shipping_Zones::get_zones();
	foreach ((array) $delivery_zones as $key => $the_zone ) { 
		$shipping_zonename = $the_zone['zone_name']; 
		$html .= '<div style="width:100%;float:left;margin:15px">';
		$html .= '<span style="width:100%;float:left;border-bottom:1px solid #CCC">Region: '.$shipping_zonename.'</span>';
		$shippingMethods = $the_zone["shipping_methods"]; 
		foreach($shippingMethods as $shipMethod){
			$shipMethodTitle = $shipMethod->get_method_title(); 
			// $shippingRate = $shipMethod->cost; 
			$shipMethodInstance = $shipMethod->instance_settings; 
			if(!isset($shipMethodInstance["cost"])){
				$shippingRate = $shipMethodInstance["min_amount"];
			} else { $shippingRate = $shipMethodInstance["cost"]; }
			if(empty($shippingRate)){ $shippingRate = 0.0; }
			$html .= '<span style="width:40%;float:left;">'.$shipMethodTitle.'</span>';
			$html .= '<span style="width:30%;float:left;">';
			$html .= '<strong style="font-weight:600">Cost: '; 
			$html .= wc_price($shippingRate).'</strong>'; 
			$html .= '</span>'; 
		}
		$html .= '</div>';
    }
	echo $html;
}*/

add_filter( 'woocommerce_shop_order_search_fields', 'woocommerce_shop_order_custom_search' );
function woocommerce_shop_order_custom_search( $search_fields ) {
  $search_fields[] = '_order_total';
  $search_fields[] = '_payment_method';	
  $search_fields[] = '_payment_method_title';
	
  return $search_fields;
}

add_filter( 'manage_edit-shop_order_columns', 'add_payment_method_column', 20 );
function add_payment_method_column( $columns ) {
 $new_columns = array();
 foreach ( $columns as $column_name => $column_info ) {
 $new_columns[ $column_name ] = $column_info; 
 if ( 'order_status' === $column_name ) {
   $new_columns['order_city'] = __( 'Shipment City', 'my-textdomain' );
 }	
 if ( 'order_total' === $column_name ) {
   $new_columns['order_payment'] = __( 'Payment Method', 'my-textdomain' );
 }
 }
 return $new_columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'add_payment_method_column_content' );
function add_payment_method_column_content( $column ) {
 global $post;
 if ( 'order_payment' === $column ) {
 $order = wc_get_order( $post->ID );
 echo $order->get_payment_method_title();
 }
 if ( 'order_city' === $column ) {
 	$order = wc_get_order( $post->ID );
 	echo $order->get_shipping_city();
 }	
}

// Adding to admin order list bulk options for generating shipping labels'
add_filter( 'bulk_actions-edit-shop_order', 'bulk_actions_generate_labels', 20, 1 );
function bulk_actions_generate_labels( $actions ) {
    $actions['generate_labels'] = __( 'Generate Labels', 'woocommerce' );
    return $actions;
}

// Make the action from selected orders
add_filter('handle_bulk_actions-edit-shop_order', 'bulk_actions_handle_generate_labels', 10, 3 );
function bulk_actions_handle_generate_labels( $redirect_to, $action, $post_ids ) { 
	global $pagenow; 
	if( $action == 'generate_labels') { 
		$labels = array(); $html ='';
		ob_end_clean();
		$out = fopen('php://output', 'w'); 
		$filename="packing_slips.html"; //$filename="web.csv";
	    //$filename = wp_upload_dir()['path'].'/web.csv'; //echo $filename;
		//$out = fopen($filename, 'w');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$filename.";" );
		header("Content-Transfer-Encoding: binary");
		//header('Content-Type: application/csv; charset=UTF-8');
		//header('Content-Disposition: attachment; filename="'.$filename.'";'); 

    	foreach ( $post_ids as $post_id ) {
        	$order = wc_get_order($post_id); 
			$order_data = $order->get_data(); //var_dump($order_data); exit; 
			// generate an html as the label from order data
			$html = '<div style="width:1100px;height:800px; overflow:hidden;">'; 
			$html .= '<table style="width:100%">'; 
			// top row
			$html .= '<tr><td style="width:100%;"><div class="">';
			$html .= '<div style="width:50%;float:left;font-size:14px;font-weight:bold">';
			$html .= '<p><span> Order No: </span><span>'.$order_data['id'].'</p>';
            $html .= '<p><span> Created Date: </span><span>'.$order->get_date_created()->format('j F Y').'</p></div>';
			$html .= '<div style="width:50%;float:right;font-size:12px;font-weight:bold">';
			$html .= '<p><span> From: </span><span> SP Brazil</p></div>';
			$html .= '</div></td></tr>';
			// address row
			$html .= '<tr><td style="width:100%;">';
			$html .= '<div style="width:50%;float:left;font-size:12px;">';			
			$html .= '<p><span style="font-weight:bold;background:#CCC;padding:5px;"> Billing Address: </span><span></p>';
            $html .= '<p><span> '.$order_data["billing"]["first_name"].$order_data["billing"]["last_name"].'</p>';
            $html .= '<p><span> '.$order_data["billing"]["company"].'</p>';	
            $html .= '<p><span> '.$order_data["billing"]["address_1"].'</p>';	
            $html .= '<p><span> '.$order_data["billing"]["address_2"].'</p>';	
            $html .= '<p><span> '.$order_data["billing"]["city"].', '.$order_data["billing"]["state"].'</p>';			
            $html .= '<p><span> '.$order_data["billing"]["country"].', '.$order_data["billing"]["postcode"].'</p>';			
			$html .= '</div>';
			$html .= '<div style="width:50%;float:left;font-size:12px;">';			
			$html .= '<p><span style="font-weight:bold;background:#CCC;padding:5px;"> Shipping Address: </span><span></p>';			
            $html .= '<p><span> '.$order_data["shipping"]["first_name"].$order_data["shipping"]["last_name"].'</p>';
            $html .= '<p><span> '.$order_data["shipping"]["company"].'</p>'; 
            $html .= '<p><span> '.$order_data["shipping"]["address_1"].'</p>';	
            $html .= '<p><span> '.$order_data["shipping"]["address_2"].'</p>';	
            $html .= '<p><span> '.$order_data["shipping"]["city"].', '.$order_data["shipping"]["state"].'</p>';			
            $html .= '<p><span> '.$order_data["shipping"]["country"].', '.$order_data["shipping"]["postcode"].'</p>';				
			$html .= '</div></td></tr>';
			// email options 
			$html .= '<tr><td style="width:100%">';
			$html .= '<div style="width:100%;float:left;font-size:12px;">';			
			$html .= '<p><span style="font-weight:bold;background:#CCC;padding:5px;"> Email & Phone: <span></p>';
            $html .= '<p><span> Email: </span><span> '.$order_data["billing"]["email"].'</p>';
            $html .= '<p><span> Phone: </span><span> '.$order_data["billing"]["phone"].'</p>';	
			$html .= '</td></div></tr>';
			// products list
			$html .= '<tr><td style="width:100%;font-size:12px;font-weight:bold;">';
			$html .= '<div style="width:10%;float:left;background:#CCC;padding:2px;">ProductID</div>';
			$html .= '<div style="width:20%;float:left;background:#CCC;padding:2px;">Name</div>';
			$html .= '<div style="width:10%;float:left;background:#CCC;padding:2px;">SKU</div>';
			$html .= '<div style="width:10%;float:left;background:#CCC;padding:2px;">Quantity</div>';
			$html .= '<div style="width:10%;float:left;background:#CCC;padding:2px;">Subtotal</div></td></tr>'; 
			$oitems = $order_data["line_items"]; 
			foreach($oitems as $oitem){ 
				$oitem_data = $oitem->get_data();  
				if($oitem_data['order_id'] == $order_data['id']){ 
					$cproduct = wc_get_product($oitem_data['product_id']);
					$cproduct_data = $cproduct->get_data(); //var_dump($cproduct_data);
					$html .= '<tr><td style="width:100%;font-size:12px;">';
					$html .= '<div style="width:10%;float:left;">'.$oitem_data['product_id'].'</div>';
					$html .= '<div style="width:20%;float:left;">'.$oitem_data['name'].'</div>';
					$html .= '<div style="width:10%;float:left;">'.$cproduct_data['sku'].'</div>';
					$html .= '<div style="width:10%;float:left;">'.$oitem_data["quantity"].'</div>';
					$html .= '<div style="width:10%;float:left;">'.$oitem_data["subtotal"].'</div></td></tr>';
				} //endif
			}// endforeach
			$html .= '<tr><td style="width:100%">';
			$html .= '<div style="width:100%;float:left;font-size:12px;">';	
			$html .= '<p><span style="font-weight:bold;background:#CCC;padding:5px;"> Total: <span></p>';
            $html .= '<p><span> Order Total: </span><span> '.$order_data["total"].'</p>';	
			$html .= '</div></td></tr>';	
			$html .= '</table></div>'; 
			fputs($out, $html);
		}
		fclose($out); exit(); 
		
	}//endif action generate_labels	
}

//--------
// -- create new templates in themes/curr_theme/woocommerce/emails
// -- these can be customized as per requirement
//-------- 
add_action('woocommerce_order_status_pending', 'mysite_pending');
function mysite_pending($order_id) {
  $filename = 'pending_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/admin-new-order-note.php'; 
  $heading = ''; $mailer='';
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );
  $uploaded = wp_upload_bits($filename, null, $mail_html);		
  //add_attachment_to_order($uploaded[$url],'Pending Order Attachment'); 
  //$note = __("Pending Order Attachment…".$uploaded['url']);
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Pending Order Attachment…").'</a>';
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';	
  $order->add_order_note( $note ); 
}

add_action('woocommerce_order_status_failed', 'mysite_failed');
function mysite_failed($order_id) {
  $filename = 'failed_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/admin-failed-order-note.php';			
  $heading = ''; $mailer='';
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );
  $uploaded = wp_upload_bits($filename, null, $mail_html);
  //add_attachment_to_order($uploaded[$url],'Failed Order Attachment');	
  //$note = __("Failed Order Attachment…".$uploaded['url']);
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Failed Order Attachment…").'</a>';	
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';  
  $order->add_order_note( $note );
}

add_action('woocommerce_order_status_on-hold', 'mysite_hold');
function mysite_hold($order_id) { 	
  $filename = 'onhold_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/customer-on-hold-order-note.php';			
  $heading = ''; $mailer='';
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );	
  $uploaded = wp_upload_bits($filename, null, $mail_html); //var_dump($uploaded); exit;
  //add_attachment_to_order($uploaded['url'],'On Hold Order Attachment');		
  //$note = __("On Hold Order Attachment…".$uploaded['url']);
  $note = '<a href="'.$uploaded['url'].'" download>'.__("On Hold Order Attachment…").'</a>'; 
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';
  $order->add_order_note( $note );	
}

add_action('woocommerce_order_status_processing', 'mysite_processing');
function mysite_processing($order_id) {
  $filename = 'processing_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/customer-processing-order-note.php';	
  $heading = ''; $mailer='';	
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer ); 
  $uploaded = wp_upload_bits($filename, null, $mail_html); //var_dump($uploaded); exit;
  //add_attachment_to_order($uploaded['url'],'Priocessing Order Attachment');	
  //$note = __("Processing Order Attachment…".$uploaded['url']);	
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Processing Order Attachment…").'</a>';
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';
  $order->add_order_note( $note );	
}

add_action('woocommerce_order_status_completed', 'mysite_completed');
function mysite_completed($order_id) {
  $filename = 'completed_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/customer-completed-order-note.php';	
  $heading = ''; $mailer='';
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );
  $uploaded = wp_upload_bits($filename, null, $mail_html);		
  //add_attachment_to_order($uploaded[$url],'Completed Order Attachment');	
  //$note = __("Completed Order Attachment…".$uploaded['url']);	
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Completed Order Attachment…").'</a>'; 
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';
  $order->add_order_note( $note );	
}

add_action('woocommerce_order_status_refunded', 'mysite_refunded');
function mysite_refunded($order_id) {
  $filename = 'refunded_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/customer-refunded-order-note.php';	
  $heading = ''; $mailer='';
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );
  $uploaded = wp_upload_bits($filename, null, $mail_html);		
  //add_attachment_to_order($uploaded[$url],'Refunded Order Attachment');	
  //$note = __("Refunded Order Attachment…".$uploaded['url']);	
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Refunded Order Attachment…").'</a>';
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';
  $order->add_order_note( $note ); 
}

add_action('woocommerce_order_status_cancelled', 'mysite_cancelled');
function mysite_cancelled($order_id) {
  $filename = 'cancelled_order_'.$order_id.'.html';	
  $order = wc_get_order(  $order_id );
  $ord_template = 'emails/admin-cancelled-order-note.php';	
  $heading = ''; $mailer='';		
  $mail_html = 	get_custom_email_html( $order, $ord_template, $heading, $mailer );
  $uploaded = wp_upload_bits($filename, null, $mail_html);	  
  //add_attachment_to_order($uploaded[$url],'Cancelled Order Attachment');	
  //$note = __("Cancelled Order Attachment…".$uploaded['url']);	
  $note = '<a href="'.$uploaded['url'].'" download>'.__("Cancelled Order Attachment…").'</a>';
  $note .= '<a href="?post='.$order_id.'&action=edit&notifyCustomer=1" style="background:#EEE;border-radius:3px;padding:4px;margin-left:16px;font-size: 10px;text-decoration: none;">ToCustomer</a>';
  $order->add_order_note( $note ); 
}

/**
 * get custom order html
 */
function get_custom_email_html( $order, $ord_template, $heading = false, $mailer ) {
	$template = $ord_template;
	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_heading' => $heading,
		'sent_to_admin' => true,
		'plain_text'    => false,
		'email'         => $mailer,
		'additional_content' => '',
	));
}

// display the extra data in the order admin panel
function order_data_upload_image_in_admin( $order ){ ?>
    <div class="order_data_column">
        <h4><?php _e( 'Extra Details' ); ?></h4>
        <?php 
            echo '<form action="" method="POST" enctype="multipart/form-data">';
            echo '<p> Upload Image: '; 
            echo '<input type="file" id="attachmentImage" name="attachmentImage" style="background:#CCC;padding:2px;width:217px;border-radius:8px;" />';
            echo '<input type="button" class="go-upload" name="goupload" id="goupload" value="GO" style="background:#EEE;border-radius:4px;"/>';
            echo '<input type="hidden" name="oId" id="oId" value="'.$order->get_id().'"/>';            
            echo '</p></form>';
		?>											
    </div>
    <script>
        jQuery(document).on('click', '.go-upload', function (e) {
            var oId = jQuery('#oId').val();
            var file_data = jQuery('#attachmentImage').prop('files')[0]; 
            //console.log(file_data); 
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'md_attachment_save');
            form_data.append('oId', oId);            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) { 
                    console.log(response); 
                    //jQuery('#attached').href=response;
                },
                complete: function(response) {
                    window.location.reload();
                },
                error: function (response) { console.log('error'); }
            });
        });
    </script>
<?php }
add_action( 'woocommerce_admin_order_data_after_order_details', 'order_data_upload_image_in_admin' );

add_action( 'wp_ajax_md_attachment_save','md_attachment_save' );
add_action( 'wp_ajax_nopriv_md_attachment_save','md_attachment_save' );
function md_attachment_save(){ 
    //var_dump($_FILES); var_dump($_POST); exit;    
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    //files uploaded
    $uploadedfile = $_FILES['file'];
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
    // echo $movefile['url'];
    
    if ($movefile && !isset($movefile['error'])) {
        //add note to order
        $order = wc_get_order( $_POST['oId'] );
        $currentStatus = ucfirst(strtolower($order->get_status()));
        $note = '<a href="'.$movefile['url'].'" download>'.__("Image Attachment For Order Status: ".$currentStatus).'</a>';
        $order->add_order_note( $note );     
        echo $movefile['url'];
    } else {
        echo $movefile['error'];
    }
    die();
}

//-------
//generates the block before shop line items
//-------
add_action( 'woocommerce_admin_order_items_after_shipping', 'admin_order_data_after_shipping', 10, 3 ); 
function admin_order_data_after_shipping($order_id) { 
    $order = wc_get_order(  $order_id ); 
?>    
    <div class="order_data_column" style="border:1px solid #000;background:#DDD;">
        <h4 style="background:#BBB;text-align:center;margin:2px;"><?php _e('Preview Order Status Template: '); ?></h4>
        <div class="after-shipping"></div>
        <input type="hidden" name="oId" id="oId" value="<?php echo $order->get_id(); ?>"/>
    </div>
    <script>
        jQuery(document).on('change', '#order_status', function(e) { 
            var statusVal = this.value; 
            var oId = jQuery('#oId').val();
            var form_data = new FormData();
            form_data.append('cstatus', statusVal);
            form_data.append('action', 'md_attachment_get');
            form_data.append('oId', oId);            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                contentType: false,
                processData: false,
                data: form_data,
                dataType: 'html',
                success: function (response) { 
                    console.log(response); 
                    //var innerHtml = '<iframe>'+response+'</iframe>';
                    //jQuery('.after-shipping').html(innerHtml);
                    jQuery('.after-shipping').html(response);
                },
                //complete: function(response) {
                //    window.location.reload();
                //},
                error: function (response) { console.log('error'); }
            });
        });
    </script>
<?php
}

add_action( 'wp_ajax_md_attachment_get','md_attachment_get' );
add_action( 'wp_ajax_nopriv_md_attachment_get','md_attachment_get' );
function md_attachment_get(){ 
    //var_dump($_POST); exit;
    $order = ''; $mail_html = ''; $heading= ''; $ord_template = '';
    // order object 
    if($_POST['oId'] != ''){ $order = wc_get_order( $_POST['oId'] ); } 
    
    //template selection based on order status
    if($_POST['cstatus'] == "wc-pending"){ 
      $ord_template = 'emails/admin-new-order-note.php'; 
      $heading = ''; $mailer='';
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-processing"){ 
      $ord_template = 'emails/customer-processing-order-note.php';	
      $heading = ''; $mailer='';	
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-on-hold"){ 
      $ord_template = 'emails/customer-on-hold-order-note.php';			
      $heading = ''; $mailer='';
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-completed"){ 
      $ord_template = 'emails/customer-completed-order-note.php';	
      $heading = ''; $mailer='';
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-cancelled"){ 
      $ord_template = 'emails/admin-cancelled-order-note.php';	
      $heading = ''; $mailer='';		
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-refunded"){ 
      $ord_template = 'emails/customer-refunded-order-note.php';	
      $heading = ''; $mailer='';
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    if($_POST['cstatus'] == "wc-failed"){ 
      $ord_template = 'emails/admin-failed-order-note.php';			
      $heading = ''; $mailer='';
      $mail_html = wc_get_template_html( $ord_template, array('order' => $order, 'email_heading' => $heading,
                                            'sent_to_admin' => true, 'plain_text' => false, 'email' => $mailer, 'additional_content' => '',));
    }
    echo $mail_html;    
    die();
}

add_filter ( 'woocommerce_account_menu_items', 'remove_my_account_links' );
function remove_my_account_links( $menu_links ){
  //unset( $menu_links['edit-address'] ); // Addresses
  //unset( $menu_links['dashboard'] ); // Remove Dashboard
  //unset( $menu_links['payment-methods'] ); // Remove Payment Methods
  //unset( $menu_links['orders'] ); // Remove Orders
  unset( $menu_links['downloads'] ); // Disable Downloads
  //unset( $menu_links['edit-account'] ); // Remove Account details tab
  //unset( $menu_links['customer-logout'] ); // Remove Logout link
  return $menu_links;
}

//remove_action('electro_shop_control_bar', 'woocommerce_catalog_ordering', 50 );
add_filter('woocommerce_default_catalog_orderby', 'func_default_catalog_orderby');
function func_default_catalog_orderby( $sort_by ) {
   //return 'date'; 
   return 'menu_order';	
}




