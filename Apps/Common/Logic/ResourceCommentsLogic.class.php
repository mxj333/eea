<?php
namespace Common\Logic;
class ResourceCommentsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'table' => C('DB_PREFIX').'resource_comments rc',
            'join' => 'LEFT JOIN ' . C('DB_PREFIX') . 'resource r ON r.res_id = rc.res_id',
            'is_deal_result' => true,
            'order' => 'rc.rco_id DESC',
            'p' => intval($param['p']),
            'where' => array('rc.rco_pid' => 0)
        );

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['r.re_id'] = array('IN', $region);
        }

        if ($param['s_id']) {
            $default['where']['r.s_id'] = $param['s_id'];
        }

        if ($param['res_title']) {
            // 资源查询
            $default['where']['r.res_title'] = $param['res_title'];
        }

        if ($param['me_nickname']) {
            // 用户名称查询
            $meConfig['fields'] = 'me_id';
            $meConfig['where']['me_nickname'] = $param['me_nickname'];
            $me_id = D('Member', 'Model')->getOne($meConfig);
            if (!$me_id) {
                // '用户不存在';
                return array();
            }

            $default['where']['rc.me_id'] = $me_id;
        }
        
        if ($param['rco_status']) {
            $default['where']['rc.rco_status'] = $param['rco_status'];
        }

        if ($param['rco_pid']) {
            $default['where']['rc.rco_pid'] = $param['rco_pid'];
        }

        if ($param['starttime']) {
            $default['where']['rc.rco_created'] = array('EGT', $param['starttime']);
        }
        if ($param['endtime']) {
            $default['where']['rc.rco_created'] = array('ELT', $param['endtime']);
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('ResourceComments')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                // 资源名称
                if ($value['res_id']) {
                    $res_info = D('Resource', 'Model')->getOne(array('where' => array('res_id' => $value['res_id'])));
                    $lists['list'][$key]['res_title'] = $res_info['res_title'];
                }
                // 用户姓名
                if ($value['me_id']) {
                    $member_info = D('Member', 'Model')->getOne(array('where' => array('me_id' => $value['me_id'])));
                    $lists['list'][$key]['me_nickname'] = $member_info['me_nickname'];
                }
                if ($value['rco_status']) {
                    $lists['list'][$key]['rco_status'] = getStatus($value['rco_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id, $config = array()) {
        
        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);

        // 信息
        $info = D('ResourceComments')->getOne(intval($id));

        // 信息处理
        if ($config['is_deal_result']) {
            if ($info['rco_created']) {
                $info['rco_created'] = date('Y-m-d', $info['rco_created']);
            }
            // 资源
            $res_info = D('Resource')->getOne($info['res_id']);
            $info['res_title'] = $res_info['res_title'];
            // 用户
            $member_info = D('Member')->getOne($info['me_id']);
            $info['me_nickname'] = $member_info['me_nickname'];
        }

        return $info;
    }

    public function delete($config) {

        // 有子集
        $data['where']['rco_pid'] = $config['where']['rco_id'];
        $list = D('ResourceComments')->getAll($data);
        if ($list) {
            $this->error = L('CHILDREN_EXIST');
            return false;
        }
        
        // 删除
        return D('ResourceComments', 'Model')->delete($config);
    }
}