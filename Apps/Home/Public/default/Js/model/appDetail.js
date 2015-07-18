$(function(){
    //展开
    $(".flex > label").click(function(){
        if($(this).parent().prev().find(".hidden").hasClass("none")){
            $(this).parent().prev().find(".hidden").slideDown("fast").removeClass("none");
            $(this).parent().prev().find(".half").css({"width":"966px"}).removeClass("ellipsis");
            $(this).text("收起")&&$(this).next().addClass("up");
        }else{
            $(this).parent().prev().find(".hidden").slideUp("fast").addClass("none");
            $(this).parent().prev().find(".half").css({"width":"640px"}).addClass("ellipsis");
            $(this).text("展开")&&$(this).next().removeClass("up");
        }
    });
    //评价
    $(".rating_simple").webwidget_rating_simple({
        rating_star_length: "5",
        rating_initial_value: "4",
        rating_score: "ture",
        n:1,
        directory: MPUBLIC + "/Images/"
    });
});