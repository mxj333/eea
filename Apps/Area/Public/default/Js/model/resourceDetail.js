document.write("<script language=javascript src='/Public/Js/rating_simple.js'></script>");
$('document').ready(function(){
    $(".starEvaluat .star").each(function(){
        $(this).find(".rating_simple").webwidget_rating_simple({
            rating_star_length: "5",
            rating_initial_value: ""+ Math.ceil(Math.random()*5) +"",
            rating_score: "ture",
            rating_scale: 1,
            rating_star1: "/star2.png",
            rating_star2: "/star1.png",
            rating_cate: "完整性",
            directory: MPUBLIC + "/Images"
        });
    });
    calculationAverage($(".starEvaluat .webwidget_rating_score span"),$(".average"));
    $(".webwidget_rating_simple li").click(function(){
        calculationAverage($(".starEvaluat .webwidget_rating_score span"),$(".average"));
    });
});
function calculationAverage(objs,targetObj){
    var sum = 0;
    objs.each(function(){
        sum = sum + parseInt($(this).text());
    });
    var average = Math.round(sum/objs.size());
    targetObj.find("img:lt(" + average + ")").attr("src",MPUBLIC + "/Images/bigStar1.png");
    targetObj.find("span:last").text(average.toFixed(1) + "分");
}