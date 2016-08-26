<?php
class gfax
{
    var $userid;
    var $pass;
    var $deskey;
    var $type;

    function gfax( $userid, $pass ,$type="md5", $deskey="")
    {
        $this->userid = $userid;
        $this->pass   = $pass;
        $this->type   = $type;
        $this->deskey = $deskey;
    }

    function getAuth()
    {
        if($this->type == "basic")
            return "basic " . base64_encode($this->userid.":".$this->pass);
        else if($this->type == "md5"){
            return "md5 " . base64_encode($this->userid.":".md5($this->pass));
    }

    function getHttp($url)
    {
        $result = curl_file_get_contents($url, null, array("Authorization: ".$this->getAuth()), 1000);
        return $result;
    }

    function sendFax($url, $postdata, $files)
    {
        $data = "";
        $boundary =  md5(time());

        foreach($postdata as $key => $val)
        {
            $data .= "--$boundary\r\n";
            $data .= "Content-Disposition: form-data; name=\"".$key."\"\r\n\r\n".$val."\r\n";
        }
        $data .= "--$boundary\r\n";

        foreach($files as $key => $file)
        {
            $fileContents = file_get_contents($file);
            $filename = basename ($file);
            $data .= "Content-Disposition: form-data; name=\"$key\"; filename=\"$filename\"\r\n";
            $data .= "Content-Type: application/octet-stream\r\n";
            $data .= "Content-Transfer-Encoding: binary\r\n\r\n";
            $data .= $fileContents."\r\n";
            $data .= "--$boundary--\r\n";
        }

        $opts = array('http' => array(
            'method' => 'POST',
            'header'=>"Authorization: ".$this->getAuth()."\r\n".
            'Content-Type: multipart/form-data; boundary='.$boundary,
            'content' => $data
        ));

        $ctx = stream_context_create($opts);
        $fp = @fopen($url, 'rb', false, $ctx);

        if (!$fp) return "";

        $response = @stream_get_contents($fp);
        if ($response === false)
        {
            return "";
        }
        return $response;
    }
}
?>
