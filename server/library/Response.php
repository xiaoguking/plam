<?php

namespace Library;
class Response
{
    /**
     * ajax 失败返回
     * @param int $code
     * @param string $msg
     */
    public static function Error( $code = 1, $msg = '')
    {
        $data['code'] = $code;
        $data['message'] = $msg;
        header('Content-type: application/json');
        echo json_encode($data);
        \Phalcon\DI::getDefault()->get('response')->send();
        exit;
    }

    /**
     * axjx 成功返回
     * @param array $data
     * @param int $code
     */
    public static function Success($datas = [], $code = 0)
    {
        $data['code'] = $code;
        $data['message'] = '';
        $data['data'] = $datas;
        header('Content-type: application/json');
        echo json_encode($data);
        \Phalcon\DI::getDefault()->get('response')->send();
        exit;
    }

    /**
     * 简单的参数验证
     * @param $rule
     * @return void
     * @throws \Exception
     */
    public static function paramsValidation($rule)
    {
        $params = $_REQUEST;
        if ((empty($rule) || !is_array($rule)) && $rule != 0 )
        {
            return;
        }
        foreach ($rule as $k =>$v){
           if (!isset($params[$k]) || $params[$k] == ''){
               throw new \Exception('必填项 '.$v.' 不能为空',1);
           }
        }
    }
}
