<?php
namespace Model\Mysql;

use Library\Cache;

class System extends BasesModel
{
    public $id;

    public $model;

    public $key;

    public $value;

    public $create_time;

    public $update_time;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_system';
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
     * @param $key string 唯一标识符key
     * @param $value string 存储的数据
     * @param string $model  数据model
     * @param bool $update   是否更新
     * @return bool
     * @throws Exception
     */
    public static function add(string $key,string $value,string $model = 'system') :bool
    {
        $sql = "delete from  admin_system where model = '$model' and `key` = '$key'";
        $db =  \Phalcon\Di::getDefault()->get('dbMaster');
        $db->execute($sql);
        $obj = new self();
        $obj->model = $model;
        $obj->key = $key;
        $obj->value = $value;
        $obj->create_time =  date('Y-m-d H:i:s', time());
        $obj->update_time =  date('Y-m-d H:i:s', time());
        $obj->save();
        Cache::save($model.":".$key,$value,null);
        return true;

    }
    /**
     * @param $model
     * @param $key
     * @return array|mixed
     */
    public static function get($model,$key)
    {
        $cache = Cache::get($model.':'.$key);
        if (!empty($cache)){
            return json_decode($cache,true);
        }
        $res = self::findFirst(["key = '$key' and model = '$model'"]);
        $res = !$res ? array() : $res->toArray();
        $data = json_decode($res['value'],true);
        Cache::save($model.':'.$key,$res['value'],null);
        return $data;
    }
    public static function getSystemKey($key)
    {
        $res = self::get("system","admin");
        if (isset($res[$key])){
            return $res[$key];
        }
        return "";
    }
}
