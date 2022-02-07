<?php


use Library\Time;

class Notice extends MongoServer
{
    public function __initialize()
    {
        $this->setSource('notice');
    }

    public static function get_list($all = true){
        $uid = Strings::getUser()['_id'];
        $where = [
            'code' => 0,
            '$or' => [['uid' => ''],['uid' => $uid]],
        ];

        $filed = '*';
        if ($all == false)$filed = 'time,content';
        $ret = self::getList($where,$filed,['time'=> -1]);
        if (!empty($ret)){
            foreach ($ret as &$value){
                $time = Time::getReadable(strtotime($value['time']));
                $value['time'] = $time;
            }
        }
        return $ret;
    }
    public static function add($content,$uid = '')
    {
        if (empty($content)){
            return false;
        }
        $data = [
            'time' => date('Y-m-d H:i:s',time()),
            'content' => $content,
            'code' => 0,
            'error' => '',
            'data' => '',
            'uid' => $uid
        ];
        return self::insertOne($data);
    }
}
