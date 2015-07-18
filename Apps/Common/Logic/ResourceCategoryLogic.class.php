<?php
namespace Common\Logic;
class ResourceCategoryLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'rc_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rc_status']) {
            $default['where']['rc_status'] = $param['rc_status'];
        }

        if ($param['rc_title']) {
            $default['where']['rc_title'] = array('like', '%' . $param['rc_title'] . '%');
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('ResourceCategory')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['rc_status']) {
                    $lists['list'][$key]['rc_status'] = getStatus($value['rc_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}