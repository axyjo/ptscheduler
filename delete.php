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
      $teacher = getUser($_POST['teacher']);
      $teacher = $teacher['fname'].' '.$teacher['lname'];
      $parent = getUser($_POST['parent']);
      $parent = $parent['fname'].' '.$parent['lname'];
      if($user_access == USER_ADMIN) {
        $_SESSION['notices'][] = 'The appointment between '.$teacher.' and '.$parent.' on '.date($date_format, $_POST['time']).' has been deleted.';
      } elseif($user_access == USER_TEACHER) {
        $_SESSION['notices'][] = 'Your appointment with '.$parent.' on '.date($date_format, $_POST['time']).' has been deleted.';
      } elseif($user_access == USER_PARENT) {
        $_SESSION['notices'][] = 'Your appointment with '.$teacher.' on '.date($date_format, $_POST['time']).' has been deleted.';
      }
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
    $parent = getUser($array['parent']);
  } else {
    $parent = array('lname' => 'NULL', 'desc' => 'NULL');
  }
  
  if($user_access == USER_PARENT && $array['parent'] != $user_id) {
    echo '403';
    exit;
  }

  echo '<form class="app_form" id="appointment" method="post" action="index.php?delete">';
  echo 'Please confirm the deletion of this appointment:<br />';
  echo 'Parent: '.$parent['lname'].' ('.$parent['desc'].')';
   
  echo '<br />';

  echo 'Teacher: ';
  $teacher = getUser($array['teacher']);
  echo $teacher['fname'].' '.$teacher['lname'];
  echo '<input id="teacher" type="hidden" name="teacher" value="'.$teacher['id'].'" />';
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