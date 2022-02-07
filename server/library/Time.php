<?php
namespace Library;
/**
 * 时间戳类库
 * @author
 */
class Time {

    /**
     * 时间段处理函数
     * 1、单一时间直接strtotime返回
     * 2、时间段以数组形式返回：索引为start、end，值为时间戳
     * 3、解析失败或者timeStr为空时返回false
     * 4、不取限制的时候返回array()
     * @param type $timeStr
     * @return mixed
     */
    public static function getTimeRange($timeStr = 'today') {
            $timeStr = (string)$timeStr;

            $date = date('Y-m-d');
            $w = date('w');  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
            $weekstart_timestamp = strtotime($date.' -'.($w ? $w - 1 : 6).' days'); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
            $weekstart_date = date('Y-m-d', $weekstart_timestamp);
            $weekend_timestamp = strtotime("{$weekstart_date} +6 days");  //本周结束日期
            switch ($timeStr) {
                case 'week': //从今天开始7天，含今天
                    $res = array(
                        'start' => strtotime($date),
                        'end' => strtotime("{$date} +6 days")
                    );
                    break;
                case 'weekend'://当前自然周的周末，这里不能使用Sunday this week这种写法。实测得知，如果今天是周日，则此参数返回的将是下周一，bug？
                    $res = array(
                        'start' => strtotime("{$weekstart_date} +5 days"),
                        'end' => $weekend_timestamp
                    );
                    break;
                case 'all':
                    $res = array();
                    break;
                case '':
                    $res = false;
                    break;
                default:
                    $res = strtotime($timeStr);
                    break;

            }
            return $res;
    }

    /**
     * 获取 N年，N月，N天，N小时，N分钟前 这种字符串
     * @param  [type] $time 时间戳
     * @return [type]       [description]
     */
    public static function  getReadable($time)
    {
        $j = time() - $time;
        if ($j < 0)
        {
            return false;
        }
        else
        {
            $hourTime = 3600;
            $dayTime = $hourTime * 24;
            $mouthTime = 30 * $dayTime;
            $yearTime = 365 * $dayTime;
            $it = array(
                $yearTime => '年',
                $mouthTime => '月',
                $dayTime => '天',
                $hourTime => '小时',
                60 => '分钟',
                1 => '秒'
            );
            foreach ($it as $item => $value) {
                $v = floor($j / $item);
                if ($v > 0)
                {
                    $result = $v . $value . '前';
                    break;
                }
            }
            return $result;
        }
    }

    /**
     * 获取 时间戳的小时：分钟格式，分多重情况
     * @param $time
     */
    public static function getTimeDay($time){
        if (date('Y-m-d H:i:s',$time) >= date('Y-m-d 00:00:00') && date('Y-m-d H:i:s',$time) <= date('Y-m-d 23:59:59')) {
            $newtime = date('H:i', $time);
        } elseif (date('Y-m-d H:i:s',$time) >= date('Y-m-d 00:00:00', strtotime('-1 day')) && date('Y-m-d H:i:s',$time) <= date('Y-m-d 23:59:59', strtotime('-1 day'))) {
            $newtime = '昨天' . date('H:i', $time);
        } elseif (date('Y-m-d', $time) <= date('Y-m-d', strtotime('-2 day')) && date('Y', $time) > date('Y', strtotime('last year'))) {
            $newtime = date('m-d H:i', $time);
        } else {
            $newtime = date('Y-m-d H:i', $time);
        }
        return $newtime;
    }

    //不显示年份（本年内）
    public static function getTimeOutYear($time){
        if (date('Y', $time) == date('Y', time())) {
            $newtime = date('m月d日', $time);
        } else {
            $newtime = date('Y年m月d日', $time);
        }
        return $newtime;
    }
}
?>
