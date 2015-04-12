<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } if (ROLE != 'admin') { msg('权限不足！'); }
if (option::get('isapp')) {
	echo '<div class="alert alert-danger">您的主机必须支持写入才能在线安装/更新插件，若不支持，请手工操作</div>';
}

global $i;
//dump($i['plugins']);
if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">插件操作成功</div>';
}
$sp_url = dirname(SUPPORT_URL);
$plugins = '';
$st = 0;
foreach($i['plugins']['desc'] as $key => $val) {
	if($val['plugin']['onsale'] !== true){
		continue;
	}
	$c = new wcurl(SUPPORT_URL.'getplug.php?m=ver&pname='.$val['plugin']['id']);
	$cloud = json_decode($c->exec(),true);
	$c->close();
	if(empty($cloud['version'])){
		continue;
	}
	if (!empty($val['plugin']['url'])) {
		$pluginfo .= '<b><a href="'.$sp_url.'/index.php?mod=see&pname='.$val['plugin']['id'].'" target="_blank">'.$val['plugin']['name'].'</a></b>';
	} else {
		$pluginfo .= '<b>'.$val['plugin']['name'].'</b>';
	}
	if (!empty($val['plugin']['description'])) {
		$pluginfo .= '<br/>'.$val['plugin']['description'];
	} else {
		$pluginfo .= '<br/>';
	}
	if (!empty($val['plugin']['version']) && !empty($i['plugins']['info'][$val['plugin']['id']]['ver'])) {
		$pluginfo .= '<br/>本地版本：'.$val['plugin']['version'];
		if ($i['plugins']['info'][$val['plugin']['id']]['ver'] != $val['plugin']['version']) {
			msg('警告： '.$val['plugin']['name'].' 插件的数据库与文件版本不一致，此时升级存在风险');
		}
		$pluginfo .= ' | 最新版本：'.$cloud['version'];
		if ($cloud['version'] > $val['plugin']['version']) {
			$pluginfo .= ' | <a href="setting.php?mod=admin:cloud&upd='.$val['plugin']['id'].'" onclick="return confirm(\'你确实要升级此插件吗？\\n'.$val['plugin']['name'].'\');">点击升级到最新版本</a>';
		}
	} else {
		$pluginfo .= '<br/><font color="red">版本异常</font>';
	}
	if (!empty($val['author']['url'])) {
		$authinfo = '<a href="'.htmlspecialchars($val['author']['url']).'" target="_blank">'.$val['author']['author'].'</a>';
	} else {
		$authinfo = $val['author']['author'].'<br/>';
	}
	$st++;
	$plugins .= '<tr><td>'.$pluginfo.'</td><td>'.$authinfo.'<br/>'.$val['plugin']['id'].$fortc.'<td>'.$status.'<br/>';
    $plugins .= '</tr>';
}

?>
<div class="alert alert-info" id="tb_num">当前有 <?php echo count($i['plugins']['all']); ?> 个已安装的插件，<?php echo count($i['plugins']['actived']) ?> 个已激活的插件。其中已在 <a href="<?php echo $sp_url; ?>" target="_blank">产品中心</a> 上架的有 <?php echo $st ?> 个。</div>
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:70%">插件信息</th>
			<th style="width:30%">作者/标识符</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $plugins; ?>
	</tbody>
</table>
</div>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> @ <a href="http://www.stus8.com" target="_blank">StusGame GROUP</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>