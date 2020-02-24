**Version 1.0** (24.02.2020)

Custom class "template" with teplate parts and header / footer builder

## CONFIGURATION OPTIONS
* $template_container: activate container
* $template_coloring: template coloring (dark/light)
* $template_ph_active: activate placeholder
* $template_ph_address: placeholder show address block
* $template_ph_custom: placeholder custom content
* $template_address: address block content
* $template_socialmedia: social media
* $template_header_divider: Activate header divider
* $template_header_sticky: activate sticky header
* $template_header_dmenu: Activate header hamburger for desktop
* $template_header_custom:  Custom header html
* $template_header_sort: Sort and activate blocks inside header builder
* $template_header_logo_d: desktop logo configuration
* $template_header_logo_m: mobile logo configuration
* $template_footer_active: activate footer
* $template_footer_cr: copyright text
* $template_footer_custom: custom html
* $template_footer_sort: Sort and activate blocks inside footer builder

## CONFIGURATION FILE
```
"general": {
  "container": true,
  "coloring": "light",
  "placeholder": {
    "active": true,
    "address": true,
    "notification": "",
    "custom": ""
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
    "email": "info@dmili.com"
  },
  "contactblock": {
    "phone": "",
    "mail": "",
    "whatsapp": ""
  },
  "socialmedia": {
    "facebook": "",
    "instagram": ""
  }
},
"header": {
  "sort": {
    "logo": true,
    "menu": true,
    "socialmedia": true,
    "custom": false
  },
  "logo_desktop": {
    "img": "",
    "width": "",
    "height": ""
  },
  "logo_mobile": {
    "img": "",
    "width": "",
    "height": ""
  },
  "divider": true,
  "sticky": true,
  "desktop_menu": false,
  "custom": ""
},
"footer": {
  "active": true,
  "copyright": "Copyright © Text",
  "custom": "<div>custom</div>",
  "sort": {
    "menu": true,
    "socialmedia": true,
    "copyright": true,
    "address": true,
    "custom": false
  }
}
```

## USAGE
### HEADER BUILDER
Inside header tag
```php
echo prefix_template::HeaderContent();
```
### FOOTER BUILDER
Inside footer tag
```php
echo prefix_template::FooterContent();
```
### STICKY
Add to the body tag
```php
echo prefix_template::CheckSticky(prefix_template::$template_header_sticky);
```
### CONTAINER
First variable to set container true or false
Set second variable to true if you would like to add class attribute to the output
```php
echo prefix_template::AddContainer(prefix_template::$template_container, true);
```
### TEMPLATE PARTS
```php
SitePlaceholder();
Logo();
WP_MainMenu();
AddressBlock();
Divider();
WP_FooterMenu();
Copyright();
SocialMedia();
ContactBlock();
```
