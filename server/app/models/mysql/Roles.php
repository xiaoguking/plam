<?php
namespace Model\Mysql;

class Roles extends BasesModel
{
    public $id;

    public $name;

    public $roles;

    public $desc;

    public $create_time;

    public $update_time;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_roles';
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

    public static function get_router_list($token = null,$hid = false)
    {
        if ($token !== null) {
            $userRoles = User::getUserRoles($token);
            if (!$userRoles || empty($userRoles)) {
                return false;
            }
            //获取角色权限
            $roles = Roles::findFirst(["id = $userRoles"]);
            if ($roles->roles == 'admin') {
                $data = Menu::find(["status = 0",'order'=>"[order] asc"])->toArray();

                if (!empty($data)){
                    foreach ($data as &$vs){
                        $vs['meta'] = json_decode($vs['meta'],true);
                        $vs['alwaysShow'] = $vs['alwaysShow'] == 0 ? true : false;
                        $vs['hidden'] = $vs['hidden'] == 0 ? false : true;
                        if ($vs['pid'] == 0){
                            $vs['redirect'] =  'noRedirect';
                        }
                    }
                }
            } else {
                $permissions = Permissions::find(["role_id = '$userRoles'"])->toArray();
                if (empty($permissions)) {
                    return [];
                }
                $permissions_id = implode("','", array_column($permissions,'permissions'));
                $data = Menu::find(["id in ('$permissions_id') and status = 0 ",'order'=>'[order] asc'])->toArray();
                if (!empty($data)){
                    foreach ($data as &$v){
                        $v['meta'] = json_decode($v['meta'],true);
                        $v['alwaysShow'] = $v['alwaysShow'] == 0 ? true : false;
                        $v['hidden'] = $v['hidden'] == 0 ? false : true;
                        if ($v['pid'] == 0){
                           $v['redirect'] =  'noRedirect';
                        }

                    }
                }

            }

        } else {
            $data = Menu::find(["status = 0",'order'=>'[order] asc'])->toArray();
            if (!empty($data)){
                foreach ($data as &$vs){
                    $vs['meta'] = json_decode($vs['meta'],true);
                    $vs['alwaysShow'] = $vs['alwaysShow'] == 0 ? true : false;
                    $vs['hidden'] = $vs['hidden'] == 0 ? false : true;
                }
            }
        }
        if ($hid == true){
            foreach ($data as &$v)
            {
                $v['hidden'] = false;
            }
        }
        $data = self::generateTree($data);
        return $data;
    }

    public static function generateTree($data)
    {
        $items = array();
        foreach ($data as $v) {
            $items[$v['id']] = $v;
        }

        $tree = array();
        foreach ($items as $k => $item) {

            if (isset($items[$item['pid']])) {
                $items[$item['pid']]['children'][] = &$items[$k];
            } else {
                $tree[] = &$items[$k];
            }
        }
        return $tree;
    }
    public static function orderTree(&$data){
        foreach ($data as $k => &$v){
            if (isset($v['children']) && !empty($v['children'])){
                $v['children'] = \Strings::arraySort($v['children'],'order','SORT_ASC ');
                self::orderTree($v['children']);
            }
        }
    }
    public static function add($params)
    {
        if (empty($params)) {
            return false;
        }
        $ret = self::findFirst(["roles = '{$params['roles']}'"]);
        if ($ret){
            throw new \Exception("角色已经存在",1);
        }
        $permissions = self::dirRolesId($params['routes']);
        $obj = new self();
        $obj->roles =  $params['roles'];
        $obj->name =  $params['name'];
        $obj->desc = $params['desc'];
        $obj->create_time = date('Y-m-d H:i:s', time());
        $obj->update_time = date('Y-m-d H:i:s', time());
        $res = $obj->save();
        if (!$res){
            return  false;
        }
        $role_id = $obj->id;
        if (!empty($permissions)){
            $per = Permissions::find(["role_id = $role_id"]);
            $per->delete();
            foreach ($permissions as $v){
                 $pers = new Permissions();
                 $pers->role_id = $role_id;
                 $pers->permissions = $v;
                 $res = $pers->save();
            }
        }

        if (!$res) {
            return false;
        }
        return true;
    }

    public static function edit($id, $params) :bool
    {
        if (empty($id)) {
            return false;
        }
        if (empty($params)) {
            return false;
        }
        $ret = self::findFirst(["roles = '{$params['roles']}' and id != $id"]);
        if ($ret){
            throw new \Exception("角色已经存在",1);
        }
        $permissions = self::dirRolesId($params['routes']);
        $obj = self::findFirst($id);
        if (!$obj){
            return  false;
        }
        $obj->roles =  $params['roles'];
        $obj->name =  $params['name'];
        $obj->desc = $params['desc'];
        $obj->update_time = date('Y-m-d H:i:s', time());
        $res = $obj->save();
        if (!$res){
            return  false;
        }
        if (!empty($permissions)){
            $sql = "delete from  admin_permissions where role_id = $id";
            $db =  \Phalcon\Di::getDefault()->get('dbMaster');
            $db->execute($sql);
            foreach ($permissions as $v){
                $pers = new Permissions();
                $pers->role_id = $id;
                $pers->permissions = $v;
                $pers->save();
            }
        }
        return true;
    }

    public static function get_list($page, $limit) :array
    {
        $offset = ($page-1)* $limit;

        $data = self::find(['limit' => $limit,'offset' => $offset])->toArray();

        foreach ($data as &$val) {

            if ($val['roles'] == 'admin') {
                $menu = Menu::find()->toArray();
            } else {
                $per = Permissions::find(["role_id = '{$val['id']}'"])->toArray();
                if (empty($page)){
                    $menu = array();
                }else{
                    $per = implode("','", array_column($per,'permissions'));
                    $menu = Menu::find(["id in ('$per')"])->toArray();
                }
            }
            foreach ($menu as &$v)
            {
                $v['hidden'] = false;
            }
            $menu = self::generateTree($menu);
            $val['routes'] = $menu;
        }
        return $data;
    }

    /**
     * 递归获取权限id
     * @param array $roles
     * @authou shaoyisun
     * @date 2021/7/22
     * @return array
     */
    protected static function dirRolesId(array $roles) : array
    {
        if (empty($roles)) {
            return $roles;
        }
        static $id = [];
        foreach ($roles as $v) {
            if (!empty($v['id'])) $id[] = $v['id'];
            if (isset($v['children']) && !empty($v['children'])) self::dirRolesId($v['children']);
        }
        return $id;
    }

}
