<?php
namespace Common\Logic;
class GradNewsContentLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'gnc_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['gnc_title']) {
            $where['gnc_title'] = array('like', $param['gnc_title']);
        }
        
        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        // 获取所有新闻栏目
        $GradNewsConfig['fields'] = 'gn_id,gn_title';
        $newsCategory = D('GradNews', 'Model')->getAll($GradNewsConfig);

        // 处理数据
        foreach ($lists['list'] as $key => $value) {
            $lists['list'][$key]['gnc_remark'] = strip_tags($lists['list'][$key]['gnc_remark']);
            $lists['list'][$key]['gn_title'] = $newsCategory[$value['gn_id']];
            $lists['list'][$key]['gnc_created'] = toDate($value['gnc_created']);
        }
        return $lists;
    }
}