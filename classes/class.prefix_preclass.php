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



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
