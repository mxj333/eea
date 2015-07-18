$(function(){
    $(".file").change(function(){
        var strRegex = /(.xls|.xlsx)$/;
        var re=new RegExp(strRegex);
        $(".textBox").prop("value","");
        if (re.test($(this).val().toLowerCase())){
            $(".textBox").prop("value",$(this).val());
            $(".message").text("上传文件中...");
        }else{
            shake("notice");
        }
    });
    $(".btns a").click(function(){
        if ($(".textBox").val()) {
            $("form").submit();
        }
    });
});
//抖动效果
function shake(panel){
    var $panel = $("."+panel);
    box_left = ($(window).width() - $panel.width()) / 2.69;
    for(var i=1; 4>=i; i++){
        $panel.animate({left:box_left-(40-10*i)},50);
        $panel.animate({left:box_left+2*(40-10*i)},50);
    }
}