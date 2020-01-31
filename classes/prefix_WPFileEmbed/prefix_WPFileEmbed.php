<?
/**
 *
 *
 *
 * Set file (csv/json) content as global
 * https://github.com/david-gap/classes
 * Author:      David Voglgsang
 * @version     1.0
 */

class prefix_WPFileEmbed {

  /* CONFIGURATION
  /===================================================== */
  /**
    * default vars
    * @param string $csv_directory: file directory
    * @param string $csv_File: file name
    * @param string $filetype: file type
    * @param bool $ColumnName: column name exists in file
    * @param string $csvEncodingFile: file encoding
    * @param string $csvEncodingOutput: output encoding
    * @param string $orderColumn: sort content by column number
    * @param string $orderDirection: sort direction
  */
  var $csv_directory     = '/folder/';
  var $csv_File          = 'file_name.csv';
  var $filetype          = 'csv';
  var $ColumnName        = true;
  var $csvEncodingFile   = 'UTF-8';
  var $csvEncodingOutput = 'Windows-1252';
  var $orderColumn       = '1';
  var $orderDirection    = 'ASC';

  /* INIT
  /===================================================== */
  public function __construct() {
    // save file content as global
    add_action( 'init', array( $this, 'Add_File_as_Global' ) );
    // shortcodes
    add_shortcode( 'fileoutput', array( $this, 'MemberList' ) );
  }


  /* SORT ARRAY
  /------------------------*/
  /**
    * @param array $array: array to sort
    * @param string $on: select column to sort by
    * @param string $order: order direction
    * @return array sorted array
  */
  function array_sort(array $array = array(), string $on = "0", string $order = "ASC"){
    $new_array = array();
    $sortable_array = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }
        switch ($order) {
            case "ASC":
                asort($sortable_array);
            break;
            case "DESC":
                arsort($sortable_array);
            break;
        }
        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
  }


  /* JSON GLOBAL
  /------------------------*/
  function Add_File_as_Global(){
    // vars
    $root = $_SERVER['DOCUMENT_ROOT'];
    $file = $root . $this->csv_directory . $this->csv_File;

    // set global
    global $memberlist;

    // check if file exists
    if(file_exists($file) && filesize($file) > 0):
      // check file type
      if($this->filetype == 'csv'):
        // get file from a csv file
        $dataArray = array();
        $row = 1;
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                ++$row;
                if (!empty($data) && count($data) > 1) {
                  $SingleDataArray = array();
                  foreach ($data as $key => $value) {
                    $SingleDataArray[] = mb_convert_encoding($value, $this->csvEncodingFile, $this->csvEncodingOutput);
                  }
                    $dataArray[] = $SingleDataArray;
                }
            }
        }
        $memberlist = $dataArray;
      elseif ($this->filetype == 'json'):
        // get file from a json file
        $file_content = file_get_contents($file);
        $json_decode = json_decode($file_content, true);
        $memberlist = $json_decode;
      endif;
      // remove first row if column names are inside
      if($this->ColumnName):
        unset($memberlist[0]);
      endif;
      // sort array
      if($this->orderColumn !== ''):
        $memberlist = SELF::array_sort($memberlist, $this->orderColumn, $this->orderDirection);
      endif;
    else:
      // if file is missing or empty
      $memberlist = __('File is missing or invalid','Members');
    endif;

  }



  /* OUTPUT
  /===================================================== */
  /* SINGLE MEMBER
  /------------------------*/
  /**
    * @param array $content: Member informations
    * @return string with member informations
  */
  public function SingleMember(array $content = array()){
    $output = '';

    $output .= '<li>';
    $output .= $content[0] . '<br>';
    $output .= $content[1] . '<br>';
    $output .= '</li>';

    return $output;
  }


  /* MEMBER LIST
  /------------------------*/
  /**
  * @return string with members informations as list
  */
  public function MemberList(){
    $output = '';

    global $memberlist;

    if(is_array($memberlist)):
      // if content exist
      $output .= '<ul>';
        foreach($memberlist as $member) {
          $output .= SELF::SingleMember($member);
        }
      $output .= '</ul>';
    else:
      // fallback if file content is missing
      $output .= $memberlist;
    endif;


    return $output;
  }


  /* MEMBER FILTER OPTIONS
  /------------------------*/
  /**
  * @return string with filter formular
  */
  function MemberFilter(){
    $output = '';

    return $output;
  }

}
