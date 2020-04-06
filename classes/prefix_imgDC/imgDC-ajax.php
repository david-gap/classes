<?
/**
 *
 *
 * Ajax actions
 * Author:      David Voglgsnag
 */

// Get variables from ajax request
$run_action = $_POST['action'] ? $_POST['action'] : $_GET['action'];

if($run_action) {

  /* CONNECT TO DATABASE
  /===================================================== */
  $allow_connection = array('DominantColors', 'LoadLazyIMG');
  if(in_array($run_action, $allow_connection)):
      $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $url = $_SERVER['REQUEST_URI'];
      $my_url = explode('wp-content' , $url);
      $path = $_SERVER['DOCUMENT_ROOT']."/".$my_url[0];
      include_once $path . '/wp-config.php';
      include_once $path . '/wp-includes/wp-db.php';
      include_once $path . '/wp-includes/pluggable.php';
  endif;

  /* GENERATE DOMINANT COLORS
  /------------------------*/
  function setDominantColor(){
    $output  = '';
    // call class
    $output .= prefix_imgDC::generateDominantColors();
    // return message
    echo $output;
  }

  /* LOAD SINGLE IMG
  /------------------------*/
  function getIMG(){
    $output  = '';
    // call class
    $output .= prefix_imgDC::getIMG($_POST['id']);
    // return message
    echo $output;
  }



  /* RUN FUNCTIONS
  /===================================================== */
  switch ($run_action) {
    case "DominantColors":
      setDominantColor();
      break;
    case "LoadLazyIMG":
      getIMG();
      break;
    default:
      echo "no access granted";
  }

}
