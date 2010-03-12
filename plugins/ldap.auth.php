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

  function authenticate($user, $pass) {
    $ds = ldap_connect($this->params['host'], $this->params['port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if (@ldap_bind($ds, 'uid='.$user.',cn=users,'.$this->params['basedn'], $pass)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }



  function userList() {
    $ds = ldap_connect($this->params['host'], $this->params['port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r = ldap_search($ds, $this->params['basedn'], 'uid=*');
    $result = ldap_get_entries($ds, $r);
    $list = array();
    foreach($result as $entity) {
      // Check to make sure that the entity is valid before continuing.
      if (checkList($entity)) {
        // Process and modify the description according to the function.
        $entity['description'][0] = changeDesc(@$entity['description'][0]);
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

  function checkList($e) {
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

  function changeDesc($str = null) {
    // Check for null values, numeric values or the literal 'none'.
    if (is_null($str) || is_numeric($str) || $str == 'none') return null;
    // Check for the old style descriptions where a semicolon separator was used
    // for more than one child.
    if (strstr($str, ';')) return null;
    // Check for old style descriptions where desc had 'Family Account with'.
    if (strstr($str, 'Family Account with')) return null;
    // Clean up before applying performance-heavy regular expressions.
    $str = str_replace('Family account of ', '', $str);
    $str = str_replace(' in the class of ', '', $str);
    // Apply a regular expression to get only the student name. Basically, at this
    // point, get letters and space only.
    $arr = array();
    preg_match('/^[A-Za-z\s]*/', $str, $arr);
    return $arr[0];
  }
}

$auth = new LdapAuth($dbHandle, $method);
