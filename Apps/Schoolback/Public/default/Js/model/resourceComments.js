document.write("<script language=javascript src='/Public/Js/date/WdatePicker.js'></script>");
var pid = [];
$(function() {
    $('.add').click(function() {
        add();
    })

    $('.return').click(function() {
        pid.shift();
        $('input[name=rco_pid]').val(pid[0]);
        searchClick();
    })
})

function child(id) {
    pid.unshift(id);
    $('input[name=rco_pid]').val(id);
    searchClick();
}

function add() {
    var id = $('input[name=rco_pid]').val();
    id = id ? id : 0;
    location.href =  CONTROLLER+"/add/rco_id/" + id;
}