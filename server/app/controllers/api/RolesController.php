<?php

namespace Controllers\Api;
use Model\Mysql\Permissions;
use Model\Mysql\Roles;
use Library\Response;

class RolesController extends ControllerBase
{

    public function addAction()
    {
        $params = $this->request->getRawBody();
        $params = json_decode($params, true);

        if (empty($params['roles'])) {
            Response::Error('1', '角色不能为空');
        }
        if (empty($params['name'])) {
            Response::Error('1', '角色名称不能为空');
        }
        if (empty($params['routes'])) {
            Response::Error('1', '权限不能为空');
        }
        //验证角色 合法性
        if(!preg_match('/^[A-Za-z0-9_]+$/',$params['roles'])){
            Response::Error('1', '角色不合法');
        }
        if (!Roles::add($params)) {
            Response::Error('1', '操作失败');
        }
        Response::Success();
    }

    public function updateAction()
    {
        $params = $this->request->getRawBody();
        $params = json_decode($params, true);
        if (empty($params['id'])) {
            Response::Error('1', 'id不能为空');
        }
        if (empty($params['data']['roles'])) {
            Response::Error('1', '角色不能为空');
        }
        if (empty($params['data']['name'])) {
            Response::Error('1', '角色名称不能为空');
        }
        if (empty($params['data']['routes'])) {
            Response::Error('1', '权限不能为空');
        }
        //验证角色 合法性
        if(!preg_match('/^[A-Za-z0-9_]+$/',$params['data']['roles'])){
            Response::Error('1', '角色不合法');
        }
        if (!Roles::edit($params['id'], $params['data'])) {
            Response::Error('1', '操作失败');
        }
        Response::Success();
    }

    /**
     * 获取所有的权限
     * @throws \Exception
     */
    public function getRouterAction()
    {
        $ret = Roles::get_router_list();
        if (!$ret) {
            Response::Error(1, '获取权限失败');
        }
        Response::Success($ret);
    }

    public function ListAction()
    {
        $page = (int)$this->request->getQuery('page', 'int', 1);
        $limit = (int)$this->request->getQuery('limit', 'int', 10);
        $ret = Roles::get_list($page, $limit);
        Response::Success($ret);
    }

    public function RolesListAction()
    {
        $ret = Roles::find()->toArray();
        Response::Success($ret);
    }
    public function getTerrRoutersAction()
    {
        $ret = Roles::get_router_list(null,true);
        if (!$ret) {
            Response::Error(1, '获取权限失败');
        }
        Response::Success($ret);
    }

    public function deleteAction()
    {
        $id = $this->request->getQuery('id', 'int');
        Response::paramsValidation([
            'id' => 'id'
        ]);
        $user = Roles::findFirst($id);
        if (!$user) {
            Response::Error(1, 'params error');
        }
        $user->delete();
        //删除角色权限
        $obj = Permissions::find(["role_id  = $id"]);
        $obj->delete();
        Response::Success();
    }
}
