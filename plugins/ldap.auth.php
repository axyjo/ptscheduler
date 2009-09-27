<?php

$admins['AkshayJoshi'] = TRUE;
$teachers['CarolynClark'] = TRUE;
$teachers['LaneGraciano'] = TRUE;
$teachers['PaulSelf'] = TRUE;
$teachers['MarkSommers'] = TRUE;
$teachers['JocelynWiley'] = TRUE;
$teachers['Robert-Allan'] = TRUE;
$teachers['Laura-Berntson'] = TRUE;
$teachers['OckiFernandes'] = TRUE;
$teachers['DanielNicolson'] = TRUE;
$teachers['JoannaRoberts'] = TRUE;
$teachers['SamWerlinich'] = TRUE;
$teachers['DrewDavis'] = TRUE;
$teachers['SharonKerr'] = TRUE;
$teachers['AmitKhanna'] = TRUE;
$teachers['ShawnKrause'] = TRUE;
$teachers['LindseyShoemaker'] = TRUE;
$teachers['David-Berntson'] = TRUE;
$teachers['Paul-Brocklehurst'] = TRUE;
$teachers['Glenda-Frank'] = TRUE;
$teachers['Deborah-Jones'] = TRUE;
$teachers['HanaBayyari'] = TRUE;
$teachers['RajaBayyari'] = TRUE;
$teachers['HanyaMikati'] = TRUE;
$teachers['AliMirzo'] = TRUE;
$teachers['RimaSarakbi'] = TRUE;
$teachers['LaylaBlock'] = TRUE;
$teachers['Valdir-Chagas'] = TRUE;
$teachers['manalyoussef'] = TRUE;
$teachers['ClaudiaGonzalez'] = TRUE;
$teachers['ValiaJimenez'] = TRUE;
$teachers['mercedesavila'] = TRUE;
$teachers['Loretta-Mazzuchin'] = TRUE;
$teachers['LyndaHalabi'] = TRUE;
$teachers['MarthaJensen'] = TRUE;
$teachers['John-Salminen'] = TRUE;
$teachers['NicolasPavlos'] = TRUE;
$teachers['DonnaAllen'] = TRUE;
$teachers['JasmineBrawn'] = TRUE;
$teachers['HajeHalabi'] = TRUE;
$teachers['Bradley-Newell'] = TRUE;
$teachers['AnneRussell'] = TRUE;
$teachers['Mark-Hopkin'] = TRUE;
$teachers['abhayanivarthi'] = TRUE;
$teachers['JesseRemington'] = TRUE;

function authenticate($user, $pass, $params) {
  $ds = ldap_connect($params['host'], $params['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  if (ldap_bind($ds, 'uid='.$user.',cn=users,'.$params['basedn'], $pass)) {
    return TRUE;
  } else {
    return FALSE;
  }
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
  if (isset($e['uid'][0]) && isset($e['givenname'][0]) && isset($e['sn'][0])) return TRUE;
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
