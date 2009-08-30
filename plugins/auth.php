<?php

$username = @$_SERVER['PHP_AUTH_USER'];
$password = @$_SERVER['PHP_AUTH_PW'];

define('USER_FORBIDDEN', -1);
define('USER_PARENT', 0);
define('USER_TEACHER', 1);
define('USER_ADMIN', 2);

// This is the key of the first element in $auth
$method = key($auth);
$params = $auth[$method];
require_once($method.'.auth.php');

if ($username && $password && authenticate($username, $password, $params)) {
  if (isset($admins[$username])) {
    $user_access = USER_ADMIN;
  } elseif (isset($teachers[$username])) {
    $user_access = USER_TEACHER;
  } else {
    $user_access = USER_PARENT;
  }
} else {
  $user_access = USER_FORBIDDEN;
}