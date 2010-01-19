<?php

if(isset($_POST['hash'])) {
  if($user_access == USER_ADMIN 
    || ($user_access == USER_TEACHER && $user_id == $_POST['teacher'])
    || ($user_access == USER_PARENT && $user_id == $_POST['parent'])
  ) {
    //teacher with appointment or admin
    //check hash
    if(substr($_POST['hash'],0,32) == md5($_POST['parent'].$_POST['teacher'].$secure_hash.$_POST['time'])) {
      $exec = 'DELETE FROM appointments WHERE `id` = '. $_POST['appointment'];
      $dbHandle->exec($exec);
      echo 'success';
    } else {
      echo 'Your security hash does not match the given variables.<br>';
      //echo $_POST['parent'].$_POST['teacher'].$secure_hash.$_POST['time'];
    }
  }
} else {
  $teacher_id = $_GET['teacher'];
  $time = $_GET['time'];

  $sql_query = 'SELECT * FROM appointments WHERE `teacher`= '.$teacher_id.' AND `time` = '.$time;
  try {
    $result = $dbHandle->query($sql_query);
    $array = $result->fetch();
  } catch (Exception $e) {
    $array = null;
  }
  
  if(!is_null($array['parent'])) {
    $sql_query = 'SELECT * FROM users WHERE `id`= '.$array['parent'];
    try {
      $result = $dbHandle->query($sql_query);
      $parent = $result->fetch();
    } catch (Exception $e) {
      $array = null;
    }
  } else {
    $parent = array('lname' => 'NULL', 'desc' => 'NULL');
  }
  
  if($user_access == USER_PARENT && $array['parent'] != $user_id) {
    echo '403';
    exit;
  }

  echo '<form class="app_form" id="appointment" method="post" action="index.php?delete">';
  echo 'Please confirm the deletion of this appointment:<br />';
  echo '<div class="errors"></div>';
  echo 'Parent: '.$parent['lname'].' ('.$parent['desc'].')';
   
  echo '<br />';

  echo 'Teacher: ';
  $sql = 'SELECT * FROM users WHERE id=:s LIMIT 1';
  $stmt = $dbHandle->prepare($sql);
  $stmt->bindParam(':s', $array['teacher']);
  $stmt->execute();
  $row = $stmt->fetch();
  echo $row['fname'].' '.$row['lname'];
  echo '<input id="teacher" type="hidden" name="teacher" value="'.$row['id'].'" />';
  if(isset($parent['id'])) {
    echo '<input id="parent" type="hidden" name="parent" value="'.$parent['id'].'" />';
  }
  echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
  echo '<input id="appointment" type="hidden" name="appointment" value="'.$array['id'].'" />';

  echo '<br />';

  echo 'Time: '.date('l j F Y h:i A',$time);
  echo '<br />';

  //checking for 0 parent;
  if($array['parent'] == 0) $array['parent'] = '';

  echo '<input id="hash" type="hidden" name="hash" value="'.md5($array['parent'].$array['teacher'].$secure_hash.$time).'  " />';
  
  echo '<input type="submit" id="submit" value="Delete" />';
  echo '</form>';
}