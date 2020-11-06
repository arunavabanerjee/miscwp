<?php
/**
 * Admin new order note
 *
 * Overridden by copying to yourtheme/woocommerce/emails/admin-new-order-note.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails/HTML
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$email_heading = 'Order Status Set To Pending For New Order';

$ord_data = $order->get_data(); 

?>

<div id="wrapper" dir="ltr">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
	<td valign="top" align="center">
	<div id="template_header_image"></div>
	<table id="template_container" width="600" cellspacing="0" cellpadding="0" border="0">
	<tbody>
	    <tr><td valign="top" align="center">
			<!-- Header -->
			<table id="template_header" width="600" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			    <tr><td id="header_wrapper"><h1><?php _e( $email_heading ); ?></h1></td></tr>
			</tbody></table>
			<!-- End Header -->
		</td></tr>
		<tr><td valign="top" align="center">
			<!-- Body -->
			<table id="template_body" width="600" cellspacing="0" cellpadding="0" border="0">
			<tbody><tr><td id="body_content" valign="top">
				<!-- Content -->
				<table width="100%" cellspacing="0" cellpadding="20" border="0">
				<tbody><tr><td valign="top">
				<div id="body_content_inner">
                    <p></p><p></p>
                    <table id="addresses" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" cellspacing="0" cellpadding="0" border="0">
	                <tbody><tr>
		            <td style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" width="50%" valign="top">
			        <h2 style="color:#23282d;font-size:1.3em;padding:0;font-weight:504;"><?php _e( 'Billing address' ); ?></h2>
                    <?php $billingAddress = $ord_data['billing']; ?>
			        <address class="address">
				    <?php echo $billingAddress['first_name'].' '.$billingAddress['last_name']; ?><br>
				    <?php echo $billingAddress['company']; ?><br>
				    <?php echo $billingAddress['address_1']; ?><br>
				    <?php echo $billingAddress['address_2']; ?><br>
				    <?php echo $billingAddress['city']; ?><br>
				    <?php echo $billingAddress['state']; ?><br>
				    <?php echo $billingAddress['postcode']; ?><br>
				    <a href="tel:<?php echo $billingAddress['phone']; ?>"><?php echo $billingAddress['phone']; ?></a><br>
				    <a href="#">[email&nbsp;protected]</a>							
				    </address>
		            </td>
					<td style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" width="50%" valign="top">
				    <h2 style="color:#23282d;font-size:1.3em;padding:0;font-weight:504;"><?php _e( 'Shipping address' ); ?></h2>
				    <?php $shippingAddress = $ord_data['shipping']; ?>
    				<address class="address">
				    <?php echo $shippingAddress['first_name'].' '.$shippingAddress['last_name']; ?><br>
				    <?php echo $shippingAddress['company']; ?><br>
				    <?php echo $shippingAddress['address_1']; ?><br>
				    <?php echo $shippingAddress['address_2']; ?><br>
				    <?php echo $shippingAddress['city']; ?><br>
				    <?php echo $shippingAddress['state']; ?><br>
				    <?php echo $shippingAddress['postcode']; ?><br>
    				</address>
			        </td>
			        </tr></tbody></table>

                    <h3> <?php _e( 'Order Details:' ); ?></h3>
                    <h4> <?php _e( 'Order ID: '.$ord_data["id"] ); ?></h4>
                    <table id="t_ord_det"> 
                    <tbody><tr>
                        <th style="padding:5px;font-size:13px;width:100px;text-align:left"><?php _e( 'Product ID '); ?></th>
                        <th style="padding:5px;font-size:13px;width:150px;text-align:left"><?php _e( 'Product Name' ); ?></th>
                        <th style="padding:5px;font-size:13px;width:80px;text-align:center"><?php _e( 'Quantity' ); ?></th>
                        <th style="padding:5px;font-size:13px;width:100px;text-align:left"><?php _e( 'Sub-Total' ); ?></th>
                        <th style="padding:5px;font-size:13px;width:100px;text-align:left"><?php _e( 'Total' ); ?></th>
                    </tr>    
                    <?php $items = $ord_data["line_items"];
                      foreach ($items as $item) { $orderData = $item->get_data(); 
                    ?>
                    <tr>
                    <td style="padding:5px;font-size:13px;width:100px;"><?php echo $orderData['product_id']; ?></td>
                    <td style="padding:5px;font-size:13px;width:150px;"><?php echo $orderData['name']; ?></td>
                    <td style="padding:5px;font-size:13px;width:80px;"><?php echo $orderData['quantity']; ?></td>
                    <td style="padding:5px;font-size:13px;width:100px;"><?php echo wc_price($orderData['subtotal']); ?></td>    
                    <td style="padding:5px;font-size:13px;width:100px;"><?php echo wc_price($orderData['total']); ?></td>        
                    </tr> 
                    <?php } ?>                         
                    </tbody></table> 
				</div>
				</td></tr>
				</tbody></table>
				<!-- End Content -->
			</td></tr></tbody></table>
			<!-- End Body -->
		</td></tr>
		</tbody></table>
	</td></tr>
	<tr><td valign="top" align="center">
	    <!-- Footer -->
		<table id="template_footer" width="600" cellspacing="0" cellpadding="10" border="0">
		<tbody><tr><td valign="top">
			<table width="100%" cellspacing="0" cellpadding="10" border="0">
			<tbody>
			    <tr><td colspan="2" id="credit" valign="middle">
				<p>Gorillas Site — Built with <a href="https://woocommerce.com">WooCommerce</a></p>
				</td></tr>
			</tbody></table>
		</td></tr>
		</tbody></table>
		<!-- End Footer -->
	</td></tr>
</tbody></table>
</div>
