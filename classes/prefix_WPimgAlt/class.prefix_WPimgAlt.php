<?
/**
 *
 *
 * Wordpress - add custom fields for alt tag translations
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
 * @version     1.0.2
 *
*/

class prefix_WPimgAlt extends prefix_BaseFunctions {

  /* CONFIGURATION
  /===================================================== */
  /**
    * default vars
    * @param static string $WPimgAlt_content: simple way to disable img alt for the_content
    * @param static string $WPimgAlt_attachmentt: simple way to disable img attachment alt
    * @param static string $WPimgAlt_shortcode: simple way to disable img in shortcode alt
    * @param static array $WPimgAlt_languages: List of Languages, first one is default
    * @param static string $WPimgAlt_prefix: Prefix for variables
  */
  static $WPimgAlt_content      = true;
  static $WPimgAlt_attachment   = true;
  static $WPimgAlt_shortcode    = true;
  static $WPimgAlt_languages    = array("de", "en", "fr", "it");
  static $WPimgAlt_prefix       = 'WPimgAlt_';



  /* INIT
  /===================================================== */
  public function __construct() {
    // add custom fields
    if(SELF::$WPimgAlt_content !== false || SELF::$WPimgAlt_attachment !== false):
      // add custom fields
      add_filter( 'attachment_fields_to_edit', array( $this, 'WPimgAlt_meta_CustomFields' ), null, 2 );
      // update custom fields
      add_action('add_attachment', array( $this, 'WPimgAlt_meta_Attachments_Save' ),  10, 2 );
      add_action('edit_attachment', array( $this, 'WPimgAlt_meta_Attachments_Save' ),  10, 2 );
    endif;
    // alt img in the_content
    if(SELF::$WPimgAlt_content !== false):
      add_filter('the_content', array( $this, 'IMGalt_Content' ) );
    endif;
    // alt img in do_shortcode
    if(SELF::$WPimgAlt_shortcode !== false):
      add_filter('do_shortcode', array( $this, 'IMGalt_Content' ) );
    endif;
    // alt img for attachments
    if(SELF::$WPimgAlt_attachment !== false):
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'IMGalt_Attachment' ), 10, 2 );
    endif;
  }


  /* ADD CUSTOM FIELDS
  /------------------------*/
  function WPimgAlt_meta_CustomFields( $form_fields, $post ) {
    //output for each language
    if(is_array(SELF::$WPimgAlt_languages)):
      // repeat output for each language (exept first one - is default)
      foreach (SELF::$WPimgAlt_languages as $key => $lang) {
        if($key > 0):
          // get saved value
          $var_name = SELF::$WPimgAlt_prefix . $lang;
          $value = get_post_meta($post->ID, $var_name, true);
          // create custom field
          $form_fields[$var_name] = array(
              'value' => $value ? $value : '',
              'label' => __( 'Alternative Text', 'WPimgAlt' ) . ' (' . $lang . ')',
              'input' => 'text'
          );
        endif;
      }
    endif;
    // output
    return $form_fields;
  }


  /* SAVE METABOXES
  /------------------------*/
  public function WPimgAlt_meta_Attachments_Save($post_id) {
    if( isset( $_POST['attachment'] ) ):
      //Not save if the user hasn't submitted changes
      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
        return;
      endif;
      // Verifying whether input is coming from the proper form
      if ( ! wp_verify_nonce ( $_POST['WPimgAlt_lang_de'] ) ):
        return;
      endif;
      // Making sure the user has permission
      if( 'post' == $_POST['attachment'] ):
        if( ! current_user_can( 'edit_post', $post_id ) ):
          return;
        endif;
      endif;
    endif;
    // save fields
    SELF::WPimgAlt_saveAltAttributes($post_id);
  }


  /* SAVE CUSTOM FIELDS
  /------------------------*/
  public function WPimgAlt_saveAltAttributes(int $id = 0){
    // save field for each language
    foreach (SELF::$WPimgAlt_languages as $key => $lang) {
      if($key > 0):
        // get field name
        $var_name = SELF::$WPimgAlt_prefix . $lang;
        // save value
        if ( isset( $_REQUEST['attachments'][ $id ][$var_name] ) ):
            $new_value = $_REQUEST['attachments'][ $id ][$var_name];
            update_post_meta( $id, $var_name, $new_value );
        endif;
      endif;
    }
  }



  /* FUNCTIONS
  /===================================================== */

  /* IMG ALT TAG
  /------------------------*/
  public static function getAltAttribute(int $id = 0){
    // vars
    $output = '';
    $lang = PARENT::getCurrentLang();
    // check if active lang is default or not
    if($lang == SELF::$WPimgAlt_languages[0]):
      // default language
      $output .= get_post_meta($id, '_wp_attachment_image_alt', TRUE);
    else:
      // alternative text
      $name = SELF::$WPimgAlt_prefix . $lang;
      $output .= get_post_meta($id, $name, true);
    endif;
    // output
    return $output;
  }


  /* IMG ALT TAG - ATTACHMENT
  /------------------------*/
  function IMGalt_Attachment($attributes, $attachment){
    // print_r($attributes);
    // print_r($attachment);
    // get up to date alt attribute
    $alt = SELF::getAltAttribute($attachment->ID);
    // set alt tag
    $attributes['alt'] = $alt;
    // output
    return $attributes;
  }


  /* IMG ALT TAG - CONTENT
  /------------------------*/
  function IMGalt_Content($content) {
    if($content):
      // encode content
      $content  = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
      $document = new \DOMDocument();
      // Disable libxml errors and allow user to fetch error information as needed
      libxml_use_internal_errors(true);
      $document->loadHTML(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
      // get img tag from content
      $images = $document->getElementsByTagName('img');
      foreach ($images as $image) {
        // get orginal from srcset
        if( $image->hasAttribute('srcset') ):
          $orginal = '';
          // get srcset from content and explode to array
          $srcset = $image->getAttribute('srcset');
          $srcset_array = explode(", ", $srcset);
          // get orginal size
          foreach ($srcset_array as $key => $value) {
            $single_srcset = explode(" ", $value);
            $src_size = str_replace("w", "", end($single_srcset));
            if(strpos($single_srcset[0], $src_size) !== false):
              // not the orginal size
              // $orginal .= $single_srcset[0] . ' ' . $src_size;
            else:
              $orginal .= $single_srcset[0];
            endif;
          }
        else:
          $orginal = strpos($image->getAttribute('src'), 'http') !== false ? $image->getAttribute('src') : get_option( 'siteurl' ) . $image->getAttribute('src');
        endif;
        // get orginal img id and call alt
        $id = attachment_url_to_postid($orginal);
        $alt = SELF::getAltAttribute($id);
        $image->removeAttribute('alt');
        $image->setAttribute('alt', $alt);
      }
      // output
      return $document->saveHTML();
    endif;
  }



}
