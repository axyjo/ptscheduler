<?php

$template->setTitle('Viewing User List');
$users = getAllUsers();
if(!isset($_GET['user'])) {
  // Remove the break user.
  unset($users[-1]);

  $return = 'Here is a list of all users. Click on a user to view the list of appointments currently scheduled.<br /><br />';

  foreach($users as $user) {
    $return .= '<a href="index.php?list&user='.$user['id'].'">';
    $return .= $user['fname'].' '.$user['lname'].' ('.$user['desc'].')';
    $return .= '</a><br />';
  }

  $template->setContent($return);
} else {
  $user = getUser($_GET['user']);
  $return = '<h3>'.$user['fname'].' '.$user['lname'].'\'s Current Appointments:</h3>';
  $time = time() - 300;
  $getQuery = 'SELECT * FROM appointments WHERE `teacher` = "'.$user['id'].'" OR `parent`= "'.$user['id'].'" ORDER BY `time` ASC';
  $result_res = $dbHandle->query($getQuery);
  $appointments = array();
  while($result = $result_res->fetch()) {
    $appointments[] = $result;
    if(isset($parents[$user['id']])) {
      $return .= date($date_format, $result['time']).' - '.$users[$result['teacher']]['fname'].' '.$users[$result['teacher']]['lname'];
    } else {
      // This user is a teacher.
      $return .= date($date_format, $result['time']).' - '.$users[$result['parent']]['fname'].' '.$users[$result['parent']]['lname'];      
    }
    $return .= '<br />';
  }
  if (count($appointments) == 0) $return .= 'Sorry, tihs user does not have any appointments in the future.';
  $_SESSION['notices'][] = $return;
  header('Location: index.php');
  exit();
}
