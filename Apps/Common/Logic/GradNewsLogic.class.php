<?php
namespace Common\Logic;
class GradNewsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'sort' => 'gn_id ASC',
            'p' => intval($param['p']),
        );

        if ($param['gn_title']) {
            $where['gn_title'] = array('like', '%' . $param['gn_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['gn_status']) {
                $lists['list'][$key]['gn_status'] = getStatus($value['gn_status']);
            }
        }

        // 输出数据
        return $lists;
    }

}