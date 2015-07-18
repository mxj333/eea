// 页面加载中
function Loading() {
    // 设置loading遮罩层的宽高
    $(".loading_cover").css({
        height: $(document).height(),
        width: $(document).width()
    });
    $(".loading_Win").show();
    $('.loading_cover').show();
}

function closeLoading() {
    $(".loading_Win").hide();
    $('.loading_cover').hide();
}

$(function() {

    // 新增
    $('.tools .add').click(function() {
        if (!$(this).attr('attr')) {
            add();
        }
    })

    // 删除
    $('.tools .del').click(function() {
        del();
    })

    // 禁用
    $('.tools .forbid').click(function() {
        forbid();
    })

    // 恢复
    $('.tools .resume').click(function() {
        resume();
    })

    // 发布
    $('.publish').click(function() {
        publish();
    })

    // 显示全称
    $(document).on('mouseover', '.showContent', function() {
        $(this).attr('title', $(this).text());
    })

    // 通过
    $('.tools .pass').click(function() {
        keyValue = getSelectCheckboxValues();

        if (!keyValue) {
            showMessage('请选择操作项！');
            return false;
        }

        location.href = CONTROLLER+'/pass/id/'+keyValue;
    })

    $('.list_header .return').click(function() {
        returnList($(this).attr('attr'));
    })

    // 编辑
    $('.tools .edit').click(function() {
        edit();
    });

    // 列表页面搜索
    $('.search .nowSearch').click(function(){
        $('form').submit();
    });

})

function getUrlRequest() {

    var res = new Object();
    var str = location.pathname.substr(1);
    var arr = str.split("/");
    for(var i = 1, j = Math.ceil(arr.length / 2); i < j; i ++) {
        res[arr[i * 2 - 1]] = arr[i * 2];
    }
    return res;
}

function returnList(p) {
    location.href = CONTROLLER+'/index/p/'+p;
}

function add() {
    location.href = CONTROLLER + '/add';
}

function shows(id) {
    action(id, '展示', 'shows');
}

function apply(id) {
    location.href = CONTROLLER + '/apply/id/'+id;
}

// 全选
function allSelect(name) {
    $("input[name=" + name + "]").each(function(){
        $(this).attr('checked', true);
    })
}

// 全不选
function allUnSelect(name) {
    $("input[name=" + name + "]").each(function(){
        $(this).attr('checked', false);
    })
}

// 反选
function InverSelect(name) {
    $("input[name=" + name + "]").each(function(){
        $(this).attr('checked', !Boolean($(this).attr('checked')));
    })
}

// 新增
function add() {
    location.href = CONTROLLER + '/add';
}

// 编辑
function action(id, title, action){
    var keyValue;
    if (id) {
        keyValue = id;
    } else {
        if (action == 'edit') {
            keyValue = getSelectCheckboxValue();
        } else {
            keyValue = getSelectCheckboxValues();
        }
    }
    if (!keyValue) {
        showMessage('请选择'+title+'项！');
        return false;
    }
    location.href = CONTROLLER + "/" + action + "/id/" + keyValue+'/p/'+$('.page a.current').html();
}

// 发布
function publish(id) {
    action(id, '发布', 'publish');
}

// 编辑
function edit(id){
    action(id, '编辑', 'edit');
}

// 删除
function del(id) {
    action(id, '删除', 'delete');
}

// 禁用
function forbid(id){
    action(id, '禁用', 'forbid');
}

// 启用
function resume(id){
    action(id, '恢复', 'resume');
}

// 排序
function sort() {

    var keyValue = getSelectCheckboxValues();
    location.href = CONTROLLER+"/sort/sortId/"+keyValue;
}

// 获取被选中的所有复选框的值
function getSelectCheckboxValues(){

    var str = '';
    $("input[name=key]:checked").each(function(){
        str += ',' + $(this).val();
    })
    return str.slice(1);
}

// 获取被选中的第一个复选框的值
function getSelectCheckboxValue(){

    return $("input[name=key]:checked").eq(0).val();
}

// 选中指定ID下的checkbox
function CheckAll(id) {
    var obj = $("#"+id+" :checkbox");

    var flag;
    obj.each(function(i){
        if (i == 0) {
            flag = Boolean($(this).is(':checked'));
        } else {
            $(this).prop("checked", flag);
        }
    })
}


//定义弹出窗口
function popWin() {
    var windowHeight;    //获取窗口的高度
    var windowWidth;     //获取窗口的宽度
    var popWidth;        //获取弹出窗口的宽度
    var popHeight;       //获取弹出窗口高度

    windowHeight = $(window).height();
    windowWidth = $(window).width();
    popHeight = $(".hide").height();
    popWidth = $(".hide").width();

    //计算弹出窗口的左上角Y的偏移量
    var popY = (windowHeight-popHeight)/2-50;
    var popX = (windowWidth-popWidth)/2-100;

    //设定窗口的位置
    $(".hide").css("top",popY).css("left",popX).slideToggle("slow");

}

//关闭窗口
function closeWindow() {
    $(".closeWin").click(function() {
     $(this).parent().parent().hide("slow");
    });
}

/*
 * setProvince
 * 初始化省市区
 * $param string id 省市区HTML id
 * $param string name 传值input name
 * $param string str 初始值
 *
 */
function setProvince(id, name, str, str2, str3) {
    if (id != '' && name != ''){
        if (str) {
            $("#" + id).ProvinceCity(str, str2, str3).children("select").css('color','#333333');
        } else {
            $("#" + id).ProvinceCity("", "", "").children("select").css('color','#333333');
        }
        $("#" + id + " select").change(function() {
            var region,add1,add2,add3,add='###';
            var add1 = $("#" + id + " select").eq(0).val();
            add2 = $("#" + id + " select").eq(1).val();
            add3 = $("#" + id + " select").eq(2).val();
            if (add1 == add2) {
                add2 = '';
            }
            if (add1 == add3) {
                add3 = '';
            }
            city = add1 + add + add2 + add + add3;
            $('input[name=' + name + '][type=hidden]').val(city);
        });
    }
}

// 提示信息
function showMessage(message, status) {
    if(!$('.showMessage_cover')[0]) {    // 消息窗口不存在
        $("body").append('<div class="showMessage_cover"></div><div class="messageWin"><p></p></div>');
        $(".messageWin p").html(message);

        var time;

        if (parseInt(status) == 1) {
            $(".messageWin p").addClass('exactMessage');
            $(".messageWin").fadeIn(400);
            $(".messageWin").fadeOut(5000);
            time = 1000;
        } else {
            $(".messageWin p").addClass('errorMessage');
            $(".messageWin").fadeIn(600);
            $(".messageWin").fadeOut(1000);
            time = 1000;
        }

        // 3秒后移除节点和遮罩层
        setTimeout(function(){
            $('.showMessage_cover').remove();
            $('.messageWin').remove();
        }, time);
    }

    // 设置遮罩层的宽高
    $(".showMessage_cover").css({
        height: function () {
            return document.clientHeight;
        },
        width: function () {
            return document.clientWidth;
        }
    });
}

// 字体字数设置中文1，英文数字0.5 向上去整。
/*
    $this:对象名称的内容
    num:取值方式 1，向上去整 2，向下去整 3，四舍五入
*/
function chineseFilter($this,num){
    var number,leng;
    !num ? num = 1 : num;
    var text = $this;
    var len = 0;
    for (var i = 0; i < text.length; i++) {
         var a = text.charAt(i);
         if (a.match(/[^\x00-\xff]/ig) != null)
        {
            len += 1;
        }
        else
        {
            len += 0.5;
        }
    }
    switch(num) {
        case 1:
          leng = Math.ceil(len); //向上去整
          break;
        case 2:
          leng = Math.floor(len); //向下去整
          break;
        case 3:
          leng = Math.round(len); //四舍五入
        break;
    }
    return leng;
}