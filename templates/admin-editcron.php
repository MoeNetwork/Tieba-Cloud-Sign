<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }  if (ROLE != 'admin') { msg('权限不足!'); }
global $i;
?>
<form action="setting.php?mod=admin:cron&add" method="post">
    <?php if(!empty($_GET['edit'])) {
        $edit = htmlspecialchars($_GET['edit']);
        echo '<h3>编辑计划任务：' . $edit  . '</h3><br/>';
    } else {
        echo '<h3>新建计划任务</h3><br/>';
    } ?>
    <div class="table-responsive">
        <?php if(isset($edit)) echo '<input type="hidden" name="edit" value="'.$edit.'">'; ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th style="width:25%">参数</th>
                <th>值</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>任务名称<br/>唯一，不能有中文</td>
                <td><input type="text" name="name" class="form-control" required="" <?php if(isset($edit)) echo 'readonly value="'.$edit.'"'; ?>></td>
            </tr>
            <tr>
                <td>任务文件<br/>基准目录为云签到根目录，开头不需要带/</td>
                <td><input type="text" name="file" class="form-control" required="" <?php if(isset($edit)) echo 'value="'.$i['cron'][$edit]['file'].'"'; ?>></td>
            </tr>
            <tr>
                <td>任务描述<br/>描述这个任务</td>
                <td><textarea class="form-control" name="desc"><?php if(isset($edit)) echo $i['cron'][$edit]['desc']; ?></textarea></td>
            </tr>
            <tr>
                <td>忽略任务</td>
                <td><input type="radio" name="no" value="0" required="" <?php if(!isset($edit) || $i['cron'][$edit]['no'] != '1') { echo 'checked'; }; ?>> 否&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="no" value="1" required="" <?php if(isset($edit) && $i['cron'][$edit]['no'] == '1') { echo 'checked'; }; ?>> 是</td>
            </tr>
            <tr>
                <td>执行间隔<br/>单位为秒，0为始终执行</td>
                <td><input type="number" name="freq" class="form-control" required="" value="<?php if(isset($edit)) {echo $i['cron'][$edit]['freq'];} else {echo '0';} ?>"></td>
            </tr>
            <tr>
                <td>上次执行<br/>Unix 时间戳</td>
                <td><input type="number" name="lastdo" class="form-control" required="" value="<?php if(isset($edit)) {echo $i['cron'][$edit]['lastdo']; } else {echo time();} ?>"></td>
            </tr>
            <tr>
                <td>执行日志<br/><br/>系统会自动写入</td>
                <td><textarea name="log" class="form-control" style="height:100px"><?php if(isset($edit)) echo $i['cron'][$edit]['log']; ?></textarea></td>
            </tr>
            </tbody>
        </table>
    </div>
    <br/><button type="submit" class="btn btn-primary">提交更改</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="button" class="btn btn-default" onclick="location = 'index.php?mod=admin:cron'">取消</button>
</form>
<br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="http://zhizhe8.net" target="_blank">Kenvix</a>  &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a> &amp;  <a href="http://fyy.l19l.com/" target="_blank">FYY</a> 
