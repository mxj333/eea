<?php
namespace Manage\Controller;
class RoleController extends ManageController {

    public function user() {

        if (intval($_POST['r_id'])) {
            D('Role')->saveUser(intval($_POST['r_id']), $_POST['roleUser']);
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->assign('res', D('Role')->user(intval($_GET['id'])));
            $this->display();
        }
    }

    public function authorization() {

        if ($_POST['r_id']) {
            D('Role')->saveRole(intval($_POST['r_id']), strval($_POST['n_action']));
            $this->success(L('OPERATION_SUCCESS'));
        } else {

            $accessConfig['where']['r_id'] = intval($_GET['id']);
            $accessConfig['fields'] = 'n_id,n_action';
            $choose = D('Access')->getAll($accessConfig);

            $this->assign('action', C('ACTION_LIST'));
            $this->assign('shows', $this->groups);
            $this->assign('choose', json_encode($choose));
            $this->assign('r_id', intval($_GET['id']));
            $this->display();
        }
    }

    public function index() {

        // 显示字段
        $tplList['id'] = array('id' => 'r_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'r_title', 'percent' => '40', 'title' => L('NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'r_status', 'percent' => '15', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '24', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));
        $action[] = array('id' => 'authorization', 'percent' => '8', 'title' => L('AUTHORIZE'));
        $action[] = array('id' => 'user', 'percent' => '8', 'title' => L('USERS_LIST'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'r_title');

        // 工具
        $tools = array('add', 'edit', 'del');
        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'r_title', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'r_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'r_id', $title, $result);
    }

}