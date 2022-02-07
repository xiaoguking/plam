<?php
namespace Library;
/**
 * @Notes: 控制反转和依赖注入
 * @Authou: ShaoYi.Sun
 * @Date: 2021/8/13
 * @package Library
 */
class Ico
{
   private static $class;

   private static $redis;
    /**
     * @Notes: 添加实例到容器
     * @param $name  string  容器名称
     * @param $closure  object 容器实例
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/13
     */
   public static function set(string $name,$closure)
   {
       static::$class[$name] = $closure;
   }

    /**
     * @Notes: 获取实例
     * @param $name string 容器名称
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/13
     * @return mixed
     * @throws \Exception
     */
   public static function _get(string $name)
   {

       if (static::exists($name))
       {
           $new = static::$class[$name];
           return $new();  //这里如果没有括号，返回得将是一个闭包类,不是真正的类实例
       }
       throw new \Exception('Class instance does not exist',2021);
   }

    /**
     * @Notes: 检测容器是否存在
     * @param $name string 容器名称
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/13
     * @return bool
     */
   protected static function exists(string $name) : bool
   {
       return array_key_exists($name,static::$class);
   }


   public static function getRedis()
   {
       if (!self::$redis){
           return new Redis();
       }
       return self::$redis;
   }
}
