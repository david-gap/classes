<?php
/**
 * SAVE FILE CONTENT AS A GLOBAL
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.0
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


class prefix_FileEmbed {
// class prefix_FileEmbed extends prefix_core_BaseFunctions {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param private string $main_directory: file directory
      * @param private array $files: files to insert
      * @param private bool $ColumnName: file contains labels
      * @param private string $csvEncodingFile: file coding
      * @param private string $csvEncodingOutput: file encode
      * @param private string $orderColumn: sort global by
      * @param private string $fixIdColumn: list has id
      * @param private string $orderDirection: sort direction
      * @param private string $CSVseperator: csv file is seperated (, or ;)
      * @param private bool $orderByDate: sort column is date
    */
    private $main_directory     = '/';
    private $files              = array();
    // defaults
    private $ColumnName        = true;
    private $csvEncodingFile   = 'UTF-8';
    private $csvEncodingOutput = 'Windows-1252';
    private $orderColumn       = '';
    private $fixIdColumn       = false;  // CSV only
    private $orderDirection    = 'ASC';
    private $CSVseperator      = ',';
    private $orderByDate       = false;
    private $SSLstream         = true;


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
      if(!empty($this->files)):
        // vars
        $root = $_SERVER['DOCUMENT_ROOT'];
        // for each file
        foreach ($this->files as $file_key => $file) {
          // file path
          $path       = substr($file["file"], 0, 4) === 'http' ? $file["file"] : $root . $this->main_directory . $file["file"];
          $path_parts = pathinfo($path);
          // check if file exists
          if(prefix_core_BaseFunctions::CheckFileExistence($path)):
            // create global
            global $$file_key;
            // file configuration
            $title           = array_key_exists('title', $file) ? $file["title"] : $this->ColumnName;
            $file_coding     = array_key_exists('file_coding', $file) ? $file["file_coding"] : $this->csvEncodingFile;
            $encoding        = array_key_exists('encoding', $file) ? $file["encoding"] : $this->csvEncodingFile;
            $id_column       = array_key_exists('id_column', $file) ? $file["id_column"] : $this->fixIdColumn;
            $order_column    = array_key_exists('order_column', $file) ? $file["order_column"] : $this->orderColumn;
            $order_direction = array_key_exists('order_direction', $file) ? $file["order_direction"] : $this->orderDirection;
            $order_bydate    = array_key_exists('order_bydate', $file) ? $file["order_bydate"] : $this->orderByDate;
            $seperator       = array_key_exists('seperator', $file) ? $file["seperator"] : $this->CSVseperator;
            $sslStream       = array_key_exists('ssl_stream', $file) ? $file["ssl_stream"] : $this->SSLstream;
            // ssl stream
            if($sslStream !== true):
              $stream_settings = array(
                                  "ssl"=>array(
                                    "verify_peer"=> false,
                                    "verify_peer_name"=> false
                                  ),
                                  'http' => array(
                                    'timeout' => 30
                                  )
                                );
            else:
              $stream_settings = array();
            endif;
            $stream          = stream_context_create($stream_settings);
            // check file type
            if($path_parts['extension'] == 'json'):
              // get content from a json file
              $file_content = file_get_contents($path, 0, $stream);
              // utf 8 bom fix
              $file_content = ltrim($file_content, chr(239).chr(187).chr(191));
              // decode file
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
                $$file_key = array();
                $debug_errors['FileEmbed'][] = "File content of file " . $file_key . " is broken";
              endif;
            elseif($path_parts['extension'] == 'xml'):
              // get content from a xml file
              $doc = new \DOMDocument();
              if(@$doc->load($path)):
                  // var_dump("$path is a valid XML document");
                  $$file_key = simplexml_load_file($path);
              else:
                // file content is broken
                $$file_key = array();
                $debug_errors['FileEmbed'][] = "File content of file " . $file_key . " is broken";
              endif;
            elseif($path_parts['extension'] == 'csv'):
              // get content from a csv file
              $dataArray = array();
              $row = 0;
              if (($handle = fopen($path, 'r', false, $stream)) !== false) {
                  while (($data = fgetcsv($handle, 1000, $seperator)) !== false) {

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
              $$file_key = array();
              $debug_errors['FileEmbed'][] = "File type of file " . $file_key . " is not supported";
            endif;
            // remove first row if column names are inside
            if($title):
              unset($$file_key[0]);
            endif;
            // sort array
            if($order_column !== ''):
              $$file_key = prefix_core_BaseFunctions::MultidArraySort($$file_key, $order_column, $order_direction, $order_bydate);
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
      $this->main_directory = array_key_exists('directory', $myConfig) ? $myConfig['directory'] : $this->main_directory;
      $this->files = array_key_exists('files', $myConfig) ? $myConfig['files'] : $this->files;
    endif;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
