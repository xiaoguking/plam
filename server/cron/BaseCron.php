<?php
namespace Cron;

class BaseCron
{
    public static function msg($code = 0 ,$msg = '' ){
       return [
           'code' => $code,
           'msg'  => $msg
       ];
    }
}
