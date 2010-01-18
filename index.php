<?php

$admins = array();
$teachers = array();
$date_boundaries = array();
$time_boundaries = array();
$auth = array();
// This isn't __DIR__ because it isn't supported in PHP < 5.3.0
$base_path = dirname(__FILE__);

// Check for the template engine before attemptig to load it.
if(!file_exists($base_path.'/plugins/template.php')) {
  echo 'The template engine does not exist. Please redownload this application.';
  exit();
} else {
  require($base_path.'/plugins/template.php');
  $template = new Template();
}

// Check for all of the other required files without reverting to PHP's default
// white page of errors.
$required_files = array($base_path.'/config.php', $base_path.'/plugins/session.php', $base_path.'/plugins/db.php', $base_path.'/plugins/time.php');
$return = '<div class="error"><ul>';
$stop = FALSE;
foreach($required_files as $file) {
  if(!file_exists($file)) {
    $stop = TRUE;
    $return .= '<li>Could not find required file <code>'.$file.'</code> to load.</li>';
  }
}
if($stop) {
  $return .= '</ul></div>';
  $template->set_title('Error: files not found');
  $template->set_content($return);
  $template->render();
  exit();
} else {
  foreach($required_files as $file) {
    require($file);
  }
}

// Enable verbose error reporting if set.
if ($debug) {
  error_reporting(E_ALL);
  if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
  }
} else {
  if (ini_get('display_errors')) {
    ini_set('display_errors', 0);
  }
}

try {
  $check = 'SELECT 1 FROM users';
  $check_res = $dbHandle->query($check);
} catch (PDOException $e) {
  include($base_path.'/install.php');
  exit();
}

if($user_access == USER_FORBIDDEN) {
  // Send a login screen to unauthenticated users.
  if(isset($_GET['login'])) {
    include($base_path.'/plugins/auth.php');
  } else {
    include($base_path.'/views/login.php');
  }
} else {  
  if(isset($_GET['logout'])) {
    include($base_path.'/plugins/auth.php');
  }
  
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
