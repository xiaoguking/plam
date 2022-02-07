<?php
namespace Library;

/**
 * 缓存类
 * Cache
 * @authou shaoyisun
 * @date 2021/7/29
 */
class Cache
{
    /**
     * 设置缓存
     * @param $ket  string 缓存key
     * @param $val  string values
     * @param float|int $expire int 过期时间（单位 秒）
     * @authou shaoyisun
     * @date 2021/7/29
     * @return bool
     */
   public static function save($ket, $val, $expire = 10*60)
   {
       $cache = \Phalcon\Di::getDefault()->get('cache');

       if (!$cache)
       {
           return false;
       }
       if (empty($ket))
       {
           return false;
       }
       if (empty($val))
       {
           return false;
       }

       $ret = $cache->save($ket,$val,$expire);
       if (!$ret)
       {
           return false;
       }
       return true;
   }

    /**
     * 获取缓存
     * @param $key string 缓存key
     * @authou shaoyisun
     * @date 2021/7/29
     * @return
     */
    public static function get($key)
    {
        $cache = \Phalcon\Di::getDefault()->get('cache');

        if (!$cache)
        {
            return false;
        }
        if (empty($key))
        {
            return false;
        }
        $ret = $cache->get($key);

        if (!$ret)
        {
            return false;
        }
        return  $ret;
    }

    /**
     * 获取缓存
     * @param $key string 缓存key
     * @authou shaoyisun
     * @date 2021/7/29
     * @return
     */
    public static function delete($key)
    {
        $cache = \Phalcon\Di::getDefault()->get('cache');

        if (!$cache)
        {
            return false;
        }
        if (empty($key))
        {
            return false;
        }
        $ret = $cache->delete($key);

        if (!$ret)
        {
            return false;
        }
        return  $ret;
    }
    public static function flush(){
        $cache = \Phalcon\Di::getDefault()->get('cache');
        return $cache->flush();
    }
}
