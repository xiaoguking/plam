<?php


namespace Controllers\Api;

use Library\Cache;
use Model\Mysql\Log;
use Model\Mysql\Roles;
use Model\Mysql\User;
use Library\Response;

class UserController extends ControllerBase
{
    public function loginAction()
    {
        $username = $this->request->getPost('username', 'string');
        $password = $this->request->getPost('password', 'string');
        Response::paramsValidation([
            'username' => '用户名',
            'password' => '密码'
        ]);
        $ret = User::userLogin($username, $password);
        if (!$ret) {
            Response::Error(201, '登录失败');
        }
        Response::Success(['token' => $ret]);
    }

    public function infoAction()
    {
        $token = $this->request->getPost('token');
        $user = User::getInfo($token);
        Response::Success($user);
    }

    public function logoutAction()
    {
        $token = $this->token;
        $ret = Cache::delete($token);
        if (!$ret) {
            Response::Error(1, '注销失败');
        }
        Response::Success();
    }

    public function ListAction()
    {
        $keyword = $this->request->getQuery('keyword', 'string');
        $page = (int)$this->request->getQuery('page', 'int', 1);
        $limit = (int)$this->request->getQuery('limit', 'int', 10);
        $ret = User::get_list($keyword, $page, $limit);
        Response::Success($ret);
    }

    public function addAction()
    {
        $params['username'] = trim($this->request->getPost('username', 'string'));
        $params['name'] = trim($this->request->getPost('name', 'string'));
        $params['roles'] = $this->request->getPost('roles', 'string');  //角色id可以是多个
        $params['phone'] = (int)$this->request->getPost('phone', 'int');
        $params['email'] = trim($this->request->getPost('email', 'string'));
        $params['roles_id'] = $this->request->getPost('roles_id', 'int');
        $params['id'] = $this->request->getPost('id', 'int');
        Response::paramsValidation([
            'username' => '账号',
            'phone'    => '手机号',
            'email'    => '邮箱'
        ]);
        //电话手机验证
        $is_phone = \Strings::is_phone($params['phone']);
        if (!$is_phone ){
            Response::Error('1', '手机号不合法');
        }
        //
        $is_email = \Strings::is_mail($params['email']);
        if (!$is_email ){
            Response::Error('1', '邮箱不合法');
        }
        if (empty($params['roles_id'])){
            Response::Error('1', '请选择管理员角色');
        }

        $ret = User::add($params);
        if (!$ret) {
            Response::Error('1', '操作失败');
        }
        Response::Success();
    }

    /**
     * 获取当前用户可以操作的路由
     */
    public function getRouterAction()
    {
        $token = $this->token;

        $ret = Roles::get_router_list($token);
        if ($ret === false) {
            Response::Error(1, '获取权限失败');
        }
        Response::Success($ret);
    }

    /**
     * 更换头像
     * @throws \Exception
     */
    public function updateImgAction()
    {
        $file = $_FILES['file'];
        $imgUpload = new \ImageUpload($file, 3);
        $imgUpload->setUploadPath()->upload()->zoom(false);
        $img = $imgUpload->zoom_img_url;
        $uid = User::getUid($this->token);
        User::updateAvatar($uid, $img);
        Response::Success(['img' => $img]);
    }

    /**
     * 修改密码
     */
    public function updatePwdAction()
    {
        $password = $this->request->getQuery('pass', 'string');
        if (empty($password)) {
            Response::Error('密码不能为空');
        }
        $uid = User::getUid($this->token);
        $password = md5($password);
        $user = User::findFirst($uid);
        if (!$user) {
            Response::Error();
        }
        $user->password = $password;
        $ret = $user->save();
        if (!$ret) {
            Response::Error(1, 'password update error');
        }
        Response::Success();
    }

    public function updateInfoAction()
    {
        $params['name'] = $this->request->getPost('name', 'string');
        $params['phone'] = $this->request->getPost('phone', 'string');
        $params['email'] = $this->request->getPost('email', 'string');
        Response::paramsValidation([
            'name' => '名称',
            'phone' => '手机号',
            'email' => '邮箱'
        ]);
        $uid = User::getUid($this->token);
        $ret = User::updateInfo($uid, $params);
        if (!$ret) {
            Response::Error(1, 'password update error');
        }
        Response::Success();
    }

    public function deleteAction()
    {

        $id = $this->request->getQuery('id', 'int');
        Response::paramsValidation([
            'id' => 'uid'
        ]);
        $user = User::findFirst($id);
        if (!$user) {
            Response::Error(1, '账号不存在');
        }
        $user->delete();
        Response::Success();
    }
    public function getUserAllAction()
    {
        $list = User::find(['columns' => 'id,name'])->toArray();
        Response::Success(['list' => $list]);
    }
}
