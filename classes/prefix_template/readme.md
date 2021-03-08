**Version 2.13.12** (08.03.2021)

Custom class "template" with template parts and header / footer builder

## CONFIGURATION OPTIONS
* $template_container_header: activate container for the header
* $template_container: activate container for the content
* $template_container_footer: activate container for the footer
* $template_coloring: template coloring (dark/light)
* $template_ph_active: activate placeholder
* $template_ph_address: placeholder show address block
* $template_ph_custom: placeholder custom content
* $template_address: address block content
* $template_socialmedia: social media
* $template_header_divider: Activate header divider
* $template_header_sticky: activate sticky header
* $template_header_stickyload: activate sticky header on load
* $template_header_dmenu: Activate header hamburger for desktop
* $template_header_custom: Custom header html
* $template_header_sort: Sort and activate blocks inside header builder
* $template_header_logo_link: Logo link with wordpress fallback
* $template_header_logo_d: desktop logo configuration
* $template_header_logo_m: mobile logo configuration
* $template_header_after: html code after header
* $template_page_active: activate page options
* $template_page_options: show/hide template elements
* $template_page_additional: additional custom fields template elements
* $template_page_metablock: activate metablock on detail page
* $template_page_metablockAdds: Add metabox to CPT by slugs
* $template_footer_active: activate footer
* $template_footer_cr: copyright text
* $template_footer_custom: custom html
* $template_footer_sort: Sort and activate blocks inside footer builder
* $template_footer_before: html code before footer

## CONFIGURATION FILE
```
"template": {
  "container_header": 1,
  "container": 1,
  "container_footer": 1,
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
  },
  "header": {
    "sort": {
      "logo": 1,
      "container_start": 1,
      "menu": 1,
      "socialmedia": 1,
      "custom": 0,
      "container_end": 1,
      "hamburger": 1
    },
    "logo_link": "",
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
    "divider": 1,
    "sticky": 1,
    "sticky_onload": 0;
    "desktop_menu": 0,
    "custom": "",
    "after_header": ""
  },
  "page": {
    "active": 1,
    "metablock": {
      "page": 1,
      "post": 1
    }
    "options": {
      "header": 1,
      "time": 1,
      "author": 1,
      "title": 1,
      "title": 1,
      "sidebar": 1,
      "footer": 1,
      "darkmode": 1,
      "beforeMain"; 1,
      "afterMain"; 1
    },
    "additional":  {
      "0": {
        "key": "Custom var"
        "value": "Custom name"
      }
    }
  },
  "blog": {
    "type": 1
  },
  "footer": {
    "active": 1,
    "copyright": "Copyright © Text",
    "custom": "<div>custom</div>",
    "sort": {
      "container_start": 1,
      "menu": 1,
      "socialmedia": 1,
      "copyright": 1,
      "address": 1,
      "custom": 0,
      "container_end": 1
    },
    "before_footer": ""
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
echo prefix_template::CheckSticky(prefix_template::$template_header_sticky, prefix_template::$template_header_stickyload);
```

### CONTAINER
First variable to set container true or false
Set second variable to true if you would like to add class attribute to the output
```php
echo prefix_template::AddContainer(prefix_template::$template_container, true);
```

### PAGE OPTIONS
Enter page id to get backend settings
```php
echo prefix_template::PageOptions($page_id);
```

### SITE PLACEHOLDER
If the Website is under construction return the placeholder
```php
echo prefix_template::SitePlaceholder();
```

### LOGO CONTAINER
Logo container, with alternative mobile logo
```php
$deskop_logo = array(
  "img" => "your/img/logo.jpg",
  "width" => "100",
  "height" => "40"
);
$mobile_logo = array(
  "img" => "your/img/logo.jpg",
  "width" => "50",
  "height" => "20"
);
echo prefix_template::Logo("https://website-link.com", $deskop_logo, $mobile_logo);
```

### WP MAINMANU WITH HAMBURGER
Get the WP Menu mainmenu with the hamburger button
```php
echo prefix_template::WP_MainMenu();
```

### ADDRESSBLOCK
Addressblock with/without labels, call links with desktop disabler
```php
$address = array(
  'company' => 'Company name',
  'street' => 'Address',
  'street2' => 'Additional address line',
  'postalCode' => '00000',
  'city' => 'City name',
  'phone' => 'Phone number',
  'mobile' => 'Mobile number',
  'email' => 'your@mail.com',
  'labels' => array(
    'company' => '',
    'street' => '',
    'street2' => '',
    'postalCode' => '',
    'city' => '',
    'phone' => 'Phone label',
    'mobile' => 'Mobile label',
    'email' => 'E-Mail label'
  )
);
echo prefix_template::AddressBlock($address);
```

### DIVIDER
Return a hr element
```php
echo prefix_template::Divider();
```

### FOOTER MENU
Get WP Menu footermenu
```php
echo prefix_template::WP_FooterMenu();
```

### COPYRIGHT
Span element for the copyright information
```php
echo prefix_template::Copyright("my copyright text");
```

### SOCIAL MEDIA BLOCK
Social media inline icons block (supports: facebook, instagram)
```php
$sm = array(
  "facebook" => "https://facebook.com/your-slug",
  "instagram" => "https://instagram.com/your-slug"
);
echo prefix_template::SocialMedia($sm);
```

### CONTACT BLOCK
Gives contact options as inline icon links (supports: phone, mail, whatsapp)
```php
$contacts = array(
  "phone" => "000000000",
  "mail" => "your@mail.com",
  "whatsapp" => "000000000"
);
echo prefix_template::ContactBlock($contacts);
```

### ICON BLOCK
A list of given inline icons
```php
$icons = array(
  // for each svg
  array(
    "svg" => 'SVG-CONTENT',
    "link" => "https://your-link.com",
    "target" => "blank",
    "class" => "custom-css-class",
    "attr" => array(
      "data-example" => "attribute content",
      "data-example-two" => "attribute content"
    )
  )
);
$settings = array(
  "class" => "custom-css-class",
  "attr" => array(
    "data-example" => "attribute content",
    "data-example-two" => "attribute content"
  )
);
echo prefix_template::IconBlock($icons, $settings);
```

## FILTERS
To Add custom options by Template (ACF for example)
template_PageOptions

Add Custom CSS by template
template_BodyCSS
