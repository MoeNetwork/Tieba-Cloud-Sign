# 百度贴吧云签到
在服务器上配置好就无需进行任何操作便可以实现贴吧的全自动签到。     
配合插件使用还可实现云灌水、点赞、封禁、删帖、审查等功能。     
获取插件，教程，扩展，资料等请前往Wiki [GitHub](https://github.com/MoeNetwork/Tieba-Cloud-Sign/wiki)     
**注意**：Gitee (原Git@osc) 仓库将不再维护，目前唯一指定的仓库为 Github。本项目**没有官方交流群**，如需交流可以直接使用Github的Discussions。**没有商业版本**，目前贴吧云签到由社区共同维护，**不会停止更新**（PR 通常在一天内处理）。


## 使用Docker-Compose快速部署
##### 1.安装Docker
[安装docker](http://get.daocloud.io/#install-docker)，[安装docker-compose](https://hub.docker.com/r/gists/docker-compose-bin)
##### 2.下载`docker-compose.yml`并启动服务
下载 `wget https://raw.githubusercontent.com/MoeNetwork/Tieba-Cloud-Sign/master/docker/docker-compose.yml`
如下载不下来，就直接访问 [这个链接](https://github.com/MoeNetwork/Tieba-Cloud-Sign/blob/master/docker/docker-compose.yml)，参考它修改为你自己的`docker-compose.yml`
开始部署 `docker-compose up -d` 参数`-d`为后台运行。(主要时间消耗在下载，启动不用啥时间)
这里已经包含了`MySQL`部署
##### 3.进入网页配置
启动完之后，直接访问`http://<ip>:8080`，本机就访问`http://127.0.0.1:8080`  
在配置数据库连接的时候，选择`自动导入`即可,不用自己输入。  
然后就配置好了。
##### 4.注意事项
如果你使用Windows，请先去`docker-compose.yml`修改mysql的持久化路径，默认当前目录下的`mysql`文件夹。  
或者直接去掉`volumes`也行。  
映射出来的端口，可以修改`docker-compose.yml`的`8080:8080`。默认`8080`  
`CSRF的设置`在`docker-compose.yml`的`CSRF=true`,默认`true`  
除去docker安装，整个安装流程不超两分钟(网速快)。

## 常见问题解决方案
往往大部分人安装出错第一反应都是：“没错啊，哪里错了，一定是程序错了”
##### 1.如何安装程序
上传此程序到您的网站，然后访问您的网站
##### 2.如何开启 MySQL 连接方式强制功能
如果数据库配置正确，但连接数据库失败（错误代码 20XX），可使用此方法     
打开   mysql_autoload.php     
找到   define('SQLMODE', 'mysqli');     
替换为 define('SQLMODE', 'mysql');
##### 3.如何开启数据库长连接
打开   mysql_autoload.php     
找到   define('LONGSQL', false);     
替换为 define('LONGSQL', true);
##### 4.如何手动修改数据库配置
打开 config.php 并按照里面的注释修改     
切勿使用记事本编辑，否则程序将不能工作
##### 5.如何手动导入数据库
打开 /setup/install.template.sql 并按照里面的注释修改
##### 6.如何安装新版本
（1）自动更新：前往 检查更新 更新程序即可     
（2）手动更新：直接下载 Zip，删除压缩包内的 config.php ，然后上传到您的网站即可     
另外，每一个大版本都会有一个升级脚本，别忘了运行它    
（文件名一般为 update旧版本to新版本.php ，例如 update1.0to2.0.php）      

## 参与开发
贴吧云签到是一个开放的开源项目，任何人均可参与开发，Pull Request即可提交您修改的代码     
Pull Request和Issue请提交到 Github 代码库，在其他代码库提交可能不会被处理     
如需加入开发组请联系 @Kenvix
### 开发者列表
#### 主要
@Kenvix [kenvix@qq.com]     
#### 协助
@mokeyjay [i@mokeyjay.com]     
@fyy99 [fyod@qq.com]
#### 热心贡献者
以下几位反馈/解决了程序的不足之处，特此感谢     
@Ver4 [i@v4.hk]     
@kirainmoe [kotori@wo.cn]     
@VFleaKing [liuhaotian0520@163.com]     
@superxzr [a457418121@gmail.com]         
没有商业版！没有商业版！没有商业版！   
### 耻辱柱
这里列出的是**一小部分**违反协议者的名单
##### 1.@shirakun -- 发布去版权版本
[https://github.com/shirakun/Tieba-Cloud-Sign](https://github.com/shirakun/Tieba-Cloud-Sign)
##### 2."CCGV" -- 去版权、辱骂作者
[http://ccgv.me/](http://ccgv.me/)  [http://ccgv.party/](http://ccgv.party/)
##### 3.多星宇 -- 发布修改版权的版本
[http://www.asp300.com/SoftView/11/SoftView_57242.html](http://www.asp300.com/SoftView/11/SoftView_57242.html)
##### 4.贴吧:国王zhang -- 去版权+辱骂作者+经提醒之后死性不改和删除提醒
[http://52king.cn/tieba](http://52king.cn/tieba)
[http://tieba.baidu.com/p/4822692349](http://tieba.baidu.com/p/4822692349)
