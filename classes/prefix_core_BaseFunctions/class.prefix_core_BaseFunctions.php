<?
/**
 *
 *
 * Base dev functions - parent for all custom classes
 * Author:      David Voglgsnag
 * @version     1.5
 *
 */

class prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 FOR DEVELOPMENT
  ==================================================================================*/

  /* LOG FILE
  /------------------------*/
  /**
  * add text to log file
  * @param string/array $content: content to write inside log file
  * @param string $path: file path
  * @param string $name: file name
  */
  public static function LogFile($content = NULL, string $path = "", string $name = "log.txt"){
    // file directory
    $path = $path !== "" ? $path : $_SERVER['DOCUMENT_ROOT'] . "/log/";
    // check if slash exists between path and name
    $path = substr($path, -1) == "/" ? $path : $path . "/";
    // create directory
    SELF::CreateDirectory($path);
    // file name
    $filename = $path . $name;
    // check if file/path exists
    if(file_exists($filename)):
      $mode = 'a';
    else:
      $mode = 'w';
    endif;
    // open file
    $file = fopen($filename, $mode);
    // add text to file
    if(is_array($content)):
      foreach ($content as $key => $value) {
        fwrite($file, "\n" . date('Y-m-d H:i:s') . " - " . $value);
      }
    else:
      fwrite($file, "\n" . date('Y-m-d H:i:s') . " - " . $content);
    endif;
    // close file
    fclose($file);
  }


  /* ABSOLUTE FILE EXISTS
  /------------------------*/
  /**
  * check if absolute url exists
  * @return bool true/false
  */
  public static function checkRemoteFile($url){
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
  }


  /* EXPLODE COMMA SEPERATED ARRAY
  /------------------------*/
  public static function AttrToArray(string $attr){
    // remove spaces from string
    $clean = str_replace(", ", ",", $attr);
    // create array
    $array = explode(',', $clean);

    return $array;
  }


  /* PRICE FORMAT
  /------------------------*/
  /**
    * @param string $preis: given price
    * @param string $seperator: seperates the $ from the cents
    * @return string price
  */
  public static function formatPrice(int $preis = 0, string $seperator = "." ) {
      $preis += .00;
      return number_format($preis,2,$seperator," ");
  }


  /* BROWSER CHECK
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


  /* GENERATE SHORT ID
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


  /* CHECK IF FOLDER EXISTS
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


  /* CREATE FOLDER
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


  /* COPY FOLDER CONTENT AND SUB FOLDERS
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


  /* GET CONTENT FROM STRING BETWEEN TWO CHARS/CHAR GROUPS
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


  /* FIND KEY IN MULTIDIMENSIONAL ARRAY
  /------------------------*/
  /**
    * @param string $searchterm: search for
    * @param array $array: search inside
    * @return bool true/false if found string in array
  */
  public static function MultidArraySearch(string $searchterm = "", array $array = array()) {
    $found = false;
    foreach ($array as $item) {
    if ($item === $searchterm) {
            $found = true;
            break;
        } elseif (is_array($item)) {
            $found = SELF::MultidArraySearch($searchterm, $item);
            if($found) {
                break;
            }
        }
    }
    return $found;
  }



  /*==================================================================================
    2.0 FOR FORMULARS
  ==================================================================================*/

  /* GET POST
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


  /* CHECK IF OPTION IS SELECTED
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


  /* CHECK IF CHECKBOX IS CHECKED
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
    3.0 FOR WORDPRESS
  ==================================================================================*/

  /* GET CURRENT LANGUAGE
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


  /* ADD USER ROLE
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


  /* ADD CUSTOM TAXONOMY
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


  /* LOGIN FORMULAR
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



}

?>
