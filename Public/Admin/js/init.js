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
		var _this = $(this);
		var len = _this.find('img').length;
		if (len == 0) {
			var src = $(this).find('.imgbox').attr('_src');
			$(this).find('.wrapimg').append('<img src="' + src + '" width="" height="" />');
		}
		//查找位置
		var top = _this.offset().top;
		var sh = $(window).height();
		var ih = $(this).find('.imgbox').height();
		//console.log(top,sh);
		if (sh - top - 20 < ih && top >= sh - top) {
			$(this).find('.imgbox').css({
				top: '-' + ih + 'px'
			});
		} else {
			$(this).find('.imgbox').css({
				top: '35px'
			});
		}
		$(this).find('.imgbox').show();
	}, function() {
		$(this).find('.imgbox').hide();
	});

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
});