<?php
/**
 * Email
 +-----------------------------------------
 * @author page7 <zhounan0120@gmail.com>
 * @category
 * @version $Id$
 */
class email
{

    private $Mail;

    // From
    protected $from = '';


    // Setting Account
    public function __construct($from='', $host='', $port=25, $username='', $password='')
    {
        // format email address
        $this -> from = self::check_mail($from);
        $form = explode('@', $this -> from['email']);

        //include PHPMailer
        require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
        $this -> Mail = new PHPMailer();
        $this -> Mail -> IsSMTP();
        $this -> Mail -> Host = $host;
        $this -> Mail -> Port = $port;
        $this -> Mail -> SMTPAuth = true;
        $this -> Mail -> Username = $username;
        $this -> Mail -> Password = $password;
    }


    // Send Email
    public function send($to, $subject="", $body="", $altbody="", $cc="", $bcc="")
    {
        $this -> Mail -> FromName = $this -> from['name'];
        $this -> Mail -> From = $this -> from['email'];

        $this -> Mail -> ClearAllRecipients();

        // Add Addressee
        if(!is_array($to)) $to = explode(',', $to);
        foreach ($to as $val)
            if($touser = self::check_mail($val))
                $this -> Mail -> AddAddress($touser['email'], $touser['name']);

        // Add CC
        if(!is_array($cc)) $cc = explode(',', $cc);
        if($cc)
            foreach ($cc as $val)
                if($touser = self::check_mail($val))
                    $this -> Mail -> AddCC($touser['email'], $touser['name']);

        // Add BCC
        if(!is_array($bcc)) $bcc = explode(',', $bcc);
        if($bcc)
            foreach ($bcc as $val)
                if($touser = self::check_mail($val))
                    $this -> Mail -> AddBCC($touser['email'], $touser['name']);

        $this -> Mail -> Subject = $subject;
        $this -> Mail -> Body = $body;
        $this -> Mail -> AltBody = $altbody;
        return $this -> Mail -> Send();
    }


    // Add Attachment
    public function attachment($path, $name, $encoding='base64', $type='application/octet-stream')
    {
        $this -> Mail -> AddAttachment($path, $name, $encoding, $type);
    }


    // Is HTML
    public function is_html()
    {
        $this -> Mail -> IsHTML();
    }


    // SET Char
    public function set_char($char)
    {
        $this -> Mail -> $CharSet = $char;
    }


    // Check email address
    static function check_mail($address)
    {
        if(strpos($address, '>') === false)
            return array('email' => $address, 'name' => '');

        $address = explode('<', substr($address, 0, -1));

        if(count($address) == 2)
            return array('email' => $address[1], 'name' => $address[0]);
        return false;
    }


    // Error
    public function get_error()
    {
        return $this -> Mail -> ErrorInfo;
    }

}
?>