/*
wicked navigation
*/
$(document).ready(function(){
	$('.wn_menu').click(function(){
		$('#wn_hamburger').toggleClass('open');
		$('.wn_expandedMenu').show().toggleClass('expanded');
	});
});