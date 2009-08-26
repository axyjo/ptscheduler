<?php
$template->set_title('Viewing Admin Page');
    global $dbHandle;
    global $teachers;
    global $ldap_return;
    global $template;
    $return .= "<h3>All Current Appointments:</h3>";
    $appointmentsGet = 'SELECT * FROM appointments INNER JOIN teachers ON appointments.teacher=teachers.id ORDER BY `teacher`,`time` ASC';
    $result_res = $dbHandle->query($appointmentsGet);
    $appointments = array();
    while ($result = $result_res->fetch()) $appointments[] = $result;
  //  var_dump($appointments);
    $newappointments = array();
    foreach($appointments as $appointment) {
      $time = date('l, F j Y', $appointment['time']);
      $name = $appointment['fname'] . ' ' . $appointment['lname'];
      $newappointments[$time][$name][] = $appointment;
    }
    //var_dump($appointments);
    $hadAppointments = false;
    foreach($newappointments as $date => $dateval) {
      $hadAppointments = true;
      $return .= '<h4>'.$date.'</h4>';
      foreach($dateval as $teacher => $appointment) {
//		var_dump($appointment);
        $return .= '<h5>'.$teacher.'</h5>';
        foreach ($appointment as $thing) {
//        $return .= date('r', $appointment[0]['time']).' - '.$appointment[0]['student'].' with '.$appointment[0]['fname'].' '.$appointment[0]['lname'];
        $return .= date('r', $thing['time']).' - '.$thing['student'].' with '.$thing['fname'].' '.$thing['lname'];
                $return .= '<br />';
        }

      }
      
    }

    if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';
    $template->set_content($return);
  