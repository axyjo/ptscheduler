<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" ); 

$admins = array();
$teachers = array();

$base_path = dirname($_SERVER['SCRIPT_FILENAME']);

$date_boundaries = array();
$date_boundaries['start'] = '2009-03-19';
$date_boundaries['end'] = '2009-03-19';

$time_boundaries = array();
$time_boundaries['start'] = (8*60*60);
$time_boundaries['end'] = (16*60*60) + (30*60);
$time_increments = (10*60);


// Only use one authentication method. Comment out the unused ones.
$auth = array();
$auth['ldap'] = array(
  'host' => 'home.acs.sch.ae',
  'port' => NULL,
  'basedn' => 'dc=home,dc=acs,dc=sch,dc=ae',
);
//$auth['test'] = array();

$db_url = 'sqlite:db.sqlite';

$debug = TRUE;
if ($debug) {
  error_reporting(E_ALL);
  if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
  }
}
