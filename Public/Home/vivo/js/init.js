$(function(){
$('.nav li.nav-item').hover(function(){
	$(this).find('ul').show();
	},function(){
	$(this).find('ul').hide();	
		});
});