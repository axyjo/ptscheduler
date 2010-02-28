$(document).ready(function () {
  $('.notice').slideDown('slow');
  $('.error').slideDown('slow');
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

    var location = "form";
    if($(this).is(".green")) {
      var title = "Adding new appointment";
    } else {
      var title = "Deleting appointment";
    }
    
    $.get("index.php?"+location, {teacher: t_id, time: time}, function(data, textStatus) {
      if(data != "") {
        $("#dialog").html(data);
        $("#dialog").dialog({
          modal: true,
          title: title,
          width: 450,
          show: "fade",
          close: function(ev, ui) {
            $("#textos").append("<div id=\"dialog\" />");
          }
        });
        $(".app_form").ajaxForm({success: function(resp, stat) {
          $("#throbber_"+t_id).hide();
          if (resp=="success") {
            $("#dialog").dialog('close');
            window.location.reload();
          } else {
            $(".app_form").before(resp);
            $('.error').slideDown('slow');
          }
        }, beforeSubmit: function() {
          var box = $('.error');
          box.slideUp('fast', function() {
            box.remove();
          });
          $("#throbber_"+t_id).show();
        }});
      }
    });
  });
});
