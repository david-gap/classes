<?php
/**
 *
 *
 * WORDPRESS GUTENBERG SUPPORT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.4.1
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
  2.6 CUSTOM THEME SUPPORT
3.0 OUTPUT
  3.1 RETURN CUSTOM CSS
  3.2 CHANGE INLINE FONT SIZE
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
      * @param private int $WPgutenberg_DefaultPatterns: Remove default patterns
      * @param private int $WPgutenberg_fontsizeScaler: Activate fontsize scaler
      * @param private array $WPgutenberg_AllowedBlocks: List core allowed gutenberg blocks
      * @param private array $WPgutenberg_CustomAllowedBlocks: List custom allowed gutenberg blocks
      * @param private array $WPgutenberg_ColorPalette: Custom theme color palette
      * @param private array $WPgutenberg_FontSizes: Custom theme font sizes
      * @param private int $WPgutenberg_ColorPalette_CP: Disable custom color picker
    */
    private $WPgutenberg_active                 = 0;
    private $WPgutenberg_css                    = 0;
    private $WPgutenberg_Stylesfile             = 0;
    private $WPgutenberg_DefaultPatterns        = 0;
    private $WPgutenberg_fontsizeScaler         = 0;
    private $WPgutenberg_AllowedBlocks          = array();
    private $WPgutenberg_CustomAllowedBlocks    = array();
    private static $WPgutenberg_ColorPalette    = array();
    private static $WPgutenberg_FontSizes       = array();
    private static $WPgutenberg_ColorPalette_CP = 0;


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
      if($this->WPgutenberg_css == 0):
        add_action( 'wp_enqueue_scripts', array($this, 'DisableGutenbergCSS'), 100 );
      endif;
      // add theme support
      SELF::CustomThemeSupport();
      // register custom blocks
      add_action( 'init', array($this, 'WPgutenbergCustomBlocks') );
      // Change inline font size to var
      if($this->WPgutenberg_fontsizeScaler == 0):
        add_filter('the_content',  array($this, 'InlineFontSize') );
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
      "Patterns" => array(
        "label" => "Default Patterns",
        "type" => "switchbutton"
      ),
      "fontsizeScaler" => array(
        "label" => "Fontsize scaler",
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
      ),
      "ColorPalette_CP" => array(
        "label" => "Custom color picker",
        "type" => "switchbutton"
      ),
      "ColorPalette" => array(
        "label" => "Custom color palette",
        "type" => "array_addable",
        "value" => array(
          "key" => array(
            "label" => "Name",
            "type" => "text"
          ),
          "value" => array(
            "label" => "Color",
            "type" => "text",
            "css" => "colorpicker"
          )
        )
      ),
      "FontSizes" => array(
        "label" => "Custom font sizes",
        "type" => "array_addable",
        "value" => array(
          "key" => array(
            "label" => "Name",
            "type" => "text"
          ),
          "value" => array(
            "label" => "Size (without px)",
            "type" => "text"
          )
        )
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
      $this->WPgutenberg_DefaultPatterns = array_key_exists('Patterns', $myConfig) ? $myConfig['Patterns'] : $this->WPgutenberg_DefaultPatterns;
      $this->WPgutenberg_fontsizeScaler = array_key_exists('fontsizeScaler', $myConfig) ? $myConfig['fontsizeScaler'] : $this->WPgutenberg_fontsizeScaler;
      $this->WPgutenberg_AllowedBlocks = array_key_exists('AllowedBlocks', $myConfig) ? $myConfig['AllowedBlocks'] : $this->WPgutenberg_AllowedBlocks;
      $this->WPgutenberg_CustomAllowedBlocks = array_key_exists('CustomAllowedBlocks', $myConfig) ? $myConfig['CustomAllowedBlocks'] : $this->WPgutenberg_CustomAllowedBlocks;
      SELF::$WPgutenberg_ColorPalette = array_key_exists('ColorPalette', $myConfig) ? $myConfig['ColorPalette'] : SELF::$WPgutenberg_ColorPalette;
      SELF::$WPgutenberg_FontSizes = array_key_exists('FontSizes', $myConfig) ? $myConfig['FontSizes'] : SELF::$WPgutenberg_FontSizes;
      SELF::$WPgutenberg_ColorPalette_CP = array_key_exists('ColorPalette_CP', $myConfig) ? $myConfig['ColorPalette_CP'] : SELF::$WPgutenberg_ColorPalette_CP;
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
        ['wp-i18n', 'wp-element', 'wp-blocks']
      );
    endif;
  }


  /* 2.6 CUSTOM THEME SUPPORT
  /------------------------*/
  function CustomThemeSupport(){
    // coloring
    if(!empty(SELF::$WPgutenberg_ColorPalette)):
      $newColors = array();
      foreach (SELF::$WPgutenberg_ColorPalette as $colorkey => $color) {
        $newColors[] = array(
          'name'  => __( $color["key"], 'WPgutenberg' ),
          'slug'  => prefix_core_BaseFunctions::Slugify($color["key"]),
          'color'	=> $color["value"],
        );
      }
      add_theme_support( 'editor-color-palette', $newColors );
    endif;
    // font sizes
    if(!empty(SELF::$WPgutenberg_FontSizes)):
      $newColors = array();
      foreach (SELF::$WPgutenberg_FontSizes as $sizekey => $size) {
        $newColors[] = array(
          'name'  => __( $size["key"], 'WPgutenberg' ),
          'slug'  => prefix_core_BaseFunctions::Slugify($size["key"]),
          'size'	=> $size["value"],
        );
      }
      add_theme_support( 'editor-font-sizes', $newColors );
      // disable custom color picker
      if(SELF::$WPgutenberg_ColorPalette_CP == 0):
        add_theme_support( 'disable-custom-colors');
      endif;
    endif;
    // disable default patterns
    if($this->WPgutenberg_DefaultPatterns == 0):
      remove_theme_support( 'core-block-patterns' );
    endif;

  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

  /* 3.1 RETURN CUSTOM CSS
  /------------------------*/
  function returnCustomCSS(){
    // vars
    $output = '';
    // build styling
    if(!empty(SELF::$WPgutenberg_ColorPalette) || !empty(SELF::$WPgutenberg_FontSizes)):
      // coloring
      if(!empty(SELF::$WPgutenberg_ColorPalette)):
        foreach (SELF::$WPgutenberg_ColorPalette as $colorkey => $color) {
          $output .= ' .has-' . prefix_core_BaseFunctions::Slugify($color["key"]) . '-color {color: ' . $color["value"] . ';}';
          $output .= ' .has-' . prefix_core_BaseFunctions::Slugify($color["key"]) . '-background-color {background-color: ' . $color["value"] . ';}';
        };
      endif;
      // font sizes
      if(!empty(SELF::$WPgutenberg_FontSizes)):
        foreach (SELF::$WPgutenberg_FontSizes as $sizekey => $size) {
          $output .= ' .has-' . prefix_core_BaseFunctions::Slugify($size["key"]) . '-font-size {font-size: ' . $size["value"] . 'px;}';
        };
      endif;
    endif;
    // return
    return $output;
  }


  /* 3.2 CHANGE INLINE FONT SIZE
  /------------------------*/
  function InlineFontSize($content) {
    if(!is_admin()):
      return str_replace("font-size","--font-size",$content);
    endif;
  }


}
