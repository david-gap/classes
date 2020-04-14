**Version 1.2.1** (14.04.2020)

Custom class "WPinit" basic functions

## CONFIGURATION OPTIONS
* $WPinit_gutenberg: disable gutenberg
* $WPinit_gutenberg_css: disable gutenberg styling
* $WPinit_support: select theme support
* $WPinit_google_fonts: google fonts
* $WPinit_css: activate theme styling
* $WPinit_css_path: theme styling path (theme is root)
* $WPinit_js: activate theme js
* $WPinit_js_path: theme js path (theme is root)
* $WPinit_gutenberg_jquery: activate jquery

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
