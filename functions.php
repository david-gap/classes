<?

/*==================================================================================
  CALL CLASSES
==================================================================================*/
// Add all custom classses
function dmili_registerFunction($class) {
  if ( 0 !== strpos( $class, 'dmili_' ) ) {
      return;
  }
  require_once("$class/classes/class.$class.php");
}
spl_autoload_register('dmili_registerFunction');

?>
