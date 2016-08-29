<?php
// array(
// 	'field'   => 'sex',
// 	'type'    => 'radio',
// 	'name'    => 'sex',
// 	'title'   => '性别',
// 	'note'    => '',   //对标题的说明性文字
// 	'extra'   => array(
// 		1 => '男',
// 		2 => '女',
// 	),
// 	'value'   => 1,
// 	'is_show' => 3,
// 	'is_require'=>0, //是否必填
//  'data_ts' => '', //鼠标放在表单上时的提示文本
//  'data_err' =>'', //格式不对时的提示文本
//  'data_ok' => '', //格式正确时的提示文本
//  'data_reg' => '', //验证格式的正则
// ),
/////////////说明//////////////
//@is_show  1 add状态下显示   2 edit编辑状态下显示   3 add edit状态下都显示  4 只要是超级管理员状态下都显示
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
	'liandong'   => 0,
	// 'dandu'      => 0,
	'cutpicture' => 0,
);
function create_form($fieldarr, $data = []) {
	$md5key = md5(json_encode($fieldarr));
	$data || ($data = []);
	if (isset($fieldarr['title'])) {
		$fieldarr = [$fieldarr];
	}
	global $formjs;
	$static_dir = __STATIC__;
	$formstr    = '';
	// $formjsstr  = '';
	$formstr = F('_formcache/' . $md5key);
	if (empty($formstr) || APP_DEBUG) {
		$formstr       = '';
		$formjsstr     = '';
		$default_value = [];
		// dump($fieldarr);
		foreach ($fieldarr as $key => $value) {
			// dump($fieldarr);
			$field      = $value['field'];
			$type       = $value['type'];
			$name       = $value['name'];
			$title      = $value['title'];
			$note       = isset($value['note']) ? $value['note'] : '';
			$extra      = isset($value['extra']) ? $value['extra'] : [];
			$setvalue   = isset($value['value']) ? $value['value'] : '';
			$is_show    = isset($value['is_show']) ? $value['is_show'] : 3;
			$is_require = isset($value['is_require']) ? $value['is_require'] : '0';

			//验证表单
			$data_reg = isset($value['data_reg']) ? $value['data_reg'] : '';
			$data_ok  = isset($value['data_ok']) ? $value['data_ok'] : '';
			$data_ts  = isset($value['data_ts']) ? $value['data_ts'] : '';
			$data_err = isset($value['data_err']) ? $value['data_err'] : '';

			($type == 'umeditor') && ($type = 'editor');
			($type == 'string') && ($type = 'text');
			//保存默认值
			$default_value[$name] = $setvalue;
			//处理一些判断必填的
			$is_require = $is_require ? '<span style="color:red;">(必填)</span>' : '';

			$yzstr   = '';
			$yzclass = '';
			if ($data_reg) {
				$yzstr   = ' data-reg="{$data_reg}" data-ts="{$data_ts}" data-ok="{$data_ok}" data-err="{$data_err}"';
				$yzclass = ' autoyz';
			}
			//判断当前操作是add  edit
			$is_add  = (strpos(strtolower(ACTION_NAME), 'add') === false) ? false : true;
			$is_edit = (strpos(strtolower(ACTION_NAME), 'edit') === false) ? false : true;
			if (!(($is_add && $is_show == 1) or ($is_edit && $is_show == 2) or ($is_show == 3) or ($is_show == 4 && MODULE_NAME == 'Admin'))) {
				//不符合条件直接退出本次循环
				continue;
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
	<input type="text" {$yzstr}  class="form-control input-small {$yzclass}"  placeholder="请输入{$title}" name="{$name}" value="[REPLACE_SETVALUE_{$name}]" />
</div>
eot;
				break;
			case 'double':
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="form-wrap">
	<input type="text"  class="form-control input-small {$yzclass}"  {$yzstr}     placeholder="请输入{$title}" name="{$name}" value="[REPLACE_SETVALUE_{$name}]" />
</div>
eot;
				break;
			case 'password':
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="form-wrap">
	<input type="password"  class="form-control input-small {$yzclass}"  {$yzstr}     placeholder="请输入{$title}" name="{$name}" value="[REPLACE_SETVALUE_{$name}]" />
</div>
eot;
				break;
			case 'textarea':
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="form-wrap">
<textarea  rows=1 cols=40 style='overflow:scroll;overflow-y:hidden;;overflow-x:hidden;' onfocus="file.autoHeight(this);" onblur="clearInterval(file.clock);"   class="form-control input-large {$yzclass}" {$yzstr}  placeholder="请输入{$title}"  name="{$name}">[REPLACE_SETVALUE_{$name}]</textarea>
</div>
eot;
				break;
			case 'bigtextarea':
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="form-wrap">
<textarea  rows=1 cols=40 style='overflow:scroll;overflow-y:hidden;;overflow-x:hidden;overflow-x:hidden;width:96%;' onfocus="file.autoHeight(this);" onblur="clearInterval(file.clock);"   class="form-control input-large {$yzclass}" {$yzstr}  placeholder="请输入{$title}"  name="{$name}">[REPLACE_SETVALUE_{$name}]</textarea>
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
	<input name="{$name}" type="text" readonly class="form-control input-middle time" style="width:145px;" value="[REPLACE_SETVALUE_{$name}]" placeholder="请选择时间" />
</div>
eot;
				break;
			case 'color':
				///////////////////////////////////////////////////////////////////////////
				$formjs['color']++;
				$tem_input = <<<eot
<input name="{$name}" type="text" class="selectcolor form-control input-small" value="[REPLACE_SETVALUE_{$name}]" />
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

				$optionstr = '';
				foreach ($extra as $key => $val) {
					$sel = '';
					// if ($setvalue == $key) {
					// 	$sel = 'selected';
					// }
					// if (isset($_REQUEST[$name])) {
					// 	$se = I($name);
					// 	if ($se == $key) {
					// 		$sel = 'selected';
					// 	}
					// }
					$optionstr .= <<<eot
<option value="{$key}" {$sel} >{$val}</option>\n
eot;
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
					// if ($setvalue == $key) {
					// 	$sel = ' checked="checked" ';
					// }
					// if (isset($_REQUEST[$name])) {
					// 	$se = I($name);
					// 	if ($se == $key) {
					// 		$sel = ' checked="checked" ';
					// 	}
					// }
					$radiostr .= <<<eot
<label class="form-radio">
  <input type="radio" name="{$name}" value="{$key}" {$sel} /><span>{$val}</span>
</label>\n
eot;
				}
				$tem_input = str_replace('[REPLACE_RADIO]', $radiostr, $tem_input);
				break;
			case 'checkbox':
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="controls">
	<span style="display:none;"><input type="checkbox" name="{$name}[]" value="0"  /></span>
	[REPLACE_CHECKBOX]
</div>
eot;

				$sel      = '';
				$checkstr = '';
				$valuearr = explode(',', $setvalue);
				foreach ($extra as $key => $val) {
					// if (in_array($key, $valuearr)) {
					// 	$sel = ' checked="checked" ';
					// }
					$checkstr = <<<eot
<label class="form-checkbox">
	<input type="checkbox" name="{$name}[]" value="{$key}"   {$sel}  />
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
<script type="text/plain" id="{$name}" name="{$name}" style="width:99%;height:150px;">[REPLACE_SETVALUE_{$name}]</script>
<script>
$(function(){
  //保存编辑器初始化数据
  var uescr{$name}='';
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
      file.delEditorImg(uescr{$name},ue{$name}.getContent());
      });
});
</script>
eot;
				break;

			case 'picture':
				///////////////////////////////////////////////////////////////////////////
				$formjs['picture']++;
				$tem_input = get_upload_picture_html($name, $setvalue);
			case 'batchpicture':
				///////////////////////////////////////////////////////////////////////////
				$formjs['picture']++;
				$tem_input = get_upload_picture_html($name, $setvalue, true);
				break;

				break;
			case 'file':
				///////////////////////////////////////////////////////////////////////////
				$formjs['picture']++;
				$tem_input = get_upload_picture_html($name, $setvalue, false, true);
				break;
			case 'liandong':
				///////////////////////////////////////////////////////////////////////////
				$formjs['liandong']++;
				$tem_input = <<<eot
<style>select.selarea{ width:150px; overflow:hidden;}</style>
<input type="hidden" id="ssq{$name}"  name="{$name}"  value="[REPLACE_SETVALUE_{$name}]" />
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
	var goodstypeform=$('#goodstype_form');
	goodstypeform.bind('propertychange change',function(){
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
	goodstypeform.val('[REPLACE_SETVALUE_{$name}]');
	goodstypeform.change();
});
</script>

eot;
				break;
			case 'cutpicture':
				///////////////////////////////////////////////////////////////////////////
				$formjs['cutpicture']++;
				$tem_input = get_upload_picture_html($name, $setvalue);
				break;
			case 'custom':
				$tem_input = get_custom_form($extra, $name, $setvalue);
			default:
				///////////////////////////////////////////////////////////////////////////
				$tem_input = <<<eot
<div class="form-wrap">
	<input type="text"  class="form-control input-large {$yzclass}" {$yzstr}   placeholder="请输入{$title}"  name="{$name}" value="[REPLACE_SETVALUE_{$name}]" />
</div>
eot;
				break;
			}

			/**
			 * 替换循环出来的表单
			 */
			$formstr .= str_replace('[REPLACE_INPUT]', $tem_input, $tem_formstr);
		}
		F('_formcache/' . $md5key, $formstr);
		F('_formcache/' . $md5key . '_json', $default_value);
		F('_formcache/' . $md5key . '_js', $formjs);

	}
	//要引用的表单js
	$formjs        = array_merge($formjs, F('_formcache/' . $md5key . '_js'));
	$default_value = F('_formcache/' . $md5key . '_json');
	//此表单的默认json字符串值
	// $default_value = json_encode($default_value);
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
<link href="{$static_dir}/datetimepicker/css/datetimepicker.min.css" type="text/css" rel="stylesheet" />
<link href="{$static_dir}/datetimepicker/css/dropdown.min.css" type="text/css" rel="stylesheet" />
<script src="{$static_dir}/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript" ></script>
<script src="{$static_dir}/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" type="text/javascript" ></script>
<script>
$(function(){
	$('.time').each(function(dom,index){
		var val=$(this).val();
		var formstr='yyyy-mm-dd HH:mm:ss';
		if(/\d{4}\-\d{2}\-\d{2}/ig.test(val)){
			formstr='yyyy-mm-dd';
		}
	    $(this).datetimepicker({
	        format: formstr,
	        language:"zh-CN",
	        minView:2,
	        autoclose:true
	    });
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
<link href="{$static_dir}/html5Upload/css/zyUpload.min.css?r=32673" type="text/css" rel="stylesheet" />
<link href="{$static_dir}/uploadify/uploadify.min.css?r=32673" type="text/css" rel="stylesheet" />
<script src="{$static_dir}/html5Upload/js/uploadFile.min.js?r=16300" type="text/javascript" ></script>
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
<script type="text/javascript" src="{$static_dir}/ueditor/ueditor.config.min.js"></script>
<script type="text/javascript" src="{$static_dir}/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="{$static_dir}/ueditor/lang/zh-cn/zh-cn.min.js"></script>
<!--UE编辑器js end-->\n

eot;
	}
	if ($formjs['cutpicture'] && $formjs['cutpicture'] !== true) {
		$formjs['cutpicture'] = true;
	}

	$data = array_merge($default_value, $data);
	//替换成默认值
	foreach ($data as $key => $value) {
		//替换文本框的值
		$formstr = str_replace("[REPLACE_SETVALUE_{$key}]", $value, $formstr);

		//字符小于指定值的才正则设置值
		if (strlen($value) < 50) {
			$key   = preg_quote($key);
			$value = preg_quote($value);
			$key   = str_replace('/', '\/', $key);
			$value = str_replace('/', '\/', $value);
			//替换select
			$pattern = '/<select.*?name\=\"' . $key . '\".*?>.*?<\/select>/is';
			preg_match($pattern, $formstr, $match);
			if ($match) {
				$tstr     = $match[0];
				$pattern1 = '/(<option.*?value=".*?").*?(>.*?<\/option>)/i';
				$pattern2 = '/(<option.*?value="' . $value . '").*?(>.*?<\/option>)/i';
				$tstr2    = preg_replace([$pattern1, $pattern2], ['$1 $2', '$1 selected $2'], $tstr);
				$formstr  = str_replace($tstr, $tstr2, $formstr);
			}

			//替换radio
			//去掉默认的选中
			$pattern1 = '/(<input type="radio".*?name\="' . $key . '" value\=".*?").*? \/>/i';
			$pattern2 = '/(<input type="radio".*?name\="' . $key . '" value\="' . $value . '").*? \/>/i';
			$formstr  = preg_replace([$pattern1, $pattern2], ['$1 />', '$1 checked="checked" />'], $formstr);
			// trace($pattern2);
			//替换checkbox

			$valarr   = explode(',', $value);
			$pattern1 = '/(<input type="checkbox".*?name\="' . $key . '\[\]" value\=".*?").*? \/>/i';
			$formstr  = preg_replace($pattern1, '$1 />', $formstr);
			foreach ($valarr as $v) {
				$pattern2 = '/(<input type="checkbox".*?name\="' . $key . '\[\]" value\="' . $v . '").*? \/>/i';
				$formstr  = preg_replace($pattern2, '$1 checked="checked" />', $formstr);
			}
		}

	}
	//替换掉隐藏类型的值
	//替换掉没有默认值的
	$formstr = preg_replace("/\[REPLACE\_SETVALUE\_.*?\]/is", '', $formstr);
	return $formstr . $formjsstr;
}

function get_upload_picture_html($name, $setvalue, $muli = false, $filetype = false) {
	$static_dir        = __STATIC__;
	$upload_text       = $filetype ? '上传附件' : '上传图片';
	$preimglist        = '';
	$uploadsuccessfunc = '';
	$preurl            = U('File/getFileInfo');
	$fileuploadurl     = U('File/uploadpic', array('session_id' => session_id()));
	$filetype && ($fileuploadurl = U('File/uploadfile', array('session_id' => session_id())));
	$is_muli_upload = $muli ? 'true' : 'false';
	$prejs          = ''; //加载图片或附件预览的js
	//上传附件
	if ($filetype) {
		//上传成功后的函数
		$uploadsuccessfunc = <<<eot
window.uploadPicture{$name}=function(upfile, data){
 var data = $.parseJSON(data);
  if(data.status){
    var name = "{$name}";
    var sid=$("#cover_id_{$name}").val();
    if(sid){
    	$.get(ainiku.delattach,{id:sid})}
    $("input[name="+name+"]").val(data.id);

    $("#uploadimg_{$name}").html(
      "<div class=\"upload-pre-file\"><span class=\"upload_icon_all\"></span>" + data.info + "<a href='javascript:;' class='btn btn-danger' dataid='"+data.id+"' >删除</a></div>"
    );
    file&&file.bindDelAttach();
  } else {
    ank.msg(data);
    setTimeout(function(){
      $('#top-alert').find('button').click();
      // $(that).removeClass('disabled').prop('disabled',false);
    },1500);
  }
}
eot;
		$prejs = <<<eot
!function(){
	var sid=$("#cover_id_{$name}").val();
	if(sid){
		$.post("{$preurl}",{id:sid,type:'other'},function(data){
			var da=data.info;
			if(da.length>0){
				var data=da[0];
			    $("#uploadimg_{$name}").html(
			      "<div class=\"upload-pre-file\"><span class=\"upload_icon_all\"></span>" + data.srcname + "<a href='javascript:;' class='btn btn-danger' dataid='"+data.id+"' >删除</a></div>"
			    );
			}

		});
	}
}();
eot;
	} else {
		//上传图片显示设置的默认图片
		$sethtml = $muli ? 'append' : 'html';
		$prejs   = <<<eot
!function(){
	var sid=$("#cover_id_{$name}").val();
	if(sid){
		$.post("{$preurl}",{id:sid,type:'img'},function(data){
			var da=data.info;
			if(da.length>0){
			for(a in da){
			    $("#uploadimg_{$name}").{$sethtml}(
			      "<div class='imgblock'><div class='upload-img-box uploadimg'><div class='upload-pre-item'><img layer-pid='"+da[a]['destname']+"' layer-src='"+da[a]['path']+"' src='" + da[a]['thumbpath'] + "' /></div></div><a href='javascript:;' class='btn btn-danger' dataid='"+da[a]['id']+"' >删除</a></div>"
			    );
			}
			  layer.photos({
			    photos: '#uploadimg_{$name}'
			  });
			   file&&file.bindDel();
			}
		});
	}
}();
eot;
		//上传图片
		if ($muli) {
			//多图上传

			//上传成功后的函数
			$uploadsuccessfunc = <<<eot
window.uploadPicture{$name}=function(upfile, data){
  var data = $.parseJSON(data);
  if(data.status){
    var a=$("#cover_id_{$name}").val();
    a=a.replace(/\s+/g,'');
    if(a==''){a=data.id;}else{a+='|'+data.id}
    $("#cover_id_{$name}").val(a);
    var selobj=$("#uploadimg_{$name}");
    selobj.append(
      "<div class='imgblock'><div class='upload-img-box uploadimg'><div class='upload-pre-item'><img  layer-pid='"+data.srcname+"' layer-src='"+data.path+"' src='" + data.thumbpath + "' /></div></div><a href='javascript:;' class='btn btn-danger' dataid='"+data.id+"' >删除</a></div>"
    );

   file&&file.bindDel();
  } else {
    ank.msg(data);
    setTimeout(function(){
      $('#top-alert').find('button').click();
      // $(that).removeClass('disabled').prop('disabled',false);
    },1500);
  }
}
eot;
		} else {
			//上传成功后的函数
			$uploadsuccessfunc = <<<eot
    window.uploadPicture{$name}=function(upfile, data){
        var data = $.parseJSON(data);
        var src = '';
        if(data.status){
            var sid=$("#cover_id_{$name}").val();
        if(sid!=""){
        	$.get(ainiku.delimg,{id:sid})}
      $("#cover_id_{$name}").val(data.id);
            src = data.url ||  data.path
            $("#uploadimg_{$name}").html(
                "<div class='imgblock'><div class='upload-img-box uploadimg'><div class='upload-pre-item'><img  layer-pid='"+data['srcname']+"' layer-src='"+data['path']+"' src='" + src + "' /></div></div><a href='javascript:;' class='btn btn-danger' dataid='"+data.id+"' >删除</a></div>"
            );

   file&&file.bindDel();
        } else {
            ank.msg(data);
            setTimeout(function(){
                $('#top-alert').find('button').click();
               // $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        }
    }
eot;
		}
	}

	//表单字符串
	$tem_input = <<<eot
<div class="controls h5upload-block">
<input type="file" name="file" id="upload_picture_{$name}"  style="display:none;">
<a  id="demohtml5upload{$name}btn" class="btn  html5uploadbtn" style="margin-bottom:10px;"  href="javascript:;">展开</a>
<div id="demohtml5upload{$name}" class="demo  html5upload"></div>
<input type="hidden" name="{$name}" id="cover_id_{$name}" value="[REPLACE_SETVALUE_{$name}]"/>
<div id="uploadimg_{$name}" class="cl">
{$preimglist}
</div>
</div>
<script type="text/javascript">
$(function(){
	//自动加载图片或附件预览
	{$prejs}
	if (window.applicationCache) {
	   // 把原来的上传按钮去掉
	  $('#upload_picture_{$name}').remove();
	  $('#demohtml5upload{$name}btn').click(function(e) {
	      var aaa=$(this).text();
	      if(aaa=='展开'){
		      $('#demohtml5upload{$name}').slideDown();
		      $(this).html('收缩');
	      }else{
	        $(this).html('展开');
	        $('#demohtml5upload{$name}').slideUp();
	      }
	  });
	  // 初始化插件
	  $("#demohtml5upload{$name}").zyUpload({
	    parentsel   :'#demohtml5upload{$name}',
	    width            :   "80%",                 // 宽度
	    height           :   "auto",                 // 宽度
	    itemWidth        :   "120px",                 // 文件项的宽度
	    itemHeight       :   "100px",                 // 文件项的高度
	    url              :   "{$fileuploadurl}",       // 上传文件的路径
	    data             :{myname:'your name'},
	    multiple         :   {$is_muli_upload},        // 是否可以多个文件上传
	    dragDrop         :   true,                    // 是否可以拖动上传文件
	    del              :   true,                    // 是否可以删除文件
	    finishDel        :   true,            // 是否在上传文件完成后删除预览
	    /* 外部获得的回调接口 */
	    // 选择文件的回调方法
	    onSelect: function(files, allFiles){},
	    // 删除一个文件的回调方法
	    onDelete: function(file, surplusFiles){},
	    // 文件上传成功的回调方法
	    onSuccess: function(file,response){uploadPicture{$name}(file,response);},
	    //文件上传失败的回调方法
	    onFailure: function(file){},
	    // 上传完成的回调方法
	    onComplete: function(responseInfo){}
	  });

    } else {
	  //  alert("你的浏览器不支持HTML5");
	  $('#demohtml5upload{$name}btn').remove();
	  $('#demohtml5upload{$name}').remove();
	    //上传图片
	    /* 初始化上传插件 */
	    $("#upload_picture_{$name}").uploadify({
	        "height"          : 30,
	        "swf"             : "{$static_dir}/uploadify/uploadify.swf",
	        "fileObjName"     : "filelist",
	        "buttonText"      : "{$upload_text}",
	        "uploader"        : "{$fileuploadurl}",
	        "width"           : 120,
	        'removeTimeout'   : 1,
	        'fileTypeExts'    : '*.jpg; *.png; *.gif;*.bmp;',
	        "onUploadSuccess" : uploadPicture{$name},
	        'onFallback' : function() {
	            alert('未检测到兼容版本的Flash.');
	        }
	    });
    }

});
{$uploadsuccessfunc}
    </script>
eot;
	return $tem_input;
}