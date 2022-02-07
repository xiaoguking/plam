<?php


use MongoDB\BSON\ObjectId;

class MongoServer
{
    protected static $collection;

    protected static $db ;

    private static $_insert_id = 1;

    private static $m;

    public function __construct()
    {
        $config = json_decode(json_encode($GLOBALS['config']['mongodb']),true);
        static::$db = $config['db'];
        self::$m  = Phalcon\Di::getDefault()->get('mongo');
        if (method_exists($this, '__initialize'))
        {
            $this->__initialize();
        }
        if (empty(static::$collection))
        {
           static::$collection = strtolower(static::class);
        }
    }
    public function setSource($collection)
    {
        if (!empty($collection))
        {
            static::$collection = $collection;
        }
    }

    public function setDb($db)
    {
        if (empty($db))
        {
            static::$db = $db;
        }
    }

    /**
     * 自动插入_id
     * @param bool $_id
     */
    public function set_insert_id($_id)
    {
        if ($_id)
        {
            self::$_insert_id = $_id;
        }
    }

    public static function findOne(array $filter,array $options = [])
    {
        new static();
        //2.创建一个Query对象
        $options['limit'] = 1;
        $query = new \MongoDB\Driver\Query($filter, $options);

        $cursor = self::$m->executeQuery(static::$db.'.'.static::$collection, $query)->toArray();
        $cursor = json_decode(json_encode($cursor),true);

        return $cursor[0];
    }
    public static function insert($document)
    {
        new static();
       //2.创建一个BulkWrite对象
        $bulk = new \MongoDB\Driver\BulkWrite();

        $id = new ObjectId().str;
        $document['_id'] = $id;
        $bulk->insert($document);

        //3.执行插入
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $ret = self::$m->executeBulkWrite(static::$db.'.'.static::$collection, $bulk,$writeConcern);
        return $ret;
    }

    public static function insertOne($document)
    {
        new static();
        //2.创建一个BulkWrite对象
        $bulk = new \MongoDB\Driver\BulkWrite();

        $id = new ObjectId().str;
        $document['_id'] = $id;
        $bulk->insert($document);

        //3.执行插入
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $ret = self::$m->executeBulkWrite(static::$db.'.'.static::$collection, $bulk,$writeConcern);
        if ($ret)
        {
            return $id;
        }
        return  false;
    }
    /**
     * @param $where 查询数组
     * @param $op  过滤条件
     * @param $order 排序字段
     * @param int $page
     * @param int $limit
     * @authou shaoyisun
     * @date 2021/7/9
     */
    public static function getList(array $where = [],$field = '*',array $order = [],$page = 1,$limit = 0,$id = true)
    {
        new static();
        $filter = $where;
        $options = [];

        $page = $page < 1 ? 1 : $page;
        $offset = ($page -1) * $limit;

        if ($field != '*' && !empty($field))
        {
            $field_arr = explode(',',$field);
            $field = [];
            foreach ($field_arr as $v)
            {
                $field[$v] = 1;
            }
            $options['projection']  = $field;
        }
        if (!$id)
        {
            $options['projection']  = ['_id' => 0];
        }
        if (!empty($order))
        {
            $options['sort']  = $order;
        }
        if ($limit > 0) {
            $options['limit'] = $limit;
            $options['skip'] = $offset;
        }

        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = self::$m->executeQuery(static::$db.'.'.static::$collection,$query)->toArray();
        if (!empty($cursor)) $cursor = json_decode(json_encode($cursor),true);
        return $cursor;
    }
    public static function count($filter)
    {
        new static();
        $commands = [
            'count' => static::$collection,
            'query' => empty($filter) ? new stdClass() : $filter
        ];
        $command = new \MongoDB\Driver\Command($commands);
        $cursor = self::$m->executeCommand(static::$db, $command)->toArray();
        $count = $cursor[0]->n;
        return $count;
    }
    public static function update(array $filter,array $data)
    {
        new static();
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            $filter,
            ['$set' => $data],
            ['multi' => false, 'upsert' => false]
        );

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $result = self::$m->executeBulkWrite(static::$db.'.'.static::$collection, $bulk, $writeConcern);
        return $result;
    }

    public static function delete(array $filter)
    {
        new static();
        $bulk = new MongoDB\Driver\BulkWrite;

        $bulk->delete($filter);

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $result = self::$m->executeBulkWrite(static::$db.'.'.static::$collection, $bulk, $writeConcern);
        return $result;
    }

    public static function aggregate($pipeline)
    {
        new static();

        $aggregate = [
            "aggregate"=> static::$collection,
            "pipeline" => $pipeline,
            "cursor" => (object)array(),
            'allowDiskUse' => true
        ];
        $command = new MongoDB\Driver\Command($aggregate);
        $cursor = self::$m->executeCommand(static::$db, $command)->toArray();
        if (!empty($cursor)) $cursor = json_decode(json_encode($cursor), true);
        return $cursor;

    }
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
