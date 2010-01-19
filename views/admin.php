<?php
$template->set_title('Viewing Admin Page');
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
    });
  };
});');

$return = '<br /><strong>Currently logged in as: </strong>'. $_SESSION['username'].'. Click <a href="index.php?logout">here</a> to logout.';

$return .= '<h3>Instructions:</h3>';

$return .= '<ul><li>Please use Firefox or Internet Explorer as your browser (with Javascript enabled). Safari does not work well.</li>
<li>To schedule, please click next to the teachers name. Then, click on the time that you would like to schedule. Finally, click the Submit button.</li>
<li>If you need to delete a previously scheduled appointment, click the appointment again and select the Delete button.</li>

<li>Please email <a href="mailto:jesse-remington@acs.sch.ae">jesse-remington@acs.sch.ae</a> if you have problems.</li> </ul>';

$return .= '<br />';
$times = return_times();
$tabular_times = array();
for($i = 0; $i < count($times); $i++) {
  if (!isset($tabular_times[date('i', $times[$i])])) {
    $tabular_times[date('i', $times[$i])] = array();
  }
  $tabular_times[date('i', $times[$i])][date('H', $times[$i])] = $times[$i];

}

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
  $return .= '<span class="teacher grid_2"><strong>';
  $return .= $teacher['fname'].' '.$teacher['lname'];
  $return .= '</strong></span><br />
    <div class="grid_2 throbber" id="throbber_'.$teacher['id'].'"></div>';
  foreach($tabular_times as $minute => $hours_array) {
    $i = 0;
    foreach($hours_array as $hour => $epoch) {
      if(isset($newappointments[$epoch])) {
        if($newappointments[$epoch][0]['parent'] == -1) {
          //break
          $class = 'yellow';
          $title = 'Break';
        } else {
          if (!is_null($newappointments[$epoch][0]['parent']) && $newappointments[$epoch][0]['parent'] != 0) {//real appointment
            $class = 'red';
            $sql = 'SELECT * FROM users WHERE id='.$newappointments[$epoch][0]['parent'].';';
            $parent_res = $dbHandle->query($sql);
            $parent = $parent_res->fetch();
            $title = 'Appointment with: '.$parent['fname'].' '.$parent['lname'].' ('.$parent['desc'].')';
          } else {
          	$class = 'purple';
          	$title = 'Appointment with NULL parent';
          }
        }
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
  $return .= '</div>';
}

$return .= '<div id="dialog"></div>';
$template->set_content($return);
