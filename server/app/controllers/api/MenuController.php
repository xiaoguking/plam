<?php
namespace Controllers\Api;

use Model\Mysql\Menu;
use Library\Response;

class MenuController extends ControllerBase
{
    public function listAction()
    {
        $keyword = $this->request->getQuery('keyword', 'string');
        $data = Menu::get_list($keyword);
        Response::Success($data);
    }

    public function addAction()
    {
        $params['component'] = $this->request->getPost('component', 'string');
        $params['title'] = trim($this->request->getPost('title', 'string'));
        $params['redirect'] = $this->request->getPost('redirect', 'string','');
        $params['name'] = $this->request->getPost('name', 'string','');
        $params['icon'] = $this->request->getPost('icon', 'string');
        $params['pid'] = $this->request->getPost('pid', 'int',0);
        $params['path'] = $this->request->getPost('path', 'string');
        $params['status'] = $this->request->getPost('status','int',0);
        $params['alwaysShow'] = $this->request->getPost('alwaysShow','int',0);
        $params['hidden'] = $this->request->getPost('hidden','int',0);
        $params['order'] = $this->request->getPost('order','int',1);
        Response::paramsValidation([
            'component' => '组件',
            'title' => '名称',
            'path' => '路由'
        ]);
        $ret = Menu::add($params);
        if (!$ret) {
            Response::Error();
        }
        Response::Success();
    }

    public function editAction()
    {
        $params['id'] = $this->request->getPost('id', 'string');
        $params['component'] = $this->request->getPost('component', 'string');
        $params['title'] = trim($this->request->getPost('title', 'string'));
        $params['redirect'] = $this->request->getPost('redirect', 'string');
        $params['name'] = $this->request->getPost('name', 'string');
        $params['icon'] = $this->request->getPost('icon', 'string');
        $params['pid'] = $this->request->getPost('pid', 'string');
        $params['path'] = $this->request->getPost('path', 'string');
        $params['status'] = $this->request->getPost('status','int',0);
        $params['alwaysShow'] = $this->request->getPost('alwaysShow','int',0);
        $params['hidden'] = $this->request->getPost('hidden','int',0);
        $params['order'] = $this->request->getPost('order','int',1);
        Response::paramsValidation([
            'id' => 'id',
            'component' => '组件',
            'title' => '名称',
            'path' => '路由'
        ]);
        $ret = Menu::edit($params['id'],$params);
        if (!$ret) {
            Response::Error();
        }
        Response::Success();
    }

    public function deleteAction()
    {
        $id = $this->request->getQuery('_id', 'string');

        Response::paramsValidation([
            '_id' => 'id',
        ]);
        $ret = Menu::del($id);
        if (!$ret) {
            Response::Error('1', '操作失败');
        }
        Response::Success();
    }
}
