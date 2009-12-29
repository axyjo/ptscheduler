<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );

$admins = array();
$teachers = array();

$base_path = dirname($_SERVER['SCRIPT_FILENAME']);

$date_boundaries = array();
$date_boundaries['2009-11-19'] = TRUE;

$time_boundaries = array();
$time_boundaries['start'] = (8*60*60);
$time_boundaries['end'] = (16*60*60) + (30*60);
$time_increments = (15*60);

$parent_restrict = 1257638400;
$teacher_restrict = 1257120000;

// see http://www.php.net/manual/en/function.date.php for codes
$date_format = 'D, j M Y H:i';

//gmt+4
$timezone_offset = 4*60*60;

// Only use one authentication method. Comment out the unused ones.
$auth = array();
$auth['ldap'] = array(
  'host' => 'media.acs.sch.ae',
  'port' => NULL,
  'basedn' => 'dc=home,dc=acs,dc=sch,dc=ae',
);
//$auth['test'] = array();

define('USER_FORBIDDEN', -1);
define('USER_PARENT', 0);
define('USER_TEACHER', 1);
define('USER_ADMIN', 2);

$db_url = 'sqlite:/Users/akshay/Sites/ptscheduler/db.sqlite';
//$db_url = 'mysql:host=127.0.0.1;port=3306; dbname=ptc,ptcadmin,ptc2009';
$secure_hash = 'asejtliw3tv8o35n7835 *&@$(&8235';

$debug = TRUE;
if ($debug) {
  error_reporting(E_ALL);
  if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
  }
}
