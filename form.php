<?php

function validate($post) {
  global $ldap_return;
  // Create an empty array to hold the error messages.
  $arrErrors = array();
  //Only validate if the Submit button was clicked.
  if (!empty($post['submit'])) {
    // Each time there's an error, add an error message to the error array
    // using the field name as the key.
    if ($post['teacher']==''||!is_numeric($post['teacher']))
      $arrErrors['teacher'] = 'A valid teacher is required.';
    if ($post['date']=='' || strlen($post['date']) != 10)
      $arrErrors['date'] = 'A valid date is required.';
    if ($post['time']=='' || strlen($post['time']) != 5)
      $arrErrors['time'] = 'A valid time is required.';
  	if (!@is_within_time($post['time'], $post['date']))
	    $arrErrors['time'] = 'An appointment has to be completely within working hours.';
  	if (!@is_within_date($post['date'])) {
  	  $arrErrors['date'] = 'An appointment must fall on the designated date.';
  	}
  	if (@teacher_time_conflicts($post['time'], $post['date'], $post['teacher'])) {
  	  $arrErrors['time'] = 'That timeslot has already been occupied by another appointment.';
	  }
	  if (@student_time_conflicts($post['time'], $post['date'], $ldap_return['cn'][0])) {
  	  $arrErrors['time'] = 'You already have an appointment at that time.';
	  }
  }
  return $arrErrors;
}

function is_within_date($date) {
  global $date_boundaries;
  $dateparts = explode('/', $date);
  if ($date_boundaries['start'][0] <= $dateparts[2] && $date_boundaries['end'][0] >= $dateparts[2]) {
    if ($date_boundaries['start'][1] <= $dateparts[0] && $date_boundaries['end'][1] >= $dateparts[0]) {
      if ($date_boundaries['start'][2] <= $dateparts[1] && $date_boundaries['end'][2] >= $dateparts[1]) {
        return true;
      }
    }
  }
  return false;
}

function is_within_time($time, $date) {
  global $time_boundaries;
  global $time_increments;
  $dateparts = explode('/', $date);
  $timeparts = explode(':', $time);
  $seconds_till_day = mktime(0,0,0,$dateparts[0],$dateparts[1], $dateparts[2]);
  $start_of_appointment = mktime($timeparts[0],$timeparts[1],0,$dateparts[0],$dateparts[1], $dateparts[2]) - $seconds_till_day;
  $end_of_appointment = $start_of_appointment + $time_increments;
  if($start_of_appointment < $time_boundaries['start']) {
  	//starts too early
	return false;
  } elseif($end_of_appointment > $time_boundaries['end']) {
    //ends too late
	return false;
  }
  return true; 
}

function teacher_time_conflicts($time, $date, $teacher) {
  global $time_increments;
  $dateparts = explode('/', $date);
  $timeparts = explode(':', $time);
  global $dbHandle;
  $start = mktime($timeparts[0], $timeparts[1], 0, $dateparts[0], $dateparts[1], $dateparts[2]);
  $end = $start+$time_increments;
  $sql_query = 'SELECT COUNT(*) FROM appointments WHERE `teacher`= '.$teacher.' AND `time` >= '.$start.' AND `time` < '.$end;
  var_dump($sql_query);
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array[0] = 0;
  }

  if($array[0] > 0) return true;
  return false;
}

function student_time_conflicts($time, $date, $student) {
  global $time_increments;
  $dateparts = explode('/', $date);
  $timeparts = explode(':', $time);
  global $dbHandle;
  $start = mktime($timeparts[0], $timeparts[1], 0, $dateparts[0], $dateparts[1], $dateparts[2]);
  $end = $start+$time_increments;
  $sql_query = 'SELECT COUNT(*) FROM appointments WHERE `student`= "'.$student.'" AND `time` >= '.$start.' AND `time` < '.$end;
  //var_dump($sql_query);
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array[0] = 0;
  }

  if($array[0] > 0) return true;
  return false;
}
