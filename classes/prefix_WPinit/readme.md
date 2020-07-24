**Version 2.3** (24.07.2020)

Custom class "WPinit" basic functions

## CONFIGURATION OPTIONS
* $WPinit_support: select theme support
* $WPinit_google_fonts: google fonts
* $WPinit_css: activate theme styling
* $WPinit_cachebust: activate cachebust styling file
* $WPinit_css_version: theme styling version
* $WPinit_css_path: theme styling path (theme is root)
* $WPinit_js: activate theme js
* $WPinit_js_version: theme js version
* $WPinit_js_path: theme js path (theme is root)
* $WPinit_jquery: activate jquery
* $WPinit_admin_menu: disable backend menus from not admins
* $WPinit_menus: list of all wanted WP menus

## CONFIGURATION FILE
```
"wp": {
  "support": {
    "0": "title-tag",
    "1": "menus",
    "2": "html5"
  },
  "google_fonts": {
    "0": "Roboto"
  },
  "css" : "1",
  "cachebust" : "1",
  "css_version" : 1.0,
  "css_path" : "/dist/style.min.css",
  "js" : "1",
  "js_version" : 1.0,
  "js_path" : "/dist/script.min.js",
  "jquery" : "1",
  "admin_menu": {
    "0": "users.php"
  },
  "menus": {
    "mainmenu": "Main Menu",
    "footermenu": "Footer Menu"
  }
}
```

## USAGE
