$(function(){
    
});
function getList(page) {
    if (!page || page == undefined) {
        page = 1;
    }
    // 查询条件
    var ac_id = $('.app_title span').attr('attr');
    var keywords = '';

    $.ajax({
        type : 'POST',
        url : AREA_MODULE.toLowerCase() + '/app/lists',
        data : {ac_id:ac_id,keywords:keywords,page:page},
        dataType : 'json',
        success : function(data) {
            $('.app_item').remove();
            $('.app_title').after(data['list']);
            $('.pageBtn a').remove();
            $('.pageBtn').append(data['page']);
        }
    });
}