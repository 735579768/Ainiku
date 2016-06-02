$(function() {
	//绑定左边菜单
	window.bindleftmenu = function() {
		//加载完菜单后进行绑定
		//菜单合并
		$('.menu .mt a').click(function(e) {
			var obj = $(this).find('i');
			if (obj.hasClass('menuopenico')) {
				//$(this).parents('#west').find('dd').slideUp('fast');
				obj.removeClass('menuopenico');
				obj.addClass('menucloseico');
				obj.parents('dl').find('dd').hide();
			} else {
				$(this).parents('#west').find('i.menuopenico').click();
				obj.removeClass('menucloseico');
				obj.addClass('menuopenico');
				obj.parents('dl').find('dd').show();
			}

		});
		//菜单单击后保存状态
		$('.menu .mitem  a').click(function(e) {
			$('.menu .mitem  a').removeClass('hover');
			$(this).addClass('hover');
			var uri = $(this).attr('href');
			var title = $(this).text();
			addtabclose(title, uri);
			return false;
		});
		//设置默认左边菜单////////////////////////////////////////////////////////////////////////

		//默认折叠
		$('.menu .mt a').click();
		window.hasmenu = false;
		$('.menu a').each(function(index, element) {
			var a = window.location.href;
			a = a.replace(/(.*?)\.(.*?)\//, '/');
			a = a.replace('&mainmenu=true', '');
			var b = $(this).attr('href');
			//	var c=b.replace(/(\?)|(\.)|(\=)/g,'\\$1$2$3');
			//	c=c+".*?";
			//	console.log(a.match(/c/));
			if (a == b) {
				window.hasmenu = true;
				$(this).parents('.menu').find('dt a').click();
				$(this).addClass('hover')
			}
		});
		if (!hasmenu) {
			var ur = window.location.href;
			ur = ur.replace(/(.*?)\.(.*?)\//, '/');
			ur = ur.replace('.html', '');
			var issel = false;
			$('.menu a').each(function(index, element) {
				var arr = ur.split('/');
				var b = $(this).attr('href');
				b = b.replace('.html', '');
				var brr = b.split('/');
				if (arr.length > brr.length) {
					arr = arr.slice(0, brr.length);
				} else {
					brr = brr.slice(0, arr.length);
				}
				if (arr.join('/') == brr.join('/')) {
					window.hasmenu = true;
					$(this).parents('.menu').find('dt a').click();
					$(this).addClass('hover');
				}
			});
		}

		if (!hasmenu) {
			//如果没有查到一样的链接就查找上一次的链接
			$('.menu a').each(function(index, element) {
				var a = document.referrer;
				a = a.replace(/(.*?)\.(.*?)\//, '/');
				var b = $(this).attr('href');
				if (a === b) {
					window.hasmenu = true;
					$(this).parents('.menu').find('dt a').click();
					$(this).addClass('hover')
				}
			});
		}
		if (!hasmenu) {
			//如果还没有找到就设置默认的链接
			var objj = $(".menu a[href='" + ainiku.defaultmenu + "']");
			if (objj.length > 0) {
				objj.addClass('hover');
				objj.parents('.menu').find('dt a').click();
			} else {
				$('.menu dt a').eq(0).click();
			}
		}
		//设置默认左边菜单end////////////////////////////////////////////////////////////////////////
	};
	//绑定删除图片的按钮
	window.binddel = function() {
		$('.imgblock  .btn-danger').unbind('click');
		$('.imgblock  .btn-danger').click(function(e) {
			var a = $(this).attr('dataid');
			$.get(ainiku.delimg, {
				id: a
			});
			var val = $(this).parent().parent().prev().val();
			if (val != '') {
				val = val.replace(/\s+/g, '');
				val = val.replace(a + '|', '');
				val = val.replace('|' + a, '');
				val = val.replace(a, '');
				val = val.replace('||', '|');
				$(this).parent().parent().prev().val(val);
				$(this).parent().remove();
				//删除服务器上的图片
				//$.get($(this).prepend('href'),success(){});
			}
			return false;
		});
	};
	//对比编辑器内容并删除其中的图片
	window.delEditorImg = function(scr, dest) {
		if (scr === dest) {} else {
			$.ajax({
				type: 'POST',
				url: ainiku.deleditorimg,
				data: {
					s: scr,
					d: dest
				}
			})
		}
	};
	window.binddelattach = function() {
		$('.upload-pre-file .btn-danger').unbind('click');
		$('.upload-pre-file .btn-danger').click(function(e) {
			var a = $(this).attr('dataid');
			$.get(ainiku.delattach, {
				id: a
			});
			$(this).parent().parent().prev().val('');
			$(this).parent().remove();
		});
	};

	window.setPosition = function(table1, id1, field1, value1, srcid) {
		$('body').append('<div id="markbg" class="bg"></div>');
		$.get(ainiku.setposition, {
			table: table1,
			id: id1,
			field: field1,
			value: value1
		}, function(da) {
			msgDialog({
				'title': '修改信息',
				'content': da.info,
				'btn': true,
				'ok': function() {
					var formobj = $('#positionform');
					$.ajax({
						type: 'POST',
						url: formobj.attr('action'),
						data: formobj.serialize(),
						success: function(da) {
							var chl = $(srcid).children();
							if (da.status == 1) {
								chl.eq(0).val(da.info[1]);
								chl.eq(1).html(da.info[0]);
							}
						}
					});
				},
				'cancel': function() {}
			});
			$('#markbg').remove();
		});
		//	var arr=str.split(',');
		//	console.log(arr);
		//	var con='<input type="checkbox" name="position[]" value="" />';
	};
});