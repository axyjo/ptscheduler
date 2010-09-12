<?php

/* To use this authentication method, set the following parameters in
 * config.php:
 * $auth = array(
 *   'method' => 'ldap',
 *   'host' => '',
 *   'port' => NULL,
 *   'basedn' => '',
 *   'admin_filter' => '(&(objectclass=person)(|(uid=admin1)(uid=admin2)...))',
 *   'teacher_filter' => '(&(objectclass=person)(apple-keyword=Faculty)...)',
 *   'parent_filter' => '(&(objectclass=person)(uid=fam_*))',
 *   'id_field' => 'uidnumber',
 *   'uid_field' => 'uid',
 *   'dn_field' => 'dn',
 *   'fname_field' => 'givenname',
 *   'lname_field' => 'sn',
 *   'email_field' => 'mail',
 *   'description_field' => 'description',
 * );
 */

class LdapAuth extends Authentication {
  private $ds;

  private function connectToServer() {
    $this->ds = ldap_connect($this->params['host'], $this->params['port']);
    ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  }

  private function searchServer($filter, array $attributes = array()) {
    $r = ldap_search($this->ds, $this->params['basedn'], $filter, $attributes);
    $result = ldap_get_entries($this->ds, $r);
    return $result;
  }

  public function authenticate($user, $pass) {
    connectToServer();
    $result = searchServer('uid='.$user, array('dn'));
    if (is_array($result) {
      if (!is_null($result[0]['dn']) {
        if (@ldap_bind($this->ds, $result[0]['dn'], $pass)) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  public function userList() {
    connectToServer();
    $attributes = array(
      $this->params['id_field'],
      $this->params['uid_field'],
      $this->params['fname_field'],
      $this->params['lname_field'],
      $this->params['email_field'],
      $this->params['description_field'],
    );
    $result = searchServer('uid=*', $attributes);
    $list = array();
    foreach($result as $entity) {
      // Build the final array entry.
      $list[$entity[$this->params['id_field']][0]] = array(
        'uid' => $entity[$this->params['uid_field']][0],
        'fname' => $entity[$this->params['fname_field']][0],
        'lname' => $entity[$this->params['lname_field']][0],
        'email' => $entity[$this->params['email_field']][0],
        'description' => @$entity[$this->params['description_field']][0]
      );
    }
    return $list;
  }

  public function acl($user_id) {
    $filter = '(&(uidnumber='.$user_id.')%%%)';
    $attr = array('uid');

    $result = searchServer(str_replace('%%%', $this->params['admin_filter'], $filter), $attr);
    if (is_array($result)) return USER_ADMIN;

    $result = searchServer(str_replace('%%%', $this->params['teacher_filter'], $filter), $attr);
    if (is_array($result)) return USER_TEACHER;

    $result = searchServer(str_replace('%%%', $this->params['parent_filter'], $filter), $attr);
    if (is_array($result)) return USER_PARENT;

    return USER_FORBIDDEN;
  }
}

$authHandle = new LdapAuth($dbHandle, $auth);
