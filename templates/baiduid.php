<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i;
global $m;
?>

<!-- MODAL -->
<div class="modal fade" id="DownloadPluginModal" tabindex="-1" role="dialog" aria-labelledby="DownloadPluginModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">请选择插件下载方式</h4>
      </div>
      <div class="modal-body">
        方式1：<a href="https://chrome.google.com/webstore/detail/editthiscookie/fngmhnnpilhplaeedifhccceomclgfbg" target="_blank">点击从谷歌应用商店安装</a><br/><br/>
        方式2：<a href="http://git.oschina.net/kenvix/Tieba-Cloud-Sign/attach_files" target="_blank">点击从 OSChina 下载 [ 选择 Edit This Cookie.crx ]</a><br/><br/>
        方式3：<a href="http://pan.baidu.com/s/1nt4uCGx" target="_blank">点击从百度网盘下载手动安装</a><br/><br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- END MODAL -->

<!-- NAVI -->
<ul class="nav nav-tabs" id="PageTab">
  <li class="active"><a href="#adminid" data-toggle="tab" onclick="$('#newid2').css('display','none');$('#newid').css('display','none');$('#adminid').css('display','');">管理账号</a></li>
  <?php if (option::get('bduss_num') != '-1') { ?><li><a href="#newid" data-toggle="tab" onclick="$('#newid').css('display','');$('#adminid').css('display','none');$('#newid2').css('display','none');">自动绑定</a></li>
  <li><a href="#newid2" data-toggle="tab" onclick="$('#newid2').css('display','');$('#adminid').css('display','none');$('#newid').css('display','none');">手动绑定</a></li><?php } ?>
</ul>
<br/>
<!-- END NAVI -->

<!-- PAGE1: ADMINID-->
<div class="tab-pane fade in active" id="adminid">
<a name="#adminid"></a>
<?php if (option::get('bduss_num') == '-1' && ROLE != 'admin') { ?>
<div class="alert alert-danger" role="alert">
  本站禁止绑定百度账号，当前已绑定 <?php echo sizeof($i['user']['bduss']) ?> 个账号，PID 即为 账号ID
</div>
<?php } elseif(empty($i['user']['bduss'])) { ?>
<div class="alert alert-warning">
  无法显示列表，因为当前还没有绑定任何百度账号
  <br/>若要绑定账号，请点击上方的 [ 绑定新账号 ]
  <?php if (option::get('bduss_num') != '0') echo '，您最多能够绑定 '.option::get('bduss_num').' 个账号'; ?>
</div>
<?php } else { ?>
<div class="alert alert-info">
  当前已绑定 <?php echo sizeof($i['user']['bduss']) ?> 个账号，PID 即为 账号ID
  <?php if (option::get('bduss_num') != '0' && ROLE != 'admin') echo '，您最多能够绑定 '.option::get('bduss_num').' 个账号'; ?>
</div>
<?php } if(!empty($i['user']['bduss'])) { ?>

<table class="table table-striped">
  <thead>
    <tr>
      <th style="width:10%">PID</th>
      <th style="width:90%">BDUSS Cookie</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
   <?php
    foreach ($i['user']['bduss'] as $key => $value) {
      echo '<tr><td>'.$key.'</td><td><input type="text" class="form-control" readonly value="'.$value.'"></td><td><a class="btn btn-default" href="setting.php?mod=baiduid&del='.$key.'">解除绑定</a></td></tr>';
    }
   ?>
  </tbody>
</table>
<?php } ?>
</div>
<!-- END PAGE1 -->

<!-- PAGE2: NEWID -->
<div class="tab-pane fade" id="newid" style="display:none">
  <script type="text/javascript">
  function addbdid_getcode() {
    $('#addbdid_submit').attr('disabled',true);
    $('#addbdid_prog').css({"display":""});
    $('.addbdis_text').html('正在拉取验证信息...');
    $('#addbdid_pb').css({"width":"25%"});
    $(document).ready(function(){
      $.ajax({
        url:"ajax.php?mod=baiduid:getverify",
        async:true,
        dataType:"html",
        type:'POST',
        data:{
          'bd_name': document.getElementById('bd_name').value ,
          'bd_pw': document.getElementById('bd_pw').value
        },
        complete: function(x,y) {
          $('#addbdid_prog').css({"display":"none"});
          $('#addbdid_submit').removeAttr('disabled');
        },
        success: function(x) {
          $('#addbdid_form').attr('onsubmit','addbdid_getbduss();return false;');
          $('#addbdid_ver').html(x);
        },
        error: function(x) {
        	alert('(1/3)操作失败，发生未知错误。错误已记录到浏览器的控制台，请联系管理员协助解决此问题<br/>若要重新尝试绑定账号，请刷新此页面');
        	if (console) {
        		console.log(x);
        	}
        }
     });
    });
  }
  function addbdid_getbduss() {
    $('#addbdid_submit').attr('disabled',true);
    $('#addbdid_prog').css({"display":""});
    $('.addbdis_text').html('正在绑定账号信息...');
    $('#addbdid_pb').css({"width":"50%"});
    $(document).ready(function(){
      $.ajax({
        url:"ajax.php?mod=baiduid:bdid",
        async:true,
        dataType:"json",
        type:'POST',
        data:{
          'bd_name': document.getElementById('bd_name').value ,
          'bd_pw': document.getElementById('bd_pw').value ,
          'bd_v': document.getElementById('bd_v').value ,
          'vcodestr': document.getElementById('vcodeStr').value ,
        },
        success: function(x) {
          if (x.error == '0') {
	        $('.addbdis_text').html('正在完成绑定...');
	    	$('#addbdid_pb').css({"width":"75%"});
	    	$.ajax({
	    		url:'setting.php?mod=baiduid&bduss=' + x.bduss ,
	    		async:true,
	    		dataType:"html",
        		type:'GET',
        		success: function(x) {
        			$('#addbdid_form').attr('onsubmit','addbdid_getcode();return false;');
			        $('.addbdis_text').html('绑定完成...');
			        $('#addbdid_pb').css({"width":"100%"});
			        $('#addbdid_submit').removeAttr('disabled');
			        $("#addbdid_prog").fadeOut(4000);
			        alert('百度账号绑定完成，刷新本页后可以看到效果');
        		},
        		error: function(x) {
        			$('#addbdid_submit').removeAttr('disabled');
		        	alert('(3/3)操作失败，发生未知错误。错误已记录到浏览器的控制台，请联系管理员协助解决此问题<br/>若要重新尝试绑定账号，请刷新此页面');
		        	if (console) {
		        		console.log(x);
		        	}
        		}
        	});
	      } else {
	      	$('#addbdid_submit').removeAttr('disabled');
	      	$('#addbdid_form').attr('onsubmit','addbdid_getcode();return false;');
	      	alert('绑定百度账号失败，' + x.msg);
	      	addbdid_getcode();
	      }
        },
        error: function(x) {
        	$('#addbdid_submit').removeAttr('disabled');
        	alert('(2/3)操作失败，发生未知错误。错误已记录到浏览器的控制台，请联系管理员协助解决此问题<br/>若要重新尝试绑定账号，请刷新此页面');
        	if (console) {
        		console.log(x);
        	}
        }
     });
    });
  }
</script>
<div id="addbdid_prog" style="display:none">
  <b><span class="addbdis_text">正在拉取验证信息...</span></b><br/><br/>
  <div class="progress">
    <div class="progress-bar progress-bar-striped active" id="addbdid_pb" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
    </div>
  </div>
</div>
<a name="#newid"></a>
<form method="post" id="addbdid_form" onsubmit="addbdid_getcode();return false;">
<div class="input-group">
  <span class="input-group-addon">百度账号</span>
  <input type="text" class="form-control" id="bd_name" placeholder="你的百度账户名，建议填写邮箱" required>
</div>

<br/>

<div class="input-group">
  <span class="input-group-addon">百度密码</span>
  <input type="password" class="form-control" id="bd_pw" placeholder="你的百度账号密码" required>
</div>
<br/><div id="addbdid_ver"></div>
<input type="submit" id="addbdid_submit" class="btn btn-primary" value="点击绑定">
</form>
</div>
<!-- END PAGE2 -->

<!-- PAGE3: NEWID2 -->
<div class="tab-pane fade" id="newid2" style="display:none">
<form action="setting.php" method="get">
<div class="input-group">
  <input type="hidden" name="mod" value="baiduid">
  <span class="input-group-addon">输入BDUSS</span>
  <input type="text" class="form-control" name="bduss">
  <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="点击提交"></span>
</div>
</form>
<br/><br/><b>以下是贴吧账号手动绑定教程：</b><br/><br/>
<div class="panel panel-default">
	<div class="panel-heading" onclick="$('#win_bduss').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 点击查看在 Windows 系统下的绑定方法</h3></div>
	<div class="panel-body" id="win_bduss" style="display:none">
	    1.<a href="source/doc/get_bduss.exe">点击此处下载 贴吧BDUSS获取器</a>
	    <br/><br/>2.请确保已安装了 <b>.Net Framework 3.0</b> [ 正版Win7已自带 ]
	    <br/><br/>3.请运行此程序，按照要求输入账号信息，然后将获取到的 BDUSS 填入上面的表单即可
	</div>
</div>
<br/>
<div class="panel panel-default">
	<div class="panel-heading" onclick="$('#chrome_bduss').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 点击查看在 Chrome 浏览器下的绑定方法</h3></div>
	<div class="panel-body" id="chrome_bduss" style="display:none">
	    1.使用 Chrome 或 Chromium 内核的浏览器
		<br/><br/>2.<a href="javascript:;" onclick="$('#DownloadPluginModal').modal('show');">安装插件 [ EditThisCookie ]，点击下载 </a>
		<br/><br/>3.打开百度首页 <a href="http://www.baidu.com" target="_blank">http://www.baidu.com/</a>
		<br/><br/>4.确保已经登录百度，然后按下 F12 ( 或右键点击审查元素 )
		<br/><br/>3.按下图操作：( 点图片查看大图 )
		<br/><br/><a href="<?php echo SYSTEM_URL ?>source/doc/baiduid.jpg" target="_blank"><img src="<?php echo SYSTEM_URL ?>source/doc/baiduid.jpg" width="90%" height="90%"></a>
		<br/><br/>4.输入复制到的 BDUSS 到上面的表单即可
	</div>
</div>
</div>
<!-- END PAGE3 -->

<br/><br/><br/><br/><br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> By <a href="http://zhizhe8.net" target="_blank">无名智者</a>