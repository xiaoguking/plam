<?php
namespace Model\Mysql;

class AdminCronLog extends BasesModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $code;

    /**
     *
     * @var string
     */
    public $msg;

    /**
     *
     * @var string
     */
    public $time;

    /**
     *
     * @var integer
     */
    public $cron_id;

    /**
     *
     * @var integer
     */
    public $time_len;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("admin_cron_log");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_cron_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AdminCronLog[]|AdminCronLog|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AdminCronLog|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function getList($page,$limit,$id){
        if (empty($id)){
            return [];
        }
        $offset = ($page-1)* $limit;
        $list = self::find(["cron_id = $id",'order' => 'time desc','limit' => $limit,'offset' => $offset])->toArray();
        $count = self::count(["cron_id = $id"]);
        return  ['list' => $list,'total' => $count];
    }
}
