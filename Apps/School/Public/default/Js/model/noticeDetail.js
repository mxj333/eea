$(function(){
    $(".d_search input[type='text']").focus(function(){
        if($(this).val() == "搜索资源..."){
            $(this).val("").css("color","#323232");
        }
    });
    $(".d_search input[type='text']").blur(function(){
        if($(this).val() == ""){
            $(this).val("搜索资源...").css("color","#ccc");
        }
    });
    // 搜索关键词
    $('.mySubmit').click(function(){
        var keywords = $(this).prev().val();
        if (keywords && keywords.trim() && keywords != undefined) {
            var url = $(this).parent().attr('action');
            $(this).parent().attr('action', url.replace('keywords', keywords.trim()));
            $('form.d_search').submit();
        }
    });
});
