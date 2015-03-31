var $ = jQuery;

$(function() {

  var linkie = $( "#wp-admin-bar-speedly a" );

  linkie.click(function( event ) {

    linkie.append('<span class="indicator" style="margin-left:10px;">Loading...</span>');

    event.preventDefault();

    var url = linkie.attr('href');
    
    $.ajax({
        url: url,
        type: "POST"
    }).done(function( data ) {
        if(data.success == 'true') {
          $(".indicator").delay(500).queue(function(n) {  $(this).html('Success!'); n(); });
          $(".indicator").delay(750).fadeOut(750);
        } else {
          $(".indicator").delay(500).queue(function(n) {  $(this).html('Invalid token! Please contact support'); n(); });
          $(".indicator").delay(2500).fadeOut(750);
        }

      });

  });

});