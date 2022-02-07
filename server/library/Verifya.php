<?php


class Verifya
{

    /*$type  验证码类型
     * $num  验证码长度
     * $white 验证码的宽度
     * $higth 验证码的高度
     * $code  验证码字符串
     * $images  验证图像
    */
    private $type;
    private $num;
    private $width;
    private $height;
    private $imges;
    private $code;

    public function __construct($type = 2, $num = 4, $width = 140, $height = 50)
    {
        //初始化属性
        $this->type = $type;
        $this->num = $num;
        $this->width = $width;
        $this->height = $height;
        //生成验证码字符串的函数
        $this->code = $this->createCode();
    }
    

    //生成验证的方法
    protected function createCode()
    {
        //判断验证码类型
        switch ($this->type) {
            case 0;   //生成纯数字的验证码
                $code = $this->create_Num();
                break;
            case 1;   //生成纯字母的验证码
                $code = $this->create_Che();
                break;
            case 2;   //生成字母和数字组合的验证码
                $code = $this->create_NumChe();
                break;
            default:
                die('不支持验证码类型');
        }
        return $code;
    }
    //生成0-9的纯数字组合的验证码
    protected function create_Num()
    {
        $str = join('', range(0, 9));   //range函数生成一个0到9的数组，然后用join函数将数组的值和空字符串拼接起来。
        return substr(str_shuffle($str), 0, $this->num);  //用str_shuffle函数将字符串打乱，然后用substr函数将想要的字符串截取出来。
    }
    //生成a-z的纯字母组合的验证码
    protected function create_Che()
    {
        $str = join('', range('a', 'z'));
        $str = $str . strtoupper($str);  //将生成的a到z的字符串转成大写。然后将小写拼接起来
        return substr(str_shuffle($str), 0, $this->num);
    }
    //生成数字和数字组合的验证码
    protected function create_NumChe()
    {
        $str_num = join('', range(0, 9));
        $str_che = join('', range('a', 'z'));
        $str = $str_num . $str_che . strtoupper($str_che);  //拼接数字，小写，大写
        $strs =  substr(str_shuffle($str), 0, $this->num);
        $this->sessionCode($strs);
        return $strs;
    }
    protected function sessionCode($strs){
        $session = \Phalcon\Di::getDefault()->get('session');
        $session->set(SESSION_PREFIX.'code',$strs);
    }
    public  function imgCode(){
        //随机生成的字符串
        $str =$this->create_NumChe();
        //验证码图片的宽度
        $width  = 90;

        //验证码图片的高度
        $height = 45;

        //声明需要创建的图层的图片格式
        @ header("Content-Type:image/png");

        //创建一个图层
        $im = imagecreate($width, $height);

        //背景色
        $back = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

        //模糊点颜色
        $pix  = imagecolorallocate($im, 187, 230, 247);

        //字体色
        $font = imagecolorallocate($im, 41, 163, 238);

        //绘模糊作用的点
        mt_srand();
        for ($i = 0; $i < 1000; $i++) {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pix);
        }

        //输出字符
        imagestring($im, 10, 25, 15, $str, $font);

        //输出矩形
        imagerectangle($im, 0, 0, $width -1, $height -1, $font);

        //输出图片
        imagepng($im);
        imagedestroy($im);
    }
}