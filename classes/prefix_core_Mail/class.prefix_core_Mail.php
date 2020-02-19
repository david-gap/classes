<?
/**
 *
 *
 * PHP Mailer
 * https://github.com/david-gap/classes
 * Author: David Voglgsang
 * @version     1.0.2
 */

 class prefix_core_Mail {

  /* DEFAULT SETTINGS
  /===================================================== */
  /*
    default vars
  */
  var $sendto = array();
  var $acc = array();
  var $abcc = array();
  var $aattach = array();
  var $xheaders = array();
  var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
  var $charset = "UTF-8";
  var $contenttype = "text/plain";
  var $receipt = 0;
  var $ctencoding = "8bit";



  /* INIT FUNCTIONS
  /===================================================== */
  /*
    Mail constructor
  */
  function __construct(){
    $this->autoCheck( true );
    $this->boundary = "--" . md5( uniqid("myboundary") );
  }

  /*
    activate or desactivate the email addresses validator
    ex: autoCheck( true ) turn the validator on
    by default autoCheck feature is on
    @param boolean	$bool set to true to turn on the auto validation
  */
  public function autoCheck( $bool ){
    if( $bool ):
      $this->checkAddress = true;
    else:
      $this->checkAddress = false;
    endif;
  }

  /*
    check an email address validity
    @param string $address: email address to check
    @return true if email adress is ok
    @used in: CheckAdresses()¥
  */
  public function ValidEmail($address){
    if(strpos($address, '<') !== false):
      $parts = explode(' ', trim($address));
      $address = trim(array_pop($parts), "<> \t\n\r\0\x0B");
    endif;
    if( ! filter_var($address, FILTER_VALIDATE_EMAIL) ):
      return false;
    else:
      return true;
    endif;
  }

  /*
    check validity of email addresses
    @param	array $mails -
    @return if unvalid, output an error message and exit
    @used in: To(), Cc(), Bcc()
  */
  function CheckAdresses( $mails ){
    for($i=0;$i< count( $mails); $i++ ) {
      if( ! $this->ValidEmail( $mails[$i]) && $mails[$i] !== "" ) {
        echo "PHPclasses_PHPMailform, invalid address $mails[$i]";
        exit;
      }
    }
  }



  /* FUNCTIONS TO USE AND ADD MAIL HEADERS
  /===================================================== */
  /*
    set the mail recipient
    @param string $to email address, accept both a single address, comma seperated addresses or an array of addresses
  */
  function To( $to ){
    if( is_array($to) ):
      $this->sendto = $to;
    elseif(strpos($to, ',') !== false):
      $nospaces = str_replace(' ', '', $to);
      $array = explode(',',$nospaces);
      $this->sendto = $array;
    else:
      $this->sendto[] = $to;
    endif;

    if( $this->checkAddress == true ):
      $this->CheckAdresses( $this->sendto );
      // fallback in CheckAdresses() available
    endif;
  }

  /*
    set the CC headers
    $cc : email address(es), accept both array and string
    @param string $cc email address, accept both a single address, comma seperated addresses or an array of addresses
  */
  function Cc( $cc ){
    if( is_array($cc) ):
      $this->acc= $cc;
    elseif(strpos($cc, ',') !== false):
      $nospaces = str_replace(' ', '', $cc);
      $array = explode(',',$nospaces);
      $this->acc = $array;
    else:
      $this->acc[]= $cc;
    endif;

    if( $this->checkAddress == true ):
      $this->CheckAdresses( $this->acc );
      // fallback in CheckAdresses() available
    endif;
  }

  /*
    set the Bcc headers ( blank carbon copy ).
    $bcc : email address(es), accept both array and string
    @param string $bcc email address, accept both a single address, comma seperated addresses or an array of addresses
  */
  function Bcc( $bcc ){
    if( is_array($bcc) ):
      $this->abcc = $bcc;
    elseif(strpos($bcc, ',') !== false):
      $nospaces = str_replace(' ', '', $bcc);
      $array = explode(',',$nospaces);
      $this->abcc = $array;
    else:
      $this->abcc[]= $bcc;
    endif;

    if( $this->checkAddress == true ):
      $this->CheckAdresses( $this->abcc );
      // fallback in CheckAdresses() available
    endif;
  }

  /*
    set the Content-type header.
    @param string $contenttype
  */
  function ContentType( $ct ){
    if( trim( $ct != "" )  ):
      $this->contenttype = $ct;
    endif;
  }

  /*
    add a receipt to the mail ie. a confirmation is returned to the "From" address (or "ReplyTo" if defined)
    when the receiver opens the message.
    @warning this functionality is *not* a standard, thus only some mail clients are compliants.
  */
  function Receipt(){
    $this->receipt = 1;
  }

  /*
    set the body (message) of the mail
    define the charset if the message contains extended characters (accents)
    default to iso-8859-1
    $mail->Body( "E-Mail mit Umlauten äöü", "iso-8859-1" );
  */
  function Body( $body, $charset="" ){
    $this->body = $body;
    if( $charset != "" ):
      $this->charset = strtolower($charset);
      if( $this->charset != "iso-8859-1" ):
        $this->ctencoding = "8bit";
      endif;
    endif;
  }

  /*
    Define the subject line of the email
    @param string $subject any monoline string
  */
  function Subject( $subject ){
    $this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
  }

  /*
    set the sender of the mail
    @param string $from should be an email address
  */
  function From( $from ){
    if( ! is_string($from) ) {
      echo "PHPclasses_PHPMailform, Sender is not a string";
      exit;
    }
    $this->xheaders['From'] = $from;
  }

  /*
    set the Reply-to header
    @param string $email should be an email address
  */
  function ReplyTo( $address ){
    if( ! is_string($address) ):
      return false;
      echo "PHPclasses_PHPMailform, Reply-to is not a string";
    else:
      $this->xheaders["Reply-To"] = $address;
    endif;
  }

  /*
    set the Organization header
  */
  function Organization( $org ){
    if( trim( $org != "" )  ):
      $this->xheaders['Organization'] = $org;
    endif;
  }

  /*
    set the mail priority
    $priority : integer taken between 1 (highest) and 5 ( lowest )
    ex: $mail->Priority(1) ; => Highest
  */
  function Priority( $priority ){
    if( ! intval( $priority ) ):
      return false;
    endif;
    if( ! isset( $this->priorities[$priority-1]) ):
      return false;
    endif;

    $this->xheaders["X-Priority"] = $this->priorities[$priority-1];

    return true;
  }

  /*
    Attach a file to the mail
    @param string $filename : path of the file to attach
    @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
    @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
  */
  function Attach( $filename, $filetype = "", $disposition = "inline" ){
    if( $filetype == "" ):
      $filetype = "application/x-unknown-content-type";
    endif;
    $this->aattach[] = $filename;
    $this->actype[] = $filetype;
    $this->adispo[] = $disposition;
  }

  /*
    check and encode attach file(s) . internal use only
  */
  private function _build_attachement(){

    $this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";
    $this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
    $this->fullBody .= "Content-Type: $this->contenttype; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";

    $sep= chr(13) . chr(10);
    $ata= array();
    $k=0;

    // for each attached file, do...
    for( $i=0; $i < count( $this->aattach); $i++ ) {

        $filename = $this->aattach[$i];
        $basename = basename($filename);
        $ctype = $this->actype[$i];    // content-type
        $disposition = $this->adispo[$i];

        if( ! file_exists( $filename) ) {
            echo "PHPclasses_PHPMailform, method attach : file $filename can't be found"; exit;
        }
        $subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
        $ata[$k++] = $subhdr;
        // non encoded line length
        $linesz= filesize( $filename)+1;
        $fp= fopen( $filename, 'r' );
        $ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
        fclose($fp);
    }
    $this->fullBody .= implode($sep, $ata);
  }



  /* OUTPUT AND SEND FUNCTIONS
  /===================================================== */
  /*
    Build the email message
  */
  protected function BuildMail(){

    // build the headers
    $this->headers = "";
    // $this->xheaders['To'] = implode( ", ", $this->sendto );

    if( count($this->acc) > 0 ):
      $this->xheaders['CC'] = implode( ", ", $this->acc );
    endif;
    if( count($this->abcc) > 0 ):
      $this->xheaders['BCC'] = implode( ", ", $this->abcc );
    endif;

    if( $this->receipt ):
      if( isset($this->xheaders["Reply-To"] ) ):
        $this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
      else:
        $this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
      endif;
    endif;

    if( $this->charset != "" ):
      $this->xheaders["Mime-Version"] = "1.0";
      $this->xheaders["Content-Type"] = "$this->contenttype; charset=$this->charset; boundary=$this->boundary";
      $this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
    endif;

    $this->xheaders["X-Mailer"] = "7";

    // include attached files
    if( count( $this->aattach ) > 0 ):
      $this->_build_attachement();
    else:
      $this->fullBody = $this->body;
    endif;

    reset($this->xheaders);
    while( list( $hdr,$value ) = each( $this->xheaders )  ):
      if( $hdr != "Subject" ):
        $this->headers .= "$hdr: $value\n";
      endif;
    endwhile;
  }

  /*
    return true
    fornat and send the mail
  */
  public function Send(){
    $this->BuildMail();
    $this->strTo = implode( ", ", $this->sendto );
    // send mail
    $res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );

    return true;
  }

  /*
    return the whole mail , headers + message
    can be used for displaying the message in plain text or logging it
  */
  function Get(){
    $this->BuildMail();
    $this->strTo = implode( ", ", $this->sendto );
    $mail = "To: " . $this->strTo . "\n";
    $mail .= $this->headers . "\n";
    $mail .= $this->fullBody;
    return $mail;
  }

}

?>
