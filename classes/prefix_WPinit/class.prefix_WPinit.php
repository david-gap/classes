<?
/**
 *
 *
 * Wordpress - init configuration
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.0.1
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
  2.1 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
  2.2 HEADER CLEANUP
  2.3 RESET INLINE IMAGE DIMENSIONS (FOR CSS-SCALING OF IMAGES)
  2.4 ENQUEUE SCRIPTS/STYLES
  2.5 EMBED GOOGLE FONTS
  2.6 DISABLE GUTENBERG
  2.7 HIDE CORE-UPDATES FOR NON-ADMINS
  2.8 THEME SUPPORT
3.0 OUTPUT
=======================================================*/


class prefix_WPinit extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static bool $WPinit_gutenberg: disable gutenberg
      * @param static bool $WPinit_gutenberg_css: disable gutenberg styling
      * @param static array $WPinit_support: select theme support
      * @param static array $WPinit_google_fonts: google fonts
      * @param static bool $WPinit_css: activate theme styling
      * @param static string $WPinit_css_path: theme styling path (theme is root)
      * @param static bool $WPinit_js: activate theme js
      * @param static string $WPinit_js_path: theme js path (theme is root)
      * @param static bool $WPinit_jquery: activate jquery
    */
    static $WPinit_gutenberg        = true;
    static $WPinit_gutenberg_css    = true;
    static $WPinit_support          = array("title-tag", "menus", "html5", "post-thumbnails");
    static $WPinit_google_fonts     = array("Roboto");
    static $WPinit_css              = true;
    static $WPinit_css_path         = "/dist/style.min.css";
    static $WPinit_js               = true;
    static $WPinit_js_path          = "/dist/script.min.js";
    static $WPinit_jquery = true;


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // clean wp head
      add_action('wp_head', array( $this, 'WPinit_headcleanup' ), 1);
      // clean WP head
      add_action( 'admin_head', array( $this, 'wp_onlyadmin_update' ), 1 );
      // template translation files
      load_theme_textdomain('DMili', get_template_directory() . '/languages');
      // disable gutenberg
      SELF::DisableGutenberg();
      // thumbnail dimensions
      add_filter( 'post_thumbnail_html', array( $this, 'wp_remove_thumbnail_dimensions' ), 10, 3);
      // frontend css/js files
      add_action('wp_enqueue_scripts', array( $this, 'WPinit_enqueue' ));
      // add theme support
      SELF::theme_support();
      // add google fonts
      add_action( 'wp_footer', array( $this, 'GoogleFonts' ) );
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

    /* 2.1 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
    /------------------------*/
    private function updateVars(){
      // get configuration
      global $configuration;
      // if configuration file exists && class-settings
      if($configuration && array_key_exists('wp', $configuration)):
        // class configuration
        $myConfig = $configuration['wp'];
        // update vars
        SELF::$WPinit_gutenberg = array_key_exists('gutenberg', $myConfig) ? $myConfig['gutenberg'] : SELF::$WPinit_gutenberg;
        SELF::$WPinit_gutenberg_css = array_key_exists('gutenberg_css', $myConfig) ? $myConfig['gutenberg_css'] : SELF::$WPinit_gutenberg_css;
        SELF::$WPinit_support = array_key_exists('support', $myConfig) ? $myConfig['support'] : SELF::$WPinit_support;
        SELF::$WPinit_google_fonts = array_key_exists('google_fonts', $myConfig) ? $myConfig['google_fonts'] : SELF::$WPinit_google_fonts;
        SELF::$WPinit_css = array_key_exists('css', $myConfig) ? $myConfig['css'] : SELF::$WPinit_css;
        SELF::$WPinit_css_path = array_key_exists('csspath', $myConfig) ? $myConfig['csspath'] : SELF::$WPinit_css_path;
        SELF::$WPinit_js = array_key_exists('js', $myConfig) ? $myConfig['js'] : SELF::$WPinit_js;
        SELF::$WPinit_js_path = array_key_exists('jspath', $myConfig) ? $myConfig['jspath'] : SELF::$WPinit_js_path;
        SELF::$WPinit_jquery = array_key_exists('jquery', $myConfig) ? $myConfig['jquery'] : SELF::$WPinit_jquery;
      endif;
    }

    /* 2.2 HEADER CLEANUP
    /------------------------*/
    // remove unused stuff from wp_head()
    public function WPinit_headcleanup () {
      // remove the generator meta tag
      remove_action('wp_head', 'wp_generator', 10);
      // remove wlwmanifest link
      remove_action('wp_head', 'wlwmanifest_link');
      // remove RSD API connection
      remove_action('wp_head', 'rsd_link');
      // remove wp shortlink support
      remove_action('wp_head', 'wp_shortlink_wp_head');
      // remove next/previous links (this is not affecting blog-posts)
      remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
      // remove generator name from RSS
      add_filter('the_generator', '__return_false');
      // disable emoji support
      remove_action('wp_head', 'print_emoji_detection_script', 7);
      remove_action('wp_print_styles', 'print_emoji_styles');
      // disable automatic feeds
      remove_action('wp_head', 'feed_links_extra', 3);
      remove_action('wp_head', 'feed_links', 2);
      // remove rest API link
      remove_action( 'wp_head', 'rest_output_link_wp_head', 10);
      // remove oEmbed link
      remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10);
      remove_action('wp_head', 'wp_oembed_add_host_js');
    }


    /* 2.3 RESET INLINE IMAGE DIMENSIONS (FOR CSS-SCALING OF IMAGES)
    /------------------------*/
    function wp_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
      $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html);
      return $html;
    }


    /* 2.4 ENQUEUE SCRIPTS/STYLES
    /------------------------*/
    // enqueues scripts and styles (optional typekit embed)
    // >> https://developer.wordpress.org/reference/functions/wp_enqueue_script/
    function WPinit_enqueue() {
      // jQuery (from wp core)
      if (SELF::$WPinit_jquery !== false):
        wp_deregister_script( 'jquery' );
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', false, '3.3.1');
        wp_enqueue_script( 'jquery' );
      endif;
      // scripts
      if (SELF::$WPinit_js !== false):
        wp_register_script('theme/scripts', get_stylesheet_directory_uri() . SELF::$WPinit_js_path, false, array( 'jquery' ), true);
        wp_enqueue_script('theme/scripts');
      endif;
      // disable Gutenberg block styles
      if (SELF::$WPinit_gutenberg_css !== false) :
        wp_dequeue_style( 'wp-block-library' );
      endif;
      // styles
      if (SELF::$WPinit_css !== false):
        wp_enqueue_style('theme/styles', get_stylesheet_directory_uri() . SELF::$WPinit_css_path, false, null);
      endif;
    }


    /* 2.5 EMBED GOOGLE FONTS
    /------------------------*/
    function GoogleFonts(){
      if(SELF::$WPinit_google_fonts):
        // embed start
        $output = '<link href="https://fonts.googleapis.com/css?family=';
        if(is_array(SELF::$WPinit_google_fonts)):
          // array output
          $row = 1;
          foreach (SELF::$WPinit_google_fonts as $value) {
            $output .= $row > 1 ? '|' : '';
            $output .= $value;
          }
        else:
          // fallback for a string
          $output .= SELF::$WPinit_google_fonts;
        endif;
        // embed end
        $output .= '&display=swap" rel="stylesheet">';
        // return result
        echo $output;
      endif;
    }


    /* 2.6 DISABLE GUTENBERG
    /------------------------*/
    function DisableGutenberg(){
      // check if gutenberg is disabled
      if(SELF::$WPinit_gutenberg !== true):
        // disable for posts
        add_filter('use_block_editor_for_post', '__return_false', 10);
        // disable for post types
        add_filter('use_block_editor_for_post_type', '__return_false', 10);
        // remove gutenberg styling
        // wp_dequeue_style( 'wp-block-library' );
      endif;
    }


    /* 2.7 HIDE CORE-UPDATES FOR NON-ADMINS
    /------------------------*/
    function wp_onlyadmin_update() {
      if (!current_user_can('update_core')) { remove_action( 'admin_notices', 'update_nag', 3 ); }
    }


    /* 2.8 THEME SUPPORT
    /------------------------*/
    function theme_support()  {
      if(SELF::$WPinit_support):
        foreach (SELF::$WPinit_support as $key => $value) {
          add_theme_support($value);
        }
      endif;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/



}
