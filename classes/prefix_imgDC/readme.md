**Version 1.1.3** (05.05.2020)

Custom class "imgDC" to get image dominant color and ad a WP preloader

## CONFIGURATION OPTIONS
* $imgDC_wp: activate WP settings
* $imgDC_content: simple way to disable the lazy loading inside the_content
* $imgDC_assets: include classes assets. Disable it if you use your own files
* $imgDC_nocolor_files: exclude file types from dominant color generator
* $imgDC_defaultcolor: default color

## CONFIGURATION FILE
```
"wp": {
  "gutenberg": true,
  "gutenberg_css": true,
  "support": {
    "0": "title-tag",
    "1": "menus",
    "2": "html5"
  },
  "google_fonts": {
    "0": "Roboto"
  },
  "css" : true,
  "csspath" : "/dist/style.min.css",
  "js" : true,
  "jspath" : "/dist/script.min.js",
  "jquery" : true
}
```

## USAGE

### GET DOMINANT COLOR BY URL
```php
echo prefix_imgDC::IMGcolor($image_url);
```

### GET WORDPRESS SINGLE IMAGE
```php
echo prefix_imgDC::getIMG($image_id, 'data-additional="value"', 'additional_css');
```
