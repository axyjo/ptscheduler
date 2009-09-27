<?php
$template->set_title('Viewing Parent Page');
$return = '<h3>Your Current Appointments:</h3>';
$time = time() - 300;
$getQuery = 'SELECT * FROM appointments WHERE `student`= "'.$username.'" ORDER BY `time` ASC';
//var_dump($ldap_return);
//$getQuery = 'SELECT * FROM appointments WHERE `student`="'.$ldap_return['cn'][0].'"';
$result_res = $dbHandle->query($getQuery);
$appointments = array();

while ($result = $result_res->fetch()) $appointments[] = $result;
$hadAppointments = false;
foreach($appointments as $appointment) {
  $hadAppointments = true;
  $return .= '<br />';
  $return .= date('r', $appointment['time']).' - '.$teachers[$appointment['teacher']]['fname'].' '.$teachers[$appointment['teacher']]['lname'];
}

if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';
$return .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
if(!empty($strMsg)) $return .= $strMsg;
$return .= 'Create a new appointment with ';
$return .= '<select name="teacher">';
foreach ($teachers as $teacher) {
  $return .= '<option value="'.$teacher['id'].'">';
  $return .= $teacher['fname'].' '.$teacher['lname'];
}
$return .= '</select>';
$return .= '<br />';
//update the following with js date picker thing.
$return .= ' on <input type="textbox" id="date" name="date" />';
$return .= '<br />';
//update the following with js time picker thing.
$return .= ' at <input type="textbox" id="time" name="time" /><div id="slider"></div>';
$js_func = '
function slideValue() {
  var val = $("#slider").slider("value");
  var end = '.$time_boundaries['end'].';
  var start = '.$time_boundaries['start'].';
  var increment = '.$time_increments.';

  var final = start+(increment*val);
  var minutes = (final/60) % 60 + "";
  var hours = ((final/60)-minutes)/60 + "";
  if(minutes.length == 1) minutes = "0"+minutes;
  if(hours.length == 1) hours = "0"+hours;

  var finalStr = hours + ":" + minutes;
  $("#time").val(finalStr);
}';
$template->add_script($js_func);
//The minus 1 in floor exists because the time boundary is the absolute end time, and not the time the last appointment can start at.
$template->add_script('$(function(){$(\'#slider\').slider({value:0, min:0, max:'.floor((($time_boundaries['end'] - $time_boundaries['start'])/$time_increments)-1).', slide: slideValue, change: slideValue});});');
$return .= '<input type="submit" name="submit" id="submit" value="Do it!" />';
$return .= '</form>';
$template->set_content($return);
