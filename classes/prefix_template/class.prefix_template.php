<?
/**
 *
 *
 * Basic template parts
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
=======================================================*/


class prefix_template {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

  /* 1.1 CONFIGURATION
  /------------------------*/
  /**
    * default vars
    * @param static int $template_container: activate container
    * @param static string $template_coloring: template coloring (dark/light)
    * @param static bool $template_ph_active: activate placeholder
    * @param static bool $template_ph_address: placeholder show address block
    * @param static string $template_ph_custom: placeholder custom content
    * @param static array $template_address: address block content
    * @param static array $template_socialmedia: social media
    * @param static bool $template_header_divider: Activate header divider
    * @param static bool $template_header_sticky: activate sticky header
    * @param static bool $template_header_dmenu: Activate header hamburger for desktop
    * @param static string $template_header_custom:  Custom header html
    * @param static array $template_header_sort: Sort and activate blocks inside header builder
    * @param static string $template_header_logo_link: Logo link with wordpress fallback
    * @param static array $template_header_logo_d: desktop logo configuration
    * @param static array $template_header_logo_m: mobile logo configuration
    * @param static bool $template_page_active: activate page options
    * @param static array $template_page_options: show/hide template elements
    * @param static array $template_page_additional: additional custom fields template elements
    * @param static bool $template_footer_active: activate footer
    * @param static string $template_footer_cr: copyright text
    * @param static string $template_footer_custom: custom html
    * @param static array $template_footer_sort: Sort and activate blocks inside footer builder
  */
  static $template_container        = 1;
  static $template_coloring         = "light";
  static $template_ph_active        = true;
  static $template_ph_address       = true;
  static $template_ph_custom        = "";
  static $template_address          = array(
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
  static $template_socialmedia      = array(
    "facebook" => "",
    "instagram" => ""
  );
  static $template_contactblock     = array(
    "phone" => "",
    "mail" => "",
    "whatsapp" => ""
  );
  static $template_header_divider   = 1;
  static $template_header_sticky    = 1;
  static $template_header_dmenu     = 1;
  static $template_header_custom    = "";
  static $template_header_sort      = array(
    "logo" => 1,
    "menu" => 1,
    "socialmedia" => 0,
    "custom" => 0
  );
  static $template_header_logo_link = "";
  static $template_header_logo_d    = array(
    "img" => "",
    "width" => "",
    "height" => "",
    "alt" => ""
  );
  static $template_header_logo_m    = array(
    "img" => "",
    "width" => "",
    "height" => "",
    "alt" => ""
  );
  static $template_page_active      = 1;
  static $template_page_options     = array(
    "header" => 1,
    "title" => 1,
    "sidebar" => 1,
    "footer" => 1
  );
  static $template_page_additional  = array();
  static $template_footer_active    = 1;
  static $template_footer_cr        = "";
  static $template_footer_custom    = "";
  static $template_footer_sort      = array(
    "menu" => 1,
    "socialmedia" => 1,
    "copyright" => 1,
    "address" => 1,
    "custom" => 0
  );


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
  }

  /* 1.3 BACKEND ARRAY
  /------------------------*/
  static $classtitle = 'Template';
  static $classkey = 'template';
  static $backend = array(
    "container" => array(
      "label" => "Activate container",
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
          "label" => "Sticky Menu",
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
            "logo" => array(
              "label" => "Logo",
              "type" => "switchbutton"
            ),
            "menu" => array(
              "label" => "Menu",
              "type" => "switchbutton"
            ),
            "socialmedia" => array(
              "label" => "social media",
              "type" => "switchbutton"
            ),
            "custom" => array(
              "label" => "Custom",
              "type" => "switchbutton"
            )
          )
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
        "options" => array(
          "label" => "Page options",
          "type" => "multiple",
          "value" => array(
            "header" => array(
              "label" => "Hide header",
              "type" => "switchbutton"
            ),
            "title" => array(
              "label" => "Hide title",
              "type" => "switchbutton"
            ),
            "sidebar" => array(
              "label" => "Hide sidebar",
              "type" => "switchbutton"
            ),
            "footer" => array(
              "label" => "Hide footer",
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
            )
          )
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
        SELF::$template_container = array_key_exists('container', $myConfig) ? $myConfig['container'] : SELF::$template_container;
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
          SELF::$template_header_dmenu = array_key_exists('desktop_menu', $header) ? $header['desktop_menu'] : SELF::$template_header_dmenu;
          SELF::$template_header_custom = array_key_exists('custom', $header) ? $header['custom'] : SELF::$template_header_custom;
          SELF::$template_header_sort = array_key_exists('sort', $header) ? $header['sort'] : SELF::$template_header_sort;
          SELF::$template_header_logo_link = array_key_exists('logo_link', $header) ? $header['logo_link'] : SELF::$template_header_logo_link;
          SELF::$template_header_logo_d = array_key_exists('logo_desktop', $header) ? $header['logo_desktop'] : SELF::$template_header_logo_d;
          SELF::$template_header_logo_m = array_key_exists('logo_mobile', $header) ? $header['logo_mobile'] : SELF::$template_header_logo_m;
        endif;
        if($configuration && array_key_exists('page', $myConfig)):
          $page = $myConfig['page'];
          SELF::$template_page_active = array_key_exists('active', $page) ? $page['active'] : SELF::$template_page_active;
          SELF::$template_page_options = array_key_exists('options', $page) ? array_merge(SELF::$template_page_options, $page['options']) : SELF::$template_page_options;
          SELF::$template_page_additional = array_key_exists('additional', $page) ? $page['additional'] : SELF::$template_page_additional;
        endif;
        if($configuration && array_key_exists('footer', $myConfig)):
          $footer = $myConfig['footer'];
          SELF::$template_footer_active = array_key_exists('active', $footer) ? $footer['active'] : SELF::$template_ph_custom;
          SELF::$template_footer_cr = array_key_exists('copyright', $footer) ? $footer['copyright'] : SELF::$template_ph_custom;
          SELF::$template_footer_custom = array_key_exists('custom', $footer) ? $footer['custom'] : SELF::$template_ph_custom;
          SELF::$template_footer_sort = array_key_exists('sort', $footer) ? $footer['sort'] : SELF::$template_ph_custom;
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
      $options = $_POST['template_page_options'] && $_POST['template_page_options'] !== '' ? serialize($_POST['template_page_options']) : '';
      update_post_meta($post_id, 'template_page_options', $options);
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 SORTABLE HEADER CONTENT
    /------------------------*/
    static public function HeaderContent(){
      // vars
      $order = SELF::$template_header_sort;

      foreach ($order as $key => $value) {
        switch ($key) {
          case 'menu':
            echo $value == 1 ? SELF::WP_MainMenu(SELF::$template_header_dmenu) : '';
            break;
          case 'logo':
            echo $value == 1 ? SELF::Logo(SELF::$template_header_logo_link, SELF::$template_header_logo_d, SELF::$template_header_logo_m) : '';
            break;
          case 'socialmedia':
            echo $value == 1 ? SELF::SocialMedia(SELF::$template_socialmedia) : '';
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
    }


    /* 3.2 SORTABLE FOOTER CONTENT
    /------------------------*/
    static public function FooterContent(){
      // vars
      $order = SELF::$template_footer_sort;

      foreach ($order as $key => $value) {
        switch ($key) {
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
            echo $value == 1 ? SELF::Socialmedia(SELF::$template_socialmedia) : '';
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
          echo '<p><b>' . __( 'Hide Elements', 'template' ) . '</b></p>';
          echo '<ul>';
            foreach (SELF::$template_page_options as $key => $value) {
              // check if option is active
              if($value !== false):
                $active = prefix_core_BaseFunctions::setChecked($key, $options);
                echo '<li><label><input type="checkbox" name="template_page_options[]" value="' . $key . '" ' . $active . '>' . __( 'Hide ' . $key, 'template' ) . '</label></li>';
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
        echo '</div>';
    }


    /* 3.4 PAGE OPTIONS
    /------------------------*/
    static public function PageOptions($id) {
        // vars
        $output = array();
        $get_options = get_post_meta($id, 'template_page_options', true);
        $options = $get_options && $get_options !== '' ? unserialize($get_options) : array();
        // check activity
        foreach (SELF::$template_page_options as $key => $value) {
          // check if option is active
          if($value !== false && in_array($key, $options)):
            $output[] = $key;
          endif;
        }
        foreach (SELF::$template_page_additional as $key => $value) {
          // check if additional option are available
          if(in_array($key, $options)):
            $output[] = $key;
          endif;
        }
        // output
        return $output;
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
      $add_desktop = $mobile['img'] !== "" ? 'class="desktop"' : '';
      $add_container = $desktop['img'] == "" && $mobile['img'] == "" ? ' text_logo' : '';
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
    public static function WP_MainMenu(int $active = 1){
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
        $output .= wp_nav_menu([
          'container_class'=> $menu_active,
          'menu_id' => 'menu_main',
          'container'=> 'div',
          'container_id' => 'menu-main-container',
          'depth' => 2,
          'theme_location' => 'mainmenu'
        ]);
        $output .= '<button class="hamburger ' . $hamburger_active . '" aria-label="Main Menu">';
          $output .= '<span>&nbsp;</span>';
        $output .= '</button>';
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
        $output .= $config["company"] !== '' ? '<b rel="me" class="company">' . $config["labels"]["company"] . $config["company"] . '</b>' : '';
        $output .= $config["street"] !== '' ? '<span class="street">' . $config["labels"]["street"] . $config["street"] . '</span>' : '';
        $output .= $config["street2"] !== '' ? '<span class="street_add">' . $config["labels"]["street2"] . $config["street2"] . '</span>' : '';
        $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? '<span class="location">' : '';
          $output .= $config["postalCode"] !== '' ? '<span class="postalcode">' . $config["labels"]["postalCode"] . $config["postalCode"] . '</span>' : '';
          $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? ' ' : '';
          $output .= $config["city"] !== '' ? '<span class="city">' . $config["labels"]["city"] . $config["city"] . '</span>' : '';
        $output .= $config["postalCode"] !== '' && $config["city"] !== '' ? '</span>' : '';
        if($config["phone"] !== ''):
          $update_phone = str_replace(array('+', ' '), array('00', ''), $config["phone"]);
          if(strpos($update_phone, '(') !== false):
            $update_phone_l = substr($update_phone, 0, 1);
            $phone_text_between = prefix_core_BaseFunctions::getBetween($update_phone , "(", ")");
            $clean_phone = $update_phone_l == "(" ? $phone_text_between . str_replace(array('(', ')'), array('', ''), $update_phone) : str_replace(array('(' . $phone_text_between . ')'), array(''), $update_phone);
          endif;
          $output .= '<a href="tel:' . $clean_phone . '" class="call phone_nr">' . $config["labels"]["phone"] . $config["phone"] . '</a>';
        endif;
        if($config["mobile"] !== ''):
          $update_mobile = str_replace(array('+', ' '), array('00', ''), $config["mobile"]);
          if(strpos($update_mobile, '(') !== false):
            $update_mobile_l = substr($update_mobile, 0, 1);
            $mobile_text_between = prefix_core_BaseFunctions::getBetween($update_mobile , "(", ")");
            $clean_mobile = $update_mobile_l == "(" ? $mobile_text_between . str_replace(array('(', ')'), array('', ''), $update_mobile) : str_replace(array('(' . $mobile_text_between . ')'), array(''), $update_mobile);
          endif;
          $output .= '<a href="tel:' . $clean_mobile . '" class="call mobile_nr">' . $config["labels"]["mobile"] . $config["mobile"] . '</a>';
        endif;
        $output .= $config["email"] !== '' ? '<a href="mailto:' . $config["email"] . '" class="mail">' . $config["labels"]["email"] . $config["email"] . '</a>' : '';
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
    public static function SocialMedia(array $sm = array()){
      // value fallback
      $sm = empty($sm) ? SELF::$template_socialmedia : $sm;
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
                    $output .= '<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Facebook</title><path d="M23.9981 11.9991C23.9981 5.37216 18.626 0 11.9991 0C5.37216 0 0 5.37216 0 11.9991C0 17.9882 4.38789 22.9522 10.1242 23.8524V15.4676H7.07758V11.9991H10.1242V9.35553C10.1242 6.34826 11.9156 4.68714 14.6564 4.68714C15.9692 4.68714 17.3424 4.92149 17.3424 4.92149V7.87439H15.8294C14.3388 7.87439 13.8739 8.79933 13.8739 9.74824V11.9991H17.2018L16.6698 15.4676H13.8739V23.8524C19.6103 22.9522 23.9981 17.9882 23.9981 11.9991Z"/></svg>';
                    break;
                  case 'instagram':
                    $output .= '<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Instagram</title><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>';
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


}
