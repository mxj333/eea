<?php
namespace Common\Logic;
class AppApplyLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'aa_id DESC',
            'p' => intval($param['p']),
        );
        
        if ($param['aa_title']) {
            $default['where']['aa_title'] = array('LIKE', '%' . $param['aa_title'] . '%');
        }

        $config = array_merge($default, $config);
        // 审核状态
        $pass_status = array(L('UNCENSORED'), L('APPROVAL'), 9 => L('NOPASSED'));

        // 分页获取数据
        $lists = D('AppApply')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            // 用户姓名
            if ($value['me_id']) {
                $member_info = D('Member')->getOne($value['me_id']);
                $lists['list'][$key]['me_nickname'] = $member_info['me_nickname'];
            }
            if ($value['aa_status']) {
                $lists['list'][$key]['aa_status'] = getStatus($value['aa_status']);
            }
            
            $lists['list'][$key]['aa_is_pass'] = $pass_status[$value['aa_is_pass']];
        }

        // 输出数据
        return $lists;
    }

    public function getById($id) {
        // 信息
        $info = D('AppApply')->getOne(intval($id));
        $info['aa_created'] = date('Y-m-d H:i:s', $info['aa_created']);

        // 申请用户
        $member_info = D('Member')->getOne($info['me_id']);
        $info['me_nickname'] = $member_info['me_nickname'];

        // 审核用户
        if ($info['aa_is_pass']) {
            $user_info = D('User')->getOne($info['u_id']);
            $info['u_nickname'] = $user_info['u_nickname'];
            $info['aa_pass_time'] = date('Y-m-d H:i:s', $info['aa_pass_time']);
        }

        // 审核状态
        $pass_status = array(L('UNCENSORED'), L('APPROVAL'), 9 => L('NOPASSED'));
        $info['aa_is_pass'] = $pass_status[$info['aa_is_pass']];

        return $info;
    }
}