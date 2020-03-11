/**
 * javascript/jQuery functions for google Maps
 *
 * @author      David Voglgsang
 * @version     1.0
 *
 * https://api3.geo.admin.ch/api/doc.html
*/

/*=======================================================
Table of Contents:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––
  1.0 CONFIGURATION
  2.0 FUNCTIONS
    2.1 BUILD NEW MAP
    2.2 SWISS COORDINATES
    2.3 LOOK FOR MAP CONTAINERS
  3.0 3.0 ACTIONS
    3.1 BUILD MAP's ON LOAD
=======================================================*/


/*==================================================================================
  1.0 CONFIGURATION
==================================================================================*/

  /* Global Settings
  /––––––––––––––––––––––––*/
  //
  // SVG icons
  var gmapCircle = function (color, stroke_width, stroke_color) {
    var encoded = window.btoa('<svg width="10" height="10" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-50 -50 100 100"><g fill="' + color + '" stroke="' + stroke_color + '" stroke-width="' + stroke_width + '"><circle r="50"/><use xlink:href="#a"/></g></svg>');
    return ('data:image/svg+xml;base64,' + encoded);
  };
  var customSVG = function (type) {
    if(type == "CUSTOM SVG KEY"){
      var svg = '<svg width="10" height="10">CUSTOM SVG CONTENT</svg>';
    }
    var encoded = window.btoa(svg);
    return ('data:image/svg+xml;base64,' + encoded);
  };



/*==================================================================================
  2.0 FUNCTIONS
==================================================================================*/

  /* 2.1 BUILD NEW MAP
  /------------------------*/
  function new_STmap($container){
    // DEFAULT SETTINGS
    // map
    var map = null;
    var resolution = 450;
    var zoom = 9;
    // clustering
    var cluster_distance = "40";
    var cluster_radius = "15";
    var cluster_bg = '#FFF';
    var cluster_stroke = '#000';
    var cluster_color = '#000';
    var cluster_x = 0;
    var cluster_y = 0;
    // icons
    var icon_size = "2";
    var icon_color = "#FFF";
    var icon_stroke_width = "0";
    var icon_stroke_color = "#FFF";
    var svg_content = gmapCircle(icon_color, icon_stroke_width, icon_stroke_color);

    // UPDATE SETTINGS BY DATA ATTRIBUTES
    // get map container ID
    var map_container = $container.find('.swisstopo-map').attr('id');
    // map resolution
    if($container.attr('data-resolution')){
      var resolution = $container.attr('data-resolution');
    }
    // map zoom
    if($container.attr('data-zoom')){
      var zoom = $container.attr('data-zoom');
    }
    // clustering distance
    if($container.attr('data-cluster-distace')){
      var cluster_distance = $container.attr('data-cluster-distace');
    }
    // clustering radius
    if($container.attr('data-cluster-radius')){
      var cluster_radius = $container.attr('data-cluster-radius');
    }
    // clustering position X
    if($container.attr('data-cluster-x')){
      var cluster_x = $container.attr('data-cluster-x');
    }
    // clustering position Y
    if($container.attr('data-cluster-y')){
      var cluster_y = $container.attr('data-cluster-y');
    }
    // clustering stroke
    if($container.attr('data-cluster-stroke')){
      var cluster_stroke = $container.attr('data-cluster-stroke');
    }
    // clustering fill background color
    if($container.attr('data-cluster-bg')){
      var cluster_bg = $container.attr('data-cluster-bg');
    }
    // clustering text color
    if($container.attr('data-cluster-color')){
      var cluster_color = $container.attr('data-cluster-color');
    }


    /* CREATE ARRAY FOR MARKER
    /------------------------*/
    /*
    hidden div .swisstopo-markers is the markers container
    Each marker has his own div with the css class marker
    Needed attributes:
    data-count to identify marker inside the markers array (features)
    data-lat & data-lng for coordinates
    */
    var $markers = $container.find('.swisstopo-markers .marker').length;
    var features = new Array($markers);
    var e = 4500000;
    for (var i = 0; i < $markers; ++i) {
      // get coordinates from marker
      var lat = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-lat');
      var lng = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-lng');
      var marker = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-marker');
      var content = $(".swisstopo-markers .marker[data-count='" + i + "']").html();
      var marker_color = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-marker-color');
      var marker_size = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-marker-size');
      var marker_stroke_width = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-marker-stroke-width');
      var marker_stroke_color = $(".swisstopo-markers .marker[data-count='" + i + "']").attr('data-marker-stroke-color');
      // DEBUG Vars: console.log(i + ": " + lat + " - " + lng);
      // coordinates math
      if($container.attr('data-coordinates') == "WGS"){
        var swissCord = Swisstopo.WGStoCH(lat, lng, true);
      } else if($container.attr('data-coordinates') == "CH1903"){
        var swissCord = Swisstopo.WGStoCH(lat, lng);
      } else {
        var swissCord = [lat, lng];
      }
      // pointer position
      var coordinates = swissCord;
      // DEBUG coordinates: console.log(coordinates);
      // set layer
      features[i] = new ol.Feature({
        element: content,
        marker: marker,
        marker_color: marker_color,
        marker_size: marker_size,
        marker_stroke_width: marker_stroke_width,
        marker_stroke_color: marker_stroke_color,
        geometry: new ol.geom.Point(coordinates)
      });
    }


    /* Layers
    /------------------------*/
    // MARKER LAYER
    var source = new ol.source.Vector({
      features: features
    });

    // CLUSTER MARKER
    var clusterSource = new ol.source.Cluster({
      distance: cluster_distance,
      source: source
    });

    // RASTER SETTINGS (BG)
    var raster = new ol.layer.Tile({
      source: new ol.source.OSM()
    });

    // SWISS TOPO
    var swisstopo = ga.layer.create('ch.swisstopo.pixelkarte-farbe');


    /* Clustering
    /------------------------*/
    // CLUSTER SETTINGS
    var styleCache = {};
    var clusters = new ol.layer.Vector({
      source: clusterSource,
      style: function(feature) {
        var size = feature.get('features').length;
        var style = styleCache[size];
        // console.log(style);
        if (!style) {
          if(size > 1){
            style = [new ol.style.Style({
              image: new ol.style.Circle({
                radius: cluster_radius,
                stroke: new ol.style.Stroke({
                  color: cluster_stroke
                }),
                fill: new ol.style.Fill({
                  color: cluster_bg
                })
              }),
              text: new ol.style.Text({
                text: size.toString(),
                offsetX: cluster_x,
                offsetY: cluster_y,
                fill: new ol.style.Fill({
                  color: cluster_color
                })
              })
            })];
          } else {
            // update marker settings
            if (feature['D']['features']['0']['D']['marker_size']) {
              var icon_size = feature['D']['features']['0']['D']['marker_size'] / 10;
            }
            if(feature['D']['features']['0']['D']['marker_color']){
              icon_color = feature['D']['features']['0']['D']['marker_color'];
            }
            if(feature['D']['features']['0']['D']['marker_stroke_width']){
              icon_stroke_width = feature['D']['features']['0']['D']['marker_stroke_width'];
            }
            if(feature['D']['features']['0']['D']['marker_stroke_color']){
              icon_stroke_color = feature['D']['features']['0']['D']['marker_stroke_color'];
            }
            // icon type
            if (feature['D']['features']['0']['D']['marker']) {
              svg_content = customSVG(feature['D']['features']['0']['D']['marker']);
            } else {
              svg_content = gmapCircle(icon_color, icon_stroke_width, icon_stroke_color);
            }
            // single marker
            style = [new ol.style.Style({
              image: new ol.style.Icon({
                radius: 20,
                anchor: [0.5, 0.5],
                anchorXUnits: 'fraction',
                anchorYUnits: 'fraction',
                src: svg_content,
                scale: icon_size
              })
            })];
          }
        }
        return style;
      }
    });


    /* MAP
    /------------------------*/
    // MAP SETTINGS
    var map = new ga.Map({
      view: new ol.View({
        // Define a coordinate CH1903+ (EPSG:2056) for the center of the view
        center: [2660000, 1190000],
        resolution: resolution,
        zoom: zoom
      }),
      layers: [
        swisstopo,
        clusters
      ],
      target: map_container
    });


    /* POP UP
    /------------------------*/
    // popup elements
    var container = document.getElementById('swisstopo-popup');
    var content = document.getElementById('swisstopo-popup-content');
    var closer = document.getElementById('swisstopo-popup-closer');
    /**
     * Create an overlay to anchor the popup to the map.
    */
    var popup = new ol.Overlay({
      element: container,
      autoPan: true,
      autoPanAnimation: {
        duration: 200
      }
    });
    map.addOverlay(popup);
    /**
     * Add a click handler to hide the popup.
     * @return {boolean} Don't follow the href.
     */
    closer.onclick = function() {
      popup.setPosition(undefined);
      closer.blur();
      return false;
    };


    /* USER ACTIONS
    /------------------------*/
    // Change cursor style when cursor is hover a feature
    map.on('pointermove', function(evt) {
      var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
        return feature;
      });
      map.getTargetElement().style.cursor = feature ? 'pointer' : '';
    });

    // On click we display the feature informations
    map.on('singleclick', function(evt) {
      var pixel = evt.pixel;
      var coordinate = evt.coordinate;
      var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
        // return feature;
        return feature;
      });
      // DEBUG count features: console.log(feature.get('features').length);
      var element = $(popup.getElement());
      if (feature) {
        if(feature.get('features').length > 1){
          // CLUSTER MARKER
          map.getView().setZoom(map.getView().getZoom()+1);
          var point = feature.getGeometry();
          map.getView().setCenter([point['B']['0'], point['B']['1']]);
        } else {
          // SINGLE MARKER
          // reset popup content
          $(content).empty();
          popup.setPosition(coordinate);
          // new content
          $(content).html(feature['D']['features']['0']['D']['element']);
        }
        // console.log(feature);
      } else {
        $( closer ).trigger( "click" );
      }
    });
  }


  /* 2.2 SWISS COORDINATES
  /------------------------*/
  /*
  EXAMPLE: var swissCord = Swisstopo.WGStoCH($(this).attr('data-lat'), $(this).attr('data-lng'), true);
  WGS to CH: lat, lng, true/false (if CH1903+ is active)
  CH to WGS: y, x, true/false (if CH1903+ is active)
  */
  var Swisstopo = {
    WGStoCH: function (lat, lng, plus) {
      return [
        this.WGStoCHy(lat, lng, plus),
        this.WGStoCHx(lat, lng, plus)
      ]
    },

    // Convert WGS lat/lng (� dec) to CH x
    WGStoCHx: function (lat, lng, plus) {
      // Convert decimal degrees to sexagesimal seconds
      lat = this.DECtoSEX(lat);
      lng = this.DECtoSEX(lng);
      // Auxiliary values (% Bern)
      var lat_aux = (lat - 169028.66)/10000;
      var lng_aux = (lng - 26782.5)/10000;
      // Process X
      x = 200147.07 +
      308807.95 * lat_aux  +
      3745.25 * Math.pow(lng_aux,2) +
      76.63 * Math.pow(lat_aux,2) -
      194.56 * Math.pow(lng_aux,2) * lat_aux +
      119.79 * Math.pow(lat_aux,3);
      // Process X plus
      x_plus = parseFloat( x ) + 1000000;
      // output
      if(plus == true){
        return x_plus;
      } else {
        return x;
      }
    },

    // Convert WGS lat/lng (� dec) to CH y
    WGStoCHy: function (lat, lng, plus) {
      // Convert decimal degrees to sexagesimal seconds
      lat = this.DECtoSEX(lat);
      lng = this.DECtoSEX(lng);
      // Auxiliary values (% Bern)
      var lat_aux = (lat - 169028.66)/10000;
      var lng_aux = (lng - 26782.5)/10000;
      // Process Y
      y = 600072.37 +
      211455.93 * lng_aux -
      10938.51 * lng_aux * lat_aux -
      0.36 * lng_aux * Math.pow(lat_aux,2) -
      44.54 * Math.pow(lng_aux,3);
      // Process Y plus
      y_plus = parseFloat( y ) + 2000000;
      // output
      if(plus == true){
        return y_plus;
      } else {
        return y;
      }
    },

    CHtoWGS: function (y, x, plus) {
      return [
        this.CHtoWGSlng(y, x, plus),
        this.CHtoWGSlat(y, x, plus)
      ]
    },

    // Convert CH y/x to WGS lat
    CHtoWGSlat: function (y, x, plus) {
      // CH1903+ to CH1903
      if(plus == true){
        var x = x.substr(1);
        var y = y.substr(1);
      }
      // Converts military to civil and to unit = 1000km
      // Auxiliary values (% Bern)
      var y_aux = (y - 600000)/1000000;
      var x_aux = (x - 200000)/1000000;
      // Process lat
      var lat = 16.9023892 +
      3.238272 * x_aux -
      0.270978 * Math.pow(y_aux, 2) -
      0.002528 * Math.pow(x_aux, 2) -
      0.0447   * Math.pow(y_aux, 2) * x_aux -
      0.0140   * Math.pow(x_aux, 3);
      // Unit 10000" to 1 " and converts seconds to degrees (dec)
      lat = lat * 100 / 36;
      // output
      return lat;
    },

    // Convert CH y/x to WGS lng
    CHtoWGSlng: function (y, x, plus) {
      // CH1903+ to CH1903
      if(plus == true){
        var x = x.substr(1);
        var y = y.substr(1);
      }
      // Converts military to civil and  to unit = 1000km
      // Auxiliary values (% Bern)
      var y_aux = (y - 600000)/1000000;
      var x_aux = (x - 200000)/1000000;
      // Process lng
      var lng = 2.6779094 +
      4.728982 * y_aux +
      0.791484 * y_aux * x_aux +
      0.1306   * y_aux * Math.pow(x_aux, 2) -
      0.0436   * Math.pow(y_aux, 3);
      // Unit 10000" to 1 " and converts seconds to degrees (dec)
      lng = lng * 100 / 36;
      // output
      return lng;
    },

    // Convert angle in decimal degrees to sexagesimal seconds
    DECtoSEX: function (angle) {
      // Extract DMS
      var deg = parseInt(angle);
      var min = parseInt((angle-deg)*60);
      var sec = (((angle-deg)*60)-min)*60;
      // Result sexagesimal seconds
      return sec + min*60.0 + deg*3600.0;
    }
  }


  /* 2.3 LOOK FOR MAP CONTAINERS
  /------------------------*/
  function create_STmaps(){
    $('.swiss-topo').each(function(){
      map = new_STmap( $(this) );
    });
  };



/*==================================================================================
  3.0 ACTIONS
==================================================================================*/

  $(document).ready(function() {

    /* 3.1 BUILD MAP's ON LOAD
    /––––––––––––––––––––––––*/
    create_STmaps();

  });
