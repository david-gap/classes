<?
/**
 *
 *
 * Intranet
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
 * @version     1.0.2
 *
 * Class running with http://www.directorylister.com
*/

class prefix_WPintranet {

  /* CONFIGURATION
  /===================================================== */
  /**
    * default vars
    * @param string/array $wp_roles: define wp roles. if it is a string and folder access is by role, add folderaccess to array
    * @param string $root: root directory
    * @param static $mode: define user rights
    * @param static $directory: main directory
    * @param static $folders: folders to create inside the main directory
  */
  static $wp_roles = 'intranet';
  static $root = ABSPATH;
  static $mode = 0777;
  static $directory = 'intranet';
  static $folders = array('Folder 1', 'Folder 2', 'Folder 3');




  /* INIT
  /===================================================== */
  /*
    wordpress addons
  */
  public function __construct() {
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


  /* FUNCTIONS
  /===================================================== */

  /**
    * copy intanet res to intranet folder
    * @param string $src: source directory
    * @param string $dst: destination directory
  */
  function copyDirectory($src, $dst) {
    // open the source directory
    $dir = opendir($src);
    // Make the destination directory if not exist
    @mkdir($dst);
    chmod($dst, SELF::$mode);
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


  /**
    * create folder
    * @param string $DirName: folder name
    * @return true/false
  */
  public function initialIntranet(){
    // check if main directory exists
    $parent_exists = PARENT::CheckDir(SELF::$root . SELF::$directory);
    // create main directory
    if(!$parent_exists):
      // create main directory
      PARENT::CreateDirectory(SELF::$root . SELF::$directory);
      PARENT::CreateDirectory(SELF::$root . SELF::$directory . '/list-resources');
      SELF::copyDirectory(__DIR__ . '/list-resources', SELF::$root . SELF::$directory . '/list-resources');
      // wait before creating child folders
    endif;
    // create child folders
    if(SELF::$folders):
      // if child folders are given
      foreach (SELF::$folders as $folder) {
        // check if main directory exists
        $child_exists = PARENT::CheckDir(SELF::$root . SELF::$directory . '/' . $folder);
        if(!$child_exists):
          PARENT::CreateDirectory(SELF::$root . SELF::$directory . '/' . $folder);
          copy(__DIR__ . '/folders/index.php', SELF::$root . SELF::$directory . '/' . $folder . '/index.php');
        endif;
      }
    endif;
  }



  /* USER FUNCTIONS
  /===================================================== */

  /*
    add user role for the intranet
  */
  function setRole($name){
    // remove space from role name
    $role = str_replace(' ', '', $name);
    // create role
    $result = add_role(
      $role,
      __($name, 'Class Intranet'),
      array(
        'read' => false, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        'edit_themes' => false, // false denies this capability. User can’t edit your theme
        'install_plugins' => false, // User cant add new plugins
        'update_plugin' => false, // User can’t update any plugins
        'update_core' => false // user cant perform core updates
      )
    );
  }


  function AddUserRole(){
    // check if string or array
    if( is_array(SELF::$wp_roles) ):
      foreach (SELF::$wp_roles as $role_name) {
        SELF::setRole($role_name['name']);
      }
    else:
      SELF::setRole(SELF::$wp_roles);
    endif;
  }


  /*
    add custom field to define access rights to the folders
  */
  function UserCustomFields( $user ) {
    // vars
    $output = '';
    $user_roles = $user->roles;
    $user_role = array_shift($user_roles);

    if($user_role == SELF::$wp_roles):
      // get the saved value
      $active_value = get_the_author_meta( 'intranet_access', $user->ID );
      // output label & input
      $output .= '<table class="form-table">';
        $output .= '<tr>';
          $output .= '<th>';
              $output .= '<label for="intranet">' . __( 'Intranet access', 'Class Intranet' ) . '</label>';
          $output .= '</th>';
          $output .= '<td>';
              foreach (SELF::$folders as $access) {
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


  /*
    save user custom field
  */
  function SaveUserCustomFields( $user_id ) {
    // vars
    $user = get_userdata($user_id);
    $user_roles = $user->roles;
    $user_role = array_shift($user_roles);
    // save custom field if user role is intranet
    if($user_role == SELF::$wp_roles):
        if ( !current_user_can( 'edit_user', $user_id ) )
        return FALSE;
        update_usermeta( $user_id, 'intranet_access', $_POST['intranet_access'] );
    endif;
  }



  /* LOGIN FORM
  /===================================================== */
  public function IntranetLoginForm($redirect=false) {
    // vars
    $output = '';

    $output .= '<div class="login-area">';
        if($_GET['login'] == "logged_out"){
          $output .= '<p class="login-message login_success">';
            $output .= __('You have been logged out successfully', 'Class Intranet');
          $output .= '</p>';
        }
        $output .= '<h3>' . __( 'Login Intranet', 'Class Intranet' ) . '</h3>';
        $output .= '<p class="desc">' . __( 'Please enter your user data to login', 'Class Intranet' ) . '</p>';
        // login form configuration
        $args = array(
          'echo'           => false,
          'remember'       => false,
          'redirect'       => $redirect ? $redirect : ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
          'form_id'        => 'loginform',
          'id_username'    => 'user_login',
          'id_password'    => 'user_pass',
          'id_remember'    => 'rememberme',
          'id_submit'      => 'wp-submit',
          'label_username' => __( 'Username', 'Class Intranet' ),
          'label_password' => __( 'Password', 'Class Intranet' ),
          'label_remember' => __( 'Remember Me', 'Class Intranet' ),
          'label_log_in'   => __( 'Log In', 'Class Intranet' ),
          'value_username' => '',
          'value_remember' => false
        );
        // login form
        $output .= wp_login_form( $args );
        // error message if login has failed
        if($_GET['login'] == "failed"){
          $output .= '<p class="login-message login_error">';
            $output .= __('Username and/or password is wrong', 'Class Intranet');
          $output .= '</p>';
        }
        $output .= '<a href="' . wp_lostpassword_url() . '">' . __( 'Lost Password?', 'Class Intranet' ) . '</a>';
    $output .= '</div>';

    return $output;
  }



  /* OUTPUT
  /===================================================== */

  /* NEW DIRECTORY
  /------------------------*/
  public static function IntranetNewDirectory(string $folder = '', string $folder_title = '') {
    return '<iframe src="../../' . SELF::$directory . '/' . $folder . '/?title=' . $folder_title . '" id="myftp-iframe" scrolling="no" frameborder="0"></iframe>';
  }

  /* SHORTCPDE
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
      if(is_array(SELF::$wp_roles)):
        $allowed_roles = SELF::$wp_roles;
      else:
        $allowed_roles = array(SELF::$wp_roles);
      endif;
      $allowed_roles[] = 'administrator';

      // if intranet user is logged in
      if( array_intersect($allowed_roles, $user->roles ) ):
        // define start directory and accessable folders
        if($user_role == "administrator") {
          $all_directorys = true;
          $start = SELF::$folders["0"];
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
          foreach (SELF::$folders as $folder) {
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
      $output .= SELF::IntranetLoginForm();
    }

    $output .= '</div>';

    return $output;

  }



  /* LOGIN REDIRECTS
  /===================================================== */

  /* REDIRECT LOGIN AFTER LOGOUT
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


  /* REDIRECT LOGIN IF USERNAME IS EMPTY
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


  /* REDIRECT LOGIN IF PASSWORD IS EMPTY OR INCORRECT
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



}

?>
