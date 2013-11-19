(function($){

  var style            = null,
      index            = 0,
      states           = window.OBRANEGRA_STATES,
      scrollThreshold  = 255,
      scrollTop        = 0,
      scrollSum        = 0

  function initialize() {
    //$(window).scroll($.throttle(200, scrollHandler));
    setInterval(scrollHandler, 200);
  }

  function playState() {
    if (style) style.remove();
    style = $("<style>" + states[index] + "</style>").appendTo('head');
    if (index++ >= states.length) index = 0;
  }

  function scrollHandler() {
    var newScrollTop = $(window).scrollTop();
    scrollSum += Math.abs(scrollTop - newScrollTop);
    scrollTop = newScrollTop;

    if (scrollSum >= scrollThreshold) {
      scrollSum = 0;
      playState();
    }
  }

  window.obranegra = {
    initialize: initialize,
    playState: playState
  }


})(jQuery)

jQuery(document).ready(function($) {
  window.obranegra.initialize();
});
