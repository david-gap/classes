<?
/**
 *
 *
 * Wordpress - add custom fields for alt tag translations
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
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
2.0 FUNCTIONS
  2.1 GET CONFIGURATION FORM CONFIG FILE
  2.2 ADD CUSTOM FIELDS
  2.3 SAVE METABOXES
  2.4 SAVE CUSTOM FIELDS
  2.5 IMG ALT TAG
  2.6 IMG ALT TAG - ATTACHMENT
3.0 OUTPUT
  3.1 IMG ALT TAG - CONTENT
=======================================================*/


class prefix_WPimgAttr {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

  /* 1.1 CONFIGURATION
  /------------------------*/
  /**
    * default vars (if configuration file is missing or broken)
    * @param private bool $WPimgAttr_Alt_content: simple way to disable img alt for the_content
    * @param private bool $WPimgAttr_Alt_attachmentt: simple way to disable img attachment alt
    * @param private bool $WPimgAttr_Alt_shortcode: simple way to disable img in shortcode alt
    * @param private array $WPimgAttr_Alt_languages: List of Languages, first one is default
    * @param private string $WPimgAttr_Alt_prefix: Prefix for variables
  */
  private $WPimgAttr_Alt_content             = 1;
  private $WPimgAttr_Alt_attachment          = 1;
  private $WPimgAttr_Alt_shortcode           = 1;
  private static $WPimgAttr_Alt_languages    = array("de", "en", "fr", "it");
  private static $WPimgAttr_Alt_prefix       = 'WPimgAttr_Alt_';


  /* 1.2 ON LOAD RUN
  /------------------------*/
  public function __construct() {
    // update default vars with configuration file
    SELF::updateVars();
    // add custom fields
    if($this->WPimgAttr_Alt_content == 1 || $this->WPimgAttr_Alt_attachment == 1):
      // add custom fields
      add_filter( 'attachment_fields_to_edit', array( $this, 'WPimgAttr_Alt_meta_CustomFields' ), null, 2 );
      // update custom fields
      add_action('add_attachment', array( $this, 'WPimgAttr_Alt_meta_Attachments_Save' ),  10, 2 );
      add_action('edit_attachment', array( $this, 'WPimgAttr_Alt_meta_Attachments_Save' ),  10, 2 );
    endif;
    // alt img in the_content
    if($this->WPimgAttr_Alt_content == 1):
      add_filter('the_content', array( $this, 'IMGalt_Content' ) );
    endif;
    // alt img in do_shortcode
    if($this->WPimgAttr_Alt_shortcode == 1):
      add_filter('do_shortcode', array( $this, 'IMGalt_Content' ) );
    endif;
    // alt img for attachments
    if($this->WPimgAttr_Alt_attachment == 1):
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'IMGalt_Attachment' ), 10, 2 );
    endif;
  }


  /* 1.3 BACKEND ARRAY
  /------------------------*/
  static $classtitle = 'Image attributes';
  static $classkey = 'WPimgAttr';
  static $backend = array(
    "Alt_content" => array(
      "label" => "Repalce img alt inside content",
      "type" => "switchbutton"
    ),
    "Alt_attachment" => array(
      "label" => "Repalce img alt inside atttachments",
      "type" => "switchbutton"
    ),
    "Alt_shortcode" => array(
      "label" => "Repalce img alt inside shortcodes",
      "type" => "switchbutton"
    ),
    "Alt_languages" => array(
      "label" => "Add languages",
      "type" => "array_addable"
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
      if($configuration && array_key_exists('WPimgAttr', $configuration)):
        // class configuration
        $myConfig = $configuration['WPimgAttr'];
        // update vars
        $this->WPimgAttr_Alt_content = array_key_exists('Alt_content', $myConfig) ? $myConfig['Alt_content'] : $this->WPimgAttr_Alt_content;
        $this->WPimgAttr_Alt_attachment = array_key_exists('Alt_attachment', $myConfig) ? $myConfig['Alt_attachment'] : $this->WPimgAttr_Alt_attachment;
        $this->WPimgAttr_Alt_shortcode = array_key_exists('Alt_shortcode', $myConfig) ? $myConfig['Alt_shortcode'] : $this->WPimgAttr_Alt_shortcode;
        SELF::$WPimgAttr_Alt_languages = array_key_exists('Alt_languages', $myConfig) ? $myConfig['Alt_languages'] : SELF::$WPimgAttr_Alt_languages;
      endif;
    }


    /* 2.2 ADD CUSTOM FIELDS
    /------------------------*/
    function WPimgAttr_Alt_meta_CustomFields( $form_fields, $post ) {
      //output for each language
      if(is_array(SELF::$WPimgAttr_Alt_languages)):
        // repeat output for each language (exept first one - is default)
        foreach (SELF::$WPimgAttr_Alt_languages as $key => $lang) {
          if($key > 0):
            // get saved value
            $var_name = SELF::$WPimgAttr_Alt_prefix . $lang;
            $value = get_post_meta($post->ID, $var_name, true);
            // create custom field
            $form_fields[$var_name] = array(
                'value' => $value ? $value : '',
                'label' => __( 'Alternative Text', 'WPimgAttr' ) . ' (' . $lang . ')',
                'input' => 'text'
            );
          endif;
        }
      endif;
      // output
      return $form_fields;
    }


    /* 2.3 SAVE METABOXES
    /------------------------*/
    public function WPimgAttr_Alt_meta_Attachments_Save($post_id) {
      if( isset( $_POST['attachment'] ) ):
        //Not save if the user hasn't submitted changes
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
          return;
        endif;
        // Verifying whether input is coming from the proper form
        if ( ! wp_verify_nonce ( $_POST['WPimgAttr_Alt_lang_de'] ) ):
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
      SELF::WPimgAttr_Alt_saveAltAttributes($post_id);
    }


    /* 2.4 SAVE CUSTOM FIELDS
    /------------------------*/
    public function WPimgAttr_Alt_saveAltAttributes(int $id = 0){
      // save field for each language
      foreach (SELF::$WPimgAttr_Alt_languages as $key => $lang) {
        if($key > 0):
          // get field name
          $var_name = SELF::$WPimgAttr_Alt_prefix . $lang;
          // save value
          if ( isset( $_REQUEST['attachments'][ $id ][$var_name] ) ):
              $new_value = $_REQUEST['attachments'][ $id ][$var_name];
              update_post_meta( $id, $var_name, $new_value );
          endif;
        endif;
      }
    }


    /* 2.5 IMG ALT TAG
    /------------------------*/
    public static function getAltAttribute(int $id = 0){
      // vars
      $output = '';
      $lang = prefix_core_BaseFunctions::getCurrentLang();
      // check if active lang is default or not
      if($lang == SELF::$WPimgAttr_Alt_languages[0]):
        // default language
        $output .= get_post_meta($id, '_wp_attachment_image_alt', TRUE);
      else:
        // alternative text
        $name = SELF::$WPimgAttr_Alt_prefix . $lang;
        $output .= get_post_meta($id, $name, true);
      endif;
      // output
      return $output;
    }


    /* 2.6 IMG ALT TAG - ATTACHMENT
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



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

  /* 3.1 IMG ALT TAG - CONTENT
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
