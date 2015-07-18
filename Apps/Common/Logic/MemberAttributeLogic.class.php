<?php
namespace Common\Logic;
class MemberAttributeLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'mat_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['mat_title']) {
            $where['mat_title'] = array('like', '%' . $param['mat_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

    public function delete($id) {
        $config['where']['mat_id'] = array('IN', strval($id));
        $list = D('MemberAttribute')->getAll($config);
        
        // 删除属性
        $result = D('MemberAttribute', 'Model')->delete($config);
        if ($result !== false) {
            // 删除属性记录
            foreach ($list as $info) {
                $recordConfig['where']['mare_name'] = $info['mat_name'];
                D('MemberAttributeRecord', 'Model')->delete($recordConfig);
            }
        }

        return $result;
    }
}