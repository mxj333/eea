<?php
namespace Common\Logic;
class AdvertTypeLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        // 查询条件
        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'sort' => 'at_id ASC',
            'p' => intval($param['p']),
        );

        if ($param['at_title']) {
            $where['at_title'] = array('like', '%' . $param['at_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);
        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            $lists['list'][$key]['at_code'] = stripFilter($value['at_code']);
        }

        return $lists;
    }
}