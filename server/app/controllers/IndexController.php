<?php

use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_AFTER_TEMPLATE);
    }
    /**
     * 提示信息页.
     *
     * @return [type] [description]
     */
    public function infoAction()
    {
        $this->view->setRenderLevel(
            View::LEVEL_ACTION_VIEW
        );
        $params = $this->dispatcher->getParams();
        if (empty($params)) {
            $params['title'] = '出错了！';
            $params['magess'] = '404 NOT FOUND';
        }
        $this->view->magess = $params['magess'];
        $this->view->title = $params['title'];
    }
    public function indexAction()
    {

    }
}
