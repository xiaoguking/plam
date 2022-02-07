<?php


namespace Cron;
use Model\Mysql\AdminCronLog;
use Model\Mysql\Log;

class ActionLog extends BaseCron
{
    /**
     * 清除超过30天的日志
     */
   public static function delete()
   {
       $time = date('Y-m-d',strtotime('-30 day')).' 00:00:00';
       //操作日志
       $action = Log::find(["time < '$time'"]);
       $action_count = Log::count(["time < '$time'"]);
       if ($action){
           $action->delete();
       }
       //cron 日志
       $cron = AdminCronLog::find(["time < '$time'"]);
       $cron_count = AdminCronLog::count(["time < '$time'"]);

       if ($cron){
           $cron->delete();
       }
       return self::msg(0,'删除操作日志'.$action_count.'条，删除cron日志'.$cron_count.'条');
   }
}
