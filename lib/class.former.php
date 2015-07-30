<?php
/**
 * 表单生成类
 * @copyright (c) FYY
 * @help  http://git.oschina.net/fyy99/former/wikis/home
 */

class former {

	/**
	 * 新建表单
	 * @param array $set 信息设置
	 * @param array $content 内容
	 */

	public static function create(array $set,array $content){
		//检查信息
		if(empty($set['name']) || empty($set['url']) || empty($set['method']) || !is_array($content)){
			throw new Exception('错误：在使用form::create时，表单必需内容不足<br/><b>提示：</b>$set和$content参数的说明和范例请访问<a href="http://git.oschina.net/fyy99/former/wikis/home#$set和$content参数说明" target="_blank">$set和$content参数说明</a>');
			return;
		}
		$set['th1'] = empty($set['th1']) ? '参数' : $set['th1'];
		$set['th2'] = empty($set['th2']) ? '值' : $set['th2'];
		$set['width'] = empty($set['width']) ? '40%' : $set['width'];

		$form = empty($set['title']) ? '' :'<h3>'.$set['title'].'</h3>';
		if($set['method'] != 3){
			$form .= '<form action="'.$set['url'].'" method="';
			$form .= $set['method'] == 2 ? 'get">' : 'post">';
		}
		$form .= '<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:'.$set['width'].'">'.$set['th1'].'</th>
			<th>'.$set['th2'].'</th>
		</tr>
	</thead>
	<tbody>';
		foreach ($content as $item => $show) {
			switch($show['type']){
				case 'text':
				case 'password':
				case 'email':
				case 'url':
				case 'number':
				case 'hidden':
				case 'checkbox':
					if($show['type'] != 'checkbox'){
						$show['extra'] .= ' class="form-control"';
						$show['value'] = self::fill($item,$set['name']);
					} else {
						if (self::fill($item,$set['name']) == 1) {
							$show['extra'] .= ' checked="checked"';
						}
						$show['text'] = ' '.$show['text'];
						$show['value'] = '1';
					}
					$form .= '<tr><td>'.$show['td1'].'</td><td><input type="'.$show['type'].'" name="'.$item.'" id="'.$item.'" value="'.htmlspecialchars($show['value']).'" '.$show['extra'].'>'.$show['text'].'</td>';
					break;

				case 'select':
					$form .= '<tr><td>'.$show['td1'].'</td><td><select name="'.$item.'" class="form-control">';
					foreach ($show['select'] as $opvalue => $optext) {
						$form .= '<option value="'.$opvalue.'"';
						if (self::fill($item,$set['name']) == $opvalue) {
							$form .= ' selected="selected"';
						}
						$form .= '>'.$optext.'</option>';
					}
					$form .= '</select>'.$show['text'].'</td></tr>';
					break;

				case 'else':
					$form .= $show['html'];
					break;

				default:
					throw new Exception('错误：在使用form::create时，发现意外的type('.$show['type'].')<br/><b>提示：</b>$set和$content参数的说明和范例请访问<a href="http://git.oschina.net/fyy99/former/wikis/home#$set和$content参数说明" target="_blank">$set和$content参数说明</a>');
					break;
			}
		}
		$form .= '</tbody></table></div>';
		$form .= '<br/><input type="submit" class="btn btn-primary" value="提交更改">&nbsp;&nbsp;&nbsp;';
		if($set['method'] != 3) {
			$form .= '</form>';
		} else {
			$form .= '<span id="'.$set['name'].'"><font color="grey"><-单击提交</font></span>';
		}
		return $form;
	}


	/**
	 * 填充表单
	 * @note  必须针对自己的程序对fill进行修改
	 * @param array $item 信息设置
	 * @param array $formname 表单名称，供识别
	 */

	public static function fill($item,$formname){
		$filling = option::get($item);
		return $filling;
	}

}
