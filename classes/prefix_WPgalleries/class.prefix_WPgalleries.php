<?php
/**
 * WORDPRESS img galleries CPT
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.1
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
  2.4 ENQUEUE FRONTEND SCRIPTS/STYLES
  2.5 SAVE METABOXES
  2.6 ENQUEUE FRONTEND POPUP SCRIPTS/STYLES
3.0 OUTPUT
  3.1 SINGLE OUTPUT
  3.2 BLOCK OUTPUT
=======================================================*/


class prefix_WPgalleries {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars (if configuration file is missing or broken)
      * @param private array $WPgalleries_slug: post type slug
      * @param private int $WPgalleries_detailpage: enable detail page for cpt
      * @param private int $WPgalleries_assets: enable js/css register
      * @param private int $WPgalleries_assets_popup: enable js/css register for img popup
      * @param private array $WPgalleries_others: support for other CPTs
      * @param private int $WPgalleries_cpt: Activate galleries CPT
      * @param private string $WPgalleries_cpt_label: CPT Label
      * @param private string $WPgalleries_cpt_rewrite: CPT rewrite
      * @param private string $WPgalleries_cpt_icon: CPT backend icon
      * @param private string $WPgalleries_cpt_support: CPT support
      * @param private array $WPgslleries_cpt_tax: CPT taxonomies
    */
    private $WPgalleries_slug               = 'galleries';
    private $WPgalleries_others             = array();
    private $WPgalleries_detailpage         = 0;
    private $WPgalleries_assets             = 1;
    private $WPgalleries_assets_popup       = 1;
    private STATIC $WPgalleries_noimg_files = array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg');
    private $WPgalleries_cpt                = 1;
    private $WPgalleries_cpt_label          = 'galleries';
    private $WPgalleries_cpt_rewrite        = 'galleries';
    private $WPgalleries_cpt_icon           = "dashicons-format-gallery";
    private $WPgalleries_cpt_support        = array( 'title' );
    private $WPgalleries_cpt_tax            = array();


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // register cpt and redirect pages
      if($this->WPgalleries_cpt == 1):
        add_action( 'init', array( $this, 'WPgalleries_register_cpt' ) );
        // redirect detail page
        if($this->WPgalleries_detailpage == 0):
          add_action( 'template_redirect', array( $this, 'WPgalleries_redirect_cpt' ) );
        endif;
      endif;
      // add class assets
      if($this->WPgalleries_assets == 1):
        add_action('wp_enqueue_scripts', array( $this, 'WPgalleries_frontend_enqueue_scripts_and_styles' ) );
      endif;
      add_action('admin_enqueue_scripts', array( $this, 'WPgalleries_backend_enqueue_scripts_and_styles' ) );
      // add class assets
      if($this->WPgalleries_assets_popup == 1):
        add_action('wp_enqueue_scripts', array( $this, 'WPgalleries_frontend_popup_enqueue_scripts_and_styles' ) );
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
    static $classtitle = 'Galleries';
    static $classkey = 'WPgalleries';
    static $backend = array(
      "cpt" => array(
        "label" => "Register CPT",
        "type" => "switchbutton"
      ),
      "label" => array(
        "label" => "CPT label",
        "type" => "text"
      ),
      "rewrite" => array(
        "label" => "CPT rewrite",
        "type" => "text"
      ),
      "icon" => array(
        "label" => "CPT icon",
        "type" => "text"
      ),
      "support" => array(
        "label" => "CPT support",
        "type" => "array_addable"
      ),
      "taxanomies" => array(
        "label" => "Add taxonomies",
        "type" => "array_addable"
      ),
      "detailpage" => array(
        "label" => "Actiate detail page",
        "type" => "switchbutton"
      ),
      "assets" => array(
        "label" => "Embed assets",
        "type" => "switchbutton"
      ),
      "assets_popup" => array(
        "label" => "Embed popup assets",
        "type" => "switchbutton"
      ),
      "others" => array(
        "label" => "Activate on other post types",
        "type" => "array_addable"
      )
    );


    /* 1.4 CREATE CPT
    /------------------------*/
    function WPgalleries_register_cpt() {
      $labels = array(
          'name' => __( $this->WPgalleries_cpt_label, 'WPgalleries' ),
      );
      $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'description' => __( $this->WPgalleries_cpt_label, 'WPgalleries' ),
          'show_in_rest' => true,
          'supports' => $this->WPgalleries_cpt_support,
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => $this->WPgalleries_cpt_icon,
          'show_in_nav_menus' => false,
          'publicly_queryable' => false,
          'exclude_from_search' => true,
          'has_archive' => false,
          'query_var' => true,
          'can_export' => true,
          'rewrite' => false,
          'capability_type' => 'post'
      );
      if($this->WPgalleries_detailpage == 1):
        $args['exclude_from_search'] = false;
        $args['has_archive'] = true;
        $args['publicly_queryable'] = true;
        $args['show_in_nav_menus'] = true;
        $args['rewrite'] = array('slug' => $this->WPgalleries_cpt_rewrite);
      endif;
      register_post_type( $this->WPgalleries_slug, $args );
      // add given taxonomies
      if(!empty($this->WPgalleries_cpt_tax)):
        prefix_core_BaseFunctions::register_cpt_taxonomy("galleries", $this->WPgalleries_cpt_tax);
      endif;
    }


    /* 1.5 CREATE META BOX
    /------------------------*/
    function WPgalleries_Metabox() {
      // combine VARS
      $WPgalleries_allow = array_merge(array($this->WPgalleries_slug), $this->WPgalleries_others);
      // register meta box for all selected post types
      foreach( $WPgalleries_allow as $post_type ){
        add_meta_box(
            $this->WPgalleries_cpt_rewrite,
            __( $this->WPgalleries_cpt_label, 'WPgalleries' ),
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
        $this->WPgalleries_cpt = array_key_exists('cpt', $myConfig) ? $myConfig['cpt'] : $this->WPgalleries_cpt;
        $this->WPgalleries_cpt_label = array_key_exists('label', $myConfig) ? $myConfig['label'] : $this->WPgalleries_cpt_label;
        $this->WPgalleries_cpt_rewrite = array_key_exists('rewrite', $myConfig) ? $myConfig['rewrite'] : $this->WPgalleries_cpt_rewrite;
        $this->WPgalleries_cpt_icon = array_key_exists('icon', $myConfig) ? $myConfig['icon'] : $this->WPgalleries_cpt_icon;
        $this->WPgalleries_cpt_support = array_key_exists('support', $myConfig) ? $myConfig['support'] : $this->WPgalleries_cpt_support;
        $this->WPgalleries_cpt_tax = array_key_exists('taxanomies', $myConfig) ? $myConfig['taxanomies'] : $this->WPgalleries_cpt_tax;
        $this->WPgalleries_detailpage = array_key_exists('detailpage', $myConfig) ? $myConfig['detailpage'] : $this->WPgalleries_detailpage;
        $this->WPgalleries_assets = array_key_exists('assets', $myConfig) ? $myConfig['assets'] : $this->WPgalleries_assets;
        $this->WPgalleries_assets_popup = array_key_exists('assets_popup', $myConfig) ? $myConfig['assets_popup'] : $this->WPgalleries_assets_popup;
        $this->WPgalleries_others = array_key_exists('others', $myConfig) ? $myConfig['others'] : $this->WPgalleries_others;
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
      $class_path = get_template_directory_uri() . '/classes/prefix_WPgalleries/';
      // css
      wp_enqueue_script( 'backend/WPgalleries-styles' );
      wp_enqueue_style('backend/WPgalleries-styles', $class_path . 'backend.css', false, 1.0);
      // js
      wp_enqueue_script('backend/WPgalleries-script', $class_path . 'WPgalleries-backend.js', false, 1.0);
      // Define path for ajax requests
      $backend_ajax_action_file = $class_path . 'ajax.php';
      wp_localize_script( 'backend/WPgalleries-script', 'WPgalleries_Ajax', $backend_ajax_action_file );
    }


    /* 2.4 ENQUEUE FRONTEND SCRIPTS/STYLES
    /------------------------*/
    function WPgalleries_frontend_enqueue_scripts_and_styles(){
      $class_path = get_template_directory_uri() . '/classes/prefix_WPgalleries/';
      // css
      wp_enqueue_script( 'frontend/WPgalleries-styles' );
      wp_enqueue_style('frontend/WPgalleries-styles', $class_path . 'WPgalleries.css', false, 1.0);
      // js
      wp_enqueue_script('frontend/WPgalleries-script', $class_path . 'WPgalleries.js', false, 1.0);
    }


    /* 2.5 SAVE METABOXES
    /------------------------*/
    public function WPgalleries_meta_Save($post_id) {
      // get alloed post types
      $WPgalleries_allow = array_merge(array($this->WPgalleries_slug), $this->WPgalleries_others);
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
        if(isset($_POST['WPgalleries_images'])):
          update_post_meta($post_id, 'WPgalleries_images', $_POST['WPgalleries_images']);
        endif;
      }
    }


    /* 2.6 ENQUEUE FRONTEND POPUP SCRIPTS/STYLES
    /------------------------*/
    function WPgalleries_frontend_popup_enqueue_scripts_and_styles(){
      $class_path = get_template_directory_uri() . '/classes/prefix_WPgalleries/popup/';
      // css
      wp_enqueue_script( 'frontend/WPgalleries-popup-styles' );
      wp_enqueue_style('frontend/WPgalleries-popup-styles', $class_path . 'popup.css', false, 1.0);
      // js
      wp_enqueue_script('frontend/WPgalleries-popup-script', $class_path . 'popup.js', false, 1.0);
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
      $container_id = prefix_core_BaseFunctions::ShortID(10, 'letters');
      $config = shortcode_atts( array(
        'id' => '0',
        'layout' => 'grid',
        'css' => '',
        'step' => '4',
        'mstep' => '2',
        'sort' => '',
        'popup' => ''
      ), $atts );
      // check if shortcode has images selection or a single gallery
      if (strpos($config['id'], ',') !== false):
        $selection = prefix_core_BaseFunctions::AttrToArray($config['id']);
      else:
        $get_selection = get_post_meta($config['id'], 'WPgalleries_images', true);
        $selection = prefix_core_BaseFunctions::AttrToArray($get_selection);
      endif;
      // random sort
      if($config['sort'] == "random"):
        shuffle($selection);
      endif;
      // popup
      if($config['popup'] !== ""):
        $config['css'] .= ' popup-img';
      endif;

      // count entries
      $sum = count($selection);
      // output
      if($sum > 0):
        $output .= '<div class="galleries-block layout-' . $config['layout'] . ' ' . $config['css'] . '" data-id="' . $container_id . '" data-layout="' . $config['layout'] . '" data-steps="' . $config['step'] . '" data-stepsmobile="' . $config['mstep'] . '" data-sum="' . $sum . '">';
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
        // call wp media
        wp_enqueue_media();
        // svg
        $remove = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24.9 24.9" xml:space="preserve"><rect x="-3.7" y="10.9" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -5.1549 12.4451)" fill="#000" width="32.2" height="3"/><rect x="10.9" y="-3.7" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -5.1549 12.4451)" fill="#000" width="3" height="32.2"/></svg>';
        // selected images
        $selection = get_post_meta($post->ID, 'WPgalleries_images', true);
        $selected_images = '';
        $selected_images .= '<ul class="galleriesImages_list">';
          if($selection):
            $gallery = explode(',', $selection);
            foreach ($gallery as $image) {
              $selected_images .= '<li data-id="' . $image . '"><div class="galleriesImages_container">';
                $selected_images .= '<span class="remove_image">' . $remove . '</span>';
                $selected_images .= '<img id="' . esc_attr($image) . '" src="' . wp_get_attachment_thumb_url($image) . '">';
              $selected_images .= '</div></li>';
            }
          endif;
        $selected_images .= '</ul>';

        // output
        echo '<div class="wrap" id="WPgalleries">';
          echo '<input id="galleriesImages" type="hidden" name="WPgalleries_images" value="' . esc_attr($selection) . '" />';
          echo '<button class="wp-media ajax-action" data-action="WPgalleries">' . __('Select images','WPgalleries') . '</button>';
          echo $selected_images;
        echo '</div>';
    }


}
