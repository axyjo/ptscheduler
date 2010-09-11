<?php

define('ROOT', getcwd());
require_once ROOT.'/plugins/bootstrap.php';

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
