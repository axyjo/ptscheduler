<?php

$template->setTitle('Viewing User List');
$users = getAllUsersByStatus(USER_TEACHER) + getAllUsersByStatus(USER_PARENT);
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
  // Because the partial appends to the $return array so that it's compatible
  // with other views, we must set the $return variable.
  $return = '';
  $user = getUser($_GET['user']);
  include(ROOT.'/views/_appointments.php');
  $_SESSION['notices'][] = $return;
  header('Location: index.php');
  exit();
}
