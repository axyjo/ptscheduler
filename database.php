<?php

function process_teacher_array($array) {
  if (strpos($array['homedirectory'][0], "/Network/Servers/home.acs.sch.ae/Volumes/ACSBackup/Faculty") === FALSE) {
    return false;
  } elseif ($array['sn'][0] == '' || $array['givenname'][0] == '') {
    list($array['givenname'][0], $array['sn'][0]) = explode(' ', $array['cn'][0]);
  }
  return true;
}

// create a MySQL database file with PDO and return a database handle (Object Oriented)
try{
  $dbHandle = new PDO('mysql:host=localhost;dbname=test', 'testuser', 'testpass');
  $dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch( PDOException $exception ){
  die($exception->getMessage());
}

// create page view database table
if (!file_exists('db_installed')) {
  $dbHandle->exec('DROP TABLE IF EXISTS teachers');
  $dbHandle->exec('DROP TABLE IF EXISTS appointments');

  
  $sqlCreateTable = 'CREATE TABLE teachers(id INTEGER, fname CHAR(30), lname CHAR(30))';
  $dbHandle->exec($sqlCreateTable);
  
  global $ldapconfig;
  $ds = ldap_connect($ldapconfig['host'],$ldapconfig['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

  $r = ldap_search($ds, $ldapconfig['basedn'], 'uid=*', array('givenname', 'sn', 'cn', 'homedirectory', 'uidnumber'));
  $results = ldap_get_entries($ds, $r);
  $results = array_filter($results, 'process_teacher_array');
  foreach($results as $result) {
    $stmt = $dbHandle->prepare('INSERT INTO teachers (id, fname, lname) VALUES (:id, :fname, :lname)');
    $stmt->bindParam(':id', $result['uidnumber'][0], PDO::PARAM_INT);
    $stmt->bindParam(':fname', $result['givenname'][0], PDO::PARAM_STR);
    $stmt->bindParam(':lname', $result['sn'][0], PDO::PARAM_STR);
    $stmt->execute();
  }

  $sqlCreateTable = 'CREATE TABLE appointments(id INTEGER PRIMARY KEY AUTOINCREMENT, student CHAR(50), teacher INTEGER, time INTEGER)';
  $dbHandle->exec($sqlCreateTable);
  
  touch('db_installed');
}
