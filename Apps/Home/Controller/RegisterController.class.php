<?php
namespace Home\Controller;
class RegisterController extends BaseController {
    public function index() {
        
        if (session('me_nickname')) {
            //已经登录不用再注册
            $this->error('用户已有账号', '/');
        }

        // 是否开启验证码
        $this->verifyed = intval(C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ));

        parent::index();
    }

    // 注册完成页面
    public function complete() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Complete.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        
        $this->assign('me_info', array('me_id' => session(C('USER_AUTH_KEY')), 'me_nickname' => session('me_nickname')));
        $this->display();
    }

    // 验证码
    public function verify() {
        // 前台注册
        D('Public')->verify(4);
    }

    // 注册用户
    public function register() {

        if (!$_POST['is_agree']) {
            echo json_encode(array('status' => 0, 'info' => L('IS_AGREE_ERROR')));
            exit;
        }

        // 验证码验证
        if (C('IS_STARTED_VERIFY') && (C('START_VERIFY_TYPE') & 1 ) && !D('Public')->checkVerify($_POST['me_verify'], 4)) {
            echo json_encode(array('status' => 0, 'info' => L('VERIFICATION_CODE_ERROR')));
            exit;
        }

        // 密码验证
        if ($_POST['me_password'] != $_POST['repassword']) {
            echo json_encode(array('status' => 0, 'info' => L('PASSWORD_NO_EQ')));
            exit;
        }

        // 添加用户
        $registerInfo = $this->apiReturnDeal(getApi($_POST, 'Member', 'add'));
        if (!$registerInfo['status']) {
            echo json_encode(array('status' => 0, 'info' => L('REGISTER_FAIL')));
            exit;
        }

        // 获取用户信息
        $user = $this->apiReturnDeal(getApi(array('me_id' => $registerInfo['res_value']), 'Member', 'shows'));
        $_SESSION[C('USER_AUTH_KEY')]   = $user['me_id'];
        $_SESSION['me_nickname']         = $user['me_nickname'];

        // 更新状态  不需要返回值
        $slsData['me_id'] = $user['me_id'];
        $slsData['login_ip'] = rewrite_ip2long(get_client_ip());
        getApi($slsData, 'Member', 'saveLoginStatus');
        // 跳转注册成功页面
        $this->success(L('REGISTER_IS_SUCCESSFUL'), getUrlAddress(array(), 'complete', 'register'));
    }
}