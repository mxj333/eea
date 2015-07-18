<?php
namespace Common\Logic;
class ResourceStandardsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'rst_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rst_title']) {
            $default['where']['rst_title'] = array('LIKE', '%' . $param['rst_title'] . '%');
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('ResourceStandards')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['rst_status']) {
                $lists['list'][$key]['rst_status'] = getStatus($value['rst_status']);
            }
        }

        // 输出数据
        return $lists;
    }
}