// email templates
//--mail being sent to admin email. 
if(!empty($changes)){
   $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
	 $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
	 $message = '<!DOCTYPE HTML><html>
		           <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
               <title>Profile Changes</title></head>';
   $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
		     <tr><td><img src="img/new_year_bg.jpg" style="display:block"></td></tr>
		     <tr style="text-align:center;font-size:24px;color:#009fff">
			   <td style="padding-top:30px;"> The following changes have been made in Personal Section</td></tr>'; 
         foreach($changes as $key => $change){ 
           $message .= '<tr style="text-align:center;font-size:14px;color:#aa8095">
			                   <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
           $message .= $key.' : '. $change;
	         $message .= '</td></tr>'; 
         }									
   $message .= '<tr style="background:#306C9E;color:#fff;text-align:center;font-size:14px;">
			          <td style="height:30px; color:#fff;">All rights reserved,2016.Swiss Chinese Chamber of Commerce </td></tr>';
   $message .= '</table></body></html>';
						
	 $headers = "MIME-Version: 1.0" . "\r\n";
	 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
	 $headers .= "From: info@bei.swisscham.org\r\n";
	 @mail($to, $subject, $message, $headers);
}

