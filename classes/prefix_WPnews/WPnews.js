/**
 * javascript/jQuery functions for WPnews Class
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



  /* ACTIONS
  /===================================================== */

  /* Toggle news block
  /------------------------*/
  $(function(){
    $(document).on('click', '.news-block label', function (event) {
      $(this).parents('.news-block').toggleClass('active');
    });
  });
  // accessibility
  $(".news-block label").keypress(function (e) {
    if (e.which == 13) {
        $(this).parents('.news-block').toggleClass('active');
    }
  });



});
