<?php
namespace Common\Logic;
class SchoolManagerLogic extends Logic {

    public function check($me_id = '', $s_id = 1) {

        $member['where']['sm_status'] = 1;
        $member['where']['s_id'] = intval($s_id);
        $member['where']['me_id'] = intval($me_id);
        return D($this->name, 'Model')->getOne($member);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'sm_id DESC',
            'p' => intval($param['p']),
        );
        
        if ($param['s_id']) {
            $default['where']['s_id'] = intval($param['s_id']);
        }

        if ($param['me_nickname'] || $param['me_account']) {
            $meConfig['fields'] = 'me_id';
            if ($param['me_nickname']) {
                $meConfig['where']['me_nickname'] = $param['me_nickname'];
            }
            if ($param['me_account']) {
                $meConfig['where']['me_account'] = $param['me_account'];
            }
            $me_id = D('Member', 'Model')->getOne($meConfig);
            if (!$me_id) {
                return array();
            }
            $default['where']['me_id'] = intval($me_id);
        }

        if (isset($param['me_id'])) {
            $default['where']['me_id'] = intval($param['me_id']);
        }

        if (isset($param['sm_status'])) {
            $default['where']['sm_status'] = intval($param['sm_status']);
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('SchoolManager')->getListByPage($config);

        if ($config['is_deal_result']) {

            foreach ($lists['list'] as $key => $value) {
                if ($value['sm_status']) {
                    $lists['list'][$key]['sm_status'] = getStatus($value['sm_status']);
                }
                if ($value['s_id']) {
                    $sConfig['fields'] = 's_title';
                    $sConfig['where']['s_id'] = $value['s_id'];
                    $s_title = D('School', 'Model')->getOne($sConfig);
                    $lists['list'][$key]['s_title'] = strval($s_title);
                }
                if ($value['me_id']) {
                    $member_config['fields'] = 'me_nickname';
                    $member_config['where']['me_id'] = $value['me_id'];
                    $nickname = D('Member', 'Model')->getOne($member_config);
                    $lists['list'][$key]['me_nickname'] = strval($nickname);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id, $config = array()) {

        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);

        // 管理员关系信息
        $school_config['where']['sm_id'] = intval($id);
        $school_manage = D('SchoolManager', 'Model')->getOne($school_config);

        // 用户信息
        if ($config['is_deal_result']) {
            $member_config['fields'] = 'me_nickname';
            $member_config['where']['me_id'] = $school_manage['me_id'];
            $nickname = D('Member', 'Model')->getOne($member_config);
            $school_manage['me_nickname'] = strval($nickname);

            $sch_config['fields'] = 's_title';
            $sch_config['where']['s_id'] = $school_manage['s_id'];
            $s_title = D('School', 'Model')->getOne($sch_config);
            $school_manage['s_title'] = strval($s_title);
        }

        return $school_manage;
    }

    public function insert($data) {
        $save_data = D('SchoolManager', 'Model')->create($data);
        if (false === $save_data) {
            $this->error = D('SchoolManager', 'Model')->getError();
            return false;
        }
        // 验证唯一
        $config['where']['s_id'] = $data['s_id'];
        $config['where']['me_id'] = $data['me_id'];
        $info = D('SchoolManager', 'Model')->getOne($config);
        if ($info) {
            $this->error = '已经是管理员';
            return false;
        }

        if ($data['sm_id']) {
            return D('SchoolManager', 'Model')->update($save_data);
        } else {
            unset($data['sm_id']);
            $_POST['sm_id'] = D('SchoolManager', 'Model')->insert($save_data);
            return $_POST['sm_id'];
        }
    }
}