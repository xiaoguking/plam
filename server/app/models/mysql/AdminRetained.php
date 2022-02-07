<?php
namespace Model\Mysql;

class AdminRetained extends BasesModel
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
    public $times;

    /**
     *
     * @var int
     */
    public $news;

    /**
     *
     * @var string
     */
    public $days1;

    /**
     *
     * @var string
     */
    public $days3;

    /**
     *
     * @var string
     */
    public $days7;

    /**
     *
     * @var int
     */
    public $daynews;

    /**
     *
     * @var float
     */
    public $box_trading;


    /**
     *
     * @var float
     */
    public $box_sales;


    /**
     *
     * @var float
     */
    public $nft_trading;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("admin_retained");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_retained';
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
    public static function getList($page,$limit,$begin,$end) :array
    {
        $dy = self::getDy();
        $where = ' 1 = 1';
        if (!empty($begin)){
            $where .= " and times >= '$begin'";
        }
        if (!empty($end)){
            $where .= " and times <= '$end'";
        }
        if (empty($begin) && empty($end)){
            $time = date('Y-m-d',strtotime("-30 day"));
            $where .= " and times > $time";
        }
        $list = self::find([$where,"order" => 'times desc'])->toArray();
        if (!empty($list)){
            foreach ($list as &$value){
                if ($value['times'] == date('Y-m-d',time()))
                {
                    //活跃人数
                    $sql = "select count(*) as count from (select uid from login where DATE_FORMAT(gettime,'%Y-%m-%d') = '{$value['times']}' group by uid) as b";
                    $ret = $dy->fetchOne($sql);
                    $value['new'] = empty($ret['count']) ? 0 : (int)$ret['count'];
                    //新增人数
                    $sql = "select count(*) as count from register  where DATE_FORMAT(gettime,'%Y-%m-%d') = '{$value['times']}'";
                    $ret = $dy->fetchOne($sql);
                    $value['daynews'] = empty($ret['count']) ? 0 : (int)$ret['count'];
                    //box 交易额
                    $sql = "select sum(price) as sum_pr from box_market_logs where DATE_FORMAT(gettime,'%Y-%m-%d') = '{$value['times']}'";
                    $ret = $dy->fetchOne($sql);
                    $value['box_trading'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;
                    //nft交易额
                    $sql = "select sum(price) as sum_pr from card_market_logs where DATE_FORMAT(gettime,'%Y-%m-%d') = '{$value['times']}'";
                    $ret = $dy->fetchOne($sql);
                    $value['nft_trading'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;
                    //box销售
                    $sql = "select sum(price) as sum_pr from box_panic_buy where DATE_FORMAT(gettime,'%Y-%m-%d') = '{$value['times']}'";
                    $ret = $dy->fetchOne($sql);
                    $value['box_sales'] = !empty($ret['sum_pr']) ? round($ret['sum_pr'],4) : 0 ;
                }

            }
        }
        return  ['list' => $list];
    }
}
