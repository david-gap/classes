<?php
/**
 *
 *
 * Ajax actions
 * Author:      David Voglgsnag
 */

// Get variables from ajax request
$run_action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

if($run_action):

  /* CONNECT TO DATABASE
  /===================================================== */
  $allow_connection = array('DEMO');
  if(in_array($run_action, $allow_connection)):
      $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $url = $_SERVER['REQUEST_URI'];
      $my_url = explode('wp-content' , $url);
      $path = $_SERVER['DOCUMENT_ROOT']."/".$my_url[0];
      include_once $path . '/wp-config.php';
      include_once $path . '/wp-includes/wp-db.php';
      include_once $path . '/wp-includes/pluggable.php';
      include_once $path . '/wp-admin/includes/user.php';
  endif;



  /* DEMO FUNCTION
  /------------------------*/
  function DemoFunction(){
    $output  = '';
    $output .= '<div></div>';
    // return result
    echo $output;

  }


  /* RUN FUNCTIONS
  /===================================================== */
  switch ($run_action) {
    case "DEMO":
      DemoFunction();
      break;

    default:
      echo "Keine Aktion definiert";
  }

endif;
