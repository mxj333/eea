document.write("<script language=javascript src='/Public/Js/gallery.js'></script>");
$(function() {
    $(".picList").DB_gallery({
        thumWidth:80,
        thumGap:8,
        thumMoveStep:4,
        moveSpeed:300,
        fadeSpeed:500,
        nMaxWidth:658,
        nMaxHeight:448,
    });
    $(".DB_thumMove li > a").click(function(){
        $(".pic_abstract").text($(this).attr("alt"));
    });
    $(".DB_imgSet .DB_prevBtn,.DB_imgSet .DB_nextBtn").click(function(){
        $(".DB_thumMove li > a .thumCover").each(function(){
            if($(this).css("visibility") == "hidden"){
                $(".pic_abstract").text($(this).parent().attr("alt"));
            }
            
        });
    });
});