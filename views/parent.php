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

$user = getUser($_SESSION['user_id']);
include(ROOT.'/views/_appointments.php');

$tabular_times = tabularTimes();
$teachers = getAllUsersByStatus(USER_TEACHER);
$return .= '<div id="time_grid">';
foreach($teachers as $teacher) {
  include(ROOT.'/views/_grid.php');
}
$return .= '</div>';

$template->setContent($return);
