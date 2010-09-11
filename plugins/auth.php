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

    if ($username && $password && $authHandle->authenticate($username, $password)) {
      $_SESSION['auth'] = md5($username.$secure_hash);
      $_SESSION['username'] = $username;
      $id = $authHandle->getUserId($username);
      $user = getUser($id);
      if(!$user) {
        // This should never happen, but check just in case.
        $user_access = USER_FORBIDDEN;
      } else {
        $user_access = $user['status'];
        // Deny any pre-restriction logins.
        if (($user_access == USER_TEACHER && $teacher_restrict > time()) || ($user_access == USER_PARENT && $parent_restrict > time())) {
          $_SESSION['errors'][] = 'You are currently not allowed to enter the website.';
          $user_access = USER_FORBIDDEN;
        }
      }
    } else {
      $user_access = USER_FORBIDDEN;
      $_SESSION['errors'][] = 'Invalid username or password.';
    }
    if($user_access == USER_FORBIDDEN) {
      unset($_SESSION['auth']);
      unset($_SESSION['username']);
    }

    if($user_access != USER_FORBIDDEN) {
      $_SESSION['user_access'] = $user_access;
      $_SESSION['user_id'] = $authHandle->getUserId($username);
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
  abstract public function acl($user_id);

  public function __construct($dbHandle, $params) {
    $this->dbHandle = $dbHandle;
    $this->params = $params;
  }

  function getUserId($username) {
    $sql = 'SELECT id FROM users WHERE uid = "'.strtolower($username).'"';
    $res = $this->dbHandle->query($sql);
    $id = $res->fetchColumn();
    return (int)$id;
  }
}
