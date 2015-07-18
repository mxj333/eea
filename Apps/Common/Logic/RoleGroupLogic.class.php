<?php
namespace Common\Logic;
class RoleGroupLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'rg_id ASC',
            'p' => intval($param['p']),
        );

        // 组织查询条件
        if ($param['rg_title']) {
            $default['where']['rg_title'] = array('like', '%' . $param['rg_title'] . '%');
        }

        if ($param['rg_type']) {
            $default['where']['rg_type'] = $param['rg_type'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('RoleGroup')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {

                if ($value['rg_status']) {
                    $lists['list'][$key]['rg_status'] = getStatus($value['rg_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function user($rg_id, $config = array()) {

        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);
        
        // 获取当前角色用户
        $roleMemberConfig['where']['rg_id'] = $rg_id;
        $roleMemberConfig['fields'] = 'me_id';
        $roleMember = D('RoleMember', 'Model')->getAll($roleMemberConfig);
        // 不处理结果
        if (!$config['is_deal_result']) {
            return $roleMember;
        }

        $res = array();
        foreach ($roleMember as $member) {
            // 用户列表
            $memberConfig['where']['me_id'] = $member;
            $memberConfig['fields'] = 'me_nickname';
            $nickname = D('Member', 'Model')->getOne($memberConfig);
            $res[] = array(
                'me_id' => $member,
                'me_nickname' => $nickname,
            );
        }

        return $res;
    }

    // 多用户添加
    public function addUser($rg_id, $roleMember) {
        // 先删除所有用户
        $roleMemberConfig['where']['rg_id'] = $rg_id;
        $res = D('RoleMember', 'Model')->delete($roleMemberConfig);
        if ($res === false) {
            return false;
        }

        // 在添加选择用户
        if (!is_array($roleMember)) {
            $roleMember = explode(',', $roleMember);
        }

        foreach ($roleMember as $me_id) {
            $config[] = array(
                'rg_id' => intval($rg_id),
                'me_id' => intval($me_id),
            );
        }
        $insertData['fields'] = 'rg_id,me_id';
        $insertData['values'] = $config;
        return D('RoleMember', 'Model')->insertAll($insertData);
    }

    // 单个用户添加
    public function saveUser($rg_id, $roleMember) {

        $roleMemberConfig['where']['rg_id'] = $rg_id;
        $roleMemberConfig['where']['me_id'] = $roleMember;
        $info = D('RoleMember', 'Model')->getOne($roleMemberConfig);
        if ($info) {
            // 用户已经添加
            return true;
        }

        $config['rg_id'] = intval($rg_id);
        $config['me_id'] = intval($roleMember);
        return D('RoleMember', 'Model')->insert($config);
    }

    public function delUser($rg_id, $roleMember) {

        $roleMemberConfig['where']['rg_id'] = $rg_id;
        $roleMemberConfig['where']['me_id'] = $roleMember;
        return D('RoleMember', 'Model')->delete($roleMemberConfig);
    }

    public function saveRole($rg_id, $pe_action) {

        // 删除组用户
        $accessConfig['where']['rg_id'] = $rg_id;
        D('AccessMember', 'Model')->delete($accessConfig);

        if ($pe_action) {
            $data = array();
            $action = explode(',', $pe_action);
            foreach ($action as $key => $value) {
                $tmp = explode('-', $value);
                $add[] = array('rg_id' => $rg_id, 'pe_id' => $tmp[0], 'pe_action' => $tmp[1]);
            }
            $config['fields'] = 'rg_id,pe_id,pe_action';
            $config['values'] = $add;

            return D('AccessMember', 'Model')->insertAll($config);
        }
    }
}
?>