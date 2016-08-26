<?php
/**
 * page 分页类
 +-----------------------------------------
 * @category
 * @package page
 * @author Page7
 * @version $Id$
 */
class page
{

    // 总行数
    protected $total;

    // 分页根据
    protected $var_name = 'page';

    // 列表每页显示行数
    public $rows;

    // 分页参数
    public $params = '';

    // 当前页起始行
    public $first_row = 0;

    // 当前页
    protected $now_page = 1;

    // 分页栏每页显示的页数
    public $other_pages_count = 5;

    // 分页显示定制
    public $config = array('header'=>'条记录', 'prev'=>'上一页', 'next'=>'下一页', 'first'=>'第一页', 'last'=>'最后一页');


    public function __construct($total, $rows=15, $params=array(), $var_name='page')
    {
        $this -> total = (int)$total;
        $this -> rows  = !empty($rows) ? (int)$rows : 15;
        $this -> params = $params ? http_build_query($params).'&' : '';

        if ($var_name)
            $this -> var_name = $var_name;

        $this -> now_page = !empty($_GET[$var_name]) && ($_GET[$var_name] > 0) ? (int)$_GET[$var_name] : 1;

        $this -> first_row = $this -> rows * ($this -> now_page - 1);
    }


    /**
     * 获取SQL Limit 字段
     +-----------------------------------------
     * @access public
     * @return void
     */
    public function limit()
    {
        if(0 == $this -> total)
            return '0 , '.$this -> rows;

        $total_pages = ceil($this -> total / $this -> rows);
        if ($this -> now_page > $total_pages)
            $this -> now_page = $total_pages;

        $first_row = ($this -> now_page - 1) * $this -> rows;
        $this -> first_row = $first_row;

        return "{$first_row}, {$this->rows}";
    }



    /**
     * 显示分页
     +-----------------------------------------
     * @access public
     * @param bool $array
     * @return void
     */
    public function show()
    {
        $total_pages = ceil($this -> total / $this -> rows);
        if ($this -> now_page > $total_pages)
            $this -> now_page = $total_pages;

        $first_row = ($this -> now_page - 1) * $this -> rows;
        $this -> first_row = $first_row;

        if(strpos($_SERVER['REQUEST_URI'], "&{$this->var_name}=") === false && strpos($_SERVER['REQUEST_URI'], "?{$this->var_name}=") === false)
            $url  =  $_SERVER['REQUEST_URI'];
        else
            $url  =  preg_replace("/([&]|[?]){$this->var_name}=[0-9]+/", '', $_SERVER['REQUEST_URI']);

        $url = $url . (strpos($url, '?') ? '&' : '?') . $this -> params;

        $pageArray['url']   =   "{$url}{$this->var_name}=";
        $pageArray['rows']  =   $this -> total;
        $pageArray['prev']  =   $this -> now_page - 1 < 1 ? 1 : $this -> now_page - 1;;
        $pageArray['next']  =   $this -> now_page + 1 > $total_pages ? $total_pages : $this -> now_page + 1;
        $pageArray['total'] =   $total_pages;
        $pageArray['now']   =   $this -> now_page;
        return $pageArray;
    }

}//类定义结束
?>