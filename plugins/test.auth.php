<?php

function authenticate($user, $pass, $params) {
  if($user && $pass && $user == $pass) {
    return TRUE;
  }
  return FALSE;
}