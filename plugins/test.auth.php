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
    $list[1] = array('uid' => 'admin', 'fname' => 'Admin', 'lname' => 'Istrator', 'email' => 'admin@localhost', 'description' => 'admin user');
    $list[2] = array('uid' => 'teacher1', 'fname' => 'Teacher', 'lname' => 'One', 'email' => 't1@localhost', 'description' => 'teacher1 user');
    $list[3] = array('uid' => 'teacher2', 'fname' => 'Teacher', 'lname' => 'Two', 'email' => 't2@localhost', 'description' => 'teacher2 user');
    $list[4] = array('uid' => 'parent1', 'fname' => 'John', 'lname' => 'Doe', 'email' => 'p1@localhost', 'description' => 'parent1 user');
    $list[5] = array('uid' => 'parent2', 'fname' => 'Richard', 'lname' => 'Roe', 'email' => 'p2@localhost', 'description' => 'parent2 user');
    return $list;
  }

  public function acl($user_id) {
    if ($user_id == 1) return USER_ADMIN;
    if ($user_id == 2 || $user_id == 3) return USER_TEACHER;
    if ($user_id == 4 || $user_id == 5) return USER_PARENT;
    return USER_FORBIDDEN;
  }
}

$authHandle = new TestAuth($dbHandle, $auth);
