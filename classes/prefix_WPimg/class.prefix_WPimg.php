<?
/**
 *
 *
 *
 * Additional functions for WP Media
 * https://github.com/david-gap/classes
 * Author:      David Voglgsang
 * @version     1.3.1
 *
 * Change the $assets to false if you use your own backend.js and ajax file
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
  2.2 ENQUEUE FRONTEND SCRIPTS/STYLES
  2.3 ENQUEUE BACKEND SCRIPTS/STYLES
  2.4 ADD CUSTOM FIELDS
  2.5 ADD BACKEND SUB-PAGE
  2.6 SAVE METABOXES
  2.7 SAVE DOMINANT
  2.8 GET DOMINANT IMG COLOR
3.0 OUTPUT
  3.1 BACKEND SUB-PAGE
  3.2 GENERATE DOMINANT COLORS
  3.3 BACKEND META BOX OUTPUT
  3.4 LAZY LOADING
  3.5 CONTENT LAZY LOADING
  3.6 POP-UP CONTENT
  3.7 CONTENT LAZY LOADING
  3.8 SINGLE BLOCK
  3.9 LIST OUTPUT
=======================================================*/

class prefix_WPimg extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static boolable $WPimg_content: simple way to disable the lazy loading inside the_content
      * @param static boolable $WPimg_assets: include classes assets. Disable it if you use your own files
      * @param static boolable $WPimg_js_loading: lazy load with JS
      * @param static string $WPimg_parent_element: parent container typ (div, section)
      * @param static boolable $WPimg_popupContent: On klick open media in a lightbox
      * @param static array $WPimg_nocolor_files: exclude file types from dominant color generator
      * @param static string $WPimg_defaultcolor: default color
    */
    static $WPimg_content         = true;
    static $WPimg_assets          = true;
    static $WPimg_js_loading      = true;
    static $WPimg_parent_element  = 'div';
    static $WPimg_popupContent    = false;
    static $WPimg_nocolor_files   = array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg');
    static $WPimg_defaultcolor    = 'ffffff';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // lazy loading content img
      if(SELF::$WPimg_content !== false):
        add_filter('the_content', array( $this, 'ContentLazyLoading' ) );
      endif;
      // add custom fields
      add_action('add_meta_boxes', array( $this, 'meta_Attachments' ) );
      // update custom fields
      add_action('add_attachment', array( $this, 'meta_Attachments_Save' ),  10, 2 );
      add_action('edit_attachment', array( $this, 'meta_Attachments_Save' ),  10, 2 );
      // backend page
      add_action( 'admin_menu', array( $this, 'WPimg_backendPage' ) );
      // add class assets
      if(SELF::$WPimg_assets !== false):
        add_action('wp_enqueue_scripts', array( $this, 'WPimg_frontend_enqueue_scripts_and_styles' ) );
        add_action('admin_enqueue_scripts', array( $this, 'WPimg_backend_enqueue_scripts_and_styles' ) );
      endif;
      // shortcodes
      add_shortcode( 'gallery', array( $this, 'MyGallery' ) );
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

    /* 2.1 GET CONFIGURATION FORM CONFIG FILE
    /------------------------*/
    private function updateVars(){
      // get configuration
      global $configuration;
      // if configuration file exists && class-settings
      if($configuration && array_key_exists('prefix_WPimg', $configuration)):
        // class configuration
        $myConfig = $configuration['prefix_WPimg'];
        // update vars
        SELF::$WPimg_content = array_key_exists('WPimg_content', $myConfig) ? $myConfig['WPimg_content'] : SELF::$WPimg_content;
        SELF::$WPimg_assets = array_key_exists('WPimg_assets', $myConfig) ? $myConfig['WPimg_assets'] : SELF::$WPimg_assets;
        SELF::$WPimg_js_loading = array_key_exists('WPimg_js_loading', $myConfig) ? $myConfig['WPimg_js_loading'] : SELF::$WPimg_js_loading;
        SELF::$WPimg_parent_element = array_key_exists('WPimg_parent_element', $myConfig) ? $myConfig['WPimg_parent_element'] : SELF::$WPimg_parent_element;
        SELF::$WPimg_popupContent = array_key_exists('WPimg_popupContent', $myConfig) ? $myConfig['WPimg_popupContent'] : SELF::$WPimg_popupContent;
        SELF::$WPimg_nocolor_files = array_key_exists('WPimg_nocolor_files', $myConfig) ? $myConfig['WPimg_nocolor_files'] : SELF::$WPimg_nocolor_files;
        SELF::$WPimg_defaultcolor = array_key_exists('WPimg_defaultcolor', $myConfig) ? $myConfig['WPimg_defaultcolor'] : SELF::$WPimg_defaultcolor;
      endif;
    }


    /* 2.2 ENQUEUE FRONTEND SCRIPTS/STYLES
    /------------------------*/
    function WPimg_frontend_enqueue_scripts_and_styles() {
      $class_path = __DIR__ . '/';
      $backend_ajax_action_file = $class_path . 'ajax.php';
      // scripts
      wp_register_script('frontend/WPimg-script', $class_path . 'WPimg.js', false, array( 'jquery' ), true);
      wp_enqueue_script('frontend/WPimg-script');
      wp_localize_script('frontend/WPimg-script', 'WPimg_Ajax', $backend_ajax_action_file);
      // styles
      wp_enqueue_style('frontend/WPimg-styles', $class_path . 'WPimg.css', false, null);
    }


    /* 2.3 ENQUEUE BACKEND SCRIPTS/STYLES
    /------------------------*/
    function WPimg_backend_enqueue_scripts_and_styles(){
      $class_path = __DIR__ . '/';
      wp_enqueue_script('backend/WPimg-script', $class_path . 'backend-scripts.js', false, null);
      // Define path for ajax requests
      $backend_ajax_action_file = $class_path . 'ajax.php';
      wp_localize_script( 'backend/WPimg-script', 'WPimg_Ajax', $backend_ajax_action_file );
    }


    /* 2.4 ADD CUSTOM FIELDS
    /------------------------*/
    public function meta_Attachments() {
      add_meta_box (
        'WPimg_DominantColor',
        'WPimg fields',
        array( $this, 'meta_Attachments_Output' ),
        'attachment',
        'normal',
        'high'
      );
    }


    /* 2.5 ADD BACKEND SUB-PAGE
    /------------------------*/
    function WPimg_backendPage() {
      add_submenu_page(
          'upload.php',
          __('Dominant Color','WPimg'),
          __('Dominant Color','WPimg'),
          'WPimg',
          'WPimg-DominantColor',
          array( $this, 'backend_page' ),
          6
      );
      // give access to administrator
      $role_admin = get_role( 'administrator' );
      $role_admin->add_cap( 'WPimg' );
    }


    /* 2.6 SAVE METABOXES
    /------------------------*/
    public function meta_Attachments_Save($post_id) {
      if( isset( $_POST['attachment'] ) ):
        //Not save if the user hasn't submitted changes
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
          return;
        endif;
        // Verifying whether input is coming from the proper form
        if ( ! wp_verify_nonce ( $_POST['WPimg_DominantColor'] ) ):
          return;
        endif;
        // Making sure the user has permission
        if( 'post' == $_POST['attachment'] ):
          if( ! current_user_can( 'edit_post', $post_id ) ):
            return;
          endif;
        endif;
      endif;
      // save dominant color
      SELF::saveDominantColor($post_id);
    }

    /* 2.7 SAVE DOMINANT
    /------------------------*/
    public static function saveDominantColor(int $id = 0, bool $return = false){
      $file_type = get_post_mime_type($id);
      if(!in_array($file_type, SELF::$WPimg_nocolor_files)):
        $full_image_url = wp_get_attachment_image_src($id, 'full');
        $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
        $img_url = $thumb_image_url[0] ? $thumb_image_url[0] : $full_image_url[0];
        // check if file exists
        $check_file = PARENT::checkRemoteFile($img_url);
        if ($check_file == true):
          $color = SELF::IMGcolor($img_url);
        else:
          $color = 'file_missing';
        endif;
      else:
        $color = SELF::$WPimg_defaultcolor;
      endif;
      update_post_meta($id, 'WPimg_DominantColor', $color);
      if($return):
        return $color;
      endif;
    }


    /* 2.8 GET DOMINANT IMG COLOR
    /------------------------*/
    /**
      * @param int $img: img url
      * @param string $default: fallback color
      * @return string dominant color hex
    */
    public static function IMGcolor(string $img = '', string $default = ''){
      $default = $default == '' ? $default : SELF::$WPimg_defaultcolor;
      // check if given var is an image
      if(@exif_imagetype($img)):
        $img_type = getimagesize($img)[2];
        if ($img_type === 1) {
          // img is gif
          $image = imagecreatefromgif($img);
          // fallback if imh is transparent return default
          if (imagecolorsforindex($image, imagecolorstotal($image)-1)['alpha'] == 127) return $default;
        } else if ($img_type === 2) {
          // img is jpg
          $image = imagecreatefromjpeg($img);
        } else if ($img_type === 3) {
          // img is png
          $image = imagecreatefrompng($img);
          // fallback if imh is transparent return default
          if ((imagecolorat($image, 0, 0) >> 24) & 0x7F === 127) return $default;
        } else {
          // current img is not one of the default types (gif, jpg, png)
          return $default;
        }
        // return dominant color
        $color = imagecreatetruecolor(1, 1); // FIND DOMINANT COLOR
        imagecopyresampled($color, $image, 0,0,0,0,1,1, imagesx($image), imagesy($image));
        return dechex(imagecolorat($color, 0, 0)); // RETURN HEX COLOR
      else:
        // fallback for svg
        $file_type = pathinfo($img, PATHINFO_EXTENSION);
        if($file_type == 'svg'):
          $commands = array('convert ' . ($img) .' ' . '-format "%k" info:');
          $command = implode(' && ', $commands);
          $response = exec($command);
          return $response == 1 ? '000000' : 'ffffff';
        endif;
        // fallback - file is not a img
        return $default;
      endif;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 BACKEND SUB-PAGE
    /------------------------*/
    function backend_page() {
        if ( !current_user_can( 'WPimg' ) )  {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $output = '';
        $output .= '<div class="wrap" id="WPimg">';
          $output .= '<h1 class="wp-heading-inline">' . __('Dominant Color','WPimg') . '</h1>';
          $output .= '<button class="page-title-action ajax-action" data-action="DominantColors">' . __('Generate dominant colors','WPimg') . '</button>';
          $output .= '<table class="wp-list-table widefat fixed striped pages hidden">';
            $output .= '<thead>';
              $output .= '<tr>';
                $output .= '<td>';
                  $output .= __('IMG color','WPimg');
                $output .= '</td>';
                $output .= '<td>';
                  $output .= __('Image / ID / Title','WPimg');
                $output .= '</td>';
              $output .= '</tr>';
            $output .= '</thead>';
            $output .= '<tbody id="the-list" class="ui-sortable">';
            $output .= '</tbody>';
          $output .= '</table>';
          $output .= '<div class="log"></div>';
        $output .= '</div>';
        echo $output;
    }


    /* 3.2 GENERATE DOMINANT COLORS
    /------------------------*/
    public static function generateDominantColors(){
      $args = array(
        'post_type'=> 'attachment',
        'post_status' => 'publish',
        'post_status' => 'inherit',
        'post_mime_type' => array('image', 'application/pdf', 'video', 'audio/mpeg'),
        'posts_per_page' => 10,
        'meta_key'     => 'WPimg_DominantColor',
        'meta_value'   => '',
        'meta_compare' => 'not exists'
      );
      $wp_query = new WP_Query($args);
      $output = '';
      if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post();
          // save dominant color
          $new_color = SELF::saveDominantColor(get_the_ID(), true);
          // get img
          $full_image_url = wp_get_attachment_image_src(get_the_ID(), 'full');
          // output
          $output .= '<tr>';
            $output .= '<td style="background-color: #' . $new_color . '">';
              $output .= '&nbsp;';
              $output .= $new_color == 'file_missing' ? __( 'Current image file is missing', 'WPimg' ) : '';
            $output .= '</td>';
            $output .= '<td>';
              $output .= '<img src="' . $full_image_url[0] . '" width="50" height="50" style="object-fit: cover;"> ';
              $output .= get_the_ID() . ': ' . get_the_title();
            $output .= '</td>';
          $output .= '</tr>';
        endwhile;
        wp_reset_query();
        wp_reset_postdata();
      else:
        $output = 'stop';
      endif;
      return $output;
    }


    /* 3.3 BACKEND META BOX OUTPUT
    /------------------------*/
    public function meta_Attachments_Output($post) {
      $content = get_post_meta($post->ID, 'WPimg_DominantColor', true);
      $output = '';
      $output .= '<table class="backend-metabox-output">';
        $output .= '<tr>';
          $output .= '<td>';
            $output .= '<label for="WPimg_DominantColor">' . __( 'Dominant color', 'WPimg' ) . '</label>';
          $output .= '</td>';
          $output .= '<td>';
            $output .= '<input type="text" disabled name="WPimg_DominantColor" value="' . $content . '">';
          $output .= '</td>';
        $output .= '</tr>';
      $output .= '</table>';
      echo $output;
    }


    /* 3.4 LAZY LOADING
    /------------------------*/
    /**
      * @param int $id: img id
      * @param string $attr: other img attributes
      * @param string $css: css class
      * @return string img tag with all attributes and dominant bg color
    */
    public static function getIMG(int $id = 0, string $attr = '', string $css = ''){
      $output = '';
      $additional = $attr;

      if($id > 0):
        // img alt attribute
        $img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
        $img_title = get_the_title( $id);
        $alt = $img_alt ? $img_alt : $img_title;
        // add file type as css class
        $file_type = get_post_mime_type($id);
        $css .= ' ' . str_replace(array("/", "+"), array("-", "-"), $file_type);
        // get img url
        $full_image_url = wp_get_attachment_image_src($id, 'full');
        $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
        $img_url = $thumb_image_url[0] ? $thumb_image_url[0] : $full_image_url[0];
        // fallback for missing srcset
        $sizes = wp_get_attachment_image_sizes( $id );
        $srcset = wp_get_attachment_image_srcset( $id );
        // srcset fallback
        if(!$srcset):
          $srcset = '';
          // get srcset only if type is img
          if($file_type == 'image/jpeg'):
            $attachment_meta = wp_get_attachment_metadata( $id );
            $image_name = basename( get_attached_file( $id ) );
            $image_path = str_replace($image_name , '', $full_image_url[0]);
            $count = 0;
            foreach ($attachment_meta["sizes"] as $key => $value) {
              if($count > 0):
                $srcset .= ', ';
              endif;
              $srcset .= $image_path . $value["file"] . ' ' . $value["width"] . 'w';
              $count++;
            }
          endif;
        endif;
        // img fallback
        $src_fb = $img_url;
        $fallback_bg = '';
        if(in_array($file_type, SELF::$WPimg_nocolor_files)):
          $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/video.png');
          $fallback_bg .= ' background-image:url(\'' . $icon . '\');';
          $src_fb = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        elseif($file_type == 'audio/mpeg'):
          $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/audio.png');
          $fallback_bg .= ' background-image:url(\'' . $icon . '\');';
          $src_fb = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        elseif($file_type == 'image/svg+xml'):
          $fallback_bg .= ' background-image:url(\'' . $full_image_url[0] . '\');';
          $src_fb = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        endif;
        // bg color
        $get_color = get_post_meta( $id, 'WPimg_DominantColor', true);
        $color = $get_color ? $get_color : "false";
        // fallback color
        if($color == "false"):
          if($fallback_bg == '' || $file_type == 'image/svg+xml'):
            $image_url = $img_url;
            $color = SELF::IMGcolor($image_url);
          else:
            $color = SELF::$WPimg_defaultcolor;
          endif;
        endif;
        // fallback for no img types
        if($fallback_bg !== ''):
          $thumb_id = get_post_thumbnail_id($id);
          if($thumb_id):
            // get img url
            $full_noimage_url = wp_get_attachment_image_src($thumb_id, 'full');
            $thumb_noimage_url = wp_get_attachment_image_src($thumb_id, 'thumbnail');
            $noimage_url = $thumb_noimage_url[0] ? $thumb_noimage_url[0] : $full_noimage_url[0];
            $src_fb = $noimage_url;
          endif;
        endif;
        // output
        $output .= '<img';
            $output .= ' class="' . $css . '"';
            $output .= ' id="img-' . $id . '"';
            $output .= ' ' . $additional;
            $output .= ' data-src="' . $src_fb . '"';
            $output .= $srcset !== '' ? ' srcset="' . $srcset . '"' : '';
            $output .= $srcset !== '' ? ' sizes="' .  $sizes . '"' : '';
            $output .= ' style="background-color: #' . $color . ';' . $fallback_bg . '"';
            $output .= ' alt="' . $alt . '"';
            $output .= ' src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII="';
        $output .= ' />';
      endif;

      return $output;
    }


    /* 3.5 CONTENT LAZY LOADING
    /------------------------*/
    /**
      * @param string $attr: img attributes
      * @return string img tag with all attributes and dominant bg color
    */
    public static function getContentIMG(string $attr = ''){
      $output = '';
      $additional = '';
      $css = SELF::$WPimg_js_loading ? 'lazy-content-img' : '';
      // fallback for content img
      if($attr !== ''):
        $attr_array = explode(" ", $attr);
        foreach ($attr_array as $key => $value) {
          // check attributes
          $check_css = strpos($value, 'class="');
          $check_src = strpos($value, 'src="');
          $check_alt = strpos($value, 'alt="');
          $check_srcset = strpos($value, 'srcset="');

          if($check_css !== false):
            preg_match('/class="(.*?)"/s', $value, $match_css);
            $css = $match_css ? $match_css[1] : '';
          elseif($check_src !== false):
            preg_match('/src="(.*?)"/s', $value, $match_src);
            $imgurl = $match_src[1];
            $mainurl = str_replace(array('https:/', 'http:/'), array('', ''), get_bloginfo('url'));
            $remove_vars = strpos($imgurl, '?') !== false ? substr($imgurl, 0, strpos($imgurl, "?")) : $imgurl;
            $remove_cdn = get_bloginfo('url') . strstr($remove_vars, '/wp-content');
            $id = attachment_url_to_postid($remove_cdn);
          elseif($check_alt !== false || $check_srcset !== false):
          else:
            $additional .= ' ' . $value;
          endif;
        }
      endif;

      if($id > 0):
        return SELF::getIMG($id, $additional, $css);
      endif;
    }


    /* 3.6 POP-UP CONTENT
    /------------------------*/
    /**
      * @param int $id: img id
    */
    public static function postPopUp(int $id = 0){
      $output = '';
      $posttype = get_post_type($id);
      $video_types = array('video/mp4', 'video/videopress');
      $img_id = $posttype == 'attachment' ? $id : get_post_thumbnail_id($id);
      $file_type = get_post_mime_type($img_id);
          $output .= '<div class="post-flex" data-id="img-' . $id . '">';
          // content img
          if(SELF::$WPimg_popupContent !== false):
            $output .= '<div class="column-img">';
          endif;
              $output .= '<span class="popup-arrow back hidden"></span>';
              if($posttype == 'attachment' && in_array($file_type, SELF::$WPimg_nocolor_files)):
                $output .= do_shortcode('[video src="' . wp_get_attachment_url($img_id) . '"]');
              elseif($posttype == 'attachment' && $file_type == 'video/quicktime'):
                $output .= '<video id="sampleMovie" src="' . wp_get_attachment_url($img_id) . '" controls></video>';
              elseif($posttype == 'attachment' && $file_type == 'audio/mpeg'):
                $output .= do_shortcode('[audio src="' . wp_get_attachment_url($img_id) . '"]');
              elseif($posttype == 'attachment' && $file_type == 'application/pdf'):
                $output .= '';
              else:
                $output .= bd_WPimg::getIMG($img_id);
              endif;
              $output .= '<span class="popup-arrow next hidden"></span>';
          if(SELF::$WPimg_popupContent !== false):
            $output .= '</div>';
            // content text
            $output .= '<div class="column-content">';
              $output .= '<div>';
                // add your content here
              $output .= '</div>';
            $output .= '</div>';
          endif;
      $output .= '</div>';

      return $output;
    }


    /* 3.7 CONTENT LAZY LOADING
    /––––––––––––––––––––––––*/
    public function ContentLazyLoading($content) {
      if(!is_admin()):
        // replace IMG with lazy loading
        // $content = preg_replace('/<img(.*?)src=\"(.*?)\"(.*?)class=\"(.*?)\"(.*?)>/e', '$4 $2', $content);
        $content = preg_replace_callback('/<img(.*?)>/', function ($matches) {
                  return SELF::getContentIMG($matches[1]);
        }, $content);
      endif;

    	return $content;
    }


    /* 3.8 SINGLE BLOCK
    /------------------------*/
    /**
      * @param int $id: img or post id
      * @param string $type: post type
      * @return string img container
    */
    public static function LazyImgBlock(int $id = 0, string $type = 'attachment'){
      // get img id
      if($type == 'attachment'):
        $img_id = $id;
      else:
        $img_id = get_post_thumbnail_id($id);
      endif;
      // get bg color
      $get_color = get_post_meta( $img_id, 'WPimg_DominantColor', true);
      $color = $get_color ? $get_color : "false";
      // fallback color if meta dont exists
      if($color == "false"):
        $file_type = get_post_mime_type($img_id);
        if(!in_array($file_type, SELF::$WPimg_nocolor_files)):
          $full_image_url = wp_get_attachment_image_src($id, 'full');
          $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
          $img_url = $thumb_image_url ? $thumb_image_url : $full_image_url;
          $color = SELF::IMGcolor($img_url[0]);
        else:
          // fallback color
          $color = SELF::$WPimg_defaultcolor;
        endif;
      endif;

      $status = SELF::$WPimg_js_loading ? 'update' : 'rdy';
      $css = SELF::$WPimg_js_loading ? 'post lazy-img' : 'post';

      // output
      $output = '';
      $output .= '<article class="' . $css . '" data-id="' . $img_id . '" style="background-color: #' . $color . '";>';
        $output .= SELF::$WPimg_js_loading ? '' : SELF::getIMG($img_id);
      $output .= '</article>';

      return $output;
    }

    /* 3.9 LIST OUTPUT
    /------------------------*/
    /**
    * @param array $atts: shortcode attributes
    * @return html output
    */
    // Usage: [gallery]
    public function MyGallery($atts){
      if(!is_admin()):
          // vars
          $output = '';
          $id = PARENT::ShortID(10, 'letters');
          $config = shortcode_atts( array(
            'post_type' => 'attachment',
            'post_status' => 'publish',
            'id' => '-1',
            'template' => 'grid',
            'class' => '',
          ), $atts );
          // all files
          if($config['id'] == "-1"):
            $args = array(
              'post_type'=> PARENT::AttrToArray($config['post_type']),
              'posts_per_page' =>  -1,
              'post_status' => PARENT::AttrToArray($config['post_status'])
            );
            $wp_query = new WP_Query($args);
            $ids = array();
            if ( $qa_query->have_posts() ) :
              while ( $qa_query->have_posts() ) : $qa_query->the_post();
                  $ids[] += get_the_ID();
              endwhile;
              // reset query
              wp_reset_postdata();
            endif;
          else:
            $ids = PARENT::AttrToArray($config['id']);
          endif;

          // output
          $output .= '<' . SELF::$WPimg_parent_element . ' class="' . $config['template'] . ' ' . $config['class'] . '" id="' . $id . '" data-id="' . $id . '">';
            // swiper navigation
            if($config['template'] == "swiper"):
              $output .= '<span class="swiper-arrow back hidden"></span>';
              $output .= '<span class="swiper-arrow next hidden"></span>';
            endif;
            if($config['template'] == "swiper"):
              $output .= '<div class="swiper-container">';
            endif;
            foreach ($ids as $key => $id) {
              $output .= SELF::LazyImgBlock($id, $config['post_type']);
            }
            if($config['template'] == "swiper"):
              $output .= '</div>';
            endif;
          $output .= '</' . SELF::$WPimg_parent_element . '>';

          return $output;
      endif;
    }



}
