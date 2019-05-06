		(function($){
				var bannerArea=$('.bannerArea'),num=bannerArea.find('.bannerNum'),focusNum=num.find('.focus'), ul=bannerArea.find('ul.list'),allLi=ul.children(),thisAdd=0,bannerSetId=null,bannerSetT=5000,autoL,autoR,prev=bannerArea.find('.prev'),next=bannerArea.find('.next');
				var	liW=allLi.width(),len=allLi.length,ulW=liW*len;ul.width(ulW);allLi.last().prependTo(ul);
						num.find('.total').text(len);
						
						autoL=function(){
								if(!ul.is(':animated')){
									thisAdd++;
									if(thisAdd >= len){thisAdd=0;}
										ul.animate({left:-2*liW+'px'},600,function(){focusNum.html(thisAdd+1);$(this).css({left:-1*liW+'px'}).children(':first').appendTo($(this));});
										ul.children().eq(1).find('.desc').fadeOut(400).end().end().eq(2).find('.desc').fadeIn(600);
									}
							
							}
						autoR=function(){
								if(!ul.is(':animated')){
									thisAdd--;
									if(thisAdd < 0){thisAdd=len-1;}
									ul.css({left:-2*liW+'px'}).children(':last').prependTo(ul);
										ul.animate({left:-1*liW+'px'},600,function(){focusNum.html(thisAdd+1);});
										ul.children().eq(2).find('.desc').fadeOut(400).end().end().eq(1).find('.desc').fadeIn(600);
									}
							
							}
						bannerSetId=setInterval(autoL,bannerSetT);
						prev.add(next).mouseenter(function(){if(bannerSetId){clearInterval(bannerSetId);bannerSetId=null;}}).mouseleave(function(){if(!bannerSetId){bannerSetId=setInterval(autoL,bannerSetT);}});
						prev.click(function(){autoR();return false;});
						next.click(function(){autoL();return false;});						
			})(jQuery);