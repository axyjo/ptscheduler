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

$return .= '<div id="time_grid">';

foreach($teachers as $teacher) {
  $sql = 'SELECT * FROM appointments WHERE teacher='.$teacher['id'];
  $app_res = $dbHandle->query($sql);
  $appointments = array();
  while($result = $app_res->fetch()) {
    $appointments[$result['time']] = $result;
  }
	$return .= '<div id="'.$teacher['id'].'">';
  $return .= '<span class="teacher grid_6"><strong>';
  $return .= $teacher['fname'].' '.$teacher['lname'];
  $return .= '</strong > - <a id="link_'.$teacher['id'].'">Click here to view available appointments</a></span><br />
    <div class="grid_2 throbber" id="throbber_'.$teacher['id'].'"></div>
    <div style="display:none;" id="times_'.$teacher['id'].'">';
  $script = '$("#link_'.$teacher['id'].'").click(function() { $("#times_'.$teacher['id'].'").toggle(); });';
  $template->addScript($script);
  foreach($tabular_times as $minute => $hours_array) {
    foreach($hours_array as $hour => $epoch) {
      if(isset($appointments[$epoch])) {
          $class = 'red';
          $title = 'Unavailable';
      } else {
        //free
        $class = 'green';
        $title = 'Available';
      }

      $time = $hour.$minute;

      $return .= '<span title="'.$title.'" class="'.$class.' times grid_1 push_2" id="'.$teacher['id'].'-'.$epoch.'">'.$time.'</span>';
    }
    $return .= '<br />';
  }
  $return .= '</div></div>';
}

$return .= '</div>';

$return .= '<div id="dialog"></div>';
$template->setContent($return);
