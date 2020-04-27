/**
 * javascript/jQuery functions for popup - WPgalleries extension
 *
 * @author      David Voglgsang
 * @version     1.0
 *
 */


/*==================================================================================
  Functions
==================================================================================*/

  /* OPEN POP-UP
  /------------------------*/
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
  /------------------------*/
  function cleanPopUpContent(container) {
      $('.popup[data-container="' + container + '"] .popup-container .popup-content').empty();
      $('.popup[data-container="' + container + '"] .popup-container .popup-content').addClass('loading');
      $('.popup[data-container="' + container + '"] .popup-container .popup-arrow').addClass('hidden');
  }


  /* SHOW POP-UP ARROWS
  /------------------------*/
  function showPopUpArrows(container, id) {
    var prev_id   = $('[data-id="' + container + '"] img[data-id=' + id + ']').parents('div').prev( "div" ).find('img').attr('data-id'),
        next_id   = $('[data-id="' + container + '"] img[data-id=' + id + ']').parents('div').next( "div" ).find('img').attr('data-id');

    // show arrows
    if(prev_id){
      $('.popup[data-container="' + container + '"] .popup-container').find('.popup-arrow.back').removeClass('hidden');
    }
    if(next_id){
      $('.popup[data-container="' + container + '"] .popup-container').find('.popup-arrow.next').removeClass('hidden');
    }
  }


  /* ADD POP-UP
  /------------------------*/
  function addPopUpCode(container) {
    if ( $( '.popup[data-container="' + container + '"]' ).length ) {
      // DEBUG: console.log("POPUP div exists");
    } else {
      // DEBUG: console.log("POPUP div added");
      $( "body" ).append( '<div class="popup dn closed" data-container="' + container + '"><div class="popup-container"><span class="close">X</span><span class="popup-arrow back hidden"></span><div class="popup-content"></div><span class="popup-arrow next hidden"></span></div></div>' );
    }
    openPopUp(container);
  }


  /* LOAD POP-UP CONTENT
  /------------------------*/
  function getPopUpContent(action, container, id) {
    $('.popup[data-container="' + container + '"] .popup-container .popup-content').attr('data-id', id);
    // default img loading
    if(action == "popup-img"){
      $('.popup[data-container="' + container + '"] .popup-container').addClass('img-content');
      $('.popup-img[data-id="' + container + '"] img[data-id="' + id + '"]').clone().appendTo('.popup[data-container="' + container + '"] .popup-content');
      $('.popup[data-container="' + container + '"] .popup-content').removeClass('loading');
      showPopUpArrows(container, id);
    }
  }




/*==================================================================================
  Actions
==================================================================================*/

  /* ON DOCUMENT READY
  /===================================================== */
  $(document).ready(function () {

      /* CALL POP-UP IMG
      /------------------------*/
      $( '.popup-img img' ).click(function() {
        var get_id    = $(this).attr('data-id'),
            container = $(this).parents('.popup-img').attr('data-id');
        addPopUpCode(container);
        getPopUpContent("popup-img", container, get_id);
      });


      /* BACK POP-UP
      /------------------------*/
      $(document).on("click", '.popup .back', function(event) {
        var active_id = $(this).parents('.popup').find('.popup-content').attr('data-id'),
            container = $(this).parents('.popup').attr('data-container'),
            get_id    = $('.popup-img[data-id="' + container + '"] img[data-id=' + active_id + ']').parents('div').prev( "div" ).find('img').attr('data-id');
        // clean popup
        cleanPopUpContent(container);
        // insert new content
        if(get_id) {
          getPopUpContent("popup-img", container, get_id);
        }
      });


      /* NEXT POP-UP
      /------------------------*/
      $(document).on("click", '.popup .next', function(event) {
        var active_id = $(this).parents('.popup').find('.popup-content').attr('data-id'),
            container = $(this).parents('.popup').attr('data-container'),
            get_id    = $('.popup-img[data-id="' + container + '"] img[data-id=' + active_id + ']').parents('div').next( "div" ).find('img').attr('data-id');
        // clean popup
        cleanPopUpContent(container);
        // insert new content
        if(get_id) {
          getPopUpContent("popup-img", container, get_id);
        }
      });


      /* KEYBOARD ARROWS POP-UP
      /------------------------*/
      $(document).on("keydown", function(event) {
        if (event.keyCode == 37) {
          var imgclass = $('.popup .back').attr('class');
          if (!imgclass.includes("hidden")) {
            $('.popup .back').trigger("click");
          }
        } else if (event.keyCode == 39) {
            var imgclass = $('.popup .next').attr('class');
            if (!imgclass.includes("hidden")) {
              $('.popup .next').trigger("click");
            }
        } else if (event.keyCode == 38) {} else if (event.keyCode == 40) {}
      });


      /* CLOSE POP-UP - X
      /------------------------*/
      $(document).on("click", '.popup .close', function(event) {
        $('html').removeClass('popup-noscroll');
        $(this).parents('.popup').addClass('closed');
        $(this).parents('.popup').remove();
      });


      /* CLOSE POP-UP - BG
      /------------------------*/
      $('body').click(function(event) {
        var target_class = $(event.target).attr('class');
        if(target_class == "popup"){
          $('html').removeClass('popup-noscroll');
          $(event.target).addClass('closed');
          $(event.target).remove();
        }
      });

  });
