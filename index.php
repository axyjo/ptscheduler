<?php

define('ROOT', getcwd());
require_once ROOT.'/plugins/bootstrap.php';
bootstrap();

// Check for the template engine before attemptig to load it.
if(!file_exists(ROOT.'/plugins/template.php')) {
  echo 'The template engine does not exist. Please redownload this application.';
  exit();
} else {
  require(ROOT.'/plugins/template.php');
  $template = Template::getInstance();
}

// Check for configuration file before attempting to load it
if(!file_exists(ROOT.'/config.php')) {
  $template->setTitle('Configuration not found');
  $template->setContent('A configuration file was not found. Please copy <code>default.config.php</code> to <code>config.php</code>. After that, please modify <code>config.php</code> so that it has the right settings for your purposes.');
  $template->render();
  exit();
} else {
  require(ROOT.'/config.php');
  // Set site title now since it wasn't available before.
  $template->setSiteName($site_name);
}

// Check for all of the other required files without reverting to PHP's default
// white page of errors.
$required_files = array(ROOT.'/plugins/session.php', ROOT.'/plugins/db.php', ROOT.'/plugins/time.php');
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
  include(ROOT.'/install.php');
  exit;
}
unset($admins, $teachers);

if($user_access == USER_FORBIDDEN) {
  // Send a login screen to unauthenticated users.
  if(isset($_GET['login'])) {
    include(ROOT.'/plugins/auth.php');
  } else {
    include(ROOT.'/views/login.php');
  }
} else {
  if(isset($_GET['logout'])) {
    include(ROOT.'/plugins/auth.php');
  } elseif(isset($_GET['add']) || isset($_GET['delete']) || isset($_GET['form']) ) {
    include(ROOT.'/form.php');
    exit;
  } else {
    //this is the home page
    if ($user_access == USER_ADMIN) {
      if(isset($_GET['list'])) {
        include(ROOT.'/views/list.php');
      } else {
        include(ROOT.'/views/admin.php');
      }
    } elseif ($user_access == USER_TEACHER) {
      include(ROOT.'/views/teacher.php');
    } elseif ($user_access == USER_PARENT) {
      include(ROOT.'/views/parent.php');
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
