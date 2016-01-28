// JavaScript Document
(typeof console === "undefined") || (console = {}, console.log = function() {});;
$(function() {
	//主导航单击后保存状态
	$('.mainnav li a').click(function(e) {
		$('.mainnav li a').removeClass('hover');
		$(this).addClass('hover');
	});
	//密码下拉菜单绑定
	$('.downmenu').hover(function() {
		$(this).find('ul').show();
	}, function() {
		$(this).find('ul').hide();
	});
	//侧栏关闭
	$('#closewest').click(function(e) {
		if ($(this).css('left') == '165px') {
			$('#west').animate({
				'overflow': 'hidden',
				//'padding':'10px 0px 50px 0px',
				'left': '-179'
			}, 500);
			//$('#center').animate({'marginLeft':'0'},500);
			$('body').animate({
				'paddingLeft': '0'
			}, 500);
			$(this).animate({
				'left': '-10'
			}, 500);
			writeCookie('leftside', 0);
		} else {
			//$('#center').animate({'marginLeft':'179'},500);
			$('body').animate({
				'paddingLeft': '179'
			}, 500);
			//$('#west').show();
			$('#west').animate({
				'overflow': 'hidden',
				//'padding':'10px 10px 50px 10px',
				'left': '0',
				'overflowY': 'scroll'
			}, 500);
			$(this).animate({
				'left': '165'
			}, 500);
			writeCookie('leftside', 1);
		}
	});
	if (readCookie('leftside') === '0') {
		//$('#west').hide();
		$('#west').css({
			'overflow': 'hidden',
			'padding': '10px 0px 50px 0px',
			'left': '-198px'
		});
		//$('#center').css('margin-left','0px');
		$('body').css({
			'paddingLeft': '0'
		});
		$('#closewest').css('left', '-10px');
	}
	//设置默认主菜单

	$('#mainnav a').each(function(index) {
		var str = window.location.href;
		var str = str.replace(/(.+?)\.(.+?)\//, '/');
		var href = $(this).attr('href');
		if (href == str) {
			$(this).addClass('hover');
		}
	});
	$('#mainnav li a').click(function(e) {
		$('#loadingimg').show();
		var obj = $('#west');
		var shtml = obj.html();
		var dataurl = $(this).attr('data-url');
		$.ajax({
			type: 'POST',
			url: ainiku.getmenu,
			data: {
				url: dataurl
			},
			success: function(da) {
				$('#loadingimg').hide();
				if (da.status == '1') {
					$('#west').html(da.info);
				} else {
					ank.msg(da.info);
				}

			}
		});
		return false;
	});
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
	bindleftmenu();

	//选择全部
	$('.check-all').click(function(e) {
		if ($(this).is(':checked')) {
			$('.check-item').prop('checked', true).change();
		} else {
			$('.check-item').prop('checked', false).change();
		}
	});
	//绑定列表中的图片显示功能
	$('.popupthumb').hover(function() {
		var _this=$(this);
		var len = _this.find('img').length;
		if (len == 0) {
			var src = $(this).find('.imgbox').attr('_src');
			$(this).find('.wrapimg').append('<img src="' + src + '" width="" height="" />');
		}
		//查找位置
		var top=_this.offset().top;
		var sh=$(window).height();
		var ih=$(this).find('.imgbox').height();
		//console.log(top,sh);
		if(sh-top-20<ih && top>=sh-top){
			$(this).find('.imgbox').css({
				top:'-'+ih+'px'
			});
		}else{
			$(this).find('.imgbox').css({
				top:'35px'
			});
		}
		$(this).find('.imgbox').show();
	}, function() {
		$(this).find('.imgbox').hide();
	});
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
	$('.klbatch').bind('click', function() {
		$(this).addClass('disabled');
		obj = $(this);
		url = obj.attr('href');
		//取id
		idstr = '';
		$('input.check-item:checked').each(function(index, element) {
			if (idstr == '') {
				idstr = $(this).val();
			} else {
				idstr += ',' + $(this).val();
			}
		});
		idstr = idstr.replace(/\s+/g, '');
		if (idstr == '') {
			topmsg('请选择后再操作');
			$(this).removeClass('disabled');
			return false;
		}
		if (!confirm('确定要此操作吗?')) return false;
		$.ajax({
			'type': 'GET',
			'url': url,
			data: {
				id: idstr
			},
			'success': function(da) {
				topmsg(da, function(da) {
					if (da.status == '1') window.location.reload();
				});
			},
			dataType: 'JSON'
		});
		return false;
	});
	//绑定搜索按钮
	$('.btn-search').click(function(e) {
		$(this).parents('form').submit();
	});
	//回车自动提交
	$('.autosubmit').find('input').keyup(function(event) {
		if (event.keyCode === 13) {
			//回车后要处理的数据
			var obj = $(this).parents('form').find('.ajax-post');
			if (obj.length > 0) {
				obj.click();
			} else {
				$(this).parents('form').submit();
			}
		}
	});

	//设置默认导航
	$('#nav-bar a').each(function(index, element) {
		var a = window.location.pathname;
		//a=a.replace(/(.*?)\.(.*?)\//,'/');
		var b = $(this).attr('href');
		if (a === b) {
			$(this).parent().addClass('on')
		}
	});
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
	//setTimeout(function(){
	//		//设置表格中按钮栏的宽度
	//		var ww=0;
	//		$('tr td:last-child').each(function(index, element) {
	//			var wt=0;
	//			$(this).find('.btn').each(function(index, element) {
	//				wt+=$(this).outerWidth();
	//			console.log(wt);
	//			});
	//			wt>ww&&(ww=wt);
	//		});
	//		$('tr th:last-child').css('width',ww+10);
	//	},1000);
	//替换select
	hideSelect.hide($('select'));
	hideCheckbox.hide($('input[type="checkbox"]'));
    hideRadio.hide($('input[type="radio"]'));
});