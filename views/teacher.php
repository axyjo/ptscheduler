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
$teacher = getUser($_SESSION['user_id']);
$return .= '<div id="time_grid">';
include(ROOT.'/views/_grid.php');
$return .= '</div>';

$template->setContent($return);
