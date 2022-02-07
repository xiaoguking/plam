<?php

namespace Model\Mysql;

class Menu extends BasesModel
{
    public $id;

    public $name;

    public $pid;

    public $uid;

    public $path;

    public $component;

    public $redirect;

    public $status;

    public $alwaysShow;

    public $hidden;

    public $meta;

    public $create_time;

    public $update_time;

    public $order;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_menu';
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
     * 列表数据
     * @param $keyword
     * @return array
     */
    public static function get_list($keyword)
    {
        $where = " 1 = 1";
        if (!empty($keyword)) {
            $where .= " and name like '%$keyword%'";
        }
        $data = self::find([$where,"order" => "[order] asc"])->toArray();

        foreach ($data as &$v) {
            if (isset($v['meta'])) {
                $meta = json_decode($v['meta'],true);
                $v['title'] = $meta['title'];
                $v['icon'] = $meta['icon'];
            }
            unset($v['meta']);
        }
        $data = Roles::generateTree($data);
        return ['list' => $data];
    }

    public static function add($params)
    {
        if (empty($params)){
            return false;
        }
        $obj = new self();
        $obj->pid = $params['pid'];
        $obj->path = $params['path'];
        $obj->component = $params['component'];
        $obj->redirect = $params['redirect'];
        $obj->name = $params['title'];
        $obj->status = intval($params['status']);
        $obj->create_time = date('Y-m-d H:i:s', time());
        $obj->update_time = date('Y-m-d H:i:s', time());
        $obj->alwaysShow = intval($params['alwaysShow']);
        $obj->hidden = intval($params['hidden']);
        $obj->order = intval($params['order']);
        $obj->meta = json_encode([
            'title' => $params['title'],
            'icon' => $params['icon']
        ]);
        $ret = $obj->save();
        if ($ret) {
            return true;
        }
        return false;
    }

    public static function edit($id,$params)
    {
        if (empty($params) || empty($id)){
            return false;
        }

        $obj = self::findFirst($id);
        if (!$obj){
            return  false;
        }
        $obj->pid = $params['pid'];
        $obj->path = $params['path'];
        $obj->component = $params['component'];
        $obj->redirect = $params['redirect'];
        $obj->name = $params['title'];
        $obj->status = intval($params['status']);
        $obj->create_time = date('Y-m-d H:i:s', time());
        $obj->update_time = date('Y-m-d H:i:s', time());
        $obj->alwaysShow = intval($params['alwaysShow']);
        $obj->hidden = intval($params['hidden']);
        $obj->order = intval($params['order']);
        $obj->meta = json_encode([
            'title' => $params['title'],
            'icon' => $params['icon']
        ]);
        $ret = $obj->save();
        if ($ret) {
            return true;
        }
        return false;
    }

    public static function del($id) :bool
    {
        if (empty($id)) {
            return false;
        }
        $menu = self::findFirst($id);
        //查询是否存在子菜单
        $child = self::findFirst(["pid = ". $id]);
        if ($child) {
            throw new \Exception('当前菜单存在子菜单不允许删除', 1);
        }
        $ret = $menu->delete();
        if (!$ret) {
            return false;
        }
        return true;
    }
}
