<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } if (ROLE != 'admin') { msg('权限不足！'); }

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">插件操作成功</div>';
}

$x=getPlugins();
$plugins = '';
$stat=0;
foreach($x as $key=>$val) {
	$stat++;
	$pluginfo = '';
	if (!empty($val['Url'])) {
		$pluginfo .= '<b><a href="'.$val['Url'].'" target="_blank">'.$val['Name'].'</a></b>';
	} else {
		$pluginfo .= '<b>'.$val['Name'].'</b>';
	}
	if (!empty($val['Description'])) {
		$pluginfo .= '<br/>'.$val['Description'];
	} else {
		$pluginfo .= '<br/>';
	}

	if (!empty($val['Version'])) {
		$pluginfo .= '<br/>版本：'.$val['Version'];
	} else {
		$pluginfo .= '<br/>版本：1.0';
	}

	if (!empty($val['AuthorUrl'])) {
		$authinfo = '<a href="'.$val['AuthorUrl'].'" target="_blank">'.$val['Author'].'</a>';
	} else {
		$authinfo = $val['Author'];
	}

	if (!empty($val['For'])) {
		$fortc = '<br/>适用版本：'.$val['For'];
	} else {
		$fortc = '<br/>适用版本：不限';
	}

	if (in_array($val['Plugin'], unserialize(option::get('actived_plugins')))) {
		$status = '<font color="green">已激活</font> | <a href="setting.php?mod=admin:plugins&dis='.$val['Plugin'].'">禁用插件</a><br/>';
		if (file_exists(SYSTEM_ROOT.'/plugins/'.$val['Plugin'].'/'.$val['Plugin'].'_setting.php')) {
			$status .= '<a href="index.php?mod=admin:setplug&plug='.$val['Plugin'].'">打开插件设置</a>';
		}
	} else {
		$status = '<font color="black">已禁用</font> | <a href="setting.php?mod=admin:plugins&act='.$val['Plugin'].'">激活插件</a><br/>';
	}
	$plugins .= '<tr><td>'.$pluginfo.'</td><td>'.$authinfo.'<br/>'.$val['Plugin'].$fortc.'<td>'.$status.'<br/><a onclick="return confirm(\'你确实要卸载此插件吗？\');" href="setting.php?mod=admin:plugins&uninst='.$val['Plugin'].'" style="color:red;">卸载插件</a></td></tr>'; 
}

doAction('admin_plugins');
?>
<div class="alert alert-info" id="tb_num">当前有 <?php echo sizeof(unserialize(option::get('actived_plugins'))); ?> 个已激活的插件，总共有 <?php echo $stat ?> 个插件 | <a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank">插件商城</a><br/>插件安装方法：直接解包插件并上传到 /plugins/ 即可</div>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:40%">插件信息</th>
			<th style="width:30%">作者/标识符</th>
			<th style="width:30%">状态/操作</th>
		</tr>
	</thead>
	<tobdy>
		<?php echo $plugins; ?>
	</tbody>
</table>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>