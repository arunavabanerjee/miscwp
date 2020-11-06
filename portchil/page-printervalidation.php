<?php
/**
 * Template Name: Printer Validation Page
 *
 * The template for displaying fields to search printer settings
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); 

//fetch all products from the "printers" taxonomy
//$args = array('post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', 
//			  'orderby' => 'date', 'order' => 'DESC'); 
$args = array('post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', 
		'meta_key' => 'custom_ordering', 'orderby' => 'meta_value_num', 'order' => 'ASC');
$tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printers'), 'operator' => 'IN',);
$args['tax_query'] = $tax_query; $products = new WP_Query($args); //var_dump($products); exit;

?>

<div class="outer-div">
  <div class="intro-header">
	<?php // Start the Loop.
	  //while ( have_posts() ) : the_post();
		// Include the page content template.
		//get_template_part( 'content', 'page' );
		// If comments are open or we have at least one comment, load up the comment template.
		//if ( comments_open() || get_comments_number() ) {
		//	comments_template();
		//}
	  //endwhile; ?>
  </div>
  <div id="main">

  <?php //get the list of products as values. 
    if($products->have_posts()){ $cnt=0; ?>
    <div class="container-fluid"> 
    <div class="row">
	<div class="col-md-3">
	   <h2 style="font-size:20px;color: #e1000f;text-align: right;font-weight:400;font-style:normal;margin-top:10px;margin-bottom:10px;" class="vc_custom_heading"><?php echo esc_html__('Select Material :'); ?></h2>
	</div>
	<div class="col-md-6">
	   <?php $materials = array(); $materialHtml = []; ?>
	   <?php foreach($products->posts as $product){
     	   	$validated_resins = wp_get_post_terms( $product->ID, 'pa_validated-resins', array('orderby'=>'name',));
	   	$pending_resins = wp_get_post_terms( $product->ID, 'pa_pending-resins', array('orderby'=>'name',)); 
	   	foreach($validated_resins as $validated){ //for validated	   
	  	   if(!in_array($validated->term_id, $materials)){ 
		     array_push($materials, $validated->term_id); 
		     $materialHtml[] = '<option value="'.$validated->slug.'">'.$validated->name.'</option>'; 
		   }
		}
	   	foreach($pending_resins as $pending){ //for pending	   
	  	   if(!in_array($pending->term_id, $materials)){ 
		     array_push($materials, $pending->term_id);
		     $materialHtml[] = '<option value="'.$pending->slug.'">'.$pending->name.'</option>'; 
		   }
		}
	    } 
	    $materialHtmlUniq = array_unique($materialHtml); sort($materialHtmlUniq); ?>
	   <?php // var_dump($materials); var_dump($materialHtmlUniq); ?>
	   <?php $materials = array(); $materialHtmlUniqNoDuplicates = array(); 
		foreach($materialHtmlUniq as $indx => $html){ //echo $html;
		   $start = strpos($html, '>'); $end = strpos($html, '<', -10);
		   $name = substr($html, ($start+1), ($end-$start-1)); //echo $name.'<br/>'; 
		   $hrefstart = strpos($html, 'value='); $hrefend = strpos($html, '>');
		   $href = substr($html, ($hrefstart+7), ($hrefend-$hrefstart-8));
		   if(!strpos($name,'-')){ //color not found, add that to array
		      if(!in_array($name, $materials)){ 
			array_push($materials, trim($name)); array_push($materialHtmlUniqNoDuplicates, $html);
		      }
		   }else{ // color info available.
		      $name_array = explode('-',$name); //var_dump($name_array);
		      if(!in_array($name_array[0], $materials)){ array_push($materials, trim($name_array[0])); 
			 //echo $href . '--' . $name_array[0] .'<br/>';  
			$materialHtmlUniqNoDuplicates[] = '<option value="'.$href.'">'.$name_array[0].'</option>';
		      } 
		   }
		   $start =0; $end=0; $hrefstart=0; $hrefend=0;
		}
		//var_dump($materials);
	   ?>
	   <select class="form-control" name="select_printer_validation_material" style="margin:10px;padding:10px;">
		<option value="">Select Material</option> 
		<?php //foreach($materialHtmlUniq as $html){ echo $html; }?>
		<?php foreach($materialHtmlUniqNoDuplicates as $html){ echo $html; }?>
	   </select>
	   <p style="width:100%;text-align:center;font-size:16px;margin:2px;" class="messages"></p>		
	</div>
    </div>
    <div class="row products">
	<?php foreach($products->posts as $product){ $j = $cnt+1;
		   $validated_resins = wp_get_post_terms( $product->ID, 'pa_validated-resins', array('orderby'=>'name',));
		   $pending_resins = wp_get_post_terms( $product->ID, 'pa_pending-resins', array('orderby'=>'name',)); ?>
	<div class="col-md-3" data-pid="<?php echo $product->ID; ?>">		
  		<div class="wpb_wrapper vc_column-inner" style="margin-bottom:20px;border:1px solid #EEE;">
		  <div class="wpb_single_image">
		  <div class="wpb_wrapper">
		  <div class="vc_single_image-wrapper vc_box_rounded  vc_box_border_grey" style="margin:15px;">
		    <?php echo get_the_post_thumbnail($product->ID, 'medium', 
				array('title' => $product->post_title, 'alt' => $product->post_name, 
				      'style' => 'height:250px', 'class' => 'vc_single_image-img') ); ?>
		  </div></div></div>
		
		  <?php $manufact_link = get_post_meta($product->ID, 'manufacturer_page_link', true); 
		    if(isset($manufact_link) && !empty($manufact_link)){ ?>
			<h2 style="font-size:18px;color: #e1000f;text-align: center;font-weight:400;font-style:normal;margin-bottom:10px;" class="vc_custom_heading"><a href="<?php echo $manufact_link;?>"><?php echo $product->post_title; ?></a></h2>
		  <?php }else { ?>
			<h2 style="font-size:18px;color: #e1000f;text-align: center;font-weight:400;font-style:normal;margin-bottom:10px;" class="vc_custom_heading"><?php echo $product->post_title; ?></h2>
		  <?php } ?>

		  <!-- validated and pending resins -->
		  <?php $menu_border = get_post_meta($product->ID, 'product_menu_border_color', true); 
		    if(isset($menu_border) && !empty($menu_border)){ ?>
		  <div class="accordion" id="validated-pending-accordion-<?php echo $j; ?>" style="background:#EEE;border:2px solid <?php echo $menu_border;?>">
		  <?php } else { ?>
		  <div class="accordion" id="validated-pending-accordion-<?php echo $j; ?>" style="background:#EEE;">
		  <?php } ?>
		  <?php if(!empty($validated_resins)){ ?>
		    <div class="vc_tta-panel" data-vc-content=".vc_tta-panel-body">
		    <div class="vc_tta-panel-heading" style="padding:10px 20px;background:#DDD;">
			<h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left" style="margin-bottom:0px;">
			  <a href="javascript:void(0);" style="font-size:16px;color:#666;" data-toggle="collapse" 
				data-target=".vc_valid_tta-panel-body-<?php echo $j; ?>" aria-expanded="false" 
				aria-controls=".vc_valid_tta-panel-body-<?php echo $j; ?>">
				<span class="vc_tta-title-text">Validated Resins</span><i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
			  </a>
			</h4>
		    </div>
		    <div class="vc_valid_tta-panel-body-<?php echo $j; ?> collapse" data-parent="#validated-pending-accordion-<?php echo $j; ?>" style="padding:1px 0;">
			<div class="wpb_text_column wpb_content_element " style="margin-bottom:15px;">
			<div class="wpb_wrapper">
			<?php foreach($validated_resins as $validated){ ?>
			<?php if($validated->description != ''){ ?>
			  <p style="text-align:left;margin-bottom:0px;margin-top:-10px;height:22px;"><span style="font-size:12px;">
			    <?php $attrb_name = $validated->name; $attrb_name_array = array();
			     if(strstr($attrb_name, '-')){ $attrb_name_array = explode('-',$attrb_name); ?>
			      <span><a href="<?php echo trim($validated->description); ?>">
				<?php echo trim($attrb_name_array[0]); ?></a><?php echo ' '.$attrb_name_array[1]; ?></span>
			    <?php } else { ?>
			    <a href="<?php echo trim($validated->description); ?>"><?php echo trim($attrb_name); ?></a>
			    <?php } ?>
			  </span></p>
			<?php } else { ?>
			  <p style="text-align:left;margin-bottom:0px;margin-top:-10px;">
			    <span style="font-size: 12px;margin:0 10px;"><?php echo trim($validated->name); ?></span></p>
			<?php }} ?>
			</div></div>
		    </div>
		    </div>
		   <?php } ?>

		   <?php if(!empty($pending_resins)){ ?>
		   <div class="vc_tta-panel vc_active" data-vc-content=".vc_tta-panel-body">
		    <div class="vc_tta-panel-heading" style="padding:10px 20px;background:#DDD;">
			<h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left" style="margin-bottom:0px;">
			  <a href="javascript:void(0);" style="font-size:16px;color:#666;" data-toggle="collapse" 
				data-target=".vc_pending_tta-panel-body-<?php echo $j; ?>" aria-expanded="false" 
				aria-controls=".vc_pending_tta-panel-body-<?php echo $j; ?>">
			   <span class="vc_tta-title-text">Pending Resins</span> <i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i>
			  </a>
			</h4>
		    </div>
		    <div class="vc_pending_tta-panel-body-<?php echo $j; ?> collapse" data-parent="#validated-pending-accordion-<?php echo $j; ?>">
			<div class="wpb_text_column wpb_content_element " style="margin-bottom:0px;">
			<div class="wpb_wrapper" style="padding-bottom:5px;">
			  <?php foreach($pending_resins as $pending){ ?>
			  <?php if($pending->description != ''){ ?>
			    <p style="text-align:left;margin-bottom:0px;margin-top:-10px;height:22px;"><span style="font-size: 12px;">
			    <?php $attrb_name = $pending->name; $attrb_name_array = array();
			     if(strstr($attrb_name, '-')){ $attrb_name_array = explode('-',$attrb_name); ?>
			      <span><a href="<?php echo trim($pending->description); ?>">
				<?php echo trim($attrb_name_array[0]); ?></a><?php echo ' '.$attrb_name_array[1]; ?></span>
			    <?php } else { ?>
			    <a href="<?php echo trim($pending->description); ?>"><?php echo trim($attrb_name); ?></a>
			    <?php } ?>
			    </span></p>
			  <?php } else { ?>
			    <p style="text-align:left;margin-bottom:0px;margin-top:-10px;">
			      <span style="font-size: 12px;margin:0 10px;"><?php echo trim($pending->name); ?></span></p>
			  <?php }} ?>
			</div></div>
		    </div>
		  </div>
		 <?php } ?>
		 </div><!-- end accordian -->
		 <?php $tagline = get_post_meta($product->ID, 'tagline', true); 
		   $logoimg = get_post_meta($product->ID, 'admin_product_logoimg', true);
		   $menu_border = get_post_meta($product->ID, 'product_menu_border_color', true); 
		   $product_page_link = get_post_meta($product->ID, 'product_page_link', true);
		  if(isset($tagline) && isset($logoimg)){ 
		    if(!empty($tagline) && !empty($logoimg)){ 
		      if(isset($product_page_link) && !empty($product_page_link)){ 
			$product_link = $product_page_link; 
		      } else{ $product_link = '#'; }
		      $selImageID = get_post_meta($product->ID, 'admin_product_logoimg', true);
     		      $arr_existing_image = wp_get_attachment_image_src($selImageID, 'medium');
     		      $existing_image_url = $arr_existing_image[0];
		      if(isset($menu_border) && !empty($menu_border)){
		        echo '<div style="text-align:center;color:'.$menu_border.';border:1px solid '.$menu_border.';background: #f5f1f1;">';
			echo '<a href="'.$product_link.'" style="color:'.$menu_border.'">';
		        echo '<strong>'.$tagline.' by '.'</strong><img src="'.$existing_image_url.'" style="width:101px;height:35px;"/>';
			echo '</a></div>'; 
		      }else{
			echo '<div style="text-align:center;color:#CFCFCF;border:1px solid #CFCFCF;background: #f5f1f1;">'; 
			echo '<a href="'.$product_link.'">';
		        echo '<strong>'.$tagline.' by '.'</strong><img src="'.$existing_image_url.'" style="width:101px;height:35px;"/>';
			echo '</a></div>';
		      }
		    }
		  } ?>
	</div></div><!-- end col-md-3 -->
	<?php $cnt++; } ?>
	<?php /*if($cnt < 4 ){ ?></div><?php }*/ ?>
   </div>
   </div>
   <?php }else{ echo '<b>'.'No Printers Found. Add Printers Under Category Printers to Display Here.'.'</b>'; } ?>

  </div><!-- end main -->
</div><!-- end outer div -->

<script>
var $jqry = jQuery.noConflict(); 
$jqry(document).ready(function(){
 
  var ajaxurl = js_porto_vars.ajax_url; 
  var ajaxloaderurl = js_porto_vars.ajax_loader_url; 

  /*----------------------------*
   * On select of the brand 
   * ---------------------------*/
  $jqry('select[name="select_printer_validation_material"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqry(this);
     var $retrievedVal = $this.val(); 
     //alert($retrievedVal); return false;
     var $parent = $this.parents('.col-md-6');
     var $messages = $parent.find(".messages"); 
     var $loaderimg = $parent.find('p');

     //if value entered is empty
     //if($retrievedVal == ''){
     //  $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
     //  return false;
     //}else{ $messages.empty(); }

     $jqry.ajax({
        url: ajaxurl,
        data: { 
	   'validationVal' : $retrievedVal, 
	   'action' : 'porto_child_fetch_validation_material',
	},
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
	       //$messages.empty().html('<span style="color:#ee1414;">'+response.msg+'</span>');
	       var $prods = response.msg.split(','); 
	       $jqry('.products .col-md-3').each(function(){ 
		 var $pid = $jqry(this).attr('data-pid'); 
		 if($prods.indexOf($pid) > -1){ 
		   $jqry(this).css('display','block');
		 } else { $jqry(this).css('display','none'); }
	       });
               $loaderimg.children('img').remove();
	       //$messages.empty().html('<span style="color:#ee1414;">'+response.msg+'</span>');
           } else {
              $loaderimg.children('img').remove();
               $messages.empty().append('<span style="background:#FF0000;color:#000;">'+response.msg+'</span>');
           }
        },
        error: function(xhr, status, error) {
             var err = eval("(" + xhr.responseText + ")");
             console.log('Error:'+err.Message);
        },
        //complete: function(){
        //    $this.children('i').removeClass(process_loader_spinner);
        //    $this.children('i').addClass(success_icon);
        //}
     });
  });

  $jqry('.products .col-md-3').each(function(){
    $jqry(this).find('.collapse').on('shown.bs.collapse', function(){ 
       if($jqry(this).hasClass('show')){ $jqry(this).removeClass('show'); }
    }); 
    $jqry(this).find('.vc_tta-panel-heading h4 a')
		.prepend('<i class="porto-icon-plus-squared-alt" style="font-size:16px;float:left;margin: 4px;"></i>');
  });
});

$jqry('.vc_tta-panel-heading h4 a').click(function(){ 
  var $parent = $jqry(this).parents('.vc_tta-panel'); 
  if($parent.find('.collapse').css('display') == ''){
      $parent.find('.collapse').css('display', 'block');
      $parent.find('.vc_tta-panel-heading h4 a').children('i').remove();
      $parent.find('.vc_tta-panel-heading h4 a')
	.prepend('<i class="porto-icon-minus-squared-alt" style="font-size:16px;float:left;margin: 4px;"></i>');

  }else if($parent.find('.collapse').css('display') == 'block'){
      $parent.find('.collapse').css('display', 'none');
      $parent.find('.vc_tta-panel-heading h4 a').children('i').remove();
      $parent.find('.vc_tta-panel-heading h4 a')
	.prepend('<i class="porto-icon-plus-squared-alt" style="font-size:16px;float:left;margin: 4px;"></i>');
  } else {
      $parent.find('.collapse').css('display', 'block');
      $parent.find('.vc_tta-panel-heading h4 a').children('i').remove();
      $parent.find('.vc_tta-panel-heading h4 a')
	.prepend('<i class="porto-icon-minus-squared-alt" style="font-size:16px;float:left;margin: 4px;"></i>');
  }
}); 

</script>

<?php
get_footer();
?>
