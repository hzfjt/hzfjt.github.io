(function ($) {
    $.fn.pic_scroll = function(options){
        var defaults = {
        		scrollBox:'', //滚动区域
        		scrollElementAll:'', // 滚动的元素
        		changeNumClass:'', // 切换标签父元素 class名称
        		nextBtClass:'next', // 下翻按钮class名称
        		prevBtClass:'prev',
            setId:null,
            delay:3000, //定时间隔时间
            duration:'normal',//动画运行时间 normal,fast,slow 或num
            addClass:"here",  //changeNum 下选中的当前图标
            autoPlay:true  //是否自动播放
        }
        var options = $.extend(defaults, options),autoL,autoR,thisAdd=0,len=options.scrollElementAll.length,changeNum,allNum;
        		if(options.changeNumClass){
        			changeNum=options.scrollBox.find('.'+options.changeNumClass)
        			options.scrollElementAll.each(function(){
        					$('<span></span>').appendTo(changeNum);
        				});
        			
        			allNum=changeNum.children();
        			allNum.eq(0).addClass(options.addClass);
        			allNum.click(function(){
        					var ii=$(this).index();
        					if(ii!=thisAdd){
        							$(this).addClass(options.addClass).siblings().removeClass(options.addClass);
        							options.scrollElementAll.eq(thisAdd).animate({left:'-100%'},options.duration,function(){$(this).css({left:'100%'});});        							
        							options.scrollElementAll.eq(ii).animate({left:'0'},options.duration,function(){});
        							thisAdd=ii;
        						}
        					return false;
        				}).mouseenter(function(){if(options.setId){clearInterval(options.setId);options.setId=null;}}).mouseleave(function(){if(!options.setId){options.setId=setInterval(autoL,options.delay);}});
        		}
        		autoL=function(){
        				if(!options.scrollElementAll.eq(thisAdd).is(':animated')){
        						options.scrollElementAll.eq(thisAdd).animate({left:'-100%'},options.duration,function(){$(this).css({left:'100%'});});
        						thisAdd++;
        						if(thisAdd > len-1){thisAdd=0;}
         						options.scrollElementAll.eq(thisAdd).animate({left:'0'},options.duration,function(){if(options.changeNumClass){allNum.removeClass(options.addClass).eq(thisAdd).addClass(options.addClass);}});
       					}
        			};
        		autoR=function(){
        				if(!options.scrollElementAll.eq(thisAdd).is(':animated')){
        						options.scrollElementAll.eq(thisAdd).animate({left:'100%'},options.duration,function(){});
        						thisAdd--;
        						if(thisAdd < 0){thisAdd=len-1;}
         						options.scrollElementAll.eq(thisAdd).css({left:'-100%'}).animate({left:'0'},options.duration,function(){if(options.changeNumClass){allNum.removeClass(options.addClass).eq(thisAdd).addClass(options.addClass);}});
       					}
        			};
        			options.scrollBox.on('mouseenter','.'+options.nextBtClass,function(){
        					if(options.setId){clearInterval(options.setId);options.setId=null;}
        				})
        			options.scrollBox.on('mouseleave','.'+options.nextBtClass,function(){
        					if(!options.setId){options.setId=setInterval(autoL,options.delay);}
        				})
        			options.scrollBox.on('click','.'+options.nextBtClass,function(){
        					autoL();return false;
        				})
        			options.scrollBox.on('mouseenter','.'+options.prevBtClass,function(){
        					if(options.setId){clearInterval(options.setId);options.setId=null;}
        				})
        			options.scrollBox.on('mouseleave','.'+options.prevBtClass,function(){
        					if(!options.setId){options.setId=setInterval(autoL,options.delay);}
        				})
        			options.scrollBox.on('click','.'+options.prevBtClass,function(){
        					autoR();return false;
        				})
        		
        		if(options.autoPlay){options.setId=setInterval(autoL,options.delay);}
						
    };
})(jQuery);
