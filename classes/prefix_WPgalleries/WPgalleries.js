/**
 * javascript/jQuery functions for WPgalleries Class
 *
 * @author      David Voglgsang
 *
 */


/*==================================================================================
  Functions
==================================================================================*/
jQuery(function ($) {

  /* Global Settings
  /––––––––––––––––––––––––*/
  // mobile breakpoint
  var breakpoint = 767;


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
    var steps_d = $('.galleries-block[data-id="' + id + '"]').attr('data-steps');
    var steps_m = $('.galleries-block[data-id="' + id + '"]').attr('data-stepsmobile');
    if(layout == "fullscreen"){
      var steps = 1;
    } else {
      if(windowWidth > breakpoint){
        var steps = steps_d;
      } else {
        var steps = steps_m;
      }
    }
    var scrollContainer = document.querySelector('.galleries-block[data-id="' + id + '"] .galleries-inner'),
        totalWidth      = $('.galleries-block[data-id="' + id + '"] .galleries-inner').get(0).scrollWidth,
        slideWidth      = $('.galleries-block[data-id="' + id + '"] .galleries-inner').width(),
        stepSize        = (slideWidth / steps),
        currentpos      = scrollContainer.scrollLeft,
        backStep        = currentpos - stepSize,
        nextStep        = currentpos + stepSize,
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
    if(currentpos > 0 || action == 'next' || action == 'back' && offset > stepSize){
      $('.galleries-block[data-id="' + id + '"] .back').removeClass('hidden');
    } else {
      $('.galleries-block[data-id="' + id + '"] .back').addClass('hidden');
    }
    if(totalWidth > nextStep + slideWidth || action == 'back'){
      $('.galleries-block[data-id="' + id + '"] .next').removeClass('hidden');
    } else {
      $('.galleries-block[data-id="' + id + '"] .next').addClass('hidden');
    }
    // DEBUG:
    // console.log(
    //   " totalWidth: " + totalWidth +
    //   " slideWidth: " + slideWidth +
    //   " stepSize: " + stepSize +
    //   " backStep: " + backStep +
    //   " nextStep: " + nextStep +
    //   " maxRight: " + maxRight +
    //   " current pos: " + currentpos
    // );
  }



  /* ACTIONS
  /===================================================== */
  $(document).ready(function () {

    $( ".galleries-block" ).each(function() {
      var swiperID = $(this).attr('data-id');
      SwiperArrowNav(swiperID, 'arrow-check');
    });


    // check visible img/arrow on swiper hover
    $( ".galleries-block .galleries-inner, .galleries-block .galleries-inner img" ).mouseover(function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'arrow-check');
    });

    // swiper arrow navigation
    $('.galleries-block .next').on('click', function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'next');
      setTimeout(function() {
        SwiperArrowNav(swiperID, 'arrow-check');
      }, 500);
    });
    $('.galleries-block .back').on('click', function() {
      var swiperID = $(this).parents('.galleries-block').attr('data-id');
      SwiperArrowNav(swiperID, 'back');
      setTimeout(function() {
        SwiperArrowNav(swiperID, 'arrow-check');
      }, 500);
    });

  });



});
