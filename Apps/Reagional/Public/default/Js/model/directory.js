var pid = [];
$(function() {
    $('.add').click(function() {
        add();
    })
    
    $('.return').click(function() {
        pid.shift();
        $('input[name=d_pid]').val(pid[0]);
        searchClick();
    })
    
    $('.tools').append('<a class="createExcel">生成excel</a>');
    $('.tools').append('<a class="download" attr="0">导出excel</a>');
    $('.tools').append('<a class="createWord">生成word</a>');
    $('.tools').append('<a class="download" attr="1">导出word</a>');

    $('.tools .createExcel').click(function(){
        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/makeTime',
            data : {type:2},
            dataType : 'json',
            success : function(data) {
                if (data) {
                    var tt=new Date(parseInt(data) * 1000).toLocaleString() 
                    if (confirm('您已于'+tt+'生成过，确定要重新生成?')) {
                        location.href =  CONTROLLER+"/createExcel/";
                    }
                } else {
                    location.href =  CONTROLLER+"/createExcel/";
                }
            }
        });
    })
    $('.tools .createWord').click(function(){
        $.ajax({
            type : 'POST',
            url : CONTROLLER + '/makeTime',
            data : {type:1},
            dataType : 'json',
            success : function(data) {
                if (data) {
                    var tt=new Date(parseInt(data) * 1000).toLocaleString() 
                    if (confirm('您已于'+tt+'生成过，确定要重新生成?')) {
                        location.href =  CONTROLLER+"/createWord/";
                    }
                } else {
                    location.href =  CONTROLLER+"/createWord/";
                }
            }
        });
    })
    $('.tools .download').click(function(){
        var type = $(this).attr('attr');
        location.href =  CONTROLLER+"/download/type/" + type;
    })
})

function child(id) {
    pid.unshift(id);
    $('input[name=d_pid]').val(id);
    searchClick();
}

function add() {
    var id = $('input[name=d_pid]').val();
    id = id ? id : 0;
    location.href =  CONTROLLER+"/add/d_id/" + id;
}

function user(id){
    location.href = CONTROLLER+"/user/id/"+id;
}