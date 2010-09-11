<?php

$teacher_id = $_GET['teacher'];
$time = $_GET['time'];

$sql_query = 'SELECT * FROM appointments WHERE `teacher`= '.$teacher_id.' AND `time` = '.$time;
if($user_access == USER_PARENT) {
  // Restrict what appointments a parent can delete.
  $sql_query .= ' AND `parent` = '.$_SESSION['user_id'];
}

try {
  $result = $dbHandle->query($sql_query);
  $array = $result->fetchAll();
} catch (Exception $e) {
  $array = null;
}

if(count($array) > 0) {
  echo 'Please confirm the deletion of this appointment:<br />';
} else {
  exit;
}

foreach($array as $appointment) {
  if(!is_null($appointment['parent'])) {
    $parent = getUser($appointment['parent']);
  } else {
    $parent = array('lname' => 'NULL', 'desc' => 'NULL');
  }

  if($user_access == USER_PARENT && $appointment['parent'] != $_SESSION['user_id']) {
    // A parent is trying to delete an appointment that's not theirs.
    break;
  }

  echo '<form class="app_form" id="appointment" method="post" action="index.php?delete">';
  echo 'Parent: '.$parent['lname'].' ('.$parent['desc'].')';

  echo '<br />';

  echo 'Teacher: ';
  $teacher = getUser($appointment['teacher']);
  echo $teacher['fname'].' '.$teacher['lname'];
  echo '<input id="teacher" type="hidden" name="teacher" value="'.$teacher['id'].'" />';
  if(isset($parent['id'])) {
    echo '<input id="parent" type="hidden" name="parent" value="'.$parent['id'].'" />';
  }
  echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
  echo '<input id="appointment" type="hidden" name="appointment" value="'.$appointment['id'].'" />';

  echo '<br />';

  echo 'Time: '.date($date_format,$time);
  echo '<br />';

  //checking for 0 parent;
  if($appointment['parent'] == 0) $appointment['parent'] = '';

  echo '<input id="hash" type="hidden" name="hash" value="'.md5($secure_hash.$_SESSION['user_id'].$time).'  " />';

  echo '<input type="submit" id="submit" value="Delete" />';
  echo '</form>';
}
