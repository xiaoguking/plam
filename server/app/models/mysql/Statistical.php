<?php

namespace Model\Mysql;

class Statistical extends BasesModel
{
    /**
     * 统计注册数
     * 当前在线人数
     * ...
     * @return array
     */
    public static function getHomeStl() :array
    {
        $db = self::getDy();
        $data = [
            'register_count' => 0,
            'online_count'   => 0,
            'box_trading'    => 0,
            'nft_trading'    => 0,
            'box_sales'      => 0
        ];
        //总注册数
        $sql = "select count(*) as count from register ";
        $ret = $db->fetchOne($sql);
        if (!empty($ret)){
            $data['register_count'] = (int)$ret['count'];
        }

        //当前在线人数
        $time = date("Y-m-d",time());
        $sql = "select ol_num from ol where DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$time' order by gettime desc limit 0,1";
        $ret = $db->fetchOne($sql);
        if (!empty($ret['ol_num'])){
            $data['online_count'] = (int)$ret['ol_num'];
        }
        //Box销售额
        $sql = "select sum(price) as sum_pr from box_panic_buy";
        $ret = $db->fetchOne($sql);
        if (!empty($ret['sum_pr'])){
            $data['box_sales'] = round($ret['sum_pr'],4);
        }
        //box 交易额
        $sql = "select sum(price) as sum_pr from box_market_logs ";
        $ret = $db->fetchOne($sql);
        if (!empty($ret['sum_pr'])){
            $data['box_trading'] = round($ret['sum_pr'],4);
        }
        //nft 交易额
        $sql = "select sum(price) as sum_pr from card_market_logs ";
        $ret = $db->fetchOne($sql);
        if (!empty($ret['sum_pr'])){
            $data['nft_trading'] = round($ret['sum_pr'],4);
        }
        return $data;
    }

    /**
     * 在线人数统计
     * @return array
     */
    public static function getOnline() :array
    {
        //今日数据
        $data = ['today' => [],'yesterday' => [],'time' => ''];
        $time = date('Y-m-d',time());
        $sql = "select ol_num,DATE_FORMAT( gettime, '%H:%i:%s' ) as gettime from ol where  DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$time' order by gettime asc";
        $ret = self::getDy()->fetchAll($sql);
        if (!empty($ret)){
            $data['today'] = array_column($ret,'ol_num');
            $data['time'] = array_column($ret,'gettime');
        }
        //昨日
        $time = date('Y-m-d',strtotime('-1 day'));
        $sql = "select ol_num,DATE_FORMAT( gettime, '%H:%i:%s' ) as gettime from ol where  DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$time' order by gettime asc";
        $ret = self::getDy()->fetchAll($sql);

        if (!empty($ret)){
            $data['yesterday'] = array_column($ret,'ol_num');
            $data['time'] = array_column($ret,'gettime');
        }
        return $data;
    }

    /**
     * 交易额统计
     * @return array
     */
    public static function getTurnover() :array
    {
        //七天数据
        $db = self::getDy();
        $time = [];
        for ($i = 0;$i < 7;$i++){
            $time[$i] = date('Y-m-d',strtotime("-$i day"));
        }
        sort($time);
        $data = [
            'box_trading'    => [],
            'nft_trading'    => [],
            'box_sales'      => []
        ];
        foreach ($data as $k => &$v){
            foreach ($time as $key => $value){
                   switch ($k){
                       case "box_trading":
                           $sql = "select sum(price) as sum_pr from box_market_logs where DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$value'";
                           $ret = $db->fetchOne($sql);
                           $v[$key] = empty($ret['sum_pr']) ? "0" : round($ret['sum_pr'],4);
                           break;
                       case "nft_trading":
                           $sql = "select sum(price) as sum_pr from card_market_logs where DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$value'";
                           $ret = $db->fetchOne($sql);
                           $v[$key] = empty($ret['sum_pr']) ? "0" : round($ret['sum_pr'],4);
                           break;
                       case "box_sales":
                           $sql = "select sum(price) as sum_pr from box_panic_buy where DATE_FORMAT( gettime, '%Y-%m-%d' ) = '$value'";
                           $ret = $db->fetchOne($sql);
                           $v[$key] = empty($ret['sum_pr']) ? "0" : round($ret['sum_pr'],4);
                           break;
                       default:
                           $v[$k] = "0";
                   }
            }
        }
        return  ['x'=> $time,'y' => $data];
    }
}
