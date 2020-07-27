**Version 2.1** (27.07.2020)

Custom class "WPgalleries"

## CONFIGURATION OPTIONS
* $WPgalleries_slug: post type slug
* $WPgalleries_cpt: Activate galleries CPT
* $WPgalleries_cpt_label: CPT Label
* $WPgalleries_cpt_rewrite: CPT rewrite
* $WPgalleries_cpt_icon: CPT backend icon
* $WPgalleries_support: CPT support
* $WPgalleries_cpt_tax: CPT taxonomies
* $WPgalleries_block_max: max entries on block list
* $WPgalleries_detailpage: enable detail page for cpt
* $WPgalleries_assets: enable js/css register
* $WPgalleries_assets_popup: enable js/css register for img popup

## CONFIGURATION FILE
```
"WPgalleries": {
  "cpt": 1,
  "label": "Galleries",
  "rewrite": "galleries",
  "icon": "dashicons-calendar-alt",
  "taxanomies": {
    "equipments": {
      "label": "Equipments",
      "hierarchical": true,
      "query_var": true
    }
  },
  "detailpage": 0,
  "assets": 1,
  "assets_popup": 1,
  "others": {
    "0": "other cpt slug"
  }
}
```

## USAGE
### Wordpress Shortcode
Show a gallery by inserting the gallery cpt ID, or select the images directly
```
Shows gallery with ID 12
[galleries id="12"]

Shows images with ID 12, 3 and 88
[galleries id="12, 3, 88"]
```

Possible layout option (grid/swiper/fullscreen)
```
Shows layout type fullscreen
[galleries layout="fullscreen"]
```

For a custom css class for the container add css attribute
```
[galleries css="my_css"]
```

Define Swiper columns desktop step attribute and mobile mstep.
Defaults are 4/2
```
[galleries step="4" mstep="2"]
```

Activate popup
```
[galleries popup="true"]
```
