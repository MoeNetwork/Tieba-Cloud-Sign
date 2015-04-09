<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } if (ROLE != 'admin') { msg('权限不足！'); }
global $i;
if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">插件操作成功</div>';
}

$x       = getPlugins();
$plugins = '';
foreach($x as $key => $val) {
	$pluginfo = '';
	$action   = '';
	if (!empty($val['plugin']['url'])) {
		$pluginfo .= '<b><a href="'.htmlspecialchars($val['plugin']['url']).'" target="_blank">'.$val['plugin']['name'].'</a></b>';
	} else {
		$pluginfo .= '<b>'.$val['plugin']['name'].'</b>';
	}
	if (!empty($val['plugin']['description'])) {
		$pluginfo .= '<br/>'.$val['plugin']['description'];
	} else {
		$pluginfo .= '<br/>';
	}

	if (!empty($val['plugin']['version'])) {
		$pluginfo .= '<br/>程序版本：'.$val['plugin']['version'];
		if (!empty($i['plugins']['info'][$val['plugin']['id']]['ver'])) {
			$pluginfo .= ' | 已安装版本：' . $i['plugins']['info'][$val['plugin']['id']]['ver'];
		}
		if (isset($i['plugins']['info'][$val['plugin']['id']]['ver']) && version_compare($i['plugins']['info'][$val['plugin']['id']]['ver'], $val['plugin']['version']) == -1 && $val['view']['update']) {
			$pluginfo .= ' | <a href="setting.php?mod=admin:plugins&upd='.$val['plugin']['id'].'" onclick="return confirm(\'你确实要升级此插件吗？\\n'.$val['plugin']['name'].'\');">点击升级到最新版本</a>';
		}
	} else {
		$pluginfo .= '<br/>程序版本：1.0';
	}

	if (!empty($val['author']['url'])) {
		$authinfo = '<a href="'.htmlspecialchars($val['author']['url']).'" target="_blank">'.$val['author']['author'].'</a>';
	} else {
		$authinfo = $val['author']['author'];
	}

	if (!empty($val['plugin']['for'])) {
		if($val['plugin']['for'] == '不限' || $val['plugin']['for'] == 'all') {
			$for = '';
			$fortc = '<br/>适用版本：不限';
		} elseif($val['plugin']['for'] > SYSTEM_VER) {
			$for = "&ver={$val['plugin']['for']}";
			$fortc = '<br/>适用版本：<font color="red">V'.$val['plugin']['for'].'+</font>';
		} else {
			$for = '';
			$fortc = '<br/>适用版本：V'.$val['plugin']['for'].'+';
		}
	}

	if (in_array($val['plugin']['id'], $i['plugins']['all'])) {
		if ($i['plugins']['info'][$val['plugin']['id']]['status'] == '1') {
			$status = '<font color="green">已激活</font> | <a href="setting.php?mod=admin:plugins&dis='.$val['plugin']['id'].'">禁用插件</a><br/>';
			if ($val['core']['setting'] && $val['view']['setting']) {
				$status .= '<a href="index.php?mod=admin:setplug&plug='.$val['plugin']['id'].'">打开插件设置</a>';
				$action .= '<a href="index.php?mod=admin:setplug&plug='.$val['plugin']['id'].'" title="查看设置"><span class="glyphicon glyphicon-cog"></span></a> ';
			}
			if ($val['core']['show'] && $val['view']['show']) {
				$action .= '<a href="index.php?plugin='.$val['plugin']['id'].'" title="查看页面"><span class="glyphicon glyphicon-eye-open"></span></a> ';
			}
		} else {
			$status = '<font color="black">已禁用</font> | <a href="setting.php?mod=admin:plugins&act='.$val['plugin']['id'].'">激活插件</a><br/>';
		}
	} else {
		$status = '<font color="#977C00">未安装</font> | <a href="setting.php?mod=admin:plugins&install='.$val['plugin']['id'].$for.'">安装插件</a><br/>';
	}

	$plugins .= '<tr><td>'.$pluginfo.'</td><td>'.$authinfo.'<br/>'.$val['plugin']['id'].$fortc.'<td>'.$status.'<br/>';
	$plugins .= $action.'<a onclick="return confirm(\'你确实要卸载此插件吗？\\n'.$val['plugin']['name'].'\');" href="setting.php?mod=admin:plugins&uninst='.$val['plugin']['id'].'" style="color:red;" title="卸载"><span class="glyphicon glyphicon-trash"></span></a></td></tr>'; 
}

doAction('admin_plugins');
?>
<div class="alert alert-info" id="tb_num">当前有 <?php echo count($i['plugins']['all']); ?> 个已安装的插件，<?php echo count($i['plugins']['actived']) ?> 个已激活的插件，总共有 <?php echo count($x) ?> 个插件
<br/>插件手工安装方法：直接解包插件并上传到 /plugins/ 即可
<?php if (option::get('isapp')) {
	echo ' | 您已在全局设置中指定环境为引擎，卸载插件将不会删除插件文件';
}
?>
<br/><a href="javascript:;" data-toggle="modal" data-target="#InstallPlugin">点击这里上传安装插件</a> | <a href="http://www.stus8.com/forum.php?mod=forumdisplay&fid=163&filter=sortid&sortid=13" target="_blank">插件商城</a>
</div>
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:50%">插件信息</th>
			<th style="width:30%">作者/标识符</th>
			<th style="width:30%">状态/操作</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $plugins; ?>
	</tbody>
</table>
</div>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> @ <a href="http://www.stus8.com" target="_blank">StusGame GROUP</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>

<div class="modal fade" id="InstallPlugin" tabindex="-1" role="dialog" aria-labelledby="InstallPluginLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">安装插件包</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:plugins:install" onsubmit="$('#installplugin_button').attr('disabled',true);" method="post" enctype="multipart/form-data">
      <div class="modal-body">
        请浏览插件包：( ZIP格式 )
        <br/><br/><input type="file" name="plugin" required accept="application/zip" style="width:100%">
        <br/><br/>您的主机必须支持写入才能安装插件，若不支持，请手工安装
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary" id="installplugin_button">上传插件</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->