#百度贴吧云签到
详细了解本程序请前往：http://www.stus8.com/forum.php?mod=viewthread&tid=2141
##常见问题解决方案
#####1.如何安装程序
上传此程序到您的网站，然后网页访问 /setup/install.php   
#####2.如何开启 MySQL 连接方式强制功能
打开   mysql_autoload.php   
找到   define('SQLMODE', 'mysqli');   
替换为 define('SQLMODE', 'mysql');   
#####3.数据库配置正确，但连接数据库失败，错误代码 20XX
请尝试开启 MySQL 连接方式强制功能   
#####4.如何手动修改配置
打开 config.php 并按照里面的注释修改   
切勿使用记事本编辑，否则程序将不能工作   
#####5.如何手动导入数据库
打开 /setup/install.template.sql 并按照里面的注释修改   
#####6.如何安装新版本
若需要安装新版本，直接下载 Zip，删除压缩包内的 config.php ，然后上传到您的网站即可   
每一个大版本都会有一个升级脚本，运行它即可   
文件名一般命名为 update旧版本to新版本.php ，例如 update1.0to2.0.php   

## 贴吧云签到在 SAE/BAE/JAE 下的架设方案
SAE：[http://www.stus8.com/forum.php?mod=viewthread&tid=2158](http://www.stus8.com/forum.php?mod=viewthread&tid=2158)   
JAE：[http://www.stus8.com/forum.php?mod=viewthread&tid=2164](http://www.stus8.com/forum.php?mod=viewthread&tid=2164)   
BAE：[http://www.stus8.com/forum.php?mod=viewthread&tid=2162](http://www.stus8.com/forum.php?mod=viewthread&tid=2162)   

##禁止利用该软件来盈利
#####1.不得要求用户付费
例：你提出用户要付费才能使用
#####2.不得通过提供类似增值服务的方法去盈利
例：你提供一个免费节点和一个付费的会员节点，这是不允许的
#####3.只要涉及收费就禁止
总之：禁止贩售程序，禁止去版权，禁止要求用户付费      
可以：摆放一个捐赠链接（必须是用户自愿，不得强制要求捐赠），挂广告

#####不要尝试去违反协议或用于盈利

#其他
StusGame GROUP:http://www.stus8.com   
博客:http://zhizhe8.net   
感谢Emlog(http://www.emlog.net)   
插件商城：http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13   

#联系方式
无名智者:kenvix@loli.com
mokeyjay:longting@longtings.com