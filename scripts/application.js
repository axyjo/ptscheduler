function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

$(document).ready(function () {
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
          $("#throbber_"+t_id).hide();
          if (resp=="success") {
            $(".errors").html("<div class=\"green\">Your appointment has been successfully added!</div>");
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
            $("#throbber_"+t_id).hide();
            if (resp=="success") {
              $(".errors").html("<div class=\"green\">Your appointment has been successfully deleted!</div>");
              sleep(1250);
              window.location.reload();
            } else {
              $(".errors").html(resp);
            }
          }, beforeSubmit: function() {
            $("#throbber_"+t_id).show();
          }});
        }
      });
    }
  });
});
