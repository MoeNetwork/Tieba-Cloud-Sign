<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } if (ROLE != 'admin') { msg('权限不足！'); }
if (defined('SYSTEM_NO_PLUGIN') && SYSTEM_NO_PLUGIN){
	die('<div class="alert alert-danger">您设置了"SYSTEM_NO_PLUGIN"，所以无法使用任何与插件相关的功能</div>');
}
global $i;
if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">插件操作成功</div>';
}
if (isset($_GET['error_msg'])) {
	echo '<div class="alert alert-danger">'.(empty($_GET['error_msg']) ? '未知的异常' : $_GET['error_msg']).'</div>';
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
	} else {
		$pluginfo .= '<br/>程序版本：1.0';
	}

	if (!empty($val['author']['url'])) {
		$authinfo = '<a href="'.htmlspecialchars($val['author']['url']).'" target="_blank">'.$val['author']['author'].'</a>';
	} else {
		$authinfo = $val['author']['author'];
	}

	if (!empty($val['plugin']['for'])) {
		if(!is_numeric($val['plugin']['for'])) {
			$for = '';
			$fortc = '<br/>适用版本：不限';
		} elseif ($val['plugin']['for'] > SYSTEM_VER) {
			$for = "&ver={$val['plugin']['for']}";
			$fortc = '<br/>适用版本：<font color="red">V'.$val['plugin']['for'].'+</font>';
		} else {
			$for = '';
			$fortc = '<br/>适用版本：V'.$val['plugin']['for'].'+';
		}
	}
    if(isset($i['plugins']['info'][$val['plugin']['id']]['status'])){
        $fortc .= '<br/>加载顺序：<input required type="number" class="form-control input-sm" style="width:50%; display:inline" name="'.$val['plugin']['id'].'" value="'.$val['plugin']['order'].'">';
    }
	if (in_array($val['plugin']['id'], $i['plugins']['all'])) {
		if ($i['plugins']['info'][$val['plugin']['id']]['status'] == '1') {
			$status = '<font color="green">已激活</font> | <a href="setting.php?mod=admin:plugins&dis='.$val['plugin']['id'].'">禁用插件</a>';
			if (isset($val['core']) && ($val['core']['setting'] && $val['view']['setting']) || (isset($val['plugin']['old']) && file_exists(SYSTEM_ROOT . '/plugins/' . $val['plugin']['id'] . '/' . $val['plugin']['id'] . '_setting.php'))) {
				$action .= '<a href="index.php?mod=admin:setplug&plug='.$val['plugin']['id'].'" title="查看设置"><span class="glyphicon glyphicon-cog"></span><span style="padding:0 3px">设置</span></a> ';
			}
			if (isset($val['core']) && $val['core']['show'] && $val['view']['show']) {
				$action .= '<a href="index.php?plugin='.$val['plugin']['id'].'" title="查看页面"><span class="glyphicon glyphicon-eye-open"></span><span style="padding:0 3px">页面</span></a> ';
			}
		} else {
			$status = '<font color="black">已禁用</font> | <a href="setting.php?mod=admin:plugins&act='.$val['plugin']['id'].'">激活插件</a>';
		}
		$action .= '<br/><a onclick="return confirm(\'你想要清除此插件的数据吗？文件会得到保留。\\n'.$val['plugin']['name'].' V'.$val['plugin']['version'].'\');" href="setting.php?mod=admin:plugins&clean='.$val['plugin']['id'].'" style="color:#FF6A00;" title="清除数据"><span class="glyphicon glyphicon-remove"></span><span style="padding:0 3px">重置</span></a> ';
	} else {
		$status = '<font color="#977C00">未安装</font> | <a href="setting.php?mod=admin:plugins&install='.$val['plugin']['id'].$for.'">安装插件</a>';
	}

	$plugins .= '<tr><td>'.$pluginfo.'</td><td>'.$authinfo.'<br/>'.$val['plugin']['id'].$fortc.'<td>'.$status.'<br/>';
	$plugins .= $action.'<a onclick="return confirm(\'你想要要卸载此插件吗？这将删除相关文件。\\n'.$val['plugin']['name'].' V'.$val['plugin']['version'].'\');" href="setting.php?mod=admin:plugins&uninst='.$val['plugin']['id'].'" style="color:red;" title="卸载插件"><span class="glyphicon glyphicon-trash"></span><span style="padding:0 3px">卸载</span></a></td> ';
    $plugins .= '</tr>';
}

doAction('admin_plugins');
?>
<div class="alert alert-info" id="tb_num">当前有 <?php echo count($i['plugins']['all']); ?> 个已安装的插件，<?php echo count($i['plugins']['actived']) ?> 个已激活的插件，总共有 <?php echo count($x) ?> 个插件
<br/>插件手工安装方法：直接解包插件并上传到 /plugins/ 即可
<br/><a href="javascript:;" data-toggle="modal" data-target="#InstallPlugin">上传安装插件</a> | <a href="http://git.oschina.net/kenvix/Tieba-Cloud-Sign/wikis/%E8%B4%B4%E5%90%A7%E4%BA%91%E7%AD%BE%E5%88%B0%E6%8F%92%E4%BB%B6%E5%BA%93" target="_blank">插件库</a> | <a href="javascript:;" onclick="alert('请确保插件目录名和插件入口文件的文件名一致(扩展名除外)<br/>例如，插件目录名是 <i>wmzz_debug</i>，则插件入口文件的文件名应该是 <i>wmzz_debug.php</i><br/><br/>如果您是从Git上下载的插件包，请注意去掉文件夹名称-master之类的字符');" target="_blank">找不到上传的插件？</a>
</div>
<form action="setting.php?mod=admin:plugins&xorder" method="post">
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th>插件信息</th>
			<th>作者/标识符</th>
			<th style="width:20%">状态/操作</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $plugins; ?>
	</tbody>
</table>
</div><input type="submit" class="btn btn-primary" value="提交更改">
</form>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a>  &amp; <a href="http://www.mokeyjay.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>

<div class="modal fade" id="InstallPlugin" tabindex="-1" role="dialog" aria-labelledby="InstallPluginLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">安装插件包</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=install_plugin" onsubmit="$('#installplugin_button').attr('disabled',true);" method="post" enctype="multipart/form-data">
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
