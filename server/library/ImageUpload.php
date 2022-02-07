<?php
/**
 * 图片上传处理类
 * Class ImageUpload
 */

class ImageUpload
{
    protected $file = null; //文件数组

    protected $error;

    protected $all_type = [
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp'
    ];
    protected $max_size;

    protected $uploadPath;

    protected $suffix;

    public $img_h = 150;

    public $imh_w = 200;

    public $new_file;

    public $new_img_url;

    public $zoom_file;

    public $zoom_img_url;

    public function __construct($file,$max_size)
    {
        $this->file = $file;
        $this->suffix = pathinfo($file['name'],PATHINFO_EXTENSION);
        $this->max_size = $max_size;
        $this->error();
        $this->vdt_type();
        $this->vdt_size();
    }

    private function error()
    {
        if (empty($this->file))
        {
            throw new \Exception('图片数据为空', 1);
        }
        $code = array(
            UPLOAD_ERR_INI_SIZE => '文件超出系统设定大小',
            UPLOAD_ERR_PARTIAL => '文件上传中断',
            UPLOAD_ERR_NO_FILE => '请选择文件',
            UPLOAD_ERR_NO_TMP_DIR => '文件创建失败',
            UPLOAD_ERR_CANT_WRITE => '文件写入出错',
        );
        $error_code = $this->file['error'];
        if ($error_code > 0)
        {
            $msg = isset($code[$error_code]) ? $code[$error_code]: "未知错误[{$error_code}]";
            throw new \Exception('上传失败:' . $msg, 1);
        }
    }

    private function vdt_type()
    {
        $type = $this->file['type'];
        if (!in_array($type,$this->all_type))
        {
            throw new \Exception('文件类型不合法', 1);
        }
    }

    private function vdt_size()
    {
        $size = $this->file['size'];
        if ($size > $this->max_size*1024*1024)
        {
            throw new \Exception('文件不能超出'.$this->max_size.'M', 1);
        }
    }

    /**
     * @param string $path
     * @return $this
     * @throws Exception
     */
    public function setUploadPath($path = '')
    {
        if (empty($path))
        {
            $path = $GLOBALS['config']['application']['uploadUri'] . 'images/' . date('Y') . '/' .date('m') . '/' . date('d') . '/';
        }
        $this->uploadPath = $path;
        if (!is_dir($path))
        {
            $res = @mkdir($path, 0777, true);
            @chmod($path, 0777);
            if (!$res) {
                throw new \Exception('目录创建失败,请检查权限', 1);
            }
        }
        return $this;
    }
    public function upload()
    {
        $filePath = $this->uploadPath.md5($this->file['name'].time()).'.'.$this->suffix;
        $res = @move_uploaded_file($this->file['tmp_name'], $filePath);
        if (!$res) {
            throw new \Exception('文件上传失败', 1);
        }
        $this->new_file = $filePath;
        $this->new_img_url = str_replace($GLOBALS['config']['application']['uploadUri'],UPLOAD_URL,$filePath);
        return $this;
    }

    /**
     * @Notes: 图片缩放
     * @Authou: ShaoYi.Sun
     * @Date: 2021/8/20
     * @return $this
     * @throws Exception
     */
    public function zoom($orig = true){

        $width = $this->imh_w;
        $height = $this->img_h;
        $file = $this->new_file;

        $image = new \Phalcon\Image\Adapter\Gd($file);
        //不能拉伸
        if ($width < $image->getWidth() && $height < $image->getHeight()){
            $image->resize(
                $width,
                $height,
                \Phalcon\Image::NONE
            );
            $ex = pathinfo($file, PATHINFO_EXTENSION);
            $dir_name = dirname($file);
            $file_path = $dir_name.'/'.md5_file($file).'_'.$width.'x'.$height.'.'.$ex;
            $ret = $image->save($file_path);
            if (!$ret)
            {
                throw new Exception('zoom error',1);
            }
            if ($orig == false)
            {
                unlink($file);
            }
            $this->zoom_file = $file_path;
            $this->zoom_img_url = str_replace($GLOBALS['config']['application']['uploadUri'],UPLOAD_URL,$file_path);
            return $this;
        }
        $this->zoom_img_url = str_replace($GLOBALS['config']['application']['uploadUri'],UPLOAD_URL,$file);
        return $this;
    }

    /**
     * 图片水印
     */
    protected function watermark(){
        $img = new \Phalcon\Image\Adapter\Gd();
        $watermark = new \Phalcon\Image\Adapter\Gd();
        $offsetX =w;
        $offsetY =h;

        $opacity = 90;

        $img->watermark(
            $watermark,
            $offsetX,
            $offsetY,
            $opacity
        );

        $img->save();
    }
}
