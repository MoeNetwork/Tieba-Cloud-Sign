<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>您的SAE数据库信息</title>
</head>
<body>
<?php
echo 'SAE数据库地址(已含端口)：'.SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT.'<br/><br/>';
echo 'SAE数据库名称：'.SAE_MYSQL_DB.'<br/><br/>';
echo 'SAE数据库用户名：'.SAE_MYSQL_USER.'<br/><br/>';
echo 'SAE数据库密码：'.SAE_MYSQL_PASS.'<br/><br/>';
echo '<br/><br/><br/><br/><br/>为保证站点安全，请立即删除此文件！';
?>
</body>
</html>