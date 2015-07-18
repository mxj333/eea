<?php
namespace Common\Logic;
class MemberAttributeRecordLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'mare_id ASC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

    public function insert($data) {
        // 先删除属性
        $recordConfig['where']['me_id'] = intval($data['me_id']);
        D('MemberAttributeRecord')->delete($recordConfig);

        // 在添加属性
        $attribute = D('MemberAttribute')->getAll();
        $insert = array();
        foreach ($attribute as $attr_info) {
            
            if ($attr_info['mat_type'] == 2) {
                // 多选
                $mare_value = implode(',', $data[$attr_info['mat_name']]);
            } else {
                $mare_value = $data[$attr_info['mat_name']];
            }

            $insert[] = array(
                'me_id' => intval($data['me_id']),
                'mare_name' => strval($attr_info['mat_name']),
                'mare_value' => strval($mare_value),
            );
        }

        if ($insert) {
            D('MemberAttributeRecord')->insertAll(array('fields' => 'me_id,mare_name,mare_value', 'values' => $insert));
        }
    }

    // 获取用户属性信息
    public function getInfo($me_id) {

        $config['fields'] = 'mare_name,mare_value';
        return D('MemberAttributeRecord')->getAll($config);
    }
}