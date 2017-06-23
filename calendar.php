
//create a 3 week calendar
<?php 
function draw_calendar(){
  //parameters of the calendar. 
  $weekCnt = 1; $days_in_a_week = 7; 
  $current_date = date('j'); $current_month = date('n'); $current_year = date('Y'); 
  $current_no_of_day = date('w'); $number_of_days_curr_month = date('t'); 
  $number_of_days_prev_month = date('t',mktime(0,0,0,($current_month-1),1,date('Y'))); 
  $number_of_days_next_month = date('t',mktime(0,0,0,($current_month+1),1,date('Y'))); 

  //$current_date = 1; $current_no_of_day = 4;
  //$current_date = 6; $current_no_of_day = 2;
  //$current_date = 14; $current_no_of_day = 3; 
  //$current_date = 20; $current_no_of_day = 2; 

  //create the table header for the current month
  echo '<h2>'.date('F').' '.date('Y').'</h2>';
  $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
  $headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
  $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

  if($current_no_of_day < 6 && $current_date < 7){ 
    $startel = ($current_date - $current_no_of_day); //echo $startel;
    if($startel < 0){ $startel = $number_of_days_prev_month + $startel; } 
  }
  if($current_date >= 7 &&  $current_date < 31){ $startel = ($current_date - $current_no_of_day); }
  if($current_date == 31){ $startel = ($current_date - $current_no_of_day); }
  //display the calendar 
  while(true){
    if($weekCnt > 3){ break; } 
    $calendar.= '<tr class="calendar-row">';
    for($x = 0; $x < $days_in_a_week; $x++):
      $calendar.= '<td class="calendar-day-np">'.$startel.'</td>'; $startel++; 
      if($startel > 31 && $weekCnt < 3){ $startel = 1; } 
      if($startel > $number_of_days_curr_month){ $startel = 1; }
    endfor;
    $calendar.='</tr>'; $weekCnt++; 
  }
  $calendar.= '</table>';	
  return $calendar;
} ?>


      if($weekCnt == 1){ 
         if($startel != $current_date){ $calendar.= '<td class="calendar-day-np" readonly="readonly">'.$startel.'</td>'; $startel++; 
         } else { $calendar.= '<td class="calendar-day-np">'.$startel.'</td>'; $startel++; } 
      }
      else{ $calendar.= '<td class="calendar-day-np">'.$startel.'</td>'; $startel++; } 	
      if($startel > 31 && $weekCnt < 3){ $startel = 1; } 
      if($startel > $number_of_days_curr_month){ $startel = 1; }
