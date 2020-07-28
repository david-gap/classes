<?php
/**
 * WORDPRESS SEO ADDON
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.1.2
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
  2.2 FAVICON
  2.3 XML SITEMAP
3.0 OUTPUT
  3.1 GOOGLE TAG MANAGER / ANALYTICS
  3.2 DATA StRUCTURE FOR GOOGLE
=======================================================*/


class prefix_WPseo {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars (if configuration file is missing or broken)
      * @param private int $WPseo_logo: Logo
      * @param private string $WPseo_tracking: Google tracking code (analytics or tag manager)
      * @param private int $WPseo_favicon: fav icon link
      * @param private int $WPseo_icon: default screen icon
      * @param private int $WPseo_icon_72: apple screen icon 72
      * @param private int $WPseo_icon_114: apple screen icon 114
      * @param private int $WPseo_datastructure: turn datastructure on/off
      * @param private array $WPseo_datastructure_add: additional structure attributes
    */
    private $WPseo_logo               = 0;
    private static $WPseo_tracking    = '';
    private $WPseo_favicon            = 0;
    private $WPseo_icon               = 0;
    private $WPseo_icon_72            = 0;
    private $WPseo_icon_114           = 0;
    private $WPseo_datastructure      = 0;
    private $WPseo_datastructure_add  = array(
      "type" => "Website"
    );
    private $WPseo_address = array(
      "company" => "Company",
      "street" => "Street",
      "street2" => "Street 2",
      "postalCode" => "Postal Code",
      "country" => "Country",
      "city" => "City",
      "phone" => "0041",
      "email" => "info@mail.com"
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
    static $classtitle = 'WP SEO';
    static $classkey = 'seo';
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
        "type" => "switchbutton"
      ),
      "address" =>  array(
        "label" => "Data-Structure Address",
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
          "email" => array(
            "label" => "E-Mail",
            "type" => "text"
          )
        )
      ),
      "data-structure-add" => array(
        "label" => "Additional Data",
        "type" => "array_addable",
        "value" => array(
          "key" => array(
            "label" => "Data key",
            "type" => "text"
          ),
          "value" => array(
            "label" => "Data value",
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
      if($configuration && array_key_exists('seo', $configuration)):
        // class configuration
        $myConfig = $configuration['seo'];
        // update vars
        $this->WPseo_logo = array_key_exists('logo', $myConfig) ? $myConfig['logo'] : $this->WPseo_logo;
        SELF::$WPseo_tracking = array_key_exists('google-tracking', $myConfig) ? $myConfig['google-tracking'] : SELF::$WPseo_tracking;
        $this->WPseo_favicon = array_key_exists('favicon', $myConfig) ? $myConfig['favicon'] : $this->WPseo_favicon;
        $this->WPseo_icon = array_key_exists('apple-touch-icon', $myConfig) ? $myConfig['apple-touch-icon'] : $this->WPseo_icon;
        $this->WPseo_icon_72 = array_key_exists('apple-touch-icon-72', $myConfig) ? $myConfig['apple-touch-icon-72'] : $this->WPseo_icon_72;
        $this->WPseo_icon_114 = array_key_exists('apple-touch-icon-114', $myConfig) ? $myConfig['apple-touch-icon-114'] : $this->WPseo_icon_114;
        $this->WPseo_datastructure = array_key_exists('data-structure', $myConfig) ? $myConfig['data-structure'] : $this->WPseo_datastructure;
        $this->WPseo_datastructure_add = array_key_exists('data-structure-add', $myConfig) ? $myConfig['data-structure-add'] : $this->WPseo_datastructure_add;
        $this->WPseo_address = array_key_exists('address', $myConfig) ? $myConfig['address'] : $this->WPseo_address;
      endif;
    }


    /* 2.2 FAVICON
    /------------------------*/
    public static function FavIcon(){
      if($this->WPseo_favicon !== 0):
        $get_fav = wp_get_attachment_image_src($this->WPseo_favicon, 'full');
        echo '<link rel="icon" href="' . $get_fav[0] . '" />';
      endif;
      // apple touch icons
      if($this->WPseo_icon !== 0):
        $get_touch_1 = wp_get_attachment_image_src($this->WPseo_icon, 'full');
        echo '<link rel="apple-touch-icon" href="' . $get_touch_1[0] . '" />';
      endif;
      if($this->WPseo_icon_72 !== 0):
        $get_touch_2 = wp_get_attachment_image_src($this->WPseo_icon_72, 'full');
        echo '<link rel="apple-touch-icon" sizes="72x72" href="' . $get_touch_2[0] . '" />';
      endif;
      if($this->WPseo_icon_114 !== 0):
        $get_touch_3 = wp_get_attachment_image_src($this->WPseo_icon_114, 'full');
        echo '<link rel="apple-touch-icon" sizes="114x114" href="' . $get_touch_3[0] . '" />';
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
      if(1 == get_option('blog_public')):
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
      endif;

      echo $output;
    }


    /* 3.2 DATA STRUCTURE FOR GOOGLE
    /------------------------*/
    public static function dataStructure(){
      // vars
      $output = '';

      if($this->WPseo_datastructure == 1):
        $address = $this->WPseo_address;
        $additionals = $this->WPseo_datastructure_add;
        $logo = $this->WPseo_logo !== 0 ? wp_get_attachment_image_src($this->WPseo_logo, 'full') : '';

        $output .= '<script type="application/ld+json">';
          $output .= '{';
            $output .= '"@context": "http://schema.org",';
            $output .= !empty($logo) ? '"image": "' . $logo[0] . '",' : '';
            $output .= key_exists('company' , $address) ? '"name": "' . $address["company"] . '",' : '';
            $output .= key_exists('phone' , $address) ? '"telephone": "' . $address["phone"] . '",' : '';
            $output .= key_exists('email' , $address) ? '"email": "' . $address["email"] . '",' : '';
            $output .= '"address": {';
              $output .= '"@type": "PostalAddress",';
              $output .= key_exists('street' , $address) ? '"streetAddress": "' . $address["street"] . '",' : '';
              $output .= key_exists('city' , $address) ? '"addressLocality": "' . $address["city"] . '",' : '';
              $output .= key_exists('country' , $address) ? '"addressCountry": "' . $address["country"] . '",' : '';
              $output .= key_exists('postalCode' , $address) ? '"postalCode": "' . $address["postalCode"] . '"' : '';
            $output .= '},';
            $output .= '"url": "' . get_bloginfo('url') . '"';
            if($additionals):
              $count_adds = count($additionals);
              $adds = 1;
              foreach ($additionals as $key => $additional) {
                $output .= ',';
                $output .= '"' . $additional["key"] . '": "' . $additional["value"] . '"';
                $adds++;
              }
            endif;
          $output .= '}';
        $output .= '</script>';

        echo $output;

      endif;
    }



}
