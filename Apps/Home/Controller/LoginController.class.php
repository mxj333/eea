<?php
namespace Home\Controller;
class LoginController extends BaseController {
    public function verify() {
        D('Public')->verify(5);
    }

    // 登录检测
    public function checkLogin() {

        if (empty($_POST['me_account']) || empty($_POST['me_password'])) {
            echo json_encode(array('status' => 0, 'info' => L('THE_ACCOUNT_PASSWORD_CAN_NOT_BE_EMPTY')));
            exit;
        }
        if (C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ) && !D('Public')->checkVerify($_POST['me_verify'], 5)) {
            echo json_encode(array('status' => 0, 'info' => L('VERIFICATION_CODE_ERROR')));
            exit;
        }

        // 用户信息
        $user = $this->apiReturnDeal(getApi(array('account' => $_POST['me_account'], 'password' => $_POST['me_password']), 'Member', 'check'));
        if (isset($user['status']) && !$user['status']) {
            echo json_encode(array('status' => 0, 'info' => $user['info']));
            exit;
        }

        $_SESSION[C('USER_AUTH_KEY')]   = $user['me_id'];
        $_SESSION['me_nickname']         = $user['me_nickname'];

        // 更新状态  不需要返回值
        $slsData['me_id'] = $user['me_id'];
        $slsData['login_ip'] = rewrite_ip2long(get_client_ip());
        $user = getApi($slsData, 'Member', 'saveLoginStatus');
        $this->success(L('LOGIN_IS_SUCCESSFUL'));
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