<?php

require('config.php');
require($base_path.'/plugins/auth.php');
require($base_path.'/plugins/db.php');
require($base_path.'/plugins/template.php');
require($base_path.'/plugins/time.php');
$template = new Template();

if ($user_access == USER_FORBIDDEN) {
  // Bad or no username/password.
  // Send HTTP 401 error to make the
  // browser prompt the user.
  header("WWW-Authenticate: Basic");
  header("HTTP/1.0 401 Unauthorized");
  // Display message if user cancels dialog
  include($base_path.'/views/forbidden.php');
} else {  
  $sqlGet = 'SELECT * FROM users WHERE status = '.USER_TEACHER.' ORDER BY `lname` ASC ';
  $result_res = $dbHandle->query($sqlGet);
  $tempteachers = array();
  $teachers = array();
  while ($result = $result_res->fetch()) $tempteachers[] = $result;
  foreach ($tempteachers as $teacher) {
    $teachers[$teacher['id']] = $teacher;
  }
  
  //this is the home page
  if ($user_access == USER_ADMIN) {
    //admin
    include($base_path.'/views/admin.php');
  } elseif ($user_access == USER_TEACHER) {
    //teacher
    include($base_path.'/views/teacher.php');
  } elseif ($user_access == USER_PARENT) {
    //parent
    include($base_path.'/views/parent.php');
  } else {
    //forbidden
    include($base_path.'/views/forbidden.php');
  }

}

$template->render();
