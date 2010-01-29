<?php


//define the variables
$teacher_id = $_GET['teacher'];
$time = $_GET['time'];
//display the form
echo '<form class="app_form" id="appointment" method="post" action="index.php?form">';

echo 'Parent: ';

echo '<select id="parent" name="parent">';
getAllParents();
foreach($parents as $parent) {
  echo '<option value="'.$parent['id'].'">'.$parent['lname'].' ('.$parent['desc'].')</option>';
}
echo '</select>';

echo '<br />';

echo 'Time: '.date($date_format, $time);
echo '<input id="time" type="hidden" name="time" value="'.$time.'" />';
echo '<br />';

echo '<input id="hash" type="hidden" name="hash" value="'.md5($secure_hash.$time).'  " />';

echo '<input type="submit" id="submit" value="Submit" />';
echo '</form>';
