jQuery(document).ready(function($){

  var meta_gallery_frame;


  /* SORT SELECTED IMAGES
  /------------------------*/
  $( ".galleriesImages_list" ).sortable({
    update: function( event, ui ) {
      // create array
      var img_ids = [];
      // push ids into array
      $( ".galleriesImages_list li" ).each(function() {
        var img_id = $(this).attr("data-id");
        img_ids.push(img_id);
      });
      // array to string
      var newSort = img_ids.join();
      // insert new value
      $("#galleriesImages").val(newSort);
    }
  });


  /* WP MEDIA SELECTION
  /------------------------*/
  $(document).on('click', '.wp-media', function (e) {
    // get action
    var action = $(this).attr('data-action');
    // check if right action is active for img selection
    if(action == "WPgalleries"){
      // stop page reload
      e.preventDefault();
      // if the frame already exists, re-open it.
      if ( meta_gallery_frame ) {
        meta_gallery_frame.open();
        return;
      }
      // Sets up the media library frame
      meta_gallery_frame = wp.media.frames.meta_gallery_frame = wp.media({
        title: galleriesImages.title,
        button: { text:  galleriesImages.button },
        library: { type: 'image' },
        multiple: true
      });
      // get already selected images
      meta_gallery_frame.on('open', function() {
        var selection = meta_gallery_frame.state().get('selection');
        var library = meta_gallery_frame.state('gallery-edit').get('library');
        var ids = jQuery('#galleriesImages').val();
        if (ids) {
          idsArray = ids.split(',');
          idsArray.forEach(function(id) {
                  attachment = wp.media.attachment(id);
                  attachment.fetch();
                  selection.add( attachment ? [ attachment ] : [] );
          });
        }
      });
      //When an image is selected, run a callback.
      meta_gallery_frame.on('select', function() {
              var imageIDArray = [];
              var imageHTML = '';
              var metadataString = '';
              images = meta_gallery_frame.state().get('selection');
              images.each(function(attachment) {
                      imageIDArray.push(attachment.attributes.id);
                      imageHTML += '<li data-id="'+attachment.attributes.id+'"><div class="galleriesImages_container"><span class="remove_image"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24.9 24.9" xml:space="preserve"><rect x="-3.7" y="10.9" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -5.1549 12.4451)" fill="#000" width="32.2" height="3"/><rect x="10.9" y="-3.7" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -5.1549 12.4451)" fill="#000" width="3" height="32.2"/></svg></span><img id="'+attachment.attributes.id+'" src="'+attachment.attributes.sizes.thumbnail.url+'"></div></li>';
              });
              metadataString = imageIDArray.join(",");
              if (metadataString) {
                      jQuery("#galleriesImages").val(metadataString);
                      jQuery("#galleriesImages_list").html(imageHTML);
              }
      });
      // Finally, open the modal
      meta_gallery_frame.open();
    }
  });



  /* REMOVE IMAGE FROM SELECTION
  /------------------------*/
  $(document).on('click', '.galleriesImages_container .remove_image', function (e) {
    event.preventDefault();
    if (confirm('Are you sure you want to remove this image?')) {
      var removedImage = $(this).parents('li').attr('data-id');
      var oldGallery = $("#galleriesImages").val();
      var newGallery = oldGallery.replace(','+removedImage,'').replace(removedImage+',','').replace(removedImage,'');
      $(this).parents().eq(1).remove();
      $("#galleriesImages").val(newGallery);
    }
  });

});
