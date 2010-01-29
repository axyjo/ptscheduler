<?php

$template->setTitle('Viewing Teacher Page');

$return = '<br /><strong>Currently logged in as: </strong>'. $_SESSION['username'].'. Click <a href="index.php?logout">here</a> to logout.';

$return .= '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use a Javascript enabled browser to make your appointments.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>

<li>Please contact <a href="mailto:'.$support_email.'">'.$support_email.'</a> if you have problems.</li> </ul>';

$return .= '<h3>Your Current Appointments (<a href="javascript:window.print()">Print</a>):</h3>';

$time = time() - 300;
$getQuery = 'SELECT * FROM appointments WHERE `teacher`= "'.$user_id.'" ORDER BY `time` ASC';
$result_res = $dbHandle->query($getQuery);
$appointments = array();
while($result = $result_res->fetch()) {
  $appointments[] = $result;
  // The <br> tag is on the outside of the check so that even breaks are
  // counted into calculation. This is so that teachers can visually see break
  // periods in their printed schedule.
  $return .= '<br />';
  if($result['parent'] != -1)  {
    $parent = getUser($result['parent']);
    $return .= date($date_format, $result['time']).' - Family of '.$parent['desc'].' '.$parent['lname'];
  }
}
if (count($appointments) == 0) $return .= 'Sorry, you do not have any appointments in the future.<br /><br />';


$tabular_times = tabularTimes();

$sql = 'SELECT * FROM appointments WHERE teacher='.$user_id;
$app_res = $dbHandle->query($sql);
$appointments = array();
while($result = $app_res->fetch()) {
  $appointments[$result['time']] = $result;
}

$teacher = getUser($user_id);
$return .= '<div id="'.$user_id.'">';
$return .= '<span class="teacher grid_6"><strong>';
$return .= $teacher['fname'].' '.$teacher['lname'];
$return .= '</strong > - <a id="link_'.$user_id.'">Click here to view available appointments</a></span><br />
  <div class="grid_2 throbber" id="throbber_'.$user_id.'"></div>
  <div id="times_'.$user_id.'">';
foreach($tabular_times as $minute => $hours_array) {
  foreach($hours_array as $hour => $epoch) {
    if(isset($appointments[$epoch])) {
      if($appointments[$epoch]['parent'] == -1) {
        //break
        $class = 'yellow';
        $title = 'Break';
      } else {
        //real appointment
        $class = 'red';
        $parent = getUser($appointments[$epoch]['parent']);
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
$template->setContent($return);
