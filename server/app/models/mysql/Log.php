<?php

namespace Model\Mysql;

class Log extends BasesModel
{
    public $id;

    public $name;

    public $status;

    public $time;

    public $ip;

    public $uid;

    public $api;

    public $info;

    public $client;

    public $request;
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'admin_log';
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

    public static function add($name, $url = '',$uid = null)
    {
        if (!$uid){
            $uid = \Strings::getAdminUid();
            if (empty($uid)){
                $uid = 0;
            }
        }

        $ip = \Strings::get_client_ip();
        $header = \Strings::getHeader();
        
        $request = $_REQUEST;
        unset($request['_url']);
        $agent = $header['User-Agent'];
        $client = '';
        if (!empty($agent)){
            $agent = \Strings::getClientOS($agent);
            $client = $agent['os'].' '.$agent['os_ver'];
        }
        //记录请求日志
        $obj = new self();
        $obj->name = $name;
        $obj->time = date('Y-m-d H:i:s', time());
        $obj->api = $url;
        $obj->uid = $uid;
        $obj->ip = $ip;
        $obj->client = $client;
        $obj->status = 0;
        $obj->info = json_encode($request);
        $obj->request = json_encode($header);
        $obj->save();
    }

    public static function getList($page,$limit,$time,$uid,$keyword){
        $where = "1 = 1";
        $offset = ($page-1)*$limit;
        if (!empty($time)){
            $time = strtotime($time);
            $time = date("Y-m-d",$time);
            $beg = $time." 00:00:00";
            $end = $time." 23:59:59";
            $where .= " and time between '$beg' and '$end'";
        }
        if (!empty($uid)){
            $where .= " and uid = $uid";
        }
        if (!empty($keyword)){
            $where .= " and name like '%$keyword%'";
        }
        $data = self::find([$where,"order" => "time desc","limit"=>$limit,'offset'=>$offset])->toArray();
        //重构数据
        foreach ($data as &$v) {
            if ($v['uid'] == 0){
                $v['user'] = '未知';
            }else{
                $v['user'] = User::getName($v['uid']);
            }

        }
        $total = self::count($where);

        return ['list' => $data, 'total' => $total];
    }
}
