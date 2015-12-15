;
(function($) {
	$.fn.mytab = function(options) {
		var thissel = $(this).selector; //取传进来的选择器
		var defaults = {
			'ev': 'click',
			'navcls': '.kl-tab-nav', //默认导航类
			'divcls': '.kl-tab-div', //默认内容块
			'navhovercls': 'hover', //nav显示时候拥有的类
			'divshowcls': 'd1', //div显示的时候拥有的类
			'showdiv': 1,
			'lnav': '.lnav',
			'rnav': '.rnav'
		};
		var opts = $.extend(defaults, options);
		$(thissel + ' ' + opts.divcls).each(function(index, element) {
			$(this).attr('markid', index);
		});
		$(thissel + ' ' + opts.navcls).each(function(index, element) {
			$(this).attr('markid', index);
		});
		//设置左右切换导航
		$(thissel + ' ' + opts.lnav).click(function(e) {

		});
		$(thissel + ' ' + opts.divcls).hide();
		$(thissel + ' ' + opts.navcls).bind(opts.ev, function() {
			var obj = $(this);
			//设置hover类
			obj.parents(thissel).find(opts.navcls).removeClass(opts.navhovercls);
			obj.addClass(opts.navhovercls);
			//隐藏掉所有的tabdiv
			obj.parents(thissel).find(opts.divcls).hide();
			obj.parents(thissel).find(opts.divcls).removeClass(opts.divshowcls);
			//查找要显示的div
			obj.parents(thissel).find(opts.navcls).each(function(i) {
				if ($(this).attr('markid') == obj.attr('markid')) {
					obj.parents(thissel).find(opts.divcls).eq(i).addClass(opts.divshowcls);
					var temobj = obj.parents(thissel).find(opts.divcls).eq(i);
					if (opts.effect == 'fade') {
						temobj.css({
							'opacity': '0',
							'display': 'block'
						});
						temobj.animate({
							opacity: 1
						}, 200);
					} else {
						temobj.show();
					}

				}
			});
		});
		$(thissel + ' ' + opts.navcls + ' .close').bind('click', function() {
			var obj = $(this);
			obj.parents(thissel).find(opts.divcls).each(function(i) {
				if ($(this).attr('markid') == obj.parent().attr('markid')) {
					//  obj.parents(thissel).find(opts.divcls).eq(i).addClass(opts.divshowcls);
					var temobj = obj.parents(thissel).find(opts.divcls).eq(i);


					//obj.parents(opts.navcls).animate({width:0},100,function(){
					obj.parents(opts.navcls).remove();
					temobj.remove();
					//查找有没有默认显示的
					if ($(thissel + ' ' + opts.navcls + '.' + opts.navhovercls).length <= 0) {
						$(thissel + ' ' + opts.navcls + ':last').click();
					}
					//	});

					return 0;
				}
			});

		});
		//默认显示的块
		$(thissel + ' ' + opts.navcls + ':first-child').trigger(opts.ev);

	};
})(jQuery);
$(function() {
	$('.kl-tab').mytab();
	window.addtabclose = function(title, url) {
		$('#loadingimg').show();
		var isyou = false;
		$('.kl-tab iframe').each(function(index, element) {
			var srcurl = $(this).attr('src');
			var par = $(this).parent();
			if (url == srcurl) {
				$(this).remove();
				par.append('<span class="iframeloading">正在加载...</span><iframe class="con-iframe" marginWidth=0 frameSpacing=0 marginHeight=0  onload="setIframeHeight(this);" frameborder="0" border="0" src="' + url + '"  noResize width="100%" scrolling=auto  vspale="0"></iframe>');
				//$(this).attr('src',url);		
				$('.kl-tab .kl-tab-nav').eq(index).click();
				isyou = true;
				return 0;
			}
		});
		if (isyou) return true;
		$('#nav-block').append(' <li class="kl-tab-nav">' + title + '<span class="close">X</span></li>');
		$('#div-block').append(' <div class="kl-tab-div"><span class="iframeloading">正在加载...</span><iframe class="con-iframe" marginWidth=0 frameSpacing=0 marginHeight=0  onload="setIframeHeight(this);" frameborder="0" border="0" src="' + url + '"  noResize width="100%" scrolling=auto  vspale="0"></iframe></div>');
		$('.kl-tab').mytab();
		$('.kl-tab .kl-tab-nav:last').click();
	};
});