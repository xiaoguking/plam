<?php

namespace Cron;

use Model\Mysql\AdminRetained;

class Retained extends BaseCron
{
    public static function main()
    {
        ini_set('max_execution_time', 1000);
        ini_set('memory_limit', '1024M');
        $data = self::msg(0,'');
        //统计14天内用户的留存数据
        $time = [];
        for ($i = 0;$i < 14;$i++){
            $time[$i] = date('Y-m-d',strtotime("-$i day"));
        }
        sort($time);
        $list = [];
        $db_log =  \Phalcon\Di::getDefault()->get('dbDy');
        //当前时间小于登录表中最小时间直接跳过
        $sql = 'select gettime from login order by gettime asc limit 0,1';
        $ret = $db_log->fetchOne($sql);

        foreach ($time as $k => $value){
            if (empty($ret)){
                if ($value < date('Y-m-d',time())){
                    $list[$k] =  [];
                    continue;
                }
            }else{
                if ($value < date('Y-m-d',strtotime($ret['gettime']))){
                    $list[$k] =  [];
                    continue;
                }
            }
            $ret = self::ret($db_log,$value);
            $list[$k] = $ret;
        }
        $data['msg']= $list;
        return $data;
    }
    private static function ret($db , $time) : array
    {
        //当前时间小于登录表中最小时间直接跳过
        $db_log = $db;
        $data = [
            'times' => $time,
            'news'  => 0,
            'days1' => '0.00%',
            'days3' => '0.00%',
            'days7' => '0.00%',
            'daynews' => 0,
            'box_trading' => 0,
            'nft_trading'=> 0,
            'box_sales' => 0

        ];
        $where = "DATE_FORMAT( a.gettime,'%Y-%m-%d') = '$time'";
        $sql = "select 
            *,
            concat(round(100*day1/news,2),'%')  'days1'
            from (
            select 
            DATE_FORMAT( a.gettime,'%Y-%m-%d') 'time',
			count(distinct a.uid) 'news',						
            count(distinct b.uid) 'day1'
            from login a 
            left join login b on a.uid = b.uid and DATE_FORMAT( b.gettime,'%Y-%m-%d') =  DATE_FORMAT(FROM_UNIXTIME(unix_timestamp( a.gettime ) + 60 * 60 * 24), '%Y-%m-%d' ) 
            where  $where  group by DATE_FORMAT( a.gettime,'%Y-%m-%d') 
            ) p";
        $list = $db_log->fetchOne($sql);
        if (!empty($list)){
            $data['news'] = $list['news'];
            $data['days1'] = $list['days1'];
        }
        $sql = "select *,
            concat(round(100*day3/news,2),'%')  'days3'
            from (
            select 
            DATE_FORMAT( a.gettime,'%Y-%m-%d') 'time',
			count(distinct a.uid) 'news',						
            count(distinct c.uid) 'day3'
            from login a 
            left join login c on a.uid = c.uid and DATE_FORMAT( c.gettime,'%Y-%m-%d') =  DATE_FORMAT(FROM_UNIXTIME(unix_timestamp( a.gettime ) + 60 * 60 * 24 * 3), '%Y-%m-%d' ) 
            where  $where  group by DATE_FORMAT( a.gettime,'%Y-%m-%d') 
            ) p";
        $list3 = $db_log->fetchOne($sql);
        if (!empty($list3)){
            $data['days3'] = $list3['days3'];
        }
        $sql = "select *,
            concat(round(100*day7/news,2),'%')  'days7'
            from (
            select 
			count(distinct a.uid) 'news',						
            count(distinct d.uid) 'day7'
            from login a 
            left join login d on a.uid = d.uid and DATE_FORMAT( d.gettime,'%Y-%m-%d') = DATE_FORMAT(FROM_UNIXTIME(unix_timestamp( a.gettime ) + 60 * 60 * 24 * 7), '%Y-%m-%d' ) 
            where  $where  group by DATE_FORMAT( a.gettime,'%Y-%m-%d') 
            ) p";
        $list7 = $db_log->fetchOne($sql);
        if (!empty($list7)){
            $data['days7'] = $list7['days7'];
        }
        //今日新增人数
        $sql = "select count(*) as count from register where DATE_FORMAT(gettime,'%Y-%m-%d') = '$time'";
        $res = $db_log->fetchOne($sql);
        $data['daynews'] = empty($res['count']) ? 0 : (int)$res['count'];

        $sql = "select sum(price) as sum_pr from box_market_logs where DATE_FORMAT(gettime,'%Y-%m-%d') = '$time'";
        $ret = $db_log->fetchOne($sql);
        $data['box_trading'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;
        //nft交易额
        $sql = "select sum(price) as sum_pr from card_market_logs where DATE_FORMAT(gettime,'%Y-%m-%d') = '$time'";
        $ret = $db_log->fetchOne($sql);
        $data['nft_trading'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;
        //box销售
        $sql = "select sum(price) as sum_pr from box_panic_buy where DATE_FORMAT(gettime,'%Y-%m-%d') = '$time'";
        $ret = $db_log->fetchOne($sql);
        $data['box_sales'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;

        $arr = AdminRetained::findFirst(["times = '$time'"]);
        if ($arr){
            $arr->news = $data['news'];
            $arr->days1 = $data['days1'];
            $arr->days3 = $data['days3'];
            $arr->days7 = $data['days7'];
            $arr->daynews = $data['daynews'];
            $arr->box_trading = $data['box_trading'];
            $arr->nft_trading = $data['nft_trading'];
            $arr->box_sales = $data['box_sales'];
            $arr->save();
        }else{
            $ret = new AdminRetained();
            $ret->times = $data['times'];
            $ret->news = $data['news'];
            $ret->days1 = $data['days1'];
            $ret->days3 = $data['days3'];
            $ret->days7 = $data['days7'];
            $ret->daynews = $data['daynews'];
            $arr->box_trading = $data['box_trading'];
            $arr->nft_trading = $data['nft_trading'];
            $arr->box_sales = $data['box_sales'];
            $ret->save();
        }
        return $data;
    }
}
