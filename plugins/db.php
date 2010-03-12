<?php

if(isset($db_url)) {
  // Connect to a database with PDO and return a database handle.
  try{
    $dbHandle = new PDO($db_url);
    $dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $exception){
    if(method_exists($template, 'throwException')) {
      $template->throwException($exception);
      exit();
    } else {
      echo $exception->getMessage();
    }
  }
}

function getAllAdmins() {
  global $admins;
  global $dbHandle;
  if(!isset($admins) || !is_array($admins)) {
    $admins = array();
    $sql = 'SELECT * FROM users WHERE status = '.USER_ADMIN.' ORDER BY `lname` ASC ';
    $result_res = $dbHandle->query($sql);
    while ($result = $result_res->fetch()) {
      $admins[$result['id']] = $result;
    }
  }
}

function getAllTeachers() {
  global $teachers;
  global $dbHandle;
  if(!isset($teachers) || !is_array($teachers)) {
    $teachers = array();
    $sql = 'SELECT * FROM users WHERE status = '.USER_TEACHER.' ORDER BY `lname` ASC ';
    $result_res = $dbHandle->query($sql);
    while ($result = $result_res->fetch()) {
      $teachers[$result['id']] = $result;
    }
  }
}

function getAllUsers() {
  getAllParents();
  getAllTeachers();
  global $parents;
  global $teachers;
  return $teachers + $parents;
}

function getAllParents() {
  global $parents;
  global $dbHandle;
  if(!isset($parents) || !is_array($parents)) {
    $parents = array();
    $sql = 'SELECT * FROM users WHERE status='.USER_PARENT.' ORDER BY uid ASC';
    $result_res = $dbHandle->query($sql);
    while($row = $result_res->fetch()) {
      if($row['desc'] == '') $row['desc'] = $row['uid'];
      $parents[$row['id']] = $row;
    }
  }
}

function getUser($uid) {
  // Check the teachers array first, then parents and finally, admins.
  getAllTeachers();
  global $teachers;
  if(!isset($teachers[$uid])) {
    getAllParents();
    global $parents;
    if(!isset($parents[$uid])) {
      getAllAdmins();
      global $admins;
      if(!isset($admins[$uid])) {
        return FALSE;
      } else {
        return $admins[$uid];
      }
    } else {
      return $parents[$uid];
    }
  } else {
    return $teachers[$uid];
  }
}
