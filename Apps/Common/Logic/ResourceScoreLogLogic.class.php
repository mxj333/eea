<?php
namespace Common\Logic;
class ResourceScoreLogLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'rsl_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['rsl_status']) {
            $default['where']['rsl_status'] = $param['rsl_status'];
        }

        $config = array_merge($default, $config);
        
        $rstConfig['fields'] = 'rst_id,rst_title';
        $rst_list = D('ResourceStandards')->getAll($rstConfig);

        // 分页获取数据
        $lists = D('ResourceScoreLog')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            // 资源名称
            if ($value['res_id']) {
                $res_info = D('Resource')->getOne($value['res_id']);
                $lists['list'][$key]['res_title'] = $res_info['res_title'];
            }
            // 用户姓名
            if ($value['me_id']) {
                $member_info = D('Member')->getOne($value['me_id']);
                $lists['list'][$key]['me_nickname'] = $member_info['me_nickname'];
            }
            if ($value['rst_id']) {
                $lists['list'][$key]['rst_title'] = $rst_list[$value['rst_id']];
            }
            if ($value['rsl_status']) {
                $lists['list'][$key]['rsl_status'] = getStatus($value['rsl_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id) {
        // 信息
        $info = D('ResourceScoreLog')->getOne(intval($id));
        $info['rsl_created'] = date('Y-m-d', $info['rsl_created']);
        // 资源
        $res_info = D('Resource')->getOne($info['res_id']);
        $info['res_title'] = $res_info['res_title'];
        // 用户
        $member_info = D('Member')->getOne($info['me_id']);
        $info['me_nickname'] = $member_info['me_nickname'];
        // 评分标准
        $rst_info = D('ResourceStandards')->getOne($info['rst_id']);
        $info['rst_title'] = $rst_info['rst_title'];
        return $info;
    }
}