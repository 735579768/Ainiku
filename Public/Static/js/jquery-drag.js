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