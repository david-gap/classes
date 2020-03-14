jQuery(document).ready(function($){

  var meta_gallery_frame;

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
              imageHTML += '<ul class="galleriesImages_list">';
              images.each(function(attachment) {
                      imageIDArray.push(attachment.attributes.id);
                      imageHTML += '<li><div class="galleriesImages_container"><span class="galleriesImages_close"><img id="'+attachment.attributes.id+'" src="'+attachment.attributes.sizes.thumbnail.url+'"></span></div></li>';
              });
              imageHTML += '</ul>';
              metadataString = imageIDArray.join(",");
              if (metadataString) {
                      jQuery("#galleriesImages").val(metadataString);
                      jQuery("#galleriesImages_src").html(imageHTML);
              }
      });
      // Finally, open the modal
      meta_gallery_frame.open();
    }
  });


  $(document).on('click', '.galleriesImages_container .remove_image', function (e) {
    event.preventDefault();
    if (confirm('Are you sure you want to remove this image?')) {
      var removedImage = $(this).children('img').attr('id');
      var oldGallery = $("#galleriesImages").val();
      var newGallery = oldGallery.replace(','+removedImage,'').replace(removedImage+',','').replace(removedImage,'');
      $(this).parents().eq(1).remove();
      $("#galleriesImages").val(newGallery);
    }
  });

});
