<?php
require_once('PHPUnit/Framework.php');
require_once('../plugins/template.php');

class TemplateTest extends PHPUnit_Framework_TestCase {
  protected $t;

  protected function setUp() {
    $this->t = Template::getInstance();
  }

  private function renderString() {
    ob_start();
    $this->t->render();
    $str = ob_get_contents();
    ob_end_clean();
    return $str;
  }

  public function testCreateAndClone() {
    $methods = get_class_methods('Template');
    $this->assertFalse(in_array('__construct', $methods));
    $this->assertFalse(in_array('__clone', $methods));
  }

  public function testContainsCriticalStrings() {
    $str = $this->renderString();
    $this->assertContains('id="header"', $str, 'Template contains header div.');
    $this->assertContains('id="content"', $str, 'Template contains content div.');
    $this->assertContains('id="footer"', $str, 'Template contains footer div.');
    $this->assertContains('id="dialog"', $str, 'Template contains dialog div.');
    $this->assertContains('Akshay Joshi', $str, 'Template contains author\'s name.');
  }

  public function testSettingContent() {
    $this->t->setContent('The quick brown fox jumped over the lazy dog.');
    $str = $this->renderString();
    $this->assertContains('The quick brown fox jumped over the lazy dog.', $str);

    $test = (int)rand()*10000000;
    $this->t->setContent($test);
    $str = $this->renderString();
    $this->assertContains((string)$test, $str);
  }

  public function testSiteName() {
    $this->t->setSiteName('Test SiteName');
    $str = $this->renderString();
    $this->assertContains(' | Test SiteName</title>', $str);
    $this->assertContains('Test SiteName</a></h2>', $str);
  }

  public function testTitle() {
    $this->t->setTitle('Test PageTitle');
    $str = $this->renderString();
    $this->assertContains('<title>Test PageTitle | ', $str);
    $this->assertContains('<h1>Test PageTitle</h1>', $str);
  }

  public function testThrowingException() {
    try {
      $int = 1 / 0;
    } catch (Exception $e) {
      ob_start();
      $this->t->throwException($e);
      $str = ob_get_contents();
      ob_end_clean();
      $message = $e->getMessage();
    }

    $this->assertContains('<title>An error occured | ', $str);
    $this->assertContains('<h1>An error occured</h1>', $str);
    $this->assertContains($message, $str);
  }

  public function testNotices() {
    $_SESSION['notices'] = array();
    $_SESSION['notices'][] = 'Check 1';
    $_SESSION['notices'][] = 'Check 2';

    $str = $this->renderString();
    $this->assertContains('<div class="notice">', $str);
    $this->assertContains('<li>Check 1</li>', $str);
    $this->assertContains('<li>Check 2</li>', $str);
  }

  public function testErrors() {
    $_SESSION['errors'] = array();
    $_SESSION['errors'][] = 'Check 1';
    $_SESSION['errors'][] = 'Check 2';

    $str = $this->renderString();
    $this->assertContains('<div class="error">', $str);
    $this->assertContains('<li>Check 1</li>', $str);
    $this->assertContains('<li>Check 2</li>', $str);
  }

  public function testNoticesAndErrors() {
    $_SESSION['notices'] = array();
    $_SESSION['notices'][] = 'Check notices';
    $_SESSION['errors'] = array();
    $_SESSION['errors'][] = 'Check errors';

    $str = $this->renderString();
    $this->assertContains('<div class="notice">', $str);
    $this->assertContains('<li>Check notices</li>', $str);
    $this->assertContains('<div class="error">', $str);
    $this->assertContains('<li>Check errors</li>', $str);
    $this->assertTrue(strpos($str, '<div class="notice">') < strpos($str, '<div class="error">'));
  }

  public function testDebug() {
    $debug_time = array('start' => 5, 'end' => 7);
    $debug_mem = array('peak' => 3*1024*1024);
    $debug = array('time' => $debug_time, 'mem' => $debug_mem);

    $this->t->setDebugInfo($debug);
    $str = $this->renderString();

    $this->assertContains('Total request time: 2 s.', $str);
    $this->assertContains('Peak PHP memory usage: 3 MB.', $str);
  }

}
