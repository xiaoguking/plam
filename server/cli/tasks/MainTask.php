<?php
class MainTask extends \Phalcon\Cli\Task
{
    public static $next;

    public function mainAction()
    {
        register_shutdown_function('\MainTask::catchError');
        //查询所有需要执行的定时器
        $t = 0;
        $cron = \Model\Mysql\AdminCron::getCron();
        if (empty($cron)){
            return;
        }
        while (true){
            if ($t >= 60){
                return;
            }
            foreach ($cron as $value){
                $class = "\Cron\\".$value['class'];
                $function = "\Cron\\".$value['class'].'::'.$value['function'];
                if (!class_exists($class)){
                   continue;
                }
                if (!method_exists($class,$value['function'])){
                   continue;
                }
                if (empty($value['cron'])){
                    continue;
                }
                $beg_time = time();
                if ($value['begin_time'] > date('Y-m-d H:i:s',$beg_time)){
                    continue;
                }
                $res = self::parseCron($value['cron'],$beg_time);
                if (!$res){
                    continue;
                }
                $res = @$function();
                echo(date("Y-m-d H:i:s",time())." 执行>>>>>>".$value['name']."\n");
                $end_time = time();
                self::upCron($value['id'],$beg_time,$end_time,$res);
            }
            $t += 1;
            sleep(1);
        }
    }

    /**
     * 判断某个时间点是否在 cron 规则之内
     * @param $cron
     * @param $time
     * @return bool
     */
    public static function parseCron($cron, $time): bool
    {
        $cronArray = self::getCronArray($cron);

        $now = explode(' ', date('s i G j n w', $time));
        foreach ($now as $key => $piece) {
            if (!in_array($piece, $cronArray[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $cron
     * @return array
     */
    public static function getCronArray($cron): array
    {
        $cronArray = explode(' ', $cron);

        $timeArray = [];

        $dimensions = [
            [0, 59], //seconds
            [0, 59], //Minutes
            [0, 23], //Hours
            [1, 31], //Days
            [1, 12], //Months
            [0, 6]  //Weekdays
        ];

        foreach ($cronArray as $key => $item) {
            list($repeat, $every) = explode('/', $item, 2) + [false, 1];
            if ($repeat === '*') {
                $timeArray[$key] = range($dimensions[$key][0], $dimensions[$key][1]);
            } else {
                // 处理逗号拼接的命令
                $tmpRaw = explode(',', $item);
                foreach ($tmpRaw as $tmp) {
                    // 处理10-20这样范围的命令
                    $tmp = explode('-', $tmp, 2);
                    if (count($tmp) == 2) {
                        $timeArray[$key] = array_merge($timeArray[$key], range($tmp[0], $tmp[1]));
                    } else {
                        $timeArray[$key][] = $tmp[0];
                    }
                }
            }
            // 判断*/10 这种类型的
            if ($every > 1) {
                foreach ($timeArray[$key] as $k => $v) {
                    if ($v % $every != 0) {
                        unset($timeArray[$key][$k]);
                    }
                }
            }
        }
        return $timeArray;
    }

    private static function upCron($id,$beg_time,$end_time,$res){
        $cron = \Model\Mysql\AdminCron::findFirst(["id =$id"]);
        $cron->next_time = date('Y-m-d H:i:s',$beg_time);;
        $cron->save();
        $cron_log = new \Model\Mysql\AdminCronLog();
        $cron_log->time = date('Y-m-d H:i:s',$beg_time);
        $cron_log->cron_id = $id;
        $cron_log->code = $res['code'];
        $cron_log->msg  = $res['msg'];
        $cron_log->time_len = $end_time-$beg_time;
        $cron_log->save();
    }
    public static function catchError()
    {
        if ($error = error_get_last()) {
            echo "<pre>";
            print_r($error);
            echo "</pre>";die;
       }
    }
}
