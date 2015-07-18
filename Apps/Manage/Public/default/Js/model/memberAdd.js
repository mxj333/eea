document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
document.write("<script language=javascript src='/Public/Js/cascade/cascade.js'></script>");
$(document).ready(function(){
    //编辑动态分组
    $("input[name=me_account]").parents("li").css("border-top","1px solid #d5d5d5").before("<li class='cate'>基础资料</li>");
    $("input[name=me_mobile]").parents("li").css("border-top","1px solid #d5d5d5").before("<li class='cate'>联系方式</li>");
    $("input[name=md_chinese_name]").parents("li").css("border-top","1px solid #d5d5d5").before("<li class='cate'>个人信息</li>");
    if($("textarea[name=md_description]").parents("li").nextAll().size() > 1){
        $("textarea[name=md_description]").parents("li").after("<li class='cate'>其他信息</li>").next().next().css("border-top","1px solid #d5d5d5");
    }

    // 编辑时 账号不允许修改
    var me_id = $('input[name=me_id]').val();
    if (me_id) {
        $('input[name=me_account]').attr('readonly', true).css('border', 'none');
        //编辑时密码要显示....不能为空
        $("input[name=me_password]").after("<div class='defalut_dot'><span>●</span><span>●</span><span>●</span><span>●</span><span>●</span><span>●</span></div>").parent().css({"position":"relative","overflow":"visible"});
        $(".defalut_dot").live("click",function(){
            $(this).prev().focus();
            $(this).remove();
        });
    }
    $('input[name=region]').parent().addClass('region_cascade');
    $('input[name=region]').remove();
    var defaultValue = [];
    if (region_title) {
        defaultValue = region_title.split('-');
    }
    $('.region_cascade').cascade(
        {url: CONTROLLER+"/getRegion/", test: 0, valueBox: 're_id', valueText: 're_title', defaultValue: defaultValue}
    );
    
    
});