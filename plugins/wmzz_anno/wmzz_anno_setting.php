<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
if (ROLE != 'admin') { msg('权限不足!'); }

if (isset($_GET['ok'])) {
  echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>插件设置成功</div>';
}

$doa = unserialize(option::get('wmzz_anno_doa'));
?>

<form action="index.php?plugin=wmzz_anno" method="post">
<b>请输入您的公告： [ 每行一个，支持 HTML ]</b><br/><br/>
<textarea class="form-control" name="wmzz_anno_set" style="height:300px;"><?php echo htmlspecialchars(option::get('wmzz_anno_set')) ?></textarea>
<br/><br/>
<b>公告栏模板： [ 使用 {$anno} 表示公告 ]</b><br/><br/>
<textarea class="form-control" name="wmzz_anno_tpl" style="height:150px;"><?php echo htmlspecialchars(option::get('wmzz_anno_tpl')) ?></textarea>
<br/><br/>
<div class="input-group">
  <span class="input-group-addon">公告栏挂载点 <br/><br/> 按住Ctrl可以多选</span>
  <select class="form-control" name="wmzz_anno_doa[]" multiple="" style="height:200px">
  	<option value="index_1" <?php if (in_array('index_1', $doa )) { echo 'selected'; } ?> >index_1</option>
  	<option value="index_2" <?php if (in_array('index_2', $doa )) { echo 'selected'; } ?> >index_2</option>
  	<option value="index_3" <?php if (in_array('index_3', $doa )) { echo 'selected'; } ?> >index_3</option>
	  <option value="navi_7" <?php if (in_array('navi_7', $doa )) { echo 'selected'; } ?> >navi_7</option>
  	<option value="navi_8" <?php if (in_array('navi_8', $doa )) { echo 'selected'; } ?> >navi_8</option>
  	<option value="navi_9" <?php if (in_array('navi_9', $doa )) { echo 'selected'; } ?> >navi_9</option>
    <option value="navi_9" <?php if (in_array('navi_9', $doa )) { echo 'selected'; } ?> >navi_9</option>
    <option value="login_page_1" <?php if (in_array('login_page_1', $doa )) { echo 'selected'; } ?> >login_page_1</option>
    <option value="login_page_2" <?php if (in_array('login_page_2', $doa )) { echo 'selected'; } ?> >login_page_2</option>
    <option value="login_page_3" <?php if (in_array('login_page_3', $doa )) { echo 'selected'; } ?> >login_page_3</option>
    <option value="reg_page_1" <?php if (in_array('reg_page_1', $doa )) { echo 'selected'; } ?> >reg_page_1</option>
    <option value="reg_page_2" <?php if (in_array('reg_page_2', $doa )) { echo 'selected'; } ?> >reg_page_2</option>
    <option value="reg_page_3" <?php if (in_array('reg_page_3', $doa )) { echo 'selected'; } ?> >reg_page_3</option>
    <option value="header" <?php if (in_array('header', $doa )) { echo 'selected'; } ?> >header</option>
    <option value="footer" <?php if (in_array('footer', $doa )) { echo 'selected'; } ?> >footer</option>
  </select>
</div>
<br/><br/>

<button type="submit" class="btn btn-success">提交更改</button>
</form>
<br/><br/>公告栏 V1.5 // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a>