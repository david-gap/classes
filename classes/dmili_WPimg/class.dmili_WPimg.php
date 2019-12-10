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

class dmili_WPimg extends bd_Blog {

  var $assets = true;

  /* INIT
  /===================================================== */
  public function __construct() {
    // lazy loading content img
    add_filter('the_content', array( $this, 'ContentLazyLoading' ) );
    // add custom fields
    add_action('add_meta_boxes', array( $this, 'meta_Attachments' ) );
    // update custom fields
    add_action('add_attachment', array( $this, 'meta_Attachments_Save' ),  10, 2 );
    add_action('edit_attachment', array( $this, 'meta_Attachments_Save' ),  10, 2 );
    // backend page
    add_action( 'admin_menu', array( $this, 'WPimg_backendPage' ) );
    // add class assets
    if($this->assets !== false):
      add_action('admin_enqueue_scripts', array( $this, 'WPimg_backend_enqueue_scripts_and_styles' ) );
    endif;
  }


  /* ENQUEUE BACKEND SCRIPTS/STYLES
  /------------------------*/
  function WPimg_backend_enqueue_scripts_and_styles(){
    $class_path = get_template_directory_uri() . '/classes/dmili_WPimg/';
    wp_enqueue_script('backend/WPimg-script', $class_path . 'scripts.js', false, null);
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
  function saveDominantColor(int $id = 0, bool $return = false){
    $file_type = get_post_mime_type($id);
    if(!in_array($file_type, array('video/mp4', 'video/quicktime', 'video/videopress', 'audio/mpeg'))):
      $full_image_url = wp_get_attachment_image_src($id, 'full');
      $thumb_image_url = wp_get_attachment_image_src($id, 'thumbnail');
      $img_url = $thumb_image_url ? $thumb_image_url : $full_image_url;
      $color = SELF::IMGcolor($img_url[0]);
    else:
      $color = 'ffffff';
    endif;
    update_post_meta($id, 'WPimg_DominantColor', $color);

    if($return):
      return $color;
    endif;
  }

  /* GET DOMINANT IMG COLOR
  /------------------------*/
  /**
    * @param int $img: img url
    * @param string $default: fallback color
    * @return string dominant color hex
  */
  public static function IMGcolor(string $img = '', string $default='eee'){
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
    * @param string $css: img css classes
    * @param string $attr: other img attributes
    * @return string img tag with all attributes and dominant bg color
  */
  public static function get_lazy(int $id = 0, string $css = '', string $attr = ''){
    $output = '';
    $additional = '';
    $root = $_SERVER['DOCUMENT_ROOT'];
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
          $css .= $match_css ? $match_css[1] : '';
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
          $color = "FFFFFF";
        endif;
      endif;
      // output
      $output .= '<img';
          $output .= ' class="lazy ' . $css . '"';
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



  /* USAGE
  /===================================================== */

  /* CONTENT LAZY LOADING
  /––––––––––––––––––––––––*/
  public function ContentLazyLoading($content) {
    if(!is_admin()):
      // replace IMG with lazy loading
      // $content = preg_replace('/<img(.*?)src=\"(.*?)\"(.*?)class=\"(.*?)\"(.*?)>/e', '$4 $2', $content);
      $content = preg_replace_callback('/<img(.*?)>/', function ($matches) {
                return SELF::get_lazy(0, '', $matches[1]);
      }, $content);
    endif;

  	return $content;
  }


}
