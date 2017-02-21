        //--mail being sent to admin email. 
        if(!empty($changes)){
         $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
	 $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
	 $message = '<!DOCTYPE HTML><html>
		    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Profile Changes</title></head>';
         $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
		     <tr><td><img src="images/logo.png" style="display:block"></td></tr>
		     <tr style="text-align:left;font-size:20px;color:#009fff">
			<td style="padding-top:30px;">Member Name:'.$family_name.'-'.$given_name.'</td></tr>
                     <tr style="text-align:left;font-size:18px;color:#009fff">
			<td style="padding-top:30px;padding-bottom:30px;"> The following changes have been made in Personal Section</td></tr>';
         $message .= '<hr/>'; 
         foreach($changes as $key => $change){ 
           $message .= '<tr style="text-align:left;font-size:16px;color:#aa8095">
			  <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
           $message .= $key.' : '. $change;
           $message .= '<hr/>';
	   $message .= '</td></tr>'; 
         }
         $message .= '<tr style="text-align:center;height:30px;"></tr>';							
         $message .= '<tr style="text-align:center;font-size:14px;">
		      <td style="height:30px; color:#fff; background:#306C9E; padding-top:20px; padding-bottom:20px;">All rights reserved,2016.Swiss Chinese Chamber of Commerce</td></tr>';
         $message .= '</table></body></html>';
						
	 $headers = "MIME-Version: 1.0" . "\r\n";
	 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
	 $headers .= "From: info@cham.org\r\n";
	 @mail($to, $subject, $message, $headers);
        }

//email templates
//--mail being sent to admin email. 
if(!empty($changes)){
   $to  = 'arunava@sketchwebsolutions.com'; //$to  = $email;
   $subject = 'Changes Made To User Profile:'.$family_name.'-'.$given_name;
   $message = '<!DOCTYPE HTML><html>
		    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <title>Profile Changes</title></head>';
   $message .= '<body><table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse" align="center">
	     <tr><td><img src="images/logo.png" style="display:block"></td></tr>
	     <tr style="text-align:center;font-size:24px;color:#009fff">
		<td style="padding-top:30px;">Name:'.$family_name.'-'.$given_name.'</td></tr>
                <tr style="text-align:center;font-size:24px;color:#009fff">
		<td style="padding-top:30px;"> The following changes have been made in Personal Section</td></tr>';
  $message .= '<hr/>'; 
  foreach($changes as $key => $change){ 
    $message .= '<tr style="text-align:center;font-size:14px;color:#aa8095">
	  <td style="padding-top:17px;padding-bottom:5px; font-family:arial; color:#333;">'; 
    $message .= $key.' : '. $change;
    $message .= '<hr/>';
    $message .= '</td></tr>'; 
  }									
  $message .= '<tr style="background:#306C9E;color:#fff;text-align:center;font-size:14px;">
	      <td style="height:30px; color:#fff;">All rights reserved,2016.Swiss Chinese Chamber of Commerce </td></tr>';
  $message .= '</table></body></html>';
						
 $headers = "MIME-Version: 1.0" . "\r\n";
 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";					
 $headers .= "From: info@cham.org\r\n";
 @mail($to, $subject, $message, $headers);

}

// email templates
//--mail being sent to admin email. 
$changes = array();
//--get the user data from database
$selSql = "SELECT * FROM `sc_c_userdetails` WHERE `userId` = $userId"; $selRows = mysql_query($selSql);
if(mysql_num_rows($selRows) > 0){
  while ($row = mysql_fetch_assoc($selRows)) { 
   if($row['user_title'] != $user_title){ $changes["user_title"] = $user_title; }
   if($row['year_of_birth'] != $year_of_birth){ $changes["year_of_birth"] = $year_of_birth; }
   if($row['family_name'] != $family_name){ $changes["family_name"] = $family_name; }
   if($row['given_name'] != $given_name){ $changes["given_name"] = $given_name; }
   if($row['chinese_name'] != $chinese_name){ $changes["chinese_name"] = $chinese_name; }
   if($row['nationality'] != $nationality){ $changes["nationality"] = $nationality; }
   if($row['positionIn_company'] != $positionIn_company){ $changes["positionIn_company"] = $positionIn_company; }
   if($row['address_english'] != $address_english){ $changes["address_english"] = $address_english; }
   if($row['city_english'] != $city_english){ $changes["city_english"] = $city_english; } 
   if($row['province_area'] != $province_area){ $changes["province_area"] = $province_area; }
   if($row['address_chinese'] != $address_chinese){ $changes["address_chinese"] = $address_chinese; }
   if($row['city_chinese'] != $city_chinese){ $changes["city_chinese"] = $city_chinese; }
   if($row['zip_code_english'] != $zip_code_english){ $changes["zip_code_english"] = $zip_code_english; }
   if($row['direct_phone'] != $direct_phone){ $changes["direct_phone"] = $direct_phone; }
   if($row['mobile_phone'] != $mobile_phone){ $changes["mobile_phone"] = $mobile_phone; }
   //if($row['direct_email'] != $direct_email){ $changes["direct_email"] = $direct_email; }
   if($row['npo_organization_type'] != $npo_organization_type){ 
                                       $changes["npo_organization_type"] = $npo_organization_type; }
   if($row['npo_organization_description'] != $npo_organization_description){ 
                                       $changes["npo_organization_description"] = $npo_organization_description; }
   if($row['media_type'] != $media_type){ $changes["media_type"] = $media_type; }
   if($row['media_description'] != $media_description){ $changes["media_description"] = $media_description; }
  }
} 
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
 $headers .= "From: info@cham.org\r\n";
 @mail($to, $subject, $message, $headers);
}

