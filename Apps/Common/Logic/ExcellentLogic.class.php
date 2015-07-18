<?php
namespace Common\Logic;
class ExcellentLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'ex_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['ex_title']) {
            $default['where']['ex_title'] = array('like', '%' . $param['ex_title'] . '%');
        }

        $config = array_merge($default, $config);
        $type = array(1 => L('AUTO'), 2 => L('MANUAL'));

        // 分页获取数据
        $lists = D('Excellent')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            if ($value['ex_status']) {
                $lists['list'][$key]['ex_status'] = getStatus($value['ex_status']);
            }
            if ($value['ex_type']) {
                $lists['list'][$key]['ex_type'] = $type[$value['ex_type']];
            }
        }

        // 输出数据
        return $lists;
    }

    // 推优机制规则添加
    public function insertRule() {

        // 先删除以前的规则  在添加规则
        $exr_config['where']['ex_id'] = intval($_POST['ex_id']);
        D('ExcellentRule')->delete($exr_config);

        foreach ($_POST['exr_value'] as $key => $value) {

            $value_second = $_POST['exr_value_second'][$key];
            // 当是时间时 转成时间戳
            if ($_POST['exr_type'][$key] == 3) {
                $value = strtotime($value);
                $value_second = strtotime($_POST['exr_value_second'][$key]);
            }

            // 如果没有填值 直接跳过
            if (!$value) {
                continue;
            }

            $data = array(
                'ex_id' => intval($_POST['ex_id']),
                'exr_type' => intval($_POST['exr_type'][$key]),
                'exr_condition' => intval($_POST['exr_condition'][$key]),
                'exr_value' => intval($value),
                'exr_value_second' => intval($value_second),
            );
            
            // 入库
            D('ExcellentRule')->insert($data);
        }
    }

    // 执行推优机制
    public function excellent($id, $type = 1) {
        
        $config['where']['ex_id'] = intval($id);
        $info = D('Excellent')->getOne($config);
        if ($info['ex_status'] != 1) {
            // 关闭状态的机制不执行
            return false;
        }

        $fieldsType = array(1 => 'res_downloads', 2 => 'res_hits', 3 => 'res_created');
        $condition = array('>', '<', '=', '>=', '<=', 'between', 'not between');

        $rule_list = D('ExcellentRule')->getAll($config);
        $where = array();
        $where['SUBSTRING(res_is_deleted, '.$type.', 1)'] = 9; // 未删
        $where['SUBSTRING(res_is_published, '.$type.', 1)'] = 1; // 发布
        $where['SUBSTRING(res_is_pass, '.$type.', 1)'] = 1; // 审核
        $where['SUBSTRING(res_is_eliminated, '.$type.', 1)'] = 9; // 未淘汰
        $where['SUBSTRING(res_is_excellent, '.$type.', 1)'] = 9; // 未推优

        foreach ($rule_list as $rule_info) {

            if (in_array($rule_info['exr_condition'], array(5, 6))) {
                // between 和 not between
                $where[$fieldsType[$rule_info['exr_type']]] = array($condition[$rule_info['exr_condition']], array(intval($rule_info['exr_value']), intval($rule_info['exr_value_second'])));
            } else {
                $where[$fieldsType[$rule_info['exr_type']]] = array($condition[$rule_info['exr_condition']], intval($rule_info['exr_value']));
            }
        }
        
        // 资源推优
        return D('Resource')->excellent(array('where' => $where));
    }
}