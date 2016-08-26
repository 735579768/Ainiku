<?php
// array(
// 	'field'   => 'sex',
// 	'type'    => 'radio',
// 	'name'    => 'sex',
// 	'title'   => '性别',
// 	'note'    => '',
// 	'extra'   => array(
// 		1 => '男',
// 		2 => '女',
// 	),
// 	'value'   => 1,
// 	'is_show' => 3,
// ),
/**
 *系统共用生成表单列表
 */
function create_form($fieldarr, $data = array()) {
	$formstr = '';
	foreach ($fieldarr as $key => $value) {
		$field      = $value['field'];
		$type       = $value['type'];
		$name       = $value['name'];
		$title      = $value['title'];
		$note       = $value['note'];
		$extra      = $value['extra'];
		$setvalue   = $value['value'];
		$is_show    = $value['is_show'];
		$is_require = $value['is_require'];

		//验证表单
		$data_reg = $value['data_reg'];
		$data_ok  = $value['data_ok'];
		$data_ts  = $value['data_ts'];
		$data_err = $value['data_err'];
		//循环表单
		switch ($type) {
		case 'number':
			$yzstr = '';
			if ($data_reg) {
				$yzstr .= <<<eot
				  class="form-control input-small autoyz" data-reg="{$data_reg}" data-ts="{$data_ts}" data-ok="{$data_ok}" data-err="{$data_err}"
eot;
			} else {
				$yzstr .= <<<eot
				class="form-control input-small"
eot;

			}
			$formstr .= <<<eot
            <input type="text" {$yzstr}  placeholder="请输入{$field['title']}" name="{$field['name']}" value="{$field['data']}" />
eot;
			break;

		default:

			break;
		}
	}
}