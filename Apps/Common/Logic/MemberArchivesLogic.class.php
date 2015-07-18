<?php
namespace Common\Logic;
class MemberArchivesLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'mar_id ASC',
            'p' => intval($param['p']),
        );

        if ($param['me_id']) {
            $default['where']['me_id'] = $param['me_id'];
        }

        if ($param['mar_type']) {
            $default['where']['mar_type'] = $param['mar_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('MemberArchives')->getListByPage($config);

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
}