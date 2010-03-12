<?php

echo 'Please confirm the scheduling of this appointment:<br />';
echo '<form class="app_form" id="appointment" method="post" action="index.php?add">';

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
