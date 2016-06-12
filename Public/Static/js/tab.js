/**
 * 使用方法
 * 		$('#mytab').mytab({
			ev: "mouseover", //事件
			navcls: ".kl-tab-nav", //导航
			divcls: ".kl-tab-div", //显示块
			navhovercls: "hover", //选中后的类
			divshowcls: "hover", //显示后的类
			showdiv: 1 //显示第一个div块
		});
 */

;
(function($) {
	$.fn.mytab = function(options) {
		var thissel = $(this).selector;
		var defaults = {
			ev: "mouseover", //事件
			navcls: ".kl-tab-nav", //导航
			divcls: ".kl-tab-div", //显示块
			navhovercls: "hover", //选中后的类
			divshowcls: "hover", //显示后的类
			showdiv: 1 //显示第一个div块
		};
		var opts = $.extend(defaults, options);
		var navlist = $(thissel + " " + opts.navcls);
		var divlist = $(thissel + " " + opts.divcls);
		divlist.each(function(index, element) {
			$(this).data('tab-index', index);
		});
		navlist.each(function(index, element) {
			$(this).data('tab-index', index);
		});
		navlist.bind(opts.ev, function() {
			var obj = $(this);
			var tabindex = obj.data('tab-index');
			navlist.not(opts.navhovercls).removeClass(opts.navhovercls);
			obj.addClass(opts.navhovercls);
			divlist.not(opts.divshowcls).hide();
			divlist.each(function(i) {
				var temobj = divlist.eq(i);
				if ($(this).data('tab-index') == tabindex) {
					temobj.addClass(opts.divshowcls);
					temobj.show();
				} else {
					temobj.removeClass(opts.divshowcls);
				}
			});
		});
		navlist.eq(opts.showdiv - 1).trigger(opts.ev);
	}
})(jQuery);