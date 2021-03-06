<?php

function create_user_record($id, $uid, $fname, $lname, $email, $status, $description = null) {
  global $dbHandle;
  $stmt = $dbHandle->prepare('INSERT INTO users (id, uid, fname, lname, email, status, description) VALUES (:id, :uid, :fname, :lname, :email, :status, :description)');
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
  $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
  $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':status', $status, PDO::PARAM_INT);
  $stmt->bindParam(':description', $description, PDO::PARAM_STR);
  $stmt->execute();
  $stmt->closeCursor();
}

require_once(ROOT.'/plugins/auth.php');
require_once(ROOT.'/plugins/'.$auth['method'].'.auth.php');
$return = '<ul>';

try {
  $dbHandle->exec('DROP TABLE IF EXISTS users');
  $return .= '<li>Deleted table <code>users</code>.</li>';

  $res = $dbHandle->query('SELECT name FROM sqlite_master WHERE type="table" AND name="appointments"');
  if ($res->fetch()) {
    $new_name = 'appointments_'.time();
    $dbHandle->exec('ALTER TABLE appointments RENAME TO '.$new_name);
    $return .= '<li>Renamed existing table <code>appointments</code> to <code>'.$new_name.'</code>.</li>';
  }

  $sqlCreateTable = 'CREATE TABLE users(id INTEGER, uid CHAR(50), fname CHAR(30), lname CHAR(30), email CHAR(200), status INTEGER, description TEXT)';
  $dbHandle->exec($sqlCreateTable);
  $return .= '<li>Created table <code>users</code>.</li>';

  $sqlCreateTable = 'CREATE TABLE appointments(id INTEGER PRIMARY KEY AUTOINCREMENT, parent INTEGER, teacher INTEGER, time INTEGER)';
  $dbHandle->exec($sqlCreateTable);
  $return .= '<li>Created table <code>appointments</code>.</li>';
} catch (Exception $e) {
  $template->throwException($e);
}

$users = $authHandle->userList();
foreach($users as $id => $user) {
  if(!isset($user['description'])) $user['description'] = '';
  // Lowercase usernames so that there are no case conflicts.
  $user['uid'] = strtolower($user['uid']);
  $status = $authHandle->acl($id);
  create_user_record($id, $user['uid'], $user['fname'], $user['lname'], $user['email'], $status, $user['description']);
  $return .= '<li>Created user #';
  $return .= $id.' - '.$user['fname'].' '.$user['lname'].' ('.$user['uid'].' '.$user['email'].') - '.$user['description'] . $status;
  $return .= '</li>';
}

create_user_record(-1, '_break', 'Scheduled', 'Break', 'a@example.com', USER_PARENT, 'Break User');
$return .= '<li>Created break user</li>';

$return .= '</ul>';
$template->setContent($return);
$template->render();
$dbHandle = NULL;
