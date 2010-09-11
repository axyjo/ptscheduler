<?php

define('USER_FORBIDDEN', -1);
define('USER_PARENT', 0);
define('USER_TEACHER', 1);
define('USER_ADMIN', 2);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );

session_start();

// Check for invalid session entries.
if(isset($_SESSION['auth']) && $_SESSION['auth'] != md5($_SESSION['username'].$secure_hash)) {
  session_unset();
  session_destroy();
  session_start();
  $_SESSION['errors'][] = 'Invalid session. Please login again.';
  header('Location: index.php?login');
}

if(!isset($_SESSION['user_access'])) {
  $_SESSION['user_access'] = USER_FORBIDDEN;
}
$user_access = $_SESSION['user_access'];
