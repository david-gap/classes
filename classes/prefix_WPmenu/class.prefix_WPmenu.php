<?php
/**
 * WORDPRESS MENU
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
  2.1 GET CONFIGURATION FORM CONFIG FILE
  2.2 REGISTER MENUS
3.0 OUTPUT
=======================================================*/


class prefix_WPmenu extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static array $WPmenu_register: list of all wanted WP menus
    */
    static $WPmenu_register    = array(
      'mainmenu' => 'Main Menu',
      'footermenu' => 'Footer Menu'
    );


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // register menu
      add_action( 'init', array( $this, 'wpseed_register_theme_menus' ) );
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



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/

    /* 2.1 GET CONFIGURATION FORM CONFIG FILE
    /------------------------*/
    private function updateVars(){
      // get configuration
      global $configuration;
      // if configuration file exists && class-settings
      if($configuration && array_key_exists('WPmenu', $configuration)):
        // class configuration
        $myConfig = $configuration['WPmenu'];
        // update vars
        SELF::$WPmenu_register = array_key_exists('menus', $myConfig) ? $myConfig['menus'] : SELF::$WPmenu_register;
      endif;
    }


    /* 2.2 REGISTER MENUS
    /------------------------*/
    // => https://codex.wordpress.org/Function_Reference/register_nav_menus
    function wpseed_register_theme_menus() {
      if(is_array(SELF::$WPmenu_register)):
        register_nav_menus(SELF::$WPmenu_register);
      endif;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
