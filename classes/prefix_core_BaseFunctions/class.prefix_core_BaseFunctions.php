<?
/**
 *
 *
 * Base dev functions - parent for all custom classes
 * Author:      David Voglgsnag
 * @version     2.8
 *
 */

 /*=======================================================
 Table of Contents:
 ---------------------------------------------------------
 1.0 FOR DEVELOPMENT
   1.1 EXPLODE COMMA SEPERATED ARRAY
   1.2 PRICE FORMAT
   1.3 BROWSER CHECK
   1.4 GENERATE SHORT ID
   1.5 FILE EXISTS
   1.6 CHECK IF FOLDER EXISTS
   1.7 CREATE FOLDER
   1.8 COPY FOLDER CONTENT AND SUB FOLDERS
   1.9 GET CONTENT FROM STRING BETWEEN TWO CHARS/CHAR GROUPS
   1.10 FIND KEY IN MULTIDIMENSIONAL ARRAY
   1.11 CLEAN PHONE NUMBER
   1.12 DELETE FOLDER
   1.13 SORT ARRAY
   1.14 CLEAN ARRAY
   1.15 SLUGIFY STRING
   1.16 INSERT TO ARRAY AT SPACIFIC POSITION
 2.0 DATES
   2.1 CHECK IF VARS ARE OUT OF DATE
   2.2 DATE RANGE FORMAT
 3.0 FOR FORMULARS
   3.1 GET POST
   3.2 CHECK IF OPTION IS SELECTED
   3.3 CHECK IF CHECKBOX IS CHECKED
 4.0 FOR WORDPRESS
   4.1 GET CURRENT LANGUAGE
   4.2 ADD USER ROLE
   4.3 ADD CUSTOM TAXONOMY
   4.4 RETURN TAXONOMY TERMS IN A LIST
   4.5 LOGIN FORMULAR
   4.6 UPLOAD WP IMG
 5.0 COORDINATES
   5.1 CONVERT: WGS lat/long TO CH1903 y
   5.2 CONVERT: WGS lat/long TO CH1903 x
   5.3 CONVERT: CH1903 y/x TO WGS lat
   5.4 CONVERT: CH1903 y/x TO WGS lng
   5.5 CONVERT: DEC angle to SEX DMS
 =======================================================*/


class prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 FOR DEVELOPMENT
  ==================================================================================*/

    /* 1.1 EXPLODE COMMA SEPERATED ARRAY
    /------------------------*/
    public static function AttrToArray(string $attr){
      // remove spaces from string
      $clean = str_replace(", ", ",", $attr);
      // create array
      $array = explode(',', $clean);

      return $array;
    }


    /* 1.2 PRICE FORMAT
    /------------------------*/
    /**
      * @param string $preis: given price
      * @param string $seperator: seperates the $ from the cents
      * @param string $seperator_thousands: seperates the tousands steps
      * @return string price
    */
    function formatPrice(int $preis = 0, string $seperator = ".", string $seperator_thousands = " " ) {
        $preis += .00;
        return number_format($preis,2,$seperator,$seperator_thousands);
    }


    /* 1.3 BROWSER CHECK
    /------------------------*/
    /**
      * @return string current browser
    */
    public static function get_browser_name() {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
      if (strpos($user_agent, 'Chrome')) return 'Chrome';
      elseif (strpos($user_agent, 'Safari')) return 'Safari';
      elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
      elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
      elseif (strpos($user_agent, 'Edge')) return 'Edge';
      elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'InternetExplorer';
      return 'Other';
    }


    /* 1.4 GENERATE SHORT ID
    /------------------------*/
    /**
      * @param int $length: ID length
      * @param string $type: ID chars int/letters both on default
      * @return string random id
    */
    public static function ShortID(int $length = 10, string $type = ''){
      if($type == 'int'):
        return substr(str_shuffle("0123456789"), 0, $length);
      elseif($type == 'letters'):
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
      else:
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $length);
      endif;
    }

    /* 1.5 FILE EXISTS
    /------------------------*/
    /**
    * check if file exists - relative or absolute path
    * @return bool true/false
    */
    public static function CheckFileExistence($url){
      if(substr($url, 0, 4) === 'http'):
        // absolute path
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(curl_exec($ch)!==FALSE):
            return true;
        else:
            return false;
        endif;
      else:
        // relative path
        if(file_exists($url) && filesize($url) > 0):
            return true;
            // return "existing";
        else:
            return false;
            // return "not existing";
        endif;
      endif;
    }


      /* 1.6 CHECK IF FOLDER EXISTS
      /------------------------*/
      /**
        * check if folder exists
        * @param string $DirName: folder name
        * @return true/false
      */
      public static function CheckDir($DirName) {
        if (file_exists($DirName)) {
            $debug_errors['CheckDir'] = 'Dir ' . $DirName . " exists";
            return true;
        } else {
            $debug_errors['CheckDir'] = 'Dir ' . $DirName . " does not exist";
            return false;
        }
      }


    /* 1.7 CREATE FOLDER
    /------------------------*/
    /**
      * create folder
      * @param string $DirName: folder name
      * @param int $mode: folder rights
      * @return true/false
    */
    private function CreateDirectory($DirName, int $mode = 0777) {
      // check the folder
      $check = SELF::CheckDir($DirName);
      // create the folder
      if(!$check):
        if (mkdir($DirName)):
            chmod($DirName, $mode);
            $debug_errors['CreateDir'] = 'Dir ' . $DirName . " created";
            return true;
        else:
            $debug_errors['CreateDir'] = 'Dir ' . $DirName . " exists already";
            return false;
        endif;
      endif;
    }


    /* 1.8 COPY FOLDER CONTENT AND SUB FOLDERS
    /------------------------*/
    /**
      * copy intanet res to intranet folder
      * @param string $src: source directory
      * @param string $dst: destination directory
      * @param int $mode: folder settings
    */
    function copyDirectory($src, $dst, $mode){
      // open the source directory
      $dir = opendir($src);
      // Make the destination directory if not exist
      @mkdir($dst);
      chmod($dst, $mode);
      // Loop through the files in source directory
      while( $file = readdir($dir) ) {
          if (( $file != '.' ) && ( $file != '..' )) {
              if ( is_dir($src . '/' . $file) )
              {
                  // Recursively calling custom copy function
                  // for sub directory
                  SELF::copyDirectory($src . '/' . $file, $dst . '/' . $file);
              }
              else {
                  copy($src . '/' . $file, $dst . '/' . $file);
              }
          }
      }
      closedir($dir);
    }


    /* 1.9 GET CONTENT FROM STRING BETWEEN TWO CHARS/CHAR GROUPS
    /------------------------*/
    /**
      * copy intanet res to intranet folder
      * @param string $string: content
      * @param string $start: start parameter
      * @param string $end: end parameter
      * @return string between start and end
    */
    public static function getBetween(string $string = "", string $start = "", string $end = ""){
      if (strpos($string, $start)):
          $startCharCount = strpos($string, $start) + strlen($start);
          $firstSubStr = substr($string, $startCharCount, strlen($string));
          $endCharCount = strpos($firstSubStr, $end);
          if ($endCharCount == 0):
              $endCharCount = strlen($firstSubStr);
          endif;
          return substr($firstSubStr, 0, $endCharCount);
      else:
          return '';
      endif;
    }


    /* 1.10 FIND KEY IN MULTIDIMENSIONAL ARRAY
    /------------------------*/
    /**
      * @param string $searchterm: search for
      * @param array $array: search inside
      * @param string $target: search for value or key
      * @return bool true/false if found string in array
    */
    public static function MultidArraySearch(string $searchterm = "", array $array = array(), string $target = 'key') {
      $found = false;
      foreach ($array as $key => $item) {
        // define where to search
        if($target == "value"):
          // search for value
          $searcharea = $item;
        else:
          // search for key
          $searcharea = $key;
        endif;
        // start to search
        if ($searcharea === $searchterm):
          $found = true;
          break;
        elseif (is_array($item)):
          $found = SELF::MultidArraySearch($searchterm, $item, $target);
          if($found):
            break;
          endif;
        endif;
      }
      return $found;
    }


    /* 1.11 CLEAN PHONE NUMBER
    /------------------------*/
    /**
      * clean given string from spaces, + or () so it can be used as phone number link
      * @param string $number: given phone number
      * @return string clean number
    */
    public static function cleanPhoneNr(string $number = ''){
      // clean country code
      $ccc_number = str_replace(array('+', ' '), array('00', ''), $number);
      // check if number contains ()
      if(strpos($ccc_number, '(') !== false):
        // check if ( is on first position
        $ccc_number_l = substr($ccc_number, 0, 1);
        $phone_text_between = PARENT::getBetween($ccc_number , "(", ")");
        $clean_number = $ccc_number_l == "(" ? $phone_text_between . str_replace(array('(', ')'), array('', ''), $ccc_number) : str_replace(array('(' . $phone_text_between . ')'), array(''), $ccc_number);
      else:
        $clean_number = $ccc_number;
      endif;
      // output
      return $clean_number;
    }


    /* 1.12 DELETE FOLDER
    /------------------------*/
    /**
      * Delete folder with files and subfolders inside
      * @param string $DirName: path to file or directory
      * @return bool true/false
    */
    private function deleteFolder(string $DirName = ''){
      // check the folder
      $check = SELF::CheckDir($DirName);
      // check if file or path exists
      if($check):
        $files = glob($DirName . '/*');
        foreach($files as $file){
          // check file type
          if(is_file($file)):
            // delete files
            unlink($file);
          else:
            // delete files inside folder
            SELF::deleteFolder($file);
            // delete folder
            rmdir($file);
          endif;
        }
        $message = true;
      else:
        $message = false;
      endif;
      // output
      return $message;
    }


    /* 1.13 SORT ARRAY
    /------------------------*/
    /**
      * @param array $array: array to sort
      * @param string $on: select column to sort by
      * @param string $order: order direction
      * @param bool $date: if sort value is a date
      * @return array sorted array
    */
    function MultidArraySort(array $array = array(), string $on = "0", string $order = "ASC", bool $date = false){
      $new_array = array();
      $sortable_array = array();
      // fallback for special chars
      $CHARsearch   = array("Ä","ä","Ö","ö","Ü","ü","ß","-");
      $CHARreplace  = array("Ae","ae","Oe","oe","Ue","ue","ss"," ");
      if (count($array) > 0) {
          foreach ($array as $k => $v) {
              if (is_array($v)) {
                  foreach ($v as $k2 => $v2) {
                      if ($k2 == $on) {
                          $sortable_array[$k] = $date !== false ? strtotime($v2) : str_replace($CHARsearch, $CHARreplace, $v2);
                      }
                  }
              } else {
                  $sortable_array[$k] = $date !== false ? strtotime($v) : $v;
              }
          }
          switch ($order) {
              case "ASC":
                  natcasesort($sortable_array);
              break;
              case "DESC":
                  natcasesort($sortable_array);
                  $sortable_array = array_reverse($sortable_array, true);
              break;
          }
          foreach ($sortable_array as $k => $v) {
              $new_array[$k] = $array[$k];
          }
      }
      return $new_array;
    }


    /* 1.14 CLEAN ARRAY
    /------------------------*/
    /**
      * @param array $array: array to sort
      * @param string $on: select column to sort by
      * @param string $order: order direction
      * @param bool $date: if sort value is a date
      * @return array sorted array
    */
    public static function CleanArray(array $fields = array(), int $repeat = 0){
      $new_fields = array();
      foreach ($fields as $key => $value) {
        if(is_array($value)):
          if(strlen(implode($value)) !== 0):
            $new_fields[$key] = SELF::CleanArray($value);
          endif;
        else:
          if($value !== NULL && $value !== FALSE && $value !== ''):
            $new_fields[$key] = $value;
          endif;
        endif;
      }
      if($repeat >= 1):
        $repeat--;
        $new_fields = SELF::CleanArray($new_fields, $repeat);
      endif;
      return $new_fields;
    }


    /* 1.15 SLUGIFY STRING
    /------------------------*/
    /**
      * @param string $text: text to slugify
      * @return string sorted array
    */
    public static function Slugify(string $text = '') {
      // trim (remove whitespace before/after string)
      $text = trim($text, '-');
      // replace umlaute
      $text = preg_replace (['/ä/','/Ä/','/ö/','/Ö/','/ü/','/Ü/','/ß/'] , ['ae','ae','oe','oe','ue','ue','ss'], $text);
      // replace special symbols
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);
      // set lowercase
      $text = strtolower($text);
      return $text;
    }


    /* 1.16 INSERT TO ARRAY AT SPACIFIC POSITION
    /------------------------*/
    /**
      * insert array into other array at specific position
      * @param array $new: new content
      * @param array $existing: existing content
      * @param int $position: position to insert
      * @return array with new content
    */
    public function AddToArrayPosition(array $new = array(), array $existing = array(), $position = 1){
      $existing = array_merge(
          array_slice( $existing, 0, $position, true ),
          $new,
          array_slice( $existing, $position, null, true )
      );
      return $existing;
    }



  /*==================================================================================
  2.0 DATES
  ==================================================================================*/

  /* 2.1 CHECK IF VARS ARE OUT OF DATE
  /------------------------*/
  /**
    * @param string $start: start date
    * @param string $end: end date
    * @return bool if times are true or false
  */
  function DateCheck(string $start = "", string $end = "", string $factor = "between"){
    // vars
    $current_date = time();
    $startdate = strtotime($start);
    $enddate = strtotime($end);
    // check start date
    if($start !== ""):
      if($startdate < $current_date):
        $show_start = "past";
      else:
        $show_start = "future";
      endif;
    else:
      $show_start = "empty";
    endif;
    // check end date
    if($end !== ""):
      if($enddate >= $current_date):
        $show_end = "future";
      else:
        $show_end = "past";
      endif;
    else:
      // end date fallback
      $show_end = "empty";
    endif;

    // returning the validation
    if($factor == "between"):
        // if event started and still running
        if($show_start == "past" && $show_end == "future"):
          return true;
        else:
          return false;
        endif;
    elseif($factor == "future"):
        // if event is in the future
        if($show_start == "future" && $show_end == "future" || $show_start == "future" && $show_end == "empty"):
          return true;
        else:
          return false;
        endif;
    elseif($factor == "past"):
        // if event is in the past
        if($show_start == "past" && $show_end == "past" || $show_start == "past" && $show_end == "empty" || $show_start == "empty" && $show_end == "past"):
          return true;
        else:
          return false;
        endif;
    endif;
  }


  /* 2.2 DATE RANGE FORMAT
  /------------------------*/
  /**
    * @param string $start: start date
    * @param string $end: end date
    * @param string $seperator: seperates start with end date
    * @return string date range start - end
  */
  function DateRange(string $start = "", string $end = "", string $seperator = "-" ) {
      // vars
      $current_date = time();
      $startdate = strtotime($start);
      $enddate = strtotime($end);
      $output = '';
      // start date
      if(!Empty($end) && date("m", $startdate) == date("m", $enddate) && date("Y", $startdate) == date("Y", $enddate) && $start !== $end):
        // if end date is given and is in the same month and year
        $output .= !Empty($start) ? date("d.", $startdate) : '';
      elseif(!Empty($end) && date("m", $startdate) !== date("m", $enddate) && date("Y", $startdate) == date("Y", $enddate) && $start !== $end):
        // if end date is given and is not the same month but in the same year
        $output .= !Empty($start) ? date("d.m.", strtotime($start)) : '';
      else:
        $output .= !Empty($start) ? date("d.m.Y", strtotime($start)) : '';
      endif;
      // seperate
      $output .= !Empty($start) && !Empty($end) && $start !== $end ? ' ' . $seperator . ' ' : '';
      // end date
      $output .= !Empty($end) && $start !== $end ? date("d.m.Y", strtotime($end)) : '';

      return $output;
  }



  /*==================================================================================
    3.0 FOR FORMULARS
  ==================================================================================*/

  /* 3.1 GET POST
  /------------------------*/
  /**
    * @param string $name: variable name
    * @param $default: default text for nonexistent or empty variables
    * @return string from called value or default text
  */
  public static function getFormPost(string $name, $default=""){
      // check if given variable exists
      if(isset($_REQUEST[$name])):
        $s = $_REQUEST[$name];
      elseif (isset($_POST[$name])):
        $s = $_POST[$name];
      elseif (isset($_GET[$name])):
        $s = $_GET[$name];
      else:
        $s = $default;
      endif;
      // return value
      return ($s) == "" ? $default : $s;
  }


  /* 3.2 CHECK IF OPTION IS SELECTED
  /------------------------*/
  /**
    * @param string $value: input value to check
    * @param array/string $range: selected array/string value
    * @return selected attribute if input value is in post array
  */
  public static function setSelected($value,$range) {
    if(!is_array($range)) {
      return ($value==$range) ? "selected='selected'" : "";
    } else {
      return (in_array($value,$range)) ? "selected='selected'" : "";
    }
  }


  /* 3.3 CHECK IF CHECKBOX IS CHECKED
  /------------------------*/
  /**
    * @param string $value: input value to check
    * @param array/string $range: checked array/string value
    * @return checked attribute if input value is in post array
  */
  public static function setChecked($value,$range) {
    if(!is_array($range)) {
      return ($value==$range) ? "checked='checked'" : "";
    } else {
      return (in_array($value,$range)) ? "checked='checked'" : "";
    }
  }



  /*==================================================================================
    4.0 FOR WORDPRESS
  ==================================================================================*/

  /* 4.1 GET CURRENT LANGUAGE
  /------------------------*/
  /**
  * get current language code
  * @return string
  */
  public static function getCurrentLang(){
    // check if wpml oder polylang is active
    if (class_exists('SitePress')) {
      // WPML
      return ICL_LANGUAGE_CODE;
    } elseif(function_exists('pll_the_languages')){
      // POLYLANG
      return pll_current_language();
    } else {
      // LANGUAGE FALLBACK
       return substr(get_locale(), 0, 2);
    }
  }


  /* 4.2 ADD USER ROLE
  /------------------------*/
  /**
  * get WP login formular
  * @param bool $name: User role name and slug
  * @param bool $editor: activate/disable default editing
  * @param bool $user_args: add or rewrite user role access
  */
  function setWProle(string $name = "", bool $editor = false, $user_args = array()){
    // merge given with default settings
    $defaults = array(
      'read' => $editor, // true allows this capability
      'edit_posts' => $editor, // Allows user to edit their own posts
      'edit_pages' => $editor, // Allows user to edit pages
      'edit_others_posts' => $editor, // Allows user to edit others posts not just their own
      'create_posts' => $editor, // Allows user to create new posts
      'manage_categories' => $editor, // Allows user to manage post categories
      'publish_posts' => $editor, // Allows the user to publish, otherwise posts stays in draft mode
      'edit_themes' => $editor, // false denies this capability. User can’t edit your theme
      'install_plugins' => $editor, // User cant add new plugins
      'update_plugin' => $editor, // User can’t update any plugins
      'update_core' => $editor // user cant perform core updates
    );
    $config = array_merge($defaults, $user_args);
    // remove space from role name
    if($name !== ""):
      $role = str_replace(' ', '', $name);
      // create role
      $result = add_role(
        $role,
        __($name, 'WP User'),
        $config
      );
    endif;
  }


  /* 4.3 ADD CUSTOM TAXONOMY
  /------------------------*/
  /**
  * get WP login formular
  * @param string $cpt: Post Type slug
  * @param array $taxonomies: All informations to the taxonomies
  * @param bool $user_args: add or rewrite user role access
  * "taxanomies": {
  *   "equipments": {
  *     "label": "Equipments",
  *     "hierarchical": true,
  *     "query_var": true
  *   }
  * }
  */
  public function register_cpt_taxonomy(string $cpt = "post", array $taxonomies = array()) {
    foreach ($taxonomies as $tax_key => $tax) {
      // check if $tax ist not empty
      if(!empty($tax)):
        // insert tax args
        $args = array(
          'label' => __($tax["label"], 'WP Taxonomies'),
          'hierarchical' => true,
          'query_var' => true,
          'show_in_rest' => true,
          'rewrite' => array( 'slug' => $tax_key )
        );
        // custom settings
        foreach ($tax as $key => $value) {
          if($key == 'label' || $key == 'rewrite'):
          else:
            $args[$key] = $value;
          endif;
        }
        register_taxonomy( $tax_key, $cpt, $args );
      endif;
    }
  }


  /* 4.4 RETURN TAXONOMY TERMS IN A LIST
  /------------------------*/
  /**
    * @param string $slug: taxonomy slug
    * @param string $id: if you like to list selected post taxonomies
    * @return string list
  */
  public static function ListTaxonomies(string $slug = "", int $id = 0){
    // vars
    $output = '';
    $taxonomy_details = $id > 0 ? get_the_terms( $id, $slug ) : get_taxonomy( $slug );
    $tax_arg = array(
            'hide_empty' => false
    );
    $tax = $id > 0 ? $taxonomy_details : get_terms( $slug, $tax_arg );
    // output
    $output .= '<ul class="list-' . $slug . '">';
      if ( ! empty( $tax ) && ! is_wp_error( $tax ) ):
        foreach ( $tax as $t ) {
            $output .= '<li class="' . $t->slug . '">';
                $output .= $t->name;
            $output .= '</li>';
        }
      endif;
    $output .= '</ul>';

    return $output;
  }


  /* 4.5 LOGIN FORMULAR
  /------------------------*/
  /**
  * get WP login formular
  * @param bool $redirect: redirect url
  * @return string login formular
  */
  public function WPLoginForm(string $redirect = "") {
    // vars
    $output = '';

    $output .= '<div class="login-area">';
        if($_GET['login'] == "logged_out"){
          $output .= '<p class="login-message login_success">';
            $output .= __('You have been logged out successfully', 'WP Login');
          $output .= '</p>';
        }
        $output .= '<h3>' . __( 'Login', 'WP Login' ) . '</h3>';
        $output .= '<p class="desc">' . __( 'Please enter your user data to login', 'WP Login' ) . '</p>';
        // login form configuration
        $args = array(
          'echo'           => false,
          'remember'       => false,
          'redirect'       => $redirect !== '' ? $redirect : ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
          'form_id'        => 'loginform',
          'id_username'    => 'user_login',
          'id_password'    => 'user_pass',
          'id_remember'    => 'rememberme',
          'id_submit'      => 'wp-submit',
          'label_username' => __( 'Username', 'WP Login' ),
          'label_password' => __( 'Password', 'WP Login' ),
          'label_remember' => __( 'Remember Me', 'WP Login' ),
          'label_log_in'   => __( 'Log In', 'WP Login' ),
          'value_username' => '',
          'value_remember' => false
        );
        // login form
        $output .= wp_login_form( $args );
        // error message if login has failed
        if($_GET['login'] == "failed"){
          $output .= '<p class="login-message login_error">';
            $output .= __('Username and/or password is wrong', 'WP Login');
          $output .= '</p>';
        }
        $output .= '<a href="' . wp_lostpassword_url() . '">' . __( 'Lost Password?', 'WP Login' ) . '</a>';
    $output .= '</div>';

    return $output;
  }


  /* 4.6 UPLOAD WP IMG
  /------------------------*/
  /**
    * Upload image to media directory
    * @param string $upload_file: path to file or directory
    * @return int uploaded image ID
  */
  public function WPuploadFile(string $upload_file = "") {
    if (filter_var($upload_file, FILTER_VALIDATE_URL) !== FALSE):

      $fileinfo = pathinfo($upload_file);
      $filename = $fileinfo['filename'] . '.' . $fileinfo['extension'];

      $upload_dir = wp_upload_dir();
      $image_data = file_get_contents($upload_file);

      if (wp_mkdir_p($upload_dir['path'])) {
          $file = $upload_dir['path'] . '/' . $filename;
      } else {
          $file = $upload_dir['basedir'] . '/' . $filename;
      }
      file_put_contents($file, $image_data);
      $wp_filetype = wp_check_filetype($filename, null);
      $attachment = array(
          'guid' => $upload_dir['url'] . '/' . $filename,
          'post_mime_type' => $wp_filetype['type'],
          'post_title' => sanitize_file_name($filename),
          'post_content' => '',
          'post_type' => 'listing_type',
          'post_status' => 'inherit',
      );
      $attach_id = wp_insert_attachment($attachment, $file);
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attach_data = wp_generate_attachment_metadata($attach_id, $file);
      wp_update_attachment_metadata($attach_id, $attach_data);
      return $attach_id;

      // return $file;
    endif;
  }



  /*==================================================================================
    5.0 COORDINATES
  ==================================================================================*/

    /* 5.1 CONVERT: WGS lat/long TO CH1903 y
    /------------------------*/
    /**
    * MAP coordinates to CH1903 / CH1903+
    * @param bool $lat: coordinate latitude
    * @param bool $long: coordinate longitude
    * @param bool $plus: CH1903 false / CH1903+ true
    * @return string CH latitude
    */
    public static function WGStoCHy($lat, $long, bool $plus = true) {
      // Converts decimal degrees sexagesimal seconds
      $lat = SELF::DECtoSEX($lat);
      $long = SELF::DECtoSEX($long);
      // Auxiliary values (% Bern)
      $lat_aux = ($lat - 169028.66)/10000;
      $long_aux = ($long - 26782.5)/10000;
      // Process Y
      $y = 600072.37
         + 211455.93 * $long_aux
         -  10938.51 * $long_aux * $lat_aux
         -      0.36 * $long_aux * pow($lat_aux,2)
         -     44.54 * pow($long_aux,3);
      // output
      $p = $plus === true ? 1 : '';
      return $p . $y;
    }


    /* 5.2 CONVERT: WGS lat/long TO CH1903 x
    /------------------------*/
    /**
    * MAP coordinates to CH1903 / CH1903+
    * @param bool $lat: coordinate latitude
    * @param bool $long: coordinate longitude
    * @param bool $plus: CH1903 false / CH1903+ true
    * @return string CH longitude
    */
    public static function WGStoCHx($lat, $long, bool $plus = true) {
      // Converts decimal degrees sexagesimal seconds
      $lat = SELF::DECtoSEX($lat);
      $long = SELF::DECtoSEX($long);
      // Auxiliary values (% Bern)
      $lat_aux = ($lat - 169028.66)/10000;
      $long_aux = ($long - 26782.5)/10000;
      // Process X
      $x = 200147.07
         + 308807.95 * $lat_aux
         +   3745.25 * pow($long_aux,2)
         +     76.63 * pow($lat_aux,2)
         -    194.56 * pow($long_aux,2) * $lat_aux
         +    119.79 * pow($lat_aux,3);
      // output
      $p = $plus === true ? 2 : '';
      return $p . $x;
    }


    /* 5.3 CONVERT: CH1903 y/x TO WGS lat
    /------------------------*/
    /**
    * CH1903 / CH1903+ coordinates to WGS
    * @param bool $lat: coordinate latitude
    * @param bool $long: coordinate longitude
    * @param bool $plus: CH1903 false / CH1903+ true
    * @return string WGS latitude
    */
    public static function CHtoWGSlat($y, $x, bool $plus = true) {
      // update vars if coordinates are CH1903+
      $y = $plus === true ? substr($y, 1) : $y;
      $x = $plus === true ? substr($x, 1) : $x;
      // Converts military to civil and  to unit = 1000km
      // Auxiliary values (% Bern)
      $y_aux = ($y - 600000)/1000000;
      $x_aux = ($x - 200000)/1000000;
      // Process lat
      $lat = 16.9023892
           +  3.238272 * $x_aux
           -  0.270978 * pow($y_aux,2)
           -  0.002528 * pow($x_aux,2)
           -  0.0447   * pow($y_aux,2) * $x_aux
           -  0.0140   * pow($x_aux,3);

      // Unit 10000" to 1 " and converts seconds to degrees (dec)
      $lat = $lat * 100/36;
      // output
      return $lat;
    }


  /* 5.4 CONVERT: CH1903 y/x TO WGS lng
  /------------------------*/
  /**
  * CH1903 / CH1903+ coordinates to WGS
  * @param bool $lat: coordinate latitude
  * @param bool $long: coordinate longitude
  * @param bool $plus: CH1903 false / CH1903+ true
  * @return string WGS longitude
  */
  public static function CHtoWGSlong($y, $x, bool $plus = true) {
    // update vars if coordinates are CH1903+
    $y = $plus === true ? substr($y, 1) : $y;
    $x = $plus === true ? substr($x, 1) : $x;
    // Converts military to civil and  to unit = 1000km
    // Auxiliary values (% Bern)
    $y_aux = ($y - 600000)/1000000;
    $x_aux = ($x - 200000)/1000000;
    // Process long
    $long = 2.6779094
          + 4.728982 * $y_aux
          + 0.791484 * $y_aux * $x_aux
          + 0.1306   * $y_aux * pow($x_aux,2)
          - 0.0436   * pow($y_aux,3);

    // Unit 10000" to 1 " and converts seconds to degrees (dec)
    $long = $long * 100/36;
    // output
    return $long;
  }


  /* 5.5 CONVERT: DEC angle to SEX DMS
  /------------------------*/
  public static function DECtoSEX($angle) {
    // Extract DMS
    $deg = intval( $angle );
    $min = intval( ($angle-$deg)*60 );
    $sec =  ((($angle-$deg)*60)-$min)*60;
    // Result in sexagesimal seconds
    return $sec + $min*60 + $deg*3600;
  }



}

?>
