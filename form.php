<?php

//date format Y-m-d or timestamp
if(isset($_POST['hash'])) {
  //submit the form after checking hash
  //var_dump($_POST);
  //echo 'hash is set ';
  if(isset($_POST['teacher'])) {
    //admin or parent
    //echo 'teacher is set ';
    if(isset($_POST['parent'])) {
      //echo 'parent is set ';
      //admin: $teacher_id.$secure_hash.$time
      if(substr($_POST['hash'],0,32) == md5($_POST['teacher'].$secure_hash.$_POST['time'])) {
      	//echo 'hash matches';
        $errors = validate($_POST);
        if(count($errors) == 0) {
          $stmt = $dbHandle->prepare('INSERT INTO appointments (parent, teacher, time) VALUES (:parent, :teacher, :time)');
          $stmt->bindParam(':parent', $_POST['parent'], PDO::PARAM_INT);
          $stmt->bindParam(':teacher', $_POST['teacher'], PDO::PARAM_INT);
          $stmt->bindParam(':time', $_POST['time'], PDO::PARAM_INT);
          $stmt->execute();
          echo 'success';
          $stmt->closeCursor();
          //header('Location: index.php');
        } else {
          echo '<div class="red"><ul>';
          foreach($errors as $error) {
          	echo '<li>'.$error.'</li>';
          }
          echo '</ul></div>';
        }
      }
    } else {
      //parent: $secure_hash.$time
      $_POST['parent'] = $user_id;
      if(substr($_POST['hash'],0,32) == md5($secure_hash.$_POST['time'])) {
      	//echo 'hash matches';
        $errors = validate($_POST);
        if(count($errors) == 0) {
          $stmt = $dbHandle->prepare('INSERT INTO appointments (parent, teacher, time) VALUES (:parent, :teacher, :time)');
          $stmt->bindParam(':parent', $_POST['parent'], PDO::PARAM_INT);
          $stmt->bindParam(':teacher', $_POST['teacher'], PDO::PARAM_INT);
          $stmt->bindParam(':time', $_POST['time'], PDO::PARAM_INT);
          $stmt->execute();
          echo 'success';
          $stmt->closeCursor();
          //header('Location: index.php');
        } else {
          echo '<div class="red"><ul>';
          foreach($errors as $error) {
          	echo '<li>'.$error.'</li>';
          }
          echo '</ul></div>';
        }
      }
    }
  } else {
    //teacher: $secure_hash.$time
    $_POST['teacher'] = $user_id;
    if(substr($_POST['hash'],0,32) == md5($secure_hash.$_POST['time'])) {
  	  //echo 'hash matches';
  	  $errors = validate($_POST);
      if(count($errors) == 0) {
        $stmt = $dbHandle->prepare('INSERT INTO appointments (parent, teacher, time) VALUES (:parent, :teacher, :time)');
        $stmt->bindParam(':parent', $_POST['parent'], PDO::PARAM_INT);
        $stmt->bindParam(':teacher', $_POST['teacher'], PDO::PARAM_INT);
        $stmt->bindParam(':time', $_POST['time'], PDO::PARAM_INT);
        $stmt->execute();
        echo 'success';
        $stmt->closeCursor();
        //header('Location: index.php');
      } else {
        echo '<div class="red"><ul>';
        foreach($errors as $error) {
          echo '<li>'.$error.'</li>';
        }
        echo '</ul></div>';
      }
    }
  }
} else {
  //this is the form page
  if ($user_access == USER_ADMIN) {
    //admin
    include($base_path.'/views/admin_form.php');
  } elseif ($user_access == USER_TEACHER) {
    //teacher
    include($base_path.'/views/teacher_form.php');
  } elseif ($user_access == USER_PARENT) {
    //parent
    include($base_path.'/views/parent_form.php');
  }
  // no else because we shouldn't talk to strangers.
}


function validate($post) {
  // Create an empty array to hold the error messages.
  $arrErrors = array();
  //Only validate if the Submit button was clicked.

  // Each time there's an error, add an error message to the error array
  // using the field name as the key.
  if ($post['teacher']==''||!is_numeric($post['teacher']))
    $arrErrors['teacher'] = 'A valid teacher is required.';
  if ($post['time']=='')
    $arrErrors['time'] = 'A valid time is required.';
  if (!is_within_time($post['time']))
    $arrErrors['time'] = 'An appointment has to be completely within working hours.';
  if (!is_within_date($post['time'])) {
    $arrErrors['date'] = 'An appointment must fall on the designated date.';
  }
  if (teacher_time_conflicts($post['time'], $post['teacher'])) {
    $arrErrors['time'] = 'This teacher already has another appointment at that time.';
  }
  if (parent_time_conflicts($post['time'], $post['parent'])) {
    $arrErrors['time'] = 'This parent already has another appointment at that time.';
  }

  return $arrErrors;
}

function is_within_date($timestamp = null) {
  global $date_boundaries;
  $date = date('Y-m-d', $timestamp);
  if ($date_boundaries[$date]) {
    return TRUE;
  }
  return FALSE;
}

function is_within_time($timestamp=null) {
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
  return true;
}

function teacher_time_conflicts($start=null, $teacher=null) {
  global $time_increments;
  global $dbHandle;
  $end = $start+$time_increments;
  $sql_query = 'SELECT COUNT(*) FROM appointments WHERE `teacher`= '.$teacher.' AND `time` >= '.$start.' AND `time` < '.$end;
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array[0] = 0;
  }

  if($array[0] > 0) return true;
  return false;
}

function parent_time_conflicts($start=null, $parent=null) {
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

  if($array[0] > 0) return true;
  return false;
}
