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

function getUser($id) {
  global $dbHandle;
  if(!is_numeric($id)) return FALSE;
  $sql = 'SELECT * FROM users WHERE id = ' . $id . ' ORDER BY `lname` ASC LIMIT 1';
  $result_res = $dbHandle->query($sql);
  return $result_res->fetch();
}

function getAllUsersByStatus($user_access) {
  if(!is_numeric($user_access)) return FALSE;
  global $dbHandle;
  $array = array();
  $sql = 'SELECT * FROM users WHERE status = ' . $user_access . ' ORDER BY `lname` ASC ';
  $result_res = $dbHandle->query($sql);
  while ($result = $result_res->fetch()) {
    $array[$result['id']] = $result;
  }
  return $array;
}
