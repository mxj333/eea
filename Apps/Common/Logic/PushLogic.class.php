<?php
namespace Common\Logic;
class PushLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'p_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['p_title']) {
            $default['where']['p_title'] = array('like', '%' . $param['p_title'] . '%');
        }

        $config = array_merge($default, $config);
        $type = array(1 => L('AUTO'), 2 => L('MANUAL'));

        // 分页获取数据
        $lists = D('Push')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['p_status']) {
                $lists['list'][$key]['p_status'] = getStatus($value['p_status']);
            }
            if ($value['p_type']) {
                $lists['list'][$key]['p_type'] = $type[$value['p_type']];
            }
        }

        // 输出数据
        return $lists;
    }

    // 推优机制 规则添加
    public function insertRule() {

        // 先删除以前的规则  在添加规则
        $pr_config['where']['p_id'] = intval($_POST['p_id']);
        D('PushRule')->delete($pr_config);

        foreach ($_POST['pr_value'] as $key => $value) {

            $value_second = $_POST['pr_value_second'][$key];
            // 当是时间时 转成时间戳
            if ($_POST['pr_type'][$key] == 3) {
                $value = strtotime($value);
                $value_second = strtotime($_POST['pr_value_second'][$key]);
            }

            // 如果没有填值 直接跳过
            if (!$value) {
                continue;
            }

            $data = array(
                'p_id' => intval($_POST['p_id']),
                'pr_type' => intval($_POST['pr_type'][$key]),
                'pr_condition' => intval($_POST['pr_condition'][$key]),
                'pr_value' => intval($value),
                'pr_value_second' => intval($value_second),
            );
            
            // 入库
            D('PushRule')->insert($data);
        }
    }

    // 执行推优机制
    public function push($id, $type = 1) {
        
        $config['where']['p_id'] = intval($id);
        $info = D('Push')->getOne($config);
        if ($info['p_status'] != 1) {
            // 关闭状态的机制不执行
            return false;
        }

        $fieldsType = array(1 => 'res_downloads', 2 => 'res_hits', 3 => 'res_created');
        $condition = array('>', '<', '=', '>=', '<=', 'between', 'not between');

        $rule_list = D('PushRule')->getAll($config);
        $where = array();
        $where['SUBSTRING(res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $where['SUBSTRING(res_is_published, '.$type.', 1)'] = 1; // 发布
        $where['SUBSTRING(res_is_pass, '.$type.', 1)'] = 1; // 审核
        $where['SUBSTRING(res_is_eliminated, '.$type.', 1)'] = 9; // 未淘汰
        $where['SUBSTRING(res_is_pushed, '.$type.', 1)'] = 9; // 未推送

        foreach ($rule_list as $rule_info) {

            if (in_array($rule_info['pr_condition'], array(5, 6))) {
                // between 和 not between
                $where[$fieldsType[$rule_info['pr_type']]] = array($condition[$rule_info['pr_condition']], array(intval($rule_info['pr_value']), intval($rule_info['pr_value_second'])));
            } else {
                $where[$fieldsType[$rule_info['pr_type']]] = array($condition[$rule_info['pr_condition']], intval($rule_info['pr_value']));
            }
        }
        
        // 资源推优
        return D('Resource')->push(array('where' => $where));
    }
}