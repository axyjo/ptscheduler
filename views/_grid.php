<?php

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
          if ($_SESSION['user_access'] == USER_TEACHER || $_SESSION['user_access'] == USER_ADMIN) {
            $class = 'yellow';
            $title = 'Break';
          } else {
            $class = 'red';
            $title = 'Unavailable';
          }
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
      if ($_SESSION['user_access'] == USER_TEACHER || $_SESSION['user_access'] == USER_ADMIN) {
        $title = 'Appointment with: ';
        foreach($appointments[$epoch] as $appointment) {
          $parent = getUser($appointment['parent']);
          $title .= $parent['lname'].', ';
          if($appointment['parent'] == -1) {
            $class = 'yellow';
            $title = 'Break';
            break;
          }
        }
        // Remove the last comma+space.
        $title = substr($title, 0, -2);
      } else {
        $title = 'Unavailable';
      }
    }

    $time = $hour.$minute;

    $return .= '<span title="'.$title.'" class="'.$class.' times" id="'.$teacher['id'].'-'.$epoch.'">'.$time.'</span>';
  }
  $return .= '<br />';
}
$return .= '</div>';
