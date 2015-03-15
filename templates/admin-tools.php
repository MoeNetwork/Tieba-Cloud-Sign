<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足！'); }
global $m;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">应用成功</div>';
}
doAction('admin_tools_1');
?>
<div id="comsys"></div>
<!--Part 1 Primary-->
<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=optim'" class="btn btn-primary" value="优化所有的数据表" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;可清除所有数据表的多余数据

<br/><br/><input type="button" data-toggle="modal" data-target="#RunSql" class="btn btn-primary" value="运行 SQL 语句" style="width:170px">

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=fixdoing'" class="btn btn-primary" value="修复计划任务状态" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;可解决运行计划任务始终提示已经有一个计划任务正在运行中的问题

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=reftable'" class="btn btn-primary" value="扫描空闲的签到表" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;扫描空闲的签到数据表，用户注册时系统会自动扫描

<br/><br/><input type="button" onclick="location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=cron_sign_again'" class="btn btn-primary" value="清空签到重试次数统计" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;[ 当前重试次数：<?php $sign_again = unserialize(option::get('cron_sign_again')); echo  $sign_again_num = empty($sign_again['num']) ? 0 : $sign_again['num'] ?> ] 本操作将清除目前的签到重试次数统计

<br/><br/><input type="button" data-toggle="modal" data-target="#BackupTable" class="btn btn-primary" value="导出数据库" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;导出数据库备份
<!--
<br/><br/><input type="button" onclick="if(confirm('将花费较长时间，请确认此操作')) location = '<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=updatefid'" class="btn btn-primary" value="更新未记录的 FID" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;签到时会自动将没有被缓存的 FID 缓存下来，您也可以手动更新 FID 提高签到效率
-->
<?php doAction('admin_tools_3'); ?>

<br/><br/>
<!--Part 2 Warning-->
<br/><br/><input type="button" data-toggle="modal" data-target="#TruncateTableLabel" class="btn btn-warning" value="清空指定数据表" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;请慎用此功能，一般用于删除无用的分表

<br/><br/><input type="button" data-toggle="modal" data-target="#RemoveTable" class="btn btn-warning" value="删除指定数据表" style="width:170px">&nbsp;&nbsp;&nbsp;&nbsp;请慎用此功能，一般用于删除无用的分表

<?php doAction('admin_tools_2'); ?>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>

<div class="modal fade" id="RunSql" tabindex="-1" role="dialog" aria-labelledby="RunSqlLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">运行 SQL 语句 - 请输入 SQL 语句</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=runsql" onsubmit="$('#runsql_button').attr('disabled',true);" method="post">
      <div class="modal-body">
        支持这些变量：<b>{VAR-PREFIX}</b> - 当前数据表前缀 | <b>{VAR-DBNAME}</b> - 当前数据库名称
        <br/><br/>
        <textarea name="sql" class="form-control" style="height:150px"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary" id="runsql_button">提交并运行</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="BackupTable" tabindex="-1" role="dialog" aria-labelledby="BackupTableLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">导出数据库</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=backup" method="post">
      <div class="modal-body">
        请选择你要导出的表，按住 Ctrl 可以多选：<br/><br/>
        <select class="form-control" name="tab[]" multiple="" style="height:300px">
        <?php $e = $m->query('SHOW TABLES');
        $aaa = 'Tables_in_'.DB_NAME;
        while ($v = $m->fetch_array($e)) {
          echo '<option value="'.$v[$aaa].'" selected>'.$v[$aaa].'</option>';
        }
        ?>
        </select>
        <br/>
        <input type="checkbox" name="zip" value="1" checked> 如果支持，压缩导出的文件 [ Zip ]
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary" id="backup_button">开始导出</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="RemoveTable" tabindex="-1" role="dialog" aria-labelledby="RemoveTableLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">删除指定数据表</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=remtab" onsubmit="$('#remtab_button').attr('disabled',true);" method="post">
      <div class="modal-body">
        <select class="form-control" name="tab">
        <option value="">请选择</option>
        <?php $e = $m->query('SHOW TABLES');
        $aaa = 'Tables_in_'.DB_NAME;
        while ($v = $m->fetch_array($e)) {
        	echo '<option value="'.$v[$aaa].'">'.$v[$aaa].'</option>';
        }
        ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-danger" id="remtab_button">提交更改</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="TruncateTableLabel" tabindex="-1" role="dialog" aria-labelledby="TruncateTableLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">清空指定数据表</h4>
      </div>
      <form action="<?php echo SYSTEM_URL ?>setting.php?mod=admin:tools&setting=truntab" onsubmit="$('#truntab_button').attr('disabled',true);" method="post">
      <div class="modal-body">
        <select class="form-control" name="tab">
        <option value="">请选择</option>
        <?php $e = $m->query('SHOW TABLES');
        $aaa = 'Tables_in_'.DB_NAME;
        while ($v = $m->fetch_array($e)) {
          echo '<option value="'.$v[$aaa].'">'.$v[$aaa].'</option>';
        }
        ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-danger" id="truntab_button">提交更改</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->