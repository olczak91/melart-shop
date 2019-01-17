$(document).ready(function(){

	$('#loader').delay(500).fadeOut();

	$('.bxslider').bxSlider({
      mode: 'fade',
      speed: 1500,
      pause: 4000,
      auto: true
  });

  $('#res').click(function(){
    $('header nav').fadeToggle();
  });

	$('.message').delay(4000).fadeOut();

	$('#validate').click(function(e) {

      var isValid = true;
      $('.req').each(function() {
          if ($.trim($(this).val()) == '') {
              isValid = false;
              $(this).css({
                  "border-bottom": "1px solid rgb(255, 150, 160)",
                  "background-color": "rgb(255, 207, 207)"
              });
              $(this).focus();
              return false;
          }
          else {
              $(this).css({
                  "border": "",
                  "background": ""
              });
          }
      });
      if (isValid == false)
          e.preventDefault();

    });

});

