$(function(){
    myOn($(".headerNav li"),"a");
    myOn($(".leftNav li"),"a");
    myOn($(".subNav li"),"a");
    
    $(".menuShow").each(function(){
        $(this).live("click",function(){
            if($(this).next().css("display") == "none"){
                $(this).next().css("display","block");
            }else{
                $(this).next().css("display","none");
            }
        });
    });
    $(".menuSelect").live("mouseleave",function(){
        $(".menuSelect").css("display","none");
    });
    $(".menuSelect span").each(function(){
        $(this).hover(
            function(){
                $(this).css({"background":"#e7f4ff","color":"#278cde"})},
            function(){
                $(this).css({"background":"","color":""})
        });
        $(this).live("click",function(){
            $(this).parents(".menuSelect").prev().find("span").text($(this).text());
        });
    });

    $(".flex").click(function(){
        if($(this).hasClass("on")){
            $(this).nextAll().slideUp("normal");
            $(".formSearch").slideUp("normal",function(){
                $(this).fadeIn("normal").addClass("on");
            });
            $(this).removeClass("on").text("展开");
        }else{
            $(this).nextAll().slideDown("normal");
            $(".formSearch").removeClass("on");
            $(this).addClass("on").text("收起");
        }
    });

    //上传
    $(".logo").live("change",function(){
        $(this).siblings(".message").text($(this).val());
    });
    
    //图片上传预览
    $(".logo").live("change",function(e){
        preImg("logo",".head img")
    });
});
//切换选中状态
function myOn(parents,targer,type){
    switch (type){
    case 0:
        parents.click(function(){
            parents.find(targer).each(function(){
                if($(this).hasClass("on")){
                    $(this).removeClass("on");
                }
            });
            $(this).find(targer).addClass("on");
        });
        break;
    case 1:
        parents.find(targer).click(function(){
            parents.find(targer).each(function(){
                if($(this).hasClass("on")){
                    $(this).removeClass("on");
                }
            });
            $(this).addClass("on");
        });
    }
}
//统计圆形进度条
function progress(targer){
    var i = 0;
    var max = parseInt(targer.children(".circle").find("span").text());
    var showbar = setInterval(function(){
        i++;
        if(i >= max){
            clearInterval(showbar);
        }
        var num = max * 3.6;
        if(num <= 180) {
            targer.find(".right").css("transform", "rotate(" + 3.6 * i + "deg)");
        }else{
            if(i <= 50){
                targer.find(".right").css("transform", "rotate("+ 3.6 * i + "deg)");
            }else{
                targer.find(".left").css("transform", "rotate(" + 3.6 * (i-50) + "deg)");
            }
        };
        targer.children(".circle").find("span").text(i);
    },30);
}
//将本地图片 显示到浏览器上 
function preImg(source, target){ 
    var url = getFileUrl(source); 
    $(target).prop("src",url); 
}
function getFileUrl(source){ 
    var url;
    if(navigator.userAgent.indexOf("MSIE")>=1){  //IE
        url = document.getElementsByClassName(source)[0].value;
    }else if(navigator.userAgent.indexOf("Firefox")>0){  //Firefox
        url = window.URL.createObjectURL(document.getElementsByClassName(source)[0].files.item(0)); 
    }else if(navigator.userAgent.indexOf("Chrome")>0) {  //Chrome
        url = window.URL.createObjectURL(document.getElementsByClassName(source)[0].files.item(0)); 
    }
    return url; 
}
//学制与年级的对应显示
function cascadeSelected_op(target,num,startIndex){
    target.each(function(){
        if(!$(this).parent().is("span")){
            $(this).wrap("<span style='display:none;'></span>");
        }
    });
    for(var i = startIndex;i < parseInt(startIndex) + parseInt(num);i++){
        if(target.eq(i).parent().is("span")){
            target.eq(i).unwrap();
        }
    }
}