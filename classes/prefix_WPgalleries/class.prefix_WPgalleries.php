<?php
/**
 * WORDPRESS img galleries CPT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.1
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
  1.4 CREATE CPT
  1.5 CREATE META BOX
2.0 FUNCTIONS
  2.1 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
  2.2 REDIRECT CPT TO HOME SITE
  2.3 ENQUEUE BACKEND SCRIPTS/STYLES
3.0 OUTPUT
  3.1 SINGLE OUTPUT
  3.2 BLOCK OUTPUT
=======================================================*/


class prefix_WPgalleries extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars (if configuration file is missing or broken)
      * @param static array $WPgalleries_slug: post type slug
      * @param static bool $WPgalleries_detailpage: enable detail page for cpt
      * @param static bool $WPgalleries_assets: enable js/css register
      * @param static array $WPgalleries_others: support for other CPTs
      * @param static bool $WPgalleries_cpt: Activate galleries CPT
      * @param static string $WPgalleries_cpt_label: CPT Label
      * @param static string $WPgalleries_cpt_rewrite: CPT rewrite
      * @param static string $WPgalleries_cpt_icon: CPT backend icon
      * @param static string $WPgalleries_cpt_support: CPT support
      * @param static array $WPgslleries_cpt_tax: CPT taxonomies
    */
    static $WPgalleries_slug         = 'galleries';
    static $WPgalleries_others       = array();
    static $WPgalleries_detailpage   = false;
    static $WPgalleries_assets       = true;
    static $WPgalleries_noimg_files  = array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg');
    static $WPgalleries_cpt          = false;
    static $WPgalleries_cpt_label    = 'galleries';
    static $WPgalleries_cpt_rewrite  = 'galleries';
    static $WPgalleries_cpt_icon     = "dashicons-format-gallery";
    static $WPgalleries_cpt_support  = array( 'title' );
    static $WPgalleries_cpt_tax      = array();


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // register cpt and redirect pages
      if(SELF::$WPgalleries_cpt === true):
        add_action( 'init', array( $this, 'WPgalleries_register_cpt' ) );
        // redirect detail page
        if(SELF::$WPgalleries_detailpage == false):
          add_action( 'template_redirect', array( $this, 'WPgalleries_redirect_cpt' ) );
        endif;
      endif;
      // add class assets
      if(SELF::$WPgalleries_assets !== false):
        // add_action('wp_enqueue_scripts', array( $this, 'WPgalleries_frontend_enqueue_scripts_and_styles' ) );
        add_action('admin_enqueue_scripts', array( $this, 'WPgalleries_backend_enqueue_scripts_and_styles' ) );
      endif;
      // shortcodes
      add_shortcode( 'galleries', array( $this, 'WPgalleries_GetgalleriesBlock' ) );
      // metabox for img selection
      add_action( 'add_meta_boxes', array( $this, 'WPgalleries_Metabox' ) );
      // update custom fields
      add_action('save_post', array( $this, 'WPgalleries_meta_Save' ),  10, 2 );
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


    /* 1.4 CREATE CPT
    /------------------------*/
    public function WPgalleries_register_cpt() {
      $labels = array(
          'name' => __( SELF::$WPgalleries_cpt_label, 'WPgalleries' ),
      );
      $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'description' => SELF::$WPgalleries_cpt_label,
          'show_in_rest' => true,
          'supports' => SELF::$WPgalleries_cpt_support,
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => SELF::$WPgalleries_cpt_icon,
          'show_in_nav_menus' => false,
          'publicly_queryable' => false,
          'exclude_from_search' => true,
          'has_archive' => false,
          'query_var' => true,
          'can_export' => true,
          'rewrite' => false,
          'capability_type' => 'post'
      );
      if(SELF::$WPgalleries_detailpage !== false):
        $args['exclude_from_search'] = false;
        $args['has_archive'] = true;
        $args['publicly_queryable'] = true;
        $args['show_in_nav_menus'] = true;
        $args['rewrite'] = array('slug' => SELF::$WPgalleries_cpt_rewrite);
      endif;
      register_post_type( SELF::$WPgalleries_slug, $args );
      // add given taxonomies
      if(!empty(SELF::$WPgalleries_cpt_tax)):
        PARENT::register_cpt_taxonomy("galleries", SELF::$WPgalleries_cpt_tax);
      endif;
    }


    /* 1.5 CREATE META BOX
    /------------------------*/
    function WPgalleries_Metabox() {
      // combine VARS
      $WPgalleries_allow = array_merge(array(SELF::$WPgalleries_slug), SELF::$WPgalleries_others);
      // register meta box for all selected post types
      foreach( $WPgalleries_allow as $post_type ){
        add_meta_box(
            SELF::$WPgalleries_cpt_rewrite,
            SELF::$WPgalleries_cpt_label,
            array($this, 'WPgalleries_selection'),
            $post_type,
            'normal',
            'high'
        );
      }
    }




  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/

    /* 2.1 ACTIVATE CONTAINER CSS CLASS FOR HEADER/FOOTER
    /------------------------*/
    private function updateVars(){
      // get configuration
      global $configuration;
      // if configuration file exists && class-settings
      if($configuration && array_key_exists('WPgalleries', $configuration)):
        // class configuration
        $myConfig = $configuration['WPgalleries'];
        // update vars
        SELF::$WPgalleries_cpt = array_key_exists('cpt', $myConfig) ? $myConfig['cpt'] : SELF::$WPgalleries_cpt;
        SELF::$WPgalleries_cpt_label = array_key_exists('label', $myConfig) ? $myConfig['label'] : SELF::$WPgalleries_cpt_label;
        SELF::$WPgalleries_cpt_rewrite = array_key_exists('rewrite', $myConfig) ? $myConfig['rewrite'] : SELF::$WPgalleries_cpt_rewrite;
        SELF::$WPgalleries_cpt_icon = array_key_exists('icon', $myConfig) ? $myConfig['icon'] : SELF::$WPgalleries_cpt_icon;
        SELF::$WPgalleries_cpt_support = array_key_exists('support', $myConfig) ? $myConfig['support'] : SELF::$WPgalleries_cpt_support;
        SELF::$WPgalleries_cpt_tax = array_key_exists('taxanomies', $myConfig) ? $myConfig['taxanomies'] : SELF::$WPgalleries_cpt_tax;
        SELF::$WPgalleries_detailpage = array_key_exists('detailpage', $myConfig) ? $myConfig['detailpage'] : SELF::$WPgalleries_detailpage;
        SELF::$WPgalleries_assets = array_key_exists('assets', $myConfig) ? $myConfig['assets'] : SELF::$WPgalleries_assets;
        SELF::$WPgalleries_others = array_key_exists('others', $myConfig) ? $myConfig['others'] : SELF::$WPgalleries_others;
      endif;
    }


    /* 2.2 REDIRECT CPT TO HOME SITE
    /------------------------*/
    public function WPgalleries_redirect_cpt() {
      $queried_post_type = get_query_var('post_type');
      if ( 'galleries' ==  $queried_post_type ) {
        $redirect_url = home_url()."/";
        wp_redirect( $redirect_url );
        exit;
      }
    }


    /* 2.3 ENQUEUE BACKEND SCRIPTS/STYLES
    /------------------------*/
    function WPgalleries_backend_enqueue_scripts_and_styles(){
      $class_path = get_stylesheet_directory_uri() . '/config/classes/prefix_WPgalleries/';
      wp_enqueue_script('backend/WPgalleries-script', $class_path . 'WPgalleries-backend.js', false, null);
      // Define path for ajax requests
      $backend_ajax_action_file = $class_path . 'ajax.php';
      wp_localize_script( 'backend/WPgalleries-script', 'WPimg_Ajax', $backend_ajax_action_file );
    }


    /* 2.4 SAVE METABOXES
    /------------------------*/
    public function WPgalleries_meta_Save($post_id) {
      // get alloed post types
      $WPgalleries_allow = array_merge(array(SELF::$WPgalleries_slug), SELF::$WPgalleries_others);
      foreach( $WPgalleries_allow as $post_type ){
        if(isset( $_POST[$post_type] )):
          //Not save if the user hasn't submitted changes
          if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
            return;
          endif;
          // Verifying whether input is coming from the proper form
          if ( ! wp_verify_nonce ( $_POST['WPgalleries_images'] ) ):
            return;
          endif;
          // Making sure the user has permission
          if( 'post' == $_POST[$post_type] ):
            if( ! current_user_can( 'edit_post', $post_id ) ):
              return;
            endif;
          endif;
        endif;
        // save dominant color
        update_post_meta($post_id, 'WPgalleries_images', $_POST['WPgalleries_images']);
      }
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 SINGLE OUTPUT
    /------------------------*/
    /**
      * @param int $id: page id
      * @return string single galleries output
    */
    public static function galleriesSingle(int $id = 0){
      // vars
      $output = '';
      $img = wp_get_attachment_image_src($id, 'full');
      $img_src = $img[0];
      $file_type = get_post_mime_type($id);
      // additional attributes
      $attr = '';
      $attr .= ' alt="' . get_post_meta( $id, '_wp_attachment_image_alt', true) . '"';
      $attr .= ' title="' . get_the_title( $id) . '"';
      $attr .= ' class="' . str_replace(array("/", "+"), array("-", "-"), $file_type) . '"';
      // fallbacks for non img files
      if(in_array($file_type, SELF::$WPgalleries_noimg_files)):
        $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/video.png');
        $attr .= ' style="background-image:url(\'' . $icon . '\');"';
        $img_src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
      elseif($file_type == 'audio/mpeg'):
        $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/audio.png');
        $attr .= ' style="background-image:url(\'' . $icon . '\');"';
        $img_src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
      elseif($file_type == 'image/svg+xml'):
        $attr .= ' style="background-image:url(\'' . $full_image_url[0] . '\');"';
        $img_src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
      endif;
      // output
      $output .= '<div>';
        $output .= '<img src="' . $img_src . '" data-id="' . $id . '"' . $attr . '>';
      $output .= '</div>';

      return $output;
    }


    /* 3.2 BLOCK OUTPUT
    /------------------------*/
    /**
      * @param int $id: page id
      * @return string single galleries output
    */
    public static function WPgalleries_GetgalleriesBlock($atts){
      // vars
      $output = '';
      $container_id = PARENT::ShortID(10, 'letters');
      $config = shortcode_atts( array(
        'id' => '0',
        'layout' => 'grid',
        'css' => ''
      ), $atts );
      // check if shortcode has images selection or a single gallery
      if (strpos($config['id'], ',') !== false):
        $selection = PARENT::AttrToArray($config['id']);
      else:
        $get_selection = get_post_meta($config['id'], 'WPgalleries_images', true);
        $selection = PARENT::AttrToArray($get_selection);
      endif;

      // count entries
      $sum = count($selection);
      // output
      if($sum > 0):
        $output .= '<div class="galleries-block layout-' . $config['layout'] . ' ' . $config['css'] . '" data-id="' . $container_id . '" data-layout="' . $config['layout'] . '">';
            if($config['layout'] == "swiper" || $config['layout'] == "fullscreen"):
              $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="60.043" height="113.137" viewBox="0 0 60.043 113.137"><path d="M30.457,114.121a3.473,3.473,0,0,1-4.912-4.912l50.635-50.64L25.545,7.929a3.475,3.475,0,0,1,4.917-4.912l53.089,53.1a3.476,3.476,0,0,1,0,4.917Z" transform="translate(84.57 115.138) rotate(180)"/></svg>';
              $output .= '<span class="galleries-arrow back hidden">' . $icon .'</span>';
              $output .= '<span class="galleries-arrow next hidden">' . $icon .'</span>';
            endif;
            $output .= '<div class="galleries-inner">';
            foreach ($selection as $key => $id) {
              $output .= SELF::galleriesSingle($id);
            }
            $output .= '</div>';
        $output .= '</div>';
      else:
        $debug_errors['WPgalleries'][] = 'Gallery with ID ' . $config['id'] . ' images missing';
      endif;

      return $output;
    }


    /* 3.3 METABOX
    /------------------------*/
    function WPgalleries_selection($object) {
        // vars
        $output = '';
        global $post;
        wp_enqueue_media();
        $selection = get_post_meta($post->ID, 'WPgalleries_images', true);
        $selected_images = '';
        if($selection):
          $selected_images .= '<ul class="galleriesImages_list">';
          $gallery = explode(',', $selection);
          foreach ($gallery as $image) {
            $selected_images .= '<li><div class="galleriesImages_container"><span class="remove_image">';
            $selected_images .= '<img id="' . esc_attr($image) . '" src="' . wp_get_attachment_thumb_url($image) . '">';
            $selected_images .= '</span></div></li>';
          }
          $selected_images .= '</ul>';
        endif;

        // output
        echo '<div class="wrap" id="WPgalleries">';
          echo '<input id="galleriesImages" type="hidden" name="WPgalleries_images" value="' . esc_attr($selection) . '" />';
          echo '<button class="wp-media ajax-action" data-action="WPgalleries">' . __('Select images','WPgalleries') . '</button>';
          echo '<span id="galleriesImages_src">' . $selected_images . '</span>';
        echo '</div>';
    }


}
