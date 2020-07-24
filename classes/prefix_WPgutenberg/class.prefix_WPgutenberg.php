<?php
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.1.1
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
      * @param private int $WPgutenberg_active: disable gutenberg
      * @param private int $WPgutenberg_css: disable gutenberg styling
      * @param private int $WPgutenberg_Stylesfile: Add the file with the additional gutenberg css classes
      * @param private array $WPgutenberg_AllowedBlocks: List core allowed gutenberg blocks
      * @param private array $WPgutenberg_CustomAllowedBlocks: List custom allowed gutenberg blocks
    */
    private $WPgutenberg_active              = 0;
    private $WPgutenberg_css                 = 0;
    private $WPgutenberg_Stylesfile          = 0;
    private $WPgutenberg_AllowedBlocks       = array();
    private $WPgutenberg_CustomAllowedBlocks = array();


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
      if($this->WPgutenberg_active == 0):
        SELF::DisableGutenberg();
      endif;
      // disable Gutenberg block styles
      if ($this->WPgutenberg_css == 0) :
        add_action( 'wp_enqueue_scripts', array($this, 'DisableGutenbergCSS'), 100 );
      endif;
    }

    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $classtitle = 'Gutenberg';
    static $classkey = 'gutenberg';
    static $backend = array(
      "active" => array(
        "label" => "Active",
        "type" => "switchbutton"
      ),
      "css" => array(
        "label" => "Gutenberg styles embed",
        "type" => "switchbutton"
      ),
      "Stylesfile" => array(
        "label" => "Backend style options",
        "type" => "switchbutton"
      ),
      "AllowedBlocks" => array(
        "label" => "Allowed core blocks",
        "css" => "multiple",
        "type" => "select",
        "value" => array(
          "core/paragraph",
          "core/image",
          "core/heading",
          "core/gallery",
          "core/list",
          "core/quote",
          "core/audio",
          "core/file",
          "core/video",
          "core/table",
          "core/verse",
          "core/code",
          "core/freeform",
          "core/html",
          "core/preformatted",
          "core/pullquote",
          "core/button",
          "core/columns,",
          "core/media-text",
          "core/more",
          "core/nextpage",
          "core/separator",
          "core/spacer",
          "core/shortcode",
          "core/archives",
          "core/categories",
          "core/latest-comments",
          "core/latest-posts",
          "core/calendar",
          "core/rss",
          "core/search",
          "core/tag-cloud",
          "core/embed",
          "core-embed/twitter",
          "core-embed/youtube",
          "core-embed/facebook",
          "core-embed/instagram",
          "core-embed/wordpress",
          "core-embed/soundcloud",
          "core-embed/spotify",
          "core-embed/flickr",
          "core-embed/vimeo",
          "core-embed/animoto",
          "core-embed/cloudup",
          "core-embed/collegehumor",
          "core-embed/dailymotion",
          "core-embed/funnyordie",
          "core-embed/hulu",
          "core-embed/imgur",
          "core-embed/issuu",
          "core-embed/kickstarter",
          "core-embed/meetup-com",
          "core-embed/mixcloud",
          "core-embed/photobucket",
          "core-embed/polldaddy",
          "core-embed/reddit",
          "core-embed/reverbnation",
          "core-embed/screencast",
          "core-embed/scribd",
          "core-embed/slideshare",
          "core-embed/smugmug",
          "core-embed/speaker",
          "core-embed/ted",
          "core-embed/tumblr",
          "core-embed/videopress",
          "core-embed/wordpress-tv"
        )
      ),
      "CustomAllowedBlocks" => array(
        "label" => "Allowed costum blocks",
        "type" => "array_addable"
      )
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
      $this->WPgutenberg_Stylesfile = array_key_exists('Stylesfile', $myConfig) ? $myConfig['Stylesfile'] : $this->WPgutenberg_Stylesfile;
      $this->WPgutenberg_AllowedBlocks = array_key_exists('AllowedBlocks', $myConfig) ? $myConfig['AllowedBlocks'] : $this->WPgutenberg_AllowedBlocks;
      $this->WPgutenberg_CustomAllowedBlocks = array_key_exists('CustomAllowedBlocks', $myConfig) ? $myConfig['CustomAllowedBlocks'] : $this->WPgutenberg_CustomAllowedBlocks;
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
    $AllowedBlocks = array_merge($this->WPgutenberg_AllowedBlocks, $this->WPgutenberg_CustomAllowedBlocks);
    return $AllowedBlocks;
  }


  /* 2.5 ADD STYLES OPTIONS
  /------------------------*/
  function AddBackendStyleOptions(){
    $path = get_stylesheet_directory_uri() . '/gutenberg-editor.js';
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
