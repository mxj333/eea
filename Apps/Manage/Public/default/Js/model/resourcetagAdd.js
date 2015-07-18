var pid = [];
$(function() {
    $('.return').click(function() {
        var p = $(this).attr('attr');
        var rta_pid = $('input[name=rta_pid]').val();
        location.href = CONTROLLER+'/index/' + 'rta_pid/' + rta_pid + '/p/'+p;
    })
})