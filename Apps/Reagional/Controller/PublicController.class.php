<?php
namespace Reagional\Controller;
class PublicController extends ReagionalController {

    public function verify() {
        D('Public')->verify(2);
    }

    // 登录检测
    public function checkLogin() {

        if (empty($_POST['me_account']) || empty($_POST['me_password'])) {
            $this->error(L('THE_ACCOUNT_PASSWORD_CAN_NOT_BE_EMPTY'), '');
        }
        
        if (C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ) && !D('Public')->checkVerify($_POST['me_verify'], 2)) {
            $this->error(L('VERIFICATION_CODE_ERROR'), '', 'verify');
        }

        // 区域管理员账号信息
        $user = $this->apiReturnDeal(getApi(array('account' => $_POST['me_account'], 'password' => $_POST['me_password'], 'aty_id' => 1), 'AreaManager', 'check'));
        if (isset($user['status']) && !$user['status']) {
            $this->error($user['info'], '');
        }

        $_SESSION[C('USER_AUTH_KEY')]   = $user['me_id'];
        $_SESSION['meNickName']          = $user['me_nickname'];
        $_SESSION['meLastLoginTime']     = $user['me_last_login_time'];
        $_SESSION['meLoginCount']        = $user['me_login_count'];
        $_SESSION['re_id'] = $user['re_id'];
        $_SESSION['re_title'] = $user['re_title'];

        if ($user['am_is_main'] == 1) {
            $_SESSION[C('ADMIN_AUTH_KEY')]  = true;
        } else {
            // 缓存访问权限
            D('Rbac')->saveAccessList();
        }

        // 更新状态  不需要返回值
        $slsData['me_id'] = $user['me_id'];
        $slsData['count'] = $user['me_login_count'];
        $slsData['login_ip'] = rewrite_ip2long(get_client_ip());
        $user = getApi($slsData, 'Member', 'saveLoginStatus');
        $this->success(L('LOGIN_IS_SUCCESSFUL'));
    }

    public function login() {
        if (!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->verifyed = intval(C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ));
            $this->display();
        } else {
            redirect(__MODULE__.'/');
        }
    }

    // 退出
    public function logout() {
        if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION['loginId']);
            unset($_SESSION[C('USER_AUTH_KEY')]);
            session_destroy();
        }
        redirect(__MODULE__.'/');
    }
}