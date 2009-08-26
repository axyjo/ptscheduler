<?php

function authenticate($user, $pass, $params) {
  $ds = ldap_connect($params['host'], $params['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  if (ldap_bind($ds, 'uid='.$user.',cn=users,'.$params['basedn'], $pass)) {
	$r = ldap_search($ds, $params['basedn'], 'uid='.$user);
	$result = ldap_first_entry($ds, $r);
	var_dump($result);
    return TRUE;
  } else {
    return FALSE;
  }
}