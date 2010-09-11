<?php

/* To use this authentication method, set at least the following parameters
 * in config.php:
 * $auth['ldap'] = array(
 *   'host' => '',
 *   'port' => NULL,
 *   'basedn' => '',
 * );
 */

class LdapAuth extends Authentication {

  public function authenticate($user, $pass) {
    $ds = ldap_connect($this->params['host'], $this->params['port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if (@ldap_bind($ds, 'uid='.$user.',cn=users,'.$this->params['basedn'], $pass)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function userList() {
    $ds = ldap_connect($this->params['host'], $this->params['port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r = ldap_search($ds, $this->params['basedn'], 'uid=*');
    $result = ldap_get_entries($ds, $r);
    $list = array();
    foreach($result as $entity) {
      // Check to make sure that the entity is valid before continuing.
      if (checkList($entity)) {
        // Build the final array entry.
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

  public function checkList($e) {
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
}

$authHandle = new LdapAuth($dbHandle, $method);
