<?php
namespace Common\Logic;
class AppStandardsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'as_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['as_title']) {
            $default['where']['as_title'] = array('LIKE', '%' . $param['as_title'] . '%');
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('AppStandards')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['as_status']) {
                $lists['list'][$key]['as_status'] = getStatus($value['as_status']);
            }
        }

        // 输出数据
        return $lists;
    }
}