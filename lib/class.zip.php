<?php
/**
 * 压缩包操作类
 * @copyright (c) mokeyjay
 */

class zip {
    
    //内部zip类
    private $z;
    
    //构造函数
    function __construct($file = '') {
        $this->z = new ZipArchive;
        if ($file != '') {
            $this->open($file);
        }
    }
    
    //打开一个压缩包，默认值为1，也就是ZipArchive::CREATE
    //8 = ZipArchive::OVERWRITE : 总是创建一个新的文件，如果指定的zip文件存在，则会覆盖掉
    //1 = ZipArchive::CREATE : 如果指定的zip文件不存在，则新建一个
    //2 = ZipArchive::EXCL : 如果指定的zip文件存在，则会报错
    //4 = ZipArchive::CHECKCONS : 未知，哪里都查不到卧槽
    public function open($file = '', $mode = 1) {
        if ($this->z->open($file, $mode) !== true) throw new Exception('打开压缩包' . $file . '失败！');
    }
    
    //关闭压缩包并释放资源
    public function close() {
        $this->z->close();
        $this->z = null;
    }
    
    //获取ZipArchive以便直接操作
    public function getArchive() {
        return $this->z;
    }
    
    //解压缩
    public function extract($path) {
        $this->z->extractTo($path);
    }
    
    //添加路径下所有目录、文件到压缩包。$path=. 则表示当前目录
    public function addDir($path) {
        $handle = opendir($path);
        while (($filename = readdir($handle)) !== false) {
            if ($filename != '.' && $filename != '..') {
                if (is_dir($path . '/' . $filename)) {
                    $this->addDir($path . '/' . $filename);
                } 
                else {
                	//如果以./开头则表示为当前目录下，因此过滤掉./，免得东西都压缩进.文件夹了
                    $filename = $path . '/' . $filename;
                    $file = substr($filename, 0, 2) == './' ? substr($filename, 2) : $filename;
                    $this->z->addFile($filename, $file);
                }
            }
        }
        @closedir($handle);
    }
    
    //添加路径下所有文件（不包括子目录）到压缩包。$path=. 则表示当前目录
    public function addFiles($path) {
        $handle = opendir($path);
        while (($filename = readdir($handle)) !== false) {
            if ($filename != '.' && $filename != '..') {
                if (is_file($path . '/' . $filename)) {
                	//如果以./开头则表示为当前目录下，因此过滤掉./，免得东西都压缩进.文件夹了
                    $filename = $path . '/' . $filename;
                    $file = substr($filename, 0, 2) == './' ? substr($filename, 2) : $filename;
                    $this->z->addFile($filename, $file);
                }
            }
        }
        @closedir($handle);
    }
    
    //云签专用备份函数
    public function backup() {
        
        //开始添加目录和文件，无视setup，没啥备份的价值，而且回滚也在里面，备份起来太费资源
        $this->addDir('lib');
        $this->addDir('source');
        $this->addDir('plugins');
        $this->addDir('templates');
        $this->addFiles('.');
    }
}
