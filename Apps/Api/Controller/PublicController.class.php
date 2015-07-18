<?php
namespace Api\Controller;
use Think\Controller;
class PublicController extends OpenController {

    // 初始化接口
    public function init() {
        extract($_POST['args']);
        $result['mtime'] = filemtime('./Runtime/Data/~config.php');

        if ($mtime < $result['mtime']) {
            $result['config'] = reloadCache('config');
        }

        $this->returnData($result);
    }

     // 登录
    public function login() {

        extract($_POST['args']);

        // 接收参数
        if (empty($username) || empty($password)) {
            $this->ajaxReturn($this->errCode[2]);
            exit;
        }

        // LOCAL本地登录，CA单点登录，
        $loginStatus = C('LOGIN_STATUS');

        // CA单点登录，
        if ($loginStatus == 'CA'){

            // TO DO 请求CA接口
            //if (CaLogin($username, $password) == 0) {
            //    $this->ajaxReturn($this->errCode[5]);
            //}

            //$caData = CaAttributes($username);
            //$info['a_nickname'] = $caData['cn'];
            //$username = $caData['useridcode'];
        }

        $result = D('Member')->check($username);

        // LOCAL本地登录，
        if ($loginStatus == 'LOCAL'){

            if ($result['me_password'] != pwdHash($password)) {
                $this->ajaxReturn($this->errCode[5]);
            }
        }

        // 组织数据
        $info['me_last_login_time'] = time();
        $info['me_last_login_ip'] = rewrite_ip2long(get_client_ip());
        $info['me_login_count'] = $result['me_login_count'] + 1;

        if (!$result && $loginStatus != 'LOCAL') {
            // TO DO 没有的账号加到系统
            //$info['a_account'] = $account;
            //$info['a_password'] = md5($password);
            //$info['a_register_ip'] = rewrite_ip2long(get_client_ip());
            //$data['auth_id'] = M('Auth')->add($info);;
        } else {
            // 通过验证后 检测本地是否可登录
            if ($result['me_status'] == 9 || $result['me_is_deleted'] == 1) {
                $this->ajaxReturn($this->errCode[5]);
            }

            if ($result['me_validity'] && $result['me_validity'] < time()) {
                $this->ajaxReturn($this->errCode[8]);
            }

            $memberConfig['where']['me_id'] = $result['me_id'];
            D('Member')->update($info, $memberConfig);

            $skey = urlencode(setPassportId($result['me_id']));
            $data['auth_name'] = $result['me_nickname'];
            $data['auth_id'] = $result['me_id'];
            $data['auth_img'] = D('Member')->getAvatar($result['me_avatar']);
            $data['skey'] = $skey;
            $data['s_id'] = $result['s_id'];
            $data['push_ip'] = C('OPENFIRE_IP');
        }

        $this->ajaxReturn($data);
    }

    //用户注销
    public function logout(){

        session_destroy();
        setPassportId(NULL);
        $this->ajaxReturn(array('status' => 1));
    }

}
?>
