<?php

error_reporting(E_ALL);
$debug = array('time' => array(), 'mem' => array());
$debug['time']['start'] = microtime(TRUE);

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
  $debug['time']['end'] = microtime(TRUE);
  $debug['mem']['peak'] = memory_get_peak_usage();
  $template->setDebugInfo($debug);
}
