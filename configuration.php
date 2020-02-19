<?php
/**
 *
 *
 * Custom classes configuration file
 * https://github.com/david-gap/classes
 * • create backend area or get configuration file
 * • include all php classes
 *
 * @author      David Voglgsang
 * @version     1.0
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 CALL CONFIGURATION FILE
  1.3 REGISTER ALL THE CLASSES
  1.4 RUN CLASSES INIT
  1.5 DEBUGING
2.0 RUN FUNCTIONS
  2.1 CONFIGURATION
  2.1 DEBUGING
=======================================================*/


/*==================================================================================
  1.0 INIT & VARS
==================================================================================*/

  /* 1.1 CONFIGURATION
  /------------------------*/
  /**
    * default vars
    * @param array $debug_errors: errors container. leave it empty
    * @param static bool $debug: turn debugging on/off
  */
  $debug_errors = array();
  static $debug = false;


  /* 1.2 CALL CONFIGURATION FILE
  /------------------------*/
  function prefix_JSON_as_Global(){
    // get file
    $configuration_file = get_stylesheet_directory() . '/config/configuration.json';
    // check if file exists or empty
    if(file_exists($configuration_file) && filesize($configuration_file) > 0):
      // set as global
      global $configuration;
      $configuration_content = file_get_contents($configuration_file);
      $configuration = json_decode($configuration_content,true);
    else:
      global $configuration;
      $configuration = false;
      // add debug message
      global $debug_errors;
      $debug_errors['configuration'][] = "Configuration file is missing";
    endif;
  }


  /* 1.3 REGISTER ALL THE CLASSES
  /------------------------*/
  function prefix_registerFunction($class) {
    if ( 0 !== strpos( $class, 'prefix_' ) ) {
        return;
    }
    require_once("classes/$class/class.$class.php");
  }


  /* 1.4 RUN CLASSES INIT
  /------------------------*/
  function prefix_RunClassesInit(){
    // selection
    $runClasses = array();
    // init classes
    foreach ($runClasses as $key => $class) {
      $class_name = "prefix_" . $class;
      $$class = new  $class_name();
    }
  }


  /* 1.5 DEBUGING
  /------------------------*/
  function classesDebug(){
    global $debug_errors;
    $output = '';
    foreach ($debug_errors as $key => $value) {
      // GROUP
      $output .= '<strong>' . $key . '</strong>';
      // single error
      if(is_array($value)):
        foreach ($value as $error) {
          $output .= '<br />' . $error;
        }
      else:
        $output .= '<br />' . $value;
      endif;
      // return errors
      echo $output;
    }
  }


  /*==================================================================================
    2.0 RUN FUNCTIONS
  ==================================================================================*/

  /* 2.1 CONFIGURATION
  /------------------------*/
  if (function_exists('add_action')):
    // set global
    add_action( 'init', 'prefix_JSON_as_Global' );
    // register
    add_action( 'init', function(){
      spl_autoload_register('prefix_registerFunction');
    } );
    // register
    add_action( 'init', 'prefix_RunClassesInit' );
  else:
    // set global
    prefix_JSON_as_Global();
    // register
    spl_autoload_register('prefix_registerFunction');
    // run classes init
    prefix_RunClassesInit();
  endif;


  /* 2.2 DEBUGING
  /------------------------*/
  if ($debug && function_exists('add_action')):
    add_action( 'init', 'classesDebug' );
  elseif($debug):
    classesDebug();
  endif;
