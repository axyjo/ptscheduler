<?php
$template->setTitle('Viewing Admin Page');

$return = '<br /><strong>Currently logged in as: </strong>'. $_SESSION['username'].'. Click <a href="index.php?logout">here</a> to logout.';
$return .= '<br />Click <a href="index.php?list">here</a> to view appointments in terms of each user (teachers and parents).';

$return .= '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use a Javascript enabled browser to make your appointments.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>

<li>Please contact <a href="mailto:'.$support_email.'">'.$support_email.'</a> if you have problems.</li> </ul>';

$return .= '<br />';
$tabular_times = tabularTimes();

getAllTeachers();
foreach($teachers as $teacher) {
  include(ROOT.'/views/_grid.php');
}

$template->setContent($return);
