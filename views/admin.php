<?php
$template->set_title('Viewing Admin Page');
$return = '<br />';
$times = return_times();
$tabular_times = array();
for($i = 0; $i < count($times); $i++) {
  if (!isset($tabular_times[date('i', $times[$i])])) {
    $tabular_times[date('i', $times[$i])] = array();
  }
  $tabular_times[date('i', $times[$i])][date('H', $times[$i])] = $times[$i];

}

foreach($teachers as $teacher) {
  $sql = 'SELECT * FROM appointments WHERE teacher='.$teacher['id'];
  $app_res = $dbHandle->query($sql);
  $appointments = array();
  while ($result = $app_res->fetch()) $appointments[] = $result;
  $newappointments = array();
	foreach($appointments as $appointment) {
 		$newappointments[$appointment['time']][] = $appointment;
  }

  $return .= '<span class="teacher grid_2"><strong>';
  $return .= $teacher['fname'].' '.$teacher['lname'];
  $return .= '</strong></span><br />';
  foreach($tabular_times as $minute => $hours_array) {
    $i = 0;
    foreach($hours_array as $hour => $epoch) {
      if(isset($newappointments[$epoch])) {
        if($newappointments[$epoch]['student'] == 0) {
          //break
          $class = 'yellow';
          $title = 'Break';
        } else {
          //real appointment
          $class = 'red';
          $title = 'Appointment with: '.$newappointments[$epoch]['student'];
        }
      } else {
        //free
        $class = 'green';
        $title = 'Available';
      }

      $time = $hour.$minute;

      $return .= '<span title="'.$title.'" class="'.$class.' times grid_1 push_2" id="t_'.$teacher['id'].'-'.$epoch.'">'.$time.'</span>';
    }
    $return .= '<br />';
  }
}

//if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';
$template->set_content($return);
