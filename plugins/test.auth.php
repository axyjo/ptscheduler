<?php

class TestAuth extends Authentication {
  public function authenticate($user, $pass) {
    if($user && $pass && $user == $pass) {
      if($user == 'admin' || $user == 'teacher1' || $user == 'teacher2' || $user == 'parent1' || $user == 'parent2') {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function userList() {
    $list = array();
    $list[1] = array('uid' => 'admin', 'fname' => 'Admin', 'lname' => 'Istrator', 'email' => 'admin@localhost');
    $list[2] = array('uid' => 'teacher1', 'fname' => 'Teacher', 'lname' => 'One', 'email' => 't1@localhost');
    $list[3] = array('uid' => 'teacher2', 'fname' => 'Teacher', 'lname' => 'Two', 'email' => 't2@localhost');
    $list[4] = array('uid' => 'parent1', 'fname' => 'Family', 'lname' => 'Doe', 'email' => 'p1@localhost');
    $list[5] = array('uid' => 'parent2', 'fname' => 'Family', 'lname' => 'Roe', 'email' => 'p2@localhost');
    return $list;
  }
}

$authHandle = new TestAuth($dbHandle, $method);
