$(function(){
    $(".c_space").css({"height":$(window).height()});
    var scrollFunc = function (e) {
        var direct = 0;
        e = e || window.event;
        if (e.wheelDelta) {  //判断浏览器IE，谷歌滑轮事件             
            if (e.wheelDelta > 0) { //当滑轮向上滚动时
                alert("滑轮向上滚动");
            }
            if (e.wheelDelta < 0) { //当滑轮向下滚动时
                alert("滑轮向下滚动");
            }
        } else if (e.detail) {  //Firefox滑轮事件
            if($(window).scrollTop() < 1648 && e.detail > 0){
                var pf = $(".front").position();
                var pm = $(".middle").position();
                var pb = $(".behind").position();
                $(".front").css({"top":pf.top + 5});
                $(".middle").css({"top":pf.top + 3});
                $(".behind").css({"top":pf.top + 2});
            }
            if($(window).scrollTop() > 0 && e.detail < 0){
                var pf = $(".front").position();
                var pm = $(".middle").position();
                var pb = $(".behind").position();
                $(".front").css({"top":pf.top - 5});
                $(".middle").css({"top":pf.top - 3});
                $(".behind").css({"top":pf.top - 2});
            }
        }
    }
    //给页面绑定滑轮滚动事件
    if (document.addEventListener) {
        document.addEventListener('DOMMouseScroll', scrollFunc, false);
    }
    //滚动滑轮触发scrollFunc方法
    window.onmousewheel = document.onmousewheel = scrollFunc;  
});