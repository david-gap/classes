<?
/**
 *
 *
 * Custom classes configuration file
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
 * @version     1.0
*/


/* INIT
/===================================================== */

/* CALL CLASSES
/------------------------*/
// Add all custom classses
function class_registerFunction($class) {
  if ( 0 !== strpos( $class, 'prefix_' ) ) {
      return;
  }
  require_once("classes/$class/class.$class.php");
}
spl_autoload_register('class_registerFunction');


/* ACTIVATE CLASS INIT
/------------------------*/
$classes = array(
  "prefix_WPimg",
  "prefix_WPimgAlt",
  "prefix_WPintranet"
);

if(in_Array("prefix_WPimg", $classes)):
  $myWPimg = new prefix_WPimg();
endif;
if(in_Array("prefix_WPimgAlt", $classes)):
  $myWPimgAlt = new prefix_WPimgAlt();
endif;
if(in_Array("prefix_WPintranet", $classes)):
  $myWPintranet = new prefix_WPintranet();
endif;

?>
