;
(function($, window) {
	/*********************私有方法*******************************/
	//判断一个变量的类型
	var _gtype = function(options) {
		return typeof(options);
	};
	//ainiku声明
	//给ainiku添加原型方法
	var ainiku = {};
	ainiku.fn = ainiku.prototype = {
		//一些公共的配置项
		config: {
			zindex: 9999,
			animate: false,
			version: '1.0.0'
		},

		//初始化对象
		init: function(config) {
			this.extend(config, this.config);
			return this;
		},
		//拖动函数
		drag: function(options) {
			//配置项
			var conf = {
				obj: null, //拖动对象
				titleheight: 50,
				space: false,
				spacestyle: ""
			};
			var _this = this;
			var doc = $(document);
			var a = {
				xxx: 0, //元素到左边的距离
				yyy: 0, //元素到顶边的距离
				_x: 0, //鼠标到元素左边距离
				_y: 0, //鼠标到元素顶边距离
				_mouseDown: false
			};

			var opts = this.extend(options, conf);
			var _t = opts.obj;
			var setxy = function(e) {
				a.xxx = _t.offset().left;
				a.yyy = _t.offset().top;
				a._x = e.pageX - a.xxx;
				a._y = e.pageY - a.yyy;
			};
			var ismove = function(e) {
				if (_t.offset().top >= 0 && (opts.titleheight === 0 || opts.titleheight >= e.pageY - _t.offset().top)) {
					_t.css("cursor", "move");
					return true;
				} else {
					_t.css("cursor", "auto");
					return false;
				}
			};
			_t.bind("mousedown", function(e) {
				a._mouseDown = true;
				setxy(e);
			});
			_t.bind("mousemove", function(e) {
				if (ismove(e)) {
					if (a._mouseDown) {
						var scryyy = doc.scrollTop();
						xx = e.pageX - a._x;
						yy = e.pageY - a._y - scryyy;
						_t.css({
							zIndex: _this.config.zindex - 1,
							position: "fixed",
							left: xx,
							top: (yy > 0) ? yy : 0,
							margin: 0
						});
					}
				}
				return false;
			});
			_t.bind("mouseup mouseout", function() {
				a._mouseDown = false;
			});
		},
		//特效函数
		show: function(obj, callback) {
			if (this.animate) {
				var w = obj.width();
				var h = obj.height();
				var ww = obj.outerWidth() / 2;
				var hh = obj.outerHeight() / 2;
				obj.css({
					display: 'block',
					opacity: 0,
					width: 10,
					height: 10,
					marginLeft: -5,
					marginTop: -5
				});
				obj.animate({
					opacity: 1,
					width: w,
					height: h,
					marginLeft: -ww,
					marginTop: -hh
				}, 300, function() {});
			} else {
				(typeof(callback) === 'function') && callback(obj);
			}
		},
		hide: function(obj, callback) {
			if (this.animate) {
				var w = obj.width();
				var h = obj.height();
				var ww = obj.outerWidth() / 2;
				var hh = obj.outerHeight() / 2;
				obj.css({
					top: '45%',
					left: '50%',
					marginLeft: -ww,
					marginTop: -hh
				});
				obj.animate({
					opacity: 0,
					width: 0,
					height: 0,
					marginLeft: 0,
					marginTop: 0
				}, 200, function() {
					obj.remove();
					(typeof(callback) === 'function') && callback(obj);
				});
			} else {
				obj.remove();
				(typeof(callback) === 'function') && callback(obj);
			}
		},
		//对话框
		msgDialog: function(opts) {
			var _this = this;
			var conf = {
				width: 0,
				height: 0,
				oktitle: '确定',
				canceltitle: '取消',
				title: '提示信息',
				content: '啊哦没有信息',
				url: null,
				btn: false,
				iframe: null,
				ok: function(da) {},
				cancel: function(da) {}
			};
			$('#dialog-wrap').length && $('#dialog-wrap').remove();

			(typeof(opts) === 'object') && (opts = $.extend(conf, opts));
			(typeof(opts) === 'string') && (conf.content = opts);
			opts = conf;
			var btn = '<div class="dialog-btn cl fr" style="margin:5px 5px 5px 100px;"><a class="btn" id="dialog-ok" href="javascript:;">' + opts.oktitle + '</a><a class="btn" id="dialog-cancel" href="javascript:;">' + opts.canceltitle + '</a></div>';
			var style = "<style>.bg{position:fixed;_position:absolute;z-index:" + (this.config.zindex - 1) + ";top:0px;left:0px;width:100%;_width:expression(document.documentElement.scrollWidth);height:100%;_height:expression(document.documentElement.scrollHeight);background:rgb(0,0,0);background:url(../images/loading.gif) center center no-repeat rgba(0,0,0,0.1);filter:alpha(opacity=0.1);}#dialog-wrap *{font-family:Microsoft Yahei;}#dialog-wrap a{text-decoration:none;}#dialog{overflow:hidden;position:fixed;z-index:" + this.config.zindex + ";top:45%;left:50%;font-size:14px;color:#333;opacity:1;_position:absolute;_width:expression((this.clientWidth<100)?'100px':'auto');_height:expression((this.clientHidth<50)?'50px':'auto');_bottom:auto;_top:expression(eval(document.documentElement.scrollTop+(document.documentElement.clientHeight-this.offsetHeight)/2));_padding-bottom:10px;}#dialog h2{font-size:14px;line-height:31px;white-space:nowrap;overflow:hidden;padding:0 10px;height:31px;font-weight:bolder;}#dialog .t_l,#dialog .t_c,#dialog .t_r,#dialog .m_l,#dialog .m_r,#dialog .b_l,#dialog .b_c,#dialog .b_r{overflow:hidden;background:#000;opacity:0.2;filter:alpha(opacity=20);}#dialog .t_l,#dialog .t_r,#dialog .b_l,#dialog .b_r{width:6px;height:6px;}#dialog .t_c,#dialog .b_c{height:6px;}#dialog .m_l,#dialog .m_r{width:6px;}#dialog .t_l{-moz-border-radius:6px 0 0 0;-webkit-border-radius:6px 0 0 0;border-radius:6px 0 0 0;}#dialog .t_r{-moz-border-radius:0 6px 0 0;-webkit-border-radius:0 6px 0 0;border-radius:0 6px 0 0;}#dialog .b_l{-moz-border-radius:0 0 0 6px;-webkit-border-radius:0 0 0 6px;border-radius:0 0 0 6px;}#dialog .b_r{-moz-border-radius:0 0 6px 0;-webkit-border-radius:0 0 6px 0;border-radius:0 0 6px 0;}#dialog .m_c{background:#FFF;}#dialog .m_c .tb{margin:0 0 10px;padding:0 10px;}#dialog .m_c .c{padding:0;}#dialog .m_c .o{padding:8px 6px 8px 20px;height:26px;text-align:right;border-top:1px solid #ededed;background:#F7F7F7;}#dialog .m_c .el{width:420px;}#dialog .m_c .el li{padding:0;border:none;}#dialog .flbc{float:right;margin:3px;font-size:16px;font-weight:bolder;position:relative;right:0px;display:inline-block;overflow:hidden;margin-right:10px;}#dialog .flbc:hover{color:#f00;}#dialog .bm_h{background:#fdfdfd;border-bottom:1px solid #ededed;}#dialog #dialog-con{padding:10px 20px;}#dialog .bm_h{border-bottom:1px solid #CDCDCD;}</style>";
			opts.btn || (btn = '');
			$("body").append('<div id="dialog-wrap">' + style + '<div class="bg" style="background-image:none;"></div><table class="dialog" id="dialog" cellpadding="0" cellspacing="0"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l"></td><td class="m_c">		<div class="bm">				<div class="bm_h cl"><span><a href="javascript:;" class="flbc" id="dialog-close" title="关闭">X</a></span><h2 class="dialogh2">' + opts.title + '</h2></div><div class="bm_c" id="dialog-con"><div id="dialog-conn"></div></div>' + btn + '</div></td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table></div>');
			var dialogwrap = $('#dialog-wrap');
			var dialog = $('#dialog');
			var dialogcon = $('#dialog-con');
			var dialogconn = $('#dialog-conn');
			var dialogclose = $("#dialog-close");
			var setdialog = function() {
				_this.drag({
					obj: dialog
				});
				dialogclose.click(function(e) {
					_this.hide(dialog, function() {
						dialogwrap.remove();
					});
					opts.cancel();
				});
				$("#dialog-ok,.bindok").click(function(e) {
					opts.ok();
					_this.hide(dialog, function() {
						dialogwrap.remove();
					});

				});
				$("#dialog-cancel,.bindcancel").click(function(e) {
					_this.hide(dialog, function() {
						dialogwrap.remove();
					});
					opts.cancel();
				});
				var w = dialog.outerWidth();
				var h = dialog.outerHeight();
				dialog.css({
					marginTop: "-" + h / 2 + "px",
					marginLeft: "-" + w / 2 + "px"
				});
				_this.show(dialog);
			};

			if (opts.iframe !== null) {
				dialogcon.html('<iframe width="100%" frameborder="0"  src="' + opts.iframe + '"></iframe>');
				dialogcon.css({
					padding: '0px'
				});
			} else if (opts.url !== null) {
				dialog.hide();
				$.get(opts.url, function(data) {
					dialogconn.html(data);
					dialog.show();
					setdialog();
				});
			} else {
				dialogconn.html(opts.content);
			}
			opts.width && opts.height && (dialogcon.children().eq(0).css({
				width: opts.width,
				height: opts.height
			}));
			setdialog();
		},

		/**表单提交**/
		ajaxform: function(opts) {
			var conf = {
				_this: thisobj,
				_before_post: function() {},
				_after_post: function() {},
				success: function() {}
			};
			opts = $.extend(conf, opts);
			try {
				opts._before_post(opts);
				var thisobj = $(opts._this);
				var obj = null;
				var url = '';
				formobj = thisobj.parents("form");
				formobj.submit(function(e) {
					return false;
				});
				url = formobj.attr("action");
				postdata = formobj.serialize();
				$.ajax({
					url: url,
					type: "POST",
					datatype: "JSON",
					data: postdata,
					success: function(da) {
						opts.success(da);
						opts._after_post(da);
					}
				});
			} catch (e) {
				alert(e.name + ": " + e.message);
			}
		},

		/**写入cookies**/
		writeCookie: function(name, value, hours) {
			var expire = "";
			if (hours != null) {
				expire = new Date(new Date().getTime() + hours * 36e5);
				expire = "; expires=" + expire.toGMTString();
			}
			document.cookie = name + "=" + escape(value) + expire;
		},

		/**读取cookies**/
		readCookie: function(name) {
			var cookieValue = "";
			var search = name + "=";
			if (document.cookie.length > 0) {
				offset = document.cookie.indexOf(search);
				if (offset != -1) {
					offset += search.length;
					end = document.cookie.indexOf(";", offset);
					if (end == -1) end = document.cookie.length;
					cookieValue = unescape(document.cookie.substring(offset, end));
				}
			}
			return cookieValue;
		},

		//弹出消息框并自己消失
		msg: function(options, callback) {
			var texiao = 0; //默认动画效果
			var content = '没有消息!';
			var url = '';
			if (typeof(options) == 'object') {
				content = options['info'];
				(options.status == '0') && (texiao = 6);
				options.url && (url = options.url.replace(' ', ''));
			} else {
				content = options;
			}

			var param = [
				content, {
					shift: texiao,
				},
				function() {
					(typeof(callback) == 'function') && callback(options);
					(url != '') && (window.location.href = url);
				}
			];

			if (parent) {
				parent.layer.msg.apply(parent.layer, param);
			} else {
				layer.msg.apply(layer, param);
			}
			// ($('#kl-msg-wrap').length >= 1) && $('#kl-msg-wrap').remove();
			// var _this = this;
			// var args = arguments;
			// var conf = {
			// 	status:1,
			// 	content: '没有消息哦！',
			// 	style: '', //算定义样式
			// 	delay: 2,
			// 	success: function() {}
			// };
			// if(typeof(args[0])==='object'){
			// 	for (var name in args[0]) {
			// 		conf[name] = args[0][name];
			// 	}
			// 	conf.content=args[0]['info'];
			// }else if (args[0] && args[1] && args[2]) {
			// 	conf.content = args[0], conf.success = args[1], conf.delay = parseInt(args[2]);
			// 	_gtype(args[1]) === 'number' && (conf.delay = parseInt(args[1]));
			// 	_gtype(args[1]) === 'function' && (conf.success = args[1]);
			// } else if (args[0] && args[1]) {
			// 	conf.content = args[0];
			// 	_gtype(args[1]) === 'number' && (conf.delay = parseInt(args[1]));
			// 	_gtype(args[1]) === 'function' && (conf.success = args[1]);
			// } else {
			// 	_gtype(options) === 'string' && (conf.content = options);
			// 	_gtype(options) === 'object' && (conf = this.extend(options, conf));
			// }
			// conf.status==1?conf.status='msgok':conf.status='msgts';
			// var style = "<style>#kl-msg-wrap *{font:14px/1.5 'microsoft yahei';}#kl-msg{position:fixed;top:40%;left:50%;border:solid 2px #C3C3C3;z-index:" + this.config.zindex + ";background:#fff;padding:10px 20px;}" + conf.style + "</style>";
			// var html = '<div id="kl-msg-wrap">' + style + '<div id="kl-msg"><span class="'+conf.status+'"></span>' + conf.content + '</div></div>';
			// $('body').append(html);
			// var obj = $('#kl-msg');
			// obj.css({
			// 	marginLeft: -obj.outerWidth() / 2,
			// 	marginTop: -obj.outerHeight() / 2
			// });
			// setTimeout(function() {
			// 	typeof(conf.success) === 'function' && conf.success(conf);
			// 	$('#kl-msg-wrap').remove();
			// }, conf.delay * 1000);
		},

		open: function() {
			if (parent) {
				parent.layer.open.apply(parent.layer, arguments);
			} else {
				layer.open.apply(layer, arguments);
			}
		},
		//冒泡信息提示
		maopao: function(options) {
			var conf = {
				'effect': 'fade',
				'title': '提示',
				'content': '',
				'button': false,
				'autohidden': false,
				'iframe': null,
				'width': null,
				'height': null,
				'shijian': 10
			};
			var msgHidden = function() {
				$('.klmpmsg').animate({
					bottom: '-' + $('.klmpmsg').outerHeight()
				}, 2000, function() {
					$('.klmpmsg').remove();
					$('#dialogstyle').remove();
					window.clearInterval(window.maopaoid);
				});
			};
			var dialogstyle = "<style id='dialogstyle'>.klmpmsg{border-radius:5px;box-shadow:2px 2px 5px #cecece;font-family:Microsoft Yahei;overflow:hidden;position:fixed;z-index:" + this.config.zindex + ";bottom:10px;right:10px;min-height:50px;min-width:100px;background-color:#FFF;border:5px solid #999;font-family:Microsoft Yahei;font-size:14px;color:#333;opacity:1;border:5px solid rgba(0,0,0,0.3);_position:absolute;_bottom:auto;_top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||0)-(parseInt(this.currentStyle.marginBottom,10)||0)));}.klmpmsg .klmpmsgbar{height:20px;padding:5px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-weight:bold;border-bottom:solid 1px #cecece;}.klmpmsg .klmpmsgbar .klmptitle{color:red;float:left;padding:0px 10px 0px 0px;}.klmpmsg .klmpmsgbar .klclose{float:right;padding:0px 10px;font-weight:bolder;cursor:pointer;color:#f00;}.klmpmsg .klmpmsgc iframe{border:none;width:100%;height:100%;}.klmpmsg .klmpmsgc{width:100%;height:100%;padding:10px;}.klmpmsg .klmpmsgbtn{float:right;display:block;padding:10px;}.klmpmsg .klmpmsgbtn a{color:#ffffff;background-color:#FE8431;border-color:#FE8431;text-decoration:none;width:auto;padding:3px 15px;font-size:16px;text-align:center;cursor:pointer;}</style>";
			$('#dialogstyle').remove();
			$('.klmpmsg').remove();
			window.clearInterval(window.maopaoid);
			$('body').append(dialogstyle);
			if (typeof(options) !== 'string') {
				conf = $.extend(options, conf);
			} else {
				conf.content = options;
			}
			var msgbtn = '<div class="klmpmsgbtn"><a href="javascript:;">确定</a></div>';
			if (!conf.button) msgbtn = '';
			if (conf.iframe != null) {
				conf.content = '<iframe src="' + conf.iframe + '"></iframe>';
			}
			var divstr = '<div class="klmpmsg"><div class="klmpmsgbar"><div class="klmptitle">' + conf.title + '</div><div class="klclose" title="点击关闭">X</div></div><div class="klmpmsgc">' + conf.content + '</div>' + msgbtn + '</div>';
			$('body').append(divstr);
			$('.klmpmsg').width($('.klmpmsg').outerWidth());
			$('.klmpmsg .klmpmsgbtn a,.klmpmsg .klmpmsgbar .klclose').bind('click',
				function() {
					msgHidden();
				});
			$('.klmpmsg').css('bottom', '-' + $('.klmpmsg').outerHeight() + 'px');
			$('.klmpmsg').animate({
				bottom: '20px'
			}, 1000, function() {
				$('.klmpmsg').animate({
						bottom: '10px'
					},
					300);
			});
			if (conf.autohidden) {
				setTimeout(function() {
						$.maopaoMsg.msgHidden();
					},
					conf.shijian * 1000);
			}
			window.maopaoid = window.setInterval(function() {
					var a = $('.klmptitle').html();
					if (a == conf.title) {
						$('.klmptitle').html('');
					} else {
						$('.klmptitle').html(conf.title);
					}
				},
				1000);
		},

		/**实现功能扩展**/
		extend: function() {
			var arg0 = arguments[0] || {};
			var target = arguments[1] || this;
			for (var name in arg0) {
				target[name] = arg0[name];
			}
			return target;
		},
		/**AJAX请求**/
		ajax: function(a) {
			a = a || {};
			var b = {
				type: 'POST',
				url: "",
				data: {},
				success: function(data) {},
				timeout: function(data) {}
			};
			b = this.extend(a, b);
			$.ajax(b);
		},
		get: function(uri, da, callback) {
			this.ajax({
				type: 'GET',
				url: uri,
				data: da,
				success: callback
			});
		},
		loadhtml: function(uri, da, obj, tpe) {
			(typeof tpe === 'undefined') && (tpe = 'GET');
			tpe = tpe.toUpperCase();
			obj.html('正在加载数据...');
			this.ajax({
				type: tpe,
				url: uri,
				data: da,
				success: function(data) {
					obj.html(data);
				}
			});
		},
		post: function(url, da, callback) {
			this.ajax({
				type: 'POST',
				url: uri,
				data: da,
				success: callback
			});
		}
	};

	//把ainiku设置到全局变量
	window.ank = ainiku.fn.init({});
	//ainiku结束
})($, window);