<?php
namespace Controllers\Api;

use Model\Mysql\System;
use Library\Response;

class CommonController extends ControllerBase
{
     public function uploadImageAction()
     {
         $file = $_FILES['file'];
         $type = $this->request->getQuery('type');
         $imgUpload = new \ImageUpload($file, 3);
         $imgUpload->imh_w = 250;
         $imgUpload->img_h = 180;
         if ($type == 2){
             $imgUpload->imh_w = 650;
             $imgUpload->img_h = 300;
         }
         if ($type == 3){
             $imgUpload->imh_w = 800;
             $imgUpload->img_h = 120;
         }
         if ($type == 0){
             $imgUpload->setUploadPath()->upload();
             $img = $imgUpload->new_img_url;
         }else{
             $imgUpload->setUploadPath()->upload()->zoom(false);
             $img = $imgUpload->zoom_img_url;
         }


         Response::Success(['img' => $img]);
     }
     public function getSystemAction(){
         $res = System::get('system','admin');
         Response::Success($res);
     }

    /**
     * 文件上传
     * author shaoyi.sun
     * date 2021/10/18 14:12
     */
     public function uploadFileAction(){
         $file = $_FILES['file'];
         if (empty($file)){
             Response::Error(1,'请上传文件');
         }

         $path = $GLOBALS['config']['application']['uploadUri'] . 'file/'. date('Y') . '/' .date('m') . '/' . date('d') . '/';
         if (!is_dir($path))
         {
             $res = @mkdir($path, 0777, true);
             @chmod($path, 0777);
             if (!$res) {
                 throw new \Exception('目录创建失败,请检查权限', 1);
             }
         }
         $s = pathinfo($file['name'],PATHINFO_EXTENSION);
         $filePath = $path.md5($file['name']).'.'.$s;
         $res = @move_uploaded_file($file['tmp_name'], $filePath);
         if (!$res){
             Response::Error(1,'上传失败');
         }
         Response::Success(['file' => $filePath]);

     }
}
