#百度贴吧云签到
在服务器上配置好就无需进行任何操作便可以实现贴吧的全自动签到。 配合插件使用还可实现云灌水、点赞、封禁、删帖、审查等功能      
详细了解本程序请前往：http://www.stus8.com/forum.php?mod=viewthread&tid=2141

## 云签到教程
所有教程：http://www.stus8.com/forum.php?mod=forumdisplay&fid=163      
在各平台的部署教程：http://www.stus8.com/forum.php?mod=viewthread&tid=2204      
“往往大部分人安装出错第一反应都是：没错啊，哪里错了，一定是程序错了”  ————插件开发者 D丶L

## 代码库列表
云签到在多个代码库均有副本，当某个代码库不可用时，您可以尝试通过其他代码库下载云签到
#### 代码库地址
OSCGit  : https://git.oschina.net/kenvix/Tieba-Cloud-Sign      
Github  : https://github.com/kenvix/Tieba-Cloud-Sign      
Coding  : https://coding.net/u/kenvix/p/Tieba-Cloud-Sign/git      
Gitcafe : https://gitcafe.com/kenvix/Tieba-Cloud-Sign      
#### 直接下载最新版压缩包地址
OSCGit  : https://git.oschina.net/kenvix/Tieba-Cloud-Sign/repository/archive?ref=master      
Github  : https://github.com/kenvix/Tieba-Cloud-Sign/archive/master.zip      
Coding  : https://coding.net/u/kenvix/p/Tieba-Cloud-Sign/git/archive/master      
Gitcafe : https://gitcafe.com/kenvix/Tieba-Cloud-Sign/archiveball/master/zip      

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
（1）自动更新：前往 检查更新 更新程序即可      
（2）手动更新：直接下载 Zip，删除压缩包内的 config.php ，然后上传到您的网站即可      
另外，每一个大版本都会有一个升级脚本，别忘了运行它      
文件名一般命名为 update旧版本to新版本.php ，例如 update1.0to2.0.php
#####7.如何开启数据库长连接
打开   mysql_autoload.php      
找到   define('LONGSQL', false);      
替换为 define('LONGSQL', true);      

##许可协议
请访问该地址阅读许可协议：http://tc.oschina.mopaas.com/license.html
#####不要尝试去违反协议或用于盈利
#####或者可以前往该地址购买商业授权：http://item.taobao.com/item.htm?id=43814067894

##禁止利用该软件来盈利
#####1.不得要求用户付费
例：你提出用户要付费才能使用
#####2.不得通过提供类似增值服务的方法去盈利
例：你提供一个免费节点和一个付费的会员节点，这是不允许的
#####3.只要涉及收费就禁止
总之：禁止贩售程序，禁止去版权，禁止要求用户付费      
可以：摆放一个捐赠链接（必须是用户自愿，不得强制要求捐赠），挂广告

##其他
StusGame GROUP：http://www.stus8.com      
插件商城：http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13

##联系方式
无名智者：kenvix@vip.qq.com　　http://zhizhe8.net      
mokeyjay：longting@longtings.com　　http://www.longtings.com

##参与开发
贴吧云签到是一个开放的开源项目，任何人均可参与开发，Pull Request即可提交您修改的代码      
Pull Request，提交Issue请到 Git@OSChina 代码库，在其他代码库提交可能不会被处理      
如需加入开发组请联系 @无名智者
###主要开发者列表
无名智者[kenvix@vip.qq.com]      
mokeyjay[longting@longtings.com]      
FYY[fyod@vip.qq.com]       
###感谢
VFleaKing[liuhaotian0520@163.com]      
liwanglin12[i@lwl12.com]      
角落里有蛇[395183830@qq.com]      