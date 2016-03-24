(function() {
	window.onload = function() {
		var ifram = document.createElement('iframe');
		var body = document.getElementsByTagName('body')[0];
		ifram.src = '/Addons/plugin/pn/Fangke/pm/record.html';
		ifram.style = 'display:none;';
		body.appendChild(ifram);
	}
})();