<?php

echo 'Please confirm the scheduling of this appointment:<br />';
echo '<form class="app_form" id="appointment" method="post" action="index.php?add">';

echo 'Parent: ';
if ($_SESSION['user_access'] == USER_ADMIN || $_SESSION['user_access'] == USER_TEACHER) {
  $parents = getAllUsersByStatus(USER_PARENT);
  echo '<select id="parent" name="parent">';
  foreach($parents as $parent) {
    echo '<option value="'.$parent['id'].'">'.$parent['lname'].'</option>';
  }
  echo '</select>';
} elseif ($_SESSION['user_access'] == USER_PARENT) {
  $parent = getUser($_SESSION['user_id']);
  echo $parent['fname'].' '.$parent['lname'];
  echo '<input id="parent" type="hidden" name="parent" value="'.$parent['id'].'" />';
}
echo '<br />';

echo 'Teacher: ';
$teacher = getUser($teacher_id);
echo $teacher['fname'].' '.$teacher['lname'];
echo '<input id="teacher" type="hidden" name="teacher" value="'.$teacher['id'].'" />';
echo '<br />';

echo 'Time: '.date($date_format, $time);
echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
echo '<br />';

echo '<input id="hash" type="hidden" name="hash" value="'.md5($secure_hash.$_SESSION['user_id'].$time).'" />';
echo '<input type="submit" id="submit" value="Submit" />';
echo '</form>';
