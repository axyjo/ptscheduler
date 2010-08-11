<?php
require_once('../plugins/test.auth.php');

class TestAuthTest extends AuthenticationTestCase {
  protected $auth;
  protected $params;

  protected function setUp() {
    $params = array('admin' => array(), 'teacher' => array(), 'parent' => array());
    $params['admin'][] = array('user' => 'admin', 'pass' => 'admin');
    $params['teacher'][] = array('user' => 'teacher1', 'pass' => 'teacher1');
    $params['teacher'][] = array('user' => 'teacher2', 'pass' => 'teacher2');
    $params['parent'][] = array('user' => 'parent1', 'pass' => 'parent1');
    $params['parent'][] = array('user' => 'parent2', 'pass' => 'parent2');
    
    $auth = new TestAuth('No $dbHandle.', $params);
    
  }

  public function testAdminAuth() {
    
  }
  
  public function testTeacherAuth() {}
  public function testParentAuth() {}
  public function testFailAuth() {

  }

}
