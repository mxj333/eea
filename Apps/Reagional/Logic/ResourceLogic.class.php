<?php
namespace Reagional\Logic;
class ResourceLogic extends ReagionalLogic {
    
    // 处理发布位置  将选项转换为字段
    public function localToFields($number, $data = array()) {
        $return = array();

        // 平台
        $return['res_is_sys'] = ($number & 1) ? 1 : 0;

        if ($number & 2) {
            // 区域
            $return['re_id'] = $data['re_id'] ? $data['re_id'] : session('re_id');
            $return['re_title'] = $data['re_title'] ? $data['re_title'] : session('re_title');
        } else {
            $return['re_id'] = '';
            $return['re_title'] = '';
        }

        if ($number & 4) {
            // 学校
            $return['s_id'] = $data['s_id'] ? $data['s_id'] : session('s_id');
        } else {
            $return['s_id'] = 0;
        }

        return $return;
    }

    // 处理发布位置  将字段转换为选项值
    public function fieldsToLocal($info, $belong = 1) {

        $return = 0;

        switch ($belong) {
            case 4:
                if ($info['s_id']) {
                    $return += 4;
                }
            case 3:
                // 学校发布  只显示区域 和 平台
                if ($info['re_id']) {
                    $return += 2;
                }
            case 2:
                //  区域发布  只展示平台
                if ($info['res_is_sys']) {
                    $return += 1;
                }
            default:
                $return += 0;
        }

        return $return;
    }
}