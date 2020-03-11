<?php
/**
 * EMBED A SWISS TOPO MAP
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.0
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
2.0 FUNCTIONS
  2.1 GET SETTINGS FROM CONFIGURATION FILE
3.0 OUTPUT
=======================================================*/


class prefix_SwissTopo extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static bool $SwissTopo_WP: if wordpress is active
      * @param static bool $SwissTopo_WP_assets: load assets file for wordpress
      * @param static string $SwissTopo_version: API 3 loader version
    */
    static $SwissTopo_WP        = false;
    static $SwissTopo_WP_assets = false;
    static $SwissTopo_version   = '4.4.2';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // wordpress settings
      if(SELF::$SwissTopo_WP !== false):
        // add class assets
        add_action('wp_enqueue_scripts', array( $this, 'SwissTopo_frontend_enqueue_scripts_and_styles' ) );
      endif;
    }

    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $backend = array(
      "key" => array(
        "label" => "",
        "type" => "",
        "value" => ""
      ),
    );


    /* 1.4 ENQUEUE FRONTEND SCRIPTS/STYLES
    /------------------------*/
    function SwissTopo_frontend_enqueue_scripts_and_styles() {
      $class_path = get_stylesheet_directory_uri() . '/config/classes/prefix_SwissTopo/';
      // scripts
      if(SELF::$SwissTopo_WP_assets !== false):
        wp_register_script('frontend/SwissTopo', $class_path . 'swisstopo.js', false, null, true);
        wp_enqueue_script('frontend/SwissTopo');
      endif;
      # api 3 geo admin
      if(SELF::$SwissTopo_version !== ''):
        wp_register_script('swisstopo', 'https://api3.geo.admin.ch/loader.js?version=' . SELF::$SwissTopo_version, false, null, false);
        wp_enqueue_script('swisstopo');
      endif;
    }



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/


  /* 2.1 GET SETTINGS FROM CONFIGURATION FILE
  /------------------------*/
  private function updateVars(){
    // get configuration
    global $configuration;
    // if configuration file exists && class-settings
    if($configuration && array_key_exists('swissTopo', $configuration)):
      // class configuration
      $myConfig = $configuration['swissTopo'];
      // update vars
      SELF::$SwissTopo_WP = array_key_exists('wp', $myConfig) ? $myConfig['wp'] : SELF::$SwissTopo_WP;
      SELF::$SwissTopo_WP_assets = array_key_exists('assets', $myConfig) ? $myConfig['assets'] : SELF::$SwissTopo_WP_assets;
      SELF::$SwissTopo_version = array_key_exists('version', $myConfig) ? $myConfig['version'] : SELF::$SwissTopo_version;
    endif;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/


    /* 3.1 SINGLE OUTPUT
    /------------------------*/
    /**
      * @param array $coordinates: marker coordinates
      * @param string $content: custom content
      * @param string $marker_settings: additional data-attributes
      * @return string single marker output
    */
    public static function topoMapsSingleMarker($marker_key, array $coordinates = array(), string $marker_content = "", array $marker_settings = array(NULL)){
      // vars
      $output = '';
      $adds = '';
      // marker settings
      if(!empty($marker_settings)):
        foreach ($marker_settings as $key => $value) {
          $adds .= ' ' . $key . '="' . $value . '"';
        }
      endif;
      if(!empty($coordinates)):
        // output
        $output .= '<div class="marker" data-count="' . $marker_key . '" data-lat="' . $coordinates[0] . '" data-lng="' . $coordinates[1] . '"' . $adds . '>';
          $output .= $marker_content !== "" ? $marker_content : '';
        $output .= '</div>';
        return $output;
      else:
        $debug_errors['SwissTopo'][] = "coordinates are missing";
      endif;
    }


    /* 3.2 MAP OUTPUT
    /------------------------*/
    /**
      * @param int $id: WP Marker ID
      * @param string $content: custom content
      * @param array $coordinates: marker coordinates
      * @return string single marker output
    */
    public static function topoMaps(array $settings = array(), array $markers = array()){
      // vars
      $output = '';
      $setting = '';
      if(!empty($settings)):
        foreach ($settings as $key => $value) {
          $setting .= $key . '="' . $value . '"';
        }
      endif;
      // output
      $output .= '<div class="swiss-topo"' . $setting . '>';
        // map markers
        if(!empty($markers)):
          $output .= '<div class="swisstopo-markers">';
            foreach ($markers as $key => $value) {
              $coord = array_key_exists(0, $value) ? $value[0] : array();
              $content = array_key_exists(1, $value) ? $value[1] : "";
              $attr = array_key_exists(2, $value) ? $value[2] : array();
              $output .= SELF::topoMapsSingleMarker($key, $coord, $content, $attr);
            }
          $output .= '</div>';
        endif;
        // map container
        $output .= '<div class="swisstopo-map" id="' . PARENT::ShortID() . '">';
        $output .= '</div>';
        // map pop up
        $output .= '<div id="swisstopo-popup" class="ol-popup">';
          $output .= '<a href="#" id="swisstopo-popup-closer" class="ol-popup-closer"></a>';
          $output .= '<div id="swisstopo-popup-content"></div>';
        $output .= '</div>';
      $output .= '</div>';

      return $output;
    }
}
