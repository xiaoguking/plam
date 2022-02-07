<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

class ApiServerPlugin extends Plugin
{

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $namespace = ltrim($dispatcher->getNamespaceName(), 'Controllers\\');
        $controller = strtolower($dispatcher->getControllerName());
        $action = strtolower($dispatcher->getActionName());
        $header = Strings::getHeader();
        $request = $_REQUEST;
        //记录请求日志
        $data = [
            'request' => $request,
            'header' => $header
        ];
        Strings::log($data,true,'api');

        if (strpos(API_URL, $_SERVER['SERVER_NAME']) !== false && API == 1 ){
           if ( $namespace !== 'Api'){
               throw new Exception('Module error', '1');
           }
        }
        //记录操作日志
        $url = 'api/'.$controller.'/'.$action;

        if (array_key_exists($url,ADMIN_ROUTER)){
            \Model\Mysql\Log::add(ADMIN_ROUTER[$url],$url,'');
        }
        $token = $header['Token'];
        if ($namespace == 'Api'){
            if (($controller == 'user' && $action != 'login') || ($controller != 'user' && $controller != 'common') ){
                if (empty($token)){
                    throw new Exception('登录信息已过期！请重新登录', '201');
                }
                $user = \Model\Mysql\User::vdt_user_token($token);
                if (!$user)
                {
                    throw new Exception('身份验证失败！请重新登录', '201');
                }
            }
        }
    }
}
