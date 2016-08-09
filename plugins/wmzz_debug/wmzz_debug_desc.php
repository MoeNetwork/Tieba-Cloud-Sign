<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
//没有标注必填的，都是选填，但是按照规范，请保留该键，只需将值留空即可
return array(
	'plugin' => array(
		'name'        => '调试信息',            //插件名称，必填
		'version'     => '2.0',                 //插件版本号
		'description' => '在底部显示调试信息，并为开发新架构插件提供范例和详细的注释',  //插件描述
		'onsale'      =>  true,                 //bool 插件是否已在产品中心上架
		'url'         => 'http://zhizhe8.net',  //插件地址，比如哪里可以下载到这个插件
		'for'         => 'all',                 //适用的云签到版本，all为所有版本，版本后面跟+表示适用于该版本或更高版本，如V4.0+
        'forphp'      => 'all'                  //适用的PHP版本，如果定义了，系统就在安装和激活时进行版本对比，如果版本低于forphp，自动禁止下一步操作，all为所有版本
	),
	'author' => array(
		'author'      => 'Kenvix',            //作者名称
		'email'       => 'kenvix@vip.qq.com',   //作者邮箱
		'url'         => 'http://zhizhe8.net'   //作者的个人网站
	),
	'view'   => array(
		//以下设置均只影响插件列表页面是否有对应按钮
		'setting'     => true,  //bool 插件是否有设置页面，必填
		'show'        => true,  //bool 插件是否有展示页面，必填
		'vip'         => false, //bool 插件是否有只给VIP看的页面，必填
		'private'     => false, //bool 插件是否有只给管理员看的页面，必填
		'public'      => false, //bool 插件是否有给任何人（包括未登录的）看的页面，必填
		'update'      => true, //bool 插件如果有新版本，是否在插件列表页面显示升级按钮
	),
	'page'   => array(
		//规定插件有哪些自定义页面，不需要自定义页面可留空
		//自定义页面访问方式：index.php?mod=view:插件名:自定义页面名
		//程序将自动在插件目录下寻找并加载 view_自定义页面名.php
		//任何人都能查看自定义页面，包括未登录的用户，因此你必须自己写好权限控制
		'phpinfo' //定义一个名为phpinfo的自定义页面，位于/plugins/wmzz_debug/view_phpinfo.php
	)
);