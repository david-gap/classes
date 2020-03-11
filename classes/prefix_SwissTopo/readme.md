**Version 1.0.1** (24.02.2020)

Custom class "prefix_SwissTopo" for embeding swiss topo.

## CONFIGURATION OPTIONS
* $SwissTopo_WP: if wordpress is active
* $SwissTopo_WP_assets: load assets file for wordpress
* $SwissTopo_version: API 3 loader version

## CONFIGURATION FILE
```
"swissTopo": {
  "wp": true,
  "assets": true,
  "version": "4.4.2"
}
```

## USAGE
Add this Folder to the config folder and enbed js and scss file to the development area.

### PHP Class call
Create 2 arrays. One with the map configuration and one multidimensional array for the markers.
```php
$settings = array(
  "data-cluster-distace" => '',
  "data-cluster-radius" => '',
  "data-cluster-x" => '',
  "data-cluster-y" => '',
  "data-cluster-stroke" => '',
  "data-cluster-bg" => '',
  "data-cluster-color" => ''
);
$markers = array(
  array(
    "0" => array("coordinates"),
    "1" => "window content",
    "2" => array(
      "data-marker" => '',
      "data-marker-color" => '',
      "data-marker-size" => '',
      "data-marker-stroke-width" => '',
      "data-marker-stroke-color" => ''
    )
  )
);

prefix_SwissTopo::topoMaps();
```
