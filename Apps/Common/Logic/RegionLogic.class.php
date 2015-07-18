<?php
namespace Common\Logic;
class RegionLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 're_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['re_title']) {
            $default['where']['re_title'] = array('LIKE', '%' . $param['re_title'] . '%');
        }

        if (isset($param['re_status'])) {
            $default['where']['re_status'] = $param['re_status'];
        }

        if (isset($param['re_pid'])) {
            $default['where']['re_pid'] = $param['re_pid'];
        }

        if (isset($param['re_level'])) {
            $default['where']['re_level'] = $param['re_level'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Region')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['re_status']) {
                    $lists['list'][$key]['re_status'] = getStatus($value['re_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}