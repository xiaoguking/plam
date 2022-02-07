<?php


namespace Controllers\Api;

use Library\Ico;
use Library\Redis;
use Library\Response;
use Library\Cache;

class CacheController extends ControllerBase
{
   public function getCacheAction(){
       $key = $this->request->getQuery('key');
       Response::paramsValidation([
           'key' => 'key'
       ]);
       $val = Cache::get($key);
       Response::Success($val);
   }
   public function deleteCacheAction(){
       Cache::flush();
       Response::Success();
   }
}
