<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" ); 

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
$self = $_SERVER['PHP_SELF']; //the $self variable equals this file    
$ldap_return = null;

$ldapconfig['host'] = 'home.acs.sch.ae';
$ldapconfig['port'] = NULL;
$ldapconfig['basedn'] = 'dc=home,dc=acs,dc=sch,dc=ae';
$ldapconfig['authrealm'] = 'My Realm';

function authenticate($user, $pass) {
  global $ldap_return;
  global $ldapconfig;
  $ds = ldap_connect($ldapconfig['host'],$ldapconfig['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  
  $r = ldap_search($ds, $ldapconfig['basedn'], 'uid='.$user);    
  if ($r) {
    $result = ldap_get_entries( $ds, $r);
    if ($result[0]) {
      if (@ldap_bind( $ds, $result[0]['dn'], $pass) ) {
        $ldap_return = $result[0];
      	return true;
      }
    }
  }
  return false;
}

