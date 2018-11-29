<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>
<footer>
    <div class="top-img">
        <img src="<?php echo get_template_directory_uri(); ?>/images/footer-curve.png" alt="">
     </div>
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 wow fadeInUp">
                    <h3>Our products</h3>
                   <ul class="quick-link">
                        <li><a href="<?php bloginfo('url')?>/domestic-maid-insurance/">Domestic Maid Insurance</a></li>
                        <li><a href="<?php bloginfo('url')?>/philippines-embassy-bond/">Philippines Embassy Bond</a></li>
                        <li><a href="<?php bloginfo('url')?>/foreign-worker-medical-insurance/">Medical Insurance</a></li>
                       
                    </ul>
                </div>
                <div class="col-lg-3 wow fadeInUp">
                    <h3>services</h3>
                    <p>Claims Service</p>
                    <p>Document Processing</p>
                    <p>Payment Processing</p>
                </div>
                <div class="col-lg-3 wow fadeInUp">
                    <h3>about iA</h3>
                      <?php
                $Footer = array(
                'theme_location'  => '',
                'menu'            => 'about iA',
                'container'       => '',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => '',
                'menu_id'         => '',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                 'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul class="quick-link">%3$s</ul>',
                'depth'           => 0,
                'walker'          => ''
                );

                wp_nav_menu( $Footer );
                ?>
                </div>
                <div class="col-lg-3 wow fadeInUp">
                    <h3>customer </h3>
                    <?php
                $Footer = array(
                'theme_location'  => '',
                'menu'            => 'Customer',
                'container'       => '',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => '',
                'menu_id'         => '',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                 'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul class="quick-link">%3$s</ul>',
                'depth'           => 0,
                'walker'          => ''
                );

                wp_nav_menu( $Footer );
                ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom wow fadeInDown">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php
                $Footer = array(
                'theme_location'  => '',
                'menu'            => 'Footer Bottom',
                'container'       => '',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => '',
                'menu_id'         => '',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                 'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul class="quick-link">%3$s</ul>',
                'depth'           => 0,
                'walker'          => ''
                );

                wp_nav_menu( $Footer );
                ?>
                </div>
                <div class="col-lg-4">
                    <p>Copyright © 2018 insureAsia. All rights reserved. </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?php bloginfo('template_url')?>/js/popper.min.js"></script>
<script src="<?php bloginfo('template_url')?>/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_url')?>/js/wow.js"></script>
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
var dateToday = new Date(); 
  $( function() {
    $( ".datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      minDate: dateToday
    });
  } );  
</script>
<script type="text/javascript">
    $(document).ready(function() {
  $(".set > a").on("click", function() {

   
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this)
        .siblings(".content")
        .slideUp(200);
      $(".set > a i")
        .removeClass("fa fa-chevron-down")
        .addClass("fa fa-chevron-up"); 
    } else {
      $(".set > a i")
        .removeClass("fa fa-chevron-down")
        .addClass("fa fa-chevron-up");
      $(this)
        .find("i")
        .removeClass("fa fa-chevron-up")
        .addClass("fa fa-chevron-down");
      $(".set > a").removeClass("active");
      $(this).addClass("active");
      $(".content").slideUp(200);
      $(this)
        .siblings(".content")
        .slideDown(200);
    }
  });
});

$(document).ready(function() {
    $(".next").click(function(){
        $(this).parent(".content").css("display", "none");
        $(this).parent(".content").prev(".sub_heading").removeClass("active");
        $(this).parent(".content").parent(".set").next(".set").children(".content").css("display", "block");
        $(this).parent(".content").parent(".set").next(".set").children(".sub_heading").addClass("active");

        console.log("sdnfj")
    })
 });

</script>

<script>
    //define template
var template = $('.form-main').clone();

//define counter
var sectionsCount = 1;

//add new section
$('body').on('click', '.add-more-btn', function() {

    //increment
    sectionsCount++;

    //loop through each input
    var section = template.clone().find(':input').each(function(){

        $('.add_close').hide();
        $('.add-more-btn').hide();

        
        //set id to store the updated section number
        var newId = this.id + sectionsCount;

        //update for label
        //$(this).attr('name', newId);

        //update id
        this.id = newId;

    }).end()

    //inject new section
    .appendTo('.content2');
   //$(this).find('.add_close').show();
   $(".add_close").hide();
   $(this).parent().parent().next().find(".add_close").show();
   var dateToday = new Date();
    $( ".datepicker" ).each(function(){ 
      $(this).datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      minDate: dateToday
    });
    });
    return false;

});


//remove section
$('body').on('click', '.add_close', function() {

    
    //fade out section
  $(this).parent().parent().prev().find(".add_close").show();
  $(this).parent().parent().prev().find(".add-more-btn").show();
    $(this).parent().parent().remove();


    if ( $('.content2 > .form-main').length > 1 ){
        console.log($('.content2 > .form-main').length);
        // 
        console.log("ani")       
}else{
    
     $('.content2 > .form-main').last().find('.add_close').hide();
}




});




</script>

<script>
    function Cloud(value)
    {
            console.log(value);
             $.ajax
            ({ 
                type: 'GET', 
                url: 'https://developers.onemap.sg/commonapi/search?returnGeom=Y&getAddrDetails=Y&pageNum=1&searchVal='+value, 
                data: { get_param: 'value' }, 
                dataType: 'json',
                success: function (data) 
                { 
                     $.each(data.results, function(index, element) 
                    {	
                        var address = element.ADDRESS;
                        //console.log(address);
                         $( "input[name=addr1]").val(address);
                    });
                }
            });
    }
    $( "input[name=postal]").keyup(function() 
    { //alert('hi');
            var value =  $(this).val();
            //console.log(value);
            Cloud(value);
    });
   


</script>

<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js"></script>
<script type="text/javascript">
        $(document).ready(function(){

            // Custom method to validate username
            
            $(".next").click(function(){
                var form = $("#work-injury-insurance");
                form.validate({
                    errorElement: 'span',
                    errorClass: 'help-block',
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest('.form-group').addClass("has-error");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).closest('.form-group').removeClass("has-error");
                    },
                    rules: {
                        ins_party_nm : {
                            required: true,
                        },
                        er_cpfno : {
                            required: true,
                            minlength: 7,
                            maxlength: 7,
                          
                        },
                        email:{
                            required: true,
                            email: true
                        },
                        postal:{
                            required: true,
                            minlength: 4,
                        },
                        
                        contact_person:{
                            required: true,
                           
                        },
                        ph_no: {
                            required: true,
                        },
                       
                      
                        ins_class: {
                            required: true,
                        },
                        
                        planamount: {
                            required: true,
                        },
                        
                         plan_selection: {
                            required: true,
                        },
                        
                        
                    },
                    messages: {
                        ins_party_nm: {
                            required: "Field is required!",
                        },
                        er_cpfno : {
                            required: "Field is required!",
                        
                        },
                        email : {
                            required: "Field is required!",
                            email: 'Please enter a valid email'
                        },
                        postal: {
                            required: "Field is required!",
                        },
                        
                        contact_person: {
                            required: "Field is required!",
                        },
                        ph_no: {
                            required: "Field is required!",
                        },
                       
                        ins_class: {
                            required: "Field is required!",
                        },
                        
                        planamount: {
                            required: "Field is required!",
                        },
                        
                        plan_selection: {
                            required: "Field is required!",
                        },
                       
                    }
                    
                    
                });
                if (form.valid() === true){
                    if ($('#account_information').is(":visible")){
                        current_fs = $('#account_information');
                        next_fs = $('#company_information');
                    }else if($('#company_information').is(":visible")){
                        current_fs = $('#company_information');
                        next_fs = $('#personal_information');
                    }
                    
                    next_fs.show(); 
                    current_fs.hide();
                }
                
                //$(".form-main").each(function(){ 
                
                $("#wp_no").rules("add", {
                    required:true,
                    
                    messages: {
                           required: "This field is required."
                    }
                });
                
                $("#ins_person").rules("add", {
                    required:true,
                    
                    messages: {
                           required: "This field is required."
                    }
                });
                
                 $("#passport").rules("add", {
                    required:true,
                    
                    messages: {
                           required: "This field is required."
                    }
                });
                
                 $("#planamount").rules("add", {
                    required:true,
                   
                    messages: {
                           required: "This field is required."
                    }
                });
                
                $("#plan_selection").rules("add", {
                    required:true,
                   
                    messages: {
                           required: "This field is required."
                    }
                });
             
                //});
                
             ///// Premimum Calculation Value Get ///////////
             
              //var planamount= $("#planamount").val();
              //alert(planamt);
              //var plan_selection= $("#plan_selection option:selected").val();
              //alert(planselection);
            //var planamount=[];
            //$(".etamount").each(function(){
            //    planamount.push( $(this).val());
            //    alert(planamount);
            //}); 
            //var plan_selection=[];
            //$(".planselectionamt option:selected").each(function(){
            //    plan_selection =  $(this).val();
            //    alert(plan_selection);
            //});
            //alert(planamount);
            //alert(plan_selection);
            var string = '';
	    $('.form-main .custom_form').each(function(){ 
		var planamount = $(this).find('.etamount').val(); 
                //alert(planamount);
                var gstamt=7;
                var prestr=" (Premium inclusive of 7% GST)";
		var plan_selection = $(this).find('.planselectionamt option:selected').val();
               //alert(plan_selection);
                //string += "PREMIUM: " + planamount + " (ACT + COMMON LAW)";
                string += "PREMIUM: ";
                var occup="OCCUPATION: "
                //string += " [" + plan_selection + "] = ";
                //occup += " [" + plan_selection + "] = ";

            if(plan_selection=='ADVERTISING CONTRACTORS' 
            || plan_selection=='BEAN CURD / SAUCE MANUFACTURERS (FOOD)' 
            || plan_selection=='BOOKBINDERS' 
            || plan_selection=='CLOTHING MANUFACTURER / WHOLESALER' 
            || plan_selection=='CLUBS (RECREATION)' 
            || plan_selection=='CONFECTIONERS / BAKING MANUFACTURER' 
            || plan_selection=='EXHIBITORS' 
            || plan_selection=='HEALTH PRODUCTS DEALER' 
            || plan_selection=='PACKERS' 
            || plan_selection=='PRINTING COMPANY' 
            || plan_selection=='RESTAURANTS' 
            || plan_selection=='SHOPS & RETAILS STORES' 
            || plan_selection=='CLINIC') 
            {
            if(planamount<=24000){ 
                var premiumamt=250; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>'; 
                //alert(string);
                $("#totpreamt").html(string); } 
            if(planamount>24000){ 
                var premiumamt=300; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>'; 
                $("#totpreamt").html(string); }  
            }
            
    if(plan_selection=='AGRICUTURAL PERSONNEL' 
            || plan_selection=='CARPENTERS & JOINERS' 
            || plan_selection=='WOODWORKING MACHINISTS') 
            {
            if(planamount<=24000){ 
                var premiumamt=400; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }  
            if(planamount>24000){ 
                var premiumamt=450; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>'; 
                $("#totpreamt").html(string); }   
            } 
            
    if(plan_selection=='AIR CONDITIONING' 
            || plan_selection=='BEDDING & MATTRESS MANUFACTURERS' 
            || plan_selection=='CAR CLEANERS' 
            || plan_selection=='FOOD CATERERS' 
            || plan_selection=='INTERIOR DESIGN CO' 
            || plan_selection=='SHOE MANUFACTURERS' 
            || plan_selection=='WINDOW BLIND MAKERS' 
            || plan_selection=='GLASS MANUFACTURERS') 
            {
            if(planamount<=24000){ 
                var premiumamt=300; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>'; 
                $("#totpreamt").html(string); }  
            if(planamount>24000){ 
                var premiumamt=350; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }   
            } 
            
    if(plan_selection=='BRICK & TILE MAKERS' 
            || plan_selection=='CLEANER (INTERIOR)' 
            || plan_selection=='CLEANING / GRASS CUTTING CONTRACTORS' 
            || plan_selection=='ELECTRICAL CONTRACTOR' 
            || plan_selection=='ENGINEERING CONTRACTOR' 
            || plan_selection=='FRAME MANUFACTURER' 
            || plan_selection=='FURNITURE MANUFACTURERS' 
            || plan_selection=='GENERAL CONSTRUCTION' 
            || plan_selection=='MANUFACTURER OF CONTAINERS & BOXES' 
            || plan_selection=='PAINTER AND DECORATORS' 
            || plan_selection=='PLASTIC GOODS MAKERS' 
            || plan_selection=='TRANSPORTATION / FORWARDERS' 
            || plan_selection=='VETERINARY PERSONNEL' 
            || plan_selection=='WELDERS') 
            {
            if(planamount<=24000){ 
                var premiumamt=350; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }  
            if(planamount>24000){ 
                var premiumamt=400; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>'; 
                $("#totpreamt").html(string); }   
            } 
            
    if(plan_selection=='CLERK' 
            || plan_selection=='OFFICE PREMISES' 
            || plan_selection=='TEACHER (PRE-SCHOOL / NURSERY)') 
            {
            if(planamount<=24000){ 
                var premiumamt=150; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); } 
            if(planamount>24000){ 
                var premiumamt=200; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }  
            }  
            
    if(plan_selection=='ENTERTAINMENT' 
            || plan_selection=='MONK / NUN / RELIGIOUS WORKER' 
            || plan_selection=='MOTOR GARAGE / SHOWROOMS') 
            {
            if(planamount<=24000){ 
                var premiumamt=200; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }  
            if(planamount>24000){ 
                var premiumamt=250; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }   
            }
            
    if(plan_selection=='PERFORMING ARTISTES') 
            {
            if(planamount<=24000){ 
                var premiumamt=70; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }   
            if(planamount>24000){ 
                var premiumamt=70; var totpreamtGST= (premiumamt*gstamt)/100; var toaAMT= premiumamt+ totpreamtGST; string += toaAMT+ prestr +'<br/>'+ occup + plan_selection +'<br/>';  
                $("#totpreamt").html(string); }  
            }




	   });



  
             
           
                
            });

            $('#previous2').click(function(){
                if($('#company_information').is(":visible")){
                    current_fs = $('#company_information');
                    next_fs = $('#account_information');
                }else if ($('#personal_information').is(":visible")){
                    current_fs = $('#personal_information');
                    next_fs = $('#company_information');
                }
                next_fs.show(); 
                current_fs.hide();
            });

            $('#previous').click(function(){
                if($('#company_information').is(":visible")){
                    current_fs = $('#company_information');
                    next_fs = $('#account_information');
                }else if ($('#personal_information').is(":visible")){
                    current_fs = $('#personal_information');
                    next_fs = $('#company_information');
                }
                next_fs.show(); 
                current_fs.hide();
            });
 
        });
    </script>
 <script>   
    $('#work-injury-insurance').submit(function() {
   
    /*var ins_class=$('#ins_class').val();
    if ($("#ins_class:checked").length == 0){
        $('.radioValidation').text("Field is required!");
        return false;
    }*/
    
    if (!jQuery("#condition").is(":checked")) {
        $('.checkValidation').text("Field is required!");
        return false;
    }
});
</script>


<?php if(isset($_REQUEST['ins'])){ $uid=$_REQUEST['ins']; ?>
<?php  $postall_id = $wpdb->get_results("SELECT *from ins_policy_applicants where insumem_ID=$uid"); //print_r($post_id); ?>
<?php  if(isset($postall_id)){ $worker_ids = count($postall_id); //echo $worker_ids; ?>
<?php    for($i = 0; $i < $worker_ids; $i++){ ?>
<?php        echo '<script>$("body .add-more-btn").trigger("click");</script>'; ?>
<?php    } ?>
<?php }} //end $_REQUEST ?>

<?php if(isset($_REQUEST['ins'])){ $uid=$_REQUEST['ins']; ?>
<?php $postall_id = $wpdb->get_results("SELECT *from ins_policy_applicants where insumem_ID=$uid"); //print_r($post_id); ?>
<script>
jQuery('.form-main').each(function(index){ //console.log(this); 
  var html = this;  
  if(index > 0){
   <?php  if(isset($postall_id)){ $worker_ids = count($postall_id); //echo $worker_ids; ?>
   <?php    for($i = 0; $i < $worker_ids; $i++){ ?> 
   var data = <?php echo json_encode($postall_id[$i]); ?>; console.log(data);

   jQuery(html).find('#wp_no'+(index+1)).val(data.wp_no); 
   jQuery(html).find('#ins_person'+(index+1)).val(data.ins_person); 
   jQuery(html).find('#eff_date'+(index+1)).val(data.eff_date); 
   jQuery(html).find('#passport'+(index+1)).val(data.passport);
    jQuery(html).find('#nationality'+(index+1)).val(data.nationality); 
    jQuery(html).find('#plan_selection'+(index+1)).val(data.plan_selection);
     jQuery(html).find('#planamount'+(index+1)).val(data.planamount);

   var dobarr=data.dob.split("-")
    jQuery(html).find('#dateo'+(index+1)).val(dobarr[2]);
    jQuery(html).find('#montho'+(index+1)).val(dobarr[1]);
    jQuery(html).find('#yearo'+(index+1)).val(dobarr[0]);
   <?php }} ?>
 }
});
</script>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>
