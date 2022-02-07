<?php
namespace Controllers\Api;


use Model\Mysql\AdminCron;
use Model\Mysql\AdminCronLog;
use Library\Response;

class CronController extends ControllerBase
{
    /**
     * 获取定时器任务列表
     * author shaoyi.sun
     * date 2021/10/20 15:26
     */
   public function listAction()
   {
       $page = $this->request->getQuery('page','int',1);
       $limit = $this->request->getQuery('limit','int',10);
       $name = $this->request->getQuery('name');
       $res = AdminCron::getList($page,$limit,$name);
       Response::Success($res);
   }
   public function saveAction()
   {
       $params['id'] = $this->request->getPost('id','int');
       $params['name'] = $this->request->getPost('name','string');
       $params['cron'] = $this->request->getPost('cron','string');
       $params['class'] = $this->request->getPost('class','string');
       $params['function'] = $this->request->getPost('function','string');
       $params['begin_time'] = $this->request->getPost('begin_time','string');
       Response::paramsValidation([
           'name' => '名称',
           'cron' => '规则',
           'class' => '类名 ',
           'function' => '方法'
       ]);
       $res = AdminCron::add($params);
       if (!$res){
           Response::Error(1,'error');
       }
       Response::Success();
   }

    /**
     * 删除定时器
     * author shaoyi.sun
     * date 2021/10/20 15:53
     */
   public function deleteAction()
   {
       $id = $this->request->getPost('id');
       if (empty($id)){
           Response::Error(1,' params error');
       }
       $res = AdminCron::del($id);
       if (!$res){
           Response::Error(1,'error');
       }
       Response::Success();
   }
    /**
     * 修改定时器状态
     * author shaoyi.sun
     * date 2021/10/20 15:53
     */
    public function updateStatusAction()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        if (empty($id)){
            Response::Error(1,'ERROR params');
        }
        $res = AdminCron::updateStatus($id,$status);
        if (!$res){
            Response::Error(1,'error');
        }
        Response::Success();
    }

    public function getCronLogAction()
    {

        $id = $this->request->getQuery('id','int');
        $page = $this->request->getQuery('page','int',1);
        $limit = $this->request->getQuery('limit','int',10);
        if (empty($id)){
            Response::Error(1,'params error');
        }
        $data = AdminCronLog::getList($page,$limit,$id);
        Response::Success($data);
    }

    /**
     * 立即执行定时任务
     */
    public function execCronAction()
    {
        $id = $this->request->getQuery('id','int');
        if (empty($id)){
            Response::Error(1,'params error');
        }
        $cron = AdminCron::findFirst($id);
        if (!$cron){
            Response::Error(1,'not found cron');
        }
        $function = "\Cron\\".$cron->class.'::'.$cron->function;
        try {
            $ret = $function();
            if ($ret['code'] == 0){
                Response::Success($ret['msg']);
            }
            Response::Error(1,$ret['msg']);
        }catch (\Exception $exception){
            Response::Error(1,$exception->getMessage());
        }
    }
}
