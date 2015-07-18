<?php
namespace Reagional\Controller;
class RoleGroupController extends ReagionalController {

    public function user() {

        if ($_POST) {
            // 接口方法
            $operation = strval($_POST['operation']) == 'del' ? 'delUser' : 'user';
            // 请求接口
            $res = $this->apiReturnDeal(getApi($_POST, 'RoleGroup', $operation));
            // 返回值
            echo json_encode($res);
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'User.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'User.css'));

            // 获取角色信息
            $vo = $this->apiReturnDeal(getApi(array('rg_id' => intval(I('id'))), 'RoleGroup', 'shows'));
            $this->assign('vo', $vo);

            // 获取角色用户
            $member_list = $this->apiReturnDeal(getApi(array('rg_id' => intval(I('id'))), 'RoleGroup', 'getUser'));
            $this->assign('member_list', $member_list);
            $this->display();
        }
    }

    public function authorization() {

        if ($_POST['rg_id']) {
            $result = $this->apiReturnDeal(getApi(array('rg_id' => I('rg_id', 0, 'intval'), 'pe_action' => I('pe_action', '', 'strval')), 'RoleGroup', 'setRelation'));

            $this->show($result);
        } else {

            $choose = $this->apiReturnDeal(getApi(array('rg_id' => I('id', 0, 'intval')), 'RoleGroup', 'getRelation'));

            $this->assign('action', C('ACTION_LIST'));
            $this->assign('shows', $this->groups);
            $this->assign('choose', json_encode($choose));
            $this->assign('rg_id', intval($_GET['id']));
            $this->display();
        }
    }

    public function index() {

        // 显示字段
        $tplList['id'] = array('id' => 'rg_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rg_title', 'percent' => '50', 'title' => L('NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rg_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '24', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));
        $action[] = array('id' => 'authorization', 'percent' => '8', 'title' => L('AUTHORIZE'));
        $action[] = array('id' => 'user', 'percent' => '8', 'title' => L('USERS_LIST'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'rg_title');

        // 工具
        $tools = array('add', 'edit', 'del');
        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 区域后台
        $_POST['rg_type'] = C('BACKGROUND_TYPE');

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'RoleGroup', 'lists'));
        
        echo json_encode($data);
    }

    public function addTpl($title = '', $result = array()) {

        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'rg_title', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'rg_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'rg_id', $title, $result);
    }

    public function edit() {
        parent::edit('rg_id');
    }

    public function delete() {
        parent::delete('rg_id');
    }

    // 默认写入操作
    public function insert() {

        $apiFunction = intval($_POST['rg_id']) ? 'edit' : 'add';

        // 区域后台
        $_POST['rg_type'] = C('BACKGROUND_TYPE');

        // 处理接口返回信息
        $data = $this->apiReturnDeal(getApi($_POST, 'RoleGroup', $apiFunction));

        // 提示跳转
        $this->show($data);
    }

}