<?php

$template->setTitle('Login');
$content = '<p>Welcome to the parent teacher conference scheduler. If you don\'t know your username or password, please contact <a href="mailto:'.$support_email.'">'.$support_email.'</a>.</p>
<br /><br />';

$content .= '
<form id="user-login-form" method="post" accept-charset="UTF-8" action="index.php?login">
  <div>
      <label for="user">Username: </label>
      <input type="text" value="" size="15" name="user" id="user" maxlength="60" />
      <label for="pass">Password: </label>
      <input type="password" size="15" maxlength="60" name="pass" id="pass" />
    <input type="submit" value="Login" name="submit"/>
  </div>
</form>';

$template->setContent($content);