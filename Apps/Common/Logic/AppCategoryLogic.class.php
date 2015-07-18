<?php
namespace Common\Logic;
class AppCategoryLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'ac_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['ac_title']) {
            $default['where']['ac_title'] = array('like', '%' . $param['ac_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('AppCategory')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['ac_status']) {
                    $lists['list'][$key]['ac_status'] = getStatus($value['ac_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}