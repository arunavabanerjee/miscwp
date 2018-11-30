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
                    <h3>our products</h3>
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


// $('.content2').delegate('.add-more-btn','click',function() {
//     // setTimeout(function(){
//          var clone = $('.content2 > .form-main').last().clone();
//          console.log("asdad ", clone);
//           $('.add_close').hide();
//           $('.add-more-btn').hide();
//           $('.content2').append(clone);
//           // $(this).css("display", "none");
//           $('.content2 > .form-main').last().find('.add_close').show();
//     // }, 2000);   
// });

// $('.content2').delegate('.add_close','click',function() {        
//             $('.content2 > .form-main').last().remove();
//             if ( $('.content2 > .form-main').length > 1 ) {
//                 $('.content2 > .form-main').last().find('.add_close').show();
//                 $('.content2 > .form-main').last().find('.add-more-btn').show();
//             } else { 
//                 $('.content2 > .form-main').last().find('.add_close').hide();
//                 $('.content2 > .form-main').last().find('.add-more-btn').show();
//             }
// });
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

// $(".add_close").click(function(event) {
//     $(this).parent().parent().remove();
//     console.log("dfjgdf")
// });


//remove section
$('body').on('click', '.add_close', function() {
    
    //fade out section
  $(this).parent().parent().prev().find(".add_close").show();
  $(this).parent().parent().prev().find(".add-more-btn").show();
  $(this).parent().parent().remove();

    if ( $('.content2 > .form-main').length > 1 ){
        console.log($('.content2 > .form-main').length);
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
                var form = $("#foreign_worker");
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
                
                /*$(".form-main").each(function(){ alert('this'); 
		   var wp_no = $(this).find("#wp_no").val();
		   if( wp_no == '' ){ 
		      $(this).find("#wp_no").after('<span>This field is required</span>'); return false;			
		   }
                   
		});*/

                $("#foreign_worker").validate();
                $(".form-main .wp_no").rules("add", {
                    required:true,
                    
                    messages: {
                           required: "This field is required."
                    }
                });
                
                $(".form-main #ins_person").rules("add", {
                    required:true,
                    
                    messages: {
                           required: "This field is required."
                    }
                });
                
                $(".form-main #occupation").rules("add", {
                    required:true,
                   
                    messages: {
                           required: "This field is required."
                    }
                });
                
                $(".form-main #passport").rules("add", {
                    required:true,
                   
                    messages: {
                           required: "This field is required."
                    }
                });
                
                //});
                
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
    $('#foreign_worker').submit(function() {
    var ins_class=$('#ins_class').val();
    if ($("#ins_class:checked").length == 0){
        $('.radioValidation').text("Field is required!");
        return false;
    }
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
<?php  } ?>
<?php //echo '<script>$(".mb-2").each(function(){ $(this).css("display","none"); });</script>'; ?>
<?php } //end $_REQUEST ?>

<?php if(isset($_REQUEST['ins'])){ $uid=$_REQUEST['ins']; ?>
<?php $postall_id = $wpdb->get_results("SELECT *from ins_policy_applicants where insumem_ID=$uid"); //print_r($post_id); ?>
<script>
jQuery('.form-main').each(function(index){  //console.log(index); //console.log(this); 
 var html = this; 
 if(index >= 1){
   <?php  if(isset($postall_id)){ $worker_ids = count($postall_id); //echo $worker_ids; ?>
   <?php  for($i = 0; $i < $worker_ids; $i++) { ?> 
   var data = <?php echo json_encode($postall_id[$i]); ?>; console.log(data);
   jQuery(html).find('#wp_no'+(index+1)).val(data.wp_no); 
   jQuery(html).find('#ins_person'+(index+1)).val(data.ins_person); 
   var effarr = data.eff_date.split("-");
   jQuery(html).find('#eff_date'+(index+1)).val(data.eff_date); 
   jQuery(html).find('#passport'+(index+1)).val(data.passport);
   jQuery(html).find('#nationality'+(index+1)).val(data.nationality); 
   jQuery(html).find('#occupation'+(index+1)).val(data.occupation);
   var dobarr=data.dob.split("-"); //console.log(dobarr);
   jQuery(html).find('#dateo'+(index+1)).val(dobarr[2].replace(/^0+/, ''));
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
