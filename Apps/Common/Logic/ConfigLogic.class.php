<?php
namespace Common\Logic;
class ConfigLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'con_id ASC',
            'p' => intval($param['p']),
        );

        if ($param['con_title']) {
            $default['where']['con_title'] = array('like', '%' . $param['con_title'] . '%');
        }

        if ($param['con_type']) {
            $default['where']['con_type'] = $param['con_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            $lists['list'][$key]['con_value'] = stripFilter($value['con_value']);
        }

        // 输出数据
        return $lists;
    }

}