<?php
namespace Common\Logic;
class EliminationRuleLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'er_id DESC',
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('EliminationRule')->getListByPage($config);

        // 输出数据
        return $lists;
    }
}