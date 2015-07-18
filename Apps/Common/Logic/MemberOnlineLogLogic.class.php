<?php
namespace Common\Logic;
class MemberOnlineLogLogic extends Logic {

    // 在线人数统计
    public function onlineNumber() {
        // 定义在线人数时长 15分钟  900秒
        $min_time = time() - 900;

        $config['fields']['count(mol_id)'] = 'num';
        $config['where']['mol_is_login'] = 1;
        $config['where']['mol_visit_time'] = array('GT', $min_time);
        // 在线登录人数
        $login_num = D('MemberOnlineLog')->getOne($config);

        // 在线未登录人数
        $config['where']['mol_is_login'] = 0;
        $logout_num = D('MemberOnlineLog')->getOne($config);

        return array('login_num' => intval($login_num), 'logout_num' => intval($logout_num));
    }

    // 添加
    public function insert($config = array()) {
        $default['mol_is_login'] = $_SESSION[C('USER_AUTH_KEY')] ? 1 : 0;
        $default['me_id'] = $_SESSION[C('USER_AUTH_KEY')] ? $_SESSION[C('USER_AUTH_KEY')] : 0;
        $default['mol_visit_time'] = time();
        $default['mol_visit_url'] = __SELF__;
        $default['mol_from_ip'] = rewrite_ip2long(get_client_ip());
        
        $config = array_merge($default, $config);

        if ($config['mol_is_login']) {
            $logConfig['fields'] = 'mol_id';
            $logConfig['where']['me_id'] = $config['me_id'];
            $mol_id = D('MemberOnlineLog', 'Model')->getOne($logConfig);
        } else {
            $logConfig['fields'] = 'mol_id';
            $logConfig['where']['mol_from_ip'] = $config['mol_from_ip'];
            $mol_id = D('MemberOnlineLog', 'Model')->getOne($logConfig);
        }

        if ($mol_id) {
            $config['mol_id'] = $mol_id;
            return D('MemberOnlineLog', 'Model')->update($config);
        } else {
            return D('MemberOnlineLog', 'Model')->insert($config);
        }
    }
}