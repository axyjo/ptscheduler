<?php
$template->set_title('Viewing Parent Page');
$template->add_script('
$(".times").click(function () {
  var vars = this.id.split("-");
  var t_id = vars[0];
  var time = vars[1];
  $("#throbber_"+t_id).ajaxStart(function() {
    $(this).show();
  });
  $("#throbber_"+t_id).ajaxComplete(function() {
    $(this).hide();
    $(this).unbind("ajaxStart ajaxComplete");
  });
  
  if($(this).is(".green")) {
    $.get("form.php", {teacher: t_id, time: time}, function(data, textStatus) {
      $("#dialog").html(data);
      $("#dialog").dialog({
        modal: true,
        title: "Adding new appointment",
        width: 450,
        show: "fade",
        hide: "slide",
        close: function(ev, ui) {
          $("#textos").append("<div id=\"dialog\" />");
        }
      });
      $(".app_form").ajaxForm({success: function(resp, stat) {
        $("#throbber_"+t_id).hide()
        if (resp=="success") {
          $(".errors").html("<div class=\"green\">Your appointment has been successfully added!</div>")
          sleep(1250);
          window.location.reload();
        } else {
          $(".errors").html(resp);
        }
      }, beforeSubmit: function() {
        $("#throbber_"+t_id).show();
      }});
    });
  } else {
    $.get("delete.php", {teacher: t_id, time: time}, function(data, textStatus) {
      if(data != "403") {
        $("#dialog").html(data);
        $("#dialog").dialog({
          modal: true,
          title: "Deleting appointment",
          width: 450,
          show: "fade",
          hide: "slide",
          close: function(ev, ui) {
            $("#textos").append("<div id=\"dialog\" />");
          }
        });
        $(".app_form").ajaxForm({success: function(resp, stat) {
          $("#throbber_"+t_id).hide()
          if (resp=="success") {
            $(".errors").html("<div class=\"green\">Your appointment has been successfully deleted!</div>")
            sleep(1250);
            window.location.reload();
          } else {
            $(".errors").html(resp);
          }
        }, beforeSubmit: function() {
          $("#throbber_"+t_id).show();
        }});
      };
    });
  };
});');

$return = '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use Firefox or Internet Explorer as your browser (with Javascript enabled). Safari does not work well.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If <strong>an error occurs</strong> while scheduling your appointment, please refresh the page.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>
<li>If you need to schedule for another student, you will need to completely close your browser and login again. We hope to have a logoff button in the future.</li>

<li>Locations of where teachers will be for conferences be emailed to you. They will also be available on the window of the HS office.</li>

<li>Please email <a href="mailto:jesse-remington@acs.sch.ae">jesse-remington@acs.sch.ae</a> if you have problems.</li> </ul>';

$return .= '<h3>Your Current Appointments (<a href="javascript:window.print()">Print</a>):</h3>';
$time = time() - 300;
$getQuery = 'SELECT * FROM appointments WHERE `parent`= "'.$user_id.'" ORDER BY `time` ASC';
$result_res = $dbHandle->query($getQuery);
$appointments = array();

while ($result = $result_res->fetch()) $appointments[] = $result;
$hadAppointments = false;
foreach($appointments as $appointment) {
  $hadAppointments = true;
  $return .= '<br />';
  $return .= date($date_format, $appointment['time']).' - '.$teachers[$appointment['teacher']]['fname'].' '.$teachers[$appointment['teacher']]['lname'];
}

if ($hadAppointments == false) $return .= 'Sorry, you currently do not have any appointments in the future.<br /><br />';

$times = return_times();
$tabular_times = array();
for($i = 0; $i < count($times); $i++) {
  if (!isset($tabular_times[date('i', $times[$i])])) {
    $tabular_times[date('i', $times[$i])] = array();
  }
  $tabular_times[date('i', $times[$i])][date('H', $times[$i])] = $times[$i];

}

$return .= '<div id="time_grid">';

foreach($teachers as $teacher) {
  $sql = 'SELECT * FROM appointments WHERE teacher='.$teacher['id'];
  $app_res = $dbHandle->query($sql);
  $appointments = array();
  while ($result = $app_res->fetch()) $appointments[] = $result;
  $newappointments = array();
	foreach($appointments as $appointment) {
 		$newappointments[$appointment['time']][] = $appointment;
  }
	$return .= '<div id="'.$teacher['id'].'">';
  $return .= '<span class="teacher grid_6"><strong>';
  $return .= $teacher['fname'].' '.$teacher['lname'];
  $return .= '</strong > - <a id="link_'.$teacher['id'].'">Click here to view available appointments</a></span><br />
    <div class="grid_2 throbber" id="throbber_'.$teacher['id'].'">
    <img src="throbber.gif" />
    </div><div style="display:none;" id="times_'.$teacher['id'].'">';
  $script = '$("#link_'.$teacher['id'].'").click(function() { $("#times_'.$teacher['id'].'").toggle(); });';
  $template->add_script($script);
  foreach($tabular_times as $minute => $hours_array) {
    $i = 0;
    foreach($hours_array as $hour => $epoch) {
      if(isset($newappointments[$epoch])) {
          $class = 'red';
          $title = 'Unavailable';
      } else {
        //free
        $class = 'green';
        $title = 'Available';
      }

      $time = $hour.$minute;

      $return .= '<span title="'.$title.'" class="'.$class.' times grid_1 push_2" id="'.$teacher['id'].'-'.$epoch.'">'.$time.'</span>';
    }
    $return .= '<br />';
  }
  $return .= '</div></div>';
}

$return .= '</div>';

$return .= '<div id="dialog"></div>';
$template->set_content($return);