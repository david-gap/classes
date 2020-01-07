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
        if(getdata.action == 'LoadLazyIMG'){
          $(getdata.container).html(data);
          fallbackCheck(getdata.id, "lazy-img");
        } else if (getdata.action == 'PostPopUp') {
          $('.popup[data-container="' + getdata.container + '"] .popup-container .popup-content').removeClass('loading');
          $('.popup[data-container="' + getdata.container + '"] .popup-container .popup-content').html(data);
          showPopUpArrows(getdata.container, getdata.id);
          fallbackCheck(getdata.id, "post-flex");
        }
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
  function fallbackCheck(id, target) {
    var srcset = $('.' + target + '[data-id="' + id + '"] img').attr('srcset');
    if (!srcset){
      var src = $('.' + target + '[data-id="' + id + '"] img').attr('data-src');
      $('.' + target + '[data-id="' + id + '"] img').attr("src", src);
    }
    $('.' + target + '[data-id="' + id + '"]').removeClass("lazy-img");
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



  /* POP UP
  /===================================================== */
  /* OPEN POP-UP
  /––––––––––––––––––––––––*/
  function openPopUp(container) {
    // toogle popup classes
    $('.popup[data-container="' + container + '"]').removeClass('dn');
    setTimeout(function() {
      $('html').addClass('popup-noscroll');
      $('.popup[data-container="' + container + '"]').removeClass('closed');
    }, 500);
    $('.popup[data-container="' + container + '"] .popup-container .popup-content').addClass('loading');
  }

  /* ADD POP-UP
  /––––––––––––––––––––––––*/
  function cleanPopUpContent(container) {
      $('.popup[data-container="' + container + '"] .popup-container .popup-content .post-flex').remove();
      $('.popup[data-container="' + container + '"] .popup-container .popup-content').addClass('loading');
  }

  /* SHOW POP-UP ARROWS
  /––––––––––––––––––––––––*/
  function showPopUpArrows(container, id) {
    var current_class = $('[data-id="' + container + '"]').attr('class');

    if(current_class == 'swiper'){
      var updated_id = id.replace("img-", ""),
          prev_id    = $('[data-id="' + container + '"] article[data-id="' + updated_id + '"]').prev( "article" ).attr('data-id'),
          next_id    = $('[data-id="' + container + '"] article[data-id="' + updated_id + '"]').next( "article" ).attr('data-id');
    } else {
      var prev_id   = $('[data-id="' + container + '"] article #' + id).parents('article').prev( "article" ).find('img').attr('id'),
          next_id   = $('[data-id="' + container + '"] article #' + id).parents('article').next( "article" ).find('img').attr('id');
    }

    // show arrows
    if(prev_id){
      $('.popup[data-container="' + container + '"] .popup-container .popup-content').find('.popup-arrow.back').removeClass('hidden');
    }
    if(next_id){
      $('.popup[data-container="' + container + '"] .popup-container .popup-content').find('.popup-arrow.next').removeClass('hidden');
    }
  }

  /* ADD POP-UP
  /––––––––––––––––––––––––*/
  function addPopUpCode(container) {
    if ( $( '.popup[data-container="' + container + '"]' ).length ) {
      // DEBUG: console.log("POPUP div exists");
    } else {
      // DEBUG: console.log("POPUP div added");
      $( "body" ).append( '<div class="popup dn closed" data-container="' + container + '" data-content="img-popup"><div class="popup-container"><span class="close">X</span><div class="popup-content"></div></div></div>' );
    }
    openPopUp(container);
  }

  /* LOAD POP-UP CONTENT
  /––––––––––––––––––––––––*/
  function getPopUpContent(id, container) {
    // vars
    var data = {
          action: 'PostPopUp',
          id: id,
          container: container
        };
    WPimg_ajaxCall(data);
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


    /* CALL POP-UP POST/IMG
    /––––––––––––––––––––––––*/
    $( '.grid article' ).click(function() {
      var get_id     = $(this).attr('id'),
          container  = $(this).parents('.grid').attr('data-id');
      addPopUpCode(container);
      getPopUpContent(get_id, container);
    });
    $( '.swiper .swiper-container article' ).click(function() {
      var get_id     = 'img-' + $(this).attr('data-id'),
          container  = $(this).parents('.swiper').attr('data-id');
      addPopUpCode(container);
      getPopUpContent(get_id, container);
    });

    /* BACK POP-UP
    /––––––––––––––––––––––––*/
    $(document).on("click", '.popup .back', function(event) {
      cleanPopUpContent('grid-popup');
      var active_id = $(this).parents('.post-flex').attr('data-id'),
          container = $(this).parents('.popup').attr('data-container'),
          get_id    = $('[data-id="' + container + '"] article #' + active_id).parents('article').prev( "article" ).find('img').attr('id');
      if(get_id){
        getPopUpContent(get_id, container);
      } else {
        var updated_active = active_id.replace("img-", "");
        var new_id = $('[data-id="' + container + '"] article[data-id="' + updated_active + '"]').prev( "article" ).attr('data-id');
        getPopUpContent(new_id, container);
      }
      // DEBUG: console.log(get_id);
    });

    /* NEXT POP-UP
    /––––––––––––––––––––––––*/
    $(document).on("click", '.popup .next', function(event) {
      cleanPopUpContent('grid-popup');
      var active_id = $(this).parents('.post-flex').attr('data-id'),
          container = $(this).parents('.popup').attr('data-container'),
          get_id    = $('[data-id="' + container + '"] article #' + active_id).parents('article').next( "article" ).find('img').attr('id');
      if(get_id){
        getPopUpContent(get_id, container);
      } else {
        var updated_active = active_id.replace("img-", "");
        var new_id = $('[data-id="' + container + '"] article[data-id="' + updated_active + '"]').next( "article" ).attr('data-id');
        getPopUpContent(new_id, container);
      }
      // DEBUG: console.log(get_id);
    });

    /* CLOSE POP-UP
    /––––––––––––––––––––––––*/
    $(document).on("click", '.popup .close', function(event) {
      $('html').removeClass('popup-noscroll');
      $(this).parents('.popup').addClass('closed');
      $(this).parents('.popup').remove();
    });
  });



});
