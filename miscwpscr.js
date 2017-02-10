
// script for loading colours after ajax
    <script>
      $(document).ready(function(){ 
        $('.fullcalendar .eventful li a').each(function(){ 
          var pclass = $(this).prop('class'); 
          if(pclass == 'beijing'){ $(this).parent().css("background-color","#ff8080"); }          
          if(pclass == 'shanghai'){ $(this).parent().css("background-color","#ccddff"); }
          if(pclass == 'guangzhou'){ $(this).parent().css("background-color","#e6ffe6"); }
          if(pclass == 'hongkong'){ $(this).parent().css("background-color","#fff5e6"); }
        });
        $('.fullcalendar .eventful-today li a').each(function(){ 
          var pclass = $(this).prop('class'); 
          if(pclass == 'beijing'){ $(this).parent().css("background-color","#ff8080"); }          
          if(pclass == 'shanghai'){ $(this).parent().css("background-color","#ccddff"); }
          if(pclass == 'guangzhou'){ $(this).parent().css("background-color","#e6ffe6"); }
          if(pclass == 'hongkong'){ $(this).parent().css("background-color","#fff5e6"); }
        });  
      });
      $('div.em-calendar-wrapper').bind("DOMSubtreeModified", hidemsg); 
      function hidemsg(){
        $('.fullcalendar .eventful li a').each(function(){ 
          var pclass = $(this).prop('class'); 
          if(pclass == 'beijing'){ $(this).parent().css("background-color","#ff8080"); }          
          if(pclass == 'shanghai'){ $(this).parent().css("background-color","#ccddff"); }
          if(pclass == 'guangzhou'){ $(this).parent().css("background-color","#e6ffe6"); }
          if(pclass == 'hongkong'){ $(this).parent().css("background-color","#fff5e6"); }
        });
        $('.fullcalendar .eventful-today li a').each(function(){ 
          var pclass = $(this).prop('class'); 
          if(pclass == 'beijing'){ $(this).parent().css("background-color","#ff8080"); }          
          if(pclass == 'shanghai'){ $(this).parent().css("background-color","#ccddff"); }
          if(pclass == 'guangzhou'){ $(this).parent().css("background-color","#e6ffe6"); }
          if(pclass == 'hongkong'){ $(this).parent().css("background-color","#fff5e6"); }
        });
      }
    </script>
    
    
