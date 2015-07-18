<?php
namespace Common\Logic;
class MemberAccessLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'ma_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['ma_title']) {
            $default['where']['ma_title'] = array('like', '%' . $param['ma_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('MemberAccess')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['ma_status']) {
                $lists['list'][$key]['ma_status'] = getStatus($value['ma_status']);
            }
        }

        // 输出数据
        return $lists;
    }
}