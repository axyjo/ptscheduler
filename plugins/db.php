<?php

// create a MySQL database file with PDO and return a database handle (Object Oriented)
try{
  $dbHandle = new PDO($db_url);
  $dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch( PDOException $exception ){
  die($exception->getMessage());
}