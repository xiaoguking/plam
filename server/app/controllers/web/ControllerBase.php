<?php


namespace Controllers\Web;


use Phalcon\Mvc\Dispatcher;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        //这里指明渲染的模板路径
        $this->view->setViewsDir($this->view->getViewsDir() . '/web');
        //$this->view->setMainView('../'.$this->view->getMainView());
     //  var_dump($this->view->getViewsDir() );die();
    }
}