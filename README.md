#百度贴吧云签到
在服务器上配置好就无需进行任何操作便可以实现贴吧的全自动签到。     配合插件使用还可实现云灌水、点赞、封禁、删帖、审查等功能。     
详细了解本程序请前往：http://www.stus8.com/forum.php?mod=viewthread&tid=2141

##云签到相关
产品中心：http://s.stus8.com/     
插件商城：http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13     
全部教程：http://www.stus8.com/forum.php?mod=forumdisplay&fid=163     
在各平台的部署教程：http://www.stus8.com/forum.php?mod=viewthread&tid=2204     
“往往大部分人安装出错第一反应都是：没错啊，哪里错了，一定是程序错了”  ————插件开发者 D丶L

##常见问题解决方案
#####1.如何安装程序
上传此程序到您的网站，然后访问您的网站
#####2.如何开启 MySQL 连接方式强制功能
如果数据库配置正确，但连接数据库失败（错误代码 20XX），可使用此方法     
打开   mysql_autoload.php     
找到   define('SQLMODE', 'mysqli');     
替换为 define('SQLMODE', 'mysql');
#####3.如何开启数据库长连接
打开   mysql_autoload.php     
找到   define('LONGSQL', false);     
替换为 define('LONGSQL', true); 
#####4.如何手动修改数据库配置
打开 config.php 并按照里面的注释修改     
切勿使用记事本编辑，否则程序将不能工作
#####5.如何手动导入数据库
打开 /setup/install.template.sql 并按照里面的注释修改
#####6.如何安装新版本
（1）自动更新：前往 检查更新 更新程序即可     
（2）手动更新：直接下载 Zip，删除压缩包内的 config.php ，然后上传到您的网站即可     
另外，每一个大版本都会有一个升级脚本，别忘了运行它     
（文件名一般为 update旧版本to新版本.php ，例如 update1.0to2.0.php）

##许可协议
请访问程序根目录下的 license.html 阅读许可协议     
也可访问该地址阅读许可协议：http://tcsdemo.oschina.mopaas.com/license.html
#####不要尝试去违反协议，我们会把严重违反协议者挂在本页下方的耻辱柱上
#####或者可以前往该地址购买商业授权：http://item.taobao.com/item.htm?id=43814067894

##参与开发
贴吧云签到是一个开放的开源项目，任何人均可参与开发，Pull Request即可提交您修改的代码     
Pull Request和Issue请提交到 Git@OSC 代码库，在其他代码库提交可能不会被处理     
如需加入开发组请联系 @Kenvix 
###主要开发者列表
@Kenvix [kenvix@vip.qq.com]     
@mokeyjay [longting@longtings.com]     
@fyy99 [fyod@vip.qq.com]
###感谢
以下几位反馈/解决了程序的不足之处，特此感谢     
@D丶L [i@v4.hk]     @吟梦 [i@inmeng.xyz]     
@角落里有蛇 [395183830@qq.com]     @VFleaKing [liuhaotian0520@163.com]     
@quericy [quericy@live.com]     @liwanglin12 [i@lwl12.com]     
@superxzr [a457418121@gmail.com]　　　@凹凸曼_m []

##耻辱柱
这里列举了几个严重违反协议且经过提示还不知悔改的家伙     
**有些不要脸的SB就是这样**     
1.多性欲     
  罪行1：发布修改了版权的贴吧云签到，俨然成了他是作者 [查看罪证](http://www.asp300.com/SoftView/11/SoftView_57242.html)     
  罪行2：云签到去版权屡教不改 [它的云签地址](http://baidu.duoxingyu.pw/)     
2."CCGV"     
  罪行1：去版权+辱骂作者：[它的云签地址](http://ccgv.me/)     
  罪行2：换个域名接着去版权：[它的云签地址](http://ccgv.party/)     