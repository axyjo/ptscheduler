<?php

if(isset($_GET['login'])) {
  $username = @$_POST['user'];
  $password = @$_POST['pass'];

  foreach($auth as $method) {
    // Load the desired authentication module.
    require_once($method['method'].'.auth.php');

    // Clear session errors since we don't want the user to see repeated errors
    // if there is more than one authentication method enabled.
    $_SESSION['errors'] = array();

    if ($username && $password && $auth->authenticate($username, $password)) {
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
          $_SESSION['errors'][] = 'You are currently not allowed to enter the website.';
          $_SESSION['user_access'] = USER_FORBIDDEN;
        }
      }
    } else {
      $_SESSION['user_access'] = USER_FORBIDDEN;
      $_SESSION['errors'][] = 'Invalid username or password.';
    }
    if($_SESSION['user_access'] == USER_FORBIDDEN) {
      unset($_SESSION['auth']);
      unset($_SESSION['username']);
    }

    if($_SESSION['user_access'] != USER_FORBIDDEN) {
      $_SESSION['user_id'] = getUserId($username);
      $_SESSION['notices'][] = 'Successfully logged in as '.$username;
      break;
    }
  }

  header('Location: index.php');
  exit();

} elseif(isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  session_start();
  $_SESSION['notices'][] = 'You have successfully logged out.';
  header('Location: index.php');
  exit();
}

abstract class Authentication {
  var $dbHandle, $params;

  abstract public function authenticate($user, $pass);
  abstract public function userList();

  public function __construct($dbHandle, $params) {
    $this->dbHandle = $dbHandle;
    $this->params = $params;
  }

  function getUserId($username) {
    $sql = 'SELECT * FROM users WHERE uid = "'.strtolower($username).'"';
    $res = $this->dbHandle->query($sql);
    $arr = $this->dbHandle->fetch();
    return (int)$arr['id'];
  }
}
