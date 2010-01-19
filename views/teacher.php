<?php

$template->set_title('Viewing Teacher Page');

$return = '<br /><strong>Currently logged in as: </strong>'. $_SESSION['username'].'. Click <a href="index.php?logout">here</a> to logout.';

$return .= '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use Firefox or Internet Explorer as your browser (with Javascript enabled). Safari does not work well.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>

<li>Please email <a href="mailto:jesse-remington@acs.sch.ae">jesse-remington@acs.sch.ae</a> if you have problems.</li> </ul>';

$return .= '<h3>Your Current Appointments (<a href="javascript:window.print()">Print</a>):</h3>';
$time = time() - 300;
$getQuery = 'SELECT * FROM appointments WHERE `teacher`= "'.$user_id.'" ORDER BY `time` ASC';
$result_res = $dbHandle->query($getQuery);
$appointments = array();

while ($result = $result_res->fetch()) $appointments[] = $result;
$hadAppointments = false;
foreach($appointments as $appointment) {
  $hadAppointments = true;
  $parent_id = $appointment['parent'];
  $res = $dbHandle->query('SELECT * FROM users WHERE id='.$parent_id);
  $parent = $res->fetch();
  if($appointment['parent'] != -1)  $return .= date($date_format, $appointment['time']).' - Family of '.$parent['desc'].' '.$parent['lname'];
  $return .= '<br />';
}

if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';

$times = return_times();
$tabular_times = array();
for($i = 0; $i < count($times); $i++) {
  if (!isset($tabular_times[date('i', $times[$i])])) {
    $tabular_times[date('i', $times[$i])] = array();
  }
  $tabular_times[date('i', $times[$i])][date('H', $times[$i])] = $times[$i];

}

$sql = 'SELECT * FROM appointments WHERE teacher='.$user_id;
$app_res = $dbHandle->query($sql);
$appointments = array();
while ($result = $app_res->fetch()) $appointments[] = $result;
$newappointments = array();
foreach($appointments as $appointment) {
  $newappointments[$appointment['time']][] = $appointment;
}
$teacher = $teachers[$user_id];
$return .= '<div id="'.$user_id.'">';
$return .= '<span class="teacher grid_6"><strong>';
$return .= $teacher['fname'].' '.$teacher['lname'];
$return .= '</strong > - <a id="link_'.$user_id.'">Click here to view available appointments</a></span><br />
  <div class="grid_2 throbber" id="throbber_'.$user_id.'"></div>
  <div id="times_'.$user_id.'">';
foreach($tabular_times as $minute => $hours_array) {
  $i = 0;
  foreach($hours_array as $hour => $epoch) {
    if(isset($newappointments[$epoch])) {
      if($newappointments[$epoch][0]['parent'] == -1) {
        //break
        $class = 'yellow';
        $title = 'Break';
      } else {
        //real appointment
        $class = 'red';
        $sql = 'SELECT * FROM users WHERE id='.$newappointments[$epoch][0]['parent'];
        $parent_res = $dbHandle->query($sql);
        $parent = $parent_res->fetch();
        $title = 'Appointment with: '.$parent['fname'].' '.$parent['lname'].' ('.$parent['desc'].')';
      }
    } else {
      //free
      $class = 'green';
      $title = 'Available';
    }

    $time = $hour.$minute;

    $return .= '<span title="'.$title.'" class="'.$class.' times grid_1 push_2" id="'.$user_id.'-'.$epoch.'">'.$time.'</span>';
  }
  $return .= '<br />';
}
$return .= '</div></div>';

$return .= '<div id="dialog"></div>';
$template->set_content($return);