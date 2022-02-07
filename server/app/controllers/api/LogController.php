<?php

namespace Controllers\Api;

use Model\Mysql\Log;
use Library\Response;
class LogController extends ControllerBase
{
    public function listAction(){
        $limit = $this->request->getQuery('limit');
        $page = $this->request->getQuery('page');
        $time = $this->request->getQuery("time");
        $uid = $this->request->getQuery("uid");
        $keyword = $this->request->getQuery("keyword");
        $res = Log::getList($page,$limit,$time,$uid,$keyword);
        Response::Success($res);
    }
    public function deleteAction(){
        $id = $this->request->getQuery('id');
        Response::paramsValidation([
            'id' => 'id'
        ]);
        $obj = Log::findFirst($id);
        if (!$obj){
            Response::Error(1,'params error');
        }
        $res = $obj->delete();
        if (!$res){
            Response::Error(1,'db error');
        }
        Response::Success();

    }
}
