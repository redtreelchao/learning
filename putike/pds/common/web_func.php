<?php

/**
 * format a array for a insert query string
 +-----------------------------------------
 * @param array $data
 * @param bool $multiple
 * @return string
 */
function insert_array($data, $multiple=false, $mukey='')
{
    if ($multiple || isset($data[0]))
    {
        $value = array();
        $sql = array();
        foreach ($data as $i => $v)
        {
            $rs = insert_array($v, false, $i+1);
            $value = array_merge($value, $rs['value']);
            $sql[] = $rs['sql'];
        }

        $key = array_keys($data[0]);
        $key = '(`'.implode('`,`', $key).'`)';

        return array('column'=>$key, 'sql'=>implode(',', $sql), 'value'=>$value);
    }
    else
    {
        $value = array();
        foreach ($data as $k => $v)
        {
            if($v === null)
            {
                $data[$k] = 'NULL';
            }
            else
            {
                $value[":{$k}{$mukey}"] = $v;
                $data[$k] = ":{$k}{$mukey}";
            }
        }

        $key = '';
        if ($mukey === '')
        {
            $key = array_keys($data);
            $key = '(`'.implode('`,`', $key).'`)';
        }

        return array('column'=>$key, 'sql'=>'('.implode(',', $data).')', 'value'=>$value);
    }
}


/**
 * format a array for a update query string
 +-----------------------------------------
 * @param array $data
 * @return string
 *
 * create sephiroth 2014-03-11
 */
function update_array($data)
{
    $value = array();
    foreach ($data as $k => $v)
    {
        if ( $v === null )
        {
            $data[$k] = "`{$k}` = NULL";
        }
        else
        {
            $value[":{$k}"] = $v;
            $data[$k] = "`{$k}` = :{$k}";
        }
    }

    return array('sql'=>implode(',', $data), 'value'=>$value);
}


/**
 * use for mysql "ON DUPLICATE KEY UPDATE"
 +-----------------------------------------
 * @param array $keys
 * @return void
 */
function update_column($keys)
{
    $columns = array();
    foreach($keys as $c)
    {
        $columns[] = "`{$c}`=VALUES(`{$c}`)";
    }
    return implode(',', $columns);
}

/**
 * file_get_centents by curl
 +-----------------------------------------
 * @param string $url
 * @param mixed  $post
 * @param int    $timeout
 * @return void
 */
function curl_file_get_contents($url, $post=null, $header=array(), $timeout=5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if ($header)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if ($post)
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($post) ? http_build_query($post) : $post);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}


/**
 * remove data to retrieve
 +-----------------------------------------
 * @param string $table
 * @param string $where
 * @return bool
 */
function delete($table, $where, $commit=true)
{
    $db = db(config('db'));

    $data = $db -> prepare("SELECT * FROM `{$table}` WHERE {$where};") -> execute();
    if (!$data) return true;

    if ($commit) $db -> beginTrans();

    $log = array(
        ':db'    => $table,
        ':data'  => serialize($data),
        ':time'  => NOW,
    );

    if (!$db -> prepare("INSERT INTO `ptc_retrieve` (`db`,`data`,`time`) VALUES (:db, :data, :time);") -> execute($log))
    {
        $db -> rollback();
        return false;
    }

    if (false === $db -> prepare("DELETE FROM `{$table}` WHERE {$where};") -> execute())
    {
        $db -> rollback();
        return false;
    }

    if (!$commit) return true;

    if ($db -> commit()) return true;

    $db -> rollback();
    return false;
}

/**
 * parse a SimpleXML to array
 +-----------------------------------------
 * @param SimpleXML $SimpleXML
 * @param array     $result
 * @param int       $length
 * @return array
 */
function parse_xml(&$SimpleXML, &$result=array(), $length=200)
{
    foreach((array)$SimpleXML as $key => $value)
    {
        if($SimpleXML -> $key && $attr = $SimpleXML -> $key -> attributes())
        {
            $result[$key] = array();
            foreach($attr as $k => $v)
            {
                $result[$key]['_'.$k] = (string)$v;
            }
        }

        if(!is_string($value))
        {
            if(is_array($value) && isset($value[0]))
            {
                $result[$key] = array();
                foreach($value as $k => $v)
                {
                    parse_xml($v, $result[$key][], $length);
                }
            }else{
                parse_xml($value, $result[$key], $length);
            }
        }else{

            if (strlen($value) >= $length) continue;

            if (isset($result[$key]))
                $result[$key]['#TEXTNOTE'] = $value;
            else
                $result[$key] = $value;
        }
    }
    return $result;
}



/**
 * build a XML from array
 +-----------------------------------------
 * @access public
 * @param mixed     $arr         源数据
 * @param SimpleXML $SimpleXML   SimpleXML 自引用回调
 * @param string    $tag         初始化标签
 * @return void
 */
function build_xml($arr, $mode=1, &$SimpleXML=false, $tag='list')
{
    if($SimpleXML === false)
    {
        $SimpleXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$tag}></{$tag}>");
        $root = 1;
    }

    foreach($arr as $key => $value)
    {
        if(!is_array($value))
        {
            if ($mode == 1)
                $SimpleXML -> addAttribute($key, $value);
            else
                $SimpleXML -> addChild($key, $value);
        }
        else
        {
            if(isset($value['#TEXTNOTE']))
            {
                $Element = $SimpleXML -> addChild($key, $value['#TEXTNOTE']);
                unset($value['#TEXTNOTE']);
                build_xml($value, $mode, $Element);
            }
            else
            {
                if(isset($value[0]))
                {
                    if (substr($key, -1) == 's')
                    {
                        $Element = $SimpleXML -> addChild($key);
                        $key = substr($key, 0, -1);
                    }
                    else
                    {
                        $Element = $SimpleXML;
                    }

                    foreach($value as $v)
                    {
                        $SubElement = $Element -> addChild($key);
                        build_xml($v, $mode, $SubElement);
                    }
                }
                else
                {
                    $Element = $SimpleXML -> addChild($key);
                    build_xml($value, $mode, $Element);
                }
            }
        }
    }

    if(isset($root)) return $SimpleXML -> asXML();
}


/**
 * 数字转字符，最高记录 31位
 +-----------------------------------------
 * @access public
 * @param int $int
 * @return void
 */
function int2chr($int)
{
    if($int < 0 || $int > 31)
        return 'X';

    if($int >= 10)
        return chr(55+$int);

    return $int;
}



/**
 * 获取2倍图
 +-----------------------------------------
 * @param string $file
 * @param int $multiple
 * @return void
 */
function image2x($file, $suffix='_@2x')
{
    return substr($file, 0, -4).$suffix.substr($file, -4);
}



/**
 * 树状数据的整理
 +-----------------------------------------
 * @param array $data  需经过 level 排序
 * @return void
 */
function tree($data)
{
    $result = array();
    foreach ($data as $v)
    {
        if ($v['level'] == 0)
        {
            $result[$v['id']] = $v;
        }
        else
        {
            $temp = array_values($result);
            $offset = array_search($v['pid'], array_keys($result)) + 1; // 父级所在位置

            if ($offset === false) continue;

            // 从父级位置往后找，发现父级同级或高级终止
            for ($i = $offset; $i <= count($temp); $i++)
            {
                if($temp[$i]['level'] <= $v['level'] - 1) break;
                $offset++;
            }

            // 数组插入
            $front = array_slice($result, 0, $offset, true);
            $back = array_slice($result, $offset, count($result), true);
            $result = $front + array($v['id'] => $v) + $back;
        }
    }

    return $result;
}


/**
 * 供应商列表
 +-----------------------------------------
 * @access public
 * @return void
 */
function supplies()
{
    return array('HMC'=>'HMC', 'JLT'=>'深捷旅', 'CNB'=>'港捷旅', 'ELG'=>'艺龙');
}

/**
 * 产品类型
 +-----------------------------------------
 * @access public
 * @return void
 */
function producttypes()
{
    return array(
        array('code'=>1, 'name'=>'酒店产品',           'abbr'=>'酒店',     'disabled'=>false),
        array('code'=>3, 'name'=>'机票产品',           'abbr'=>'机票',     'disabled'=>true),
        array('code'=>7, 'name'=>'生鲜/消费产品',      'abbr'=>'商品',     'disabled'=>false),
        array('code'=>5, 'name'=>'景点/体验产品',      'abbr'=>'景/体验',  'disabled'=>true),
        array('code'=>2, 'name'=>'酒店+车辆产品',      'abbr'=>'车+酒',    'disabled'=>false),
        array('code'=>4, 'name'=>'酒店+机票产品',      'abbr'=>'机+酒',    'disabled'=>false),
        array('code'=>6, 'name'=>'酒店+景点产品',      'abbr'=>'景+酒',    'disabled'=>true),
        array('code'=>8, 'name'=>'机票+景点产品',      'abbr'=>'机+景',    'disabled'=>true),
        array('code'=>9, 'name'=>'机票+酒店+景点产品', 'abbr'=>'机+景+酒', 'disabled'=>true),
    );
}

/**
 * 周边类型
 +-----------------------------------------
 * @access public
 * @return void
 */
function viewtypes()
{
    return array(
        'traffic'   => '交通',
        'view'      => '景点',
        'food'      => '美食',
        'sport'     => '运动',
        'shop'      => '购物',
        'play'      => '玩乐',
        'farm'      => '农家乐',
        'other'     => '其他',
    );
}

/**
 * 格式化房型名称
 +-----------------------------------------
 * @access public
 * @param mixed $name
 * @param mixed $bed
 * @param string $nation
 * @param string $package
 * @param string $condition
 * @param string $tag
 * @return void
 */
function roomname($name, $bed, $nation='', $package='', $condition='', $tag='')
{
    switch($bed)
    {
        case 'S':
            $room = '单人房';
            $bed  = '(单人床)';
            break;
        case 'T':
            $room = '双床房';
            $bed  = '(双人双床)';
            break;
        case 'D':
            $room = '大床房';
            $bed  = '(双人大床)';
            break;
        case '2':
            $room = '房';
            $bed  = '';
            break;
        case '3':
            $room = '房';
            $bed  = '';
            break;
        case 'K':
            $room = '房(超大床)';
            $bed  = '(超大床)';
            break;
        case 'C':
            $room = '房(圆床)';
            $bed  = '(圆床)';
            break;
        default:
            $room = '房';
            $bed  = '';
    }

    $nation = $nation ? "（{$nation}）" : '';
    $package = $package ? "（{$package}）" : '';
    $condition = $condition ? "（{$condition}）" : '';

    if($tag)
    {
        $nation    = "<{$tag}>{$nation}</{$tag}>";
        $package   = "<{$tag}>{$package}</{$tag}>";
        $condition = "<{$tag}>{$condition}</{$tag}>";
    }

    return $name == str_replace(array('阁','间','房','别墅'), '', $name) ? "{$name}{$room}{$nation}{$package}{$condition}" : "{$name}{$bed}{$nation}{$package}{$condition}";
}


/**
 * 格式化床型名称
 +-----------------------------------------
 * @access public
 * @param string $code
 * @return void
 */
function bedname($code)
{
    switch ($code)
    {
        case 'S': $bed = '单人床'; break;
        case 'D': $bed = '双人大床'; break;
        case 'T': $bed = '双人双床'; break;
        case 'K': $bed = '超大床'; break;
        case 'C': $bed = '圆床'; break;
        case 'D1': $bed = '尽量大床'; break;
        case 'T1': $bed = '尽量双床'; break;
        case 'D2': $bed = '务必大床'; break;
        case 'T2': $bed = '务必双床'; break;
        case '2': $bed = '大/双床'; break;
        case 'O': $bed = '特殊床型'; break;
    }

    return $bed;
}


/**
 * 格式化特殊要求内容
 +-----------------------------------------
 * @access public
 * @param string $code
 * @return void
 */
function requirename($code)
{
    $request = order::require_code();
    return $request[$code];
}


/**
 * 快递信息
 +-----------------------------------------
 * @access public
 * @param mixed $code
 * @return void
 */
function expressname($code=null)
{
    $express = array(
        'youshuwuliu'   => '优速物流',
        'shunfeng'      => '顺丰快递',
        'zhaijisong'    => '宅急送',
        'huitongkuaidi' => '百世汇通',
        'tiantian'      => '天天快递',
        'yuantong'      => '圆通快递',
        'yunda'         => '韵达快递',
        'zhongtong'     => '中通快递',
        'shentong'      => '申通快递',
        'youzhengguonei'=> '邮政',
        'ems'           => 'EMS',
        'other'         => '其他'
    );

    if ($code === null)
        return $express;
    else if ($code)
        return $express[$code];
    else
        return '';
}


/**
 * Simple BBcode
 +-----------------------------------------
 * @access public
 * @param string $text
 * @return void
 */
function bbcode($text)
{
    $text = preg_replace('/\[smiley\]([a-z_-]+)\[\/smiley\]/i', '<img src="/template/js/nicedit/smiley/$1.gif" alt="$1" />', $text);
    $text = preg_replace('/\[b\]([\s\S]*?)\[\/b\]/i', '<b>$1</b>', $text);
    $text = preg_replace('/\[color=([#0-9a-z]+)\]([\s\S]*?)\[\/color\]/i', '<font color="$1">$2</font>', $text);
    //$text = preg_replace('/\[url([\S]+?)\]([\s\S]*?)\[\/url\]/i', '<a href="$1">$2</a>', $text);
    $text = nl2br($text);
    return $text;
}


//price ID 的简单加密
function key_encryption($id, $decrypt=false)
{
    if($decrypt)
    {
        //解密
        return bzdecompress(base64_decode(str_pad(strtr($id, '-_', '+/'), strlen($id) % 4, '=', STR_PAD_RIGHT)));
    }else{
        //加密
        return rtrim(strtr(base64_encode(bzcompress($id)), '+/', '-_'), '=');
    }
}

// 检测用户信息
function rbac_user()
{
    // 检测域名 ip / api 只允许通过接口访问
    if (strtolower($_SERVER['HTTP_HOST']) != 'pds.putike.cn' && strtolower($_SERVER['HTTP_HOST']) != 'putike.pds')
    {
       // redirect('http://pds.putike.cn'.(empty($_SERVER['REQUEST_URI']) ? '/' : $_SERVER['REQUEST_URI']));
    }

    if (empty($_SESSION['uid']))
    {
        if (!empty($_COOKIE['sess']))
        {
            $s = $_COOKIE['sess'];
            $pos = strpos($s, 'p');
            $uid = substr($s, 0, $pos);
            $ck = $uid % 4;
            $password = substr($s, $pos+1, 32 - $ck);
            $md = substr($s, -$ck);

            $db = db(config('db'));
            $user = $db -> prepare("SELECT `id`,`name`,`password`,`md`,`role` FROM `rbac_user` WHERE `id`=:uid") -> execute(array(':uid'=>$uid));

            if (!$user || substr($user[0]['password'], $ck) != $password || strtolower(substr($user[0]['md'], -$ck)) != $md)
            {
                setcookie('sess');
                redirect("/login.php?referer=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
            }
            else
            {
                $_SESSION['uid'] = $user[0]['id'];
                $_SESSION['name'] = $user[0]['name'];
                $_SESSION['role'] = $user[0]['role'];
            }
        }
        else
        {
            redirect("/login.php?referer=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
        }
    }
}

// 时间倒计时
function timer($microtime, $level=3, $justnow=false)
{
    if ($justnow && $microtime < 60) echo $justnow;
    return
        floor($microtime / 86400).'天'
        .($level > 1 ? floor($microtime % 86400 / 3600).'小时' : '')
        .($level > 2 ? floor($microtime % 3600 / 60).'分' : '')
        .($level > 3 ? floor($microtime % 60).'秒' : '');
}


// 历史操作记录
function history($id, $type, $intro, $data)
{
    // log histrory
    $data = array(
        'intro'     => $intro,
        'type'      => $type,
        'pk'        => $id,
        'uid'       => (int)$_SESSION['uid'],
        'username'  => (string)$_SESSION['name'],
        'data'      => json_encode($data, JSON_UNESCAPED_UNICODE),
        'time'      => NOW,
    );
    list($column, $sql, $value) = array_values(insert_array($data));

    $db = db(config('db'));
    $rs = $db -> prepare("INSERT INTO `ptc_history` {$column} VALUES {$sql};") -> execute($value);
    return $rs;
}


// 生成加密文
function authcode($string, $key, $operate='ENCODE', $expiry=0)
{
    $ckey_length = 4;

    $key = md5($key);

    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $operate == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length);

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operate == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;

        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $result .= chr(ord($string[$i]) ^ ($box[$i]));
    }

    if ($operate == 'DECODE')
    {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
            return substr($result, 26);
        else
            return '';
    }
    else
    {
        return $keyc . rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
    }
}

// number to char
function dec2chr($int, $lower=false)
{
    // 0-9 a-z A-Z = 62
    $hex = $lower ? 36 : 62;

    $c = $int % $hex;

    if ($c < 10)
        $c = $c;
    else if ($c >= 10 && $c < 36)
        $c = chr(97 + ($c - 10));
    else
        $c = chr(65 + ($c - 36));

    if ($int >= $hex)
        return dec2chr(floor($int / $hex), $lower).$c;
    else
        return $c;
}

function chr2dec($chr, $lower=false)
{
    $hex = $lower ? 36 : 62;
    $chrs = array_reverse(str_split($chr));
    $int = 0;
    foreach ($chrs as $i => $c)
    {
        if (is_numeric($c))
            $c = $c;
        else if (strtolower($c) == $c)
            $c = ord($c) - 97 + 10;
        else
            $c = ord($c) - 65 + 36;

        $int += $c * pow($hex, $i);
    }

    return $int;
}
