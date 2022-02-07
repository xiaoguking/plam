<?php
/**
 * CommeFun
 * @author  Xiaogu
 * @copyright  2021/2/6
 * @Time: 16:19
 */

class CommonFun
{
    /**
     * 时间转换
     * @param $seconds
     * @return string
     * @author xiaogu
     * @copyright  2021/2/6
     * @Time: 16:20
     */
    public static function time($seconds){

        $hour = floor($seconds/3600);
        $minute = floor(($seconds-3600 * $hour)/60);
        $second = floor((($seconds-3600 * $hour) - 60 * $minute) % 60);
        if ($hour == 0){
            $result = $minute.'.'.$second;
        }else{
            $result = $hour.'.'.$minute.'.'.$second;
        }

        return $result;
    }

    /**
     * 截取两个字符串中的部分
     * @param $str  字符串
     * @param $sign  条件字符串
     * @param $number  位置
     * @return bool
     */
    public static function cut_str($str,$sign,$number){
        $array=explode($sign, $str);
        $length=count($array);
        if($number<0){
            $new_array=array_reverse($array);
            $abs_number=abs($number);
            if($abs_number>$length){
                return false;
            }else{
                return $new_array[$abs_number-1];
            }
        }else{
            if($number>=$length){
                return false;
            }else{
                return $array[$number];
            }
        }
    }
}