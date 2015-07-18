var pid = [];
$(function() {
    $('.add').click(function() {
        add();
    })

    $('.return').click(function() {
        pid.shift();
        $('input[name=ca_pid]').val(pid[0]);
        searchClick();
    })
})

function child(id) {
    pid.unshift(id);
    $('input[name=ca_pid]').val(id);
    searchClick();
}

function add() {
    var id = $('input[name=ca_pid]').val();
    id = id ? id : 0;
    location.href =  CONTROLLER+"/add/ca_id/" + id;
}