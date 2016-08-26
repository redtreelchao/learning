<?php
/**
 * 图像文件处理
 +-----------------------------------------
 * @author page7 <zhounan0120@gmail.com>
 * @category
 * @version $Id$
 */
class image
{

    // 转换图片
    static function convert($source, $target, $sr_x=0, $sr_y=0, $tr_x=0, $tr_y=0, $sr_w=null, $sr_h=null, $tr_w=null, $tr_h=null)
    {
        $data = getimagesize($source);
        switch ($data['2'])
        {
            case 1:
                $im = imagecreatefromgif($source);
                break;
            case 2:
                $im = imagecreatefromjpeg($source);
                break;
            case 3:
                $im = imagecreatefrompng($source);
                break;
            default:
                return false;
        }

        if (!$sr_w) $sr_w = $data[0];
        if (!$sr_h) $sr_h = $data[1];
        if (!$tr_w) $tr_w = $data[0];
        if (!$tr_h) $tr_h = $data[1];

        $ni = imagecreatetruecolor($tr_w, $tr_h);

        $target_info = pathinfo($target);
        if($target_info['extension'] == 'jpg') $target_info['extension'] = 'jpeg';

        // image type convert:
        //   gif -> jpg,png
        //   png -> jpg,gif (use a white background);
        //   png -> png (keep background transparent);
        if($target_info['extension'] == 'png' && $data['2'] == 3)
        {
            $bkcolor = imagecolorallocatealpha($ni, 0, 0, 0, 127);
            imagealphablending($ni, false);
            imagefill($ni, 0, 0, $bkcolor);

            imagecopyresampled($ni, $im, $tr_x, $tr_y, $sr_x, $sr_y, $tr_w, $tr_h, $sr_w, $sr_h);
            imagesavealpha($ni, true);
        }
        else
        {
            $bkcolor = imagecolorallocate($ni, 255, 255, 255);
            imagefill($ni, 0, 0, $bkcolor);
            imagecopyresampled($ni, $im, $tr_x, $tr_y, $sr_x, $sr_y, $tr_w, $tr_h, $sr_w, $sr_h);
        }

        $img_fun = 'image'.$target_info['extension'];

        @$img_fun($ni, $target);
        @imagedestroy($im);
        @imagedestroy($ni);
        return true;
    }
    // convert




    // 缩略图
    static function thumb($source, $target, $type='percentage', $value=0)
    {
        $data = getimagesize($source);
        switch($type)
        {
            case 'percentage':
                $sr_w = $data[0];
                $sr_h = $data[1];
                $tr_w = round($data[0] * $value / 100);
                $tr_h = round($data[1] * $value / 100);
                break;

            case 'max-width':
                $sr_w = $data[0];
                $sr_h = $data[1];
                if($sr_w > $value)
                {
                    $tr_w = $value;
                    $tr_h = round($value / $data[0] * $data[1]);
                }
                else
                {
                    $tr_w = $data[0];
                    $tr_h = $data[1];
                }
                break;
        }

        self::convert($source, $target, 0, 0, 0, 0, $sr_w, $sr_h, $tr_w, $tr_h);
    }
    // thumb




    // 字符图标生成
    static function ico_maker($file=false, $codex='', $color='', $size=32, $fontface='/fontawesome-webfont.ttf')
    {
        $width = $size;
        $height = $size;
        $im = imagecreatetruecolor($width, $height);

        // 透明背景
        $bkcolor = imagecolorallocatealpha($im, 0, 0, 0, 127);

        // 文字
        if(!is_file($fontface))
            return false;

        if(is_string($color))
        {
            $r = hexdec($color[1].$color[2]);
            $g = hexdec($color[3].$color[4]);
            $b = hexdec($color[5].$color[6]);
            $fontcolor = imagecolorallocate($im, $r, $g, $b);
        }
        else
        {
            $fontcolor = imagecolorallocate($im, $color[1], $color[2], $color[3]);
        }

        imagealphablending($im, false);
        imagefill($im, 0, 0, $bkcolor);
        $pos = imagettfbbox(round($size/96*72), 0, $fontface, $codex);

        //计算文字的区域
        $rect = imagettfbbox($fontSize, 0, $fontFile, $codex);
        $minX = min(array($pos[0],$pos[2],$pos[4],$pos[6]));
        $maxX = max(array($pos[0],$pos[2],$pos[4],$pos[6]));
        $minY = min(array($pos[1],$pos[3],$pos[5],$pos[7]));
        $maxY = max(array($pos[1],$pos[3],$pos[5],$pos[7]));

        $chr_left = abs($minX) - 1;
        $chr_top = abs($minY) - 1;
        $chr_width = $maxX - $minX;
        $chr_height = $maxY - $minY;

        $x = $chr_left + ($width / 2) - ($chr_width / 2);
        $y = $chr_top + ($height / 2) - ($chr_height / 2);

        imagettftext($im, round($size/96*72), 0, $x, $y, $fontcolor, $fontface, $codex);
        imagesavealpha($im, true);
        @imagepng($im, $file);
        @imagedestroy($im);
    }
    // ico_maker



    // 生成CODE39条形码
    static function code39($code, $file=null, $widthScale=2, $height=100)
    {
        $code39 = array(
            '0' => 'bwbwwwbbbwbbbwbw','1' => 'bbbwbwwwbwbwbbbw',
            '2' => 'bwbbbwwwbwbwbbbw','3' => 'bbbwbbbwwwbwbwbw',
            '4' => 'bwbwwwbbbwbwbbbw','5' => 'bbbwbwwwbbbwbwbw',
            '6' => 'bwbbbwwwbbbwbwbw','7' => 'bwbwwwbwbbbwbbbw',
            '8' => 'bbbwbwwwbwbbbwbw','9' => 'bwbbbwwwbwbbbwbw',
            'A' => 'bbbwbwbwwwbwbbbw','B' => 'bwbbbwbwwwbwbbbw',
            'C' => 'bbbwbbbwbwwwbwbw','D' => 'bwbwbbbwwwbwbbbw',
            'E' => 'bbbwbwbbbwwwbwbw','F' => 'bwbbbwbbbwwwbwbw',
            'G' => 'bwbwbwwwbbbwbbbw','H' => 'bbbwbwbwwwbbbwbw',
            'I' => 'bwbbbwbwwwbbbwbw','J' => 'bwbwbbbwwwbbbwbw',
            'K' => 'bbbwbwbwbwwwbbbw','L' => 'bwbbbwbwbwwwbbbw',
            'M' => 'bbbwbbbwbwbwwwbw','N' => 'bwbwbbbwbwwwbbbw',
            'O' => 'bbbwbwbbbwbwwwbw','P' => 'bwbbbwbbbwbwwwbw',
            'Q' => 'bwbwbwbbbwwwbbbw','R' => 'bbbwbwbwbbbwwwbw',
            'S' => 'bwbbbwbwbbbwwwbw','T' => 'bwbwbbbwbbbwwwbw',
            'U' => 'bbbwwwbwbwbwbbbw','V' => 'bwwwbbbwbwbwbbbw',
            'W' => 'bbbwwwbbbwbwbwbw','X' => 'bwwwbwbbbwbwbbbw',
            'Y' => 'bbbwwwbwbbbwbwbw','Z' => 'bwwwbbbwbbbwbwbw',
            '-' => 'bwwwbwbwbbbwbbbw','.' => 'bbbwwwbwbwbbbwbw',
            ' ' => 'bwwwbbbwbwbbbwbw','*' => 'bwwwbwbbbwbbbwbw',
            '$' => 'bwwwbwwwbwwwbwbw','/' => 'bwwwbwwwbwbwwwbw',
            '+' => 'bwwwbwbwwwbwwwbw','%' => 'bwbwwwbwwwbwwwbw'
        );

        $text = '*' . strtoupper($code) . '*';
        $length = strlen($text);

        $im = imagecreate($length * 16 * $widthScale + 2 * $widthScale, $height + 2 * $widthScale);

        $bg = imagecolorallocate($im, 255, 255, 255);
        //imagecolortransparent($im, $bg);
        $black = imagecolorallocate($im, 0, 0, 0);

        $chars = str_split($text);

        $colors = '';

        foreach ($chars as $char)
            $colors .= $code39[$char];

        foreach (str_split($colors) as $i => $color)
        {
            if ($color == 'b')
                imagefilledrectangle($im, $widthScale * ($i+1), $widthScale, $widthScale * ($i+1) - 1 + $widthScale , $height + $widthScale, $black);
        }

        // 16px per bar-set, halved, minus 6px per char, halved (5*length)
        // $textcenter = $length * 5 * $widthScale;
        // $textcenter = ($length * 8 * $widthScale) - ($length * 3);

        //imagestring($im, 2, $textcenter, $height-13, $text, $black);
        @imagepng($im, $file);
        @imagedestroy($im);
    }
    //code39



    // 生成CODE128
    static function code128($code, $file=null, $widthScale=2, $height=100)
    {
        include dirname(__FILE__).'/code128.php';

        // 记录字符
        $data = array();
        for($i = 0; $i < strlen($code); $i++)
        {
            if($i == (strlen($code) - 1))
            {
                //last character
                $v = $code{$i};
                if($code{$i} == ' ')
                    $v = 'SP';

                $data[] = $v;
            }
            else
            {
                if((is_numeric($code{$i})) && (is_numeric($code{($i + 1)})))
                {
                    //looks for double digit values
                    $data[] = $code{$i}.$code{($i + 1)};
                    $i++;
                }
                else
                {
                    $v = $code{$i};
                    if($code{$i} == ' ')
                        $v = 'SP';

                    $data[] = $v;
                }
            }
        }

        $code = array();
        $check = array();

        // 获取起点code
        $_type = isset($set['A'][$data[0]]) ? 'A' : ( isset($set['B'][$data[0]]) ? 'B' : 'C' );
        $code[] = $_type == 'A' ? 103 : ( $_type == 'B' ? 104 : 105 );
        $check[] = $code[0];

        foreach ($data as $v)
        {
            if (!isset($set[$_type][$v]))
            {
                // 不同类型下，转化类型
                $_type = isset($set['A'][$v]) ? 'A' : ( isset($set['B'][$v]) ? 'B' : 'C' );

                // 增加类型验证码
                $_code = $_type == 'A' ? 101 : ( $_type == 'B' ? 100 : 99 );
                $code[] = $_code;
                $check[] = $_code * (count($check));
            }

            $_code =  $set[$_type][$v];
            $code[] = $_code;
            $check[] = $_code * (count($check));
        }

        $code[] = array_sum($check) % 103;
        $code[] = 106;

        $_code = '';
        foreach ($code as $c)
            $_code .= $value[$c];

        $length = strlen($_code);
        $im = imagecreate($length * $widthScale + 2 * $widthScale, $height + 2 * $widthScale);

        $bg = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);

        foreach (str_split($_code) as $i => $color)
        {
            if ($color == '1')
                imagefilledrectangle($im, $widthScale * ($i+1), $widthScale, $widthScale * ($i+1) - 1 + $widthScale , $height + $widthScale, $black);
        }

        // 16px per bar-set, halved, minus 6px per char, halved (5*length)
        // $textcenter = $length * 5 * $widthScale;
        // $textcenter = ($length * 8 * $widthScale) - ($length * 3);

        //imagestring($im, 2, $textcenter, $height-13, $text, $black);
        @imagepng($im, $file);
        @imagedestroy($im);

    }
    //code128



}
?>