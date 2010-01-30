<?php

$template->setTitle('Viewing Parent List');
if(!isset($_GET['parent'])) {
  getAllParents();
  // Remove the break user.
  unset($parents[-1]);

  $return = 'Here is a list of all parents. Click on a parent to view the list of appointments currently scheduled.<br /><br />';

  foreach($parents as $parent) {
    $return .= '<a href="index.php?list&parent='.$parent['id'].'">';
    $return .= $parent['fname'].' '.$parent['lname'].' ('.$parent['desc'].')';
    $return .= '</a><br />';
  }

  $template->setContent($return);
} else {
  $parent = getUser($_GET['parent']);
  $return = '<h3>'.$parent['uid'].'\'s Current Appointments:</h3>';
  $time = time() - 300;
  $getQuery = 'SELECT * FROM appointments WHERE `parent`= "'.$parent['id'].'" ORDER BY `time` ASC';
  $result_res = $dbHandle->query($getQuery);
  $appointments = array();
  while($result = $result_res->fetch()) {
    $appointments[] = $result;
    $return .= date($date_format, $result['time']).' - '.$teachers[$result['teacher']]['fname'].' '.$teachers[$result['teacher']]['lname'];
    $return .= '<br />';
  }
  if (count($appointments) == 0) $return .= 'Sorry, tihs parent does not have any appointments in the future.';
  $_SESSION['notices'][] = $return;
  header('Location: index.php');
  exit();
}
