<?php
namespace Area\Controller;
use Think\Controller;
class AreaController extends Controller {
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
        
        // 获取 url 参数
        $param = I('request.param');
        $this->current_param = parseUrlParam($param, ACTION_NAME, CONTROLLER_NAME);
        define('CURRENT_UUID', I('request.uuid', '0', 'intval'));
        // 获取地区信息
        $this->current_region_info = $this->apiReturnDeal(getApi(array('re_id' => CURRENT_UUID), 'Region', 'shows'), '/');
        
        // 定义区域根目录
        $this->assign('area_module', '/' . MODULE_NAME . '/' . CURRENT_UUID);
        $this->assign('empty_array', array());

        C('DEFAULT_THEME', 'default');

        $this->friendlyLink = $this->apiReturnDeal(getApi(array('re_id' => $this->current_region_info['re_ids']), 'FriendlyLink', 'getShows'));
    }

    // api 接口返回值处理
    public function apiReturnDeal($data, $jumpUrl = '') {

        // 接口请求错误
        if (isset($data['errCode'])) {
            $this->error($data['errMessage'], $jumpUrl);
            exit;
        }

        // 接口内容返回错误
        if (isset($data['status']) && !$data['status']) {
            $this->error($data['info'], $jumpUrl);
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

    // 生成静态文件
    public function autoHtml($template = '',$data = array(), $function = 'index', $controller = 'index', $module = null, $uuid = null, $page = 1) {
        if (C('IS_OPEN_HTML_CACHE')) {
            $this->buildHtml(getUrlAddress($data, $function, $controller, $module, $uuid, $page), '', $template);
        }
    }

    public function readCacheHtml($data = array(), $function = 'index', $controller = 'index', $module = '', $uuid = '', $page = 1) {
        $html = getUrlAddress($data, $function, $controller, $module, $uuid, $page);
        if (file_exists('./htm' . $html)) {
            return './htm' . $html;
        }

        return '';
    }
}