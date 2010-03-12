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
  $template = Template::getInstance();
}

// Check for configuration file before attempting to load it
if(!file_exists($base_path.'/config.php')) {
  $template->setTitle('Configuration not found');
  $template->setContent('A configuration file was not found. Please copy <code>default.config.php</code> to <code>config.php</code>. After that, please modify <code>config.php</code> so that it has the right settings for your purposes.');
  $template->render();
  exit();
} else {
  require($base_path.'/config.php');
  // Set site title now since it wasn't available before.
  $template->setSiteName($site_name);
}

// Check for all of the other required files without reverting to PHP's default
// white page of errors.
$required_files = array($base_path.'/plugins/session.php', $base_path.'/plugins/db.php', $base_path.'/plugins/time.php');
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
  $template->setTitle('Error: files not found');
  $template->setContent($return);
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
  $debug = array('time' => array(), 'mem' => array());
  $debug['time']['start'] = microtime(TRUE);
  $debug['mem']['start'] = memory_get_usage();
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
  exit;
}
unset($admins, $teachers);

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
  } elseif(isset($_GET['add']) || isset($_GET['delete']) || isset($_GET['form']) ) {
    include($base_path.'/form.php');
    exit;
  } else {
    //this is the home page
    if ($user_access == USER_ADMIN) {
      if(isset($_GET['list'])) {
        include($base_path.'/views/list.php');
      } else {
        include($base_path.'/views/admin.php');
      }
    } elseif ($user_access == USER_TEACHER) {
      include($base_path.'/views/teacher.php');
    } elseif ($user_access == USER_PARENT) {
      include($base_path.'/views/parent.php');
    }
  }
}

if($debug) {
  $debug['time']['end'] = microtime(TRUE);
  $debug['mem']['end'] = memory_get_usage();
  $debug['mem']['peak'] = memory_get_peak_usage();
  $template->setDebugInfo($debug);
}

$template->render();
