<?
/**
 *
 *
 * Base dev functions - parent for all custom classes
 * Author:      David Voglgsnag
 * @version     1.0
 *
 */

class prefix_BaseFunctions {

  /* FOR DEVELOPMENT
  /===================================================== */

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
        $debug['CheckDir'] = 'Dir ' . $DirName . " exists";
        return true;
    } else {
        $debug['CheckDir'] = 'Dir ' . $DirName . " does not exist";
        return false;
    }
  }


  /* CREATE FOLDER
  /------------------------*/
  /**
    * create folder
    * @param string $DirName: folder name
    * @return true/false
  */
  private function CreateDirectory($DirName) {
    // check the folder
    $check = SELF::CheckDir($DirName);
    // create the folder
    if(!$check):
      if (mkdir($DirName)):
          chmod($DirName, SELF::$mode);
          $debug['CreateDir'] = 'Dir ' . $DirName . " does not exist";
          return true;
      else:
          $debug['CreateDir'] = 'Dir ' . $DirName . " does not exist";
          return false;
      endif;
    endif;
  }



  /* FOR FORMULARS
  /===================================================== */

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



  /* FOR WORDPRESS
  /===================================================== */

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



}

?>
