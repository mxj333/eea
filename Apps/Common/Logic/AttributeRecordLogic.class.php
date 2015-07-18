<?php
namespace Common\Logic;
class AttributeRecordLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'are_id ASC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        return D($this->name)->getListByPage($config);
    }

    public function insert($data) {
        $model = reloadCache('model');
        $attributeConfig['where']['at_id'] = array('in', $model[$data['m_id']]['m_list']);
        $attributeConfig['fields'] = 'at_id,at_name,at_title,at_type,at_value';
        $attribute = D('Attribute', 'Model')->getAll($attributeConfig);

        D('AttributeRecord', 'Model')->delete(array('where' => array('art_id' => $data['art_id'])));

        $AttributeRecordConfig['fields'] = 'art_id,are_name,are_value';
        $add_data = array();
        foreach ($attribute as $key => $value) {
            $tmp = '';
            if (is_array($data[$value['at_name']])) {
                $tmp = ',' . implode(',',$data['are_value']) . ',';
            } else {
                $tmp = $data[$value['at_name']];
            }

            $add_data[] = array(
                'art_id' => intval($data['art_id']),
                'are_name' => strval($value['at_name']),
                'are_value' => strval($tmp),
            );
        }

        $AttributeRecordConfig['values'] = $add_data;
        return D('AttributeRecord', 'Model')->insertAll($AttributeRecordConfig);
    }
}