<?php
/**
 * Template Name: Printer Settings Page
 * The template for displaying fields to search printer settings
 */
if(isset($_POST['checkprintsettingsBtn']) && count($_POST) > 0){ //var_dump($_POST); exit;
  $_errors = array();
  //if any one of the above is not set, then error
  if(!empty($_POST['select_printer_brand']) && !empty($_POST['select_printer_material']) && !empty($_POST['select_printer_color']) && 
     !empty($_POST['select_printer_cleaner']) && !empty($_POST['select_printer_curing'])){
     //if form is submitted check the inputs
     if(isset($_POST['select_printer_brand'])){ $select_printer_brand = $_POST['select_printer_brand']; }
     if(isset($_POST['select_printer_material'])){ $select_printer_material = $_POST['select_printer_material']; }
     if(isset($_POST['select_printer_color'])){ $select_printer_color = $_POST['select_printer_color']; }
     if(isset($_POST['select_printer_cleaner'])){ $select_printer_cleaner = $_POST['select_printer_cleaner']; }
     if(isset($_POST['select_printer_curing'])){ $select_printer_curing = $_POST['select_printer_curing']; }
     //check if the product exists. 
     $args = array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, ); 
     $tax_query[] = array('taxonomy' => 'pa_brand', 'field' => 'slug', 'terms' =>  array($select_printer_brand), 'operator' => 'IN',);
     $tax_query[] = array('taxonomy' => 'pa_material', 'field' => 'slug', 'terms' =>  array($select_printer_material), 'operator' => 'IN',);
     $tax_query[] = array('taxonomy' => 'pa_color', 'field' => 'slug', 'terms' =>  array($select_printer_color), 'operator' => 'IN',);
     $tax_query[] = array('taxonomy' => 'pa_cleaner', 'field' => 'slug', 'terms' =>  array($select_printer_cleaner), 'operator' => 'IN',);
     $tax_query[] = array('taxonomy' => 'pa_curing-method', 'field' => 'slug', 'terms' =>  array($select_printer_curing), 'operator' => 'IN',);
     $tax_query['relation'] = 'AND'; $args['tax_query'] = $tax_query;
     $products = new WP_Query($args); //var_dump($products);
     if( !$products->have_posts() ){
	array_push($_errors, 'Error: Product For The Following Combination Not Found');
	array_push($_errors, 'Printer: '.$select_printer_brand.' Material: '.$select_printer_material.' Color: '.$select_printer_color.' '); 
	array_push($_errors, 'Cleaners: '.$select_printer_cleaner.' Curing Method: '.$select_printer_curing.' '); 
     }else{
	//the first product will be considered if there are many.
	$product = $products->posts[0]; 
	//check if product display pdf value is set to Y 
	$displayPDF = get_post_meta($product->ID, 'display_pdf', true); 
	if(!$displayPDF){
	   array_push($_errors, 'Workflow PDF Not Available. Contact OEM For Printer Settings And Workflow Details.');
	}else{	
	   $displayPDFLink = get_post_meta($product->ID, 'display_pdf_link', true); 	   
	   $fileArr = explode('/',$displayPDFLink);  
	   ob_end_clean();
	   $filegetcontents = file_get_contents($displayPDFLink); 
	   $filename = $fileArr[sizeof($fileArr)-1];
	   header("Pragma: public");
	   header("Expires: 0");
	   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	   header("Cache-Control: private", false);
	   header("Content-Type: application/pdf");
	   header("Content-Disposition: attachment; filename=".$filename.";" );
	   header("Content-Transfer-Encoding: binary");
	   file_put_contents('php://output',$filegetcontents);
	}
     }
  } else{ array_push($_errors, 'Error: All Steps Are Required To Select Printers'); }
}

?>
<?php
get_header();

//generate the variables for the application
$brandhtml = ''; $brandArr = array();
$materialhtml = ''; $materialArr = array();
$colorhtml = ''; $colorArr = array();
$cleanerhtml = ''; $cleanerArr = array();
$curinghtml = ''; $curingArr = array(); 

$args = array( 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => '-1', ); 
$tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' =>  array('printer-type'), 'operator' => 'IN',);
$args['tax_query'] = $tax_query; 
$products = new WP_Query($args); //var_dump($products); exit;

//get a list of products and their ids as values. 
if($products->have_posts()){
  foreach($products->posts as $product){
     $brands = wp_get_post_terms( $product->ID, 'pa_brand', array('orderby'=>'name',) ); 
     $materials = wp_get_post_terms( $product->ID, 'pa_material', array('orderby'=>'name',) );
     $colors = wp_get_post_terms( $product->ID, 'pa_color', array('orderby'=>'name',) ); 
     $cleaners = wp_get_post_terms( $product->ID, 'pa_cleaner', array('orderby'=>'name',) ); 
     $curingmethods = wp_get_post_terms( $product->ID, 'pa_curing-method', array('orderby'=>'name',) ); 

     // create brand html
     foreach( $brands as $brand ){ 
	//if brand is not in array, create the option for dropdown
	if(!in_array($brand->term_id, $brandArr)){
	   array_push($brandArr, $brand->term_id);
	   $brandhtml .= '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
	}
     }
     /*// create material html 
     foreach( $materials as $material ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($material->term_id, $materialArr)){
	   array_push($materialArr, $material->term_id);
	   $materialhtml .= '<option value="'.$material->slug.'">'.$material->name.'</option>';
	}
     }
     // create color html 
     foreach( $colors as $color ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($color->term_id, $colorArr)){
	   array_push($colorArr, $color->term_id);
	   $colorhtml .= '<option value="'.$color->slug.'">'.$color->name.'</option>';
	}
     }
     // create cleaner html 
     foreach( $cleaners as $cleaner ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($cleaner->term_id, $cleanerArr)){
	   array_push($cleanerArr, $cleaner->term_id);
	   $cleanerhtml .= '<option value="'.$cleaner->slug.'">'.$cleaner->name.'</option>';
	}
     }
     // create curing methods html 
     foreach( $curingmethods as $curingmethod ){      
	//if material is not in array, create the option for dropdown
	if(!in_array($curingmethod->term_id, $curingArr)){
	   array_push($curingArr, $curingmethod->term_id);
	   $curinghtml .= '<option value="'.$curingmethod->slug.'">'.$curingmethod->name.'</option>';
	}
     }*/
  }
}else{ 
 $html = '<b>'.'No Products Found'.'</b>';
}

?>
<div class="outer-div">
  <div class="intro-header">
	<?php // Start the Loop.
	/*while ( have_posts() ) : the_post();
		// Include the page content template.
		get_template_part( 'content', 'page' );
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;*/
	?>
  </div>
  <div id="main">
      <div style="color:#F00;text-align:left;padding:20px;">
	<?php 
	 if(isset($_errors) && !empty($_errors)){ 
	    foreach($_errors as $_error){ echo '<p>'.$_error.'</p>'; }
	 } ?>
      </div>

      <h1 style="text-align: center" class="vc_custom_heading">Find Your Printer Settings</h1>

      <form method="post" action="<?php echo $_SERVER["REDIRECT_URL"]; ?>" 
		style="padding:5px;height:205px;border-top:1px solid #CCC;border-bottom:1px solid #CCC;">
	<div style="width:100%">
	<p style="width:100%;text-align:center;font-size:16px;margin:0;" class="messages"></p>
	
	<?php if(!empty($brandhtml)){ ?>
	<div id="brand" name="brand" style="float:left;width:15%;margin:2%;">
		<p><strong>STEP ONE</strong></p>
		<select class="form-control" name="select_printer_brand">
		   <option value="">Select Printer</option> <?php echo $brandhtml; ?>
		</select>
	</div>
	<?php } ?>

	<?php //if(!empty($materialhtml)){ ?>
	<div id="material" name="material" style="float:left;width:15%;margin:2%;">
		<p><strong>STEP TWO</strong></p>
		<select class="form-control" name="select_printer_material">
		   <option value="">Select Material</option> <?php //echo $materialhtml; ?>
		</select>
	</div>
	<?php //} ?>

	<?php //if(!empty($colorhtml)){ ?>
	<div id="color" name="color" style="float:left;width:15%;margin:2%;">
		<p><strong>STEP THREE</strong></p>
		<select class="form-control" name="select_printer_color">
		   <option value="">Select Color</option> <?php //echo $colorhtml; ?>
		</select>
	</div>
	<?php //} ?>

	<?php //if(!empty($cleanerhtml)){ ?>
	<div id="cleaner" name="cleaner" style="float:left;width:15%;margin:2%;">
		<p><strong>STEP FOUR</strong></p>
		<select class="form-control" name="select_printer_cleaner">
		   <option value="">Select Cleaner</option> <?php //echo $cleanerhtml; ?>
		</select>
	</div>
	<?php //} ?>

	<?php //if(!empty($curinghtml)){ ?>
	<div id="curing" name="curing" style="float:left;width:15%;margin:2%;">
		<p><strong>STEP FIVE</strong></p>
		<select class="form-control" name="select_printer_curing">
		   <option value="">Select Curing</option> <?php //echo $curinghtml; ?>
		</select>
	</div>
	<?php //} ?>
	</div>

	<?php if(!empty($brandhtml) || !empty($materialhtml) || !empty($colorhtml) ||
		 !empty($cleanerhtml) || !empty($curinghtml)){ ?>
	<div id="button-div" name="button-div" style="width:100%;text-align:center;">
	   <!--<button type="submit" class="btn" name="checkprintsettingsBtn" value="submit" style="margin:-5px;">Download PDF</button>-->
	   <!--<a href="#" target="__blank" class="btn" name="checkprintsettingsLink" style="margin:-5px;border:1px solid #ee1414;background:#e1000f;color:#FFF;">Download</a>-->
	   <a href="#" target="__blank" class="btn" name="checkprintsettingsLink" style="margin-top:5px;border:1px solid #ee1414;background:#e1000f;color:#FFF;">Download</a>
	</div>
	<?php } ?>


	<?php if(!empty($html)){ ?>
	<div id="html" name="html" style="float:left;width:15%;margin:2%;">
	   <?php echo $html; ?>
	</div>
	<?php } ?>
      </form>
  </div>
</div>

<script>
var $jqr = jQuery.noConflict();
$jqr(document).ready(function(){ 
 
  var ajaxurl = js_porto_vars.ajax_url; 
  var ajaxloaderurl = js_porto_vars.ajax_loader_url; 

  /*----------------------------*
   * On select of the brand 
   * ---------------------------*/
  $jqr('select[name="select_printer_brand"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqr(this);
     var $retrievedVal = $this.val(); 
     //alert($retrievedVal); return false;
     var $form = $this.parents('form');
     var $messages = $form.find(".messages"); 
     var $loaderimg = $this.parent().find('p');

     //if value entered is empty
     if($retrievedVal == ''){
       $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
	   return false;
     }

     $jqr.ajax({
        url: ajaxurl,
        data: { 
	   'brandVal' : $retrievedVal, 
	   'action' : 'porto_child_fetch_material',
	},
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
               $jqr('#material select').empty().append(response.msg);
               $jqr('#color select').empty(); $jqr('#cleaner select').empty(); 
			   $jqr('#curing select').empty();
               $loaderimg.children('img').remove();
               $messages.empty().html('<span style="color:#E1000F;">Please Proceed To Step 2!!</span>');
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

  /*----------------------------*
   * On select of the material 
   * ---------------------------*/
  $jqr('select[name="select_printer_material"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqr(this);
     var $retrievedVal = $this.val(); 
     var $brandVal = $jqr('select[name="select_printer_brand"]').val();
     //alert($brandVal + ' ' + $retrievedVal); return false;
     var $form = $this.parents('form');
     var $messages = $form.find(".messages"); 
     var $loaderimg = $this.parent().find('p');

     //if value entered is empty
     if($retrievedVal == ''){
       $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
	   return false;
     }

     $jqr.ajax({
        url: ajaxurl,
        data: { 
	   'brandVal' : $brandVal, 
	   'materialVal' : $retrievedVal,
	   'action' : 'porto_child_fetch_color',
	},
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
               $jqr('#color select').empty().append(response.msg);
               $loaderimg.children('img').remove();
               $messages.empty().html('<span style="color:#E1000F;">Please Proceed To Step 3!!</span>');
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
 
  /*----------------------------*
   * On select of the color 
   * ---------------------------*/
  $jqr('select[name="select_printer_color"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqr(this);
     var $retrievedVal = $this.val(); 
     var $brandVal = $jqr('select[name="select_printer_brand"]').val();
     var $materialVal = $jqr('select[name="select_printer_material"]').val();
     //alert($brandVal + ' ' + $retrievedVal); return false;
     var $form = $this.parents('form');
     var $messages = $form.find(".messages"); 
     var $loaderimg = $this.parent().find('p');

     //if value entered is empty
     if($retrievedVal == ''){
       $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
	   return false;
     }

     $jqr.ajax({
        url: ajaxurl,
        data: { 
	   'brandVal' : $brandVal, 
	   'materialVal' : $materialVal,
	   'colorVal' : $retrievedVal,
	   'action' : 'porto_child_fetch_cleaner',
	},
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
               $jqr('#cleaner select').empty().append(response.msg);
               $loaderimg.children('img').remove();
               $messages.empty().html('<span style="color:#E1000F;">Please Proceed To Step 4!!</span>');
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

  /*----------------------------*
   * On select of the cleaner 
   * ---------------------------*/
  $jqr('select[name="select_printer_cleaner"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqr(this);
     var $retrievedVal = $this.val(); 
     var $brandVal = $jqr('select[name="select_printer_brand"]').val();
     var $materialVal = $jqr('select[name="select_printer_material"]').val();
     var $colorVal = $jqr('select[name="select_printer_color"]').val();
     //alert($brandVal + ' ' + $retrievedVal); return false;
     var $form = $this.parents('form');
     var $messages = $form.find(".messages"); 
     var $loaderimg = $this.parent().find('p');

     //if value entered is empty
     if($retrievedVal == ''){
       $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
	   return false;
     }

     $jqr.ajax({
        url: ajaxurl,
        data: { 
	   'brandVal' : $brandVal, 
	   'materialVal' : $materialVal,
	   'colorVal' : $colorVal,
	   'cleanerVal' : $retrievedVal,
	   'action' : 'porto_child_fetch_curing',
	   },
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
               $jqr('#curing select').empty().append(response.msg);
               $loaderimg.children('img').remove();
               $messages.empty().html('<span style="color:#E1000F;">Please Proceed To Step 5!!</span>');
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

  /*----------------------------*
   * On select of the curing 
   * ---------------------------*/
  $jqr('select[name="select_printer_curing"]').on('change', function(e) { 
     e.preventDefault();

     var $this = $jqr(this);
     var $retrievedVal = $this.val(); 
     var $brandVal = $jqr('select[name="select_printer_brand"]').val();
     var $materialVal = $jqr('select[name="select_printer_material"]').val();
     var $colorVal = $jqr('select[name="select_printer_color"]').val();
     var $cleanerVal = $jqr('select[name="select_printer_cleaner"]').val();
     //alert($brandVal + ' ' + $retrievedVal); return false;
     var $form = $this.parents('form');
     var $messages = $form.find(".messages"); 
     var $loaderimg = $this.parent().find('p'); 

     //if value entered is empty
     if($retrievedVal == ''){
       $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">Empty Value Not Accepted</span>');
	   return false;
     }

     $jqr.ajax({
        url: ajaxurl,
        data: { 
	   'brandVal' : $brandVal, 
	   'materialVal' : $materialVal,
	   'colorVal' : $colorVal,
	   'cleanerVal' : $cleanerVal,
	   'curingVal'	: $retrievedVal,
	   'action' : 'porto_child_fetch_pdf',
	  },
        method: 'POST',
        dataType: "JSON",
        beforeSend: function() {
            $loaderimg.append('<img src="'+ajaxloaderurl+'" width="15" height="15" style="0 47px 0 47px"/>');
        },
        success: function(response) {
           if( response.success ) {
	       $jqr('#button-div').children('span').remove();
	       $jqr('a[name="checkprintsettingsLink"]').css('display', '');
               $jqr('a[name="checkprintsettingsLink"]').attr('href', response.msg);
               $loaderimg.children('img').remove();
               $messages.empty().html('<span style="color:#ee1414;">Please Click On Download To Open PDF!!</span>');
           } else {
	       $loaderimg.children('img').remove();	
	       if(response.msg.indexOf('Data Not Available') > -1){ 
		 //$jqr('#button-div').empty().append('<span style="background:#FB6262;color:#000;padding:5px;margin:0 26%">'+response.msg+'</span>');
		 $jqr('#button-div a[name="checkprintsettingsLink"]').css('display','none');
		 $jqr('#button-div').children('span').remove();
		 $jqr('#button-div').append('<span style="color:#E1000F;padding:5px;margin:0 26%">'+response.msg+'</span>');
	       } else {
                 $messages.empty().append('<span style="background:#FB6262;color:#000;padding:5px;">'+response.msg+'</span>');
	       }
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


});
</script>

<?php
get_footer();
?>
