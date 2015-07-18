<?php
namespace Manage\Controller;
class NodeController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'n_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'n_title', 'percent' => '25', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'n_name', 'percent' => '25', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'n_sort', 'percent' => '10', 'title' => L('SORT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'n_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '8', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'n_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $action = C('ACTION_LIST');
        foreach ($action as $key => $value) {
            $actionList[$key] = $value['title'];
        }

        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'n_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'n_name', 'class' => 'w460');
        $addList[] = array('title' => L('LINK'), 'name' => 'n_url', 'class' => 'w460');
        $addList[] = array('title' => L('ATTRIBUTION'), 'name' => 'g_id', 'label' => 'select', 'data' => reloadCache('group'));
        $addList[] = array('title' => L('OPERATION'), 'name' => 'n_action[]', 'type' => 'checkbox', 'data' => $actionList, 'sign' => '&');
        $addList[] = array('title' => L('SORT'), 'name' => 'n_sort', 'class' => 'w460');
        $addList[] = array('title' => L('SHOW'), 'name' => 'n_show', 'label' => 'select', 'data' => array(1 => L('YES'), 9 => L('NO')));
        $addList[] = array('title' => L('STATUS'), 'name' => 'n_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'n_id', $title, $result);
    }

    public function insert() {

        $_POST['n_action'] = array_sum($_POST['n_action']);
        parent::insert();
        reloadCache('node', true);
    }

}