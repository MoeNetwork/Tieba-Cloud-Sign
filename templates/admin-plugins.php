<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
$x=getPlugins();
$plugins = '';
foreach($x as $key=>$val) {
	if ($val['Setting'] != false) {
		$set = '<a href="'.$val['Setting'].'">插件设置</a>';
	} else { 
		$set = '';
	}
	$plugins .= '<tr><td>[V'.$val['Version'].'] <b><a href="'.$val['Url'].'" target="_blank">'.$val['Name'].'</a></b><br/>'.$val['Description'].'</td><td><a href="'.$val['AuthorUrl'].'" target="_blank">'.$val['Author'].'</a><br/>'.$val['Plugin'].'<td></td><td>'.$set.'</td></tr>'; 
}

doAction('admin_plugins');
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:35%">插件信息</th>
			<th style="width:25%">作者/标识符</th>
			<th style="width:15%">状态/操作</th>
			<th style="width:25%">操作</th>
		</tr>
	</thead>
	<tobdy>
		<?php echo $plugins; ?>
	</tbody>
</table>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>