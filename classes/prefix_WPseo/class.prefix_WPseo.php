<?php
/**
 * WORDPRESS SEO ADDON
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.0
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
  2.2 FAVICON
  2.3 XML SITEMAP
3.0 OUTPUT
  3.1 GOOGLE TAG MANAGER / ANALYTICS
  3.2 DATA StRUCTURE FOR GOOGLE
=======================================================*/


class prefix_WPseo extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars (if configuration file is missing or broken)
      * @param static string $WPseo_logo: Logo
      * @param static string $WPseo_tracking: Google tracking code (analytics or tag manager)
      * @param static string $WPseo_favicon: fav icon link
      * @param static string $WPseo_icon: default screen icon
      * @param static string $WPseo_icon_72: apple screen icon 72
      * @param static string $WPseo_icon_114: apple screen icon 114
      * @param static bool $WPseo_datastructure: turn datastructure on/off
      * @param static array $WPseo_datastructure_add: additional structure attributes
    */
    static $WPseo_logo               = '';
    static $WPseo_tracking           = '';
    static $WPseo_favicon            = '';
    static $WPseo_icon               = '';
    static $WPseo_icon_72            = '';
    static $WPseo_icon_114           = '';
    static $WPseo_datastructure      = true;
    static $WPseo_datastructure_add  = array(
      "type" => "Website"
    );
    static $WPseo_address = array(
      "company" => "Company",
      "street" => "Street",
      "street2" => "Street 2",
      "postalCode" => "Postal Code",
      "country" => "Country",
      "city" => "City",
      "phone" => "0041",
      "mobile" => "0041 2",
      "email" => "info@dmili.com"
    );


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // add google tracking code
      add_action( 'wp_head', array( $this, 'GoogleTracking' ), 1 );
      // add google data structure
      add_action( 'wp_head', array( $this, 'dataStructure' ), 1 );
      // add fav icons
      add_action( 'wp_head', array( $this, 'FavIcon' ) );
    }

    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $backend = array(
      "logo" => array(
        "label" => "Logo",
        "type" => "img"
      ),
      "favicon" => array(
        "label" => "Favicon",
        "type" => "img"
      ),
      "apple-touch-icon" => array(
        "label" => "Apple touch icon (57px)",
        "type" => "img"
      ),
      "apple-touch-icon-72" => array(
        "label" => "Apple touch icon (72px)",
        "type" => "img"
      ),
      "apple-touch-icon-114" => array(
        "label" => "Apple touch icon (114px)",
        "type" => "img"
      ),
      "data-structure" => array(
        "label" => "Activate Data-Structures",
        "type" => "checkbox",
        "value" => ""
      ),
      "data-structure-add" => array(
        "label" => "Additional Data",
        "type" => "multiple",
        "value" => array(
          "data-structure" => array(
            "label" => "Main Type",
            "type" => "select"
          )
        )
      ),
      "address" =>  array(
        "label" => "Address block",
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
            "label" => "Additional Street",
            "type" => "text"
          ),
          "zip" => array(
            "label" => "Zip Code",
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
      if($configuration && array_key_exists('seo', $configuration)):
        // class configuration
        $myConfig = $configuration['seo'];
        // update vars
        SELF::$WPseo_logo = array_key_exists('logo', $myConfig) ? $myConfig['logo'] : SELF::$WPseo_logo;
        SELF::$WPseo_tracking = array_key_exists('google-tracking', $myConfig) ? $myConfig['google-tracking'] : SELF::$WPseo_tracking;
        SELF::$WPseo_favicon = array_key_exists('favicon', $myConfig) ? $myConfig['favicon'] : SELF::$WPseo_favicon;
        SELF::$WPseo_icon = array_key_exists('apple-touch-icon', $myConfig) ? $myConfig['apple-touch-icon'] : SELF::$WPseo_icon;
        SELF::$WPseo_icon_72 = array_key_exists('apple-touch-icon-72', $myConfig) ? $myConfig['apple-touch-icon-72'] : SELF::$WPseo_icon_72;
        SELF::$WPseo_icon_114 = array_key_exists('apple-touch-icon-114', $myConfig) ? $myConfig['apple-touch-icon-114'] : SELF::$WPseo_icon_114;
        SELF::$WPseo_datastructure = array_key_exists('data-structure', $myConfig) ? $myConfig['data-structure'] : SELF::$WPseo_datastructure;
        SELF::$WPseo_datastructure_add = array_key_exists('data-structure-add', $myConfig) ? $myConfig['data-structure-add'] : SELF::$WPseo_datastructure_add;
        SELF::$WPseo_address = array_key_exists('address', $myConfig) ? $myConfig['address'] : SELF::$WPseo_address;
      endif;
    }


    /* 2.2 FAVICON
    /------------------------*/
    public static function FavIcon(){
      if(SELF::$WPseo_favicon !== ""):
        echo '<link rel="icon" href="' . SELF::$WPseo_favicon . '" />';
      endif;
      // apple touch icons
      if(SELF::$WPseo_icon !== ""):
        echo '<link rel="apple-touch-icon" href="' . SELF::$WPseo_icon . '" />';
      endif;
      if(SELF::$WPseo_icon_72 !== ""):
        echo '<link rel="apple-touch-icon" sizes="72x72" href="' . SELF::$WPseo_icon_72 . '" />';
      endif;
      if(SELF::$WPseo_icon_114 !== ""):
        echo '<link rel="apple-touch-icon" sizes="114x114" href="' . SELF::$WPseo_icon_114 . '" />';
      endif;
    }


    /* 2.3 XML SITEMAP
    /------------------------*/
    // public function xmlSitemap(string $run = ""){
    //   // check if rebuild xml been triggered
    //   if($run == "update_xml"):
    //     // build xml
    //     $output = '';
    //     $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    //
    //     // query
    //     global $wpdb;
    //     $args = array(
    //       'post_type'=> array("post", "page"),
    //       'post_status'=>'publish',
    //       'posts_per_page'=> '-1'
    //     );
    //     $wp_query = new WP_Query( $args );
    //
    //     if ( $wp_query->have_posts() ) :
    //       while ( $wp_query->have_posts() ) : $wp_query->the_post();
    //         // single output
    //         $output .= '<url>';
    //           $output .= '<loc>' . get_permalink() . '</loc>';
    //           $output .= '<lastmod>' . get_the_modified_date('Y-d-m g:i') . '</lastmod>';
    //           // $output .= '<changefreq>monthly</changefreq>';
    //           // $output .= '<priority>0.8</priority>';
    //         $output .= '</url>';
    //       endwhile;
    //       wp_reset_postdata();
    //     endif;
    //
    //     $output .= '</urlset>';
    //     return $output;
    //     // DEBUG: return print_r($wp_query);
    //
    //   endif;
    // }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 GOOGLE TAG MANAGER / ANALYTICS
    /------------------------*/
    /**
      * @param bool $body: set to true if you are inserting GTM code inside body tag
      * @return string tracking code
    */
    public static function GoogleTracking(bool $body = false){
      // vars
      $trackingcode = SELF::$WPseo_tracking;
      $output = '';

      if(strpos($trackingcode, 'GTM-') === 0 && $body === false):
        // GTM head
        $output .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':";
        $output .= "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],";
        $output .= "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=";
        $output .= "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);";
        $output .= "})(window,document,'script','dataLayer','" . $trackingcode . "');</script>";
      elseif(strpos($trackingcode, 'GTM-') === 0 && $body === true):
        // GTM body
        $output .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $trackingcode . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
      elseif(strpos($trackingcode, 'UA-') === 0 && $body === false):
        // analytics
        $output .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $trackingcode . '"></script>';
        $output .= '<script>';
        $output .= 'window.dataLayer = window.dataLayer || [];';
        $output .= 'function gtag(){dataLayer.push(arguments);}';
        $output .= 'gtag("js", new Date());';
        $output .= 'gtag("config", "' . $trackingcode . '");';
        $output .= '</script>';
      endif;

      echo $output;
    }


    /* 3.2 DATA STRUCTURE FOR GOOGLE
    /------------------------*/
    public static function dataStructure(){
      // vars
      $output = '';

      if(SELF::$WPseo_datastructure == true):
        $address = SELF::$WPseo_address;
        $additional = SELF::$WPseo_datastructure_add;
        $logo = SELF::$WPseo_logo;

        $output .= '<script type="application/ld+json">';
          $output .= '{';
            $output .= '"@context" : "http://schema.org",';
            $output .= !empty($logo) ? '"image" : "' . $logo . '",' : '';
            $output .= key_exists('company' , $address) ? '"name" : "' . $address["company"] . '",' : '';
            $output .= key_exists('phone' , $address) ? '"telephone" : "' . $address["phone"] . '",' : '';
            $output .= key_exists('email' , $address) ? '"email" : "' . $address["email"] . '",' : '';
            $output .= '"address" : {';
              $output .= '"@type" : "PostalAddress",';
              $output .= key_exists('street' , $address) ? '"streetAddress" : "' . $address["street"] . '",' : '';
              $output .= key_exists('city' , $address) ? '"addressLocality" : "' . $address["city"] . '",' : '';
              $output .= key_exists('country' , $address) ? '"addressCountry" : "' . $address["country"] . '",' : '';
              $output .= key_exists('postalCode' , $address) ? '"postalCode" : "' . $address["postalCode"] . '"' : '';
            $output .= '},';
            $output .= '"url" : "' . get_bloginfo('url') . '"';
            if($additional):
              $adds = 1;
              foreach ($additional as $key => $value) {
                $output .= $adds > 1 ? ',' : '';
                $output .= '"' . $key . '" : "' . $value . '",';
                $adds++;
              }
            endif;
          $output .= '}';
        $output .= '</script>';

        echo $output;

      endif;
    }



}