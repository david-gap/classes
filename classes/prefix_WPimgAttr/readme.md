**Version 2.1** (24.07.2020)

Custom class "WPimgAttr" adding custom fields for each language to WP media and inseting the alt attribute in front end.

## CONFIGURATION OPTIONS
* $WPimgAttr_Alt_content: simple way to disable img alt for the_content
* $WPimgAttr_Alt_attachmentt: simple way to disable img attachment alt
* $WPimgAttr_Alt_shortcode: simple way to disable img in shortcode alt
* $WPimgAttr_Alt_languages: List of Languages, first one is default
* $WPimgAttr_Alt_prefix: Prefix for variables

## CONFIGURATION FILE
```
"WPimgAlt": {
  "WPimgAlt_content": 1,
  "WPimgAlt_attachment": 1,
  "WPimgAlt_shortcode": 1,
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
