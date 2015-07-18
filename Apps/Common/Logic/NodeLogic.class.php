<?php
namespace Common\Logic;
class NodeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'g_id ASC, n_sort ASC, n_id ASC',
            'p' => intval($param['p']),
        );
        
        if ($param['n_title']) {
            $default['where']['n_title'] = array('LIKE', '%' . $param['n_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['n_status']) {
                $lists['list'][$key]['n_status'] = getStatus($value['n_status']);
            }
        }

        // 输出数据
        return $lists;
    }
}