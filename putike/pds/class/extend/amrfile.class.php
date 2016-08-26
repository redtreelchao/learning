<?php
/**
 * AMR 文件信息读取
 +-----------------------------------------
 * @author page7 <zhounan0120@gmail.com>
 * @category
 * @version $Id$
 */
class amrfile
{
    protected $file_size = 0;

    protected $code = 0;


    // 实例化
    public function __construct($filename)
    {
        $this -> file_size = filesize($filename);

        $fh = fopen($filename, "rb");

        // 标记头为 6 字节
        $head = fread($fh, 6);
        // 编码方式
        $this -> code = ord(fread($fh, 1));

        $this -> frame_size = $this -> get_frame($this -> code);

        fclose($fh);
    }


    // 取帧长度
    protected function get_frame($code)
    {
        $code_frame = array(
            4   => array(13, '4.75kbps'),
            12  => array(14, '5.15kbps'),
            20  => array(16, '5.9kbps'),
            28  => array(18, '6.7kbps'),
            36  => array(20, '7.4kbps'),
            44  => array(21, '7.95kbps'),
            52  => array(27, '10.2kbps'),
            60  => array(32, '12.2kbps'),
        );

        return $code_frame[$code];
    }


    // 获取meta
    public function get_metadata()
    {
        $metadata = array();

        // 比特率
        $metadata['Bitrate'] = $this -> frame_size[1];

        // 音频长度
        $metadata['Length'] = round(($this -> file_size / $this -> frame_size[0] * 20) / 1000);

        return $metadata;
    }

}

?>