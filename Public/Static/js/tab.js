;
(function($) {
	$.fn.mytab = function(options) {
		var thissel = $(this).selector;
		var defaults = {
			ev: "mouseover",
			navcls: ".kl-tab-nav",
			divcls: ".kl-tab-div",
			navhovercls: "n1",
			divshowcls: "d1",
			showdiv: 1,
			lnav: ".lnav",
			rnav: ".rnav",
			effect: "fade"
		};
		var opts = $.extend(defaults, options);
		$(thissel + " " + opts.divcls).each(function(index, element) {
			$(this).attr("markid", index)
		});
		$(thissel + " " + opts.navcls).each(function(index, element) {
			$(this).attr("markid", index)
		});
		$(thissel + " " + opts.lnav).click(function(e) {});
		$(thissel + " " + opts.divcls).hide();
		$(thissel + " " + opts.navcls).bind(opts.ev, function() {
			var obj = $(this);
			obj.parents(thissel).find(opts.navcls).removeClass(opts.navhovercls);
			obj.addClass(opts.navhovercls);
			obj.parents(thissel).find(opts.divcls).hide();
			obj.parents(thissel).find(opts.divcls).removeClass(opts.divshowcls);
			obj.parents(thissel).find(opts.navcls).each(function(i) {
				if ($(this).attr("markid") == obj.attr("markid")) {
					obj.parents(thissel).find(opts.divcls).eq(i).addClass(opts.divshowcls);
					var temobj = obj.parents(thissel).find(opts.divcls).eq(i);
					if (opts.effect == "fade") {
						temobj.css({
							opacity: "0",
							display: "block"
						});
						temobj.animate({
							opacity: 1
						}, 200)
					} else {
						temobj.show()
					}
				}
			})
		});
		$(thissel + " " + opts.navcls + ":first-child").trigger(opts.ev)
	}
})(jQuery);