<?php
/**
 * 验证码类
 * Class Captcha
 */
class Captcha {

    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789'; // 字符集
    private $code; // 验证码文本
    private $length = 4; // 验证码长度
    private $width = 130; // 验证码图片宽度
    private $height = 50; // 验证码图片高度
    private $img; // 图片资源句柄
    private $font; // 字体文件
    private $fontsize = 20; // 字体大小
    private $line = 5; // 干扰线条数量
    private $star = 50; // 干扰星号数量

    /**
     * Captcha constructor.
     * @param array $params
     */
    public function __construct($params = array())
    {
        // 默认字体
        $this->font = SYSTEM_ROOT . '/source/fonts/captcha.ttf';

        // 初始化各项参数
        foreach ($params as $k => $v){
            $this->$k = $v;
        }

        $this->createCode();
    }

    /**
     * 生成随机码
     */
    private function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->length; $i++){
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    /**
     * 生成背景
     * @return $this
     */
    private function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
        return $this;
    }

    /**
     * 生成文字
     * @return $this
     */
    private function createFont()
    {
        $_x = $this->width / $this->length;
        for ($i = 0; $i < $this->length; $i++){
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $color, $this->font, $this->code[$i]);
        }
        return $this;
    }

    /**
     * 生成线条和星号
     * @return $this
     */
    private function createLine()
    {
        // 线条
        for ($i = 0; $i < $this->line; $i++){
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        // 星号
        for ($i = 0; $i < $this->star; $i++){
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        return $this;
    }

    /**
     * 输出图片
     */
    private function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 生成验证码图片
     */
    public function create()
    {
        $this->createBg()->createLine()->createFont()->outPut();
    }
	
	/**
     * 生成空图
     */
    public function createNoting()
    {
		$this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, 255, 251, 240);
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
        $this->outPut();
    }

    /**
     * 获取验证码文本
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}