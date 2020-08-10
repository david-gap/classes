<?
/**
 *
 *
 *
 * IMG dominant color - WP compatible
 * https://github.com/david-gap/classes
 * Author:      David Voglgsang
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
  2.2 ENQUEUE FRONTEND SCRIPTS/STYLES
  2.3 ENQUEUE BACKEND SCRIPTS/STYLES
  2.4 ADD CUSTOM FIELDS
  2.5 ADD BACKEND SUB-PAGE
  2.6 SAVE METABOXES
  2.7 SAVE DOMINANT
  2.8 GET DOMINANT IMG COLOR
  2.9 GET IMG ID IF FILE IST NOT ORGINAL
3.0 OUTPUT
  3.1 BACKEND SUB-PAGE
  3.2 GENERATE DOMINANT COLORS
  3.3 BACKEND META BOX OUTPUT
  3.4 LAZY LOADING
  3.5 CONTENT IMG
  3.6 CONTENT LAZY LOADING
=======================================================*/

class prefix_imgDC {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param private int $imgDC_wp: activate WP settings
      * @param private int $imgDC_content: simple way to disable the lazy loading inside the_content
      * @param private int $imgDC_assets: include classes assets. Disable it if you use your own files
      * @param private array $imgDC_nocolor_files: exclude file types from dominant color generator
      * @param private string $imgDC_defaultcolor: default color
    */
    private $imgDC_wp                     = 0;
    private $imgDC_content                = 1;
    private $imgDC_assets                 = 1;
    private static $imgDC_nocolor_files   = array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg');
    private static $imgDC_defaultcolor    = 'ffffff';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // if wordpress is active
      if($this->imgDC_wp == 1):
        // lazy loading content img
        if($this->imgDC_content == 1):
          add_filter('the_content', array( $this, 'ContentLazyLoading' ), 12 );
          add_filter('do_shortcode_tag', array( $this, 'ContentLazyLoading' ), 12 );
        endif;
        // add custom fields
        add_action('add_meta_boxes', array( $this, 'meta_Attachments' ) );
        // update custom fields
        add_action('add_attachment', array( $this, 'meta_Attachments_Save' ),  100, 2 );
        add_action('edit_attachment', array( $this, 'meta_Attachments_Save' ),  10, 2 );
        // backend page
        add_action( 'admin_menu', array( $this, 'imgDC_backendPage' ) );
        // add class assets
        add_action('admin_enqueue_scripts', array( $this, 'imgDC_backend_enqueue_scripts_and_styles' ) );
        if($this->imgDC_assets == 1):
          add_action('wp_enqueue_scripts', array( $this, 'imgDC_frontend_enqueue_scripts_and_styles' ) );
        endif;
      endif;
    }


    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $classtitle = 'IMG dominant content preloader';
    static $classkey = 'imgDC';
    static $backend = array(
      "wp" => array(
        "label" => "Activate",
        "type" => "switchbutton"
      ),
      "content" => array(
        "label" => "Use in the content and shortcodes",
        "type" => "switchbutton"
      ),
      "assets" => array(
        "label" => "Embed assets",
        "type" => "switchbutton"
      ),
      "nocolor_files" => array(
        "label" => "Ignored file types",
        "type" => "array_addable"
      ),
      "defaultcolor" => array(
        "label" => "Default color",
        "type" => "text"
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
      if($configuration && array_key_exists('imgDC', $configuration)):
        // class configuration
        $myConfig = $configuration['imgDC'];
        // update vars
        $this->imgDC_wp = array_key_exists('wp', $myConfig) ? $myConfig['wp'] : $this->imgDC_wp;
        $this->imgDC_content = array_key_exists('content', $myConfig) ? $myConfig['content'] : $this->imgDC_content;
        $this->imgDC_assets = array_key_exists('assets', $myConfig) ? $myConfig['assets'] : $this->imgDC_assets;
        SELF::$imgDC_nocolor_files = array_key_exists('nocolor_files', $myConfig) ? $myConfig['nocolor_files'] : SELF::$imgDC_nocolor_files;
        SELF::$imgDC_defaultcolor = array_key_exists('defaultcolor', $myConfig) ? $myConfig['defaultcolor'] : SELF::$imgDC_defaultcolor;
      endif;
    }


    /* 2.2 ENQUEUE FRONTEND SCRIPTS/STYLES
    /------------------------*/
    function imgDC_frontend_enqueue_scripts_and_styles() {
      $class_path = get_template_directory_uri() . '/classes/prefix_imgDC/';
      $backend_ajax_action_file = $class_path . 'imgDC-ajax.php';
      // scripts
      wp_register_script('frontend/imgDC-script', $class_path . 'imgDC.js', false, "1.0", true);
      wp_enqueue_script('frontend/imgDC-script');
      wp_localize_script('frontend/imgDC-script', 'imgDC_Ajax', $backend_ajax_action_file);
    }


    /* 2.3 ENQUEUE BACKEND SCRIPTS/STYLES
    /------------------------*/
    function imgDC_backend_enqueue_scripts_and_styles(){
      $class_path = get_template_directory_uri() . '/classes/prefix_imgDC/';
      $backend_ajax_action_file = $class_path . 'imgDC-ajax.php';
      // scripts
      wp_enqueue_script('backend/imgDC-script', $class_path . 'imgDC-backend.js', false, null);
      wp_localize_script( 'backend/imgDC-script', 'imgDC_Ajax', $backend_ajax_action_file );
    }


    /* 2.4 ADD CUSTOM FIELDS
    /------------------------*/
    public function meta_Attachments() {
      add_meta_box (
        'imgDC_DominantColor',
        'imgDC fields',
        array( $this, 'meta_Attachments_Output' ),
        'attachment',
        'normal',
        'high'
      );
    }


    /* 2.5 ADD BACKEND SUB-PAGE
    /------------------------*/
    function imgDC_backendPage() {
      add_submenu_page(
          'upload.php',
          __('Dominant Color','imgDC'),
          __('Dominant Color','imgDC'),
          'imgDC',
          'imgDC-DominantColor',
          array( $this, 'backend_page' ),
          6
      );
      // give access to administrator
      $role_admin = get_role( 'administrator' );
      $role_admin->add_cap( 'imgDC' );
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
        if ( ! wp_verify_nonce ( $_POST['imgDC_DominantColor'] ) ):
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
      // get attachment meta data
      $img_full_url = wp_get_attachment_image_src($id, 'full');
      $img_type = get_post_mime_type($id);
      // check file type
      if(!in_array($img_type, SELF::$imgDC_nocolor_files)):
        // check if file exists
        $check_file = prefix_core_BaseFunctions::CheckFileExistence($img_full_url[0]);
        if ($check_file == true):
          $color = SELF::IMGcolor($img_full_url[0]);
        else:
        endif;
      else:
        $color = SELF::$imgDC_defaultcolor;
      endif;
      update_post_meta($id, 'imgDC_DominantColor', $color);
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
      $default = $default == '' ? $default : SELF::$imgDC_defaultcolor;
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


    /* 2.9 GET IMG ID IF FILE IST NOT ORGINAL
    /------------------------*/
    /**
      * @param int $img: img url
      * @return int image id
    */
    static function getAttachmentID_notOrginal( $url ) {
      $attachment_id = 0;
      $dir = wp_upload_dir();
      if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
        $file = basename( $url );
        $query_args = array(
          'post_type'   => 'attachment',
          'post_status' => 'inherit',
          'fields'      => 'ids',
          'meta_query'  => array(
            array(
              'value'   => $file,
              'compare' => 'LIKE',
              'key'     => '_wp_attachment_metadata',
            ),
          )
        );
        $query = new WP_Query( $query_args );
        if ( $query->have_posts() ) {
          foreach ( $query->posts as $post_id ) {
            $meta = wp_get_attachment_metadata( $post_id );
            $original_file       = basename( $meta['file'] );
            $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
            if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
              $attachment_id = $post_id;
              break;
            }
          }
        }
      }
      return $attachment_id;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

    /* 3.1 BACKEND SUB-PAGE
    /------------------------*/
    function backend_page() {
        if ( !current_user_can( 'imgDC' ) )  {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $output = '';
        $output .= '<div class="wrap" id="imgDC">';
          $output .= '<h1 class="wp-heading-inline">' . __('Dominant Color','imgDC') . '</h1>';
          $output .= '<button class="page-title-action ajax-action" data-action="DominantColors">' . __('Generate dominant colors','imgDC') . '</button>';
          $output .= '<table class="wp-list-table widefat fixed striped pages hidden">';
            $output .= '<thead>';
              $output .= '<tr>';
                $output .= '<td>';
                  $output .= __('IMG color','imgDC');
                $output .= '</td>';
                $output .= '<td>';
                  $output .= __('Image / ID / Title','imgDC');
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
        'meta_key'     => 'imgDC_DominantColor',
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
          $full_image_url = wp_get_attachment_image_src(get_the_ID());
          // output
          $output .= '<tr>';
            $output .= '<td style="background-color: #' . $new_color . '">';
              $output .= '&nbsp;';
              $output .= $new_color == 'file_missing' ? __( 'Current image file is missing', 'imgDC' ) : '';
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
      $content = get_post_meta($post->ID, 'imgDC_DominantColor', true);
      $output = '';
      $output .= '<table class="backend-metabox-output">';
        $output .= '<tr>';
          $output .= '<td>';
            $output .= '<label for="imgDC_DominantColor">' . __( 'Dominant color', 'imgDC' ) . '</label>';
          $output .= '</td>';
          $output .= '<td>';
            $output .= '<input type="text" disabled name="imgDC_DominantColor" value="' . $content . '">';
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
      $css .= ' imgDC';
      $img_src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";

      if($id > 0):
        // uploads directory
        $path = wp_get_upload_dir();
        // get attachment meta data
        $metadata = wp_get_attachment_metadata($id);
        $img_type = get_post_mime_type($id);
        $img_path = $path && array_key_exists('baseurl', $path) ? $path["baseurl"] : '';
        $img_path .= $metadata && array_key_exists('file', $metadata) ? '/' . $metadata["file"] : '';
        $full_image_url = wp_get_attachment_image_src($id, 'full');
        $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
        // fallback - img width
        if(strpos($additional, 'width="') == false):
            $additional .= $metadata && array_key_exists('width', $metadata) ? ' width="' . $metadata["width"] . '"' : '';
        endif;
        // fallback - img height
        if(strpos($additional, 'height="') == false):
            $additional .= $metadata && array_key_exists('height', $metadata) ? ' height="' . $metadata["height"] . '"' : '';
        endif;
        // fallback - alt
        if(strpos($additional, 'alt="') == false):
            $img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
            $additional .= $img_alt ? ' alt="' . $img_alt . '"' : '';
        endif;
        // add file type as css class
        $css .= ' ' . str_replace(array("/", "+"), array("-", "-"), $img_type);
        // fallback - srcset
        if(strpos($additional, 'srcset="') === false):
            $srcset = wp_get_attachment_image_srcset( $id );
            if(!$srcset):
              $srcset = '';
              // get srcset only if type is img
              if($img_type == 'image/jpeg'):
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
            // return srcset
            $additional .= $srcset !== '' ? ' srcset="' . $srcset . '"' : '';
        endif;
        // fallback - sizes
        if(strpos($additional, 'sizes="') === false):
            $sizes = wp_get_attachment_image_sizes( $id );
            $additional .= $sizes ? ' sizes="' . $sizes . '"' : '';
        endif;

        // IMG inline styling
          // background img
          $fallback_bg = '';
          $videos = array('video/mp4', 'video/quicktime', 'video/videopress');
          if(in_array($img_type, $videos)):
            $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/video.png');
            $fallback_bg .= ' background-image:url(\'' . $icon . '\');';
            $img_path = $img_src;
          elseif($img_type == 'audio/mpeg'):
            $icon = str_replace(" ", "", get_bloginfo('url') . '/wp-includes/images/media/audio.png');
            $fallback_bg .= ' background-image:url(\'' . $icon . '\');';
            $img_path = $img_src;
          elseif($img_type == 'image/svg+xml'):
            $fallback_bg .= ' background-image:url(\'' . $full_image_url[0] . '\');';
            $img_path = $img_src;
          endif;
          // bg color
          $get_img_color = get_post_meta( $id, 'imgDC_DominantColor', true);
          $img_color = $get_img_color ? $get_img_color : "false";
          // fallback color
          if($img_color == "false"):
            $img_color = SELF::$imgDC_defaultcolor;
          endif;

        // output
        $output .= '<img';
            $output .= ' src="' . $img_src . '"';
            $output .= ' class="' . $css . '"';
            $output .= ' data-id="' . $id . '"';
            $output .= ' data-src="' . $img_path . '"';
            $output .= ' ' . $additional;
            $output .= ' style="background-color: #' . $img_color . ';' . $fallback_bg . '"';
        $output .= ' />';

        return $output;
      endif;
    }


    /* 3.5 CONTENT IMG
    /------------------------*/
    /**
      * @param string $attr: img attributes
      * @return string img tag with all attributes and dominant bg color
    */
    public static function getContentIMG(string $attr = ''){
      $output = '';
      $additional = '';
      $css = '';
      // fallback for content img
      if($attr !== ''):
        $attr_clean = str_replace('" ', '"DCSPLITT', $attr);
        $attr_array = explode('DCSPLITT', $attr_clean);
        foreach ($attr_array as $key => $value) {
          // check attributes
          $check_css = strpos($value, 'class="');
          $check_src = strpos($value, 'src="');
          $check_srcset = strpos($value, 'srcset="');

          if($check_css !== false):
            preg_match('/class="(.*?)"/s', $value, $match_css);
            $css = $match_css ? $match_css[1] : '';
          elseif($check_src !== false):
            preg_match('/src="(.*?)"/s', $value, $match_src);
            $imgurl = $match_src[1];
            $mainurl = str_replace(array('https:/', 'http:/'), array('', ''), get_bloginfo('wpurl'));
            $remove_vars = strpos($imgurl, '?') !== false ? substr($imgurl, 0, strpos($imgurl, "?")) : $imgurl;
            $remove_cdn = get_bloginfo('wpurl') . strstr($remove_vars, '/wp-content');
            $id = attachment_url_to_postid($remove_cdn);
            // fallback if img is cropped or is not orginal url
            if($id == 0):
              $id = SELF::getAttachmentID_notOrginal($remove_cdn);
            endif;
          elseif($check_srcset !== false):
            $additional .= str_replace('srcset', 'srcset', $value);
          else:
            $additional .= $value;
          endif;
        }
      endif;
      // return img
      if($id > 0):
        return SELF::getIMG($id, $additional, $css);
      else:
        return '<img ' . $attr . '>';
      endif;
    }


    /* 3.6 CONTENT LAZY LOADING
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



}
