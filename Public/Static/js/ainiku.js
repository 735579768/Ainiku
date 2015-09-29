;(function($,b){
//ainiku声明
function ainiku(){}
//给ainiku添加原型方法

/**拖动功能***/
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

/**对话框**/
var msgDialog=function(opts){
	var setdialog=function(){
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
		$('#dialog-wrap').length&&$('#dialog-wrap').remove();
		var defaults={
			width:0,
			height:0,
			oktitle:'确定',
			canceltitle:'取消',
			title:'提示信息',
			content:'啊哦没有信息',
			url:null,
			btn:false,
			iframe:null,
			ok:function(da){},
			cancel:function(da){}
			};
		(typeof(opts)==='object')&&(opts=$.extend(defaults,opts));
		(typeof(opts)==='string')&&(defaults.content=opts);
        opts=defaults;
			var btn='<div class="dialog-btn cl fr" style="margin:5px 5px 5px 100px;"><a class="btn" id="dialog-ok" href="javascript:;">'+opts.oktitle+'</a><a class="btn" id="dialog-cancel" href="javascript:;">'+opts.canceltitle+'</a></div>';
			var style="<style>#dialog-wrap *{margin:0px;padding:0px;font-family:Microsoft Yahei;}#dialog-wrap a{text-decoration:none;color:#000;}#dialog{overflow:hidden;position:fixed;z-index:9991;top:40%;left:50%;font-size:14px;color:#333;opacity:1;_position:absolute;_width:expression((this.clientWidth<100)?'100px':'auto');_height:expression((this.clientHidth<50)?'50px':'auto');_bottom:auto;_top:expression(eval(document.documentElement.scrollTop+(document.documentElement.clientHeight-this.offsetHeight)/2));_padding-bottom:10px;}#dialog h2{font-size:14px;line-height:31px;white-space:nowrap;overflow:hidden;padding:0 10px;height:31px;font-weight:bolder;}#dialog .t_l,#dialog .t_c,#dialog .t_r,#dialog .m_l,#dialog .m_r,#dialog .b_l,#dialog .b_c,#dialog .b_r{overflow:hidden;background:#000;opacity:0.2;filter:alpha(opacity=20);}#dialog .t_l,#dialog .t_r,#dialog .b_l,#dialog .b_r{width:6px;height:6px;}#dialog .t_c,#dialog .b_c{height:6px;}#dialog .m_l,#dialog .m_r{width:6px;}#dialog .t_l{-moz-border-radius:6px 0 0 0;-webkit-border-radius:6px 0 0 0;border-radius:6px 0 0 0;}#dialog .t_r{-moz-border-radius:0 6px 0 0;-webkit-border-radius:0 6px 0 0;border-radius:0 6px 0 0;}#dialog .b_l{-moz-border-radius:0 0 0 6px;-webkit-border-radius:0 0 0 6px;border-radius:0 0 0 6px;}#dialog .b_r{-moz-border-radius:0 0 6px 0;-webkit-border-radius:0 0 6px 0;border-radius:0 0 6px 0;}#dialog .m_c{background:#FFF;}#dialog .m_c .tb{margin:0 0 10px;padding:0 10px;}#dialog .m_c .c{padding:0;}#dialog .m_c .o{padding:8px 6px 8px 20px;height:26px;text-align:right;border-top:1px solid #ededed;background:#F7F7F7;}#dialog .m_c .el{width:420px;}#dialog .m_c .el li{padding:0;border:none;}#dialog .flbc{float:right;margin:3px;font-size:16px;font-weight:bolder;position:relative;right:0px;display:inline-block;overflow:hidden;margin-right:10px;}#dialog .flbc:hover{color:#f00;}#dialog .bm_h{background:#fdfdfd;border-bottom:1px solid #ededed;}#dialog #dialog-con{padding:10px 20px;}#dialog .bm_h{border-bottom:1px solid #CDCDCD;}</style>";
			opts.btn||(btn='');
           $("body").append('<div id="dialog-wrap">'+style+'<div class="bg" style="background-image:none;"></div><table class="dialog" id="dialog" cellpadding="0" cellspacing="0"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l"></td><td class="m_c">		<div class="bm">				<div class="bm_h cl"><span><a href="javascript:;" class="flbc" id="dialog-close" title="关闭">X</a></span><h2 class="dialogh2">' + opts.title + '</h2></div><div class="bm_c" id="dialog-con"><div id="dialog-conn"></div></div>' +btn +'</div></td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table></div>');
		    
			if(opts.iframe!==null){
				$('#dialog-con').html('<iframe width="100%" frameborder="0"  src="'+opts.iframe+'"></iframe>');
				$('#dialog-con').css({padding:'0px'});	
			}else if(opts.url!==null){
				$('#dialog').hide();
				$.get(opts.url, function(data) {
					$('#dialog-conn').html(data);
					$('#dialog').show();
					setdialog();
				});	
			}else{
 			$('#dialog-conn').html(opts.content);			
				}
				opts.width&&opts.height&&($('#dialog-con').children().eq(0).css({width:opts.width,height:opts.height}));
				setdialog();
};

/**表单提交**/
var ajaxform=function(opts) {
		var defaults={
			_this:thisobj,
			_before_post:function(){},
			_after_post:function(){},
			success:function(){}
			};
		 opts=$.extend(defaults,opts)
      //  if (typeof arguments[2] != "undefined") reloadbool = arguments[2];
        //if (typeof arguments[1] != "undefined") msgtime = arguments[1];
        try {
            opts._before_post(opts);
			var thisobj=$(opts._this);
            var obj=null;
			var url='';
            formobj = thisobj.parents("form");
            formobj.submit(function(e) {
                return false;
            });
            url = formobj.attr("action");
            postdata = formobj.serialize();
            $.ajax({
                url:url,
                type:"POST",
                datatype:"JSON",
                data:postdata,
                success:function(da) {
					opts.success(da);
					opts._after_post(da);
                }
            });
        } catch (e) {
            alert(e.name + ": " + e.message);
        }
    };

/**写入cookies**/
var writeCookie=function (name, value, hours) {
    var expire = "";
    if (hours != null) {
        expire = new Date(new Date().getTime() + hours * 36e5);
        expire = "; expires=" + expire.toGMTString();
    }
    document.cookie = name + "=" + escape(value) + expire;
};

/**读取cookies**/
var readCookie=function(name) {
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
};

//把对外开放的方法设置到全局变量
window.ainiku=function(){return {
								msgDialog:msgDialog,
								ajaxform:ajaxform,
								writeCookie:writeCookie,
								readCookie:readCookie
								};};
//ainiku结束
})($,undefined);
