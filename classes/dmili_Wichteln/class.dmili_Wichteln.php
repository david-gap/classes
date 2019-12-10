<?
/**
 *
 *
 * Chrismas Wichteln
 * In combination with dmili_Mail.php
 * Author: David Voglgsnag
 */

 class dmili_Wichteln {

   /* FUNCTIONS
   /===================================================== */

   /* GIVE EVERY MEMBER A WICHTEL TARGET
   /------------------------*/
   /**
   * @param array $members = all wichtel members
   * @return array members with wichtel target id
   */
   private function GetTarget(array $members = array()){

     $count_members = count($members);
     $key = true;

     while($key){
       $targeting = array();
       $clearner = $members;

       foreach ($members as $key => $member) {
         // select a existing ID
         $single_key = true;
         while($single_key){
           $random = array_rand($clearner);
           if($random !== $key && !in_array($random, $member[2])):
             $single_key = false;
             unset($clearner[$random]);
           endif;
         }
         // add target ID to members array
         $member[] = $random;
         $targeting[] = $member;
       }

       // check if all members got a wichtel
       $final_count = count($targeting);
       if($final_count == $count_members):
         $key = false;
       endif;
     }

     return $targeting;

   }

   /* GIVE EVERY MEMBER A WICHTEL TARGET
   /------------------------*/
   /**
   * @param array $members = all wichtel members
   * @return array members with wichtel target id
   */
   private function SendMail(string $admin = '', string $wichtel = '', string $content ){
     if($admin !== '' && $wichtel !== '' && $content !== ''):
       $mail = new dmili_Mail();
       $mail->ContentType( 'text/html' );
       $mail->From( 'Das Institut für Wichtlerei <' . $admin . '>' );
       $mail->To( $wichtel );
       $mail->Subject( 'Wichteln 2019' );
       $mail->Body( $content );
       // echo $mail->Get();
       $mail->Send();
     endif;
   }


   /* START WICHTEL FUNCTION
   /------------------------*/
   /**
   * @param array $members = all wichtel members
   * @param string $admin = admin mail address
   * @return mail to admin and members
   */
   function Wichteln(array $members = array(), string $admin = ''){

     $wichtelAndTargets = SELF::GetTarget($members);

     // mail for admin
     if($admin !== ''):
       // mail content
       $admin_content = '';
       foreach ($wichtelAndTargets as $key => $wichtel) {
         $target_array = $wichtelAndTargets[$wichtel[3]];
         $admin_content .= $wichtel[0] . ' beschenkt ' . $target_array[0] . '<br>';
       }
       // mail configuration
       $admin_mail = new dmili_Mail();
       $admin_mail->ContentType( 'text/html' );
       $admin_mail->From( 'Das Institut für Wichtlerei <' . $admin . '>' );
       $admin_mail->To( $admin );
       $admin_mail->Subject( 'Wichteln 2019' );
       $admin_mail->Body( $admin_content );
       $admin_mail->Send();
     endif;

     // mail to members
     foreach ($wichtelAndTargets as $key => $wichtel) {

       $content = '';
       $target_array = $wichtelAndTargets[$wichtel[3]];

       $content .= '<html>';
         $content .= '<head>';
           // mail styling
           $content .= '<style>';
             $content .= 'html, body {background-color: #9b0002; color: #FFFFFF; font-size: 15px; text-align: center; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;}';
             $content .= '.content {background-color: #9b0002; color: #FFFFFF; padding: 20px;}';
             $content .= '.target {background-color: #2c8500; color: #FFFFFF; width: 100%; font-size: 18px; width: fit-content; margin: auto; padding: 10px 20px; text-align: center;}';
           $content .= '</style>';
         $content .= '</head>';

         // mail content
         $content .= '<body>';
           $content .= '<table><tr><td align="center" class="content">';
             $content .= '<p>Liebe/r ' . $wichtel[0] . ',</p>';
             $content .= '<p>es ist wieder soweit. Weihnachten steht vor der Tür und Wichteln ist angesagt.';
             $content .= ' Im Zufallsprinzip hat jeder Teilnehmer einen Namen zum Bewichteln bekommen. (Partner ausgeschlossen)</p>';
             $content .= '<table class="target"><tr><td align="center">';
               $content .= 'Du hast <b>' . $target_array[0] . '</b> bekommen.';
             $content .= '</td></tr></table>';
             $content .= '<p>Bitte behalte den Namen für dich.</p>';
             $content .= '<p>Die Namensgebung soll vorerst geheim bleiben und jeder hat einen kleinen Hinweis mit einzupacken, so kann der Beschenkte sein Wichtel erraten.</p>';
             $content .= '<p>Eine Kostengrenze gibt es nicht. Jeder soll schenken was er für angebracht hält.</p>';
             $content .= '<p>So, nun ran an das geschehen. Viel vergnügen und eine schöne Vorweihnachtszeit.</p>';
             $content .= '<p>Liebe Grüsse<br>';
             $content .= 'Das Institut für Wichtlerei</p>';
           $content .= '</td></tr></table>';
         $content .= '</body>';
       $content .= '</html>';

       // send mail to member
       SELF::SendMail($admin, $wichtel[1], $content);
     }

   }



 }
