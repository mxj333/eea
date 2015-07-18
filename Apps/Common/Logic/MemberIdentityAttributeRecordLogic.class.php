<?php
namespace Common\Logic;
class MemberIdentityAttributeRecordLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'miare_id ASC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

    public function insert($data) {
        // 先删除属性
        $recordConfig['where']['mi_id'] = intval($data['mi_id']);
        D('MemberIdentityAttributeRecord')->delete($recordConfig);

        // 在添加属性
        $attribute = D('MemberIdentityAttribute')->getAll();
        $insert = array();
        foreach ($attribute as $attr_info) {
            
            if ($attr_info['miat_type'] == 2) {
                // 多选
                $mare_value = implode(',', $data[$attr_info['miat_name']]);
            } else {
                $mare_value = $data[$attr_info['miat_name']];
            }

            $insert[] = array(
                'mi_id' => intval($data['mi_id']),
                'miare_name' => strval($attr_info['miat_name']),
                'miare_value' => strval($mare_value),
            );
        }

        if ($insert) {
            D('MemberIdentityAttributeRecord')->insertAll(array('fields' => 'mi_id,miare_name,miare_value', 'values' => $insert));
        }
    }

    // 获取用户属性信息
    public function getInfo($me_id) {

        $config['fields'] = 'miare_name,miare_value';
        return D('MemberIdentityAttributeRecord')->getAll($config);
    }
}