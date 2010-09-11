<?php
$template->setTitle('Viewing Parent Page');

$return = '<br /><strong>Currently logged in as: </strong>'. $_SESSION['username'].'. Click <a href="index.php?logout">here</a> to logout.';

$return .= '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use a Javascript enabled browser to make your appointments.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If <strong>an error occurs</strong> while scheduling your appointment, please refresh the page.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>

<li>Locations of where teachers will be for conferences be emailed to you. They will also be available on the window of the HS office.</li>

<li>Please contact <a href="mailto:'.$support_email.'">'.$support_email.'</a> if you have problems.</li> </ul>';

$return .= '<h3>Your Current Appointments (<a href="javascript:window.print()">Print</a>):</h3>';

getAllTeachers();

$time = time() - 300;
$getQuery = 'SELECT * FROM appointments WHERE `parent`= "'.$user_id.'" ORDER BY `time` ASC';
$result_res = $dbHandle->query($getQuery);
$appointments = array();
while($result = $result_res->fetch()) {
  $appointments[] = $result;
  $return .= '<br />';
  $return .= date($date_format, $result['time']).' - '.$teachers[$result['teacher']]['fname'].' '.$teachers[$result['teacher']]['lname'];
}
if (count($appointments) == 0) $return .= 'Sorry, you do not have any appointments in the future.<br /><br />';

$tabular_times = tabularTimes();
getAllTeachers();
$return .= '<div id="time_grid">';
foreach($teacher_list as $teacher) {
  include(ROOT.'/views/_grid.php');
}
$return .= '</div>';

$template->setContent($return);
