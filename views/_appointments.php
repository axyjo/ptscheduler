<?php

$return .= '<h3>';

if ($_SESSION['user_id'] != $user['id']) {
  $return .= $user['fname'] . ' ' . $user['lname'] . '\'s';
} else {
  $return .= 'Your';
}

$return .= ' Current Appointments (<a href="javascript:window.print()">Print</a>):</h3>';

$getQuery = 'SELECT * FROM appointments WHERE `teacher` = "' . $user['id'] . '" OR `parent`= "' . $user['id'] . '" ORDER BY `time` ASC';
$result_res = $dbHandle->query($getQuery);
$appointments = array();
while ($result = $result_res->fetch()) {
  $appointments[] = $result;
  if ($user['status'] == USER_PARENT) {
    $other_party = getUser($result['teacher']);
    // Clear the description for a teacher.
    $other_party['description'] = '';
  } else {
    // This user is a teacher or administrator.
    $other_party = getUser($result['parent']);
  }
  $return .= date($date_format, $result['time']) . ' - <a title="' . $other_party['description'] .'">';
  $return .= $other_party['fname'] . ' ' . $other_party['lname'];
  $return .= '</a> (<a href="mailto:' . $other_party['email'] . '">Email</a>)';
  $return .= '<br />';
}

if (count($appointments) == 0) {
  if ($_SESSION['user_id'] != $user['id']) {
    $return .= 'Sorry, this user does not have any appointments in the future.';
  } else {
    $return .= 'Sorry, you do not have any appointments in the future.';
  }
}
