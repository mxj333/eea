<?php
namespace Common\Logic;
class AttributeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'at_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['at_title']) {
            $where['at_title'] = array('like', '%' . $param['at_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }
}