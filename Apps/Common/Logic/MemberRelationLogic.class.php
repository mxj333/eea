<?php
namespace Common\Logic;
class MemberRelationLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'mr_type ASC',
            'p' => intval($param['p']),
        );

        if ($param['me_id']) {
            $default['where']['me_id'] = $param['me_id'];
        }

        if ($param['parent_id']) {
            $default['where']['parent_id'] = $param['parent_id'];
        }

        if ($param['mr_type']) {
            $default['where']['mr_type'] = $param['mr_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('MemberRelation')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['me_id']) {
                    $meConfig['fields'] = 'me_nickname,me_mobile';
                    $meConfig['where']['me_id'] = $value['me_id'];
                    $info = D('Member', 'Model')->getOne($meConfig);
                    $lists['list'][$key]['me_nickname'] = $info['me_nickname'];
                    $lists['list'][$key]['me_phone'] = $info['me_mobile'];
                }

                if ($value['parent_id']) {
                    $pmeConfig['fields'] = 'me_nickname,me_mobile';
                    $pmeConfig['where']['me_id'] = $value['parent_id'];
                    $pinfo = D('Member', 'Model')->getOne($pmeConfig);
                    $lists['list'][$key]['parent_name'] = $pinfo['me_nickname'];
                    $lists['list'][$key]['parent_phone'] = $pinfo['me_mobile'];
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function insert($me_id, $data) {
        
        // 先删除以前关系
        $config['where']['me_id'] = intval($me_id);
        D('MemberRelation', 'Model')->delete($config);

        // 保存现在关系
        $saveData['fields'] = 'me_id,mr_type,parent_id';
        $saveData['values'] = $data;
        return D('MemberRelation', 'Model')->insertAll($saveData);
    }
}