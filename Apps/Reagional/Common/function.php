<?php
// 档案 html 内容
function getArchivesHtml($data, $type) {
    $htm = '<div><div class="archives">';
    switch ($type) {
        case 1 :
            $tag = reloadCache('tag');
            $htm .= '<input type="text" class="Wdate" name="mar_endtime" onclick="' . "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});" . '" value="';
            if ($data['mar_endtime']) {
                $htm .= date('Y-m-d', $data['mar_endtime']);
            }
            $htm .= '"/><select name="mar_school_type">';
            foreach ($tag[4] as $sc_id => $sc_name) {
                $htm .= '<option value="' . $sc_id . '" ';
                if ($data['mar_school_type'] == $sc_id) {
                    $htm .= 'selected';
                }
                $htm .= '>' . $sc_name . '</option>';
            }
            $htm .= '</select><input type="text" name="mar_title" ';
            if ($data['mar_title']) {
                $htm .= 'value ="' . $data['mar_title'] . '"';
            }
            $htm .= '/>';
            break;
        case 5 :
            $htm .= '<input type="text" class="Wdate" name="mar_starttime" onclick="' . "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});" . '" value="';
            if ($data['mar_starttime']) {
                $htm .= date('Y-m-d', $data['mar_starttime']);
            }
            $htm .= '"/><input type="text" class="Wdate" name="mar_endtime" onclick="' . "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});" . '" value="';
            if ($data['mar_endtime']) {
                $htm .= date('Y-m-d', $data['mar_endtime']);
            }
            $htm .= '"/><input type="text" name="mar_title" ';
            if ($data['mar_title']) {
                $htm .= 'value ="' . $data['mar_title'] . '"';
            }
            $htm .= '/>';
            break;
        case 7 :
            $htm .= '<span class="textarea">教学能力：</span><textarea name="mar_text">' . $data['mar_text'] . '</textarea>';
            break;
        case 8 :
            $htm .= '<span class="marTitle">工作单位：</span><input type="text" name="mar_title" ';
            if ($data['mar_title']) {
                $htm .= 'value ="' . $data['mar_title'] . '"';
            }
            $htm .= '/>';
            break;
        case 9 :
            $tag = reloadCache('tag');
            $htm .= '<input type="text" class="Wdate" name="mar_endtime" onclick="' . "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});" . '" value="';
            if ($data['mar_endtime']) {
                $htm .= date('Y-m-d', $data['mar_endtime']);
            }
            $htm .= '"/><select name="mar_subject">';
            foreach ($tag[5] as $sub_id => $sub_name) {
                $htm .= '<option value="' . $sub_id . '" ';
                if ($data['mar_subject'] == $sub_id) {
                    $htm .= 'selected';
                }
                $htm .= '>' . $sub_name . '</option>';
            }
            $htm .= '</select><input type="text" name="mar_score" ';
            if ($data['mar_score']) {
                $htm .= 'value ="' . $data['mar_score'] . '"';
            }
            $htm .= '/>';
            break;
        default :
            $htm .= '<input type="text" class="Wdate" name="mar_endtime" onclick="' . "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});" . '" value="';
            if ($data['mar_endtime']) {
                $htm .= date('Y-m-d', $data['mar_endtime']);
            }
            $htm .= '"/><input type="text" name="mar_title" ';
            if ($data['mar_title']) {
                $htm .= 'value ="' . $data['mar_title'] . '"';
            }
            $htm .= '/>';
    }
    $htm .= '<input type="hidden" name="mar_id" ';
    if ($data['mar_id']) {
        $htm .= 'value ="' . $data['mar_id'] . '"';
    }
    $htm .= '></div>';
    $htm .= '<div class="funcBtn"><button type="button" class="save"><span></span>保存</button>';
    if ($data) {
        // 有数据才有删除按钮
        $htm .= '<button type="button" class="del"><span></span>删除</button>';
    }
    $htm .= '</div><div class="clear"></div></div>';

    return $htm;
}

/*
 * 处理 平台、区域、学校 字段的值
 * 字段存3个数，第一位代表平台  第二位代表区域  第三位代表学校
 */
function dealTypeValue($value, $type = 1, $default = '') {

    if ($default === '' || $default === NULL) {
        $default = '999';
    }

    if ($type == 3) {
        //  学校
        $string = substr($default, 0, 2) . intval($value);
    } elseif ($type == 2) {
        // 区域
        $string = $default[0] . intval($value) . $default[2];
    } else {
        $string = intval($value) . substr($default, 1, 2);
    }
    return $string;
}
?>