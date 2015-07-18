<?php
namespace Common\Logic;
class TagLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 't_id DESC',
            'p' => intval($param['p'])
        );

        if ($param['t_status']) {
            $default['where']['t_status'] = $param['t_status'];
        }

        if ($param['t_type']) {
            $default['where']['t_type'] = $param['t_type'];
        }

        if ($param['t_title']) {
            $default['where']['t_title'] = array('like', '%' . $param['t_title'] . '%');
        }

        $config = array_merge($default, $config);
        // 特殊处理 where
        if ($config['where']) {
            $config['where'] = array_merge($default['where'], $config['where']);
        }
        
        $type = explode(',', C('SYSTEM_TAG_TYPE'));

        // 分页获取数据
        $lists = D('Tag')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                $lists['list'][$key]['t_type'] = $type[$value['t_type']];
                if ($value['t_status']) {
                    $lists['list'][$key]['t_status'] = getStatus($value['t_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }
}