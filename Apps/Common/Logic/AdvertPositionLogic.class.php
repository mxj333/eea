<?php
namespace Common\Logic;
class AdvertPositionLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'ap_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['ap_title']) {
            $where['ap_title'] = array('like', '%' . $param['ap_title'] . '%');
        }
        
        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);
        $lists = D($this->name)->getListByPage($config);

        $themes = loadCache('template');
        foreach ($lists['list'] as $key => $value) {
            $lists['list'][$key]['te_name'] = $themes[$value['tt_id']][$value['te_name']];
        }

        return $lists;
    }
}