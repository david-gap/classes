<?

/*==================================================================================
  CALL CLASSES
==================================================================================*/
// Add all custom classses
function class_registerFunction($class) {
  if ( 0 !== strpos( $class, 'prefix_' ) ) {
      return;
  }
  require_once("$class/classes/class.$class.php");
}
spl_autoload_register('class_registerFunction');

?>
