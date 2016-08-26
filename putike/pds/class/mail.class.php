<?php
/**
 +------------------------------------------------------------------------------
 * Email 发送类，基于phpmailer
 +------------------------------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Net
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class mail
{

    protected $formMail = '';

    protected $Mail = '';

    public function __construct($formMail='', $host='', $port=0, $username='', $password='')
    {
        // Set Servers
        $this -> formMail = self::checkMail($formMail);
        $form = explode('@', $this -> formMail['email']);

        // Init PHPMailer
        require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
        $this -> Mail = new PHPMailer();
        $this -> Mail -> IsSMTP();
        if($host) $this -> Mail -> Host = $host;
        if($port) $this -> Mail -> Port = $port;
        $this -> Mail -> SMTPAuth = true;
        $this -> Mail -> SMTPSecure   = "ssl";
        $this -> Mail -> Username = $username ? $username : $form[0];
        $this -> Mail -> Password = $password ? $password : '';
    }

    // Send Email
    public function send($to, $subject="", $body="", $altbody="", $cc="", $bcc="")
    {
        // Add form-mail
        $this -> Mail -> FromName = $this -> formMail['name'];
        $this -> Mail -> From = $this -> formMail['email'];

        $this -> Mail -> ClearAllRecipients();

        // Add to-mail
        if(!is_array($to)) $to = explode(',', $to);
        foreach ($to as $val)
            if($touser = self::checkMail($val))
                $this -> Mail -> AddAddress($touser['email'], $touser['name']);

        // Add cc
        if(!is_array($cc)) $cc = explode(',', $cc);
        if($cc)
            foreach ($cc as $val)
                if($touser = self::checkMail($val))
                    $this -> Mail -> AddCC($touser['email'], $touser['name']);

        // Add bcc
        if(!is_array($bcc)) $bcc = explode(',', $bcc);
        if($bcc)
            foreach ($bcc as $val)
                if($touser = self::checkMail($val))
                    $this -> Mail -> AddBCC($touser['email'], $touser['name']);

        $this -> Mail -> Subject = $subject;
        $this -> Mail -> Body = $body;
        $this -> Mail -> AltBody = $altbody;
        return $this -> Mail -> Send();
    }

    // add attachment
    public function addAttachment($path, $name, $encoding = 'base64', $type = 'application/octet-stream')
    {
        $this -> Mail -> AddAttachment($path, $name, $encoding, $type);
    }

    // Set mail body is html
    public function isHTML()
    {
        $this -> Mail -> IsHTML();
    }


    // Set char
    public function setChar($char)
    {
        $this -> Mail -> $CharSet = $char;
    }


    // Check email and nickname
    static function checkMail($Address)
    {
        if(strpos($Address, '>') === false) //检测是否有称呼
            return array('email' => $Address, 'name' => '');
        $Address = explode('<', substr($Address, 0, -1));
        if(count($Address) == 2)                             //邮件和用户名必须合法
            return array('email' => $Address[1], 'name' => $Address[0]);
        return false;
    }


    // Error
    public function getError()
    {
        return $this -> Mail -> ErrorInfo;
    }


}
