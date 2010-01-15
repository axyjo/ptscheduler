<?php

define('USER_FORBIDDEN', -1);
define('USER_PARENT', 0);
define('USER_TEACHER', 1);
define('USER_ADMIN', 2);

session_start();
$username = @$_SERVER['PHP_AUTH_USER'];
$password = @$_SERVER['PHP_AUTH_PW'];
// This is the key of the first element in $auth
$method = key($auth);
$params = $auth[$method];
require_once($method.'.auth.php');

if ($username && $password
  && authenticate($username, $password, $params)) {
  $_SESSION['auth'] = md5($username.$password);
  if (isset($admins[strtolower($username)])) {
    $user_access = USER_ADMIN;
  } elseif(isset($teachers[strtolower($username)]) && $teacher_restrict < time()) {
    $user_access = USER_TEACHER;
  } else {
    if($parent_restrict < time()) {
      $user_access = USER_PARENT;
    } else {
      $user_access = USER_FORBIDDEN;
    }
  }
} else {
  $user_access = USER_FORBIDDEN;
}

if($user_access != USER_FORBIDDEN) {
  $user_id = get_user_id($username);
}