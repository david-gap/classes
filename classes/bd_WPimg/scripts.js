jQuery(document).ready(function($){

  /* AJAX
  /––––––––––––––––––––––––*/
  function ajaxCall(getdata) {
    $.ajax({
      url: WPimg_Ajax,
      type: 'POST',
      data: getdata,
      success: function(data) {
        // console.log("Ajax update success");
        // console.log(data);
        if(getdata.action == "DominantColors"){
          $('#WPimg .wp-list-table').removeClass('hidden');
          if (data !== "stop") {
            $('#WPimg #the-list').append(data);
            $('#WPimg .log').html("proceeding...");
            $( "#WPimg .ajax-action" ).trigger( "click" );
          } else {
            $('#WPimg .log').html("done!");
          }
        }
      },
      error:function(){
        // console.log("Ajax update failed");
      }
    });
  }

  /* RUN AJAX REQUEST
  /––––––––––––––––––––––––*/
  jQuery(".ajax-action").click(function(e) {
    // get ajax request
    var run = $(this).attr('data-action');
    // define ajax data
    var data = {
      action: run
    };
    ajaxCall(data);

  });

});
