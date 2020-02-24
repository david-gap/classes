<?
/**
 *
 *
 * Intranet
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
 * @version     1.1
 *
 * Class running with http://www.directorylister.com
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
  1.4 INITIAL INTRANET
2.0 FUNCTIONS
  2.1 GET CONFIGURATION FORM CONFIG FILE
  2.2 REDIRECT LOGIN AFTER LOGOUT
  2.3 REDIRECT LOGIN IF USERNAME IS EMPTY
  2.4 REDIRECT LOGIN IF PASSWORD IS EMPTY OR INCORRECT
  2.5 CREATE CUSTOM ROLES
  2.6 SAVE USERS CUSTOM FIELDS
3.0 OUTPUT
  3.1 INTRANET CONTAINER
  3.2 NEW DIRECTORY
  3.3 USER BACKEND CUSTOM FIELDS
=======================================================*/


class prefix_WPintranet extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

  /* 1.1 CONFIGURATION
  /------------------------*/
  /**
    * default vars
    * @param string/array $WPintranet_roles: define wp roles. if it is a string and folder access is by role, add folderaccess to array
    * @param string $WPintranet_root: root directory
    * @param static $WPintranet_mode: folder settings
    * @param static $WPintranet_directory: main directory
    * @param static $WPintranet_folders: folders to create inside the main directory
  */
  static $WPintranet_roles = 'intranet';
  static $WPintranet_root = ABSPATH;
  static $WPintranet_mode = 0777;
  static $WPintranet_directory = 'intranet';
  static $WPintranet_folders = array('Folder 1', 'Folder 2', 'Folder 3');


  /* 1.2 ON LOAD RUN
  /------------------------*/
  public function __construct() {
    // update default vars with configuration file
    SELF::updateVars();
    // add user roles
    add_action( 'init', array( $this, 'AddUserRole' ) );
    // create directories
    add_action( 'init', array( $this, 'initialIntranet' ) );
    // add custom fields
    add_action( 'show_user_profile', array( $this, 'UserCustomFields' ) );
    add_action( 'edit_user_profile', array( $this, 'UserCustomFields' ) );
    add_action( 'personal_options_update', array( $this, 'SaveUserCustomFields' ) );
    add_action( 'edit_user_profile_update', array( $this, 'SaveUserCustomFields' ) );
    // add shortcode
    add_shortcode('intranet', array( $this, 'IntranetOutput' ) );
    // redirect for login fail/validation
    add_action( 'wp_login_failed', array( $this, 'intranet_login_fail' ) );
    add_action( 'wp_authenticate', array( $this, '_catch_empty_user' ), 1, 2 );
    add_filter('login_redirect', array( $this, '_catch_login_error' ), 10, 3);
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


  /* 1.4 INITIAL INTRANET
  /------------------------*/
  /**
    * create folder
    * @param string $DirName: folder name
    * @return true/false
  */
  public function initialIntranet(){
    // check if main directory exists
    $parent_exists = PARENT::CheckDir(SELF::$WPintranet_root . SELF::$WPintranet_directory);
    // create main directory
    if(!$parent_exists):
      // create main directory
      PARENT::CreateDirectory(SELF::$WPintranet_root . SELF::$WPintranet_directory);
      PARENT::CreateDirectory(SELF::$WPintranet_root . SELF::$WPintranet_directory . '/list-resources');
      PARENT::copyDirectory(__DIR__ . '/list-resources', SELF::$WPintranet_root . SELF::$WPintranet_directory . '/list-resources', SELF::$WPintranet_mode);
      // wait before creating child folders
    endif;
    // create child folders
    if(SELF::$WPintranet_folders):
      // if child folders are given
      foreach (SELF::$WPintranet_folders as $folder) {
        // check if main directory exists
        $child_exists = PARENT::CheckDir(SELF::$WPintranet_root . SELF::$WPintranet_directory . '/' . $folder);
        if(!$child_exists):
          PARENT::CreateDirectory(SELF::$WPintranet_root . SELF::$WPintranet_directory . '/' . $folder);
          copy(__DIR__ . '/folders/index.php', SELF::$WPintranet_root . SELF::$WPintranet_directory . '/' . $folder . '/index.php');
        endif;
      }
    endif;
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
      if($configuration && array_key_exists('WPintranet', $configuration)):
        // class configuration
        $myConfig = $configuration['WPintranet'];
        // update vars
        SELF::$WPintranet_roles = array_key_exists('roles', $myConfig) ? $myConfig['roles'] : SELF::$WPintranet_roles;
        SELF::$WPintranet_root = array_key_exists('root', $myConfig) ? $myConfig['root'] : SELF::$WPintranet_root;
        SELF::$WPintranet_mode = array_key_exists('mode', $myConfig) ? $myConfig['mode'] : SELF::$WPintranet_mode;
        SELF::$WPintranet_directory = array_key_exists('directory', $myConfig) ? $myConfig['directory'] : SELF::$WPintranet_directory;
        SELF::$WPintranet_folders = array_key_exists('folders', $myConfig) ? $myConfig['folders'] : SELF::$WPintranet_folders;
      endif;
    }

    /* 2.2 REDIRECT LOGIN AFTER LOGOUT
    /------------------------*/
    function intranet_login_fail( $username ) {
      $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
      $logout_url = strpos($_SERVER['HTTP_REFERER'], '?login=failed') ? $_POST['redirect_to'] : $_POST['redirect_to'] . '?login=failed';
      // if there's a valid referrer, and it's not the default log-in screen
      if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
          wp_redirect($logout_url);  // let's append some information (login=failed) to the URL for the theme to use
          exit;
      }
    }


    /* 2.3 REDIRECT LOGIN IF USERNAME IS EMPTY
    /------------------------*/
    function _catch_empty_user( $username, $pwd ) {
      $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
      $logout_url = strpos($_SERVER['HTTP_REFERER'], '?login=failed') ? $_POST['redirect_to'] : $_POST['redirect_to'] . '?login=failed';
      // if there's a valid referrer, and it's not the default log-in screen
      if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
        if ( empty( $username ) ) {
          wp_safe_redirect( $logout_url );
          exit();
        }
      }
    }


    /* 2.4 REDIRECT LOGIN IF PASSWORD IS EMPTY OR INCORRECT
    /------------------------*/
    function _catch_login_error($redir1, $redir2, $wperr_user){
      $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
      $logout_url = strpos($_SERVER['HTTP_REFERER'], '?login=failed') ? $_POST['redirect_to'] : $_POST['redirect_to'] . '?login=failed';

      if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
        if(!is_wp_error($wperr_user) || !$wperr_user->get_error_code()) return $redir1;
        switch($wperr_user->get_error_code())
        {
            case 'incorrect_password':
              wp_redirect($logout_url);
            case 'empty_password':
              wp_redirect($logout_url);
            case 'invalid_username':
              wp_redirect($logout_url);
            default:
              wp_redirect($logout_url);
        }
        return $redir1;
      } else {
        $backend = home_url('wp-admin');
        $frontend = home_url();
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            if($user->roles == 'intranet'):
              wp_redirect($frontend);
            elseif($user->roles && $user->roles !== 'intranet'):
              wp_redirect($backend);
            endif;
        } else {
            return admin_url();
        }
      }
    }


    /* 2.5 CREATE CUSTOM ROLES
    /------------------------*/
    function AddUserRole(){
      // check if string or array
      if( is_array(SELF::$WPintranet_roles) ):
        foreach (SELF::$WPintranet_roles as $role_name) {
          PARENT::setWProle($role_name['name'], SELF::$WPintranet_roles_arg);
        }
      else:
        PARENT::setWProle(SELF::$WPintranet_roles, SELF::$WPintranet_roles_arg);
      endif;
    }


    /* 2.6 SAVE USERS CUSTOM FIELDS
    /------------------------*/
    function SaveUserCustomFields( $user_id ) {
      // vars
      $user = get_userdata($user_id);
      $user_roles = $user->roles;
      $user_role = array_shift($user_roles);
      // save custom field if user role is intranet
      if($user_role == SELF::$WPintranet_roles):
          if ( !current_user_can( 'edit_user', $user_id ) )
          return FALSE;
          update_usermeta( $user_id, 'intranet_access', $_POST['intranet_access'] );
      endif;
    }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/

  /* 3.1 INTRANET CONTAINER
  /------------------------*/
  public function IntranetOutput() {
    // vars
    $output = '';

    $output .= '<div id="intranet">';

    if ( is_user_logged_in() ) {
      // if user is logged in
      $user = wp_get_current_user();
      $user_roles = $user->roles;
      $user_role = array_shift($user_roles);
      $user_id = 'user_'.$user->id;
      // get accessable users
      if(is_array(SELF::$WPintranet_roles)):
        $allowed_roles = SELF::$WPintranet_roles;
      else:
        $allowed_roles = array(SELF::$WPintranet_roles);
      endif;
      $allowed_roles[] = 'administrator';

      // if intranet user is logged in
      if( array_intersect($allowed_roles, $user->roles ) ):
        // define start directory and accessable folders
        if($user_role == "administrator") {
          $all_directorys = true;
          $start = SELF::$WPintranet_folders["0"];
        } else {
          $active_folders = get_the_author_meta( 'intranet_access', $user->ID );
          $num_folders = count($active_folders);
          $all_directorys = false;
          $start = $active_folders[0];
        }

        $logout_vars = strpos($_SERVER['HTTP_REFERER'], '?login=') ? str_replace('?login=failed', '?login=logged_out', $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'] . '?login=logged_out';
        $logout_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $logout_vars;

        // outout header
        $output .= '<div class="intranet-header">';
          $output .= '<h3>' . __('Eingeloggt als', 'Class Intranet') . ' «' . $user->display_name . '»</h3>';
          $output .= '<span><a href="' . wp_logout_url($logout_url) . '" class="logout">' . __('Logout', 'Class Intranet') . '</a></span>';
        $output .= '</div>';

        // output buttons
        $output .= '<ul class="directories">';
          foreach (SELF::$WPintranet_folders as $folder) {
            if($all_directorys == true || in_array($folder, $active_folders) && $num_folders > 1):
              $output .= '<li data-directory="' . $folder . '" data-title="' . $folder . '" tabindex="0">';
              $output .= '' . $folder;
              $output .= '</li>';
            endif;
          }
        $output .= '</ul>';

        // output intranet
        $output .= '<div id="myftp" data-active="' . $start . '">';
        $output .= '</div>';
      else:
        // if user is logged in but no access rights
        $output .= __('Sie haben keine Berechtigungen für das Intranet', 'Class Intranet');
      endif;
    } else {
      // if no user is logged in - show login form
      $output .= PARENT::WPLoginForm();
    }

    $output .= '</div>';

    return $output;
  }


  /* 3.2 NEW DIRECTORY
  /------------------------*/
  public static function IntranetNewDirectory(string $folder = '', string $folder_title = '') {
    return '<iframe src="../../' . SELF::$WPintranet_directory . '/' . $folder . '/?title=' . $folder_title . '" id="myftp-iframe" scrolling="no" frameborder="0"></iframe>';
  }


  /* 3.3 USER BACKEND CUSTOM FIELDS
  /------------------------*/
  /*
    add custom field to define access rights to the folders
  */
  function UserCustomFields( $user ) {
    // vars
    $output = '';
    $user_roles = $user->roles;
    $user_role = array_shift($user_roles);

    if($user_role == SELF::$WPintranet_roles):
      // get the saved value
      $active_value = get_the_author_meta( 'intranet_access', $user->ID );
      // output label & input
      $output .= '<table class="form-table">';
        $output .= '<tr>';
          $output .= '<th>';
              $output .= '<label for="intranet">' . __( 'Intranet access', 'Class Intranet' ) . '</label>';
          $output .= '</th>';
          $output .= '<td>';
              foreach (SELF::$WPintranet_folders as $access) {
                if(is_array($access)):
                  $value = $access['name'];
                else:
                  $value = $access;
                endif;
                $checked = PARENT::setChecked($value, $active_value);
                $output .= '<div>';
                  $output .= '<label>';
                      $output .= '<input type="checkbox" name="intranet_access[]" value="' . $value . '" ' . $checked . '>';
                      $output .= $value;
                  $output .= '</label>';
                $output .= '</div>';
              }
          $output .= '<td>';
        $output .= '</tr>';
      $output .= '</table>';

      echo $output;

    endif;
  }



}

?>
