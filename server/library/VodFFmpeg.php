<?php
use FFMpeg\Coordinate\TimeCode as Img;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264 as Video_X264;

class VodFFmpeg
{

    /*******************
    支持的格式 rmvb，wmv，mkv，3gp，mp4，mpg，avi，mp3
     * 此插件用来处理视频
     * ffmpegpath  ffprobepath 软件安装目录
     *******************/
    private  $ffmpegpath='/usr/local/src/ffmpeg-4.3.1-amd64-static/ffmpeg';

    private  $ffprobepath = '/usr/local/src/ffmpeg-4.3.1-amd64-static/ffprobe';

    private  $cli; //连接对象

    public function __construct()
    {
        $this->cli= FFmpeg::create(array(
            'ffmpeg.binaries' => $this->ffmpegpath,
            'ffprobe.binaries' => $this->ffprobepath,
            'timeout' => 0,
            'ffmpeg.threads' => 12
        ));
    }

    /**
     *  文件对象
     */
    public  function open($file){
         $video = $this->cli->open($file);
         return $video;
    }

    /**
     * 获取文件属性
     */
    public function get($file,$filed){
        $ffprobe = \FFMpeg\FFProbe::create(array(
            'ffmpeg.binaries' => $this->ffmpegpath,
            'ffprobe.binaries' => $this->ffprobepath,
            'timeout' => 0,
            'ffmpeg.threads' => 12
        ));
        $fileds = $ffprobe
            ->format($file) // extracts file informations
            ->get($filed);             // returns the duration property
        if ($filed == 'duration'){
            $fileds = $this->time($fileds);
        }
        return $fileds;
    }
    public function time($seconds){

        $hour = floor($seconds/3600);
        $minute = floor(($seconds-3600 * $hour)/60);
        $second = floor((($seconds-3600 * $hour) - 60 * $minute) % 60);
        if ($hour == 0){
            $result = $minute.'.'.$second;
        }else{
            $result = $hour.'.'.$minute.'.'.$second;
        }
        return $result;
    }
    /**
     * 提取视频图片
     */
    public function img($file,$time){
        $video = $this->open($file);
        $frame = $video->frame(Img::fromSeconds($time));
        $basePath = $GLOBALS['config']['application']['uploadUri'];
        $namePath = date('Y', time()) . '/' . date('md', time());
        $path = $basePath.$namePath;
        $this->filePath($path);
        $imgname = time().'.jpg';
        $frame->save($path.'/'.$imgname);
        return $path.'/'.$imgname;
    }
    /**
     * 视频转码
     */
    public function videoTranscoding($file){

        $filename = basename($file);
        $filename = str_replace(strrchr($filename, '.'), '', $filename);

        $basePath = $GLOBALS['config']['application']['uploadUri'].'video/';
        $namePath = date('Y', time()) . '/' . date('md', time());
        $path = $basePath.$namePath;
        $this->filePath($path);
        $video = $this->open($file);
        $format = new Video_X264('aac');
        $format->on('progress', function ($video, $format, $percentage) {
            echo "$percentage % transcoded";
        });
        $format
            ->setKiloBitrate(1200)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(256);
        $filename = $filename.'.mp4';
        $video->save($format, $path.'/'.$filename);
        return $path.'/'.$filename;
    }
    /**
     * 视频水印
     */
    public function videoWatermark($file,$watermarkPath){
        $pxFile = pathinfo($file, PATHINFO_EXTENSION);
        $video = $this->open($file);
        $basePath = $GLOBALS['config']['application']['uploadUri'].'video/';
        $namePath = date('Y', time()) . '/' . date('md', time());
        $path = $basePath.$namePath;
        $this->filePath($path);
        $video->filters()
            ->watermark($watermarkPath, array(
                'position' => 'relative',

                'bottom' => 50,

                'right' => 100
            ));

        $format = new Video_X264('aac');
        $filename = $path.'/'.time().'.'.$pxFile;
        $video->save($format,$filename);
        return $filename;
    }
    /**
     * 视频合成
     */
    public function videoQit(){

    }
    /**
     * 创建文件夹
     */
    private function filePath($path){
        if (!file_exists($path)) {

            $res = @mkdir($path, 0777, true);
            @chmod($path, 0777);
            if (!$res) {
                throw new \Exception('目录创建失败,请检查权限', 1);
            }
        }
    }
}
