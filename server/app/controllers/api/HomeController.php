<?php

namespace Controllers\Api;

use Library\Response;
use Model\Mysql\AdminRetained;
use Model\Mysql\Statistical;

class HomeController extends ControllerBase
{
    /**
     * 首页统计接口
     * @return void
     */
    public function statisticalAction()
    {
        $data = Statistical::getHomeStl();
        Response::Success($data);
    }

    /**
     * 用户留存统计查询接口
     * @return void
     */
    public function getRetainedAction()
    {
        $begin_time = $this->request->getQuery('0');
        $end_time = $this->request->getQuery('1');

        $data = AdminRetained::getList(0,0,$begin_time,$end_time);
        Response::Success($data);
    }

    /**
     * 在线人数统计
     * @return void
     */
    public function onlineIngAction()
    {
        $data = Statistical::getOnline();
        Response::Success($data);
    }

    /**
     * 交易额统计
     */
    public function turnoverAction(){
        $data = Statistical::getTurnover();
        Response::Success($data);
    }
}
