<?php

namespace Controllers\Api;

/**
 * ControllerBase
 * @author  Xiaogu
 * @copyright  2021/2/6
 * @Time: 16:26
 */
class ControllerBase extends \Phalcon\Mvc\Controller
{
    public $token = null;


    public function initialize()
    {
        $this->token = $this->request->getHeader('Token');
    }
}
