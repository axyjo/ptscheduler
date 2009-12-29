<?php

class Template {

  public $title = '';
  //var $html = '';

  public $content = '';

  public $scripts = '';

  public function set_title($str) {
    $this->title = $str;
  }

  public function add_paragraph($str) {
    $this->content .= '<p>'.$str.'</p>';
  }

  public function set_content($str) {
    $this->content = $str;
  }

  public function add_script($scr) {
    $this->scripts .= '<script>$(document).ready(function() {'.$scr.'});</script>';
  }

  private function render_header() {
    $return = '<?xml version="1.0" encoding="UTF-8"?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
      <title>Meeting Scheduler | '.$this->title.'</title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <link rel="stylesheet" type="text/css" media="screen" href="grid.css"  />
      <link rel="stylesheet" type="text/css" media="screen" href="style.css"  />
      <link rel="stylesheet" type="text/css" media="print"  href="print.css" />
      <script type="text/javascript" src="application.js"></script>
      <script type="text/javascript" src="jquery.js"></script>
      <script type="text/javascript" src="jquery-ui.js"></script>
      <script type="text/javascript" src="jquery-form.js"></script>
      '.$this->scripts.'
      </head>
      <body>
      <div class="container_12">
      <div class="alpha omega" id="header">
	      <h2><a href="index.php" title="Home">ACS Conference Scheduler</a></h2>
      </div>
      <div id="textos">
    ';
    $return  = $return.'<h1>'.$this->title.'</h1>';
    return $return;
  }

  private function render_footer() {
    $return = '
      </div>
      <small>&copy; 2009 <a href="http://akshayjoshi.com">Akshay Joshi</a>. Licensed to the American Community School of Abu Dhabi High School.</small>
      </div>
      </body>
      </html>';
    return $return;
  }

  public function render() {
    echo $this->render_header();
    echo $this->content;
    echo $this->render_footer();
  }
}
