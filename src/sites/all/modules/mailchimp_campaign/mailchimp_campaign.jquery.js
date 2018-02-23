// Drupal.behavors ensures that our new binding can be 
// applied to any new occurances of #example-ajax 
// created on the fly by other scripts.
Drupal.behaviors.mailchimp_campaign = function(context) {
  // Bind an AJAX callback to the test segment button
  var editTest = $('input#edit-test.form-submit');

  editTest.click(function(event) {
    var conditions = $('textarea#edit-mailchimp-campaign-segment').val();
    var listid = $('input#edit-mailchimp-campaign-list-id').val();
    var match = $('select#edit-mailchimp-campaign-segment-match').val();
    // Get the URL without the query string - this is
    // so that we can distinguish between GET and POST
    // requests.
    var posturl = 'http://' + document.domain + Drupal.settings.basePath + 'mailchimp/campaign/segment';
    // Prevent the default link action - we don't
    // want to trigger a synchronous response.
    event.preventDefault();

    // Perform the ajax request - the configurations
    // below can be modified to suit your needs:
    // http://docs.jquery.com/Ajax/jQuery.ajax#options
    $.ajax({
      type: "POST",
      url: posturl,
      data: {
        'from_js' : true,
        'mailchimp_campaign_token' : Drupal.settings.mailchimp_campaign_token,
        'conditions' : conditions,
        'listid' : listid,
        'match' : match
      },
      dataType: "json",
      success: function (data) {
        if (data.message) {
          console.log(data);
          if ($('#edit-mailchimp-campaign-segment-wrapper .result').length > 0) {
            $('#edit-mailchimp-campaign-segment-wrapper .result').replaceWith('<div class="result">' + data.message + data.groups + '</div>');
          }
          else {
            $('#edit-mailchimp-campaign-segment-wrapper').append('<div class="result">' + data.message + data.groups + '</div>');
          }
        }
      },
      error: function (xmlhttp) {
        alert('An error occured: ' + xmlhttp.status);
      }
    });
  });
}