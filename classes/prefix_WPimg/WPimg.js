/**
 * javascript/jQuery functions for WPimg Class
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
  var breakpoint = 1130;


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


  /* Element is in viewpoint
  /––––––––––––––––––––––––*/
  $.fn.isOnScreen = function(){
      var win = $(window);
      var viewport = {
          top : win.scrollTop(),
          left : win.scrollLeft()
      };
      viewport.right = viewport.left + win.width();
      viewport.bottom = viewport.top + win.height();
      var bounds = this.offset();
      bounds.right = bounds.left + this.outerWidth();
      bounds.bottom = bounds.top + this.outerHeight();
      return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
  };


  /* AJAX function
  /––––––––––––––––––––––––*/
  function WPimg_ajaxCall(getdata) {
    // ajax is active - disable reload
    // event.preventDefault();
    $.ajax({
      url: directory + "/functions/functions-ajax.php",
      type: 'POST',
      data: getdata,
      success: function(data) {
        // DEBUG: console.log("Ajax update success");
        // DEBUG: console.log(data);
        $(getdata.container).html(data);
        fallbackCheck(getdata.id);
      },
      error:function(){
        // DEBUG: console.log("Ajax update failed");
      }
    });
  }


  /* IMG Visibility
  /––––––––––––––––––––––––*/
  function checkImgVisible() {
    // load img if img is visible
    $( ".lazy-img" ).each(function() {
      var id = $(this).attr('data-id');
      if ($(this).isOnScreen()) {
        var data = {
              action: 'LoadLazyIMG',
              id: id,
              container: '.lazy-img[data-id="' + id + '"]'
            };
        WPimg_ajaxCall(data);
      }
    });
  }


  /* IMG srcset fallback
  /––––––––––––––––––––––––*/
  function fallbackCheck(id) {
    var srcset = $('.lazy-img[data-id="' + id + '"] img').attr('srcset');
    if (!srcset){
      var src = $('.lazy-img[data-id="' + id + '"] img').attr('data-src');
      $('.lazy-img[data-id="' + id + '"] img').attr("src", src);
    }
    $('.lazy-img[data-id="' + id + '"]').removeClass("lazy-img");
  }


  /* Swiper - Arrow navigation
  /––––––––––––––––––––––––*/
  function SwiperArrowNav(id, action){
    // get needed variables
    var windowWidth = $(window).width();
    if(windowWidth > breakpoint){
      var steps = 4;
    } else {
      var steps = 2;
    }
    var scrollContainer = document.querySelector('.swiper[data-id="' + id + '"] .swiper-container'),
        totalWidth      = $('.swiper[data-id="' + id + '"] .swiper-container').get(0).scrollWidth,
        slideWidth      = $('.swiper[data-id="' + id + '"] .swiper-container').width(),
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
      $('.swiper[data-id="' + id + '"] .swiper-container').animate({
          scrollLeft: offset
      }, 500);
    }
    // load next images
    checkImgVisible();
    // toggle arrow visibility
    if(offset < 10 || windowWidth <= breakpoint){
      $('.swiper[data-id="' + id + '"] .back').addClass('hidden');
    } else {
      $('.swiper[data-id="' + id + '"] .back').removeClass('hidden');
    }
    if(offset + 20 > maxRight || windowWidth <= breakpoint){
      $('.swiper[data-id="' + id + '"] .next').addClass('hidden');
    } else {
      $('.swiper[data-id="' + id + '"] .next').removeClass('hidden');
    }
  }



  /* ACTIONS
  /===================================================== */
  $(document).ready(function () {
    // debounce img visibility
    var myLazyAction = WPimg_debounce(function() {
      checkImgVisible();
    }, 500);

    // check visible img on load
    checkImgVisible();

    // check visible img/arrow on swiper hover
    $( ".swiper .swiper-container, .swiper .swiper-container img" ).mouseover(function() {
      setTimeout(function() {
        myLazyAction();
      }, 500);
        var swiperID = $(this).parents('.swiper').attr('data-id');
        SwiperArrowNav(swiperID, 'arrow-check');
    });

    // check visible img on events
    addEventListener("touchend", myLazyAction, false);
    addEventListener("touchmove", myLazyAction, false);
    addEventListener('scroll', myLazyAction);
    // addEventListener("touchstart", checkImgVisible, false);
    // addEventListener("touchend", checkImgVisible, false);
    // addEventListener("mousedown", checkImgVisible, false);
    // addEventListener("mouseup", checkImgVisible, false);
    // addEventListener("mousemove", checkImgVisible, false);

    // swiper arrow navigation
    $('.swiper .next').on('click', function() {
      var swiperID = $(this).parents('.swiper').attr('data-id');
      SwiperArrowNav(swiperID, 'next');
    });
    $('.swiper .back').on('click', function() {
      var swiperID = $(this).parents('.swiper').attr('data-id');
      SwiperArrowNav(swiperID, 'back');
    });
  });



});
