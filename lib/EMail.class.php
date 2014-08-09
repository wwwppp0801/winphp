<?php
class EMail{
    public static function send($mail){
        //Logger::debug(var_export($mail,true));
        if(!defined("SMTP_HOST")||!defined('SMTP_USERNAME')||!defined("SMTP_PASSWORD")){
            return;
        }
        require(ROOT_PATH."/lib/PHPMailer/PHPMailerAutoload.php");
        $mailer = new PHPMailer();
        $mailer->isSMTP();                                      // Set mailer to use SMTP
        $mailer->Host = SMTP_HOST;  // Specify main and backup SMTP servers
        $mailer->SMTPAuth = true;                               // Enable SMTP authentication
        $mailer->CharSet = 'UTF-8';                               // Enable SMTP authentication
        $mailer->Username = SMTP_USERNAME;                 // SMTP username
        $mailer->Password = SMTP_PASSWORD;                           // SMTP password

        $mailer->From = 'test@aimeizhuyi.com';
        $mailer->FromName = '爱美主义';
        
        $mailer->addAddress($mail['to']);               // Name is optional
        //$mailer->addAddress('wwwppp0801@qq.com','wang peng');               // Name is optional

        #$mailer->addReplyTo('info@example.com', 'Information');
        #$mailer->addCC('cc@example.com');
        #$mailer->addBCC('bcc@example.com');

        $mailer->WordWrap = 50;                                 // Set word wrap to 50 characters
        $mailer->isHTML(true);                                  // Set email format to HTML

        $mailer->Subject = $mail['title'];
        $mailer->Body    = $mail['content'];
        $mailer->AltBody = strip_tags($mail['content']);

        if(!$mailer->send()) {
            Logger::error('Message could not be sent.' . $mailer->ErrorInfo);
        } else {
            Logger::debug('Message has been sent');
        }

    }
}
