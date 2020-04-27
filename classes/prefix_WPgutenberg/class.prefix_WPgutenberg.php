<?
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.1
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
  2.3 ADD STYLES OPTIONS
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
      * @param static array $WPgutenberg_AllowedBlocks: List allowed gutenberg blocks
      * @param static string $WPgutenberg_Stylesfile: path template styling options
    */
    static $WPgutenberg_AllowedBlocks = array();
    static $WPgutenberg_Stylesfile = 'config/classes/prefix_WPgutenberg/gutenberg-editor.js';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // filter gutenberg blocks
      if(!empty(SELF::$WPgutenberg_AllowedBlocks)):
        add_filter( 'allowed_block_types', array($this, 'AllowGutenbergBlocks') );
      endif;
      // add gutenberg style options
      if(!empty(SELF::$WPgutenberg_Stylesfile)):
        add_action( 'enqueue_block_editor_assets', array($this, 'AddBackendStyleOptions') );
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
      SELF::$WPgutenberg_Stylesfile = array_key_exists('Stylesfile', $myConfig) ? $myConfig['Stylesfile'] : SELF::$WPgutenberg_Stylesfile;
    endif;
  }


  /* 2.2 MANAGE BLOCKS
  /------------------------*/
  function AllowGutenbergBlocks(){
    return SELF::$WPgutenberg_AllowedBlocks;
  }


  /* 2.3 ADD STYLES OPTIONS
  /------------------------*/
  function AddBackendStyleOptions(){
      wp_register_script(
        'backend-gutenberg-css-classes',
        get_stylesheet_directory_uri() . '/' . SELF::$WPgutenberg_Stylesfile,
        array( 'wp-blocks' ),
        null,
        true
      );
      wp_enqueue_script( 'backend-gutenberg-css-classes' );
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

}
