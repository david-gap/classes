<?
/**
 *
 *
 * Basic template parts
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.12.8
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
  1.4 PAGE OPTIONS - CREATE META BOX
2.0 FUNCTIONS
  2.1 GET CONFIGURATION FORM CONFIG FILE
  2.2 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
  2.3 STICKY MENU
  2.4 SAVE METABOXES
3.0 OUTPUT
  3.1 SORTABLE HEADER CONTENT
  3.2 SORTABLE FOOTER CONTENT
  3.3 BACKEND PAGE OPTIONS - METABOX
  3.4 PAGE OPTIONS
  3.5 PLACEHOLDER
  3.6 LOGO
  3.7 CHECK IF MAINMENU IS ACTIVE
  3.8 ADDRESS BLOCK
  3.9 DIVIDE HEADER FROM CONTENT
  3.10 FOOTER MENU
  3.11 COPYRIGHT
  3.12 SOCIAL MEDIA
  3.13 CONTACT BLOCK
  3.14 ICON BLOCK
  3.15 CONTENT BLOCK
  3.16 BODY CSS
=======================================================*/


class prefix_template {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

  /* 1.1 CONFIGURATION
  /------------------------*/
  /**
    * default vars
    * @param static int $template_container_header: activate container for the header
    * @param static int $template_container: activate container for the content
    * @param static int $template_container_footer: activate container for the footer
    * @param static string $template_coloring: template coloring (dark/light)
    * @param static bool $template_ph_active: activate placeholder
    * @param static bool $template_ph_address: placeholder show address block
    * @param static string $template_ph_custom: placeholder custom content
    * @param static array $template_address: address block content
    * @param static array $template_socialmedia: social media
    * @param static int $template_header_divider: Activate header divider
    * @param static int $template_header_sticky: activate sticky header
    * @param static int $template_header_stickyload: activate sticky header on load
    * @param static int $template_header_dmenu: Activate header hamburger for desktop
    * @param static string $template_header_custom:  Custom header html
    * @param static array $template_header_sort: Sort and activate blocks inside header builder
    * @param static string $template_header_logo_link: Logo link with wordpress fallback
    * @param static array $template_header_logo_d: desktop logo configuration
    * @param static array $template_header_logo_m: mobile logo configuration
    * @param static string $template_header_after: html code after header
    * @param static int $template_page_active: activate page options
    * @param static array $template_page_options: show/hide template elements
    * @param static array $template_page_additional: additional custom fields template elements
    * @param static array $template_page_metablock: activate metablock on detail page/posts
    * @param static array $template_page_metablockAdds: Add metabox to CPT by slugs
    * @param static array $template_page_options: show/hide template elements
    * @param static int $template_footer_active: activate footer
    * @param static string $template_footer_cr: copyright text
    * @param static string $template_footer_custom: custom html
    * @param static array $template_footer_sort: Sort and activate blocks inside footer builder
    * @param static string $template_footer_before: html code before footer
  */
  static $template_container_header   = 1;
  static $template_container          = 1;
  static $template_container_footer   = 1;
  static $template_coloring           = "light";
  static $template_ph_active          = true;
  static $template_ph_address         = true;
  static $template_ph_custom          = "";
  static $template_address            = array(
    'company' => '',
    'street' => '',
    'street2' => '',
    'postalCode' => '',
    'city' => '',
    'country' => '',
    'phone' => '',
    'mobile' => '',
    'email' => '',
    'labels' => array(
      'company' => '',
      'street' => '',
      'street2' => '',
      'postalCode' => '',
      'city' => '',
      'country' => '',
      'phone' => '',
      'mobile' => '',
      'email' => ''
    )
  );
  static $template_socialmedia        = array(
    "facebook" => "",
    "instagram" => ""
  );
  static $template_contactblock       = array(
    "phone" => "",
    "mail" => "",
    "whatsapp" => ""
  );
  static $template_header_divider     = 1;
  static $template_header_sticky      = 1;
  static $template_header_stickyload  = 0;
  static $template_header_dmenu       = 1;
  static $template_header_custom      = "";
  static $template_header_sort        = array(
    "container_start" => 1,
    "logo" => 1,
    "menu" => 1,
    "hamburger" => 1,
    "socialmedia" => 0,
    "custom" => 0,
    "container_end" => 1
  );
  static $template_header_logo_link   = "";
  static $template_header_logo_d      = array(
    "img" => "",
    "width" => "",
    "height" => "",
    "alt" => ""
  );
  static $template_header_logo_m      = array(
    "img" => "",
    "width" => "",
    "height" => "",
    "alt" => ""
  );
  static $template_header_after       = "";
  static $template_page_active        = 1;
  static $template_page_options       = array(
    "header" => 1,
    "date" => 0,
    "time" => 0,
    "author" => 0,
    "title" => 1,
    "comments" => 1,
    "sidebar" => 1,
    "footer" => 1,
    "darkmode" => 1,
    "beforeMain" => 1,
    "afterMain" => 1
  );
  static $template_page_metablock     = array(
    "page" => 0,
    "post" => 0
  );
  static $template_page_metablockAdds = array();
  static $template_blog_type          = 1;
  static $template_blog_type_options  = array(
    "default" => "-",
    "Image" => "Image",
    "Video" => "Video",
    "Audio" => "Audio"
  );
  static $template_blog_type_parts    = array(
    "author" => 0,
    "date" => 0,
    "time" => 0,
    "categories" => 0
  );
  static $template_blog_dateformat    = 'd.m.Y';
  static $template_page_additional    = array();
  static $template_footer_active      = 1;
  static $template_footer_cr          = "";
  static $template_footer_custom      = "";
  static $template_footer_sort        = array(
    "container_start" => 1,
    "menu" => 1,
    "socialmedia" => 1,
    "copyright" => 1,
    "address" => 1,
    "custom" => 0,
    "container_end" => 1
  );
  static $template_footer_before      = "";


  /* 1.2 ON LOAD RUN
  /------------------------*/
  public function __construct() {
    // update default vars with configuration file
    SELF::updateVars();
    // add page options to backend
    if(SELF::$template_page_active == 1):
      // metabox for option selection
      add_action( 'add_meta_boxes', array( $this, 'WPtemplate_Metabox' ) );
      // update custom fields
      add_action('save_post', array( $this, 'WPtemplate_meta_Save' ),  10, 2 );
    endif;
    // shortcodes
    add_shortcode( 'socialmedia', array( $this, 'SocialMedia' ) );
  }


  /* 1.3 BACKEND ARRAY
  /------------------------*/
  static $classtitle = 'Template';
  static $classkey = 'template';
  static $backend = array(
    "container_header" => array(
      "label" => "Activate header container",
      "type" => "switchbutton"
    ),
    "container" => array(
      "label" => "Activate main container",
      "type" => "switchbutton"
    ),
    "container_footer" => array(
      "label" => "Activate footer container",
      "type" => "switchbutton"
    ),
    "coloring" => array(
      "label" => "Body css (light/dark)",
      "type" => "text"
    ),
    "address" => array(
      "label" => "Addressblock information",
      "type" => "multiple",
      "value" => array(
        "company" => array(
          "label" => "Company",
          "type" => "text"
        ),
        "street" => array(
          "label" => "Street",
          "type" => "text"
        ),
        "street2" => array(
          "label" => "Street add",
          "type" => "text"
        ),
        "postalCode" => array(
          "label" => "Postcode",
          "type" => "text"
        ),
        "city" => array(
          "label" => "City",
          "type" => "text"
        ),
        "country" => array(
          "label" => "Country",
          "type" => "text"
        ),
        "phone" => array(
          "label" => "Phone",
          "type" => "text"
        ),
        "mobile" => array(
          "label" => "Mobile",
          "type" => "text"
        ),
        "email" => array(
          "label" => "E-Mail",
          "type" => "text"
        ),
        "labels" => array(
          "label" => "Labels",
          "type" => "multiple",
          "value" => array(
            "company" => array(
              "label" => "Company",
              "type" => "text"
            ),
            "street" => array(
              "label" => "Street",
              "type" => "text"
            ),
            "street2" => array(
              "label" => "Street add",
              "type" => "text"
            ),
            "postalCode" => array(
              "label" => "Postcode",
              "type" => "text"
            ),
            "city" => array(
              "label" => "City",
              "type" => "text"
            ),
            "country" => array(
              "label" => "Country",
              "type" => "text"
            ),
            "phone" => array(
              "label" => "Phone",
              "type" => "text"
            ),
            "mobile" => array(
              "label" => "Mobile",
              "type" => "text"
            ),
            "email" => array(
              "label" => "E-Mail",
              "type" => "text"
            )
          )
        )
      )
    ),
    "socialmedia" => array(
      "label" => "Social Media block (Icons)",
      "type" => "multiple",
      "value" => array(
        "facebook" => array(
          "label" => "Facebook",
          "type" => "text"
        ),
        "instagram" => array(
          "label" => "Instagram",
          "type" => "text"
        ),
        "twitter" => array(
          "label" => "Twitter",
          "type" => "text"
        ),
        "linkedin" => array(
          "label" => "Linkedin",
          "type" => "text"
        )
      )
    ),
    "contactblock" => array(
      "label" => "Contact block (Icons)",
      "type" => "multiple",
      "value" => array(
        "phone" => array(
          "label" => "Phone",
          "type" => "text"
        ),
        "mail" => array(
          "label" => "Mail",
          "type" => "text"
        ),
        "whatsapp" => array(
          "label" => "WhatsApp",
          "type" => "text"
        )
      )
    ),
    // "placeholder" => array(
    //   "label" => "Placeholder",
    //   "type" => "multiple",
    //   "value" => array(
    //     "active" => array(
    //       "label" => "Activate placeholder",
    //       "type" => "checkbox"
    //     ),
    //     "address" => array(
    //       "label" => "Add Adressblock",
    //       "type" => "checkbox"
    //     ),
    //     "custom" => array(
    //       "label" => "Custom output",
    //       "type" => "textarea"
    //     )
    //   )
    // ),
    "header" => array(
      "label" => "Header",
      "type" => "multiple",
      "value" => array(
        "divider" => array(
          "label" => "Activate divider",
          "type" => "switchbutton"
        ),
        "sticky" => array(
          "label" => "Sticky header",
          "type" => "switchbutton"
        ),
        "sticky_onload" => array(
          "label" => "Sticky header on load",
          "type" => "switchbutton"
        ),
        "desktop_menu" => array(
          "label" => "Desktop menu",
          "type" => "switchbutton"
        ),
        "custom" => array(
          "label" => "Custom Element",
          "type" => "textarea"
        ),
        "logo_link" => array(
          "label" => "Custom Logo link",
          "type" => "text"
        ),
        "logo_desktop" => array(
          "label" => "Logo desktop",
          "type" => "multiple",
          "value" => array(
            "img" => array(
              "label" => "URL",
              "type" => "img"
            ),
            "width" => array(
              "label" => "Width",
              "type" => "text"
            ),
            "height" => array(
              "label" => "Height",
              "type" => "text"
            ),
            "alt" => array(
              "label" => "Alternative text",
              "type" => "text"
            )
          )
        ),
        "logo_mobile" => array(
          "label" => "Logo mobile",
          "type" => "multiple",
          "value" => array(
            "img" => array(
              "label" => "URL",
              "type" => "img"
            ),
            "width" => array(
              "label" => "Width",
              "type" => "text"
            ),
            "height" => array(
              "label" => "Height",
              "type" => "text"
            ),
            "alt" => array(
              "label" => "Alternative text",
              "type" => "text"
            )
          )
        ),
        "sort" => array(
          "label" => "Sort and activate",
          "type" => "multiple",
          "css" => "sortable",
          "value" => array(
            "container_start" => array(
              "label" => "Container start",
              "type" => "hr"
            ),
            "logo" => array(
              "label" => "Logo",
              "type" => "switchbutton"
            ),
            "menu" => array(
              "label" => "Menu",
              "type" => "switchbutton"
            ),
            "hamburger" => array(
              "label" => "Hamburger",
              "type" => "switchbutton"
            ),
            "socialmedia" => array(
              "label" => "social media",
              "type" => "switchbutton"
            ),
            "custom" => array(
              "label" => "Custom",
              "type" => "switchbutton"
            ),
            "container_end" => array(
              "label" => "Container end",
              "type" => "hr"
            )
          )
        ),
        "after_header" => array(
          "label" => "Custom content after header",
          "type" => "textarea"
        )
      )
    ),
    "page" => array(
      "label" => "Page",
      "type" => "multiple",
      "value" => array(
        "active" => array(
          "label" => "Activate page options",
          "type" => "switchbutton"
        ),
        "metablock" => array(
          "label" => "Activate metablock",
          "type" => "multiple",
          "value" => array(
            "page" => array(
              "label" => "Pages",
              "type" => "switchbutton"
            ),
            "post" => array(
              "label" => "Posts",
              "type" => "switchbutton"
            )
          )
        ),
        "add_metablock" => array(
          "label" => "Metablock for CPT",
          "type" => "array_addable"
        ),
        "options" => array(
          "label" => "Page options",
          "type" => "multiple",
          "value" => array(
            "header" => array(
              "label" => "Hide header",
              "type" => "switchbutton"
            ),
            "date" => array(
              "label" => "Hide date",
              "type" => "switchbutton"
            ),
            "time" => array(
              "label" => "Hide time",
              "type" => "switchbutton"
            ),
            "author" => array(
              "label" => "Hide author",
              "type" => "switchbutton"
            ),
            "title" => array(
              "label" => "Hide title",
              "type" => "switchbutton"
            ),
            "comments" => array(
              "label" => "Hide comments",
              "type" => "switchbutton"
            ),
            "sidebar" => array(
              "label" => "Hide sidebar",
              "type" => "switchbutton"
            ),
            "footer" => array(
              "label" => "Hide footer",
              "type" => "switchbutton"
            ),
            "darkmode" => array(
              "label" => "Darkmode",
              "type" => "switchbutton"
            ),
            "beforeMain" => array(
              "label" => "Code before main block",
              "type" => "switchbutton"
            ),
            "afterMain" => array(
              "label" => "Code after main block",
              "type" => "switchbutton"
            )
          )
        ),
        "additional" => array(
          "label" => "Add page options",
          "type" => "array_addable",
          "value" => array(
            "key" => array(
              "label" => "Option label",
              "type" => "text"
            ),
            "value" => array(
              "label" => "Option key",
              "type" => "text"
            )
          )
        )
      )
    ),
    "blog" => array(
      "label" => "Blog",
      "type" => "multiple",
      "value" => array(
        "type" => array(
          "label" => "Activate blog template options",
          "type" => "switchbutton"
        ),
        "show" => array(
          "label" => "Show on overview pages",
          "type" => "multiple",
          "value" => array(
            "author" => array(
              "label" => "Activate author",
              "type" => "switchbutton"
            ),
            "date" => array(
              "label" => "Activate date",
              "type" => "switchbutton"
            ),
            "time" => array(
              "label" => "Activate time",
              "type" => "switchbutton"
            ),
            "categories" => array(
              "label" => "Activate categories",
              "type" => "switchbutton"
            )
          )
        ),
        "dateformat" => array(
          "label" => "Date format",
          "type" => "text",
          "placeholder" => "d.m.Y"
        )
      )
    ),
    "footer" => array(
      "label" => "Footer",
      "type" => "multiple",
      "value" => array(
        "active" => array(
          "label" => "Activate footer",
          "type" => "switchbutton"
        ),
        "copyright" => array(
          "label" => "Copyright",
          "type" => "text"
        ),
        "custom" => array(
          "label" => "Custom Element",
          "type" => "textarea"
        ),
        "sort" => array(
          "label" => "Sort and activate",
          "type" => "multiple",
          "css" => "sortable",
          "value" => array(
            "container_start" => array(
              "label" => "Container start",
              "type" => "hr"
            ),
            "menu" => array(
              "label" => "Menu",
              "type" => "switchbutton"
            ),
            "socialmedia" => array(
              "label" => "Social media",
              "type" => "switchbutton"
            ),
            "copyright" => array(
              "label" => "Copyright",
              "type" => "switchbutton"
            ),
            "address" => array(
              "label" => "Address block",
              "type" => "switchbutton"
            ),
            "custom" => array(
              "label" => "Custom",
              "type" => "switchbutton"
            ),
            "container_end" => array(
              "label" => "Container end",
              "type" => "hr"
            )
          )
        ),
        "before_footer" => array(
          "label" => "Custom content before footer",
          "type" => "textarea"
        )
      )
    )
  );


  /* 1.4 PAGE OPTIONS - CREATE META BOX
  /------------------------*/
  function WPtemplate_Metabox() {
    // get core post types
    $core_args = array(
      'public' => true,
      '_builtin' => true
    );
    $core_pt = get_post_types( $core_args );
    // get custom post types
    $custom_args = array(
      'public' => true,
      'publicly_queryable' => true
    );
    $custom_pt = get_post_types( $custom_args );
    // merge & clean post types
    $post_types = array_merge($core_pt, $custom_pt);
    unset($post_types['attachment']);
    unset($post_types['nav_menu_item']);
    // register meta box for all selected post types
    foreach( $post_types as $post_type ){
        add_meta_box(
            'template_page_options',
            __( 'Options', 'template' ),
            array($this, 'WPtemplate_pageoptions'),
            $post_type,
            'side',
            'low'
        );
    }
  }



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/

    /* 2.1 GET CONFIGURATION FORM CONFIG FILE
    /------------------------*/
    private function updateVars(){
      // get configuration
      global $configuration;
      // if configuration file exists && class-settings
      if($configuration && array_key_exists('template', $configuration)):
        // class configuration
        $myConfig = $configuration['template'];
        // update vars
        SELF::$template_container_header = array_key_exists('container_header', $myConfig) ? $myConfig['container_header'] : SELF::$template_container_header;
        SELF::$template_container = array_key_exists('container', $myConfig) ? $myConfig['container'] : SELF::$template_container;
        SELF::$template_container_footer = array_key_exists('container_footer', $myConfig) ? $myConfig['container_footer'] : SELF::$template_container_footer;
        SELF::$template_coloring = array_key_exists('coloring', $myConfig) ? $myConfig['coloring'] : SELF::$template_coloring;
        SELF::$template_address = array_key_exists('address', $myConfig) ? $myConfig['address'] : SELF::$template_address;
        SELF::$template_socialmedia = array_key_exists('socialmedia', $myConfig) ? $myConfig['socialmedia'] : SELF::$template_socialmedia;
        SELF::$template_contactblock = array_key_exists('contactblock', $myConfig) ? $myConfig['contactblock'] : SELF::$template_contactblock;
        if($myConfig && array_key_exists('placeholder', $myConfig)):
          $placeholder = $myConfig['placeholder'];
          SELF::$template_ph_active = array_key_exists('active', $placeholder) ? $placeholder['active'] : SELF::$template_ph_active;
          SELF::$template_ph_address = array_key_exists('address', $placeholder) ? $placeholder['address'] : SELF::$template_ph_address;
          SELF::$template_ph_custom = array_key_exists('custom', $placeholder) ? $placeholder['custom'] : SELF::$template_ph_custom;
        endif;
        if($configuration && array_key_exists('header', $myConfig)):
          $header = $myConfig['header'];
          SELF::$template_header_divider = array_key_exists('divider', $header) ? $header['divider'] : SELF::$template_header_divider;
          SELF::$template_header_sticky = array_key_exists('sticky', $header) ? $header['sticky'] : SELF::$template_header_sticky;
          SELF::$template_header_stickyload = array_key_exists('sticky_onload', $header) ? $header['sticky_onload'] : SELF::$template_header_stickyload;
          SELF::$template_header_dmenu = array_key_exists('desktop_menu', $header) ? $header['desktop_menu'] : SELF::$template_header_dmenu;
          SELF::$template_header_custom = array_key_exists('custom', $header) ? $header['custom'] : SELF::$template_header_custom;
          SELF::$template_header_sort = array_key_exists('sort', $header) ? $header['sort'] : SELF::$template_header_sort;
          SELF::$template_header_logo_link = array_key_exists('logo_link', $header) ? $header['logo_link'] : SELF::$template_header_logo_link;
          SELF::$template_header_logo_d = array_key_exists('logo_desktop', $header) ? $header['logo_desktop'] : SELF::$template_header_logo_d;
          SELF::$template_header_logo_m = array_key_exists('logo_mobile', $header) ? $header['logo_mobile'] : SELF::$template_header_logo_m;
          SELF::$template_header_after = array_key_exists('after_header', $header) ? $header['after_header'] : SELF::$template_header_after;
        endif;
        if($configuration && array_key_exists('page', $myConfig)):
          $page = $myConfig['page'];
          SELF::$template_page_active = array_key_exists('active', $page) ? $page['active'] : SELF::$template_page_active;
          SELF::$template_page_metablock = array_key_exists('metablock', $page) ? $page['metablock'] : SELF::$template_page_metablock;
          SELF::$template_page_metablockAdds = array_key_exists('add_metablock', $page) ? $page['add_metablock'] : SELF::$template_page_metablockAdds;
          SELF::$template_page_options = array_key_exists('options', $page) ? array_merge(SELF::$template_page_options, $page['options']) : SELF::$template_page_options;
          SELF::$template_page_additional = array_key_exists('additional', $page) ? array_merge(SELF::$template_page_additional, $page['additional']) : SELF::$template_page_additional;
        endif;
        if($configuration && array_key_exists('blog', $myConfig)):
          $blog = $myConfig['blog'];
          SELF::$template_blog_type = array_key_exists('type', $blog) ? $blog['type'] : SELF::$template_blog_type;
          SELF::$template_blog_type_parts = array_key_exists('show', $blog) ? $blog['show'] : SELF::$template_blog_type_parts;
          SELF::$template_blog_dateformat = array_key_exists('dateformat', $blog) ? $blog['dateformat'] : SELF::$template_blog_dateformat;
        endif;
        if($configuration && array_key_exists('footer', $myConfig)):
          $footer = $myConfig['footer'];
          SELF::$template_footer_active = array_key_exists('active', $footer) ? $footer['active'] : SELF::$template_footer_active;
          SELF::$template_footer_cr = array_key_exists('copyright', $footer) ? $footer['copyright'] : SELF::$template_footer_cr;
          SELF::$template_footer_custom = array_key_exists('custom', $footer) ? $footer['custom'] : SELF::$template_footer_custom;
          SELF::$template_footer_sort = array_key_exists('sort', $footer) ? $footer['sort'] : SELF::$template_footer_sort;
          SELF::$template_footer_before = array_key_exists('before_footer', $footer) ? $footer['before_footer'] : SELF::$template_footer_before;
        endif;
      endif;
    }


    /* 2.2 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
    /------------------------*/
    // $container to activate, $wrap to add the class attribute
    public static function AddContainer(int $container = 0, bool $wrap = true){
      // fallback if config file is missing
      $active = $container ? $container : SELF::$template_container;
      // check if container is active
      if($active === 1 && $wrap === true):
        return 'class="container"';
      elseif($active === 1):
        return 'container';
      endif;
    }


    /* 2.3 STICKY MENU
    /------------------------*/
    public static function CheckSticky(int $sticky = 1){
      if($sticky === 1):
        return 'stickyable';
      endif;
    }


    /* 2.4 SAVE METABOXES
    /------------------------*/
    public function WPtemplate_meta_Save($post_id) {
      // //Not save if the user hasn't submitted changes
      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
        return;
      endif;
      // Making sure the user has permission
      if( ! current_user_can( 'edit_post', $post_id ) ):
        return;
      endif;
      // save page optons
      if(isset($_POST['template_page_options'])):
        $options = $_POST['template_page_options'] !== '' ? serialize($_POST['template_page_options']) : '';
        update_post_meta($post_id, 'template_page_options', $options);
      else:
        update_post_meta($post_id, 'template_page_options', '');
      endif;
      // save blog template
      if(isset($post_type) && "post" != $post_type && "attachment" != $post_type && "nav_menu_item" != $post_type && isset($_POST['template_blog_type'])):
        update_post_meta($post_id, 'template_blog_type', $_POST['template_blog_type']);
      endif;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 SORTABLE HEADER CONTENT
    /------------------------*/
    static public function HeaderContent(){
      // vars
      $order = SELF::$template_header_sort;
      $counter = 0;
      $container = '<div class="header-container ' . prefix_template::AddContainer(prefix_template::$template_container_header, false) . '">';
      $container_open = false;
      $container_closed = false;

      // open container for not defined container
      if(!array_key_exists('container_start', $order) && prefix_template::$template_container_header !== 0):
        echo $container;
      endif;

      // open before container
      if(prefix_template::$template_container_header !== 0):
        echo '<div class="before-container">';
      endif;

      foreach ($order as $key => $value) {
        $counter++;
        switch ($key) {
          case 'container_start':
            if(prefix_template::$template_container_header !== 0):
              // close before container
              echo '</div>';
              // open container
              echo $container;
              $container_open = true;
            endif;
            break;
          case 'container_end':
            if($container_open !== false):
              // close container
              echo '</div>';
              $container_closed = true;
              // open after container
              echo '<div class="after-container">';
            endif;
            break;
          case 'menu':
            echo $value == 1 ? SELF::WP_MainMenu(SELF::$template_header_dmenu, 'menu') : '';
            break;
          case 'hamburger':
            echo $value == 1 ? SELF::WP_MainMenu(SELF::$template_header_dmenu, 'hamburger') : '';
            break;
          case 'logo':
            echo $value == 1 ? SELF::Logo(SELF::$template_header_logo_link, SELF::$template_header_logo_d, SELF::$template_header_logo_m) : '';
            break;
          case 'socialmedia':
            echo $value == 1 ? SELF::SocialMedia() : '';
            break;
          case 'contactblock':
            echo $value == 1 ? SELF::ContactBlock(SELF::$template_contactblock) : '';
            break;
          case 'custom':
            // WP check
            if (function_exists('do_shortcode')):
              echo $value == 1 ? do_shortcode(str_replace("'", '"', SELF::$template_header_custom)) : '';
            else:
              echo $value == 1 ? str_replace("'", '"', SELF::$template_header_custom) : '';
            endif;
            break;

          default:
            // code...
            break;
        }
      }
      // check if container been closed
      if($container_closed == false):
        echo '</div>';
      endif;

      // close after container
      if($container_open !== false):
        echo '</div>';
      endif;
    }


    /* 3.2 SORTABLE FOOTER CONTENT
    /------------------------*/
    static public function FooterContent(){
      // vars
      $order = SELF::$template_footer_sort;
      $counter = 0;
      $container = '<div class="footer-container ' . prefix_template::AddContainer(prefix_template::$template_container_footer, false) . '">';
      $container_open = false;
      $container_closed = false;

      // open container for not defined container
      if(!array_key_exists('container_start', $order) && prefix_template::$template_container_footer !== 0):
        echo $container;
      endif;

      // open before container
      if(prefix_template::$template_container_footer !== 0):
        echo '<div class="before-container">';
      endif;

      foreach ($order as $key => $value) {
        $counter++;
        switch ($key) {
          case 'container_start':
            if(prefix_template::$template_container_footer !== 0):
              // close before container
              echo '</div>';
              // open container
              echo $container;
              $container_open = true;
            endif;
            break;
          case 'container_end':
            if($container_open !== false):
              // close container
              echo '</div>';
              $container_closed = true;
              // open after container
              echo '<div class="after-container">';
            endif;
            break;
          case 'menu':
            echo $value == 1 ? SELF::WP_FooterMenu($value) : '';
            break;
          case 'address':
            echo $value == 1 ? SELF::AddressBlock(SELF::$template_address) : '';
            break;
          case 'copyright':
            echo $value == 1 && !empty(SELF::$template_footer_cr) ? SELF::Copyright(SELF::$template_footer_cr) : '';
            break;
          case 'socialmedia':
            echo $value == 1 ? SELF::Socialmedia() : '';
            break;
          case 'contactblock':
            echo $value == 1 ? SELF::ContactBlock(SELF::$template_contactblock) : '';
            break;
          case 'custom':
            // WP check
            if (function_exists('do_shortcode')):
              echo $value == 1 ? do_shortcode(str_replace("'", '"', SELF::$template_footer_custom)) : '';
            else:
              echo $value == 1 ? str_replace("'", '"', SELF::$template_footer_custom) : '';
            endif;
            break;

          default:
            // code...
            break;
        }
      }
      // check if container been closed
      if($container_open !== false && $container_closed == false):
        echo '</div>';
      endif;

      // close after container
      if($container_open !== false):
        echo '</div>';
      endif;
    }


    /* 3.3 BACKEND PAGE OPTIONS - METABOX
    /------------------------*/
    function WPtemplate_pageoptions($post) {
        // vars
        $output = '';
        $get_options = get_post_meta($post->ID, 'template_page_options', true);
        $options = unserialize($get_options);
        // output
        echo '<div class="wrap" id="WPtemplate">';
          // page options
          echo '<p><b>' . __( 'Page options', 'template' ) . '</b></p>';
          echo '<ul>';
            $exeptions = array('beforeMain', 'afterMain');
            foreach (SELF::$template_page_options as $key => $value) {
              // check if option is active
              if($value == 1 && !in_array($key, $exeptions)):
                $active = prefix_core_BaseFunctions::setChecked($key, $options);
                $hide = $key !== 'darkmode' ? 'Hide ' : '';
                // rule for metabox before output
                if(in_array($key, array('date', 'time', 'author'))):
                  if(in_array($post->post_type, array('page', 'post')) && SELF::$template_page_metablock[$post->post_type] == 1 || !empty(SELF::$template_page_metablockAdds) && in_array($post->post_type, SELF::$template_page_metablockAdds)):
                    $show = true;
                  else:
                    $show = false;
                  endif;
                else:
                  $show = true;
                endif;
                // output
                if($show == true):
                  echo '<li><label><input type="checkbox" name="template_page_options[]" value="' . $key . '" ' . $active . '>' . __( $hide . $key, 'template' ) . '</label></li>';
                endif;
              endif;
            }
            foreach (SELF::$template_page_additional as $key => $additional) {
              // check if additional option are available
              if(array_key_exists('key', $additional) && array_key_exists('value', $additional)):
                $active = prefix_core_BaseFunctions::setChecked($additional["key"], $options);
                echo '<li><label><input type="checkbox" name="template_page_options[]" value="' . $additional["key"] . '" ' . $active . '>' . $additional["value"] . '</label></li>';
              endif;
            }
          echo '</ul>';
          // return exeptions
          foreach (SELF::$template_page_options as $key => $value) {
            // check if option is active
            if($value == 1 && in_array($key, $exeptions)):
              if($key == 'beforeMain'):
                $customLabel = 'Code before main block';
              elseif($key == 'afterMain'):
                $customLabel = 'Code after main block';
              endif;
              echo '<div class="exeption">';
                echo '<p><label for="exeption-' . $key . '"><b>' . __( $customLabel, 'template' ) . '</b></label></p>';
                echo '<textarea id="exeption-' . $key . '" name="template_page_options[' . $key . ']">' . $options[$key] . '</textarea>';
              echo '</div>';
            endif;
          }
          // blog template options
          if(get_post_type() == "post" && SELF::$template_blog_type == 1):
            $get_template = get_post_meta($post->ID, 'template_blog_type', true);
            echo '<p><b>' . __( 'Blog Template', 'template' ) . '</b></p>';
            echo '<select name="template_blog_type">';
              foreach (SELF::$template_blog_type_options as $key => $value) {
                // check if option is active
                $selected = prefix_core_BaseFunctions::setSelected($key, $get_template);
                echo '<option value="' . $key . '" ' . $selected . '>' . __( $value, 'template' ) . '</option>';
              }
            echo '</select>';
          endif;
        echo '</div>';
    }


    /* 3.4 PAGE OPTIONS
    /------------------------*/
    static public function PageOptions($id) {
        // vars
        $output = array();
        $get_options = get_post_meta($id, 'template_page_options', true);
        $options = $get_options && $get_options !== '' ? unserialize($get_options) : array();
        $options = apply_filters( 'template_PageOptions', $options );
        // check activity
        foreach (SELF::$template_page_options as $key => $value) {
          // check if option is active
          if($value !== 0 && in_array($key, $options)):
            unset($options[$key]);
          endif;
        }
        // output
        return $options;
    }


    /* 3.5 PLACEHOLDER
    /------------------------*/
    static public function SitePlaceholder(){
      // vars
      $output = '';

      if(SELF::$template_ph_active === true):
        $output .= '<div id="placeholder">';
          $output .= '<h1>' . __( 'Website under construction', 'Template' ) . '</h1>';
          $output .= SELF::$template_ph_custom ? SELF::$template_ph_custom : '';
          $output .= SELF::$template_ph_address === true ? SELF::AddressBlock(SELF::$template_address) : '';
        $output .= '</div>';

        return $output;
        exit;
      endif;
    }


    /* 3.6 LOGO
    /------------------------*/
    public static function Logo(string $link = "", array $desktop = array(), array $mobile = array()){
      // vars
      $output = '';
      $page_name = get_bloginfo();
      $link = function_exists("get_bloginfo") && $link == "" ? get_bloginfo('url') : $link;
      $add_desktop = array_key_exists('img', $mobile) && $mobile['img'] !== "" ? 'class="desktop"' : '';
      $add_container = array_key_exists('img', $desktop) && $desktop['img'] == "" && $mobile['img'] == "" ? ' text_logo' : '';
      $img_desktop = array_key_exists('img', $desktop) && $desktop['img'] !== '' ? wp_get_attachment_image_src($desktop['img'], 'full') : '';
      $img_mobile = array_key_exists('img', $mobile) && $mobile['img'] !== '' ? wp_get_attachment_image_src($mobile['img'], 'full') : '';
      // output
      $output .= '<a href="' . $link . '" class="logo' . $add_container .'">';
      if($img_desktop !== ""):
        $desktop_add = '';
        $desktop_add .= array_key_exists('width', $desktop) && $desktop['width'] !== "" ? ' width="' . $desktop['width'] . '"' : '';
        $desktop_add .= array_key_exists('height', $desktop) && $desktop['height'] !== "" ? ' height="' . $desktop['height'] . '"' : '';
        $desktop_add .= $desktop['alt'] !== "" ? ' alt="' . $desktop['alt'] . '"' : '';
        $output .= '<img src="' . $img_desktop[0] . '" ' . $add_desktop . $desktop_add . '>';
        $mobile_add = '';
        $mobile_add .= array_key_exists('width', $mobile) && $mobile['width'] !== "" ? ' width="' . $mobile['width'] . '"' : '';
        $mobile_add .= array_key_exists('height', $mobile) && $mobile['height'] !== "" ? ' height="' . $mobile['height'] . '"' : '';
        $mobile_add .= $mobile['alt'] !== "" ? ' alt="' . $mobile['alt'] . '"' : '';
        $output .= $img_mobile !== "" ? '<img src="' . $img_mobile[0] . '" class="mobile"' . $mobile_add . '>' : '';
      else:
        $output .= $page_name;
      endif;
      $output .= '</a>';

      return $output;
    }


    /* 3.7 CHECK IF MAINMENU IS ACTIVE
    /------------------------*/
    public static function WP_MainMenu(int $active = 1, string $request = ''){
      if($active === 1):
        $menu_active = 'hidden_mobile';
        $hamburger_active = 'mobile';
      else:
        $menu_active = 'hidden_mobile hidden_desktop';
        $hamburger_active = '';
      endif;
      // output
      $output = '';
      if ( has_nav_menu( 'mainmenu' ) ) :
        // get menu
        if($request !== 'hamburger'):
          $output .= wp_nav_menu([
            'container_class'=> $menu_active,
            'menu_id' => 'menu_main',
            'container'=> 'div',
            'container_id' => 'menu-main-container',
            'depth' => 2,
            'theme_location' => 'mainmenu'
          ]);
        endif;
        // get hamburger
        if($request !== 'menu'):
          $output .= '<button class="hamburger ' . $hamburger_active . '" aria-label="Main Menu">';
            $output .= '<span>&nbsp;</span>';
          $output .= '</button>';
        endif;
      endif;

      return $output;
    }


    /* 3.8 ADDRESS BLOCK
    /------------------------*/
    /**
      * @param array $address: given address content
      * options are company, street, street2, zip, postalCode, city, phone, mobile, email, labels (array with childs)
      * @return string price
    */
    public static function AddressBlock(array $address = array()){
      // vars
      $output = '';
      // defaults
      $defaults = array(
        'company' => '',
        'street' => '',
        'street2' => '',
        'postalCode' => '',
        'city' => '',
        'phone' => '',
        'mobile' => '',
        'email' => '',
        'labels' => array(
          'company' => '',
          'street' => '',
          'street2' => '',
          'postalCode' => '',
          'city' => '',
          'phone' => '',
          'mobile' => '',
          'email' => ''
        )
      );
      $config = array_merge($defaults, $address);

      $output .= '<address>';
        if($config["company"] !== ''):
          $output .= '<b rel="me" class="company">';
            $output .= $config["labels"] && array_key_exists('company', $config["labels"]) && $config["labels"]["company"] !== '' ? $config["labels"]["company"] . ' ' : '';
            $output .= $config["company"];
          $output .= '</b>';
        endif;
        if($config["street"] !== ''):
          $output .= '<span class="street">';
            $output .= $config["labels"] && array_key_exists('street', $config["labels"]) && $config["labels"]["street"] !== '' ? $config["labels"]["street"] . ' ' : '';
            $output .= $config["street"];
          $output .= '</span>';
        endif;
        if($config["street2"] !== ''):
          $output .= '<span class="street_add">';
            $output .= $config["labels"] && array_key_exists('street2', $config["labels"]) && $config["labels"]["street2"] !== '' ? $config["labels"]["street2"] . ' ' : '';
            $output .= $config["street2"];
          $output .= '</span>';
        endif;
        $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? '<span class="location">' : '';
          if($config["postalCode"] !== ''):
            $output .= '<span class="postalcode">';
              $output .= $config["labels"] && array_key_exists('postalCode', $config["labels"]) && $config["labels"]["postalCode"] !== '' ? $config["labels"]["postalCode"] . ' ' : '';
              $output .= $config["postalCode"];
            $output .= '</span>';
          endif;
          $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? ' ' : '';
          if($config["city"] !== ''):
            $output .= '<span class="city">';
              $output .= $config["labels"] && array_key_exists('city', $config["labels"]) && $config["labels"]["city"] !== '' ? $config["labels"]["city"] . ' ' : '';
              $output .= $config["city"];
            $output .= '</span>';
          endif;
        $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? '</span>' : '';
        if($config["country"] !== ''):
          $output .= '<span class="country">';
            $output .= $config["labels"] && array_key_exists('country', $config["labels"]) && $config["labels"]["country"] !== '' ? $config["labels"]["country"] . ' ' : '';
            $output .= $config["country"];
          $output .= '</span>';
        endif;
        if($config["phone"] !== ''):
          $output .= '<a href="tel:' . prefix_core_BaseFunctions::cleanPhoneNr($config["phone"]) . '" class="call phone_nr">';
            $output .= $config["labels"] && array_key_exists('phone', $config["labels"]) && $config["labels"]["phone"] !== '' ? $config["labels"]["phone"] . ' ' : '';
            $output .= $config["phone"];
          $output .= '</a>';
        endif;
        if($config["mobile"] !== ''):
          $output .= '<a href="tel:' . prefix_core_BaseFunctions::cleanPhoneNr($config["mobile"]) . '" class="call mobile_nr">';
            $output .= $config["labels"] && array_key_exists('mobile', $config["labels"]) && $config["labels"]["mobile"] !== '' ? $config["labels"]["mobile"] . ' ' : '';
            $output .= $config["mobile"];
          $output .= '</a>';
        endif;
        if($config["email"] !== ''):
          $output .= '<a href="mailto:' . $config["email"] . '" class="mail">';
            $output .= $config["labels"] && array_key_exists('email', $config["labels"]) && $config["labels"]["email"] !== '' ? $config["labels"]["email"] . ' ' : '';
            $output .= $config["email"];
          $output .= '</a>';
        endif;
      $output .= '</address>';

      return $output;
    }


    /* 3.9 DIVIDE HEADER FROM CONTENT
    /------------------------*/
    public static function Divider(int $divider = 1){
      if($divider === 1):
        return '<hr class="divider" />';
      endif;
    }


    /* 3.10 FOOTER MENU
    /------------------------*/
    public static function WP_FooterMenu(bool $active = true){
      if ( has_nav_menu( 'footermenu' ) && $active === true ) :
        echo '<nav>';
          echo wp_nav_menu([
            'menu_class'=> '',
            'menu_id' => 'menu_footer',
            'container'=> false,
            'depth' => 2,
            'theme_location' => 'footermenu'
          ]);
        echo '</nav>';
      endif;
    }


    /* 3.11 COPYRIGHT
    /------------------------*/
    public static function Copyright(string $cr = ""){
      // vars
      $output = '';

      $output .= '<span class="copyright">';
        $output .= $cr;
      $output .= '</span>';

      return $output;
    }


    /* 3.12 SOCIAL MEDIA
    /------------------------*/
    public static function SocialMedia(){
      // value fallback
      $sm = SELF::$template_socialmedia;
      // check if value given
      if($sm):
        // vars
        $output = '';

        $output .= '<ul class="socialmedia">';
          foreach ($sm as $type => $link) {
            if($link !== ""):
              $output .= '<li class="' . $type . '">';
                $output .= '<a href="' . $link . '" target="_blank">';

                switch ($type) {
                  case 'facebook':
                    $output .= '<svg role="img" xmlns="http://www.w3.org/2000/svg" width="47.904" height="47.904" viewBox="0 0 47.904 47.904"><title>Facebook</title><path d="M115.4,91.452a23.952,23.952,0,1,0-27.695,23.661V98.376H81.628V91.452h6.082V86.175c0-6,3.576-9.319,9.047-9.319a36.824,36.824,0,0,1,5.362.468v5.894H99.1c-2.975,0-3.9,1.846-3.9,3.74v4.493h6.643l-1.062,6.924H95.195v16.737A23.958,23.958,0,0,0,115.4,91.452Z" transform="translate(-67.5 -67.5)"/><path id="Pfad_3025" data-name="Pfad 3025" d="M194.294,160.308l1.062-6.924h-6.643v-4.493c0-1.894.928-3.74,3.9-3.74h3.02v-5.894a36.823,36.823,0,0,0-5.362-.468c-5.471,0-9.047,3.316-9.047,9.319v5.277h-6.082v6.924h6.082v16.737a24.2,24.2,0,0,0,7.485,0V160.308Z" transform="translate(-161.018 -129.433)" fill="none"/></svg>';
                    break;
                  case 'instagram':
                    $output .= '<svg role="img" xmlns="http://www.w3.org/2000/svg" width="47.567" height="47.567" viewBox="0 0 47.567 47.567"><title>Instagram</title><g transform="translate(-449.718 -904.492)"><g transform="translate(461.245 912.44)"><path d="M53.225,40.97A12.257,12.257,0,1,0,65.482,53.227,12.271,12.271,0,0,0,53.225,40.97Zm0,20.293a8.036,8.036,0,1,1,8.036-8.036A8.045,8.045,0,0,1,53.225,61.263Z" transform="translate(-40.968 -37.391)"/><path d="M122.016,28.251a3.093,3.093,0,1,0,2.189.906A3.107,3.107,0,0,0,122.016,28.251Z" transform="translate(-96.988 -28.251)"/></g><path d="M34.44,0H13.127A13.142,13.142,0,0,0,0,13.127V34.44A13.142,13.142,0,0,0,13.127,47.567H34.44A13.142,13.142,0,0,0,47.567,34.44V13.127A13.142,13.142,0,0,0,34.44,0Zm8.907,34.44a8.917,8.917,0,0,1-8.907,8.906H13.127A8.916,8.916,0,0,1,4.22,34.44V13.127A8.917,8.917,0,0,1,13.127,4.22H34.44a8.917,8.917,0,0,1,8.907,8.906V34.44Z" transform="translate(449.718 904.492)"/></g></svg>
';
                    break;
                  case 'linkedin':
                    $output .= '<svg role="img" xmlns="http://www.w3.org/2000/svg" width="47.57" height="47.57" viewBox="0 0 47.57 47.57"><title>Linkedin</title><path d="M112.058,113H71.515A3.463,3.463,0,0,0,68,116.409v40.752a3.465,3.465,0,0,0,3.515,3.409h40.543a3.462,3.462,0,0,0,3.512-3.409V116.409A3.461,3.461,0,0,0,112.058,113ZM82.422,152.82H75.234V131.342h7.188Zm-3.592-24.413h-.05a4.106,4.106,0,1,1,.05,0Zm29.5,24.413h-7.185V141.327c0-2.888-1.043-4.857-3.639-4.857a3.927,3.927,0,0,0-3.688,2.612,4.828,4.828,0,0,0-.237,1.739v12H86.4s.1-19.465,0-21.478h7.185v3.045a7.137,7.137,0,0,1,6.475-3.551c4.728,0,8.274,3.069,8.274,9.668Zm-14.8-18.365a.7.7,0,0,1,.047-.067v.067Zm0,0" transform="translate(-68 -113)" /></svg>';
                    break;
                  case 'twitter':
                    $output .= '<svg role="img" xmlns="http://www.w3.org/2000/svg" width="56.848" height="46.201" viewBox="0 0 56.848 46.201"><title>Twitter</title><path d="M558.058,616.059c21.454,0,33.185-17.773,33.185-33.185q0-.757-.033-1.508a23.718,23.718,0,0,0,5.818-6.04,23.257,23.257,0,0,1-6.7,1.836,11.7,11.7,0,0,0,5.129-6.451,23.389,23.389,0,0,1-7.406,2.831,11.673,11.673,0,0,0-19.875,10.636,33.115,33.115,0,0,1-24.041-12.186,11.674,11.674,0,0,0,3.611,15.571,11.578,11.578,0,0,1-5.282-1.459c0,.049,0,.1,0,.149a11.667,11.667,0,0,0,9.357,11.433,11.644,11.644,0,0,1-5.268.2,11.675,11.675,0,0,0,10.9,8.1,23.4,23.4,0,0,1-14.486,4.992,23.705,23.705,0,0,1-2.782-.161,33.015,33.015,0,0,0,17.879,5.239" transform="translate(-540.179 -569.858)"/></svg>';
                    break;

                  default:
                    // code...
                    break;
                }

                $output .= '</a>';
              $output .= '</li>';
            endif;
          }
        $output .= '</ul>';

        return $output;
      endif;
    }


    /* 3.13 CONTACT BLOCK
    /------------------------*/
    public static function ContactBlock(array $contacts = array()){
      if($contacts):
        // vars
        $output = '';

        $output .= '<ul class="contact-block">';
          foreach ($contacts as $type => $link) {
            if($link !== ""):
              $output .= '<li class="' . $type . '">';

                switch ($type) {
                  case 'phone':
                    $output .= '<a href="tel:' . $link . '">';
                      $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.967 20"><path data-name="Pfad 18" d="M16.62 15.596l-2.567-2.021a1.251 1.251 0 0 0-1.726.169c-.463.526-.758.884-.863.968-.779.779-2.4-.358-4.125-2.083S4.498 9.303 5.274 8.524c.084-.084.442-.4.968-.863a1.252 1.252 0 0 0 .169-1.726L4.393 3.368a1.279 1.279 0 0 0-1.473-.379A10.787 10.787 0 0 0 .647 4.651c-1.852 1.874.463 6.125 4.5 10.186s8.313 6.377 10.165 4.5a10.755 10.755 0 0 0 1.662-2.269 1.232 1.232 0 0 0-.358-1.472z"/><path data-name="Pfad 19" d="M10.306 6.567a.832.832 0 0 0-1.031.863.878.878 0 0 0 .631.758 2.543 2.543 0 0 1 1.877 1.873.809.809 0 0 0 .758.631.833.833 0 0 0 .863-1.031 3.962 3.962 0 0 0-1.1-1.979 4.134 4.134 0 0 0-2-1.116z"/><path data-name="Pfad 20" d="M15.862 10.945a.835.835 0 0 0 .884-.905A7.415 7.415 0 0 0 9.97 3.264a.834.834 0 0 0-.147 1.662 5.649 5.649 0 0 1 3.6 1.684 5.857 5.857 0 0 1 1.684 3.6.764.764 0 0 0 .757.737z"/><path data-name="Pfad 21" d="M16.872 3.116A10.725 10.725 0 0 0 9.653.004a.817.817 0 0 0-.842.884.84.84 0 0 0 .8.779 9.039 9.039 0 0 1 8.692 8.713.831.831 0 0 0 1.662-.042 10.6 10.6 0 0 0-3.093-7.219z"/></svg>';
                    $output .= '</a>';
                    break;
                  case 'mail':
                    $output .= '<a href="mailto:' . $link . '">';
                      $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.945 13.874"><defs></defs><g id="noun_Mail_1425138_000000" transform="translate(-7 -976.362)"><path id="Pfad_6" data-name="Pfad 6" class="cls-1" d="M8.251,976.362l10.222,8.554,10.222-8.554H8.251ZM7,977.4v11.748l6.762-6.086L7,977.4Zm22.945,0-6.762,5.661,6.762,6.086V977.4ZM15,984.108l-6.812,6.128H28.753l-6.812-6.128-2.952,2.468a.8.8,0,0,1-1.034,0L15,984.108Z" transform="translate(0 0)"/></g></svg>';
                    $output .= '</a>';
                    break;
                  case 'whatsapp':
                    $output .= '<a href="https://api.whatsapp.com/send?phone=' . $link . '">';
                      $output .= '<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>WhatsApp</title><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>';
                    $output .= '</a>';
                    break;

                  default:
                    // code...
                    break;
                }

                $output .= '</a>';
              $output .= '</li>';
            endif;
          }
        $output .= '</ul>';

        return $output;
      endif;
    }


    /* 3.14 ICON BLOCK
    /------------------------*/
    /**
      * @param array $icons: list of svg icons with link, css clss or additional attributes
      * options are svg, link, target, class, attr
        * @param array $settings: container settings
        * options are class, attr
      * @return string ul with icons
    */
    public static function IconBlock(array $icons = array(), array $settings = array()){
      if(!empty($icons)):
        // container settings
        $container_css = array_key_exists('class', $settings) ? ' ' . $settings['class'] : '';
        $container_attr = '';
        if(array_key_exists('attr', $settings) && is_array($settings['attr'])):
          foreach ($settings['attr'] as $key => $single_attr) {
            $container_attr .= ' ' . $key . '="' . $single_attr . '"';
          }
        endif;
        // output
        $output = '<ul class="iconlist' . $container_css . '"' . $container_attr . '>';
        foreach ($icons as $key => $icon) {
          // svg container - if link is given use a tag else span
          if(array_key_exists('link', $icon)):
            $target = array_key_exists('target', $icon) ? ' target="_' . $icon['target'] . '"' : '';
            $tag = 'a href="' . $icon['link'] . '"' . $target;
          else:
            $tag = 'span';
          endif;
          // additional to the svg container
          $icon_css = array_key_exists('class', $icon) ? ' class="' . $icon['class'] . '"' : '';
          $icon_title = array_key_exists('title', $icon) ? ' title="' . $icon['title'] . '"' : '';
          $icon_attr = '';
          if(array_key_exists('attr', $icon) && is_array($icon['attr'])):
            foreach ($icon['attr'] as $key => $attr) {
              $icon_attr .= ' ' . $key . '="' . $attr . '"';
            }
          endif;
          // output
          $output .= '<li>';
            $output .= '<' . $tag . $icon_css . $icon_attr . $icon_title . ' tabindex="0">';
              $output .= $icon['svg'];
              if(array_key_exists('label', $icon)):
                $output .= '<label>' . $icon['label'] . '</label>';
              endif;
          $output .= '</' . $tag . '>';
          $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
      else:
        $debug_errors['template'][] = "Icon Block is empty";
      endif;
    }


    /* 3.15 CONTENT BLOCK
    /------------------------*/
    public static function ContentBlock(string $content = ''){
      if ($content !== ''):
        echo do_shortcode(str_replace("'", '"', $content));
      endif;
    }


    /* 3.16 BODY CSS
    /------------------------*/
    public static function BodyCSS(){
      $obj = get_queried_object();
      $page_id = get_queried_object_id();
      // base classes
      $classes = 'frontend';
      $classes .= $obj && array_key_exists('post_type', $obj) ? ' pt-' . $obj->post_type : '';
      $classes .= $obj && array_key_exists('name', $obj) ? ' pt-' . $obj->name : '';
      $classes .= prefix_template::$template_coloring !== '' ? ' ' . prefix_template::$template_coloring : '';
      $classes .= ' ' . prefix_template::CheckSticky(prefix_template::$template_header_sticky);
      $classes .= prefix_template::$template_header_stickyload == 1 ? ' sticky_onload' : '';
      // dark mode
      if($page_id > 0):
        $get_options = get_post_meta($page_id, 'template_page_options', true);
        $options = unserialize($get_options);
        $classes .= $options && in_array('darkmode', $options) && SELF::$template_page_options['darkmode'] == 1 ? ' dark' : '';
      endif;
      // apply filter
      $classes .= ' ' . apply_filters( 'template_BodyCSS', $classes );
      // return classes
      echo $classes;
    }


    public static function postMeta($pt, $options){
      $output = '';
      // if metablock is active for post type
      if(in_array($pt, array('page', 'post')) && SELF::$template_page_metablock[$pt] == 1 || !empty(SELF::$template_page_metablockAdds) && in_array($pt, SELF::$template_page_metablockAdds)):
        // post meta blog
        if(!in_array('date', $options) || !in_array('time', $options) || !in_array('author', $options)):
          $output .= '<div class="post-meta">';
          // meta date and time
          if(!in_array('date', $options) || !in_array('time', $options)):
            $output .= '<time class="entry-date" datetime="' . get_the_time( 'c' ) . '">';
            // if date active
            if(!in_array('date', $options)):
              $output .= '<span class="date">' . get_the_date(prefix_template::$template_blog_dateformat) . '</span>';
            endif;
            // if time active
            if(!in_array('time', $options)):
              $output .= '<span class="time">' . get_the_date('G:i') . '</span>';
            endif;
            $output .= '</time>';
          endif;
          // meta author
          if(!in_array('author', $options)):
            $output .= '<span class="entry-author">' . get_the_author() . '</span>';
          endif;
          $output .= '</div>';
        endif;
      endif;
      return $output;
    }


}
