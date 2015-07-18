<?php
namespace Common\Logic;
class UserLogic extends Logic {

    public function check($account = '') {

        $config['where']['u_status'] = array('neq', 9);
        $config['where']['u_account'] = strval($account);
        return D($this->name, 'Model')->getOne($config);
    }

    public function saveLoginStatus($u_id, $login_count = 0) {

        if (!$u_id) {
            return;
        }

        $config['u_last_login_time'] = time();
        $config['u_login_count'] = $login_count + 1;
        $config['u_last_login_ip'] = rewrite_ip2long(get_client_ip());
        $config['u_id'] = $u_id; 
        D($this->name, 'Model')->update($config);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $where['u_id'] = array('neq', 1);

        // 组织查询条件
        if ($param['u_account']) {
            $where['u_account'] = array('like', '%' . $param['u_account'] . '%');
        }

        if ($param['u_nickname']) {
            $where['u_nickname'] = array('like', '%' . $param['u_nickname'] . '%');
        }

        $default = array(
            'where' => $where,
            'order' => 'u_id DESC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            $lists['list'][$key]['u_last_login_ip'] = long2ip($value['u_last_login_ip']);
            $lists['list'][$key]['u_last_login_time'] = $value['u_last_login_time'] ? date('Y-m-d H:i:s', $value['u_last_login_time']) : '';
        }

        // 输出数据
        return $lists;
    }
}
?>