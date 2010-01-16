<?php

function authenticate($user, $pass, $params) {
  if($user && $pass && $user == $pass) {
    if($user == 'admin' || $user == 'teacher1' || $user == 'teacher2' || $user == 'parent1' || $user == 'parent2') {
      return TRUE;
    }
  }
  return FALSE;
}

function user_list($params) {
  $list = array();
  $list[1] = array('uid' => 'admin', 'fname' => 'Admin', 'lname' => 'Istrator', 'email' => 'admin@localhost');
  $list[2] = array('uid' => 'teacher1', 'fname' => 'Teacher', 'lname' => 'One', 'email' => 't1@localhost');
  $list[3] = array('uid' => 'teacher2', 'fname' => 'Teacher', 'lname' => 'Two', 'email' => 't2@localhost');
  $list[4] = array('uid' => 'parent1', 'fname' => 'Family', 'lname' => 'Doe', 'email' => 'p1@localhost');
  $list[5] = array('uid' => 'parent2', 'fname' => 'Family', 'lname' => 'Roe', 'email' => 'p2@localhost');
  return $list;
}

function get_user_id($username) {
  if($username == 'admin') return 1;
  if($username == 'teacher1') return 2;
  if($username == 'teacher2') return 3;
  if($username == 'parent1') return 4;
  if($username == 'parent2') return 5;
}