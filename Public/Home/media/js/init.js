// JavaScript Document
$(function(){
	$('#mainnav li').hover(function(){
		$(this).find('.xiala').show();
		},function(){
		$(this).find('.xiala').hide();
		});
	});