<?php
namespace Common\Logic;
class EliminationLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'e_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['e_title']) {
            $default['where']['e_title'] = array('like', '%' . $param['e_title'] . '%');
        }

        $config = array_merge($default, $config);
        $type = array(1 => L('AUTO'), 2 => L('MANUAL'));

        // 分页获取数据
        $lists = D('Elimination')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['e_status']) {
                $lists['list'][$key]['e_status'] = getStatus($value['e_status']);
            }
            if ($value['e_type']) {
                $lists['list'][$key]['e_type'] = $type[$value['e_type']];
            }
        }

        // 输出数据
        return $lists;
    }

    // 淘汰规则添加
    public function insertRule() {

        // 先删除以前的规则  在添加规则
        $er_config['where']['e_id'] = intval($_POST['e_id']);
        D('EliminationRule')->delete($er_config);

        foreach ($_POST['er_value'] as $key => $value) {

            $value_second = $_POST['er_value_second'][$key];
            // 当是时间时 转成时间戳
            if ($_POST['er_type'][$key] == 3) {
                $value = strtotime($value);
                $value_second = strtotime($_POST['er_value_second'][$key]);
            }

            // 如果没有填值 直接跳过
            if (!$value) {
                continue;
            }

            $data = array(
                'e_id' => intval($_POST['e_id']),
                'er_type' => intval($_POST['er_type'][$key]),
                'er_condition' => intval($_POST['er_condition'][$key]),
                'er_value' => intval($value),
                'er_value_second' => intval($value_second),
            );
            
            // 入库
            D('EliminationRule')->insert($data);
        }
    }

    // 执行淘汰机制
    public function elimination($id, $type = 1) {
        
        $config['where']['e_id'] = intval($id);
        $info = D('Elimination')->getOne($config);
        if ($info['e_status'] != 1) {
            // 关闭状态的机制不执行
            return false;
        }

        $fieldsType = array(1 => 'res_downloads', 2 => 'res_hits', 3 => 'res_created');
        $condition = array('>', '<', '=', '>=', '<=', 'between', 'not between');

        $rule_list = D('EliminationRule')->getAll($config);
        $where = array();
        $where['SUBSTRING(res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $where['SUBSTRING(res_is_published, '.$type.', 1)'] = 1; // 发布
        $where['SUBSTRING(res_is_pass, '.$type.', 1)'] = 1; // 审核
        $where['SUBSTRING(res_is_eliminated, '.$type.', 1)'] = 9; // 未淘汰

        foreach ($rule_list as $rule_info) {

            if (in_array($rule_info['er_condition'], array(5, 6))) {
                // between 和 not between
                $where[$fieldsType[$rule_info['er_type']]] = array($condition[$rule_info['er_condition']], array(intval($rule_info['er_value']), intval($rule_info['er_value_second'])));
            } else {
                $where[$fieldsType[$rule_info['er_type']]] = array($condition[$rule_info['er_condition']], intval($rule_info['er_value']));
            }
        }
        
        // 资源淘汰
        return D('Resource')->elimination(array('where' => $where));
    }
}