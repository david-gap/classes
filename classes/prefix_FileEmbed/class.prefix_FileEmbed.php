<?php
/**
 * SAVE FILE CONTENT AS A GLOBAL
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     1.2.3
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
2.0 FUNCTIONS
  2.1 GET SETTINGS FROM CONFIGURATION FILE
3.0 OUTPUT
=======================================================*/


class prefix_FileEmbed extends prefix_core_BaseFunctions {
// class prefix_FileEmbed extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param static string $main_directory: file directory
      * @param static array $csv_Files: files to insert
      * @param static bool $ColumnName: file contains labels
      * @param static string $csvEncodingFile: file coding
      * @param static string $csvEncodingOutput: file encode
      * @param static string $orderColumn: sort global by
      * @param static string $fixIdColumn: list has id
      * @param static string $orderDirection: sort direction
    */
    static $main_directory     = '/';
    static $csv_Files          = array(
      'global_name' => array(
        'file' => 'add_path/file_name.csv',
        'title' => true,
        'file_coding' => 'UTF-8',
        'encoding' => 'Windows-1252',
        'id_column' => false,
        'order_column' => '',
        'order_direction' => 'ASC'
      )
    );
    // defaults
    static $ColumnName        = true;
    static $csvEncodingFile   = 'UTF-8';
    static $csvEncodingOutput = 'Windows-1252';
    static $orderColumn       = '';
    static $fixIdColumn       = false;  // CSV only
    static $orderDirection    = 'ASC';


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // save file content as global
      SELF::Add_Files_as_Global();
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


    /* 1.4 JSON GLOBAL
    /------------------------*/
    function Add_Files_as_Global(){
      if(!empty(SELF::$csv_Files)):
        // vars
        $root = $_SERVER['DOCUMENT_ROOT'];
        // for each file
        foreach (SELF::$csv_Files as $file_key => $file) {
          // file path
          $path       = substr($file["file"], 0, 4) === 'http' ? $file["file"] : $root . SELF::$main_directory . $file["file"];
          $path_parts = pathinfo($path);
          // check if file exists
          if(PARENT::CheckFileExistence($path)):
            // create global
            global $$file_key;
            // file configuration
            $title           = array_key_exists('title', $file) ? $file["title"] : SELF::$ColumnName;
            $file_coding     = array_key_exists('file_coding', $file) ? $file["file_coding"] : SELF::$csvEncodingFile;
            $encoding        = array_key_exists('encoding', $file) ? $file["encoding"] : SELF::$csvEncodingFile;
            $id_column       = array_key_exists('id_column', $file) ? $file["id_column"] : SELF::$fixIdColumn;
            $order_column    = array_key_exists('order_column', $file) ? $file["order_column"] : SELF::$orderColumn;
            $order_direction = array_key_exists('order_direction', $file) ? $file["order_direction"] : SELF::$orderDirection;
            // check file type
            if($path_parts['extension'] == 'json'):
              // get content from a json file
              $file_content = file_get_contents($path);
              $json_decode = json_decode($file_content, true);
              // check if file content is broken
              if(is_array($json_decode)):
                if($id_column !== "" && $id_column !== false):
                  $dataArray = array();
                  $row = 0;
                  foreach ($json_decode as $row_key => $row_value) {
                    ++$row;
                    $SingleDataArray = array();
                    foreach ($row_value as $key => $value) {
                      $SingleDataArray[$key] = $value;
                    }
                    if($id_column >= 0 && $id_column !== false):
                      // fallback for first row if column names are inside
                      if($title && $row == 1):
                        $id = 0;
                      else:
                        $id = $SingleDataArray[$id_column];
                      endif;
                      $dataArray[$id] = $SingleDataArray;
                    else:
                      $dataArray[] = $SingleDataArray;
                    endif;
                  }
                  $$file_key = $dataArray;
                else:
                  $$file_key = $json_decode;
                endif;
              else:
                // file content is broken
                $debug_errors['FileEmbed'][] = "File content of file " . $file_key . " is broken";
              endif;
            elseif($path_parts['extension'] == 'csv'):
              // get content from a csv file
              $dataArray = array();
              $row = 0;
              if (($handle = fopen($path, 'r')) !== false) {
                  while (($data = fgetcsv($handle, 1000, ';')) !== false) {

                      ++$row;
                      if (!empty($data) && count($data) > 1) {
                        $SingleDataArray = array();
                        foreach ($data as $key => $value) {
                          $SingleDataArray[] = mb_convert_encoding($value, $file_coding, $encoding);
                        }
                        if($id_column >= 0 && $id_column !== false):
                          // fallback for first row if column names are inside
                          if($title && $row == 1):
                            $id = 0;
                          else:
                            $id = $SingleDataArray[0];
                          endif;
                          $dataArray[$id] = $SingleDataArray;
                        else:
                          $dataArray[] = $SingleDataArray;
                        endif;
                      }
                  }
              }
              $$file_key = $dataArray;
            else:
              // if file type is not supported
              $debug_errors['FileEmbed'][] = "File type of file " . $file_key . " is not supported";
            endif;
            // remove first row if column names are inside
            if($title):
              unset($$file_key[0]);
            endif;
            // sort array
            if($order_column !== ''):
              $$file_key = PARENT::MultidArraySort($$file_key, $order_column, $order_direction);
            endif;

          else:
            // if file is missing or empty
            $debug_errors['FileEmbed'][] = "File " . $file_key . " is missing or invalid";
          endif;
        }
      endif;
    }



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/


  /* 2.1 GET SETTINGS FROM CONFIGURATION FILE
  /------------------------*/
  private function updateVars(){
    // get configuration
    global $configuration;
    // if configuration file exists && class-settings
    if($configuration && array_key_exists('FileEmbed', $configuration)):
      // class configuration
      $myConfig = $configuration['FileEmbed'];
      // update vars
      SELF::$main_directory = array_key_exists('directory', $myConfig) ? $myConfig['directory'] : SELF::$DEMO;
      SELF::$csv_Files = array_key_exists('files', $myConfig) ? $myConfig['files'] : SELF::$DEMO;
    endif;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
