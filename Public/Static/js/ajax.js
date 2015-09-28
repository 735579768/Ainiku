// JavaScript Document
//jquery拖动插件
(function($) {
    var data = {
        _x:0,
        _y:0
    };
    $.fn.kldrag = function(options) {
        var defaults = {
            titleheight:50,
            space:false,
            spacestyle:""
        };
        var opts = $.extend(defaults, options);
        this.bind("mousedown", function(e) {
            var xxx = $(this).offset().left;
            var yyy = $(this).offset().top;
			var scryyy=$(document).scrollTop();
            data._x = e.pageX - xxx;
            data._y = e.pageY - yyy;
            if (opts.titleheight === 0 || opts.titleheight >= data._y) {
                $(this).css({
                    zIndex:999999999,
                    position:"fixed",
                    left:xxx + "px",
                    top:yyy-scryyy + "px",
                    margin:"0px"
                });
                if (opts.space) {
                    $("#divspace").remove();
                    $(this).after('<div id="divspace" style="z-index:999;position:fixed;left:' + xxx + "px;top:" + yyy-scryyy + "px;width:" + $(this).outerWidth() + "px;height:" + $(this).outerHeight() + "px;border:dashed 1px #f00;" + opts.spacestyle + '"></div>');
                }
                $(this).css("cursor", "move");
            }
        });
        this.bind("mousemove", function(e) {
            if (opts.titleheight === 0 || opts.titleheight >= data._y) {
                if (e.which === 1) {
					var scryyy=$(document).scrollTop();
                    xx = e.pageX - data._x;
                    yy = e.pageY - data._y-scryyy;
                    $(this).css({
                        zIndex:999999,
                        position:"fixed",
                        left:xx + "px",
                        top:yy + "px"
                    });
                }
            }
        });
        this.bind("mouseup", function() {
            $(this).css("cursor", "auto");
            $("#divspace").remove();
        });
    };
})(jQuery);

$(function() {
    window.ajaxhref = function(obj) {
        obj.addClass("disabled");
        if (typeof arguments[2] != "undefined") reloadbool = arguments[2];
        if (typeof arguments[1] != "undefined") msgtime = arguments[1];
        url = obj.attr("href");
        if (typeof url == "undefined") url = obj.attr("url");
        $("body").append('<div id="klbg" class="bg">');
        $.ajax({
            type:"POST",
            url:url,
            success:function(da) {
                topmsg(da, function() {
                    obj.removeClass("disabled");
                });
            },
            dataType:"JSON"
        });
        return false;
    };
    /**
  *全局ajax提交form表单数据thisobj为触发事件的元素
  *@param thisobj 触发事件的元素
  *可以添加另外两个参数第二个控制时间第三个控制刷新
  *备注：
  *可以添加两个函数：
  *_before_post()提交前调用
  *_after_post()提交后调用
  */
    window.ajaxform = function(thisobj, callback) {
        thisobj.addClass("disabled");
        if (typeof arguments[2] != "undefined") reloadbool = arguments[2];
        if (typeof arguments[1] != "undefined") msgtime = arguments[1];
        try {
            if (typeof _before_post == "function") _before_post();
            if (typeof _before_func == "function") _before_func();
            var thisobj, obj, a, url;
            a = "";
            formobj = thisobj.parents("form");
            formobj.submit(function(e) {
                return false;
            });
            url = formobj.attr("action");
            postdata = formobj.serialize();
            a = "{" + a + "}";
            b = eval("(" + a + ")");
            $("body").append('<div id="klbg" class="bg">');
            $.ajax({
                url:url,
                type:"POST",
                datatype:"JSON",
                data:postdata,
                success:function(da) {
                    topmsg(da, function() {
                        thisobj.removeClass("disabled");
                        if (typeof callback === "function") {
                            callback(da);
                        }
                    });
                }
            });
        } catch (e) {
            alert(e.name + ": " + e.message);
        }
    };
    //网页上面弹出信息
    window.topmsg2= function(da, callback) {
        var str = typeof da === "string" ? da :da.info;
        var uri = typeof da === "string" ? "" :da.url;
        var data = {
            url:uri,
            info:str,
            msgtime:2
        };
        var msgtime = data.msgtime;
        //信息显示时间
        $("body").css("posttion", "relative");
        $("#topmsg").remove();
        var xiaolian = da.status == "0" ? ">_<" :"o_0";
        $("body").append('<div id="topmsg" style="z-index:99999;padding:15px 20px;font-weight:bolder;text-align:center; color:#f00;display:block;position:fixed;top:0px; background:#fff; left:50%;border-radius: 10px;-webkit-box-shadow: 0px 4px 13px rgba(0,0,0,0.30);-moz-box-shadow: 0px 4px 13px rgba(0,0,0,0.30);box-shadow: 0px 4px 13px rgba(0,0,0,0.30);_left:45%;_position:absolute;_bottom:auto;_top:expression(eval(document.documentElement.scrollTop));">' + xiaolian + " ，" + data.info + "<span>," + msgtime + "</span></div>");
        $("#topmsg").css({
            marginLeft:"-" + $("#topmsg").outerWidth() / 2 + "px",
            top:"-" + $("#topmsg").outerHeight() + "px"
        });
        $("#topmsg").stop(true).animate({
            top:$("#topmsg").outerHeight() / 2 + 20 + "px"
        }, 200, function() {
            $("#topmsg").stop(true).animate({
                top:"20px"
            }, 100);
        });
        if (da.status == "0") {
            $("#topmsg").css({
                background:"#ff6666",
                color:"#ffffff"
            });
        } else {
            $("#topmsg").css({
                background:"#4bbd00",
                color:"#ffffff"
            });
        }
        //倒计时
        window.msgtimeid = setInterval(function() {
            if (msgtime > 1) {
                $("#topmsg span").html("," + --msgtime);
            } else {
                clearInterval(msgtimeid);
                $("#topmsg").stop(true).animate({
                    top:$("#topmsg").outerHeight() / 2 + "px"
                }, 100, function() {
                    $("#topmsg").stop(true).animate({
                        top:"-" + $("#topmsg").outerHeight() + "px",
                        opacity:0
                    }, 200, function() {
                        $("#topmsg").remove();
                        $("#klbg").remove();
                        $(".disabled").removeClass("disabled");
                        //如果返回的有地址的话就直接转向
                        if (data.url != "" && data.url != "undefined") {
                            window.location = data.url;
                        }
                        //最后处理代码在此添加(如果没有回调函数的话)
                        if (typeof callback == "function") {
                            callback(da);
                        }
                        //如果有后调函数的话调用
                        if (typeof _after_post == "function") {
                            _after_post(da);
                        }
                        if (typeof _after_func == "function") {
                            _after_func(da);
                        }
                    });
                });
            }
        }, 1e3);
    };

    //网页上面弹出信息
    window.topmsg= function(da, callback) {
        var str = typeof da === "string" ? da :da.info;
        var uri = typeof da === "string" ? "" :da.url;
        var data = {
            url:uri,
            info:str,
            msgtime:2
        };
        var msgtime = data.msgtime;
        //信息显示时间
        $("body").css("posttion", "relative");
        $("#topmsg").remove();
        var xiaolian = da.status == "0" ? ">_<" :"o_0";
        $("body").append('<div id="topmsg" style="display:none;top:30%;z-index:99999;padding:15px 20px;font-weight:bolder;text-align:center; color:#f00;display:block;position:fixed; background:#fff; left:50%;border-radius: 10px;-webkit-box-shadow: 0px 4px 13px rgba(0,0,0,0.30);-moz-box-shadow: 0px 4px 13px rgba(0,0,0,0.30);box-shadow: 0px 4px 13px rgba(0,0,0,0.30);_left:45%;_position:absolute;_bottom:auto;_top:expression(eval(document.documentElement.scrollTop));">' + xiaolian + " ，" + data.info + "<span>," + msgtime + "</span></div>");
        $("#topmsg").css({
            marginLeft:"-" + $("#topmsg").outerWidth() / 2 + "px",
            marginTop:"-" + $("#topmsg").outerHeight()*2 + "px",
			display:'block'
        });
        $("#topmsg").stop(true).animate({
            marginTop:'-'+$("#topmsg").outerHeight() / 2 + "px"
        }, 200, function() {
//            $("#topmsg").stop(true).animate({
//                top:"20px"
//            }, 100);
        });
        if (da.status == "0") {
            $("#topmsg").css({
                background:"#ff6666",
                color:"#ffffff"
            });
        } else {
            $("#topmsg").css({
                background:"#4bbd00",
                color:"#ffffff"
            });
        }
        //倒计时
        window.msgtimeid = setInterval(function() {
            if (msgtime > 1) {
                $("#topmsg span").html("," + --msgtime);
            } else {
                clearInterval(msgtimeid);
                $("#topmsg").stop(true).animate({
                 //   top:$("#topmsg").outerHeight() / 2 + "px",
					marginTop:"-" + $("#topmsg").outerHeight() *2+ "px"
                }, 200, function() {
                    $("#topmsg").stop(true).animate({
                        //top:"-" + $("#topmsg").outerHeight() + "px",
                        opacity:0
                    }, 200, function() {
                        $("#topmsg").remove();
                        $("#klbg").remove();
                        $(".disabled").removeClass("disabled");
                        //如果返回的有地址的话就直接转向
                        if (data.url != "" && data.url != "undefined") {
                            window.location = data.url;
                        }
                        //最后处理代码在此添加(如果没有回调函数的话)
                        if (typeof callback == "function") {
                            callback(da);
                        }
                        //如果有后调函数的话调用
                        if (typeof _after_post == "function") {
                            _after_post(da);
                        }
                        if (typeof _after_func == "function") {
                            _after_func(da);
                        }
                    });
                });
            }
        }, 1e3);
    };
    //给元素注册触发ajax-post操作的事件
    $(".ajax-post").bind("click", function() {
        $(this).addClass("disabled");
        ajaxform($(this));
    });
    $(".ajax-href").bind("click", function() {
        $(this).addClass("disabled");
        return ajaxhref($(this));
    });
    $(".ajax-href-del").bind("click", function() {
       // if (!confirm("确定此操作吗?")) return false;
	   var _this=$(this);
		msgDialog({
			'btn':true,
			'content':'确定此操作吗?',
			'ok':function(){
				_this.addClass("disabled");
				return ajaxhref(_this);
				},
			'cancel':function(){}
			});
			return false;
        
        
    });
    //失去焦点提交事件
    $(".ajax-blur").bind("blur", function() {
        var id1 = $(this).attr("data-id");
        var table1 = $(this).attr("data-table");
        var field1 = $(this).attr("data-field");
        var value1 = $(this).val();
        $.ajax({
            type:"POST",
            url:ainiku.updatefield,
            data:{
                id:id1,
                table:table1,
                field:field1,
                value:value1
            },
            success:function(da) {
                $.myMsg.msg({
                    content:da.info,
                    bg:false,
                    shijian:1
                });
            }
        });
    });
	    window.msgDialog = function(opts) {
		$('#dialog-wrap').length&&$('#dialog-wrap').remove();
		var defaults={oktitle:'确定',canceltitle:'取消',title:'提示信息',content:'啊哦没有信息',url:null,btn:false,ok:function(da){},cancel:function(da){}};
		var types=typeof(opts);
		if(types==='object'){
			opts=$.extend(defaults,opts);
		}else if(types==='string'){
			defaults.content=opts;	
				}
        opts=defaults;
           
			
			var btn='<div class="dialog-btn cl fr" style="margin:5px 5px 5px 100px;"><a class="btn" id="dialog-ok" href="javascript:;">'+opts.oktitle+'</a><a class="btn" id="dialog-cancel" href="javascript:;">'+opts.canceltitle+'</a></div>';
			if(!opts.btn)btn='';
			this.setdialog=function(){
				   var dialog = $("#dialog");
					dialog.kldrag();
					$("#dialog-close").click(function(e) {
						$("#dialog-wrap").remove();
					});
					$("#dialog-ok").click(function(e) {
						opts.ok();
						$("#dialog-wrap").remove();
						
					});
					$("#dialog-cancel").click(function(e) {
						$("#dialog-wrap").remove();
						opts.cancel();
					});
					var w = dialog.width();
					var h = dialog.height();
					dialog.css({
						marginTop:"-" + h / 2 + "px",
						marginLeft:"-" + w / 2 + "px"
					});				
				};			
			if(opts.url===null){
            $("body").append('<div id="dialog-wrap"><div class="bg" style="background-image:none;"></div><table class="dialog" id="dialog" cellpadding="0" cellspacing="0"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l"></td><td class="m_c">		<div class="bm">				<div class="bm_h cl"><span><a href="javascript:;" class="flbc" id="dialog-close" title="关闭">关闭</a></span><h2 class="dialogh2">' + opts.title + '</h2></div><div class="bm_c" id="dialog-con">' + opts.content+'</div>' +btn +'</div></td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table></div>');
			this.setdialog();
			}else{
				$("body").append('<div id="dialog-wrap"><div class="bg"></div></div>');
				$.get(opts.url, function(data) {
					$('#dialog-wrap .bg').css('backgroundImage','none');
					$('#dialog-wrap').append('<table class="dialog" id="dialog" cellpadding="0" cellspacing="0"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l"></td><td class="m_c">		<div class="bm">				<div class="bm_h cl"><span><a href="javascript:;" class="flbc" id="dialog-close" title="关闭">关闭</a></span><h2 class="dialogh2">' + opts.title + '</h2></div><div class="bm_c" id="dialog-con">' +data + '</div>' +btn +'</div></td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>');
				});		
				this.setdialog();		
				}

			

    };
    window.dialogAjax = function(url, title, callback) {
        title = title ? title :"";
		$("body").append('<div id="dialog-wrap"><div class="bg"></div></div>');
        $.get(url, function(data) {
            //$('body').append('<div id="dialog"><div class="bg"></div><div id="dialog-content">'+data+'<div id="dialog-close">X</div></div></div>');
			$('#dialog-wrap .bg').css('backgroundImage','none');
			$('#dialog-wrap').append('<table class="dialog" id="dialog" cellpadding="0" cellspacing="0"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l"></td><td class="m_c">		<div class="bm">				<div class="bm_h cl"><span><a href="javascript:;" class="flbc" id="dialog-close" title="关闭">关闭</a></span><h2 class="dialogh2">' + title + '</h2></div><div class="bm_c" id="dialog-con">' + data + '</div></div></td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>');
            //$("body").append('<div id="dialog-wrap"><div class="bg"></div></div>');
            var dialog = $("#dialog");
            dialog.kldrag();
            $("#dialog-close").click(function(e) {
                $("#dialog-wrap").remove();
            });
            var w = dialog.width();
            var h = dialog.height();
            dialog.css({
                marginTop:"-" + h / 2 + "px",
                marginLeft:"-" + w / 2 + "px"
            });
            if (typeof callback === "function") {
                callback(data);
            }
        });
    };
    window.dialogsubmit = function(obj) {
        ajaxform($(obj), function(da) {
            if (da.status === 1) {
                $("#dialog-wrap").remove();
            }
        });
    };
    //y n 插件
    $(".yn").each(function(index, element) {
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
            data1 = data1 ? 0 :1;
            if (data1 !== "" && table1 !== "" && field1 !== "" && id1 !== "") {
                $.ajax({
                    type:"POST",
                    data:{
                        id:id1,
                        table:table1,
                        field:field1,
                        value:data1
                    },
                    url:ainiku.updatefield,
                    success:function(da) {
                        if (da.status == "0") {
                            topmsg(da, 1, true);
                        } else {
                            if (_this.hasClass("y")) {
                                _this.attr("data-value", 0);
                                _this.removeClass("y");
                                _this.addClass("n");
                            } else {
                                _this.attr("data-value", 1);
                                _this.removeClass("n");
                                _this.addClass("y");
                            }
                        }
                    }
                });
            }
        });
    });
});

// JavaScript Document
///////////////////////////////////////////////////////////////////////js写cookies//////////////////////////////////////////////////
function writeCookie(name, value, hours) {
    var expire = "";
    if (hours != null) {
        expire = new Date(new Date().getTime() + hours * 36e5);
        expire = "; expires=" + expire.toGMTString();
    }
    document.cookie = name + "=" + escape(value) + expire;
}

///////////////////////////////////////////////////////////////用cookies名字读它的值////////////////////////////
function readCookie(name) {
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
}