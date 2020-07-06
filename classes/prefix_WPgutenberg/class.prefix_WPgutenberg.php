<?php
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.0
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
      * @param protected bool $WPgutenberg_active: disable gutenberg
      * @param protected bool $WPgutenberg_css: disable gutenberg styling
      * @param protected array $WPgutenberg_AllowedBlocks: List allowed gutenberg blocks
      * @param protected string $WPgutenberg_Stylesfile: path template styling options
    */
    protected $WPgutenberg_active        = true;
    protected $WPgutenberg_css           = true;
    protected $WPgutenberg_AllowedBlocks = array();
    protected $WPgutenberg_Stylesfile    = 'config/classes/prefix_WPgutenberg/gutenberg-editor.js';


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
        add_action( 'enqueue_block_editor_assets', array($this, 'AddBackendStyleOptions') );
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
  protected function DisableGutenberg(){
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
      wp_register_script(
        'backend-gutenberg-css-classes',
        get_stylesheet_directory() . '/' . $this->WPgutenberg_Stylesfile,
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
