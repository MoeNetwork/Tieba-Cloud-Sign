<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i;
global $m;
?>



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
      <th>PID</th>
      <?php if (option::get('baidu_name') == '1') {
        echo '<th style="width:25%">百度名称</th>';
      }
      ?>
      <th style="width:65%">BDUSS Cookie</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
   <?php
    foreach ($i['user']['bduss'] as $key => $value) {
      echo '<tr><td>'.$key.'</td>';
      if (option::get('baidu_name') == '1') {
          echo '<td>'.$i['user']['baidu'][$key].'</td>';
      }
      echo '<td><input type="text" class="form-control" readonly value="'.$value.'"></td><td><a class="btn btn-default" href="setting.php?mod=baiduid&del='.$key.'">解绑</a></td></tr>';
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
<div class="alert alert-warning" role="alert">由于百度强制开启了异地登陆验证，很可能导致自动绑定失败。如果你确定输入无误但却提示账号密码错误，请使用<a href="#newid2" data-toggle="tab" onclick="$('#newid2').css('display','');$('#adminid').css('display','none');$('#newid').css('display','none');$('#PageTab li').removeClass('active').last().addClass('active');">手动绑定</a></div>
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
	<div class="panel-heading" onclick="$('#win_bduss').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 在 Windows 系统下的绑定方法</h3></div>
	<div class="panel-body" id="win_bduss">
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
		<br/><br/>2.打开百度首页 <a href="http://www.baidu.com" target="_blank">http://www.baidu.com/</a>
    <br/><br/>3.右键，点击 <b>查看网页信息</b>
		<br/><br/>4.确保已经登录百度，然后点击 <b>显示 Cookie 和网站数据</b>
		<br/><br/>5.如图，依次展开 <b>passport.baidu.com</b> -> <b>Cookie</b> -> <b>BDUSS</b>
		<br/><br/><a href="source/doc/baiduid.png" target="_blank"><img src="source/doc/baiduid.png"></a>
		<br/><br/>6.按下 Ctrl+A 全选，然后复制并输入到上面的表单即可
    <br/><br/>请注意，一旦退出登录，可能导致 BDUSS 失效，因此建议在隐身模式下登录
	</div>
</div>
</div>
<!-- END PAGE3 -->

<br/><br/><br/><br/><br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER ?> // 作者: <a href="http://zhizhe8.net" target="_blank">无名智者</a> &amp; <a href="http://www.longtings.com/" target="_blank">mokeyjay</a>