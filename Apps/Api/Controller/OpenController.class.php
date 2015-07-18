<?php
namespace Api\Controller;
use Think\Controller;
class OpenController extends Controller {

    protected $errCode = array();
    protected $authInfo = array();

    // 初始化
    public function _initialize() {

        cacheData();
        $this->errCode = C('ERROR_CODE');

        // 需要对汉字做URL_DECODE处理的参数名
        $haveUrlDecode = C('HAVE_URL_DECODE');
        $haveMagicQuotes = C('HAVE_MAGIC_QUOTES');

        foreach ($_POST['args'] as $key => $value) {
            if (in_array($key, $haveUrlDecode)) {
                $_POST['args'][$key] = urldecode($value);
            }
        }
        
        // 需要验证SESSION的方法;
        if (in_array(trim($_POST['method']), C('VERIFY_METHOD')) || in_array(strtolower(ACTION_NAME), C('VERIFY_CONTROLLER'))) {

            // 若未传递skey
            if (!$_POST['skey'] || !$_POST['args']['me_id']) {
                $this->returnData($this->errCode[2]);
            }

            $_POST['skey'] = urldecode($_POST['skey']);
            if ($this->apiLogin($_POST['skey']) != intval($_POST['args']['me_id'])) {
                $this->returnData($this->errCode[4]);
            }
        }

        $params = initParams($_POST['method'], $_POST['args'], $_POST['ts'], $_POST['skey'], $_POST['format']);

        if ($_POST['sign'] != md5(generateSign($params))) {
            $this->returnData($this->errCode[1]);
        }

        foreach ($_POST['args'] as $key => $value) {
            if (in_array($key, $haveMagicQuotes)) {
                $_POST['args'][$key] = myAddSlashes($_POST['args'][$key]);
            }
        }

        /*
        $_POST['method'] = 'Auth.login';
        $_POST['args']['username'] = '654321';
        $_POST['args']['password'] = '654321';
        $_POST['ts'] = time();
        $_POST['format'] = 'JSON';

        $params = initParams($_POST['method'], $_POST['args'], $_POST['ts'], $skey);
        $params['sign'] = md5(generateSign($params));
        echo buildUrl($params);
        */
    }

    // 登录
    protected function apiLogin($skey) {
        $me_id = intval(passport_decrypt(urldecode($skey), ILC_ENCRYPT_KEY));
        $user['where']['me_is_deleted'] = 9;
        $user['where']['me_status'] = 1;
        $user['where']['me_id'] = $me_id;
        $this->authInfo = D('Member')->getOne($user);
        return $me_id;
    }

    // 默认写入操作
    public function insert($table = '') {
        $table = empty($table) ? CONTROLLER_NAME : $table;
        $this->show(D($table)->data($data));
    }

    // 默认显示操作
    public function show($result) {
        $this->returnData($result);
    }

    public function returnData($data) {
        $defaultAjaxReturn = strtolower($_POST['format']) == 'json' ? 'json' : 'xml';
        $this->ajaxReturn($data, $defaultAjaxReturn);
    }
}
?>