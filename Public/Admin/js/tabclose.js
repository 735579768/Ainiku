window.addtabclose = function(title, url) {
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
	$('#nav-block').append(' <li class="chrome-tab kl-tab-nav">' + title + '<span class="close">X</span><em></em></li>');
	$('#div-block').append(' <div class="kl-tab-div"><span class="iframeloading"></span><iframe class="con-iframe" marginWidth=0 frameSpacing=0 marginHeight=0  onload="setIframeHeight(this);" frameborder="0" border="0" src="' + url + '"  noResize width="100%" scrolling=auto  vspale="0"></iframe></div>');
	$('.kl-tab').mytab();
	$('.kl-tab .kl-tab-nav:last').click();
	rightMenu && rightMenu.init();
};