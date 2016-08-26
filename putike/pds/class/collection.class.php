<?php
// collection 采集类
class collection
{
    static public $_response = null;

    static public function hotel_elong_com($url)
    {
        self::$_response = curl_file_get_contents($url);
        $content = &self::$_response;

        if (!$content) return false;

        $data = array();
        $match = array();

//         preg_match ('/<h1>\s+<i id="hotelGrade" class=""><\/i>([\s\S]+?)<\/h1>/i', $content, $match);
//         $data['name'] = trim($match[1]);

//         if (!$match) return false;

//         preg_match ('/<li class="addr"><span title="([\s\S]+?)">/i', $content, $match);
//         $data['address'] = trim($match[1]);

//         preg_match ('/<span class="hotelphone">([\s\S]+?)<\/span>/i', $content, $match);
//         $data['tel'] = trim($match[1]);

//         preg_match ('/"BaiduLat":"([\d.]+)","BaiduLng":"([\d.]+)"/i', $content, $match);
//         $data['pos'] = $match[2].','.$match[1];


        // 2016-04-13更新 艺龙加入防抓取
        // 酒店名
        preg_match ('/.*id="lastbread">([\s\S]*?)<\/h1>/', $content, $match);

        $data['name'] = trim($match[1]);

        if (!$match) return false;

        // 地址
        preg_match ('/<p class="clearfix"><span class="mr5 left">[\s\S]*?<\/a>([\s\S]*?)<\/span>/', $content, $match);
        if(!$match[1])
        {
            preg_match ('/<h3 class="t18">.*<\/h3>[\s\S]*?<p class="c999">([\s\S]*?)<\/p>/', $content, $match);
        }
        $data['address'] = trim($match[1]);

        // 电话
        preg_match ('/<dt><i class="icon_view_s1"><\/i>酒店电话<\/dt>[\s\S]*?<dd>([\s\S]*?)<span.*>/', $content, $match);
        if(!$match[1])
        {
            preg_match ('/<p class="c999">酒店电话[\s\S]*?<span>([\s\S]*?)<\/span>/', $content, $match);
        }
        $data['tel'] = trim($match[1]);

        // 开业时间
        preg_match ('/<dd>酒店开业时间\s+([0-9]+)年\s+(新近装修时间\s+([0-9]+)\s+年\s+){0,1}<\/dd>/i', $content, $match);
        if(!empty($match[1]))
            $data['open'] = $match[1];

        if(!empty($match[3]))
            $data['decorate'] = $match[3];

        // 坐标
        preg_match ('/baiduLat:(.*?),baiduLng:(.*?),cityNameEn/', $content, $match);
        $data['pos'] = $match[2].','.$match[1];

        return $data;
    }



    static public function globalhotel_elong_com($url)
    {
        $header = array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
        );
        self::$_response = curl_file_get_contents($url, null, $header);
        $content = &self::$_response;

        if (!$content) return false;

        $data = array();
        $match = array();

        preg_match ('/<div class="titletop clx mt5">\s+<div class="topLeft">\s+<h1 class="ml15">([\s\S]+?)<span class="other">\(([\s\S]+?)\)<\/span>\s+<\/h1>/i', $content, $match);
        $data['name'] = trim($match[1]);
        $data['en'] = trim($match[2]);

        if (!$match) return false;

        preg_match ('/<div class="add ml15" id="topmap">\s+<span>地址：([\s\S]+?)<\/span>/i', $content, $match);
        $data['address'] = trim($match[1]);

        $data['tel'] = '';

        preg_match ('/\s+lat: "([\d.]+)",\s+lng: "([\d.]+)"/i', $content, $match);
        $data['pos'] = $match[2].','.$match[1];

        return $data;
    }

}


?>