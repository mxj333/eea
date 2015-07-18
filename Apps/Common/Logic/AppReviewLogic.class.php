<?php
namespace Common\Logic;
class AppReviewLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'aa_id DESC',
            'p' => intval($param['p']),
            'where' => array('aa_is_pass' => 0),
        );
        
        if ($param['aa_title']) {
            $default['where']['aa_title'] = array('LIKE', '%' . $param['aa_title'] . '%');
        }

        if ($param['starttime'] && $param['endtime']) {
            $default['where']['aa_created'] = array('BETWEEN', array(strtotime($param['starttime']),strtotime($param['endtime'])));
        } elseif (!$param['starttime'] && $param['endtime']) {
            $default['where']['aa_created'] = array('ELT', strtotime($param['endtime']));
        } elseif ($param['starttime'] && !$param['endtime']) {
            $default['where']['aa_created'] = array('EGT', strtotime($param['starttime']));
        } 

        $config = array_merge($default, $config);
        
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
        }

        // 输出数据
        return $lists;
    }

    // 审核通过
    public function pass($id, $table = 'Member') {

        $config['aa_is_pass'] = 1;
        $config['aa_pass_time'] = time();
        $config['u_id'] = session(C('USER_AUTH_KEY'));
        $data['where']['aa_id'] = array('in', $id);

        return D('AppApply', 'Model')->update($config, $data);
    }

    // 审核不通过
    public function noPass($id, $table = 'Member') {

        $config['aa_is_pass'] = 9;
        $config['aa_pass_time'] = time();
        $config['u_id'] = session(C('USER_AUTH_KEY'));
        $data['where']['aa_id'] = array('in', $id);

        return D('AppApply', 'Model')->update($config, $data);
    }
}