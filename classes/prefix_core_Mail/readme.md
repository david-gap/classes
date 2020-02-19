**Version 1.0.2** (19.02.2020)

Custom class "Mail" creates a mail content and send it.

## USAGE
* Build a new Mail
```php
$mail = new prefix_core_Mail();
```
* Mail configuration
```php
$mail->From( 'Jon Doe <sender@mail.com>' );   // set the sender of the mail
$mail->To( 'recipient@mail.com' );            // set the mail recipient. allows array for multiple mails
$mail->Subject( 'Mail subject' );             // define mail subject
$mail->Body( 'Mail content', 'charset' );     // define mail content. UTF-8 as charset default
// additional headers
$mail->autoCheck(false);                      // email addresses validator. true as default
$mail->Cc( 'cc@mail.com' );                   // set the CC headers. allows array for multiple mails
$mail->Bcc( 'bcc@mail.com' );                 // set the BCC headers. allows array for multiple mails
$mail->Organization( 'Firm name' );           // set the sender organization
$mail->ContentType( 'text/html' );            // set the mail content type. text/plain as default
$mail->Priority(1);                           // set the mail priority
$mail->Receipt();                             // add this line to add a receipt to the mail. Off as default
$mail->ReplyTo( 'replyto@mail.com' );         // set the Reply-to header. sender mail as default
$mail->Attach( 'file_path',                   // attached file
               'file_type',                   // file type
               'inline or attachment' ) ;     // define file possition if possible
```
* Send Mail
```php
$mail->Send();                                // send the mail
```
* Return Mail
```php
echo '<pre>' . $mail->Get() . '</pre>';       // return the whole mail for validation
```
