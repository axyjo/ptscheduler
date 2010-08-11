<?php
require_once('PHPUnit/Framework.php');
require_once('../plugins/auth.php');

abstract class AuthenticationTestCase extends PHPUnit_Framework_TestCase {
  protected $auth;
  protected $params;

  abstract public function testAdminAuth();
  abstract public function testTeacherAuth();
  abstract public function testParentAuth();
  abstract public function testFailAuth();
}

