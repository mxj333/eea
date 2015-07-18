var pid = [];
$(function() {
    $('.add').click(function() {
        add();
    })

    $('.return').click(function() {
        pid.shift();
        $('input[name=pe_pid]').val(pid[0]);
        searchClick();
    })
})

function child(id) {
    pid.unshift(id);
    $('input[name=pe_pid]').val(id);
    searchClick();
}

function add() {
    var id = $('input[name=pe_pid]').val();
    id = id ? id : 0;
    location.href =  CONTROLLER+"/add/pe_id/" + id;
}