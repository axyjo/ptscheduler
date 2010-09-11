<?php

// This function defines the behaviour for the custom error handler.
function error_handler($errno, $errstr, $errfile, $errline) {
  global $template;
  $template->setTitle('A PHP error occurred.');
  $template->setContent('<strong>Error:</strong> ' . $errstr . ' in ' . $errfile. ' on line ' . $errline);
  $template->render();
  exit();
}

error_reporting(E_ALL);
$debug_info = array('time' => array(), 'mem' => array());
$debug_info['time']['start'] = microtime(TRUE);

// Check for the template engine before attempting to load it.
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

// Now that we've loaded the configuration, set our custom error handler.
set_error_handler("error_handler");

// Enable verbose error reporting if set.
if ($debug) {
  if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
  }
} else {
  if (ini_get('display_errors')) {
    ini_set('display_errors', 0);
  }
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

if($debug) {
  $debug_info['time']['end'] = microtime(TRUE);
  $debug_info['mem']['peak'] = memory_get_peak_usage();
  $template->setDebugInfo($debug_info);
}

// Check to see if the 'users' table exists. If not, then run the install file.
try {
  $check = 'SELECT 1 FROM users';
  $check_res = $dbHandle->query($check);
} catch (PDOException $e) {
  include(ROOT.'/install.php');
  exit;
}

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
