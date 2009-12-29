<?php

$admins['akshayjoshi'] = TRUE;
$admins['sandyrobinson'] = TRUE;
$admins['michelleremington'] = TRUE;
$admins['geoffreymorgan'] = TRUE;
$admins['ptscheduler'] = TRUE;
$admins['luisbolanos'] = TRUE;
$admins['susannjeroudi'] = TRUE;
$teachers['carolynclark'] = TRUE;
$teachers['lanegraciano'] = TRUE;
$teachers['paulself'] = TRUE;
$teachers['marksommers'] = TRUE;
$teachers['jocelynwiley'] = TRUE;
$teachers['robert-allan'] = TRUE;
$teachers['laura-berntson'] = TRUE;
$teachers['ockifernandes'] = TRUE;
$teachers['danielnicolson'] = TRUE;
$teachers['joannaroberts'] = TRUE;
$teachers['samwerlinich'] = TRUE;
$teachers['drewdavis'] = TRUE;
$teachers['sharonkerr'] = TRUE;
$teachers['amitkhanna'] = TRUE;
$teachers['shawnkrause'] = TRUE;
$teachers['lindseyshoemaker'] = TRUE;
$teachers['david-berntson'] = TRUE;
$teachers['paul-brocklehurst'] = TRUE;
$teachers['glenda-frank'] = TRUE;
$teachers['deborah-jones'] = TRUE;
$teachers['hanabayyari'] = TRUE;
$teachers['rajabayyari'] = TRUE;
$teachers['hanyamikati'] = TRUE;
$teachers['alimirzo'] = TRUE;
$teachers['rimasarakbi'] = TRUE;
$teachers['laylablock'] = TRUE;
$teachers['valdir-chagas'] = TRUE;
$teachers['manalyoussef'] = TRUE;
$teachers['claudiagonzalez'] = TRUE;
$teachers['valiajimenez'] = TRUE;
$teachers['mercedesavila'] = TRUE;
$teachers['loretta-mazzuchin'] = TRUE;
$teachers['lyndahalabi'] = TRUE;
$teachers['marthajensen'] = TRUE;
$teachers['john-salminen'] = TRUE;
$teachers['nicolaspavlos'] = TRUE;
$teachers['donnaallen'] = TRUE;
$teachers['jasminebrawn'] = TRUE;
$teachers['hajehalabi'] = TRUE;
$teachers['bradley-newell'] = TRUE;
$teachers['annerussell'] = TRUE;
$teachers['mark-hopkin'] = TRUE;
$teachers['abhayanivarthi'] = TRUE;
$teachers['jesseremington'] = TRUE;
$teachers['bismaloan'] = TRUE;
$teachers['suzannedelap'] = TRUE;
$teachers['jack-murphy'] = TRUE;

function authenticate($user, $pass, $params) {
  global $dbHandle;
  $ds = ldap_connect($params['host'], $params['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  if (@ldap_bind($ds, 'uid='.$user.',cn=users,'.$params['basedn'], $pass)) {
    return TRUE;
  } else {
    return FALSE;
  }
}

function get_user_id($username) {
  global $dbHandle;
  $sql = 'SELECT * FROM users WHERE uid = "'.strtolower($username).'"';
  $res = $dbHandle->query($sql);
  $arr = $res->fetch();
  return (int)$arr['id'];
}

function user_list($params) {
  $ds = ldap_connect($params['host'], $params['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	$r = ldap_search($ds, $params['basedn'], 'uid=*');
	$result = ldap_get_entries($ds, $r);
  $list = array();
	foreach($result as $entity) {
	  //validate user data
	  if (check_list($entity)) {
	    //add processors here
	    $entity['description'][0] = change_desc(@$entity['description'][0]);
      //build
      $list[$entity['uidnumber'][0]] = array(
        'uid' => $entity['uid'][0],
        'fname' => $entity['givenname'][0],
        'lname' => $entity['sn'][0],
        'email' => @$entity['mail'][0],
        'desc' => @$entity['description'][0]);
      }
    }
  return $list;
}

function check_list($e) {
  if (isset($e['uid'][0]) && isset($e['givenname'][0]) && isset($e['sn'][0])) {
    if ($e['givenname'][0] == 'Family') {
      if(count(preg_grep("/^[A-Za-z]+1[0-3][a-z]?$/", array($e['uid'][0]))) == 0) {
        return FALSE;
      }
    }
    return TRUE;
  }
  return FALSE;
}

function change_desc($str = null) {
  //check for numeric or nulls or none
  if (is_null($str) || is_numeric($str) || $str == 'none') return null;
  //check for old style descs where a semicolon separator was used for more than one child
  if (strstr($str, ';')) return null;
  //check for old style descs where desc had 'Family Account with'
  if (strstr($str, 'Family Account with')) return null;
  //clean up before applying performance-heavy regex
  $str = str_replace('Family account of ', '', $str);
  $str = str_replace(' in the class of ', '', $str);
  //apply some regex to get only the student name. basically, at this point, get letters and space only.
  $arr = array();
  preg_match('/^[A-Za-z\s]*/', $str, $arr);
  return $arr[0];
}
