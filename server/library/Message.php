<?php
namespace Library;

class Message
{
    // 向单个用户发送数据
    const CMD_CLIENT_SEND_TO_ONE = 1;

    // 向所有用户发送数据
    const CMD_SEND_TO_ALL = 2;

    // 获取在线状态
    const CMD_GET_ALL_CLIENT = 6;

    // client_id绑定到uid
    const CMD_BIND_UID = 4;

    // 解绑
    const CMD_UNBIND_UID = 5;

    // 向uid发送数据
    const CMD_SEND_TO_UID = 3;

    // 根据uid获取在线的clientid
    const CMD_GET_CLIENT_ID_BY_UID = 7;

    // 判断是否在线
    const CMD_IS_ONLINE = 8;
    // 发踢出用户
    // 1、如果有待发消息，将在发送完后立即销毁用户连接
    // 2、如果无待发消息，将立即销毁用户连接
    const CMD_KICK = 9;

    // 发送立即销毁用户连接
    const CMD_DESTROY = 10;

    // 加入组
    const CMD_JOIN_GROUP = 11;

    // 离开组
    const CMD_LEAVE_GROUP = 12;

    // 向组成员发消息
    const CMD_SEND_TO_GROUP = 13;

    // 获取组成员
    const CMD_GET_CLIENT_BY_GROUP = 14;

    // 获取组在线连接数
    const CMD_GET_CLIENT_COUNT_BY_GROUP = 15;

    // 获取在线的群组ID
    const CMD_GET_GROUP_ID_LIST = 16;

    // 解散分组
    const CMD_UNGROUP = 17;

    // 心跳
    const CMD_PING = 201;


    const SOCKET_HOST = "192.168.0.160:12356";

    private static $tcpPack = "length";

    public static function send($msg){
        @$fp = stream_socket_client("tcp://".self::SOCKET_HOST,$err,$errs,3);
        @fwrite($fp, $msg);
        @$ret = fread($fp, 1024);
        @fclose($fp);
        \Strings::log($ret,true,'message');
        return json_decode($ret,true);
    }
    //消息编码
    public static function encode(array $data, $type = "tcp")

    {
        if ('tcp' == $type) {
            if ('eof' == self::$tcpPack) {
                $data = json_encode($data) . '\r\n';

            } else if ('length' == self::$tcpPack) {
                $data = json_encode($data);
                $data = pack('l', strlen($data)) . $data;
            }
            return $data;

        } else {
            return json_encode($data);

        }
    }
    //消息解码
    public static function decode($jsonString, $type = "tcp")

    {
        if ('tcp' == $type) {
            if ('eof' == self::$tcpPack) {
                $jsonString = str_replace('\r\n', '', $jsonString);

            } else if ('length' == self::$tcpPack) {
                $header = substr($jsonString, 0, 4);

                $len = unpack('Nlen', $header);

                $len = $len['len'];

                $jsonString = substr($jsonString, 4, $len);

            }
            return json_decode($jsonString, true);

        } else {
            return json_decode($jsonString, true);

        }
    }

    //封装消息
    private static function body( $cmd,$body = null,$client = null,$uid = null,$group = null): array
    {
        return [
            'cmd'     => $cmd,
            'body'    => $body,
            'client'  => $client,
            'uid'     => $uid,
            'group'   => $group
        ];
    }
    /**
     * 获取与 uid 在线的 client_id 列表
     *
     * @param string|int $uid
     * @return array
     */
    public static function getClientIdByUid($uid): array
    {
        $msg = self::body(self::CMD_GET_CLIENT_ID_BY_UID,null,null,$uid);
        $res = self::send(self::encode($msg));
        return !empty($res['data']) ? $res['data'] : array();
    }

    /**
     * 获取所有在线的客户端
     * @return array|mixed
     * author shaoyi.sun
     * date 2021/11/5 11:33
     */
    public static function getClientList() :array
    {
        $msg = self::body(self::CMD_GET_ALL_CLIENT);
        $res = self::send($msg);
        return !empty($res['data']) ? $res['data'] : array();
    }

    /**
     * 向客户端发送消息
     *
     * @param string $client
     * @param string $from
     * @param string $message
     * @return bool
     */
    public static function sendToClient(string $client,string $from , string $message): bool
    {
        $body = [
            'type' => 2,
            'to_user' => '',
            'from_user' => $from,
            'content' => $message,
            'time' => date("Y-m-d H:i:s",time())
        ];
        $msg = self::body(self::CMD_CLIENT_SEND_TO_ONE,$body,$client);
        return self::send($msg)['code'] == 0;
    }

    /**
     * 向所有客户端连接发送广播消息
     *
     * @param string $message 向客户端发送的消息
     * @return void
     * @throws Exception
     */
    public static function sendToAll(string $message) : bool
    {
        $body = [
            'type' => 1,
            'to_user' => '',
            'content' => $message,
            'time' => date("Y-m-d H:i:s",time())
        ];
        $msg = self::encode( self::body(self::CMD_SEND_TO_ALL,$body));
        return self::send($msg)['code'] == 0;
    }

    /**
     * 将 client_id 与 uid 解除绑定
     *
     * @param int $client_id
     * @param int|string $uid
     * @return void
     */
    public static function unbindUid(int $client_id, $uid)
    {

    }

    /**
     * 向指定uid发送消息 uid如果不在线 将会存储为离线消息
     * @param $uid
     * @param $message
     * @param $from
     */
    public static function sendToUid($uid,$from,$message): bool
    {
        $body = [
            'type' => 2,
            'to_user' => $uid,
            'from_user' => $from,
            'content' => $message,
            'time' => date("Y-m-d H:i:s",time())
        ];
        if (is_array($uid)){
            foreach ($uid as $value){

                $msg = self::encode(self::body(self::CMD_SEND_TO_UID,$body,null,$value));
                self::send($msg);
            }
            return true;
        }
        $msg = self::encode(self::body(self::CMD_SEND_TO_UID,$body,null,$uid));
        self::send($msg);
        return true;
    }

    /**
     * 踢掉某一个客户端
     *
     * @param string $uid
     * @param string $client
     * @return void
     */
    public static function stopClient(string $uid,string $client)
    {
        if (!empty($uid)){
            //拉取uid在线的客户端
            $UidClient = self::getClientIdByUid($uid);
        }
        $clientList = [];
        if (!empty($UidClient) && !empty($client)) {
            $clientList = array_merge($UidClient,[$client]);
        }
        $clientList = array_unique($clientList);
        foreach ($clientList as $value){
            $msg = self::body(self::CMD_KICK,[],$value);
            self::send($msg);
        }
        return true;
    }

    /**
     * UID 加入群组 如果群组不存在将会创建这个群组
     * @param string $group
     * @param string $uid
     * author shaoyi.sun
     * date 2021/11/5 12:01
     * @return bool
     */
    public static function UidJoinGroup(string $group,string $uid): bool
    {
        if (empty($group) || empty($uid)){
            return false;
        }
        $msg = self::body(self::CMD_JOIN_GROUP,[],null,$uid,$group);
        $res = self::send($msg);
        return $res['code'] == 0;
    }

    /**
     * 获取群组在线的uid列表
     * @param string $group
     * @return array
     * author shaoyi.sun
     * date 2021/11/5 12:10
     */
    public static function GetJoinGroupUid(string $group) : array
    {
        if (empty($group)){
            return array();
        }
        $msg = self::body(self::CMD_GET_CLIENT_BY_GROUP,[],null,null,$group);
        $res = self::send($msg);
        return  empty($res['data']) ? array() : $res['data'];
    }

    /**
     * 向某个群组发送消息
     * @param string $group
     * @param string $message
     * @param string $uid
     * author shaoyi.sun
     * date 2021/11/5 13:34
     */
    public static function sendToGroup(string $group,string $message, string $uid = ""): bool
    {
        $body = [
            'type' => 3,
            'from_user' => $uid,
            'group' => $group,
            'content' => $message,
            'time' => date("Y-m-d H:i:s",time())
        ];
        $msg = self::body(self::CMD_SEND_TO_GROUP,$body,null,null,$group);
        $res = self::send($msg);
        return $res['code'] == 0;
    }

    /**
     * 解散群组
     * @param string $group
     * @return bool
     * author shaoyi.sun
     * date 2021/11/5 13:42
     */
    public static function unGroup(string $group) : bool
    {
        if (empty($group)){
            return false;
        }
        $msg = self::body(self::CMD_UNGROUP,[],null,null,$group);
        $res = self::send($msg);
        return $res['code'] == 0;
    }
}
