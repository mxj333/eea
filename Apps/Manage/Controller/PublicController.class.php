<?php
namespace Manage\Controller;
class PublicController extends ManageController {

    public function verify() {
        D('Public')->verify();
    }

    // 登录检测
    public function checkLogin() {

        if (empty($_POST['u_account']) || empty($_POST['u_password'])) {
            $this->error(L('THE_ACCOUNT_PASSWORD_CAN_NOT_BE_EMPTY'), '');
        }

        if (C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ) && !D('Public')->checkVerify($_POST['u_verify'])) {
            $this->error(L('VERIFICATION_CODE_ERROR'), '', 'verify');
        }

        $user = D('User')->check($_POST['u_account']);

        // 使用用户名、密码和状态的方式进行认证
        if (!$user) {
            $this->error(L('THE_ACCOUNT_DOSE_NOT_EXITST_OR_HAS_BENN_DISABLED'), '');
        }

        if ($user['u_password'] != pwdHash($_POST['u_password'])) {
            $this->error(L('PASSWORD_ERROE'), '');
        }

        $_SESSION[C('USER_AUTH_KEY')]   = $user['u_id'];
        $_SESSION['uNickName']          = $user['u_nickname'];
        $_SESSION['uLastLoginTime']     = $user['u_last_login_time'];
        $_SESSION['uLoginCount']        = $user['u_login_count'];

        if ($user['u_account'] == 'root') {
            $_SESSION['administrator']  = true;
        } else {
            // 缓存访问权限
            D('Rbac')->saveAccessList();
        }

        D('User')->saveLoginStatus($user['u_id'], $user['u_login_count']);
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