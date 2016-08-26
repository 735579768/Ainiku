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
// 'data_ts' => '',
// 'data_err' =>'',
// 'data_ok' => '',
// 'data_reg' => '',
// ),
/**
 *系统共用生成表单列表
 */
//一次请求要引入的表单js
$GLOBALS['formjs'] = array(
	'editornum'  => 0,
	'datetime'   => 0,
	'picture'    => 0,
	'editor'     => 0,
	'color'      => 0,
	// 'dandu'      => 0,
	'cutpicture' => 0,
);
function create_form($fieldarr, $data = array()) {
	if (isset($fieldarr['title'])) {
		$fieldarr = [$fieldarr];
	}
	global $formjs;
	$static_dir = __STATIC__;
	$formstr    = '';
	$formjsstr  = '';
	// dump($fieldarr);
	foreach ($fieldarr as $key => $value) {
		// dump($fieldarr);
		$field      = $value['field'];
		$type       = $value['type'];
		$name       = $value['name'];
		$title      = $value['title'];
		$note       = $value['note'];
		$extra      = $value['extra'];
		$setvalue   = $value['value'];
		$is_show    = $value['is_show'];
		$is_require = $value['is_require'];

		($type == 'umeditor') && ($type = 'editor');
		//验证表单
		$data_reg = $value['data_reg'];
		$data_ok  = $value['data_ok'];
		$data_ts  = $value['data_ts'];
		$data_err = $value['data_err'];
		//处理一些判断必填的
		$is_require = $is_require ? '<span style="color:red;">(必填)</span>' : '';

		$yzstr   = '';
		$yzclass = '';
		if ($data_reg) {
			$yzstr   = ' data-reg="{$data_reg}" data-ts="{$data_ts}" data-ok="{$data_ok}" data-err="{$data_err}"';
			$yzclass = ' autoyz';
		}
		//等着替换的模板字符串
		$tem_formstr = <<<eot
<div class="form-group cl {$name}">
	<div class="form-label"><b>{$title}</b><span class="form-tip">{$note}</span>{$is_require}</div>
	[REPLACE_INPUT]
</div>\n
eot;
		//循环出来的表单
		$tem_input = '';
		//循环表单
		switch ($type) {
		case 'number':
			///////////////////////////////////////////////////////////////////////////

			$tem_input = <<<eot
<div class="form-wrap">
	<input type="text" {$yzstr}  class="form-control input-small {$yzclass}"  placeholder="请输入{$title}" name="{$name}" value="{$setvalue}" />
</div>
eot;
			break;
		case 'double':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="form-wrap">
	<input type="text"  class="form-control input-small {$yzclass}"  {$yzstr}     placeholder="请输入{$title}" name="{$name}" value="{$setvalue}" />
</div>
eot;
			break;
		case 'password':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="form-wrap">
	<input type="password"  class="form-control input-small {$yzclass}"  {$yzstr}     placeholder="请输入{$title}" name="{$name}" value="{$setvalue}" />
</div>
eot;
			break;
		case 'textarea':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="form-wrap">
<textarea  rows=1 cols=40 style='overflow:scroll;overflow-y:hidden;;overflow-x:hidden;' onfocus="am.autoHeight(this);" onblur="clearInterval(am.clock);"   class="form-control input-large {$yzclass}" {$yzstr}  placeholder="请输入{$title}"  name="{$name}">{$setvalue}</textarea>
</div>
eot;
			break;
		case 'bigtextarea':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="form-wrap">
<textarea  rows=1 cols=40 style='overflow:scroll;overflow-y:hidden;;overflow-x:hidden;overflow-x:hidden;width:96%;' onfocus="am.autoHeight(this);" onblur="clearInterval(am.clock);"   class="form-control input-large {$yzclass}" {$yzstr}  placeholder="请输入{$title}"  name="{$name}">{$setvalue}</textarea>
</div>
eot;
			break;
		case 'datetime':
			///////////////////////////////////////////////////////////////////////////
			$formjs['datetime']++;
			if ($setvalue) {
				$setvalue = time_format($setvalue);
			} else {
				$setvalue = date('Y-m-d');
			}
			$tem_input = <<<eot
<div class="form-wrap">
	<input name="{$name}" type="text" readonly class="form-control input-middle time" style="width:145px;" value="{$setvalue}" placeholder="请选择时间" />
</div>
eot;
			break;
		case 'color':
			///////////////////////////////////////////////////////////////////////////
			$formjs['color']++;
			$tem_input = <<<eot
<input name="{$name}" type="text" class="selectcolor form-control input-small" value="{$setvalue}" />
eot;
			break;
		case 'bool':
			///////////////////////////////////////////////////////////////////////////
			break;
		case 'select':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<select name="{$name}" class="form-control input-middle">
[REPLACE_OPTION]
</select>
eot;
			$sel       = '';
			$optionstr = '';
			foreach ($extra as $key => $val) {
				if ($setvalue == $key) {
					$sel = 'selected';
				}
				if (isset($_REQUEST[$name])) {
					$se = I($name);
					if ($se == $key) {
						$sel = 'selected';
					}
				}
				$optionstr .= "<option value='{$key}' {$sel} >" . trim($val) . "</option>\n";
			}
			$tem_input = str_replace('[REPLACE_OPTION]', $optionstr, $tem_input);
			break;

		case 'radio':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="controls">
	[REPLACE_RADIO]
</div>
eot;
			$sel      = '';
			$radiostr = '';
			foreach ($extra as $key => $val) {
				if ($setvalue == $key) {
					$sel = ' checked="checked" ';
				}
				if (isset($_REQUEST[$name])) {
					$se = I($name);
					if ($se == $key) {
						$sel = ' checked="checked" ';
					}
				}
				$radiostr .= <<<eot
<label class="form-radio">
  <input type="radio" value="{$key}" name="{$name}" {$sel} ><span>{$val}</span>
</label>\n
eot;
			}
			$tem_input = str_replace('[REPLACE_RADIO]', $radiostr, $tem_input);
			break;
		case 'checkbox':
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="controls">
	<input type="hidden" value="0" name="{$name}[]"  />
	[REPLACE_CHECKBOX]
</div>
eot;

			$sel      = '';
			$checkstr = '';
			foreach ($extra as $key => $val) {
				if (strpos($setvalue, $key . '') !== false) {
					$sel = ' checked="checked" ';
				}
				$checkstr = <<<eot
<label class="form-checkbox">
	<input type="checkbox" value="{$key}" name="{$name}[]"  {$sel}  />
	<span title="{$val}">{$val}</span>
</label>\n
eot;
			}
			$tem_input = str_replace('[REPLACE_CHECKBOX]', $checkstr, $tem_input);
			break;
		case 'editor':
			///////////////////////////////////////////////////////////////////////////
			$formjs['editor']++;
			$tem_input = <<<eot
 <!--style给定宽度可以影响编辑器的最终宽度-->
<script type="text/plain" id="{$name}" name="{$name}" style="width:99%;height:150px;">
    {$setvalue}
</script>
<script>
$(function(){
  //保存编辑器初始化数据
  var uescr{$name}=null;
    var ue{$name} = UE.getEditor("{$name}",{
        serverUrl:ainiku.ueupload,
        initialFrameHeight:300,
        imagePath:'',
        focus: true

    });
    ue{$name}.addListener('focus',function(editor){
      uescr{$name}=ue{$name}.getContent();
      });
    ue{$name}.addListener('blur',function(editor){
      am.delEditorImg(uescr{$name},ue{$name}.getContent());
      });
});
</script>
eot;
			break;

		case 'batchpicture':
			///////////////////////////////////////////////////////////////////////////
			$formjs['picture']++;
			break;
		case 'picture':
			///////////////////////////////////////////////////////////////////////////
			$formjs['picture']++;
			break;
		case 'file':
			///////////////////////////////////////////////////////////////////////////
			$formjs['picture']++;
			break;
		case 'liandong':
			///////////////////////////////////////////////////////////////////////////
			$formjs['liandong']++;
			$tem_input = <<<eot
            <style>select.selarea{ width:150px; overflow:hidden;}</style>
<input type="hidden" id="ssq{$name}"  name="{$name}"  value="{$setvalue}" />
<select id="Province{$name}" class="form-control selarea"><option value="0">请选择--</option></select>
<select id="city{$name}" class="form-control selarea"><option value="0">请选择--</option></select>
<select id="area1{$name}" class="form-control selarea"><option value="0">请选择--</option></select>
<script>
$(function(){
cityselect.create("ssq{$name},Province{$name},city{$name},area1{$name}");
});
</script>
eot;
			break;
		case 'attribute':
			///////////////////////////////////////////////////////////////////////////
			$opstr = '';
			$dlist = get_goods_type_list();
			foreach ($dlist as $key => $vo) {
				$opstr .= "<option value='{$vo['goods_type_id']}'>{$vo['title']}</option>";
			}
			$url       = U('Goodstypeattribute/formlist');
			$tem_input = <<<eot
<select class="form-control" id="goodstype_form" name="goods_type_id">
<option value="0">请选择--</option>
{$opstr}
</select>
<div id="goodsattribute" style="  padding: 20px 0px 0px 20px;" class="">
</div>
<script>
$(function(){
	$('#goodstype_form').bind('propertychange change',function(){
	var idd=$(this).val();
	var productid=$('#productid').val();
	//productid产品的id在编辑表单里面首先用js写上这个变量值为当前产品的id
		$.ajax({
			'type':'POST',
			'url':"{$url}",
			'data':{goods_type_id:idd,mainid:productid},
			'success': function(da){
			$('#goodsattribute').html(da);
			}
		});
	});
	$('#goodstype_form').val('{$setvalue});
	$('#goodstype_form').change();
});
</script>

eot;
			break;
		case 'cutpicture':
			///////////////////////////////////////////////////////////////////////////
			$formjs['cutpicture']++;
			break;
		case 'custom':
			$tem_input = get_custom_form($extra, $name, $setvalue);
		default:
			///////////////////////////////////////////////////////////////////////////
			$tem_input = <<<eot
<div class="form-wrap">
	<input type="text"  class="form-control input-large {$yzclass}" {$yzstr}   placeholder="请输入{$title}"  name="{$name}" value="{$setvalue}" />
</div>
eot;
			break;
		}

		/**
		 * 替换循环出来的表单
		 */
		$formstr .= str_replace('[REPLACE_INPUT]', $tem_input, $tem_formstr);
	}
	/**
	 * 添加用到的js
	 */
	if ($formjs['liandong'] && $formjs['liandong'] !== true) {
		$formjs['liandong'] = true;
		$formjsstr .= <<<eot
<!--城市联动s start-->
<script type="text/javascript" charset="utf-8" src="{$static_dir}/city.js"></script>
<!--城市联动js end-->\n
eot;
	}
	if ($formjs['color'] && $formjs['color'] !== true) {
		$formjs['color'] = true;
		$formjsstr .= <<<eot
<!--颜色选择器js start-->
<script type="text/javascript" charset="utf-8" src="{$static_dir}/jscolor/jscolor.js"></script>
<!--颜色选择器js end-->\n
eot;
	}
	if ($formjs['datetime'] && $formjs['datetime'] !== true) {
		$formjs['datetime'] = true;
		$formjsstr .= <<<eot
<!--日期js start-->
<link href="{$static_dir}/datetimepicker/css/datetimepicker.css" type="text/css" rel="stylesheet" />
<link href="{$static_dir}/datetimepicker/css/dropdown.css" type="text/css" rel="stylesheet" />
<script src="{$static_dir}/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript" ></script>
<script src="{$static_dir}/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" type="text/javascript" ></script>
<script>
$(function(){
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
});
</script>

<script>
$(function(){
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
});
</script>
<!--日期js end->\n
eot;
	}
	if ($formjs['picture'] && $formjs['picture'] !== true) {
		$formjs['picture'] = true;
		$formjsstr .= <<<eot
<!--上传图片js start-->
<link href="{$static_dir}/html5Upload/css/zyUpload.css?r=32673" type="text/css" rel="stylesheet" />
<link href="{$static_dir}/css/lightbox.css?r=32673" type="text/css" rel="stylesheet" />
<link href="{$static_dir}/uploadify/uploadify.css?r=32673" type="text/css" rel="stylesheet" />
<script src="{$static_dir}/html5Upload/js/uploadFile.min.js?r=16300" type="text/javascript" ></script>
<script src="{$static_dir}/js/lightbox.js?r=16300" type="text/javascript" ></script>
<script src="{$static_dir}/uploadify/jquery.uploadify.min.js?r=16300" type="text/javascript" ></script>
<!--上传图片js end-->\n

eot;
	}
	if ($formjs['editor'] && $formjs['editor'] !== true) {
		$formjs['editor'] = true;
		$formjsstr .= <<<eot
<!--ue编辑器js start-->
<script>
window.UEDITOR_HOME_URL='{$static_dir}/ueditor/';
</script>
<script type="text/javascript" src="{$static_dir}/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="{$static_dir}/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{$static_dir}/ueditor/lang/zh-cn/zh-cn.js"></script>
<!--UE编辑器js end-->\n

eot;
	}
	if ($formjs['cutpicture'] && $formjs['cutpicture'] !== true) {
		$formjs['cutpicture'] = true;
	}
	return $formjsstr . $formstr;
}