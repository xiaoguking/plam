<?php

namespace Controllers\Api;

use Library\Cache;
use Model\Mysql\System;
use Library\Response;

class SystemController extends ControllerBase
{
    public function adminSaveAction()
    {
        $params['title'] = $this->request->getPost('title');
        $params['login_background'] = $this->request->getPost('login_background');
        $params['logo'] = $this->request->getPost('logo');
        $params['login_ex_time'] = $this->request->getPost('login_ex_time');
        $params['password'] = $this->request->getPost('password');
        $ret = System::add('admin',json_encode($params),'system');
        if (!$ret){
            Response::Error(1,'update system error');
        }
        Response::Success();
    }

    public function getAdminAction()
    {
        $res = System::get('system','admin');
        Response::Success($res);
    }
}
