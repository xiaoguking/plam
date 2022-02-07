<?php

use Phalcon\Dispatcher as DispatcherAlias;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Library\Response;

class ExceptionPlugin extends Plugin
{
    public function beforeException(Event $event, Dispatcher $dispatcher,Exception $exception){
        $code = $exception->getCode();
        $msg = $exception->getMessage();
            switch ($code){
                case DispatcherAlias::EXCEPTION_HANDLER_NOT_FOUND:
                case DispatcherAlias::EXCEPTION_ACTION_NOT_FOUND:
                default;
                     $this->exception($code,$msg);
                     break;
            }

    }
    public function exception($code,$msg){

        $namespace = ltrim($this->dispatcher->getNamespaceName(), 'Controllers\\');
        if (strtolower($namespace) != 'Api'){
            if (!$this->request->isAjax()){
                $this->dispatcher->forward([
                    'namespace' =>  '',
                    'controller' => 'index',
                    'action'     => 'info',
                    'params'    =>array('title' => '出错了！','message' => $msg)
                ]);
            }else{
                Response::Error($code,$msg);
            }
        }
        Response::Error($code,$msg);
    }
}
