<?php

if(isset($_POST['hash'])) {
  $errors = validate($_POST);
  if(count($errors) == 0) {
    addAppointment($_POST['teacher'], $_POST['parent'], $_POST['time']);
    addNotice($_POST['teacher'], $_POST['parent'], $_POST['time']);
    echo 'success';
  } else {
    echo '<div class="error"><ul>';
    foreach($errors as $error) {
      echo '<li>'.$error.'</li>';
    }
    echo '</ul></div>';
  }
} else {
  $teacher_id = $_GET['teacher'];
  $time = $_GET['time'];
  $end = $time+$time_increments;
  $sql_query = 'SELECT COUNT(*), `parent` FROM appointments WHERE `teacher`= '.$teacher_id.' AND `time` >= '.$time.' AND `time` < '.$end;

  try {
    $result = $dbHandle->query($sql_query);
    $arr = $result->fetch();
  } catch (Exception $e) {
    $arr[0] = 0;
    $arr[1] = -1;
  }
  $count = $arr[0];
  $parent = $arr[1];

  if($count < $simultaneous_appointments && $parent != -1) {
    echo 'Please confirm the scheduling of this appointment:<br />';
    echo '<form class="app_form" id="appointment" method="post" action="index.php?form">';
    
    echo 'Parent: ';
    echo '<select id="parent" name="parent">';
    if ($user_access == USER_ADMIN || $user_access == USER_TEACHER) {
      getAllParents();  
    } elseif ($user_access == USER_PARENT) {
      $parents = array($user_id => getUser($user_id));
    }
    foreach($parents as $parent) {
      echo '<option value="'.$parent['id'].'">'.$parent['lname'].' ('.$parent['desc'].')</option>';
    }
    echo '</select>';
    echo '<br />';
    
    echo 'Teacher: ';
    $sql = 'SELECT * FROM users WHERE id=:s LIMIT 1';
    $stmt = $dbHandle->prepare($sql);
    $stmt->bindParam(':s', $teacher_id);
    $stmt->execute();
    $row = $stmt->fetch();
    echo $row['fname'].' '.$row['lname'];
    echo '<input id="teacher" type="hidden" name="teacher" value="'.$row['id'].'" />';
    echo '<br />';
    
    echo 'Time: '.date($date_format, $time);
    echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
    echo '<br />';
    
    echo '<input id="hash" type="hidden" name="hash" value="'.md5($secure_hash.$user_id.$time).'" />';
    echo '<input type="submit" id="submit" value="Submit" />';
    echo '</form>';
  }

  if($count > 0) {
    include('delete.php');
  }
}

function addAppointment($tid, $pid, $time) {
  global $dbHandle;
  $stmt = $dbHandle->prepare('INSERT INTO appointments (parent, teacher, time) VALUES (:parent, :teacher, :time)');
  $stmt->bindParam(':parent', $pid, PDO::PARAM_INT);
  $stmt->bindParam(':teacher', $tid, PDO::PARAM_INT);
  $stmt->bindParam(':time', $time, PDO::PARAM_INT);
  $stmt->execute();
  $stmt->closeCursor();
}

function addNotice($tid, $pid, $time) {
  global $date_format, $user_access;
  $teacher = getUser($tid);
  $teacher = $teacher['fname'].' '.$teacher['lname'];
  $parent = getUser($pid);
  $parent = $parent['fname'].' '.$parent['lname'];
  if($user_access == USER_ADMIN) {
    $_SESSION['notices'][] = 'The appointment between '.$teacher.' and '.$parent.' on '.date($date_format, $time).' has been added.';
  } elseif($user_access == USER_TEACHER) {
    $_SESSION['notices'][] = 'Your appointment with '.$parent.' on '.date($date_format, $time).' has been added.';
  } elseif($user_access == USER_PARENT) {
    $_SESSION['notices'][] = 'Your appointment with '.$teacher.' on '.date($date_format, $time).' has been added.';
  }
}

function validate($post) {
  // Create an empty array to hold the error messages.
  $arrErrors = array();

  // Each time there's an error, add an error message to the error array.
  if(!validateHash($post)) {
    $arrErrors[] = 'The security hash does not match the one in our records. Please try again.';
  }
  if($post['teacher']==''||!is_numeric($post['teacher'])) {
    $arrErrors[] = 'A valid teacher is required.';
  }
  if($post['time']=='') {
    $arrErrors[] = 'A valid time is required.';
  }
  if(!isWithinTime($post['time'])) {
    $arrErrors[] = 'An appointment has to be completely within working hours.';
  }
  if(!isWithinDate($post['time'])) {
    $arrErrors[] = 'An appointment must fall on the designated date.';
  }
  if(teacherTimeConflicts($post['time'], $post['teacher'], $post['parent'])) {
    $arrErrors[] = 'This teacher already has another appointment at that time.';
  }
  if(parentTimeConflicts($post['time'], $post['parent'])) {
    $arrErrors[] = 'This parent already has another appointment at that time.';
  }
  if(!validateUser($post)) {
    $arrErrors[] = 'You may not schedule appointments for another user.';
  }

  return $arrErrors;
}

function isWithinDate($timestamp = null) {
  global $date_boundaries;
  $date = date('Y-m-d', $timestamp);
  if ($date_boundaries[$date]) {
    return TRUE;
  }
  return FALSE;
}

function isWithinTime($timestamp=null) {
  global $time_boundaries;
  global $time_increments;
  global $timezone_offset;
  //seconds from midnight
  $start = ($timestamp % 86400)+$timezone_offset;
  $end = $start + $time_increments;

  if($start < $time_boundaries['start']) {
  	//starts too early
	  return FALSE;
  } elseif($end > $time_boundaries['end']) {
    //ends too late
	  return FALSE;
  }
  return TRUE;
}

function teacherTimeConflicts($start=null, $teacher=null, $parent=NULL) {
  global $time_increments;
  global $dbHandle;
  global $simultaneous_appointments;
  $end = $start+$time_increments;
  $sql_query = 'SELECT COUNT(*) FROM appointments WHERE `parent` = -1 AND `teacher`= '.$teacher.' AND `time` >= '.$start.' AND `time` < '.$end;
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array[0] = 0;
  }

  if($array[0] > 0) {
    return TRUE;
  } else {
    $real_sql_query = 'SELECT COUNT(*) FROM appointments WHERE `teacher`= '.$teacher.' AND `time` >= '.$start.' AND `time` < '.$end;
    try {
      $result = $dbHandle->query($real_sql_query);
      $arr = $result->fetch();
    } catch (Exception $e) {
      $arr[0] = 0;
    }

    if($arr[0] >= $simultaneous_appointments) {
      return TRUE;
    } else {
      if($parent == -1 && $arr[0] > 0) {
        return TRUE;
      } else {
        return FALSE;
      }
    }
  }
}

function parentTimeConflicts($start=null, $parent=null) {
  global $time_increments;
  global $dbHandle;
  if ($parent == -1) return FALSE;
  $end = $start+$time_increments;
  $sql_query = 'SELECT COUNT(*) FROM appointments WHERE `parent`= "'.$parent.'" AND `time` >= '.$start.' AND `time` < '.$end;
  //var_dump($sql_query);
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array[0] = 0;
  }

  if($array[0] > 0) return TRUE;
  return FALSE;
}

function validateUser($post) {
  global $user_access, $user_id;
  if($user_access == USER_PARENT && $post['parent'] != $user_id) {
    return FALSE;
  } elseif($user_access == USER_TEACHER && $post['teacher'] != $user_id) {
    return FALSE;
  } else {
    return TRUE;
  }
}

function validateHash($post) {
  global $secure_hash, $user_id;
  if(substr($post['hash'],0,32) == md5($secure_hash.$user_id.$post['time'])) {
    return TRUE;
  }
  return FALSE;
}
