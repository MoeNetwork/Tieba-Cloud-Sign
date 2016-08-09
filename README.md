#百度贴吧云签到
在服务器上配置好就无需进行任何操作便可以实现贴吧的全自动签到。     
配合插件使用还可实现云灌水、点赞、封禁、删帖、审查等功能。     
获取插件，教程，扩展，资料等请前往Wiki [Git@OSC](https://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/home) [GitHub](https://github.com/MoeNetwork/Tieba-Cloud-Sign/wiki)              

##常见问题解决方案
“往往大部分人安装出错第一反应都是：没错啊，哪里错了，一定是程序错了”
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

##参与开发
贴吧云签到是一个开放的开源项目，任何人均可参与开发，Pull Request即可提交您修改的代码     
Pull Request和Issue请提交到 Git@OSC 代码库，在其他代码库提交可能不会被处理     
如需加入开发组请联系 @Kenvix 
###开发者列表
####主要
@Kenvix [kenvix@qq.com]     
####协助
@mokeyjay [longting@longtings.com]     
@fyy99 [fyod@vip.qq.com]
####热心贡献者
以下几位反馈/解决了程序的不足之处，特此感谢     
@96dl [i@v4.hk]     
@kirainmoe [kotori@wo.cn]     
@VFleaKing [liuhaotian0520@163.com]     
@superxzr [a457418121@gmail.com]
#官方自营签到平台
[MoeSign](https://MoeSign.com)
##许可协议与商业版本等
###许可协议
请访问程序根目录下的 license.html 阅读许可协议
###商业版本
若要购买商业版本（￥270），请发送邮件至：kenvix@qq.com     
购买商业版本后可以：修改版权，获取专属插件，后续更新，提供技术支持等     
老商业用户请联系@Kenvix [kenvix@qq.com]获取福利插件库地址
###耻辱柱
这里列出的是**一小部分**违反协议者的名单
#####1.github:@shirakun -- 发布去版权版本
[https://github.com/shirakun/Tieba-Cloud-Sign](https://github.com/shirakun/Tieba-Cloud-Sign)
#####2."CCGV" -- 去版权、辱骂作者
[http://ccgv.me/](http://ccgv.me/)  [http://ccgv.party/](http://ccgv.party/)
#####3.多星宇 -- 发布修改版权的版本
[http://www.asp300.com/SoftView/11/SoftView_57242.html](http://www.asp300.com/SoftView/11/SoftView_57242.html)
###须知
贴吧云签到V5.0可能成为最后的版本     
商业版将继续支持，并提供漏洞修补程序以及接口更改