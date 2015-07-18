<?php
namespace Common\Logic;
class PaymentsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'pa_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['pa_title']) {
            $default['where']['pa_title'] = array('like', '%' . $param['pa_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Payments')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['pa_status']) {
                $lists['list'][$key]['pa_status'] = getStatus($value['pa_status']);
            }
        }

        // 输出数据
        return $lists;
    }
}