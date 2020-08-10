**Version 2.1.2** (10.08.2020)

Custom class "imgDC" to get image dominant color and ad a WP preloader

## CONFIGURATION OPTIONS
* $imgDC_wp: activate WP settings
* $imgDC_content: simple way to disable the lazy loading inside the_content
* $imgDC_assets: include classes assets. Disable it if you use your own files
* $imgDC_nocolor_files: exclude file types from dominant color generator
* $imgDC_defaultcolor: default color

## CONFIGURATION FILE
```
"imgDC": {
  "wp": 1,
  "content": 1,
  "assets": 1,
  "nocolor_files": {
    "0": "'video/mp4",
    "1": "video/quicktime",
    "2": "video/videopress",
    "3": "audio/mpeg"
  },
  "defaultcolor": "ffffff"
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
