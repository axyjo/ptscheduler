<?php

function create_user_record($id, $uid, $fname, $lname, $email, $status) {
  global $dbHandle;
  $stmt = $dbHandle->prepare('INSERT INTO users (id, uid, fname, lname, email, status) VALUES (:id, :uid, :fname, :lname, :email, :status)');
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
  $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
  $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':status', $status, PDO::PARAM_INT);
  $stmt->execute();
}

require('config.php');
require($base_path.'/plugins/auth.php');
require($base_path.'/plugins/db.php');
require($base_path.'/plugins/template.php');
$template = new Template();
$return = '<ul>';

$dbHandle->exec('BEGIN TRANSACTION');
$return .= '<li>Starting a new transaction.</li>';

$dbHandle->exec('DROP TABLE IF EXISTS users');
$return .= '<li>Deleted table <code>users</code>.</li>';

$res = $dbHandle->query('SELECT name FROM sqlite_master WHERE type="table" AND name="appointments"');
if ($res->fetch()) {
  $dbHandle->exec('ALTER TABLE appointments RENAME TO appointments'.time());
  $return .= '<li>Renamed existing table <code>appointments</code>.</li>';
}

$dbHandle->exec('COMMIT');
$return .= '<li>Committing transaction</li>';

//$dbHandle->exec('BEGIN TRANSACTION');
$return .= '<li>Starting a new transaction.</li>';

$sqlCreateTable = 'CREATE TABLE users(id INTEGER, uid CHAR(50), fname CHAR(30), lname CHAR(30), email CHAR(200), status INTEGER)';
$dbHandle->exec($sqlCreateTable);
$return .= '<li>Created table <code>users</code>.</li>';

$sqlCreateTable = 'CREATE TABLE appointments(id INTEGER PRIMARY KEY AUTOINCREMENT, student INTEGER, teacher INTEGER, time INTEGER)';
$dbHandle->exec($sqlCreateTable);
$return .= '<li>Created table <code>appointments</code>.</li>';

$users = user_list($params);
foreach($users as $id => $user) {
  //deny by default
  $status = USER_FORBIDDEN;
  if ($user['fname'] == 'Family') $status = USER_PARENT;
  if (isset($teachers[$user['uid']])) $status = USER_TEACHER;
  if (isset($admins[$user['uid']])) $status = USER_ADMIN;
  create_user_record($id, $user['uid'], $user['fname'], $user['lname'], $user['email'], $status);
  $return .= '<li>Created user #'.$id.' - '.$user['fname'].' '.$user['lname'].' ('.$user['uid'].' '.$user['email'].')';
}

//$dbHandle->exec('COMMIT');
$return .= '<li>Committing transaction</li>';

create_user_record(-1, '_break', 'Scheduled', 'Break', 'a@example.com', USER_PARENT)
$return .= '<li>Created break user</li>';

$return .= '</ul>';
$template->set_content($return);
$template->render();
$dbHandle = NULL;
