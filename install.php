<?php

function create_user_record($id, $uid, $fname, $lname, $email) {
  global $dbHandle;
  $stmt = $dbHandle->prepare('INSERT INTO users (id, uid, fname, lname, email) VALUES (:id, :uid, :fname, :lname, :email)');
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
  $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
  $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
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

$sqlCreateTable = 'CREATE TABLE users(id INTEGER, uid CHAR(50), fname CHAR(30), lname CHAR(30), email CHAR(200))';
$dbHandle->exec($sqlCreateTable);
$return .= '<li>Created table <code>users</code>.</li>';

$sqlCreateTable = 'CREATE TABLE appointments(id INTEGER PRIMARY KEY AUTOINCREMENT, student INTEGER, teacher INTEGER, time INTEGER)';
$dbHandle->exec($sqlCreateTable);
$return .= '<li>Created table <code>appointments</code>.</li>';

$users = user_list($params);
foreach($users as $id => $user) {
  create_user_record($id, $user['uid'], $user['fname'], $user['lname'], $user['email']);
  $return .= '<li>Created user #'.$id.' - '.$user['fname'].' '.$user['lname'].' ('.$user['uid'].' '.$user['email'].')';
}

//$dbHandle->exec('COMMIT');
$return .= '<li>Committing transaction</li>';

$return .= '</ul>';
$template->set_content($return);
$template->render();
$dbHandle = NULL;