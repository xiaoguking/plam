<?php

/**
 * STRING处理类.
 *
 * @author 张市军 <shijun@staff.sina.com.cn>
 *
 * @version 1.0
 *
 * @copyright (c) 2005, 新浪网研发中心 All rights reserved.
 *
 * @example ./string.php 查看源代码
 * @example ./string.example.php 如何使用请点这里(请在环境中运行)
 */
class Strings
{
    /**
     * 处理截取中文字符串的操作.
     *
     * @param string $str 要处理的字符
     *                    string $start 开始位置
     *                    string $offset 偏移量
     *                    string $t_str 字符结果尾部增加的字符串，默认为空
     *                    boolen $ignore $start位置上如果是中文的某个字后半部分是否忽略该字符，默认true
     * @return string
     *
     */
    public static function substr_cn($str, $start, $offset, $t_str = '', $ignore = true)
    {
        $length = strlen($str);
        if ($length <= $offset && $start == 0) {
            return $str;
        }
        if ($start > $length) {
            return $str;
        }
        $r_str = '';
        for ($i = $start; $i < ($start + $offset); ++$i) {
            if (ord($str[$i]) > 127) {
                if ($i == $start) { //检测头一个字符的时候，是否需要忽略半个中文
                    if (string::is_cn_str($str, $i) == 1) {
                        if ($ignore) {
                            continue;
                        } else {
                            $r_str .= $str[($i - 1)] . $str[$i];
                        }
                    } else {
                        $r_str .= $str[$i] . $str[++$i];
                    }
                } else {
                    $r_str .= $str[$i] . $str[++$i];
                }
            } else {
                $r_str .= $str[$i];
                continue;
            }
        }

        return $r_str . $t_str;
    }

    /**
     * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
     * 返回 1 是左边 0 不是中文 -1是右边.
     *
     * @param string $str 开始位置
     * @param int $location 位置
     * @return int
     *
     */
    public static function is_cn_str($str, $location)
    {
        $result = 1;
        $i = $location;
        while (ord($str[$i]) > 127 && $i >= 0) {
            $result *= -1;
            --$i;
        }

        if ($i == $location) {
            $result = 0;
        }

        return $result;
    }

    /**
     * 判断字符是否全是中文字符组成
     * 2 全是 1部分是 0没有中文.
     *
     * @param string $str 要判断的字符串
     * @return bool
     *
     */
    public static function chk_cn_str($str)
    {
        $result = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i) {
            if (ord($str[$i]) > 127) {
                ++$result;
                ++$i;
            } elseif ($result) {
                $result = 1;
                break;
            }
        }
        if ($result > 1) {
            $result = 2;
        }

        return $result;
    }

    /**
     * 判断邮件地址的正确性.
     *
     * @param string $mail 邮件地址
     * @return bool
     *
     */
    public static function is_mail($mail)
    {
        //return preg_match("/^[a-z0-9_\-\.]+@[a-z0-9_]+\.[a-z0-9_\.]+$/i" , $mail);
        return preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+){1,4}$/', $mail) ? true : false;
    }

    /**
     * 判断App的CallbackURL是否合法（可以包含端口号）.
     *
     * @param string $url URL地址
     * @return bool
     *
     */
    public static function is_callback_url($url)
    {
        return preg_match("/(ht|f)tp(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/i", $url);
    }

    /**
     * 判断URL是否以http(s):// ftp://格式开始的地址
     *
     * @param string $url URL地址
     * @return bool
     *
     */
    public static function is_http_url($url)
    {
        return preg_match("/^(https?|http):\/\/([\w-]+\.)+[\w-]+(:\d+)?(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i", $url); //增加:8080这种带端口的情况
        // return  preg_match("/^(https?|ftp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
    }

    /**
     * 允许中文.
     */
    public static function is_url($url)
    {
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
        return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i", $url);
    }

    /**
     * 是否是微博客户端协议url
     * add by zhangchao 2013-09-29.
     *
     * @param [type] $url [description]
     *
     * @return bool [description]
     */
    public static function is_client_url($url)
    {
        return preg_match("/^(sinaweibo):\/\/([\w-]+)\?([\w;\/?:@&=+$,# _.!~*'\"()%-]+)$/i", $url);
    }

    /**
     * 判断URL是否是正确的音乐地址
     *
     * @param string $url URL地址
     * @return bool
     *
     */
    public static function is_music_url($url)
    {
        return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(:\d{1,9}+)?(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i", $url);
        //return preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[^ \t\r\n{}\[\]`^<>\\\\]*)?$/i" , $url);
        //return  preg_match("/^(https?|ftp|mms|mmsu|mmst|rtsp):\/\/([\w-]+\.)+[\w-]+(\/[\w;\/?:@&=+$,# _.!~*'\"()%-]*)?$/i" , $url);
        //return preg_match("/^(http(s)|ftp):\/\/[a-z0-9\.\/_-]*?$/i" , $url);
    }

    /**
     * 是否是手机号.
     *
     * @param bool $simple 简化形式
     *                       true:
     *                       18600000000
     *                       false:
     *                       +86 18600000000
     *                       86 18600000000
     *                       +8618600000000
     *                       18600000000
     * @param [type] $str    [description]
     *
     * @return bool [description]
     */
    public static function is_phone($str, $simple = true)
    {
        if ($simple) {
            return preg_match("@^1\d{10}$@", $str);
        } else {
            return preg_match("@^((\+86)|(86))?\s?1\d{10}$@", $str);
        }
    }

    /**
     * UTF-8编码情况下 *
     * 计算字符串的长度 *.
     *
     * @param string $str 字符串
     *
     * @return array
     */
    public static function strLength($str)
    {
        $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

        $arr['en'] = strlen($str) - $length;
        $arr['cn'] = intval($length / 3); //编码GBK，除以2

        return $arr['en'] + $arr['cn'];
    }

    /**
     * 格式化打印.
     *
     * @param type $param
     */
    public static function dump($param)
    {
        echo '<pre>';
        print_r($param);
        echo '</pre>';
    }

    /**
     * 检查密码
     * 默认6位以上；
     * 必须包含1位以上的数字、小写字母、大写字母；.
     *
     * @param [type] $password [description]
     *
     * @return [type] [description]
     */
    public static function checkPassword($password, $length = 6)
    {
        return preg_match('@^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[\S]{' . $length . ',}$@', $password);
    }

    //生成一个加盐加密的的密码
    public static function my_cptye($password, $salt)
    {
        return md5(md5($password) . $salt);
    }

    /////////////////生成随机字符串//////////////////////////////////////

    /**
     * 按不同组合方式生成确定长度的随机字符串（不唯一）.
     *
     * @param $length  长度
     * @param $type  组合方式
     *
     * @return string
     */
    public static function randomStr($length, $type)
    {
        if ($type == 1) {
            $str = '0123456789';
        } elseif ($type == 2) {
            $str = 'abcdefghijklmnopqrstuvwxyz';
        } elseif ($type == 3) {
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 4) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyz';
        } elseif ($type == 5) {
            $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 6) {
            $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 7) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $strlen = strlen($str);
        while ($length > $strlen) {
            $str .= $str;
            $strlen += $strlen;
        }
        $str = str_shuffle($str);

        return substr($str, 0, $length);
    }

    //生成随机唯一字符串
    public static function coupon_card($length, $type)
    {
        if ($type == 1) {
            $str = '0123456789';
        } elseif ($type == 2) {
            $str = 'abcdefghijklmnopqrstuvwxyz';
        } elseif ($type == 3) {
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 4) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyz';
        } elseif ($type == 5) {
            $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 6) {
            $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif ($type == 7) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = $str,
            $d = '',
            $f = 0;
            $f < $length;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;

        return $d;
    }

    /////////////////生成随机字符串//////////////////////////////////////

    /////////////////生成随机字符串/（案例）/////////////////////////////////////

    /**
     * 生成随机字符串.
     *
     * @param type $length
     *
     * @return type
     */
    public static function createRandomStr($length, $all = true)
    {
        $str = '0123456789'; //62个字符
        if ($all) {
            $str .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
//        $strlen = 62;
        $strlen = strlen($str);
        while ($length > $strlen) {
            $str .= $str;
            $strlen += $strlen;
        }
        $str = str_shuffle($str);

        return substr($str, 0, $length);
    }

    /**
     * 根据id生成唯一邀请码
     *
     * @param $userId
     * @param $length
     *
     * @return string
     */
    public static function createCode($userId, $length)
    {
        static $sourceString = [
            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'q', 'r',
            's', 't', 'u', 'v', 'w', 'x',
            'y', 'z',
        ];
        $num = $userId;
        $code = '';
        while ($num) {
            $mod = $num % 36;
            $num = (int)($num / 36);
            $code = "{$sourceString[$mod]}{$code}";
        }
        //判断code的长度
        if (empty($code[$length - 1])) {
            $code = str_pad($code, $length, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    //生成随机唯一字符串
    public static function make_coupon_card($length)
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = 'ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < $length;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;

        return $d;
    }

    /**
     * 生成永远唯一的激活码
     *
     * @return string
     */
    public static function guid($namespace = null)
    {
        static $guid = '';
        $uid = uniqid('', true);

        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];     // 请求那一刻的时间戳
        $data .= $_SERVER['HTTP_USER_AGENT'];  // 获取访问者在用什么操作系统
        $data .= $_SERVER['SERVER_ADDR'];      // 服务器IP
        $data .= $_SERVER['SERVER_PORT'];      // 端口号
        $data .= $_SERVER['REMOTE_PORT'];      // 端口信息

        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = '{' . substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12) . '}';

        return $guid;
    }

    public static function make_coupon_card1()
    {
        mt_srand((float)microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = //chr(123)// "{"
            substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        //.chr(125);// "}"
        return $uuid;
    }

    public static function getInviteCode()
    {
        list($s1, $s2) = explode(' ', microtime());

        return sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public static function invitationCode1($length)
    {
        $string = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $string[rand(0, strlen($string) - 1)];
        }

        return $str;
    }

    /////////////////生成随机字符串（案例）//////////////////////////////////////

    /**
     * @param type $absolutePath
     *
     * @throws Exception
     */
    public static function drmkdir($absolutePath)
    {
        if (!file_exists($absolutePath)) {
            $res = @mkdir($absolutePath, 0777, true); //由于要递归创建子目录，所以权限必须为777
            if (!$res) {
                throw new Exception("创建目录'" . $absolutePath . "'失败，请检查权限", 1);
            }
        }
    }

    //验证url合法性
    public static function check_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * 等级的换算.
     */
    public static function level($level)
    {
        if ($level > (int)\Field::getDic('score', true)[5]) {
            $level = (floor($level / 1000)) . 'k';
        }

        return $level;
    }

    //处理重复数据
    public static function uniquearr($array2D, $stkeep = false, $ndformat = true)
    {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)
        if ($stkeep) {
            $stArr = array_keys($array2D);
        }

        // 判断是否保留二级数组键 (所有二级数组键必须相同)
        if ($ndformat) {
            $ndArr = array_keys(end($array2D));
        }

        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        foreach ($array2D as $v) {
            $v = join(',', $v);
            $temp[] = $v;
        }
        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp, SORT_NUMERIC);
        //再将拆开的数组重新组装
        foreach ($temp as $k => $v) {
            if ($stkeep) {
                $k = $stArr[$k];
            }
            if ($ndformat) {
                $tempArr = explode(',', $v);
                foreach ($tempArr as $ndkey => $ndval) {
                    $output[$k][$ndArr[$ndkey]] = $ndval;
                }
            } else {
                $output[$k] = explode(',', $v);
            }
        }

        return $output;
    }

    /**
     * 处理浏览量.
     */
    public static function digital($sum)
    {
        if ($sum > 10000) {
            $sum = $sum / 10000;
            $sum = $sum . '万';
        }

        return (string)$sum;
    }

    //PHP转换文本框内容为HTML格式
    public static function shtm($str)
    {
        $str = '<p>' . $str;
        $str = str_replace(array("\r\n", "\r", "\n"), '<br>', $str);
        $str = str_replace(chr(32), '&nbsp;', $str);
        $str .= "</p>\n"; // 文本尾加入</p>
        $str = str_replace('<p></p>', '', $str); // 去除空段落
        return $str;
    }

    //PHP转换HTML格式为文本框内容
    public static function reshtm($str)
    {
        $str = str_replace('<br>', "\n", $str);
        $str = str_replace('&nbsp;', chr(32), $str);
        $str = str_replace('<p>', '', $str); // 去除空段落
        $str = str_replace('</p>', '', $str); // 去除空段落
        return $str;
    }

    /**
     * @Notes: 记录日志 支持任何类型数据
     * @param $data
     * @param bool $fileAppend
     * @param string $filename
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/13
     */
    public static function log($data, $fileAppend = true, $filename = 'debug')
    {
        if (is_array($data) || is_object($data))
        {
            if (is_object($data))
            {
                $data = json_decode(json_encode($data),true);
            }

            $data = var_export($data,true);
        }
        $data = '['.date('Y-m-d H:i:s')."]\r\n".$data."\r\n";

        $bases_path = dirname(dirname(__FILE__)).'/cache/log/'.$filename.'/'.date('Y').'/' .date('md');

        if (!is_dir($bases_path))
        {
            @mkdir($bases_path,0777,true);
            @chmod($bases_path, 0777);
        }
        if ($fileAppend) {
            file_put_contents( $bases_path.'/' . 'debug.log', $data, FILE_APPEND);
        } else {
            file_put_contents($bases_path.'/' . 'debug.log', $data);
        }
    }

    //获取顶级域名
    public static function getTopHost($url)
    {
        $url = strtolower($url);  //首先转成小写
        $hosts = parse_url($url);
        $host = $hosts['host'];
        //查看是几级域名
        $data = explode('.', $host);
        $n = count($data);
        //判断是否是双后缀
        $preg = '/[\w].+\.(com|net|org|gov|edu)\.cn$/';
        if (($n > 2) && preg_match($preg, $host)) {
            //双后缀取后3位
            $host = $data[$n - 3] . '.' . $data[$n - 2] . '.' . $data[$n - 1];
        } else {
            //非双后缀取后两位
            $host = $data[$n - 2] . '.' . $data[$n - 1];
        }

        return $host;
    }

    /**
     * 获取请求头信息
     * @authou shaoyisun
     * @date 2021/7/16
     * @return mixed
     */
    public static function getHeader()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * @Notes: 客户端ip
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/20
     * @return mixed|string
     */
    public static function get_client_ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim(current($ip));
        }
        return $ip;
    }

    /**
     * @Notes: 二维数组排序
     * @param $array
     * @param $keys
     * @param int $sort
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/30
     * @return mixed
     */
    public static function arraySort($array, $keys, $sort = SORT_DESC) {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }

    /**
     * 获取后台用户信息
     * @return array
     * @throws Exception
     */
    public static function getUser(){
        $header = static::getHeader();
        if (empty($header)){
            return [];
        }
        $token = $header['Token'];
        $info = \Model\Mysql\User::getInfo($token);
        return $info;
    }

    public static function getAdminUid(){
        $header = static::getHeader();
        if (empty($header)){
            return 0;
        }
        $token = $header['Token'];
        $uid = \Model\Mysql\User::getUid($token);
        return $uid;
    }

    /**
     * 字符串转二进制数据
     * @param $str
     * @return string
     * author shaoyi.sun
     * date 2021/11/5 10:13
     */
   public static function StrToBin($str){
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        foreach($arr as &$v){
            $temp = unpack('H*', $v);
            $v = base_convert($temp[1], 16, 2);
            unset($temp);
        }

        return join(' ',$arr);
    }

    /**
     * 将二进制转换成字符串
     * @param type $str
     * @return type
     */
    function BinToStr($str){
        $arr = explode(' ', $str);
        foreach($arr as &$v){
            $v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
        }
        return join('', $arr);
    }

    /**
     * 获取客户端操作系统
     * @param string $agent
     * @return array
     */
    public static function getClientOS($agent = '')
    {
        //window系统
        if (stripos($agent, 'window')) {
            $os = 'Windows';
            $equipment = '电脑';
            if (preg_match('/nt 6.0/i', $agent)) {
                $os_ver = 'Vista';
            }elseif(preg_match('/nt 10.0/i', $agent)) {
                $os_ver = '10';
            }elseif(preg_match('/nt 6.3/i', $agent)) {
                $os_ver = '8.1';
            }elseif(preg_match('/nt 6.2/i', $agent)) {
                $os_ver = '8.0';
            }elseif(preg_match('/nt 6.1/i', $agent)) {
                $os_ver = '7';
            }elseif(preg_match('/nt 5.1/i', $agent)) {
                $os_ver = 'XP';
            }elseif(preg_match('/nt 5/i', $agent)) {
                $os_ver = '2000';
            }elseif(preg_match('/nt 98/i', $agent)) {
                $os_ver = '98';
            }elseif(preg_match('/nt/i', $agent)) {
                $os_ver = 'nt';
            }else{
                $os_ver = '';
            }
            if (preg_match('/x64/i', $agent)) {
                $os .= '(x64)';
            }elseif(preg_match('/x32/i', $agent)){
                $os .= '(x32)';
            }
        }
        elseif(stripos($agent, 'linux')) {
            if (stripos($agent, 'android')) {
                preg_match('/android\s([\d\.]+)/i', $agent, $match);
                $os = 'Android';
                $equipment = 'Mobile phone';
                $os_ver = $match[1];
            }else{
                $os = 'Linux';
            }
        }
        elseif(stripos($agent, 'unix')) {
            $os = 'Unix';
        }
        elseif(preg_match('/iPhone|iPad|iPod/i',$agent)) {
            preg_match('/OS\s([0-9_\.]+)/i', $agent, $match);
            $os = 'IOS';
            $os_ver = str_replace('_','.',$match[1]);
            if(preg_match('/iPhone/i',$agent)){
                $equipment = 'iPhone';
            }elseif(preg_match('/iPad/i',$agent)){
                $equipment = 'iPad';
            }elseif(preg_match('/iPod/i',$agent)){
                $equipment = 'iPod';
            }
        }
        elseif(stripos($agent, 'mac os')) {
            preg_match('/Mac OS X\s([0-9_\.]+)/i', $agent, $match);
            $os = 'Mac OS X';
            $equipment = '电脑';
            $os_ver = str_replace('_','.',$match[1]);
        }
        else {
            $os = 'Other';
        }
        return ['os'=>$os, 'os_ver'=>$os_ver, 'equipment'=>$equipment];
    }
}
