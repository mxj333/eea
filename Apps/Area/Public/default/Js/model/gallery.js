(function($){
$.fn.DB_gallery=function(options){
    var opt={
        thumWidth:110,              //芥匙老啊肺
        thumGap:8,                  //芥匙老埃拜
        thumMoveStep:5,             //芥匙老捞悼肮荐
        moveSpeed:300,              //捞悼加档
        fadeSpeed:300,              //拳搁傈券加档
		nMaxWidth:500,
		nMaxHeight:200,
        end:''
    }
    $.extend(opt,options);
    return this.each(function(){
        var $this=$(this);
        var $imgSet=$this.find('.DB_imgSet');
        var $imgWin=$imgSet.find('.DB_imgWin');
        var $page=$this.find('.DB_page');
        var $pageCurrent=$page.find('.DB_current');
        var $pageTotal=$page.find('.DB_total');
        var $thumSet=$this.find('.DB_thumSet');
        var $thumMove=$thumSet.find('.DB_thumMove');
        var $thumList=$thumMove.find('li');
        var $thumLine=$this.find('.DB_thumLine');
        var $thumCover=$this.find('.thumCover');
        var $nextBtn=$this.find('.DB_nextBtn');
        var $prevBtn=$this.find('.DB_prevBtn');
        var $nextPageBtn=$this.find('.DB_nextPageBtn');
        var $prevPageBtn=$this.find('.DB_prevPageBtn');
        var objNum=$thumList.length;
        var currentObj=0;
        var fixObj=0;
        var currentPage=0;
        var totalPage=Math.floor(objNum/opt.thumMoveStep);
        var oldImg;

        init();

        function init(){
            setInit();
            setMouseEvent();
            changeImg();
        }

        function setInit(){
            //芥匙老 扼牢 困摹函版
            $thumMove.append($thumLine.get());
			
        }

        //官牢爹
        function setMouseEvent(){
            $thumList.bind('click',function(e){
                e.preventDefault();
                currentObj=$(this).index();
                changeImg();
            });
            $nextBtn.bind('click',function(){
                currentObj++;
                changeImg();
                currentPage=Math.floor(currentObj/opt.thumMoveStep);
                moveThum();

            });
            $nextBtn.hover(
                function(){
                    $(this).addClass('DB_hover');
                },
                function(){
                    $(this).removeClass('DB_hover');
            });
            $prevBtn.bind('click',function(){
                currentObj--;
                changeImg();
                currentPage=Math.floor(currentObj/opt.thumMoveStep);
                moveThum();
            });
            $prevBtn.hover(
                function(){
                    $(this).addClass('DB_hover');
                },
                function(){
                    $(this).removeClass('DB_hover');
            });
            $nextPageBtn.bind('click',function(){
                currentPage++;
                moveThum();
            });
            $prevPageBtn.bind('click',function(){
                currentPage--;
                moveThum();
            });
        
        }
        
        //芥匙老 捞悼
        function moveThum(){
            var pos=((opt.thumWidth+opt.thumGap)*opt.thumMoveStep)*currentPage
            $thumMove.animate({'left':-pos},opt.moveSpeed);
            //
            setVisibleBtn();
        }

        //捞固瘤函版俊 蝶弗 滚瓢贸府
        function setVisibleBtn(){
            $prevPageBtn.show();
            $nextPageBtn.show();
            $prevBtn.show();
            $nextBtn.show();
            if(currentPage==0)$prevPageBtn.hide();
            if(currentPage==totalPage-1)$nextPageBtn.hide();
            if(currentObj==0)$prevBtn.hide();
            if(currentObj==objNum-1)$nextBtn.hide();
			if($thumList.size() <=7)$nextPageBtn.hide();
        }

        //捞固瘤函版
        function changeImg(){
            if(oldImg!=null){
                //何靛矾款 傈券阑 困秦 硅版俊 扁粮捞固瘤甫 硅摹
                //$imgWin.css('background','url('+oldImg+') no-repeat');
            }
            //努腐捞固瘤
            var $thum=$thumList.eq(currentObj)
            var _src=oldImg=$thum.find('a').attr('href');
            $imgWin.find('img').hide().attr('src',_src).fadeIn(opt.fadeSpeed);
            oldImg=_src

            //芥匙老扼牢 困摹函版
            $thumLine.css({'left':$thum.position().left});
            //其捞瘤函版
            $pageCurrent.text(currentObj+1);
            $pageTotal.text(objNum);
            toggleCover();
            setVisibleBtn();
			fImageAuto($(".DB_imgWin"),opt.nMaxWidth,opt.nMaxHeight);
        }
        function toggleCover(){
            var $thum=$thumList.eq(currentObj)
            $thumCover.each(function(){
                if($(this).attr('visibility','hidden')){
                    $(this).css('visibility','visible');
                }
            });
            $thum.find($thumCover).css('visibility','hidden');
        }
		//div下图片自适应大小并居中
		function fImageAuto(nId,nMaxWidth,nMaxHeight){
             var objParentId = nId;
             var objImg = objParentId.find("img");
			 var oldImg = new Image();
		     oldImg.src = objImg.attr("src");
             var nImgNewRate =0;
             var nImgOldRate = nMaxWidth/nMaxHeight;
			 nImgNewRate = oldImg.width/oldImg.height;
			 if (nImgNewRate >= nImgOldRate && oldImg.width > nMaxWidth) {
			   objImg.css({"height":nMaxWidth/nImgNewRate,"width":nMaxWidth,"margin-top":Math.round((nMaxHeight-nMaxWidth/nImgNewRate)/2),"margin-left":"0px"});
				console.log(1);
			 }else if(nImgNewRate < nImgOldRate && oldImg.height > nMaxHeight) {
			   objImg.css({"width":nMaxHeight*nImgNewRate,"height":nMaxHeight,"margin-left":(nMaxWidth-nMaxHeight*nImgNewRate)/2,"margin-top":"0px"});
				console.log(2);
			 }else if(oldImg.width <= nMaxWidth && oldImg.height<= nMaxHeight){
				objImg.css({"height":oldImg.height,"width":oldImg.width,"margin-left":Math.round((nMaxWidth-oldImg.width)/2),"margin-top":Math.round((nMaxHeight-oldImg.height)/2)});
				console.log(3);
			 }
         }
    });
    
}
})(jQuery)