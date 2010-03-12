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
  $sql = 'SELECT * FROM appointments WHERE teacher='.$teacher['id'];
  $app_res = $dbHandle->query($sql);
  $appointments = array();
  while($result = $app_res->fetch()) {
    $appointments[$result['time']][] = $result;
  }
  $return .= '<div id="'.$teacher['id'].'">';
  $return .= '<span class="teacher grid_2"><strong>';
  $return .= $teacher['fname'].' '.$teacher['lname'];
  $return .= '</strong></span><br />
    <div class="grid_2 throbber" id="throbber_'.$teacher['id'].'"></div>';
  foreach($tabular_times as $minute => $hours_array) {
    foreach($hours_array as $hour => $epoch) {
      if(!isset($appointments[$epoch]) || !is_array($appointments[$epoch])) {
        $appointments[$epoch] = array();
        $class = 'green';
        $title = 'Available';
        if($simultaneous_appointments > 1) {
          $title .= ' ('.$simultaneous_appointments.'/'.$simultaneous_appointments.')';
        }
      }
      $count = count($appointments[$epoch]);
      if($count < $simultaneous_appointments) {
        foreach($appointments[$epoch] as $appointment) {
          if($appointment['parent'] == -1) {
            $class = 'yellow';
            $title = 'Break';
            break;
          }
          $class = 'green';
          $title = 'Available';
          if($simultaneous_appointments > 1) {
            $title .= ' ('.($simultaneous_appointments-$count).'/'.$simultaneous_appointments.')';
          }
        }
      } else {
        $class = 'red';
        $title = 'Appointment with: ';
        foreach($appointments[$epoch] as $appointment) {
          $parent = getUser($appointment['parent']);
          $title .= $parent['lname'].' ('.$parent['desc'].')';
          if($appointment['parent'] == -1) {
            $class = 'yellow';
            $title = 'Break';
            break;
          }
        }
      }

      $time = $hour.$minute;

      $return .= '<span title="'.$title.'" class="'.$class.' times grid_1 push_2" id="'.$teacher['id'].'-'.$epoch.'">'.$time.'</span>';
    }
    $return .= '<br />';
  }
  $return .= '</div>';
}

$template->setContent($return);
