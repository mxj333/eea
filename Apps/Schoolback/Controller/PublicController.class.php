<?php
namespace Schoolback\Controller;
class PublicController extends SchoolbackController {

    public function verify() {
        D('Public')->verify(3);
    }

    // 登录检测
    public function checkLogin() {

        if (empty($_POST['me_account']) || empty($_POST['me_password'])) {
            $this->error(L('THE_ACCOUNT_PASSWORD_CAN_NOT_BE_EMPTY'), '');
        }
        if (C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ) && !D('Public')->checkVerify($_POST['me_verify'], 3)) {
            $this->error(L('VERIFICATION_CODE_ERROR'), '', 'verify');
        }

        // 是否为校级管理员
        $user = $this->apiReturnDeal(getApi(array('account' => $_POST['me_account'], 'password' => $_POST['me_password']), 'SchoolManager', 'check'));
        if (isset($user['status']) && !$user['status']) {
            $this->error($user['info'], '');
        }

        $_SESSION[C('USER_AUTH_KEY')]   = $user['me_id'];
        $_SESSION['sNickName']         = $user['me_nickname'];
        $_SESSION['sLoginCount']       = $user['me_login_count'];
        $_SESSION['s_id']               = $user['s_id'];
        $_SESSION['s_re_id']               = $user['re_id'];
        $_SESSION['s_re_title']               = $user['re_title'];
        $_SESSION['s_title']               = $user['s_title'];

        if ($user['is_president']) {
            // 校长
            $_SESSION[C('ADMIN_AUTH_KEY')]  = true;
        } else {
            // TO DO 要做学校后台管理员管理功能，并将权限加进来
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