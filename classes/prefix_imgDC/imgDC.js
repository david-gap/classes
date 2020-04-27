/**
 * javascript/jQuery functions for imgDC Class
 *
 * @author      David Voglgsang
 * @version     1.1.2
 *
 */


/*==================================================================================
  Functions
==================================================================================*/
jQuery(function ($) {

  /* Global Settings
  /––––––––––––––––––––––––*/


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


  /* IMG Visibility
  /––––––––––––––––––––––––*/
  function checkImgVisible() {
    // load img if img is visible
    $( ".imgDC" ).each(function() {
      // img data
      var img_src       = $(this).attr('data-src'),
          img_srcset    = $(this).attr('data-srcset'),
          img_id        = $(this).attr('data-id'),
          img_w         = $(this).attr('width'),
          img_h         = $(this).attr('height'),
          img_current_w = $(this).width(),
          percent       = 100 / img_w * img_current_w,
          get_current_h = img_h / 100 * percent,
          img_current_h = $(this).css('height'),
          parent_height = $(this).parent().css('height');
      // update height
      if(img_current_h == parent_height){
        // fallback for imgages that fill a container
      } else {
        $(this).css("max-height", get_current_h);
      }
      // place src if img is visible
      if ($(this).isOnScreen()) {
        if(img_srcset) {
          $(this).attr("srcset", img_srcset);
        } else {
          $(this).attr("src", img_src);
        }
         $(this).css("max-height", "");
         $(this).removeClass("imgDC");
      }
    });
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

    // check visible img on events
    addEventListener("touchend", myLazyAction, false);
    addEventListener("touchmove", myLazyAction, false);
    addEventListener('scroll', myLazyAction);
    addEventListener('click', myLazyAction);
    // addEventListener("touchstart", checkImgVisible, false);
    // addEventListener("touchend", checkImgVisible, false);
    // addEventListener("mousedown", checkImgVisible, false);
    // addEventListener("mouseup", checkImgVisible, false);
    // addEventListener("mousemove", checkImgVisible, false);

    // check visible img/arrow on swiper hover
    $( "img, .arrow" ).mouseover(function() {
      setTimeout(function() {
        myLazyAction();
      }, 500);
    });

  });



});
