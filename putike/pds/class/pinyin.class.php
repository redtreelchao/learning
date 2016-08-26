<?php
/**
 * 汉字转拼音类
 * 仅支持UTF-8类型，需要加载py.sql 数据库
 +-----------------------------------------
 * @author Administrator
 * @category
 * @version $Id$
 */

class pinyin
{

    static function get($str)
    {
        $db = db(config('db'));
        $pos = 0;
        $pinyin = array();

        while(true)
        {
            $chr = mb_substr($str, $pos, 1, 'utf-8');
            if($chr)
            {
                if(strlen($chr) == 1)
                {
                    $pinyin[] = $chr;
                }
                else
                {
                    if($_py = $db -> prepare("SELECT `pinyin` FROM `pinyin` WHERE `chr`=:chr ORDER BY `priority` DESC;") -> execute(array(':chr'=>$chr)))
                        $pinyin[] = ucfirst($_py[0]['pinyin']);
                }
            }
            else
            {
                break;
            }

            $pos ++;
        }

        return implode('', $pinyin);
    }

}
?>