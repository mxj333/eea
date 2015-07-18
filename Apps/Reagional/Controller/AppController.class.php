<?php
namespace Reagional\Controller;
class AppController extends ReagionalController {
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . '.css'));

        $this->assign('appCategory', reloadCache('appCategory'));
        $this->assign('ac_id', I('ac_id', 1, 'intval'));
        $this->assign('list', $list);
        $this->display();
    }

    public function lists() {
        // 应用列表
        $_POST['ac_id'] = I('ac_id', 0, 'intval');
        $_POST['aty_id'] = 1;
        $_POST['is_deal_result'] = true;
        $_POST['return_num'] = 28;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'App', 'lists'));
        
        echo json_encode($data);
    }
}