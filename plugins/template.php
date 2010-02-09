<?php

class Template {
  var $title, $content, $scripts, $debug, $site_name = NULL;

  function setSiteName($site_name) {
    $this->site_name = $site_name;
  }
  
  public function setTitle($str) {
    $this->title = $str;
  }

  public function addParagraph($str) {
    $this->content .= '<p>'.$str.'</p>';
  }

  public function setContent($str) {
    $this->content = $str;
  }

  public function addScript($scr) {
    $this->scripts .= '<script>$(document).ready(function() {'.$scr.'});</script>';
  }

  public function throwException($e) {
    $this->setTitle('An error occured');
    $this->setContent($e->getMessage());
    $this->render();
    exit();
  }

  private function renderHeader() {
    $return = '<?xml version="1.0" encoding="UTF-8"?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
      <title>'.$this->title.' | '.$this->site_name.'</title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <link rel="stylesheet" type="text/css" media="screen" href="stylesheets/grid.css"  />
      <link rel="stylesheet" type="text/css" media="screen" href="stylesheets/application.css" />
      <link rel="stylesheet" type="text/css" media="print"  href="stylesheets/print.css" />
      <script type="text/javascript" src="scripts/jquery.js"></script>
      <script type="text/javascript" src="scripts/jquery-ui.js"></script>
      <script type="text/javascript" src="scripts/jquery-form.js"></script>
      <script type="text/javascript" src="scripts/application.js"></script>
      '.$this->scripts.'
      </head>
      <body>
      <div class="container_12">
      <div class="alpha omega" id="header">
	      <h2><a href="index.php" title="Home">'.$this->site_name.'</a></h2>
      </div>
      <div id="textos">
    ';
    $return  = $return.'<h1>'.$this->title.'</h1>';
    return $return;
  }

  private function renderFooter() {
    $debug_msg = '';
    if(is_array($this->debug)) {
      $mem_peak = $this->debug['mem']['peak']/1024/1024;
      $time = $this->debug['time']['end'] - $this->debug['time']['start'];
      $debug_msg = 'Peak PHP memory usage: '.$mem_peak.' MB. Total request time: '.$time.' s.';
    }
    $return = '
      <div id="dialog"></div>
      </div>
      <small>&copy; 2009-2010 <a href="http://akshayjoshi.com">Akshay Joshi</a>. Licensed under the <a href="http://creativecommons.org/licenses/MIT/">MIT License</a>.'.$debug_msg.'</small>
      </div>
      </body>
      </html>';
    return $return;
  }
  
  public function setDebugInfo($debug) {
    $this->debug = $debug;
  }
  
  private function processMessages() {
    $return = '';
    if(isset($_SESSION['notices']) && is_array($_SESSION['notices']) && count($_SESSION['notices']) != 0) {
      $return .= '<div class="notice"><ul>';
      foreach($_SESSION['notices'] as $key => $notice) {
        $return .= '<li>'.$notice.'</li>';
      }
      unset($_SESSION['notices']);
      $return .= '</ul></div>';
    }
    if(isset($_SESSION['errors']) && is_array($_SESSION['errors']) && count($_SESSION['errors']) != 0) {
      $return .= '<div class="error"><ul>';
      foreach($_SESSION['errors'] as $key => $error) {
        $return .= '<li>'.$error.'</li>';
      }
      unset($_SESSION['errors']);
      $return .= '</ul></div>';
    }    
    return $return;
  }

  public function render() {
    echo $this->renderHeader();
    echo $this->processMessages();
    echo $this->content;
    echo $this->renderFooter();
  }
}
