/**
 * javascript/jQuery functions for WPintranet Class
 *
 * @author      David Voglgsang
 * @version     1.0.1
 *
 */


/*==================================================================================
  Functions
==================================================================================*/
jQuery(function ($) {


  /* Resize Iframe height
  /------------------------*/
  function ResizeIframeFromParent(id) {
    if ($('#'+id).length > 0) {
      var window = document.getElementById(id).contentWindow;
      var prevheight = $('#'+id).attr('height');
      var newheight = Math.max( window.document.body.scrollHeight, window.document.body.offsetHeight, window.document.documentElement.clientHeight, window.document.documentElement.scrollHeight, window.document.documentElement.offsetHeight );
      if (newheight != prevheight && newheight > 0) {
        $('#'+id).attr('height', newheight);
        // console.log("Adjusting iframe height for "+id+": " +prevheight+"px => "+newheight+"px");
      }
    }
  }


  /* First directory
  /------------------------*/
  function WPintranet_firstDirectory(id) {
    if($('#myftp').length!==0){
      var ftp_active = $('#myftp').data('active');
      $('#intranet li[data-directory="' + ftp_active + '"]').addClass('active');
      var data = {
        action: 'IntranetNewDirectory',
        directory: ftp_active,
        title: $('#intranet li.active').data('title')
      };
      WPintranet_ajaxCall(data);
    }
  }

  /* AJAX function
  /––––––––––––––––––––––––*/
  function WPintranet_ajaxCall(getdata) {
    // ajax is active - disable reload
    // event.preventDefault();
    console.log("ajax call directory: " + getdata.title);
    $.ajax({
      url: ajax_action_file + "/functions/functions-ajax.php",
      type: 'POST',
      data: getdata,
      success: function(data) {
        // DEBUG: console.log("Ajax update success");
        // DEBUG: console.log(data);
        $("#myftp").html( data );
        setInterval(function() {
            ResizeIframeFromParent('myftp-iframe');
        }, 100);
      },
      error:function(){
        // DEBUG: console.log("Ajax update failed");
      }
    });
  }



  /* ACTIONS
  /===================================================== */
  $(document).ready(function () {
    // get first directory
    WPintranet_firstDirectory();
    // on click change directory
    $('#intranet li').on('click', function() {
      $('#intranet li').removeClass('active');
      $(this).addClass('active');
      var data = {
        action: 'IntranetNewDirectory',
        directory: $(this).data('directory'),
        title: $('#intranet li.active').data('title')
      };
      WPintranet_ajaxCall(data);
    });
    // tabindex click
    $(".intranet-directories li").keypress(function (e) {
       if (e.which == 13) {
           $(this).trigger( "click" );
       }
    });
  });

});
