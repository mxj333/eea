<?php
namespace Common\Logic;
class AreaManagerLogic extends Logic {

    public function check($me_id = '', $aty_id = 1) {

        $member['where']['am_status'] = 1;
        $member['where']['aty_id'] = intval($aty_id);
        $member['where']['me_id'] = intval($me_id);
        return D($this->name, 'Model')->getOne($member);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default['is_deal_result'] = true;
        $default['p'] = intval($param['p']);

        // 通过账号  名称 搜管理员 需要连表
        if ($param['me_account'] || $param['me_nickname']) {
            $am_alias = 'am.';
            $me_alias = 'me.';
            $default['table'][C('DB_PREFIX').'area_manager'] = substr($am_alias, 0, -1);
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'member ' . substr($me_alias, 0, -1) . ' ON ' . $am_alias . 'me_id = ' . $me_alias . 'me_id';
        }
        
        if ($param['aty_id']) {
            $default['where'][$am_alias . 'aty_id'] = $param['aty_id'];
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where'][$am_alias . 're_id'] = array('IN', $region);
        }

        if (isset($param['am_is_main'])) {
            $default['where'][$am_alias . 'am_is_main'] = intval($param['am_is_main']);
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('AreaManager')->getListByPage($config);

        if ($config['is_deal_result']) {

            $appCategory = reloadCache('appType');

            foreach ($lists['list'] as $key => $value) {
                if ($value['am_status']) {
                    $lists['list'][$key]['am_status'] = getStatus($value['am_status']);
                }
                if ($value['aty_id']) {
                    $lists['list'][$key]['aty_title'] = $appCategory[$value['aty_id']];
                }
                if ($value['me_id']) {
                    $member_config['where']['me_id'] = $value['me_id'];
                    $member = D('Member', 'Model')->getOne($member_config);
                    $lists['list'][$key]['me_nickname'] = $member['me_nickname'];
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id, $config = array()) {

        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);

        // 区域管理员关系信息
        $area_config['where']['am_id'] = intval($id);
        $area_manage = D('AreaManager', 'Model')->getOne($area_config);

        // 用户信息
        if ($config['is_deal_result']) {
            $member_config['where']['me_id'] = $area_manage['me_id'];
            $member = D('Member', 'Model')->getOne($member_config);
            $area_manage['me_nickname'] = $member['me_nickname'];

            // 应用分类
            $appCategory = reloadCache('appType');
            $area_manage['aty_title'] = $appCategory[$area_manage['aty_id']];
        }

        return $area_manage;
    }

    public function insert($data) {
        $save_data = D('AreaManager', 'Model')->create($data);
        if (false === $save_data) {
            $this->error = D('AreaManager', 'Model')->getError();
            return false;
        }
        // 主管理员 验证唯一
        if ($save_data['am_is_main'] == 1) {
            if ($data['am_id']) {
                $config['where']['am_id'] = array('NEQ', $data['am_id']);
            }
            $config['where']['aty_id'] = $data['aty_id'];
            $config['where']['re_id'] = $data['re_id'];
            $config['where']['am_is_main'] = 1;
            $info = D('AreaManager', 'Model')->getOne($config);
            if ($info) {
                $this->error = '区域内已指定管理员';
                return false;
            }
        }

        if ($data['am_id']) {
            return D('AreaManager', 'Model')->update($save_data);
        } else {
            unset($data['am_id']);
            $_POST['am_id'] = D('AreaManager', 'Model')->insert($save_data);
            return $_POST['am_id'];
        }
    }
}