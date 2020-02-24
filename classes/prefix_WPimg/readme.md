**Version 1.3.1** (24.02.2020)

Custom class "WPimg" adding custom field with img dominant color for lazy loading. Gallery option (grid/swiper).

## CONFIGURATION OPTIONS
* $WPimg_content: simple way to disable the lazy loading inside the_content
* $WPimg_assets: include classes assets. Disable it if you use your own files
* $WPimg_js_loading: lazy load with JS
* $parent_element: parent container typ (div, section)
* $WPimg_popupContent: On klick open media in a lightbox
* $nocolor_files: exclude file types from dominant color generator
* $WPimg_defaultcolor: default color

## CONFIGURATION FILE
```
"prefix_WPimg": {
  "WPimg_content": true,
  "WPimg_assets": true,
  "WPimg_js_loading": true,
  "WPimg_parent_element": "div",
  "WPimg_popupContent": false,
  "WPimg_nocolor_files": {
    "0": "video/mp4",
    "1": "video/quicktime",
    "2": "video/videopress",
    "3": "audio/mpeg"
  },
  "WPimg_defaultcolor": "ffffff"
}
```

## USAGE

You can load the img with lazyloading by inserting the static function getIMG:
(You only need the img ID, can add custom attributes or custom css)
```php
// simple call
echo prefix_WPimg::getIMG($img_id);
// simple call with custom attribute
echo prefix_WPimg::getIMG($img_id, 'data-custom="value"');
// simple call with custom css class
echo prefix_WPimg::getIMG($img_id, "", "custom-css");
```

To build a gallery use the shortcode [gallery] and select the IDs and template.

### Shortcode attribute
* post_type: default "attachment"
* post_status: default "publish"
* id: default "-1"
* template: default "grid"
* class: default empty
```
[gallery template="swiper" id="1, 2, 3, 4, 5"]
```
