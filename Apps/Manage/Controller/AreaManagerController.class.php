<?php
namespace Manage\Controller;
class AreaManagerController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'am_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 're_title', 'percent' => '20', 'title' => L('REGION'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aty_title', 'percent' => '20', 'title' => L('APP_TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '20', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'am_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        //$search[] = array();

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 获取地区
    public function getRegion() {
        $region = reloadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }

    // 获取用户
    public function getUser() {
        $name = I('name', '', 'strval');
        $re_id = I('re_id', '', 'strval');
        if ($name) {
            $config['where']['re_id'] = $re_id;
            $config['where']['me_nickname'] = array('LIKE', '%' . $name . '%');
            $config['fields'] = 'me_id,me_account,me_nickname';
            $this->ajaxReturn(D('Member')->getAll($config));
        }
    }

    // 默认新增操作
    public function add() {

        if (!$_POST) {
            // 区域类型
            $appCategoryData = loadCache('appType');
            $this->assign('aty_list', $appCategoryData);
        } else {
            // 主管理员
            $_POST['am_is_main'] = 1;
        }

        parent::add('AreaManager/add');
    }

    // 默认编辑操作
    public function edit() {
        if ($_POST) {
            // 主管理员
            $_POST['am_is_main'] = 1;
        } else {
            // 应用类型
            $appCategoryData = reloadCache('appType');
            $this->assign('aty_list', $appCategoryData);
        }

        parent::edit('AreaManager/add');
    }

    public function insert() {
        $result = D('AreaManager')->insert($_POST);
        if ($result === false) {
            $this->error(D('AreaManager')->getError());
        }

        $this->show($result);
    }
}