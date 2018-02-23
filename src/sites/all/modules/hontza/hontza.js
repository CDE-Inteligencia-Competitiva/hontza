
  Drupal.behaviors.hontza = function(context) {
    $('#edit-area').hide();
    $('#edit-checkarea-wrapper').children().change(function(){
      if ($('#edit-checkarea').is(':checked')){
        $('#edit-area').show("slow");
      }
      else {
        $('#edit-area').hide("slow");
      }
    });
  }

