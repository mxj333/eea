$(function() {
    //生成缓存表
    $('.cache').click(function() {
        cache();
    })

    // 同步数据
    $('.sync').click(function() {
        sync();
    })
})

function cache(id) {
    var keyValue = id ? id : getSelectCheckboxValues();

    if (!keyValue) {
        showMessage('请选择生成项');
        return false;
    }
    location.href =  CONTROLLER+"/cache/id/"+keyValue;
}

function sync(id) {
    var keyValue = id ? id : getSelectCheckboxValues();

    if (!keyValue) {
        showMessage('请选择要同步数据的类型');
        return false;
    }
    location.href =  CONTROLLER+"/sync/id/"+keyValue;
}