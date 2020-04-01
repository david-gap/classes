<?
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * Author:      David Voglgsnag
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
  2.2 MANAGE BLOCKS
3.0 OUTPUT
=======================================================*/

class prefix_WPgutenberg extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static bool $WPgutenberg_AllowedBlocks: List allowed gutenberg blocks
    */
    static $WPgutenberg_AllowedBlocks = array();


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      //
      if(!empty(SELF::$WPgutenberg_AllowedBlocks)):
        add_filter( 'allowed_block_types', array($this, 'AllowGutenbergBlocks') );
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



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/


  /* 2.1 GET SETTINGS FROM CONFIGURATION FILE
  /------------------------*/
  private function updateVars(){
    // get configuration
    global $configuration;
    // if configuration file exists && class-settings
    if($configuration && array_key_exists('gutenberg', $configuration)):
      // class configuration
      $myConfig = $configuration['gutenberg'];
      // update vars
      SELF::$WPgutenberg_AllowedBlocks = array_key_exists('AllowedBlocks', $myConfig) ? $myConfig['AllowedBlocks'] : SELF::$WPgutenberg_AllowedBlocks;
    endif;
  }


  /* 2.2 MANAGE BLOCKS
  /------------------------*/
  function AllowGutenbergBlocks(){
    return SELF::$WPgutenberg_AllowedBlocks;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

}
