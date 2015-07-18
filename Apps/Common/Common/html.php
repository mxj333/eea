<?php

function generationTpl($tplList = array(), $action = array(), $search = array(), $tools = array(), $preList = array(), $checkbox = 1) {

    $indexTpl = '';

    if ($search) {

        $indexTpl .= '<div class="search">';

        foreach ($search as $sKey => $sValue) {
            if (!$sValue['inline']) {
                $indexTpl .= '<div class="padding_t20">';
            }

            $display = $sValue['display'] ? 'style="display:' . $sValue['display'] . '"' : '';
            $indexTpl .= '<span class="' . $sValue['name'] . '" ' . $display . '><a>' . $sValue['title'] . '：</a>';
            switch (strtolower($sValue['label'])) {
                case 'select':
                    $indexTpl .= '<select name="' . $sValue['name'] . '" class="' . $sValue['class'];
                    if ($sValue['event'] && $sValue['eventValue']) {
                        $indexTpl .= $sValue['event'] . '="' . $sValue['eventValue'] . '"';
                    }
                    $indexTpl .= '">
                    <option value="0" attr="0">请选择</option>';

                    foreach ($sValue['data'] as $svKey => $svValue) {
                        $indexTpl .= '<option value="' . $svKey . '" ';

                        if ($svKey == $_REQUEST[$sValue['name']]) {
                            $indexTpl .= ' selected ';
                        }
                        $indexTpl .= '>' . $svValue . '</option>';
                    }

                    $indexTpl .= '</select>';
                    break;
                case 'choose' :
                    $indexTpl .= '<span class="choose show_all_content">选择</span>
                        <div class="' . $sValue['class'] . '">
                            <ul></ul>
                            <input type="hidden" value="" name="' . $sValue['name'] . '" />
                        </div>
                    ';
                    break;
                default :
                    $indexTpl .= '<input type="text" name="' . $sValue['name'] . '" value="' . $_REQUEST[$sValue['name']] . '" ';
                    if ($sValue['event'] && $sValue['eventValue']) {
                        $indexTpl .= $sValue['event'] . '="' . $sValue['eventValue'] . '"';
                    }
                    if ($sValue['readonly']) {
                        $indexTpl .= 'readonly="' . $sValue['readonly'] . '"';
                    }
                    $indexTpl .= '>';
                    break;
            }
            $indexTpl .= '</span>';
            if ((!$sValue['inline'] && !$search[$sKey+1]['inline']) || ($sValue['inline'] && !$search[$sKey+1]['inline'])) {
                $indexTpl .= '</div>';
            }
        }
        $indexTpl .= '<div class="margin_t20">
                    <a class="nowSearch btn"><span></span>查询</a>
                </div>
            </div>';
    }

    if ($preList) {
        foreach ($preList as $pKey => $pValue) {
            $indexTpl .= '<input type="hidden" value="' . $pValue['value'] . '" name="' . $pValue['name'] . '">';
        }
    }

    if ($tools) {
        $actionTools = setArrayByField(C('ACTION_LIST'), 'name');
        $indexTpl .= '<div class="tools margin_t5">';
        foreach ($tools as $tValue) {
            if (intval($_SESSION['_ACCESS_LIST_DATA'.$_SESSION[C('USER_AUTH_KEY')]][$_SESSION['_ACCESS_CONTROLLER_ID'.$_SESSION[C('USER_AUTH_KEY')]]]) & intval($actionTools[$tValue]['value'])) {
                $indexTpl .= '<a class="' . $tValue . '"><span></span>' . $actionTools[$tValue]['title'] .'</a>';
            }
        }

        $indexTpl .= '</div>';
    }

    $indexTpl .= '<div class="clear"></div>
            <div id="list" class="margin_t5">
            <ul class="list_header">
                <li>';

    if ($checkbox) {
        $indexTpl .= '<input class="check on" style="width:2%;" type="checkbox" name="allcheck" onclick="CheckAll(\'list\')"/>';
    }

    foreach ($tplList as $key => $value) {
        $indexTpl .= '<span class="' .$value['id']. ' ' . $value['class'] . '" style="width:' . $value['percent'] . '%;">' . $value['title'] . '</span>';
    }

    unset($tplList['action']);

    $indexTpl .= '</li>
        </ul>
        <ul class="list_list"></ul>
        <div class="page"></div>
</div>';
    $indexTpl .= '
    <script>
    var ajaxCheck = "";
    var firstClick = 0;
    function searchClick() {
        var ajaxCheckTmp = "";';
    foreach ($search as $sKey => $sValue) {
        $indexTpl .= 'ajaxCheckTmp += $("[name=' . $sValue['name'] . ']").val();';
    }

    $indexTpl .= '
        if (firstClick == 0) {
            firstClick = 1;
            ajaxCheck = ajaxCheckTmp;
            getList();
            return false;
        }

        if (ajaxCheck != ajaxCheckTmp) {
            getList();
            ajaxCheck = ajaxCheckTmp;
        }

    }
    $(function(){
        searchClick();
        ';

    $indexTpl .= '
        $(".search").click(function() {
            searchClick();
        })
    })';

    $indexTpl .= '

    // 分页获取数据
    function getList(p, callback) {

        // 获取页码，若无页码，代表获取第一页
        var urlRequest = getUrlRequest();
        p = p ? p : (urlRequest["p"] ? urlRequest["p"] : 1);
        var param = "p=" + p;
        Loading();
        ';

    if ($search) {
        foreach ($search as $sKey => $sValue) {
            $indexTpl .= 'param += "&' . $sValue['name'] . '=" + $("[name=' . $sValue['name'] . ']").val();';
        }
    }
    if ($preList) {
        foreach ($preList as $pKey => $pValue) {
            $indexTpl .= '
                var ' . $pValue['name'] . ' = $("[name=' . $pValue['name'] . ']").val();
                if (' . $pValue['name'] . ') {
                    param += "&' . $pValue['name'] . '=" + ' . $pValue['name'] . ';
                }';
        }
    }

    $indexTpl .= '
        $(".list_list").html("");
        $(".page").html("");
        // AJAX获取数据
        $.post("'. __CONTROLLER__ . '/lists", param, function(json) {

            // 初始化DIV框，准备写入数据
            $(".page").html("");
            $(".list_list").html("");

            if (json) {
                if (json.list && json.list.length) {
                    $(".page").html(json.page);
                    var level = $(".page .current").html() ? $(".page .current").html() : 1;
                    var obj = json.list;
                    var htm = "";

                    // 循环追加数据
                    for (var i = 0; i < obj.length; i ++) {

                        htm += \'<li\'
                        if (i % 2) {
                            htm += \' class = "even"\';
                        }
                        htm += \'>';
    if ($checkbox) {
        $indexTpl .= '<input class="check on" style="width:2%;" type="checkbox" name="key" value="\'+obj[i]["' . $tplList['id']['id'] . '"]+\'"/>';
    }

    foreach ($tplList as $key => $value) {
        $indexTpl .= '<span class="' . $value['id'] . ' ' . $value['class'] . '" style="width:' . $value['percent'] . '%;">';
        if ($key === 'id') {
            $indexTpl .= '\'+(i+1 +(level-1) * 10)+\'';
        } else {
            $indexTpl .= '\'+obj[i]["' . $value['id'] . '"]+\'';
        }
        $indexTpl .= '</span>';

    }

    foreach ($action as $aKey => $aValue) {
        $indexTpl .= '<span class="' . $aValue['id'] . '"  style="width:' . $aValue['percent'] . '%;"><a href="javascript:' . $aValue['id'] . '(\'+obj[i]["' . $tplList['id']['id'] . '"]+\')">' . $aValue['title'] . '</a></span>';
    }

    $indexTpl .= '</li>\';
                    }
                    closeLoading();
                    $(".list_list").html(htm);
                } else {
                    closeLoading();
                    $(".list_list").html(\'<li><span style="width:98%">' . C('DATA_NOT_FOUND') .'</span></li>\');
                }
                if (callback) {
                    eval(callback+"()");
                }
            }

        }, "json")
    }
    $(function() {
        $(".list_list li").live("mouseover", function() {
            $(this).addClass("over").siblings().removeClass("over");
        })
    })
    </script>';

    return $indexTpl;
}

function generationAddTpl($addList, $pk ='', $title = '', $result = array(), $submit = true) {

    $action = $result[$pk] ? 'edit' : 'add';
    $addTpl = '<div class="margin_t5" id="list">
        <ul class="list_header">
            <li><span class="title">' . $title . '</span><span attr="' . intval($_REQUEST['p']) . '" class="return">返回</span></li>
        </ul>
        <form method="post" action="' . __CONTROLLER__ . '/' . $action . '/" enctype="multipart/form-data">
            <ul class="list_list">';
        $tmp = array();
        foreach ($addList as $value) {
            if ($value['type'] == 'hidden') {
                $tmp[] = $value;
                continue;
            }
            $addTpl .= '<li class="' . $value['class'] . '">
                    <div class="row_left">';
            if ($value['require']) {
                $addTpl .= '<font color="red">*</font>';
            }
            $addTpl .= $value['title'] . '：</div>
                    <div class="row_right">';
                switch ($value['label']) {
                    case 'textarea' :
                        $addTpl .= '<textarea class="' . $value['labelClass'] . '" name="' . $value['name'] . '" ROWS="5" COLS="35"';
                        if ($value['event'] && $value['eventValue']) {
                            $addTpl .= $value['event'] . '="' . $value['eventValue'] . '"';
                        }
                        $addTpl .= '>' . $result[$value['name']] . '</textarea>';
                        break;
                    case 'select' :
                        $addTpl .= '<select class="' . $value['labelClass'] . '" name="' . $value['name'] . '" ';
                            if ($value['event'] && $value['eventValue']) {
                                $addTpl .= $value['event'] . '="' . $value['eventValue'] . '"';
                            }
                            $addTpl .= '>';
                            foreach ($value['default'] as $defaultKey => $defaultValue) {
                                $addTpl .= '<option value="' . $defaultKey . '">' . $defaultValue . '</option>';
                            }
                            foreach ($value['data'] as $dKey => $dValue) {
                                $addTpl .= '<option value="' . $dKey . '" ';
                                if ($dKey == $result[$value['name']]) {
                                    $addTpl .= ' selected ';
                                }
                                $addTpl .= '>' . $dValue . '</option>';
                            }
                        $addTpl .= '</select>';
                        break;
                    case 'div' :
                        $addTpl .= '<div class="' . $value['labelClass'] . '">' . $result[$value['name']] . '</div>';
                        break;
                    default :
                        switch ($value['type']) {
                            case 'file' :
                                // 上传文件与制定的 file 框匹配显示（并与以前的程序兼容）
                                if (is_array($result['file'])) {
                                    if ($result['file'][$value['name']] && in_array($result['ext'][$value['name']], explode(',', C('ALLOW_IMAGE_TYPE')))) {
                                        $addTpl .= '<div><img src="' . $result['file'][$value['name']] . '" width="80" height="80"></div>';
                                    }
                                    $addTpl .= '<input class="' . $value['labelClass'] . '" type="' . $value['type'] . '" name="' . $value['name'] . '"/>';
                                    if ($result['file'][$value['name']] && !in_array($result['ext'][$value['name']], explode(',', C('ALLOW_IMAGE_TYPE')))) {
                                        $addTpl .= '<a class="download" href="' . $result['file'][$value['name']] . '" target="_blank">下载</a>';
                                    }
                                    break;
                                }
                                // 以前的 文件显示
                                if ($result['file'] && in_array($result['ext'], explode(',', C('ALLOW_IMAGE_TYPE')))) {
                                    $addTpl .= '<div><img src="' . $result['file'] . '" width="80" height="80"></div>';
                                }
                                $addTpl .= '<input class="' . $value['labelClass'] . '" type="' . $value['type'] . '" name="' . $value['name'] . '"/>';
                                if ($result['file'] && !in_array($result['ext'], explode(',', C('ALLOW_IMAGE_TYPE')))) {
                                    $addTpl .= '<a class="download" href="' . $result['file'] . '" target="_blank">下载</a>';
                                }
                                break;
                            case 'checkbox' :
                                foreach ($value['data'] as $dKey => $dValue) {
                                    $addTpl .= '<span><input class="' . $value['labelClass'] . '" type="' . $value['type'] . '" name="' . $value['name'] . '" value="' . $dKey . '" ';
                                    if ($value['event'] && $value['eventValue']) {
                                        $addTpl .= $value['event'] . '="' . $value['eventValue'] . '"';
                                    }
                                    switch ($value['sign']) {
                                        case '&':
                                            if (intval($dKey) & intval($result[str_replace('[]', '', $value['name'])])) {
                                                $addTpl .= ' checked ';
                                            }
                                            break;
                                        default :
                                            $dTmpValue = explode(',', $result[str_replace('[]', '', $value['name'])]);
                                            if (in_array($dKey, $dTmpValue)) {
                                                $addTpl .= ' checked ';
                                            }
                                    }
                                    $addTpl .= '><em>' . $dValue . '</em></span>';
                                }
                                break;
                            case 'radio' :
                                foreach ($value['data'] as $dKey => $dValue) {
                                    $addTpl .= '<input class="' . $value['labelClass'] . '" type="' . $value['type'] . '" name="' . $value['name'] . '" value="' . $dKey . '" ';
                                    if ($value['event'] && $value['eventValue']) {
                                        $addTpl .= $value['event'] . '="' . $value['eventValue'] . '"';
                                    }
                                    if ($dKey == $result[$value['name']]) {
                                        $addTpl .= ' checked ';
                                    }
                                    $addTpl .= '><em>' . $dValue . '</em>';
                                }
                                break;
                            case 'password' :
                                $addTpl .= '<input class="' . $value['labelClass'] . '" type="' . $value['type'] . '" name="' . $value['name'] . '"/>';
                                break;
                            case 'plupload' :
                                if ($result['upfiles_image']) {
                                    $addTpl .= '<div class="' . $value['selectClass'] . '_show"><img src="' . $result['upfiles_image'] . '" width="80" height="80" /></div>';
                                } else {
                                    $addTpl .= '<div class="' . $value['selectClass'] . '_show"></div>';
                                    $addTpl .= '<a class="' . $value['selectClass'] . '" href="javascript:;">' . $value['selectTitle'] . '</a>';
                                    $addTpl .= '<a class="' . $value['uploadClass'] . '" href="javascript:;">' . $value['uploadTitle'] . '</a>';
                                    $addTpl .= '<div class="' . $value['uploadClass'] . '_msg"></div>';
                                }
                                break;
                            default :
                                $addTpl .= '<input class="' . $value['labelClass'] . '" type="text" name="' . $value['name'] . '" ';
                                if ($value['event'] && $value['eventValue']) {
                                    $addTpl .= $value['event'] . '="' . $value['eventValue'] . '"';
                                }
                                if ($value['readonly']) {
                                    $addTpl .= 'readonly="' . $value['readonly'] . '"';
                                }
                                $addTpl .= ' value="' . $result[$value['name']] . '"/>';
                                break;
                        }
                        break;
                }
            $addTpl .= $value['tip'] . '</div>
                </li>';
        }

        $addTpl .= '<li>
                    <div class="row_right">';

        foreach ($tmp as $value) {
            $addTpl .= '<input type="hidden" name="' . $value['name'] . '" value="' . $result[$value['name']] . '">';
        }

        $addTpl .= '<input type="hidden" name="' . $pk . '" value="' . $result[$pk] . '">';

        if ($submit) {
            $addTpl .= '<button class="save btn" value="" type="submit">保存</button>';
        }

        $addTpl .= '</div>
                </li>
            </ul>
        </form>
    </div>';
    return $addTpl;
}

function ad2html($data) {

    $uploadsRootPath = C('UPLOADS_ROOT_PATH');
    $advertPath = C('ADVERT_PATH');

    switch ($data['at_id']) {
        // 普通类型
        case 1:
            // 显示代码
            $result = '<div id="advert' . $data['ap_id'] . '" style="width:' . $data['ap_width'] .'px;height:'. $data['ap_height'] .'px;overflow:hidden;position:relative;">';

            // 组织数据
            $item = '<div class="advertItem" style="width:' . $data['ap_width'] .';height:'. $data['ap_height'] .';">';
            $sign = '<span attr="'.$data['ap_id'].'" class="advertSign" style="left:'.ceil($data['ap_width']/2).'px">';
            $replaceHtml = '<div style="display:none;"><a href="{URL}" title="{REMINDS}" target="_blank"><img style="display:none;" src="{PIC}" alt="{REMINDS}" width="{WIDTH}" height="{HEIGHT}" /></a></div>';
            foreach ($data['advertList'] as $key => $value) {
                // 呈现广告图片
                $config = array(
                    '{URL}' => $value['adv_url'] ? $value['adv_url'] : 'javascript:void(0);',
                    '{ID}' => $value['adv_id'],
                    '{PIC}' => substr($uploadsRootPath, 1) . $advertPath . $value['adv_savepath'] . '/' . $value['adv_savename'],
                    '{WIDTH}' => $data['ap_width'],
                    '{HEIGHT}' => $data['ap_height'],
                    '{REMINDS}' => $value['adv_reminds'],
                );
                $sign .= '<em>' . ($key+1) . '</em>';
                $item .= str_replace(array_keys($config), array_values($config), $replaceHtml);
            }
            $item .= '</div>';
            $sign .= '</span>';
            // 多个时，显示轮播标识符
            if ($data['ap_ad_num'] > 1) {
                $result .= $sign;
            }
            $result .= $item . '</div><script>advAutoClick('.$data['ap_id'] . ')</script>';
            break;
        // 对联广告
        case 2 :
            // 显示代码
            $result = '<div id="advert' . $data['ap_id'] . '" style="width:100%;height:'. $data['ap_height'] .'px;overflow:hidden;position:absolute;top:50px;">';
            // 组织数据
            $item = '<div class="advertItem" style="position:relative;">';
            $replaceHtml = '<div style="left:0;position:absolute;"><a href="{URL}" title="{REMINDS}" target="_blank"><img src="{PIC}" alt="{REMINDS}" width="{WIDTH}" height="{HEIGHT}" /></a></div><div style="right:0;position:absolute;"><a href="{URL}" title="{REMINDS}" target="_blank"><img src="{PIC}" alt="{REMINDS}" width="{WIDTH}" height="{HEIGHT}" /></a></div>';

            // 呈现广告图片
            $config = array(
                '{URL}' => $data['advertList'][0]['adv_url'],
                '{ID}' => $data['advertList'][0]['adv_id'],
                '{PIC}' => $uploadsRootPath . $advertPath . $data['advertList'][0]['adv_savepath'] . '/' . $data['advertList'][0]['adv_savename'],
                '{WIDTH}' => $data['ap_width'],
                '{HEIGHT}' => $data['ap_height'],
                '{REMINDS}' => $data['advertList'][0]['adv_reminds'],
            );

            $item .= str_replace(array_keys($config), array_values($config), $replaceHtml);

            $item .= '</div>';
            $result .= $item . '</div><script>advAutoScroll(' . $data['ap_id'] . ')</script>';
            break;
    }
    return $result;
}
?>