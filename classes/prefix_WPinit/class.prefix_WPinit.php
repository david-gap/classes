<?
/**
 *
 *
 * Wordpress - init configuration
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.3.2
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
  2.2 HEADER CLEANUP
  2.3 RESET INLINE IMAGE DIMENSIONS (FOR CSS-SCALING OF IMAGES)
  2.4 ENQUEUE SCRIPTS/STYLES
  2.5 EMBED GOOGLE FONTS
  2.6 HIDE CORE-UPDATES FOR NON-ADMINS
  2.7 THEME SUPPORT
  2.8 BACKEND CONTROL
  2.9 REGISTER MENUS
  2.10 CLEAN THE CONTENT
3.0 OUTPUT
=======================================================*/


class prefix_WPinit {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param private array $WPinit_support: select theme support
      * @param private array $WPinit_google_fonts: google fonts
      * @param private int $WPinit_css: activate theme styling
      * @param private int $WPinit_cachebust: activate cachebust styling file
      * @param private int $WPinit_cachebust_file: theme cachebust styling path
      * @param private int $WPinit_css_version: theme styling version
      * @param private string $WPinit_css_path: theme styling path (theme is root)
      * @param private int $WPinit_js: activate theme js
      * @param private int $WPinit_js_version: theme js version
      * @param private string $WPinit_js_path: theme js path (theme is root)
      * @param private int $WPinit_jquery: activate jquery
      * @param private array $WPinit_admin_menu: disable backend menus from not admins
      * @param private array $WPinit_menus: list of all wanted WP menus
    */
    private $WPinit_support          = array("title-tag", "menus", "html5", "post-thumbnails");
    private $WPinit_google_fonts     = array();
    private $WPinit_css              = 1;
    private $WPinit_cachebust        = 1;
    private $WPinit_cachebust_file   = '/dist/rev-manifest.json';
    private $WPinit_css_version      = 1.0;
    private $WPinit_css_path         = "/dist/style.min.css";
    private $WPinit_js               = 1;
    private $WPinit_js_version       = 1.0;
    private $WPinit_js_path          = "/dist/script.min.js";
    private $WPinit_jquery           = 1;
    private $WPinit_admin_menu       = array();
    private $WPinit_menus            = array(
      array(
        'key' => 'mainmenu',
        'value' => 'Main Menu'
      ),
      array(
        'key' => 'footermenu',
        'value' => 'Footer Menu'
      )
    );


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
      // disable admin menu
      add_action( 'admin_menu', array( $this, 'Backend_remove_menus' ), 1 );
      // thumbnail dimensions
      add_filter( 'post_thumbnail_html', array( $this, 'wp_remove_thumbnail_dimensions' ), 10, 3);
      // frontend css/js files
      add_action('wp_enqueue_scripts', array( $this, 'WPinit_enqueue' ));
      // add theme support
      SELF::theme_support();
      // add google fonts
      add_action( 'wp_footer', array( $this, 'GoogleFonts' ) );
      // add menu
      add_action( 'init', array( $this, 'WPinit_theme_menus' ) );
      // clean the content
      add_filter( 'the_content', array( $this, 'WPinit_CleanContent' ) );
    }

    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $classtitle = 'WP init';
    static $classkey = 'wp';
    static $backend = array(
      "support" => array(
        "label" => "Wordpress Support",
        "type" => "array_addable"
      ),
      "google_fonts" => array(
        "label" => "Embed Google Fonts",
        "type" => "array_addable"
      ),
      "css" => array(
        "label" => "Embed CSS file",
        "type" => "switchbutton"
      ),
      "cachebust" => array(
        "label" => "Activate Cache Busting",
        "type" => "switchbutton"
      ),
      "jquery" => array(
        "label" => "Embed Jquery file",
        "type" => "switchbutton"
      ),
      "js" => array(
        "label" => "Embed JS file",
        "type" => "switchbutton"
      ),
      "css_version" => array(
        "label" => "CSS Version",
        "type" => "text"
      ),
      "js_version" => array(
        "label" => "JS Version",
        "type" => "text"
      ),
      // "css_path" => array(
      //   "label" => "CSS file path",
      //   "type" => "text"
      // ),
      // "cachebust_file" => array(
      //   "label" => "Cache Busting file path",
      //   "type" => "text"
      // ),
      // "js_path" => array(
      //   "label" => "JS file path",
      //   "type" => "text"
      // ),
      "admin_menu" => array(
        "label" => "Hide backend menu for not admins",
        "type" => "array_addable"
      ),
      "menus" => array(
        "label" => "Registered menu",
        "type" => "array_addable",
        "value" => array(
          "key" => array(
            "label" => "Slug",
            "type" => "text"
          ),
          "value" => array(
            "label" => "Name",
            "type" => "text"
          )
        )
      )
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
      if($configuration && array_key_exists('wp', $configuration)):
        // class configuration
        $myConfig = $configuration['wp'];
        // update vars
        $this->WPinit_support = array_key_exists('support', $myConfig) ? $myConfig['support'] : $this->WPinit_support;
        $this->WPinit_google_fonts = array_key_exists('google_fonts', $myConfig) ? $myConfig['google_fonts'] : $this->WPinit_google_fonts;
        $this->WPinit_css = array_key_exists('css', $myConfig) ? $myConfig['css'] : $this->WPinit_css;
        $this->WPinit_cachebust = array_key_exists('cachebust', $myConfig) ? $myConfig['cachebust'] : $this->WPinit_cachebust;
        $this->WPinit_cachebust_file = array_key_exists('cachebust_file', $myConfig) ? $myConfig['cachebust_file'] : $this->WPinit_cachebust_file;
        $this->WPinit_css_path = array_key_exists('css_path', $myConfig) ? $myConfig['css_path'] : $this->WPinit_css_path;
        $this->WPinit_css_version = array_key_exists('css_version', $myConfig) ? $myConfig['css_version'] : $this->WPinit_css_path;
        $this->WPinit_js = array_key_exists('js', $myConfig) ? $myConfig['js'] : $this->WPinit_js;
        $this->WPinit_js_version = array_key_exists('js_version', $myConfig) ? $myConfig['js_version'] : $this->WPinit_js_version;
        $this->WPinit_js_path = array_key_exists('js_path', $myConfig) ? $myConfig['js_path'] : $this->WPinit_js_path;
        $this->WPinit_jquery = array_key_exists('jquery', $myConfig) ? $myConfig['jquery'] : $this->WPinit_jquery;
        $this->WPinit_admin_menu = array_key_exists('admin_menu', $myConfig) ? $myConfig['admin_menu'] : $this->WPinit_admin_menu;
        $this->$WPinit_menus = array_key_exists('menus', $myConfig) ? $myConfig['menus'] : $this->$WPinit_menus;
      endif;
    }

    /* 2.2 HEADER CLEANUP
    /------------------------*/
    // remove unused stuff from wp_head()
    function WPinit_headcleanup () {
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
      if ($this->WPinit_jquery == 1):
        wp_deregister_script( 'jquery' );
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', false, '3.3.1');
        wp_enqueue_script( 'jquery' );
      endif;
      // scripts
      if ($this->WPinit_js == 1):
        wp_register_script('theme/scripts', get_stylesheet_directory_uri() . $this->WPinit_js_path, false, $this->WPinit_js_version, true);
        wp_enqueue_script('theme/scripts');
        # get theme directory for javascript files
        wp_localize_script( 'theme/scripts', 'theme_directory', get_stylesheet_directory_uri());
      endif;
      // styles
      if ($this->WPinit_css == 1):
        if($this->WPinit_cachebust == 1):
          $bust_file = get_stylesheet_directory_uri() . $this->WPinit_cachebust_file;
          $css_manifest_content = json_decode(file_get_contents($bust_file), true);
          $file = '/dist/'.$css_manifest_content['style.min.css'];
        else:
          $file = $this->WPinit_css_path;
        endif;
        wp_enqueue_style('theme/styles', get_stylesheet_directory_uri() . $file, false, $this->WPinit_css_version);
      endif;
    }


    /* 2.5 EMBED GOOGLE FONTS
    /------------------------*/
    function GoogleFonts(){
      if($this->WPinit_google_fonts):
        // embed start
        $output = '<link href="https://fonts.googleapis.com/css?family=';
        if(is_array($this->WPinit_google_fonts)):
          // array output
          $row = count($this->WPinit_google_fonts);
          foreach ($this->WPinit_google_fonts as $value) {
            $output .= $value;
            $output .= $row > 1 ? '|' : '';
            $row--;
          }
        else:
          // fallback for a string
          $output .= $this->WPinit_google_fonts;
        endif;
        // embed end
        $output .= '&display=swap" rel="stylesheet">';
        // return result
        echo $output;
      endif;
    }


    /* 2.6 HIDE CORE-UPDATES FOR NON-ADMINS
    /------------------------*/
    function wp_onlyadmin_update() {
      if (!current_user_can('update_core')) { remove_action( 'admin_notices', 'update_nag', 3 ); }
    }


    /* 2.7 THEME SUPPORT
    /------------------------*/
    function theme_support()  {
      if($this->WPinit_support):
        foreach ($this->WPinit_support as $key => $value) {
          add_theme_support($value);
        }
      endif;
    }


    /* 2.8 BACKEND CONTROL
    /------------------------*/
    /**
    * Removes some menus by page.
    */
    function Backend_remove_menus(){
      // get current login user's role
      $roles = wp_get_current_user()->roles;
      // test role
      if( in_array('administrator',$roles)){
      return;
      }
      if(!empty($this->WPinit_admin_menu)):
        foreach ($this->WPinit_admin_menu as $key => $value) {
          remove_menu_page( $value );
        }
      endif;
      // index.php                  // Dashboard
      // edit.php                   // Posts
      // upload.php                 // Media
      // edit.php?post_type=page    // Pages
      // edit-comments.php          // Comments
      // themes.php                 // Appearance
      // plugins.php                // Plugins
      // users.php                  // Users
      // tools.php                  // Tools
      // options-general.php        // Settings
    }


    /* 2.9 REGISTER MENUS
    /------------------------*/
    // => https://codex.wordpress.org/Function_Reference/register_nav_menus
    function WPinit_theme_menus() {
      if(is_array($this->$WPinit_menus)):
        $menus = array();
        foreach ($this->$WPinit_menus as $key => $menu) {
          $menus[$menu["key"]] = $menu["value"];
        }
        register_nav_menus($menus);
      endif;
    }


    /* 2.10 CLEAN THE CONTENT
    /------------------------*/
    function WPinit_CleanContent( $content ) {
      return preg_replace( '/[\r\n]+/', "\n", $content );
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/



}
