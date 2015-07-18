<?php
namespace Manage\Controller;
class PermissionsController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'pe_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'pe_type', 'percent' => '10', 'title' => L('TYPE'));
        $tplList[] = array('id' => 'pe_title', 'percent' => '20', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'pe_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'pe_sort', 'percent' => '10', 'title' => L('SORT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'pe_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('SUB_SECTION'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'pe_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'pe_pid');

        // 工具
        $tools = array('add', 'edit', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function insert() {
        $_POST['pe_action'] = intval(array_sum($_POST['pe_action']));
        $this->assign('waitSecond', 0);
        $this->show(D('Permissions')->data(), '', __CONTROLLER__ . '/index/pe_pid/' . $_POST['pe_pid']);
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_REQUEST['pe_id']);
        if (isset($_GET['pe_id'])) {
            $result['pe_pid'] = $id;
            $result['pe_level'] = 0;
        }
        if ($id) {
            $permissionsConfig['where']['pe_id'] = $id;
            $permissionsConfig['fields'] = 'pe_level';
            $pe_level = D('Permissions')->getOne($permissionsConfig);
            $result['pe_level'] = intval($pe_level) + 1;
        }
        
        $member_node_type = explode(',', C('PERMISSIONS_TYPE'));
        unset($member_node_type[0]);

        $action = C('ACTION_LIST');
        foreach ($action as $key => $value) {
            $actionList[$key] = $value['title'];
        }

        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'pe_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'pe_name', 'class' => 'w460');
        $addList[] = array('title' => L('LINK'), 'name' => 'pe_url', 'class' => 'w460');
        $addList[] = array('title' => L('TYPE'), 'name' => 'pe_type', 'label' => 'select', 'data' => $member_node_type);
        $addList[] = array('title' => L('OPERATION'), 'name' => 'pe_action[]', 'type' => 'checkbox', 'data' => $actionList, 'sign' => '&');
        $addList[] = array('title' => L('SORT'), 'name' => 'pe_sort', 'class' => 'w460');
        $addList[] = array('title' => L('SHOW'), 'name' => 'pe_show', 'label' => 'select', 'data' => array(1 => L('YES'), 9 => L('NO')));
        $addList[] = array('title' => L('STATUS'), 'name' => 'pe_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('title' => '', 'name' => 'pe_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'pe_level', 'type' => 'hidden');
        return generationAddTpl($addList, 'pe_id', $title, $result);
    }
}