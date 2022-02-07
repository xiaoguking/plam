<?php
namespace Model\Mysql;

use Library\Cache;
use Library\Jwt;


class User extends BasesModel
{
    /**
     * @var int
     */
    public $id;

    public $username;

    public $name;

    public $password;

    public $phone;

    public $email;

    public $create_time;

    public $update_time;

    public $roles;

    public $avatar;

    public $token;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return XgContent[]|XgContent|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return XgContent|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * 管理员列表
     * @param $keyword
     * @param $page
     * @param $limit
     * @param $sort
     * @return array
     */
    public static function get_list($keyword, $page, $limit)
    {
        $offset = ($page -1)*$limit;
        $where = " 1 = 1";
        if (!empty($keyword)) {
            $where  .= " and name like '%$keyword%'";
        }
        $data = self::find([$where,"columns"=>"id,name,roles,phone,email,username,create_time","limit"=>$limit,'offset'=>$offset])->toArray();
        foreach ($data as &$v) {
            $v['roles_id'] = $v['roles'];
            $roles = Roles::findFirst($v['roles']);
            $v['roles_name'] = $roles->name;
        }
        $total = self::count($where);

        return ['list' => $data, 'total' => $total];
    }

    public static function add($params) :bool
    {
        if (empty($params)) {
            return false;
        }
        if (empty($params['id'])){
            $name = self::findFirst(["username = '{$params['username']}'"]);
            if (!empty($name)) {
                throw new \Exception('账号已存在', 1);
            }
            $system = System::get('system','admin');
            $password = !empty($system['password']) ? $system['password'] : "123456";
            $obj = new self();
            $obj->username = $params['username'];
            $obj->roles = $params['roles_id'];
            $obj->name = $params['name'];
            $obj->password = md5($password);
            $obj->email = $params['email'];
            $obj->phone = $params['phone'];
            $obj->avatar =  'https://gmupload.era7.io/images/2021/12/15/bb9858454cdc14c9db39f85d6f14d6b7.png';
            $obj->create_time = date('Y-m-d H:i:s', time());
            $obj->update_time = date('Y-m-d H:i:s', time());
            $obj->token = '';
            return $obj->save();
        }
        /** @var  $user User */
        $user = self::findFirst(["id = {$params['id']}"]);
        if (!$user){
            throw new \Exception('当前数据不存在', 1);
        }
        $user->roles = $params['roles_id'];
        $user->phone = $params['phone'];
        $user->email = $params['email'];
        $user->name = $params['name'];
        $user->update_time =  date('Y-m-d H:i:s', time());
        return $user->save();
    }

    /**
     * 登录
     * @param $username string 用户名
     * @param $pwd string 密码
     * @authou shaoyisun
     * @date 2021/7/16
     */
    public static function userLogin(string $username, string $pwd)
    {
        if (empty($username)) {
            return false;
        }
        if (empty($pwd)) {
            return false;
        }
        /** @var  $user User */
        $user = self::findFirst(["username = '$username'"]);
        if (!$user) {
            throw new \Exception('账号不存在', 1);
        }
        if ($user->password != md5($pwd)) {
            throw new \Exception('密码错误', 1);
        }
        $uid = $user->id;
        Jwt::$key = JWT_KEY;
        $token = Jwt::getToken(['uid' => $uid]);
        if (!is_string($token)) {
            throw new \Exception('token获取失败', 1);
        }
        $user->token = $token;
        $user_token_up = $user->save();
        if (!$user_token_up) {
            throw new \Exception('token更新失败', 1);
        }
        //将用户的信息存在redis中
        $user = $user->toArray();
        unset($user['password']);
        $log_ex = (int)System::getSystemKey("login_ex_time") > 0 ? System::getSystemKey("login_ex_time") :  7*24*3600;
        Cache::save($token, $user,$log_ex);
        return $token;
    }

    /**
     * 验证token用户
     * @param $uid string 用户id
     * @param $token  string token
     * @authou shaoyisun
     * @date 2021/7/16
     */
    public static function vdt_user_token($token)
    {
        if (!$token) {
            return false;
        }
        $ret = Cache::get($token);
        if (!$ret || !is_array($ret))
        {
            return false;
        }
        $user = self::findFirst(["id = '{$ret['id']}'"]);
        if (!$user) {
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     * @param $token string
     */
    public static function getInfo($token)
    {
        $user = Cache::get($token);
        if (empty($user)) {
            throw new \Exception('请重新登录', 201);
        }
        if (empty($user['roles'])) {
            throw new \Exception('当前账号无任何角色,请联系管理', 1);
        }
        return $user;
    }

    /**
     * 根据token获取用户id
     * @param $token
     * @authou shaoyisun
     * @date 2021/7/22
     */
    public static function getUid($token)
    {
        if (empty($token)) {
            return false;
        }
        $user = Jwt::verifyToken($token);
        return $user['uid'];
    }

    /**
     * 根据token获取用户角色
     * @param $token
     * @authou shaoyisun
     * @date 2021/7/22
     * @return bool
     */
    public static function getUserRoles($token)
    {
        if (empty($token)) {
            return false;
        }
        $user = Cache::get($token);
        if (!$user) {
            return false;
        }
        return $user['roles'][0];
    }

    /**
     * 删除
     * @param $id
     * @return bool
     */
    public static function del($id)
    {
        if (empty($id)) {
            return false;
        }
        $obj = self::findFirst($id);
        $ret = $obj->delete();
        if (!$ret) {
            return false;
        }
        return true;
    }
    public static function updateAvatar($uid , $img): bool
    {
        $obj =  self::findFirst(["id =".intval($uid)]);
        if (!$obj){
            return false;
        }
        $obj->avatar = $img;
        $ret = $obj->save();
        return $ret;
    }
    public static function getName($uid) : string
    {

        $nickname = self::findFirst(["id = $uid",'columns'=> 'name',]);
        return $nickname->name;

    }
    public static function getAvatar($uid) : string
    {
        $avatar =  self::findFirst(["id = $uid",'columns'=> 'avatar',]);
        return $avatar->avatar;
    }
    public static function updateInfo($uid,$data) :bool
    {
        if (empty($uid) || empty($data)){
            return false;
        }
        /** @var  $obj User*/
        $obj = self::findFirst($uid);
        if (!$obj){
            return  false;
        }
        $obj->phone = $data['phone'];
        $obj->name = $data['name'];
        $obj->email = $data['email'];
        return $obj->save();
    }
}
