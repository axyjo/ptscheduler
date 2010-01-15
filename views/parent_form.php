<?php
  //define the variables
  $teacher_id = $_GET['teacher'];
  $time = $_GET['time'];
  //display the form
  echo '<form class="app_form" id="appointment" method="post" action="'.$_SERVER['PHP_SELF'].'">';

  echo '<div class="errors"></div>';
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

  echo 'Time: '.date('l j F Y h:i A',$time);
  echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
  echo '<br />';

  echo '<input id="hash" type="hidden" name="hash" value="'.md5($secure_hash.$time).'  " />';

  echo '<input type="submit" id="submit" value="Submit" />';
  echo '</form>';