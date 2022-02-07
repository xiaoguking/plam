<?php
namespace Model\Mysql;

class BasesModel extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        if (MYSQL_RED) {
            $this->setReadConnectionService('dbSlave');
            $this->setWriteConnectionService('dbMaster');
        }
    }
    public static function getDy()
    {
        return \Phalcon\Di::getDefault()->get('dbDy');
    }
}
