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

if (authenticate($username, $password, $params)) {
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

function create_teacher_record($id, $fname, $lname, $email) {
  $dbHandle->exec('DROP TABLE IF EXISTS teachers');
  $dbHandle->exec('DROP TABLE IF EXISTS appointments');

  $sqlCreateTable = 'CREATE TABLE teachers(id INTEGER, fname CHAR(30), lname CHAR(30), email CHAR(200))';
  $dbHandle->exec($sqlCreateTable);

  $stmt = $dbHandle->prepare('INSERT INTO teachers (id, fname, lname, email) VALUES (:id, :fname, :lname, :email)');
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
  $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();
  
  $sqlCreateTable = 'CREATE TABLE appointments(id INTEGER PRIMARY KEY AUTOINCREMENT, student CHAR(50), teacher INTEGER, time INTEGER)';
  $dbHandle->exec($sqlCreateTable);
  
  touch('db_installed');
}