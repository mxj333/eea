<?php
namespace Member\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize() {

        // 黑名单验证
        if (is_safe_ip()) {
            // 在黑名单内  跳转到404页
            header("HTTP/1.1 404 Not Found");  
            header("Status: 404 Not Found");  
            exit;
        }

        if (!file_exists('./install.log')) {
            redirect('/Install/Index');
        }
        
        cacheData();
        C('DEFAULT_THEME', C('SCHOOL_DEFAULT_THEME'));
    }

    // api 接口返回值处理
    public function apiReturnDeal($data) {

        // 接口请求错误
        if (isset($data['errCode'])) {
            $this->error($data['errMessage']);
            exit;
        }

        // 接口内容返回错误
        if (isset($data['status']) && !$data['status']) {
            $this->error($data['info']);
            exit;
        }

        // 接口内容正确
        return $data;
    }

    // 首页
    public function index($html = '') {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . '.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));

        $html = $html ? $html : CONTROLLER_NAME . '/index';

        $this->display($html);
    }
}