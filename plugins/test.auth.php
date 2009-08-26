<?php

$admins['admin'] = TRUE;
$teachers['teacher1'] = TRUE;
$teachers['teacher2'] = TRUE;

function authenticate($user, $pass, $params) {
  if($user && $pass && $user == $pass) {
    return TRUE;
  }
  return FALSE;
}

function user_list($params) {
  $list = array();
  $list[] = array('uid' => 'admin', 'fname' => 'Admin', 'lname' => 'Istrator', 'email' => 'admin@localhost');
  $list[] = array('uid' => 'teacher1', 'fname' => 'Teacher', 'lname' => 'One', 'email' => 't1@localhost');
  $list[] = array('uid' => 'teacher2', 'fname' => 'Teacher', 'lname' => 'Two', 'email' => 't2@localhost');
  $list[] = array('uid' => 'parent1', 'fname' => 'John', 'lname' => 'Doe', 'email' => 'p1@localhost');
  $list[] = array('uid' => 'parent2', 'fname' => 'Richard', 'lname' => 'Roe', 'email' => 'p2@localhost');
  return $list;
}