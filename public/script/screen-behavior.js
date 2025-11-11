jQuery(function($) {

  function doAnimations() {
    var offset = $(window).scrollTop() + $(window).height();
    var $animatables = $('.animatable');

    if ($animatables.length == 0) {
      $(window).off('scroll', doAnimations);
    }

    $animatables.each(function() {
      var $elem = $(this);
      if (($elem.offset().top + $elem.height() - 20) < offset) {
        $elem.removeClass('animatable').addClass('animated');
      }
    });
  }

  $(window).on('scroll', doAnimations);
  $(window).trigger('scroll');

});
