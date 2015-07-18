$(function() {
    var me_id = $('input[name=me_id]').val();
    if (me_id) {
        $('.tools').append('<a class="return" href="javascript:void(0)">返回</a>');
        $('.return').click(function(){
            location.href = "/Manage/Member/index/me_id/"+me_id;
        });
        $('.add').click(function(){
            location.href = CONTROLLER+"/add/me_id/"+me_id;
        });
        $('.edit').click(function(){
            edit();
        });
        $('.del').click(function(){
            keyValue = getSelectCheckboxValue();
            if (!keyValue) {
                showMessage('请选择'+title+'项！');
                return false;
            }
            location.href = CONTROLLER+"/delete/id/"+keyValue+"/me_id/"+me_id;
        });
    } else {
        $('.tools .add').remove();
    }
});

function edit(id) {
    var keyValue;
    var me_id = $('input[name=me_id]').val();

    if (id) {
        keyValue = id;
    } else {
        keyValue = getSelectCheckboxValue();
    }
    if (!keyValue) {
        showMessage('请选择'+title+'项！');
        return false;
    }
    location.href = CONTROLLER + "/edit/id/" + keyValue+'/p/'+$('.page a.current').html()+'/me_id/'+me_id;
}