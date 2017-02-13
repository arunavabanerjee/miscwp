
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
    
    //-baidu maps------------------------------------------------------------
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=DBa5badfc49c1ce0148b174ae3bdf0ea"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
         //$('.lcn').click(function(){
         /* var lang=113.52897592105;
            var lat=23.142106886029;	
            var point = new BMap.Point(lang,lat);	
            var address='上海大酒店，上海，中国';
            geocoder = new BMap.Geocoder(); //alert(geocoder)
            geocoder.getPoint(address, function(res){ 
               console.log(res); //console.log(res);
            }) 
         */
         var address=$('#address1').val();
         //var city=$('#city').val(); //alert(address); //var lat=$('#lat').val();
         var map = new BMap.Map("dituContent1");
         geocoder = new BMap.Geocoder(); 
         // geocoder.getPoint('北京市东城区长巷二条乙5号', function(res){
         geocoder.getPoint(address, function(res){
           console.log(res); //console.log(res)
           console.log(res.lat)
           var lng=res.lng; var lat=res.lat; //alert(res.address);
           //var address=res.address; //$('#address').html(address);
           var point = new BMap.Point(lng,lat); //log(res.lat)
           var sContent = "<h4 style='margin:0 0 5px 0;padding:0.2em 0'></h4>" +
                          "<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+address+"</p></div>";
           //var icon = new BMap.Icon('http://www.transcommgroup.com/assets/img/pin1.png', new BMap.Size(20, 32), {//
           //                 anchor: new BMap.Size(10, 30),
           //                 infoWindowAnchor: new BMap.Size(10, 0)
           //});
           var icon = new BMap.Icon('http://www.swisschamofcommerce.com/wp-content/uploads/2017/02/map-marker.png', new BMap.Size(20, 32), {
                          anchor: new BMap.Size(10, 30),
                          infoWindowAnchor: new BMap.Size(10, 0)
           });
           var marker = new BMap.Marker(point, { icon: icon, title: address }); 
           <!--AXIUS: opts variable has been included and the same has been set below -->
           /* var opts = { 
                   //width : 500, //height: 70, title : "Shanghai Office" , enableMessage:true, 
                   //message:"北京市朝阳区光华路4号东方梅地亚中心A座903室" } */
           <!--AXIUS: opts variable set here below -->
           //var marker = new BMap.Marker(point);
           var infoWindow = new BMap.InfoWindow(sContent);
           map.centerAndZoom(point, 15);
           map.enableScrollWheelZoom();  
           map.addOverlay(marker);
           marker.addEventListener("click", function(){
              this.openInfoWindow(infoWindow);
              document.getElementById('imgDemo').onload = function (){
                infoWindow.redraw();
              }
           });
        }); 
      });
   //  });
   </script>






