$(function() {
	window.am = {
		version: '1.0',
		changeVerify: function() {
			var obj = document.getElementById('verifyimg');
			obj && (obj.src = obj.src.replace(/\&r=.*/, '') + '&r=' + Math.random());
		},
		/**
		 * 确认删除
		 * @param  {[type]} uri [description]
		 * @return {[type]}     [description]
		 */
		okAction: function(uri) {
			layer.confirm('确认此操作?', {
					btn: ['确认', '取消']
				},
				function(index) {
					layer.close(index);
					$.get(uri, function(data) {
						ank.msg(data);
					});
					// window.location.href = uri;
				},
				function() {

				}
			);

		},
		//页面初始化调用
		init: function() {
			//给元素注册触发ajax-post操作的事件
			$(".ajax-post").bind("click", function() {
				if ($('#yzerr').length > 0) {
					ank.msg('请按格式填写!');
					return false;
				}
				am.ajaxForm($(this));
			});
			$(".ajax-href").bind("click", function() {
				return am.ajaxHref($(this));
			});
			$(".ajax-href-del").bind("click", function() {
				var _this = $(this);
				parent.layer.confirm('确认此操作吗?', {
						btn: ['确认', '取消']
					},
					function(index) {
						layer.close(index);
						return am.ajaxHref(_this);
						// window.location.href = uri;
					}
				);
				return false;
			});
			$(".ajax-list-del").bind("click", function() {
				// if (!confirm("确定此操作吗?")) return false;
				var _this = $(this);

				parent.layer.confirm('确认此操作吗?', {
						btn: ['确认', '取消']
					},
					function(index) {
						layer.close(index);
						var url = _this.attr("href");
						$.ajax({
							type: "POST",
							url: url,
							success: function(da) {
								da.status == '1' && _this.parent().parent().remove();
								ank.msg(da.info);
							},
							dataType: "JSON"
						});
					}
				);
				return false;

			});
			//失去焦点提交事件
			$(".ajax-blur").bind("change", function() {
				var id1 = $(this).attr("data-id");
				var table1 = $(this).attr("data-table");
				var field1 = $(this).attr("data-field");
				var value1 = $(this).val();
				$.ajax({
					type: "POST",
					url: ainiku.updatefield,
					data: {
						id: id1,
						table: table1,
						field: field1,
						value: value1
					},
					success: function(da) {
						ank.msg(da);
					}
				});
			});

			//y n 插件
			$(".yn").each(function(index, element) {
				$(this).parent().css('text-align', 'center');
				var da = parseInt($(this).attr("data-value"));
				if (da === 1) {
					$(this).addClass("y");
				} else {
					$(this).addClass("n");
				}
				$(this).click(function(e) {
					var _this = $(this);
					var data1 = parseInt($(this).attr("data-value"));
					var table1 = $(this).attr("data-table");
					var field1 = $(this).attr("data-field");
					var id1 = $(this).attr("data-id");
					var qh = function() {
						if (_this.hasClass("y")) {
							_this.attr("data-value", 0);
							_this.removeClass("y");
							_this.addClass("n");
						} else {
							_this.attr("data-value", 1);
							_this.removeClass("n");
							_this.addClass("y");
						}
					};
					data1 = data1 ? 0 : 1;
					if (data1 !== "" && table1 !== "" && field1 !== "" && id1 !== "") {
						qh();
						$.ajax({
							type: "POST",
							data: {
								id: id1,
								table: table1,
								field: field1,
								value: data1
							},
							url: ainiku.updatefield,
							success: function(da) {
								ank.msg(da);
							}
						});
					}
				});
			});
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
			var leftside = $('#west');
			var closewest = $('#closewest');
			closewest.click(function(e) {
				var _t = $(this);
				if (_t.css('left') == '180px') {
					_t.animate({
						'left': '-0px'
					}, 200, function() {
						_t.addClass('open');
					});
					leftside.animate({
						'overflow': 'hidden',
						'left': '-180px'
					}, 200);
					$('body').animate({
						'paddingLeft': '0px'
					}, 200);

					am.writeCookie('leftside', 0);
				} else {
					_t.animate({
						'left': '180px'
					}, 200, function() {
						_t.removeClass('open');
					});
					leftside.animate({
						'overflow': 'hidden',
						'left': '0px',
						'overflowY': 'scroll'
					}, 200);
					$('body').animate({
						'paddingLeft': '180px'
					}, 200);


					am.writeCookie('leftside', 1);
				}
			});
			if (am.readCookie('leftside') === '0') {
				leftside.css({
					'overflow': 'hidden',
					'padding': '10px 0px 50px 0px',
					'left': '-180px'
				});

				$('body').css({
					'paddingLeft': '0'
				});
				closewest.css('left', '0px');
				closewest.addClass('open');
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
				var obj = leftside;
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
							leftside.html(da.info);
						} else {
							ank.msg(da.info);
						}

					}
				});
				return false;
			});



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
					ank.msg('请选择后再操作');
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
						ank.msg(da, function(da) {
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
			if ($('#west').length > 0) {
				this.bindLeftMenu();
			}
		},
		//绑定左边菜单
		bindLeftMenu: function() {
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
				am.addtabclose(title, uri);
				return false;
			});
			//设置默认左边菜单////////////////////////////////////////////////////////////////////////

			//默认折叠
			$('.menu .mt a').click();
			am.hasmenu = false;
			$('.menu a').each(function(index, element) {
				var a = window.location.href;
				a = a.replace(/(.*?)\.(.*?)\//, '/');
				a = a.replace('&mainmenu=true', '');
				var b = $(this).attr('href');
				//	var c=b.replace(/(\?)|(\.)|(\=)/g,'\\$1$2$3');
				//	c=c+".*?";
				//	console.log(a.match(/c/));
				if (a == b) {
					am.hasmenu = true;
					$(this).parents('.menu').find('dt a').click();
					$(this).addClass('hover')
				}
			});
			if (!am.hasmenu) {
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

			if (!am.hasmenu) {
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
			if (!am.hasmenu) {
				//如果还没有找到就设置默认的链接
				var objj = $(".menu a[href='" + ainiku.defaultmenu + "']");
				if (objj.length > 0) {
					objj.addClass('hover');
					objj.parents('.menu').find('dt a').click();
				} else {
					$('.menu dt a').eq(0).click();
				}
			}
		},
		//绑定删除图片的按钮
		bindDel: function() {
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
		},
		//对比编辑器内容并删除其中的图片
		delEditorImg: function(scr, dest) {
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
		},
		bindDelAttach: function() {
			$('.upload-pre-file .btn-danger').unbind('click');
			$('.upload-pre-file .btn-danger').click(function(e) {
				var a = $(this).attr('dataid');
				$.get(ainiku.delattach, {
					id: a
				});
				$(this).parent().parent().prev().val('');
				$(this).parent().remove();
			});
		},

		setPosition: function(table1, id1, field1, value1, srcid) {
			//loading层
			var index = layer.load(1, {
				time: 0,
				shade: [0.1, '#fff'] //0.1透明度的白色背景
			});
			$.get(ainiku.setposition, {
					table: table1,
					id: id1,
					field: field1,
					value: value1
				},
				function(da) {
					layer.close(index);
					layer.open({
						shade: 0,
						title: "修改标记",
						type: 1,
						content: da.info,
						area: ['330px'],
						btn: ['保存', '取消'],
						yes: function(index, dom) {
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
									ank.msg('操作成功!');
								}
							});
							layer.close(index);
						},
						cancel: function(index, dom) {
							layer.close(index);
						}
					});
				});
			//////////////////////////////////////////////////////
			//$('body').append('<div id="markbg" class="bg"></div>');
			// $.get(ainiku.setposition, {
			// 	table: table1,
			// 	id: id1,
			// 	field: field1,
			// 	value: value1
			// }, function(da) {
			// 	ank.msgDialog({
			// 		'title': '修改信息',
			// 		'content': da.info,
			// 		'btn': true,
			// 		'ok': function() {
			// 			var formobj = $('#positionform');
			// 			$.ajax({
			// 				type: 'POST',
			// 				url: formobj.attr('action'),
			// 				data: formobj.serialize(),
			// 				success: function(da) {
			// 					var chl = $(srcid).children();
			// 					if (da.status == 1) {
			// 						chl.eq(0).val(da.info[1]);
			// 						chl.eq(1).html(da.info[0]);
			// 					}
			// 				}
			// 			});
			// 		},
			// 		'cancel': function() {}
			// 	});
			// 	$('#markbg').remove();
			// });
		},
		/**
		 *全局ajax提交form表单数据thisobj为触发事件的元素
		 *@param thisobj 触发事件的元素
		 *可以添加另外两个参数第二个控制时间第三个控制刷新
		 *备注：
		 *可以添加两个函数：
		 *_before_post()提交前调用
		 *_after_post()提交后调用
		 */
		ajaxForm: function(thisobj, callback) {
			return ank.ajaxForm(thisobj, callback);
			// thisobj = $(thisobj);
			// // thisobj.addClass("disabled");
			// if (typeof arguments[2] != "undefined") reloadbool = arguments[2];
			// if (typeof arguments[1] != "undefined") msgtime = arguments[1];
			// try {
			// 	(typeof _before_func == "function") && _before_func();
			// 	var thisobj, obj, a, url;
			// 	a = "";

			// 	formobj = thisobj.parents("form");
			// 	if (!formobj) {
			// 		return false;
			// 	}
			// 	formobj.submit(function(e) {
			// 		return false;
			// 	});
			// 	var url = formobj.attr("action");
			// 	postdata = formobj.serialize();
			// 	a = "{" + a + "}";
			// 	b = eval("(" + a + ")");
			// 	// $("body").append('<div id="klbg" class="bg">');
			// 	var lindex = parent.layer.load(1, {
			// 		shade: false //0.1透明度的白色背景
			// 	});
			// 	$.ajax({
			// 		url: url,
			// 		type: "POST",
			// 		datatype: "JSON",
			// 		data: postdata,
			// 		success: function(da) {
			// 			parent.layer.close(lindex);
			// 			ank.msg(da, function() {
			// 				(typeof _after_func == "function") && _after_func(da);
			// 				(typeof callback == "function") && callback(da);
			// 			});

			// 		}
			// 	});
			// } catch (e) {
			// 	alert(e.name + ": " + e.message);
			// }
		},
		ajaxHref: function(obj, callback) {
			return ank.ajaxHref(obj, callback);
			// obj = $(obj);
			// //obj.addClass("disabled");
			// if (typeof arguments[2] != "undefined") reloadbool = arguments[2];
			// if (typeof arguments[1] != "undefined") msgtime = arguments[1];
			// url = obj.attr("href");
			// if (typeof url == "undefined") url = obj.attr("url");
			// // $("body").append('<div id="klbg" class="bg">');
			// var lindex = parent.layer.load(1, {
			// 	shade: false //0.1透明度的白色背景
			// });
			// $.ajax({
			// 	type: "POST",
			// 	url: url,
			// 	success: function(da) {
			// 		parent.layer.close(lindex);
			// 		ank.msg(da, function() {
			// 			(typeof _after_func == "function") && _after_func(da);
			// 			(typeof callback == "function") && callback(da);
			// 		});

			// 	},
			// 	dataType: "JSON"
			// });
			// return false;
		},
		writeCookie: function(name, value, hours) {
			ank.writeCookie(name, value, hours);
			// var expire = "";
			// if (hours != null) {
			// 	expire = new Date(new Date().getTime() + hours * 36e5);
			// 	expire = "; expires=" + expire.toGMTString();
			// }
			// document.cookie = name + "=" + encodeURI(value) + expire;
		},
		readCookie: function(name) {
			return ank.readCookie(name);
			// var cookieValue = "";
			// var search = name + "=";
			// if (document.cookie.length > 0) {
			// 	offset = document.cookie.indexOf(search);
			// 	if (offset != -1) {
			// 		offset += search.length;
			// 		end = document.cookie.indexOf(";", offset);
			// 		if (end == -1) end = document.cookie.length;
			// 		cookieValue = decodeURI(document.cookie.substring(offset, end));
			// 	}
			// }
			// return cookieValue;
		},
		submitArticle: function(obj) {
			var formobj = $(obj).parents("form");
			formobj.submit(function(e) {
				return false;
			});
			var url = formobj.attr("action");
			var postdata = formobj.serialize();
			var lindex = parent.layer.load(1, {
				shade: false //0.1透明度的白色背景
			});
			$.ajax({
				url: url,
				type: "POST",
				datatype: "JSON",
				data: postdata,
				success: function(da) {
					parent.layer.close(lindex);
					ank.msg(da.info);
					var id = $('input[name="article_id"]').val();
					if (id == '') {
						$('input[name="article_id"]').val(da.article_id)
					};
				}
			});
		},
		/**
		 * 文章保存草稿
		 * @param  {[type]} issave   [是否使用草稿]
		 * @param  {[type]} savetime [保存草稿的时间]
		 * @return {[type]}          [description]
		 */
		saveDraftbox: function(issave, savetime) {
			am.draftboxtimeid && clearInterval(am.draftboxtimeid);
			var opendraftbox = issave;
			var subobj = $('#submitbtn');
			var srctext = subobj.html();
			if (opendraftbox == 1) {
				var draftboxtime = savetime;
				var jiantime = draftboxtime;
				am.draftboxtimeid = setInterval(function() {
					var caogao = $('input[name="status"]:checked').val();
					var arccid = $('input[name="article_id"]').val();
					if (caogao == '2') {
						var title = $('input[name="title"]').val();
						if (title != '') {
							$('#submitbtn').html('保存草稿(' + jiantime + ')');
							if ((jiantime--) <= 0) {
								jiantime = draftboxtime;
								am.submitArticle();
							}
						}

					} else {
						jiantime = draftboxtime;
						if (caogao != 2) {
							subobj.html('确定更新');
						} else {
							subobj.html(srctext);
						}
					}
				}, 1000);
			}
		},
		updatePwd: function(uri) {
			//loading层
			var index = layer.load(1, {
				time: 0,
				shade: [0.1, '#fff'] //0.1透明度的白色背景
			});
			$.get(uri, function(data) {
				ank.open({
					title: "修改密码",
					type: 1,
					content: data,
					area: ['300px', '350px']
				});
				layer.close(index);
			});

		},
		modPwd: function(uri) {
			//loading层
			var index = layer.load(1, {
				time: 0,
				shade: [0.1, '#fff'] //0.1透明度的白色背景
			});
			$.get(uri, function(data) {
				ank.open({
					title: "修改密码",
					type: 1,
					content: data,
					area: ['300px', '350px']
				});
				layer.close(index);
			});
		},
		closeTab: function(obj) {
			var obj = $(obj);
			$('#div-block').find('.kl-tab-div').each(function(i) {
				if ($(this).data('tab-index') == obj.parent().data('tab-index')) {
					obj.parents('.kl-tab-nav').remove();
					$(this).remove();
					//查找有没有默认显示的
					if ($('#nav-block .kl-tab-nav.hover').length <= 0) {
						$('#nav-block .kl-tab-nav:last').click();
					}
					return 0;
				}
			});
		},
		addtabclose: function(title, url) {
				$('#loadingimg').show();
				var isyou = false;
				$('.kl-tab iframe').each(function(index, element) {
					var srcurl = $(this).attr('src');
					var par = $(this).parent();
					if (url == srcurl) {
						$(this).remove();
						par.append('<span class="iframeloading"></span><iframe id="tabiframe' + index + '" class="con-iframe" marginWidth=0 frameSpacing=0 marginHeight=0  onload="setIframeHeight(this);" frameborder="0" border="0" src="' + url + '"  noResize width="100%" scrolling=auto  vspale="0"></iframe>');
						//$(this).attr('src',url);
						$('.kl-tab .kl-tab-nav').eq(index).click();
						isyou = true;
						return 0;
					}
				});
				if (isyou) return true;
				$('#nav-block').width($('#div-block').width());
				$('#nav-block').append(' <li class="chrome-tab kl-tab-nav">' + title + '<span class="close" onclick="am.closeTab(this);">X</span><em></em></li>');
				$('#div-block').append(' <div class="kl-tab-div"><span class="iframeloading"></span><iframe class="con-iframe" marginWidth=0 frameSpacing=0 marginHeight=0  onload="setIframeHeight(this);" frameborder="0" border="0" src="' + url + '"  noResize width="100%" scrolling=auto  vspale="0"></iframe></div>');
				$('.kl-tab').mytab();
				$('.kl-tab .kl-tab-nav:last').click();
				rightMenu && rightMenu.init();
			}
			// /**
			//  * textarea自动高度
			//  * @return {[type]} [description]
			//  */
			// autoHeight: function(obj) {
			// 	var _t = $(obj);
			// 	_t.height(obj.scrollHeight);
			// }
	};
	/**
	 * 右键菜单绑定
	 * @type {Object}
	 */
	window.rightMenu = {
		currentTab: null,
		init: function() {
			// debugger;
			$('#youmenu').hover(function() {
				$('#youmenu').show();
			}, function() {
				$('#youmenu').hide();
			});
			//右键菜单
			$('.chrome-tab').bind('contextmenu', function() {
				$('#nav-block .chrome-tab').removeClass('currenttab');
				$(this).addClass('currenttab');
				rightMenu.currentTab = $(this);
				try {
					if (window.event) e = window.event;
					$('#youmenu').css({
						left: e.clientX - 5,
						top: e.clientY - 5
					});
					$('#youmenu').show();
				} catch (e) {}
				return false;
			});
		},
		closeTab: function() {
			this.currentTab && this.currentTab.find('.close').click();
			$('#youmenu').hide();
		},
		closeOtherTab: function() {
			$('#nav-block .chrome-tab').not('.currenttab').find('.close').click();
			$('#youmenu').hide();
		}
	};
	am.init();
	rightMenu.init();
});