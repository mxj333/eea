<?php
namespace Common\Logic;
class TemplateTypeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'tt_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['tt_title']) {
            $default['where']['tt_title'] = array('LIKE', '%' . $param['tt_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('TemplateType')->getListByPage($config);
        
        foreach ($lists['list'] as $key => $value) {
            if ($value['tt_status']) {
                $lists['list'][$key]['tt_status'] = getStatus($value['tt_status']);
            }
        }
        // 输出数据
        return $lists;
    }
}