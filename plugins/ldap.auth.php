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
    $list[$entity['uidnumber'][0]] = array(
      'uid' => $entity['uid'][0],
      'fname' => $entity['givenname'][0],
      'lname' => $entity['sn'][0],
      'email' => $entity['mail'][0]);
	}
  return $list;
}