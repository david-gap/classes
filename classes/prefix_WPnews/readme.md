**Version 1.0** (23.02.2020)

Custom class "WPnews"

## CONFIGURATION OPTIONS
* $WPnews_cpt_label: CPT Label
* $WPnews_cpt_rewrite: CPT rewrite
* $WPnews_cpt_icon: CPT backend icon
* $WPnews_cpt_support: CPT support
* $WPnews_block_label: Label for block
* $WPnews_block_max: max entries on block list
* $WPnews_detailpage: enable detail page for cpt

## CONFIGURATION FILE
```
"WPnews": {
  "label": "News",
  "rewrite": "news",
  "icon": "dashicons-testimonial",
  "support": array(
    "0": "title",
    "1": "editor",
    "2": "thumbnail"
  ),
  "block_label": "News",
  "block_max": -1,
  "detailpage": true
}
```

## USAGE
### Wordpress Shortcode
List all published news. You can filter the news by the id attribute.
```
[newsblock]
[newsblock id="12, 3, 88"]
```
