<?php
namespace Common\Logic;
class ClassInstructorsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 't_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['s_id']) {
            $default['where']['s_id'] = $param['s_id'];
        }

        if ($param['c_id']) {
            $default['where']['c_id'] = $param['c_id'];
        }

        if ($param['me_id']) {
            $default['where']['me_id'] = $param['me_id'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('ClassInstructors')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['me_id']) {
                    $meConfig['fields'] = 'me_nickname';
                    $meConfig['where']['me_id'] = $value['me_id'];
                    $lists['list'][$key]['me_nickname'] = D('Member', 'Model')->getOne($meConfig);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getList($c_id, $config = array()) {

        $default['where']['c_id'] = $c_id;
        $default['order'] = 't_id ASC';
        $default['fields'] = '*';
        $default['is_deal_result'] = true;

        $config = array_merge($default, $config);
        $list = D('ClassInstructors', 'Model')->getAll($config);

        if ($config['is_deal_result']) {
            foreach ($list as $key => $value) {
                if ($value['me_id']) {
                    $meConfig['fields'] = 'me_nickname';
                    $meConfig['where']['me_id'] = $value['me_id'];
                    $list[$key]['me_nickname'] = D('Member', 'Model')->getOne($meConfig);
                }
            }
        }
        return $list;
    }

    // 任课教师
    public function insert($s_id, $c_id, $data) {
        
        // 先删除以前设置
        $config['where']['s_id'] = $s_id;
        $config['where']['c_id'] = $c_id;
        $aa = D('ClassInstructors', 'Model')->delete($config);

        // 添加
        $saveData = array();
        $saveFields = array('s_id', 'c_id', 't_id', 'me_id');
        foreach ($data as $sub_id => $auth_id) {
            $saveData[] = array(
                's_id' => intval($s_id),
                'c_id' => intval($c_id),
                't_id' => intval($sub_id),
                'me_id' => intval($auth_id),
            );
        }

        return D('ClassInstructors', 'Model')->insertAll(array('fields' =>$saveFields, 'values' => $saveData));
    }
}