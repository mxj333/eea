<?php
namespace Common\Logic;
class RoleLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'r_id ASC',
            'p' => intval($param['p']),
        );

        // 组织查询条件
        if ($param['r_title']) {
            $default['where']['r_title'] = array('like', '%' . $param['r_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            if ($value['r_pid']) {
                $roleConfig['where']['r_id'] = $value['r_pid'];
                $roleConfig['fields'] = 'r_title';
                $r_title = D('Role', 'Model')->getOne($roleConfig);
                $lists['list'][$key]['r_ptitle'] = $r_title;
            } else {
                $lists['list'][$key]['r_ptitle'] = '无';
            }

            if ($value['r_status']) {
                $lists['list'][$key]['r_status'] = getStatus($value['r_status']);
            }
        }

        // 输出数据
        return $lists;

    }

    public function user($r_id) {
        // 用户列表
        $userConfig['where']['u_id'] = array('neq', 1);
        $userConfig['fields'] = 'u_id,u_nickname';
        $res['users'] = D('User', 'Model')->getAll($userConfig);

        // 获取当前用户组信息
        $roleUserConfig['where']['r_id'] = $r_id;
        $roleUserConfig['fields'] = 'u_id';
        $res['roleUser'] = D('RoleUser', 'Model')->getAll($roleUserConfig);

        $roleConfig['where']['r_id'] = $r_id;
        $res['role'] = D('Role', 'Model')->getOne($roleConfig);
        return $res;
    }

    public function saveUser($r_id, $roleUser) {

        // 删除组用户
        $roleUserConfig['where']['r_id'] = $r_id;
        D('RoleUser', 'Model')->delete($roleUserConfig);

        if ($roleUser) {

            $data = array();
            foreach ($roleUser as $key => $value) {
                $add[] = array('r_id' => $r_id, 'u_id' => $value);
            }
            $config['fields'] = 'r_id,u_id';
            $config['values'] = $add;

            return D('RoleUser', 'Model')->insertAll($config);
        }
    }

    public function saveRole($r_id, $n_action) {

        // 删除组用户
        $accessConfig['where']['r_id'] = $r_id;
        D('Access', 'Model')->delete($accessConfig);

        if ($n_action) {
            $data = array();
            $action = explode(',', $n_action);
            foreach ($action as $key => $value) {
                $tmp = explode('-', $value);
                $add[] = array('r_id' => $r_id, 'n_id' => $tmp[0], 'n_action' => $tmp[1]);
            }
            $config['fields'] = 'r_id,n_id,n_action';
            $config['values'] = $add;

            return D('Access', 'Model')->insertAll($config);
        }
    }
}
?>