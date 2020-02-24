<?php
/**
 * WORDPRESS SEO ADDON
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.0.2
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
2.0 FUNCTIONS
  2.1 GET CONFIGURATION FORM CONFIG FILE
  2.2 REDIRECT CPT TO HOME SITE
3.0 OUTPUT
  3.1 SINGLE OUTPUT
  3.2 BLOCK OUTPUT
=======================================================*/


class prefix_WPnews extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars (if configuration file is missing or broken)
      * @param static string $WPnews_cpt_label: CPT Label
      * @param static string $WPnews_cpt_rewrite: CPT rewrite
      * @param static string $WPnews_cpt_icon: CPT backend icon
      * @param static string $WPnews_cpt_support: CPT support
      * @param static string $WPnews_block_label: Label for block
      * @param static int $WPnews_block_max: max entries on block list
      * @param static bool $WPnews_detailpage: enable detail page for cpt
    */
    static $WPnews_cpt_label    = 'Services';
    static $WPnews_cpt_rewrite  = 'services';
    static $WPnews_cpt_icon     = "dashicons-testimonial";
    static $WPnews_cpt_support  = array( 'title', 'editor' );
    static $WPnews_block_label  = 'News';
    static $WPnews_block_max    = -1;
    static $WPnews_detailpage   = false;


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars()
      // register cpt and redirect pages
      add_action( 'init', array( $this, 'WPnews_register_cpt_news' ) );
      // redirect detail page
      if(SELF::$WPnews_detailpage == false):
        add_action( 'template_redirect', array( $this, 'WPnews_redirect_cpt_news' ) );
      endif;
      // shortcodes
      add_shortcode( 'newsblock', array( $this, 'WPnews_GetNewsBlock' ) );
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
    public function WPnews_register_cpt_news() {
      $labels = array(
          'name' => __( SELF::$WPnews_cpt_label, 'WPNews' ),
      );
      $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'description' => SELF::$WPnews_cpt_label,
          'show_in_rest' => true,
          'supports' => SELF::$WPnews_cpt_support,
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => SELF::$WPnews_cpt_icon,
          'show_in_nav_menus' => false,
          'publicly_queryable' => false,
          'exclude_from_search' => true,
          'has_archive' => false,
          'query_var' => true,
          'can_export' => true,
          'rewrite' => false,
          'capability_type' => 'post'
      );
      if(SELF::$WPnews_detailpage !== false):
        $args['exclude_from_search'] = false;
        $args['has_archive'] = true;
        $args['publicly_queryable'] = true;
        $args['show_in_nav_menus'] = true;
        $args['rewrite'] = array('slug' => SELF::$WPnews_cpt_rewrite);
      endif;
      register_post_type( 'news', $args );
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
      if($configuration && array_key_exists('WPnews', $configuration)):
        // class configuration
        $myConfig = $configuration['WPnews'];
        // update vars
        SELF::$WPnews_cpt_label = array_key_exists('label', $myConfig) ? $myConfig['label'] : SELF::$WPnews_cpt_label;
        SELF::$WPnews_cpt_rewrite = array_key_exists('rewrite', $myConfig) ? $myConfig['rewrite'] : SELF::$WPnews_cpt_rewrite;
        SELF::$WPnews_cpt_icon = array_key_exists('icon', $myConfig) ? $myConfig['icon'] : SELF::$WPnews_cpt_icon;
        SELF::$WPServices_cpt_support = array_key_exists('support', $myConfig) ? $myConfig['support'] : SELF::$WPServices_cpt_support;
        SELF::$WPnews_block_label = array_key_exists('block_label', $myConfig) ? $myConfig['block_label'] : SELF::$WPnews_block_label;
        SELF::$WPnews_block_max = array_key_exists('block_max', $myConfig) ? $myConfig['block_max'] : SELF::$WPnews_block_max;
        SELF::$WPnews_detailpage = array_key_exists('detailpage', $myConfig) ? $myConfig['detailpage'] : SELF::$WPnews_detailpage;
      endif;
    }


    /* 2.2 REDIRECT CPT TO HOME SITE
    /------------------------*/
    public function WPnews_redirect_cpt_news() {
      $queried_post_type = get_query_var('post_type');
      if ( 'news' ==  $queried_post_type ) {
        $redirect_url = home_url()."/";
        wp_redirect( $redirect_url );
        exit;
      }
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 SINGLE OUTPUT
    /------------------------*/
    /**
      * @param int $id: page id
      * @return string single news output
    */
    public static function NewsSingle(int $id = 0){
      // vars
      $output = '';
      $output .= '<h5>';
        $output .= SELF::$WPnews_detailpage !== false ? '<a href="' . get_the_permalink($id) . '">' : '';
          $output .= get_the_title($id);
        $output .= SELF::$WPnews_detailpage !== false ? '</a>' : '';
      $output .= '</h5>';
      $output .= get_post_field('post_content', $id);

      return $output;
    }


    /* 3.2 BLOCK OUTPUT
    /------------------------*/
    /**
      * @param int $id: page id
      * @return string single news output
    */
    public static function WPnews_GetNewsBlock($atts){
      // vars
      $output = '';
      $config = shortcode_atts( array(
        'id' => '-1'
      ), $atts );
      // create array with all ids
      $ids = array();
      if($config['id'] == "-1"):
        // wp query
        $args = array(
          'post_type'=>'news',
          'post_status'=>'publish',
          'posts_per_page'=> SELF::$WPnews_block_max
        );
        $news_query = new WP_Query( $args );
        // get ids from WP query
        if ( $news_query->have_posts() ) :
          while ( $news_query->have_posts() ) : $news_query->the_post();
              $ids[] += get_the_ID();
          endwhile;
          // reset query
          wp_reset_postdata();
        endif;
      else:
        $ids = PARENT::AttrToArray($config['id']);
      endif;
      // count entries
      $sum = count($ids);
      // output
      if($sum > 0):
        $output .= '<section class="news-block">';
            $output .= SELF::$WPnews_block_label !== "" ? '<label tabindex="0">' . SELF::$WPnews_block_label . '</label>' : '';
            $output .= '<div>';
            foreach ($ids as $key => $id) {
              $output .= SELF::NewsSingle($id);
            }
            $output .= '<div>';
        $output .= '</section>';
      endif;

      return $output;
    }



}
