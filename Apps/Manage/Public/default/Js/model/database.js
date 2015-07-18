$(function() {
    $('.add').click(function() {
        add($(this).attr('attr'));
    })

    $('.shows').click(function() {
        shows($(this).attr('attr'));
    })

    getTables($('.databases').val());
    $('.databases').change(function() {
        getTables($(this).val());
    })

    $('.tableAction span').click(function() {
        $(this).addClass('on').siblings().removeClass('on');
    })

    $('.dels').click(function() {
        dels($(this).attr('attr'));
    })

    $('.tables').dblclick(function() {
        var sql = 'SELECT * FROM ' + $(this).val() + ' LIMIT 30;';
        $('.sql').html(sql);
    })
    $('.lists').click(function() {
        lists($('.sql').val());
    })
})

function dels(attr) {
    if (confirm('确定删除选中的库吗？')) {
        $.post(CONTROLLER + '/delete', 'attr=' + attr, function(json) {
            location.href = json.url;
        }, 'json');
    }
}

function lists(sql) {
    $.post(CONTROLLER + '/lists', 'attr=sql&sql=' + sql, function(json) {
        var htm = '<table style="width:100%;" border="1">';
        if (json) {
            var iLength = json.length;
            var jLength = json[0].length;
            for (i = 0; i < iLength; i++) { 
                var sign = i == 0 ? 'th' : 'td';
                htm += '<tr>';
                for (var j = 0; j < jLength; j ++) {
                    htm += '<' + sign + '>' + json[i][j] + '</' + sign + '>';
                }
                htm += '</tr>';
            }
        }
        htm += '</table>';
        $('.dataBox').html(htm);
    }, 'json');
}

function getTables(useDb) {
    $.post(CONTROLLER + '/lists', 'attr=table&useDb=' + useDb, function(json) {
        var str = '';
        for (var i = 0; i < json.length; i ++) {
            str += '<option value="' + json[i] + '">' + json[i] + '</option>';
        }
        $('.tables').html(str);
    }, 'json');
}

function add(attr) {
    location.href = CONTROLLER + '/add/attr/' + attr;
}

function shows(attr) {
    location.href = CONTROLLER + '/shows/attr/' + attr;
}