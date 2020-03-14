/**
 * javascript/jQuery functions for WPgalleries Class
 *
 * @author      David Voglgsang
 * @version     1.0
 *
 */


/*==================================================================================
  Functions
==================================================================================*/
jQuery(function ($) {

  /* Global Settings
  /––––––––––––––––––––––––*/
  // mobile breakpoint
  var breakpoint = 800;


  /* Debouncer
  /––––––––––––––––––––––––*/
  // prevents functions to execute to often/fast
  // Usage:
  // var myfunction = WPimg_debounce(function() {
  //   // function stuff
  // }, 250);
  // window.addEventListener('resize', myfunction);
  function WPimg_debounce(func, wait, immediate) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }


  /* Swiper - Arrow navigation
  /––––––––––––––––––––––––*/
  function SwiperArrowNav(id, action){
    // get needed variables
    var windowWidth = $(window).width();
    var layout = $('.galleries-block[data-id="' + id + '"]').attr('data-layout');
    if(layout == "fullscreen"){
      var steps = 1;
    } else {
      if(windowWidth > breakpoint){
        var steps = 4;
      } else {
        var steps = 2;
      }
    }
    var scrollContainer = document.querySelector('.galleries-block[data-id="' + id + '"] .galleries-inner'),
        totalWidth      = $('.galleries-block[data-id="' + id + '"] .galleries-inner').get(0).scrollWidth,
        slideWidth      = $('.galleries-block[data-id="' + id + '"] .galleries-inner').width(),
        stepSize        = (slideWidth / steps),
        backStep        = scrollContainer.scrollLeft - stepSize,
        nextStep        = scrollContainer.scrollLeft + stepSize,
        maxRight        = totalWidth - slideWidth;
    // calculate new position
    if(action == 'next'){
      var offset = nextStep;
    } else if (action == 'back') {
      var offset = backStep;
    } else if (action == 'arrow-check') {
      var offset = scrollContainer.scrollLeft;
    }
    // set new position
    if(action == 'next' || action == 'back'){
      $('.galleries-block[data-id="' + id + '"] .galleries-inner').animate({
          scrollLeft: offset
      }, 500);
    }
    // toggle arrow visibility
    if(stepSize + backStep == 0 && action == 'arrow-check' || nextStep + stepSize == totalWidth && action == 'back' || windowWidth <= breakpoint){
      $('.galleries-block[data-id="' + id + '"] .back').addClass('hidden');
    } else {
      $('.galleries-block[data-id="' + id + '"] .back').removeClass('hidden');
    }
    if(totalWidth == nextStep && action == 'arrow-check' || maxRight == 0 || nextStep + stepSize == totalWidth && action == 'next' || windowWidth <= breakpoint){
      $('.galleries-block[data-id="' + id + '"] .next').addClass('hidden');
    } else {
      $('.galleries-block[data-id="' + id + '"] .next').removeClass('hidden');
    }
    // DEBUG:
    // console.log(
    //   " totalWidth: " + totalWidth +
    //   " slideWidth: " + slideWidth +
    //   " stepSize: " + stepSize +
    //   " backStep: " + backStep +
    //   " nextStep: " + nextStep +
    //   " maxRight: " + maxRight
    // );
  }



  /* ACTIONS
  /===================================================== */
  $(document).ready(function () {

    // check visible img/arrow on swiper hover
    $( ".galleries-block .galleries-inner, .galleries-block .galleries-inner img" ).mouseover(function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'arrow-check');
    });

    // swiper arrow navigation
    $('.galleries-block .next').on('click', function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'next');
    });
    $('.galleries-block .back').on('click', function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'back');
    });

  });



});
