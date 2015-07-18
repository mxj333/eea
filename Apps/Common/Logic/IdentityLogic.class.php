<?php
namespace Common\Logic;
class IdentityLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'id_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['id_title']) {
            $default['where']['id_title'] = array('like', '%' . $param['id_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Identity')->getListByPage($config);

        // 管理的字段
        $fields = $this->getMemberFields();

        foreach ($lists['list'] as $key => $value) {
            if ($value['id_fields']) {
                $ids_fields_name = array();
                $ids_fields = explode(',', $value['id_fields']);
                foreach ($ids_fields as $id_field) {
                    $ids_fields_name[] = $fields[$id_field];
                }
                $lists['list'][$key]['id_fields'] = implode(',', $ids_fields_name);
            }
            if ($value['id_status']) {
                $lists['list'][$key]['id_status'] = getStatus($value['id_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    // 获取用户表的字段
    public function getMemberFields() {
        $member_fields = D('MemberIdentity', 'Model')->queryFetchAll('SHOW FULL FIELDS FROM ' . C('DB_PREFIX') . 'member_identity');
        $detail_fields = D('MemberIdentityDetail', 'Model')->queryFetchAll('SHOW FULL FIELDS FROM ' . C('DB_PREFIX') . 'member_identity_detail');

        $fields = array_merge((array)$member_fields, (array)$detail_fields);

        $return = array();
        foreach ($fields as $field_info) {

            // 部分字段不显示
            if (in_array($field_info['Field'], array('mi_app_all','mi_creator_id','mi_creator_table','mi_data_md5','mi_is_deleted','mi_deleted_time','mi_deleted_table','mi_deleted_extend_id','mid_id'))) {
                continue;
            }

            $return[$field_info['Field']] = $field_info['Comment'];
        }

        return $return;
    }

    // 获取身份等级
    public function getLevel($fields = '') {
        $config['where']['id_status'] = 1;
        if ($fields) {
            $config['fields'] = $fields;
        }

        return D('Identity', 'Model')->getAll($config);
    }
}