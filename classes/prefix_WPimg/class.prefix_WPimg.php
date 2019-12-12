<?
/**
 *
 *
 *
 * Additional functions for WP Media
 * https://github.com/david-gap/classes
 * Author:      David Voglgsnag
 * @version     1.0
 * @since       1.0
 *
 * Change the $assets to false if you use your own backend.js and ajax file
 */

class prefix_WPimg {

  static $WPimg_content      = true;
  static $WPimg_assets       = true;
  static $WPimg_js_loading   = true;
  static $nocolor_files      = array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg');
  static $WPimg_defaultcolor = 'ffffff';

  /* INIT
  /===================================================== */
  public function __construct() {
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
      add_action('admin_enqueue_scripts', array( $this, 'WPimg_backend_enqueue_scripts_and_styles' ) );
    endif;
    // shortcodes
    add_shortcode( 'gallery', array( $this, 'MyGallery' ) );
  }


  /* ENQUEUE BACKEND SCRIPTS/STYLES
  /------------------------*/
  function WPimg_backend_enqueue_scripts_and_styles(){
    $class_path = get_template_directory_uri() . '/classes/prefix_WPimg/';
    wp_enqueue_script('backend/WPimg-script', $class_path . 'backend-scripts.js', false, null);
    // Define path for ajax requests
    $backend_ajax_action_file = $class_path . 'ajax.php';
    wp_localize_script( 'backend/WPimg-script', 'WPimg_Ajax', $backend_ajax_action_file );
  }


  /* ADD CUSTOM FIELDS
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


  /* ADD BACKEND SUB-PAGE
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



  /* BACKEND
  /===================================================== */

  /* BACKEND SUB-PAGE
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


  /* GENERATE DOMINANT COLORS
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


  /* BACKEND META BOX OUTPUT
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


  /* SAVE METABOXES
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



  /* FUNCTIONS
  /===================================================== */

  /* SAVE COMINANT
  /------------------------*/
  public static function saveDominantColor(int $id = 0, bool $return = false){
    $file_type = get_post_mime_type($id);
    if(!in_array($file_type, $this->nocolor_files)):
      $full_image_url = wp_get_attachment_image_src($id, 'full');
      $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
      $img_url = $thumb_image_url ? $thumb_image_url : $full_image_url;
      $color = SELF::IMGcolor($img_url[0]);
    else:
      $color = SELF::$WPimg_defaultcolor;
    endif;
    update_post_meta($id, 'WPimg_DominantColor', $color);

    if($return):
      return $color;
    endif;
  }


  /* GENERATE SHORT ID
  /------------------------*/
  /**
    * @param int $length: ID length
    * @param string $type: ID chars int/letters both on default
    * @return string random id
  */
  function galShortID(int $length = 10, string $type = ''){
    if($type == 'int'):
      return substr(str_shuffle("0123456789"), 0, $length);
    elseif($type == 'letters'):
      return substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
    else:
      return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $length);
    endif;
  }


  /* EXPLODE SHORTCODE ATTRIBUTE
  /------------------------*/
  function AttrToArray(string $attr){
    // remove spaces from string
    $clean = str_replace(", ", ",", $attr);
    // create array
    $array = explode(',', $clean);

    return $array;
  }


  /* GET DOMINANT IMG COLOR
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


  /* LAZY LOADING
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
      $img_url = $thumb_image_url ? $thumb_image_url : $full_image_url;
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
      $src_fb = $thumb_image_url[0];
      $fallback_bg = '';
      if(in_array($file_type, array('video/mp4', 'video/quicktime', 'video/videopress'))):
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
      if($color == false):
        if($fallback_bg == '' || $file_type == 'image/svg+xml'):
          $image_url = $thumb_image_url[0] ? $thumb_image_url[0] : $full_image_url[0];
          $color = SELF::IMGcolor($image_url);
        else:
          $color = SELF::$WPimg_defaultcolor;
        endif;
      endif;
      // output
      $output .= '<img';
          $output .= ' class="' . $css . '"';
          $output .= ' id="img-' . $id . '"';
          $output .= ' ' . $additional;
          $output .= ' data-src="' . $src_fb . '"';
          $output .= ' data-status="update"';
          $output .= $srcset !== '' ? ' srcset="' . $srcset . '"' : '';
          $output .= $srcset !== '' ? ' sizes="' .  $sizes . '"' : '';
          $output .= ' style="background-color: #' . $color . ';' . $fallback_bg . '"';
          $output .= ' alt="' . $alt . '"';
          $output .= ' src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII="';
      $output .= '/>';
    endif;

    return $output;
  }


  /* CONTENT LAZY LOADING
  /------------------------*/
  /**
    * @param string $attr: img attributes
    * @return string img tag with all attributes and dominant bg color
  */
  public static function getContentIMG(string $attr = ''){
    $output = '';
    $additional = '';
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
          $remove_vars = substr($imgurl, 0, strpos($imgurl, "?"));
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



  /* FRONTEND
  /===================================================== */

  /* CONTENT LAZY LOADING
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



  /* IMG GRID / SWIPER
  /===================================================== */

  /* SINGLE BLOCK
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
    if($color == false):
      $file_type = get_post_mime_type($img_id);
      if(!in_array($file_type, $this->nocolor_files)):
        $full_image_url = wp_get_attachment_image_src($id, 'full');
        $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
        $img_url = $thumb_image_url ? $thumb_image_url : $full_image_url;
        $color = SELF::IMGcolor($image_url);
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

  /* LIST OUTPUT
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
        $id = SELF::galShortID(10, 'letters');
        $config = shortcode_atts( array(
          'post_type' => 'attachment',
          'post_status' => 'publish',
          'id' => '-1',
          'template' => 'grid',
        ), $atts );
        // all files
        if($config['id'] == "-1"):
          $args = array(
            'post_type'=> SELF::AttrToArray($config['post_type']),
            'posts_per_page' =>  -1,
            'post_status' => SELF::AttrToArray($config['post_status'])
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
          $ids = SELF::AttrToArray($config['id']);
        endif;

        // output
        $output .= '<section class="' . $config['template'] . '" id="' . $id . '">';
          // swiper navigation
          if($config['template'] == "swiper"):
            $output .= '<span class="arrow back hidden"></span>';
            $output .= '<span class="arrow next hidden"></span>';
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
        $output .= '</section>';

        return $output;
    endif;
  }



}
