<?php
namespace Common\Logic;
class GroupLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'g_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['g_title']) {
            $default['where']['g_title'] = array('like', '%' . $param['g_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);
        // 处理数据
        foreach ($lists['list'] as $key => $value) {
            if ($value['g_status']) {
                $lists['list'][$key]['g_status'] = getStatus($value['g_status']);
            }
        }
        return $lists;
    }

}