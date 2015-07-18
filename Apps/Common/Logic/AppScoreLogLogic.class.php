<?php
namespace Common\Logic;
class AppScoreLogLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'asl_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['asl_status']) {
            $default['where']['asl_status'] = $param['asl_status'];
        }

        $config = array_merge($default, $config);
        
        $asConfig['fields'] = 'as_id,as_title';
        $as_list = D('AppStandards')->getAll($asConfig);

        // 分页获取数据
        $lists = D('AppScoreLog')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            // 应用名称
            if ($value['a_id']) {
                $app_info = D('App')->getOne($value['a_id']);
                $lists['list'][$key]['a_title'] = $app_info['a_title'];
            }
            // 用户姓名
            if ($value['me_id']) {
                $member_info = D('Member')->getOne($value['me_id']);
                $lists['list'][$key]['me_nickname'] = $member_info['me_nickname'];
            }
            if ($value['as_id']) {
                $lists['list'][$key]['as_title'] = $as_list[$value['as_id']];
            }
            if ($value['asl_status']) {
                $lists['list'][$key]['asl_status'] = getStatus($value['asl_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id) {
        // 信息
        $info = D('AppScoreLog')->getOne(intval($id));
        $info['asl_created'] = date('Y-m-d', $info['asl_created']);
        // 应用
        $app_info = D('App')->getOne($info['a_id']);
        $info['a_title'] = $app_info['a_title'];
        // 用户
        $member_info = D('Member')->getOne($info['me_id']);
        $info['me_nickname'] = $member_info['me_nickname'];
        // 评分标准
        $as_info = D('AppStandards')->getOne($info['as_id']);
        $info['as_title'] = $as_info['as_title'];
        return $info;
    }
}