<?php

require('config.php');
require($base_path.'/plugins/auth.php');
require($base_path.'/plugins/db.php');
require($base_path.'/plugins/template.php');
$template = new Template();

if ($user_access = USER_FORBIDDEN) {
  // Bad or no username/password.
  // Send HTTP 401 error to make the
  // browser prompt the user.
  header("WWW-Authenticate: " .
        "Basic realm=\"Protected Page: " .
        "Enter your username and password " .
        "for access.\"");
  header("HTTP/1.0 401 Unauthorized");
  // Display message if user cancels dialog
  
 $template->set_title('Authorization Failed');
 $template->add_paragraph('Without a valid username and password,
    access to this page cannot be granted.
    Please click "reload" and enter a
    username and password when prompted.
 ');

} else {  
  require_once('database.php');
  $date_boundaries['start'] = explode('-',$date_boundaries['start']);
  $date_boundaries['end'] = explode('-',$date_boundaries['end']);

  // Create an empty array to hold the error messages.
  $arrErrors = array();
  $strMsg = '';
  //Only validate if the Submit button was clicked.
  if (!empty($_POST['submit'])) {
    require_once('form.php');
	$arrErrors = validate($_POST);
    if (!empty($arrErrors) == 0) {
      $timeparts = explode(':', $_POST['time']);
      $dateparts = explode('/', $_POST['date']);
      $timestamp = mktime($timeparts[0], $timeparts[1], 0, $dateparts[0], $dateparts[1], $dateparts[2]);
      $sqlInsert = 'INSERT INTO appointments(student, teacher, time) VALUES (:student, :teacher, :timestamp)';
      $stmt = $dbHandle->prepare($sqlInsert);
      $stmt->bindParam(':student', $ldap_return['cn'][0], PDO::PARAM_STR);
      $stmt->bindParam(':teacher', $_POST['teacher'], PDO::PARAM_INT);
      $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
      $stmt->execute();
      $strMsg = '
				<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 
.7em;"> 
					<span class="ui-icon ui-icon-info" style="float: left; margin-right: 
.3em;"></span>
					<strong>Congratulations!</strong> Successfully added a new appointment!
				</div>
			';

    } else {
      // The error array had something in it. There was an error.
      // Start adding error text to an error string.
      $strMsg = '
				  <div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all"> 
					  <span style="float: left; margin-right: 0.3em;" class="ui-icon 
ui-icon-alert"> </span>
					  <strong>Please correct the following errors:</strong><ul>';
      // Get each error and add it to the error string
      // as a list item.
      foreach ($arrErrors as $error) {
        $strMsg .= "<li>$error</li>";
      }
    $strMsg .= '</ul>
				
			  </div>';
    }
  }
  function render_student_home() {
    global $ldap_return;
    global $template;
    global $dbHandle;
    global $teachers;
    global $strMsg;
  	global $time_boundaries;
	  global $time_increments;
	  global $date_boundaries;
    $return = '<h3>Your Current Appointments:</h3>';
    $time = time() - 300;
    $getQuery = 'SELECT * FROM appointments WHERE `student`= "'.$ldap_return['cn'][0].'" ORDER BY `time` ASC';
    //var_dump($ldap_return);
    //$getQuery = 'SELECT * FROM appointments WHERE `student`="'.$ldap_return['cn'][0].'"';
    $result_res = $dbHandle->query($getQuery);
    $appointments = array();
    
    while ($result = $result_res->fetch()) $appointments[] = $result;
    $hadAppointments = false;
    foreach($appointments as $appointment) {
      $hadAppointments = true;
      $return .= '<br />';			

      $return .= date('r', $appointment['time']).' - '.$teachers[$appointment['teacher']]['fname'].' '.$teachers[$appointment['teacher']]['lname'];
    }
    
    if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';
    $return .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
    if(!empty($strMsg)) $return .= $strMsg;      
    $return .= 'Create a new appointment with ';
    $return .= '<select name="teacher">';
    foreach ($teachers as $teacher) {
      $return .= '<option value="'.$teacher['id'].'">';
      $return .= $teacher['fname'].' '.$teacher['lname'];
    }
    $return .= '</select>';
    $return .= '<br />';
    //update the following with js date picker thing.
    $return .= ' on <input type="textbox" id="date" name="date" />';
    $template->add_script('$(function(){$(\'#date\').datepicker({
      minDate: new Date('.$date_boundaries['start']['0'].','.$date_boundaries['start']['1'].'-1,'.$date_boundaries['start']['2'].'),
      maxDate: new Date('.$date_boundaries['end']['0'].','.$date_boundaries['end']['1'].'-1,'.$date_boundaries['end']['2'].'),
      mandatory: true,
      });});');
    $return .= '<br />';
    //update the following with js time picker thing.
    $return .= ' at <input type="textbox" id="time" name="time" /><div id="slider"></div>';
    $js_func = '
    function slideValue() {
      var val = $("#slider").slider("value");
      var end = '.$time_boundaries['end'].';
      var start = '.$time_boundaries['start'].';
      var increment = '.$time_increments.';

      var final = start+(increment*val);
      var minutes = (final/60) % 60 + "";
      var hours = ((final/60)-minutes)/60 + "";
      if(minutes.length == 1) minutes = "0"+minutes;
      if(hours.length == 1) hours = "0"+hours;

      var finalStr = hours + ":" + minutes;
      $("#time").val(finalStr);
    }
    
    ';
    $template->add_script($js_func);
    //The minus 1 in floor exists because the time boundary is the absolute end time, and not the time the last appointment can start at.
    $template->add_script('$(function(){$(\'#slider\').slider({value:0, min:0, max:'.floor((($time_boundaries['end'] - $time_boundaries['start'])/$time_increments)-1).', slide: slideValue, change: slideValue});});');
    $return .= '<input type="submit" name="submit" id="submit" value="Do it!" />';
    $return .= '</form>';
    $template->set_content($return);
  }
  
  function render_admin_home() {
    global $dbHandle;
    global $teachers;
    global $ldap_return;
    global $template;
    $return .= "<h3>All Current Appointments:</h3>";
    $appointmentsGet = 'SELECT * FROM appointments INNER JOIN teachers ON appointments.teacher=teachers.id ORDER BY `teacher`,`time` ASC';
    $result_res = $dbHandle->query($appointmentsGet);
    $appointments = array();
    while ($result = $result_res->fetch()) $appointments[] = $result;
  //  var_dump($appointments);
    $newappointments = array();
    foreach($appointments as $appointment) {
      $time = date('l, F j Y', $appointment['time']);
      $name = $appointment['fname'] . ' ' . $appointment['lname'];
      $newappointments[$time][$name][] = $appointment;
    }
    //var_dump($appointments);
    $hadAppointments = false;
    foreach($newappointments as $date => $dateval) {
      $hadAppointments = true;
      $return .= '<h4>'.$date.'</h4>';
      foreach($dateval as $teacher => $appointment) {
//		var_dump($appointment);
        $return .= '<h5>'.$teacher.'</h5>';
        foreach ($appointment as $thing) {
//        $return .= date('r', $appointment[0]['time']).' - '.$appointment[0]['student'].' with '.$appointment[0]['fname'].' '.$appointment[0]['lname'];
        $return .= date('r', $thing['time']).' - '.$thing['student'].' with '.$thing['fname'].' '.$thing['lname'];
                $return .= '<br />';
        }

      }
    }

    if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';
    $template->set_content($return);
  }
  function render_teacher_home() {
    global $dbHandle;
    global $ldap_return;
    global $template;
    $return .= '<h3>Your Current Appointments:</h3>';
    $time = time() - 300;
    $appointmentsGet = 'SELECT * FROM appointments WHERE `teacher` = '.$ldap_return['uidnumber'][0].' ORDER BY `time` ASC';
    $result_res = $dbHandle->query($appointmentsGet);
    $appointments = array();
    while ($result = $result_res->fetch()) $appointments[] = $result;
    
    foreach($appointments as $appointment) {
      $return .= '<br />';
      $return .= date('r', $appointment['time']).' - '.$appointment['student'];
    }
    $template->set_content($return);
  }

  $sqlGet = 'SELECT * FROM teachers WHERE id != 0 ORDER BY `lname` ASC ';
  $result_res = $dbHandle->query($sqlGet);
  $tempteachers = array();
  $teachers = array();
  while ($result = $result_res->fetch()) $tempteachers[] = $result;
  foreach ($tempteachers as $teacher) {
    $teachers[$teacher['id']] = $teacher;
  }
  
  if(!isset($_GET['page']) || $_GET['page'] == 'index' || $_GET['page'] == 'home') {
    //this is the home page
    if ($admins[$ldap_return['uid'][0]] == true) {
      $template->set_title('Viewing Admin Page');
      render_admin_home();
    } elseif (isset($teachers[$ldap_return['uidnumber'][0]])) {
      //teacher
      $template->set_title('Viewing Teacher Page');
      render_teacher_home();
    } else {
      //student
      $template->set_title('Viewing Student Page');
      render_student_home();
    }
  } elseif($_GET['page'] == 'teachers' && isset($_GET['teacher'])) {
    //view teacher appointments
    $teacher = $dbHandle->quote($_GET['teacher']);
    $time = time() - 300;
    $sqlGet = 'SELECT * FROM appointments WHERE teacher = ' . $teacher . ' AND time >= ' . $time;
    $result_res = $dbHandle->query($sqlGet);
    $result = $result_res->fetch();
    var_dump($result);
  }

}

$template->render();

