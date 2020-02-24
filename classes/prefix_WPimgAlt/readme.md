**Version 1.1.1** (24.02.2020)

Custom class "WPimgAlt" adding custom fields for each language to WP media and inseting the alt attribute in front end.

## CONFIGURATION OPTIONS
* $WPimgAlt_content: simple way to disable img alt for the_content
* $WPimgAlt_attachment: simple way to disable img attachment alt
* $WPimgAlt_shortcode: simple way to disable img shortcode alt
* $WPimgAlt_languages: List of Languages, first one is default
* $WPimgAlt_prefix: Prefix for variables

## CONFIGURATION FILE
```
"prefix_WPimgAlt": {
  "WPimgAlt_content": true,
  "WPimgAlt_attachment": true,
  "WPimgAlt_shortcode": true,
  "WPimgAlt_languages": {
    "0": "de",
    "1": "en",
    "2": "fr"
  }
}
```

## USAGE
You can insert the alternative text by inserting the static function into the alt attribute like this:
(You only need the img ID)
```
<img alt="<?= prefix_WPimgAlt::getAltAttribute($img_id); ?>" />
```
