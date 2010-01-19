<?php

if(isset($_GET['login'])) {
  $username = @$_POST['user'];
  $password = @$_POST['pass'];
  // This is the key of the first element in $auth
  $method = key($auth);
  $params = $auth[$method];
  require_once($method.'.auth.php');

  if ($username && $password && authenticate($username, $password, $params)) {
    $_SESSION['auth'] = md5($username.$secure_hash);
    $_SESSION['username'] = $username;
    getAllAdmins();
    getAllTeachers();
    if (isset($admins[getUserId($username)])) {
      $_SESSION['user_access'] = USER_ADMIN;
    } elseif(isset($teachers[getUserId($username)]) && $teacher_restrict < time()) {
      $_SESSION['user_access'] = USER_TEACHER;
    } else {
      if($parent_restrict < time()) {
        $_SESSION['user_access'] = USER_PARENT;
      } else {
        $_SESSION['login_errors'] = array('You are currently not allowed to enter the website.');
        $_SESSION['user_access'] = USER_FORBIDDEN;
      }
    }
  } else {
    $_SESSION['user_access'] = USER_FORBIDDEN;
    $_SESSION['login_errors'] = array('Invalid username or password.'); 
  }
  if($_SESSION['user_access'] == USER_FORBIDDEN) {
    unset($_SESSION['auth']);
    unset($_SESSION['username']);
  }
  header('Location: index.php');
  
  if($_SESSION['user_access'] != USER_FORBIDDEN) {
    $_SESSION['user_id'] = getUserId($username);
  }
} elseif(isset($_GET['logout'])) {
  session_destroy();
  session_start();
  $_SESSION['login_errors'] = array('You have successfully logged out.');
  header('Location: index.php');
}
