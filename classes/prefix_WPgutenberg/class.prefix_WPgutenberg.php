<?php
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.0.1
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
  2.2 DISABLE GUTENBERG
  2.3 DISABLE GUTENBERG STYLING
  2.4 MANAGE BLOCKS
  2.5 ADD STYLES OPTIONS
3.0 OUTPUT
=======================================================*/

class prefix_WPgutenberg {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param private bool $WPgutenberg_active: disable gutenberg
      * @param private bool $WPgutenberg_css: disable gutenberg styling
      * @param private array $WPgutenberg_AllowedBlocks: List allowed gutenberg blocks
      * @param private string $WPgutenberg_Stylesfile: path template styling options
    */
    private $WPgutenberg_active        = true;
    private $WPgutenberg_css           = true;
    private $WPgutenberg_AllowedBlocks = array();
    private $WPgutenberg_Stylesfile    = 'config/classes/prefix_WPgutenberg/gutenberg-editor.js';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // filter gutenberg blocks
      if(!empty($this->WPgutenberg_AllowedBlocks)):
        add_filter( 'allowed_block_types', array($this, 'AllowGutenbergBlocks'), 100 );
      endif;
      // add gutenberg style options
      if(!empty($this->WPgutenberg_Stylesfile)):
        add_action( 'enqueue_block_editor_assets', array($this, 'AddBackendStyleOptions'), 100 );
      endif;
      // disable gutenberg
      if($this->WPgutenberg_active !== true):
        SELF::DisableGutenberg();
      endif;
      // disable Gutenberg block styles
      if ($this->WPgutenberg_css !== true) :
        add_action( 'wp_enqueue_scripts', array($this, 'DisableGutenbergCSS'), 100 );
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
      $this->WPgutenberg_active = array_key_exists('active', $myConfig) ? $myConfig['active'] : $this->WPgutenberg_active;
      $this->WPgutenberg_css = array_key_exists('css', $myConfig) ? $myConfig['css'] : $this->WPgutenberg_css;
      $this->WPgutenberg_AllowedBlocks = array_key_exists('AllowedBlocks', $myConfig) ? $myConfig['AllowedBlocks'] : $this->WPgutenberg_AllowedBlocks;
      $this->WPgutenberg_Stylesfile = array_key_exists('Stylesfile', $myConfig) ? $myConfig['Stylesfile'] : $this->WPgutenberg_Stylesfile;
    endif;
  }


  /* 2.2 DISABLE GUTENBERG
  /------------------------*/
  private function DisableGutenberg(){
    // disable for posts
    add_filter('use_block_editor_for_post', '__return_false', 10);
    // disable for post types
    add_filter('use_block_editor_for_post_type', '__return_false', 10);
  }


  /* 2.3 DISABLE GUTENBERG STYLING
  /------------------------*/
  function DisableGutenbergCSS(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' );
  }


  /* 2.4 MANAGE BLOCKS
  /------------------------*/
  function AllowGutenbergBlocks(){
    return $this->WPgutenberg_AllowedBlocks;
  }


  /* 2.5 ADD STYLES OPTIONS
  /------------------------*/
  function AddBackendStyleOptions(){
    $path = get_stylesheet_directory_uri() . '/' . $this->WPgutenberg_Stylesfile;
    if(prefix_core_BaseFunctions::CheckFileExistence($path)):
      wp_enqueue_script(
        'backend-gutenberg-css-classes',
        $path,
        array( 'wp-blocks' )
      );
    endif;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

}
