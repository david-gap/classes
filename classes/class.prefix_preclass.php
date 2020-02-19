<?php
/**
 * TEMPLATE FOR FUTURE CLASSES
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     0.1
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


class prefix_preclass extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static bool $DEMO: demo variable
    */
    static $DEMO = true;


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
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


  /* 2.1 GET SETTINGS FROM CONFIGURATION FILE
  /------------------------*/
  private function updateVars(){
    // get configuration
    global $configuration;
    // if configuration file exists && class-settings
    if($configuration && array_key_exists('prefix_preclass', $configuration)):
      // class configuration
      $myConfig = $configuration['prefix_preclass'];
      // update vars
      SELF::$DEMO = array_key_exists('DEMO', $myConfig) ? $myConfig['DEMO'] : SELF::$DEMO;
    endif;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
