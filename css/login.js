$(function(){
	var COOKIE_PRE='cmstop_';
	if ($.cookie(COOKIE_PRE+'auth')) {
		var username = $.cookie(COOKIE_PRE+'username');
		$('.sign-wrap').html("<i class=\"caret-up\"></i><div class=\"id\"><b>"+username+"</b> , 欢迎 !</div> <a href=\"/user/login.php?redurl="+encodeURIComponent(window.location.href)+"\" class=\"out\">退出</a>");
	} else {
		$('input[name=redurl]').val(window.location.href);
		$('.sign-wrap .fr').attr('href',"/user/register.php?r="+encodeURIComponent(window.location.href));
	}
	$(function() {
		  $(".ico-sign").click(function(){
			  $(".search-wrap").hide();
			  $(".sign-wrap").fadeToggle(300);
			  $('input[name=userName]').focus();
		  });
		  $(".ico-search").click(function(){
			  $(".sign-wrap").hide();
			  $(".search-wrap").fadeToggle(300);
			  $('input[name=q]').focus();
		  });
	});
	
	
	$('.btn').click(function(){
		$(this).closest("form").submit();
		return false;
	});
	
	$("#i-nav").click(function(){
		$(".top-header").toggleClass("nav-open");
		$(".i-search-wrap").hide();
		$(".i-nav-wrap").show();
	});

	$("#i-search").click(function(){
		$(".top-header").toggleClass("nav-open");
		$(".i-nav-wrap").hide();
		$(".i-search-wrap").show();
	});
});