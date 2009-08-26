<?php

$template->set_title('Viewing Teacher Page');
    global $dbHandle;
    global $ldap_return;
    global $template;
    $return .= '<h3>Your Current Appointments:</h3>';
    $time = time() - 300;
    $appointmentsGet = 'SELECT * FROM appointments WHERE `teacher` = '.$ldap_return['uidnumber'][0].' ORDER BY `time` ASC';
    $result_res = $dbHandle->query($appointmentsGet);
    $appointments = array();
    while ($result = $result_res->fetch()) $appointments[] = $result;
    
    foreach($appointments as $appointment) {
      $return .= '<br />';
      $return .= date('r', $appointment['time']).' - '.$appointment['student'];
    }
    $template->set_content($return);