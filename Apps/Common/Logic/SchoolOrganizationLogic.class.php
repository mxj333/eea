<?php
namespace Common\Logic;
class SchoolOrganizationLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'so_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        }

        if ($param['so_status']) {
            $default['where']['so_status'] = $param['so_status'];
        }

        if ($param['so_type']) {
            $default['where']['so_type'] = array('LIKE', '%' . $param['so_type'] . '%');
        }

        if ($param['so_title']) {
            $default['where']['so_title'] = array('LIKE', '%' . $param['so_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('SchoolOrganization')->getListByPage($config);

        if ($config['is_deal_result']) {

            foreach ($lists['list'] as $key => $value) {
                if ($value['so_status']) {
                    $lists['list'][$key]['so_status'] = getStatus($value['so_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}