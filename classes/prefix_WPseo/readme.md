**Version 2.2.5** (17.09.2020)

Custom class "prefix_WPseo" for default SEO settings.

## CONFIGURATION OPTIONS
* $WPseo_logo: Logo image ID from WP media
* $WPseo_tracking: Google tracking code (analytics or tag manager)
* $WPseo_favicon: fav icon link
* $WPseo_icon: default screen icon ID from WP media
* $WPseo_icon_72: apple screen icon 72 ID from WP media
* $WPseo_icon_114: apple screen icon 114 ID from WP media
* $WPseo_datastructure: turn datastructure on/off
* $WPseo_datastructure_page: turn custom datastructure on/off for pages and posts
* $WPseo_datastructure_add: additional structure attributes

## CONFIGURATION FILE
```
"seo": {
  "logo": 0,
  "google-tracking": "",
  "favicon": "",
  "apple-touch-icon": 0,
  "apple-touch-icon-72": 0,
  "apple-touch-icon-114": 0,
  "data-structure": 0,
  "data-structure-page": 0,
  "data-structure-add": {
    "0": {
      "key": "type"
      "value": "Website"
    }
  },
  "address": {
    "company": "Company",
    "street": "Street",
    "street2": "Street 2",
    "postalCode": "Postal Code",
    "country": "Country",
    "city": "City",
    "phone": "0041",
    "mobile": "0041 2",
    "email": "info@domain.com"
  }
}
```

## USAGE


## FILTERS
Manipulate datastructure
WPseo_datastructure
