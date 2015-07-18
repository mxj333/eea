<?php
namespace Common\Logic;
class AppTypeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'aty_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['aty_title']) {
            $default['where']['aty_title'] = array('LIKE', '%' . $param['aty_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('AppType')->getListByPage($config);

        // 输出数据
        return $lists;
    }
}