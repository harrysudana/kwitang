$(document).ready(function() {

  $(".input-search").click(function() {
    if ($(this).val() != '') {
      $(this).closest('form').submit();
    }
  });

  $(".carousel-caption").hover(function() {
    $(this).closest('.carousel-inner').find('p').slideDown('fast');
  }, function() {});
  $(".carousel").hover(function() {}, function() {
    $(this).find('.carousel-caption p').slideUp('fast');
  });

});
