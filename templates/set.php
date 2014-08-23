<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}

function addset($name,$type,$x,$other = '',$text = '') {
	if ($type == 'checkbox') {
		if (option::uget($x) == 1) {
			$other .= ' checked="checked"';
		}
		$value = '1';
	} else {
		$value = option::uget($x);
	}
	echo '<tr><td>'.$name.'</td><td><input type="'.$type.'" name="'.$x.'" value="'.$value.'" '.$other.'>'.$text.'</td>';
}
?><form action="setting.php?mod=set" method="post">
<?php doAction('set_1'); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:35%">参数</th>
			<th style="width:65%">值</th>
		</tr>
	</thead>
	<tbody>
		<?php
		addset('签到模式','radio','mode','disabled checked',' 三重签到');
		doAction('set_2');
		?>
	</tbody>
</table><input type="submit" class="btn btn-primary" value="提交更改">
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a>