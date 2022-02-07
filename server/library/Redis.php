<?php
namespace Library;

class Redis
{
    /**
     * @var $redis \Redis
     */
   protected static $redis = null;

   public  function __construct(){
       if (!self::$redis){
           self::$redis = Ico::_get('redis');
       }
   }
   public function set($key,$val,$ex = -1)
   {
       return self::$redis->setex($key,$val,$ex);
   }

   public function get($key)
   {
       if (!is_string($key)) return false;

       return self::$redis->get($key);
   }

   public function exists($key){

       if (!is_string($key)) return false;

       return self::$redis->exists($key);
   }
}
