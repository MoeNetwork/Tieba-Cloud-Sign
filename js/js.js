document.write('<button class="btn btn-primary btn-lg" id="wabutton" style="display:none;" data-toggle="modal" data-target="#alert_modal"></button><div class="modal fade" id="alert_modal" tabindex="-1" role="dialog" aria-labelledby="watitle" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><span class="modal-title" style="font-size:21px;" id="watitle">提示信息</span></div><div class="modal-body" id="watext">未指定的异常</div><div class="modal-footer"><button type="button" class="btn btn-primary" id="waclose" data-dismiss="modal" style="width:100px;">关闭</button></div></div></div></div>');
function alert(text,title) {
while(/\n/.test(text) > 0) {
	text = text.replace(/\n/,'<br/>');
}
if(text != null) {document.getElementById('watext').innerHTML = text; } else { document.getElementById('watext').innerHTML = '未指定的异常'; }
if(title != null) {document.getElementById('watitle').innerHTML = title;} else { document.getElementById('watitle').innerHTML = '提示信息'; }
$("#wabutton").click();
}