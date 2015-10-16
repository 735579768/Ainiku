// JavaScript Document
//jquery拖动插件
(function($) {
    $.fn.kldrag = function(options) {
	var _t=this;
    var a = {
		xxx:0,//元素到左边的距离
		yyy:0,//元素到顶边的距离
        _x:0,//鼠标到元素左边距离
        _y:0,//鼠标到元素顶边距离
		_mouseDown:false
    };
        var defaults = {
            titleheight:50,
            space:false,
            spacestyle:""
        };
        var opts = $.extend(defaults, options);
		var setxy=function(e){
            a.xxx = _t.offset().left;
            a.yyy = _t.offset().top;
            a._x = e.pageX - a.xxx;
            a._y = e.pageY - a.yyy;			
			};
		var ismove=function(e){
				if (opts.titleheight === 0 || opts.titleheight >= e.pageY-_t.offset().top) {
					 _t.css("cursor", "move");
					return true;
					}else{
					_t.css("cursor", "auto");
					return false;	
						}
			};
        _t.bind("mousedown", function(e) {
			a._mouseDown=true;
			setxy(e);
			var scryyy=$(document).scrollTop();
            if (opts.titleheight === 0 || opts.titleheight >= a._y) {
                _t.css({
                    zIndex:999999999,
                    position:"fixed",
                    left:a.xxx + "px",
                    top:a.yyy-scryyy + "px",
                    margin:"0px"
                });
            }
        });
        _t.bind("mousemove", function(e) {
            if (ismove(e)) {
                if (a._mouseDown) {
					var scryyy=$(document).scrollTop();
                    xx = e.pageX - a._x;
                    yy = e.pageY - a._y-scryyy;
                    _t.css({
                        zIndex:999999,
                        position:"fixed",
                        left:xx + "px",
                        top:yy + "px"
                    });
                }
            }
        });
        _t.bind("mouseup", function() {
			a._mouseDown=false;
        });
    };
})(jQuery);

$(function() {

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
    $(".ajax-list-del").bind("click", function() {
       // if (!confirm("确定此操作吗?")) return false;
	   var _this=$(this);
		msgDialog({
			'btn':true,
			'content':'确定此操作吗?',
			'ok':function(){
					_this.addClass("disabled");
					url = _this.attr("href");
					$("body").append('<div id="klbg" class="bg">');
					$.ajax({
						type:"POST",
						url:url,
						success:function(da) {
							da.status=='1'&&_this.parent().parent().remove();
							ank.msg({
								content:da.info,
								info:da,
								success:function(da){
									if(da.info.url!='')window.location=da.info.url;
									}
								});
								_this.removeClass("disabled");
								$('#klbg').remove();	
						},
						dataType:"JSON"
					});
				},
			'cancel':function(){}
			});
        return false;        
        
    });
    //失去焦点提交事件
    $(".ajax-blur").bind("change", function() {
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

