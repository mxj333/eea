<?php
namespace Common\Logic;
class MemberIdentityAttributeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'miat_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['miat_title']) {
            $where['miat_title'] = array('like', '%' . $param['miat_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

    public function delete($id) {
        $config['where']['miat_id'] = array('IN', strval($id));
        $list = D('MemberIdentityAttribute')->getAll($config);
        
        // 删除属性
        $result = D('MemberIdentityAttribute', 'Model')->delete($config);
        if ($result !== false) {
            // 删除属性记录
            foreach ($list as $info) {
                $recordConfig['where']['miare_name'] = $info['miat_name'];
                D('MemberIdentityAttributeRecord', 'Model')->delete($recordConfig);
            }
        }

        return $result;
    }
}