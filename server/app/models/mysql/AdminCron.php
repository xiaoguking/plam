<?php
namespace Model\Mysql;
use Library\Cache;
use Library\Redis;

class AdminCron extends BasesModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $class;

    /**
     *
     * @var string
     */
    public $function;

    /**
     *
     * @var string
     */
    public $begin_time;

    /**
     *
     * @var string
     */
    public $end_time;

    /**
     *
     * @var string
     */
    public $next_time;

    /**
     *
     * @var string
     */
    public $cron;

    /**
     *
     * @var string
     */
    public $create_time;

    /**
     *
     * @var string
     */
    public $update_time;

    /**
     *
     * @var int
     */
    public $status;


    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("admin_cron");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_cron';
    }

    public function beforeCreate()
    {
        // Set the creation date
        $this->create_time = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        // Set the modification date
        $this->update_time = date('Y-m-d H:i:s');
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AdminCron[]|AdminCron|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AdminCron|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * 获取定时器任务列表
     * @param $page
     * @param $limit
     * @param $name
     * @return array
     * author shaoyi.sun
     * date 2021/10/20 15:18
     */
    public static function getList($page,$limit,$name): array
    {

        $offset = ($page-1)*$limit;
        $where = '1 = 1';
        if (!empty($name)){
            $where .= " and name like '%$name%'";
        }
        $list = self::find([$where,'order' => 'create_time desc','limit' => $limit,'offset' => $offset])->toArray();
        $count = self::count([$where]);

        return ['total' => $count,'list' => $list];
    }

    /**
     * 添加、编辑 定时器
     * @param $params
     * @return bool
     * author shaoyi.sun
     * date 2021/10/20 15:42
     */
    public static function add($params) : bool
    {
        if (empty($params)){
            return false;
        }
        if (empty($params['id'])){
            $obj = new self();
            $obj->name = $params['name'];
            $obj->class = $params['class'];
            $obj->function = $params['function'];
            $obj->cron = $params['cron'];
            $obj->begin_time = empty($params['begin_time']) ? null : $params['begin_time'];
            $obj->create_time = date('Y-m-d H:i:s');
            $obj->status = 1;
            return $obj->save();
        }
        $cron = self::findFirst([$params['id']]);
        if (!$cron){
            return false;
        }
        $cron->name = $params['name'];
        $cron->class = $params['class'];
        $cron->function = $params['function'];
        $cron->cron = $params['cron'];
        $cron->update_time = date('Y-m-d H:i:s');
        if (!empty($params['begin_time'])){
            $cron->begin_time = $params['begin_time'];
        }
        return $cron->save();
    }

    /**
     * 删除定时器
     * @param $id
     * @return bool
     * author shaoyi.sun
     * date 2021/10/20 15:59
     */
    public static function del($id) : bool
    {
        if (empty($id)){
            return false;
        }
        $cron = self::findFirst($id);
        if (!$cron){
            return  false;
        }
        $cron_log = AdminCronLog::find(["cron_id = $id"]);
        if ($cron_log){
            $cron_log->delete();
        }
        return $cron->delete();
    }

    /**
     * 修改定时器状态
     * @param $id
     * @param $status
     * @return bool
     * author shaoyi.sun
     * date 2021/10/20 16:19
     */
    public static function updateStatus($id,$status) : bool
    {
        if (empty($id)){
            return false;
        }
        $cron = self::findFirst($id);
        if (!$cron){
            return  false;
        }
        $cron->status = $status;
        return $cron->save();
    }

    public static function getCron(){
        $key = "cron_list";
        $cron_cache = Cache::get($key);
        if ($cron_cache){
            return json_decode($cron_cache,true);
        }
        $where = "status = 0";
        $cron = \Model\Mysql\AdminCron::find([$where])->toArray();
        Cache::save($key,json_encode($cron));
        return $cron;

    }
}

