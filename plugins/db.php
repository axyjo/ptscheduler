<?php

// create a MySQL database file with PDO and return a database handle (Object Oriented)
try{
  $dbHandle = new PDO('sqlite:db.sqlite');
  $dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch( PDOException $exception ){
  die($exception->getMessage());
}

// create page view database table
if (!file_exists('db_installed')) {
  
}
