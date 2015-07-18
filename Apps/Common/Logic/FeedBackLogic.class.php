<?php
namespace Common\Logic;
class FeedBackLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'fb_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['fb_content']) {
            $where['fb_content'] = array('like', '%' . $param['fb_content'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

}