<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i;
global $m;
?>



<!-- NAVI -->
<ul class="nav nav-tabs" id="PageTab">
  <li class="active"><a href="#adminid" data-toggle="tab" onclick="$('#newid2').css('display','none');$('#newid').css('display','none');$('#adminid').css('display','');">管理账号</a></li>
  <?php if (option::get('bduss_num') != '-1' || ISVIP) { ?><li><a href="#newid" data-toggle="tab" onclick="$('#newid').css('display','');$('#adminid').css('display','none');$('#newid2').css('display','none');">扫码绑定</a></li>
  <li><a href="#newid2" data-toggle="tab" onclick="$('#newid2').css('display','');$('#adminid').css('display','none');$('#newid').css('display','none');">手动绑定</a></li><?php } ?>
</ul>
<br/>
<!-- END NAVI -->

<!-- PAGE1: ADMINID-->
<div class="tab-pane fade in active" id="adminid">
<a name="#adminid"></a>
<?php if (option::get('bduss_num') == '-1' && ISVIP != true) { ?>
<div class="alert alert-danger" role="alert">
  本站禁止绑定百度账号，当前已绑定 <?php echo sizeof($i['user']['bduss']) ?> 个账号，PID 即为 账号ID
</div>
<?php } elseif(empty($i['user']['bduss'])) { ?>
<div class="alert alert-warning">
  无法显示列表，因为当前还没有绑定任何百度账号
  <br/>若要绑定账号，请点击上方的 [ 绑定新账号 ]
  <?php if (option::get('bduss_num') != '0' && ISVIP != true) echo '，您最多能够绑定 '.option::get('bduss_num').' 个账号'; ?>
</div>
<?php } else { ?>
<div class="alert alert-info">
  当前已绑定 <?php echo sizeof($i['user']['bduss']) ?> 个账号，PID 即为 账号ID
  <?php if (option::get('bduss_num') != '0' && ISVIP != true) echo '，您最多能够绑定 '.option::get('bduss_num').' 个账号'; ?>
。</div>
<?php } if(!empty($i['user']['bduss'])) { ?>
<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr>
      <th>PID</th>
      <th style="width:25%">百度名称</th>
      <th style="width:65%">BDUSS Cookie</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
   <?php
    foreach ($i['user']['bduss'] as $key => $value) {
      echo '<tr><td>'.$key.'</td>';
      $name = empty($i['user']['baidu'][$key]) ? '未记录百度ID' : $i['user']['baidu'][$key];
      if($name == '[E]') $name='<font color="red">已失效</font>';
      //echo '<td><a href="setting.php?mod=baiduid&reget='.$key.'"">'.$name.'</a></td>';
      echo '<td>'.$name.'</td>';
      echo '<td><input type="text" class="form-control" readonly value="'.$value.'"></td><td><a class="btn btn-default" href="setting.php?mod=baiduid&del='.$key.'">解绑</a></td></tr>';
    }
   ?>
  </tbody>
</table>
</div>
<?php } ?>
</div>
<!-- END PAGE1 -->

<!-- PAGE2: NEWID -->
<div class="tab-pane fade" id="newid" style="display:none">
  <script type="text/javascript">
      $(document).ready(function(){
          $("#addbdid_form").submit(function(e){
              $('#addbdid_submit').attr('disabled',true);
              $('#addbdid_prog').css({"display":""});
              $('.addbdis_text').html('正在拉取验证信息...');
              $('#addbdid_pb').css({"width":"25%"});
              $.ajax({
                  url:"ajax.php?mod=baiduid:qrlogin",
                  async:true,
                  dataType:"json",
                  type:'POST',
                  data: {
                      'sign': $('#sign').val() ,
                  },
                  complete: function(x,y) {
                      $('#addbdid_submit').removeAttr('disabled');
                  },
                  success: function(x) {
                      if(x.error == 0) {
                          $('#addbdid_msg').html('成功绑定百度账号：' + x.name);
                          $('#addbdid_pb').css({"width":"100%"});
                          $('#addbdid_prog').fadeOut(500);
                      } else {
                          $('#addbdid_msg').html(x.msg);
                      }
                  },
                  error: function(x) {
                      $('#addbdid_prog').fadeOut(500);
                      $('#addbdid_msg').html('操作失败，未知错误。这可能是网络原因所致，请刷新重试#2');
                  }
              });
          });
      });
  /*
  function addbdid_getbduss() {

    $(document).ready(function(){

    });
  }
  */
</script>
<div id="addbdid_prog" style="display:none">
  <b><span class="addbdis_text">正在拉取验证信息...</span></b><br/><br/>
  <div class="progress">
    <div class="progress-bar progress-bar-striped active" id="addbdid_pb" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
    </div>
  </div>
</div>
<a name="#newid"></a>
<div class="alert alert-warning" role="alert" id="addbdid_msg">如果您多次尝试绑定失败，不妨试试 <a href="https://bduss.nest.moe" target="_blank">手动获取</a> 吧！</div>
<form method="post" id="addbdid_form" onsubmit="return false;">
  <?php $login_info = misc::get_login_qrcode(); ?>
  <img src="//<?=$login_info["imgurl"] ?>" alt="qrcode" class="thumbnail center-block">
  <div id="addbdid_ver" style="display: none">
    <input type="hidden" id="sign" value="<?=$login_info["sign"] ?>">
  </div>
  <a href="https://wappass.baidu.com/wp/?qrlogin=&sign=<?=$login_info["sign"] ?>" class="btn btn-default btn-block" target="_blank">网页授权</a>
  <input type="submit" id="addbdid_submit" class="btn btn-primary btn-block" value="点击绑定">
</form>
<br/><br/>
<div class="panel panel-default">
    <div class="panel-heading" onclick="$('#win_bduss').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 提示</h3></div>
    <div class="panel-body" id="win_bduss">
    <ul>
      <li>可直接点击 "网页授权" 并在新打开页授权，无需客户端扫码，非移动设备的UA可能会导致无法打开</li>
      <li>若二维码失效请刷新本页以刷新二维码</li>
    </ul>
    </div>
</div>
</div>

<!-- END PAGE2 -->

<!-- PAGE3: NEWID2 -->
<div class="tab-pane fade" id="newid2" style="display:none">
<form action="setting.php" method="get">
<div class="input-group">
  <input type="hidden" name="mod" value="baiduid">
  <span class="input-group-addon">输入BDUSS</span>
  <input type="text" class="form-control" name="bduss" id="bduss_input">
  <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="点击提交"></span>
</div>
</form>

<br/><br/><b>以下是贴吧账号手动绑定教程：</b><br/><br/>
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
<?php doAction('baiduid'); ?>
<br/><br/><br/><br/><br/><br/><?php echo SYSTEM_FN ?> V<?php echo SYSTEM_VER  . ' ' . SYSTEM_VER_NOTE ?> // 作者: <a href="https://kenvix.com" target="_blank">Kenvix</a> &amp; <a href="http://www.mokeyjay.com/" target="_blank">mokeyjay</a> &amp; <a href="http://fyy1999.lofter.com/" target="_blank">FYY</a> &amp; <a href="http://www.stusgame.com/" target="_blank">StusGame</a>
