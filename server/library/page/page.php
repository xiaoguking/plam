<?php
/**
 * Created by PhpStorm.
 * User: whee
 * Date: 2016/10/11
 * Time: 17:17.
 */
class page
{
    /* 当前URL */
    private $now_url;

    /* page标签，用来控制url页。比如说xxx.php?PB_page=2中的PB_page */
    private $page_name = 'page=(.*)';

    private $nowindex = 1;

    /* 循环多少个分页 */
    public $pagebarnum = 5;

    /* 总数 */
    private $maxnum;

    /* 页数 */
    private $pagenum;

    /* 每页行数 */
    private $pagesize;

    private $get = 0;

    private $select_url;
    private $base_url;//不带page和pcount参数的网址
    private $jump_url;//不带page参数的网址

    public function __construct()
    {
    }

    public function url($page_name)
    {
        $this->page_name = $page_name;

        if (!strpos($page_name, '?') && !strpos($page_name, '&')) {
            $this->get = 1;
        }

        return $this;
    }

    /**
     * now_url URL转换.
     *
     * @author zhuayi
     */
    public function now_url()
    {
        /* GET模式直接获取当前URL */
        if ($this->get == 0) {
            $this->now_url = $_SERVER['REQUEST_URI'];
        }

        /* 替换通配符 */
        $this->page_name = str_replace('(.*)', '([0-9]*)', $this->page_name);
        $this->page_name = str_replace('/', '\/', $this->page_name);
        $this->page_name = str_replace('?', '\?', $this->page_name);

        /* 如果存在分页参数,则获取当前页码 */
        if (preg_match('/'.$this->page_name.'/i', urldecode($_SERVER['REQUEST_URI']), $nowindex)) {
            if (isset($nowindex[1]) || !empty($nowindex[1])) {
                $this->nowindex = $nowindex[1];
            }
        }

        if ($this->nowindex > $this->pagenum) {
            $this->nowindex = $this->pagenum;
        }

        if ($this->get == 0) {
            /* 删除带有分页参数,还原原始URL */
            $this->now_url = preg_replace('/\&?'.$this->page_name.'/i', '', $this->now_url);

            /* GET模式下组合当前URL */
            if (strpos($this->now_url, '?') === false) {
                $this->now_url .= '?'.$this->page_name;
            } elseif (substr($this->now_url, -1, 1) != '&' && substr($this->now_url, -1, 1) != '?') {
                $this->now_url .= '&'.$this->page_name;
            } else {
                $this->now_url .= $this->page_name;
            }
        } else {
            $this->now_url .= $this->page_name;
        }
    }

    /**
     * maxnum 设置总记录数.
     *
     * @author zhuayi
     */
    //@whee 重写该方法，兼容老版本分页与phalcon分页器
    public function maxnum($num = null, $pagesize)
    {
        $this->maxnum = $num;
        $this->pagesize = $pagesize;

        return $this;
    }

    /**
     * pagenum 得到总页数.
     *
     * @author zhuayi
     */
    private function pagenum()
    {
        $this->pagenum = ceil($this->maxnum / $this->pagesize);
    }

    /**
     * show 输出.
     *
     * @author zhuayi
     */
    public function show($tpl = 'page_bootstrap')
    {
        /* 获取总页数 */
        $this->pagenum();
        $this->now_url();
        $this->select_links();

        if ($this->pagenum < 2) {
            //return '';
        }

        /* 首页链接 */
        $this->home = $this->links(1);

        /* 尾页链接 */
        $this->last = $this->links($this->pagenum);

        $plus = ceil($this->pagebarnum / 2);

        if ($this->pagebarnum - $plus + $this->nowindex > $this->maxnum) {
            $plus = ($this->pagebarnum - $this->maxnum + $this->nowindex);
        }

        $begin = $this->nowindex - $plus + 1;
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '';

        for ($i = $begin; $i < $begin + $this->pagebarnum; ++$i) {
            if ($i <= $this->pagenum) {
                $this->page[$i] = $this->links($i);
            } else {
                break;
            }
        }

        /* 生成页码
        for ($i=1; $i <= $this->pagenum; $i++)
        {
            $this->page[$i] = $this->links($i);
        }
        */
        /* 生成上一页 */
        $this->prevpage();

        /* 生成下一页 */
        $this->nextpage();
        ob_start();
        require $tpl = dirname(__FILE__).'/tpl/'.$tpl.'.html';
        $page_content = ob_get_contents();
        ob_clean();

        return $page_content;
    }

    /**
     * prevpage 上一页.
     *
     * @author zhuayi
     */
    public function prevpage()
    {
        $index = $this->nowindex - 1;

        if ($index < 1) {
            $this->prevpage = '';
        } else {
            $this->prevpage = $this->links($index);
        }
    }

    /**
     * nextpage 下一页.
     *
     * @author zhuayi
     */
    public function nextpage()
    {
        $index = $this->nowindex + 1;

        if ($index > $this->pagenum) {
            $this->nextpage = '';
        } else {
            $this->nextpage = $this->links($index);
        }
    }

    /**
     * links 分页连接.
     *
     * @author zhuayi
     */
    public function links($i)
    {
        if ($this->nowindex == $i) {
            return  '';
        }

        $this->now_url = str_replace('\/', '/', $this->now_url);
        $this->now_url = str_replace('\?', '?', $this->now_url);

        if ($i == 1) {
            //echo $this->page_name = str_replace('([0-9]*)','\(\[0-9\]*\)',$this->page_name);

            //echo $this->page_name;
            if ($this->get == 0) {
                /* GET模式,去掉第一页page=1的值 */
                $now_url = str_replace($this->page_name, '', $this->now_url);
                if (substr($now_url, -1, 1) == '&' || substr($now_url, -1, 1) == '?') {
                    return substr($now_url, 0, strlen($now_url) - 1);
                } else {
                    return $this->now_url;
                }
            } else {
                /* 得到通配符前一个字符,如index_(.*).html中的"_", 替换为空 */
                $reg = substr($this->now_url, (strpos($this->now_url, '([0-9]*)') - 1), 1);

                return str_replace($reg.'([0-9]*)', '', $this->now_url);
            }

            return $now_url;
        } else {
            return str_replace('([0-9]*)', $i, $this->now_url);
        }
    }

    /**
     * select_links 行数选择器连接.
     *
     * @author eto
     */
    public function select_links()
    {
        $this->select_url = str_replace('\/', '/', $this->now_url);
        $this->select_url = str_replace('\?', '?', $this->select_url);
        $this->select_url = str_replace('([0-9]*)', 1, $this->select_url);
        $this->jump_url = preg_replace('/[&,\?]page=[0-9]*/', '', $this->select_url);
        $this->select_url = preg_replace('/&pcount=[0-9]*/', '', $this->select_url);
        $this->base_url = preg_replace('/[&,\?]page=[0-9]*/', '', $this->select_url);
    }
}
