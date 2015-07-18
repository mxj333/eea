<?php
namespace Manage\Controller;
class GroupController extends ManageController {

    public function insert() {
        $res = $this->data('Group', $_POST);
        reloadCache('group', true);
        $this->show($res);
    }

    public function delete() {
        $res = $this->deleteData('Group');
        reloadCache('group', true);
        $this->show($res);
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'g_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'g_title', 'percent' => '25', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'g_name', 'percent' => '25', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'g_sort', 'percent' => '10', 'title' => L('SORT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'g_status', 'percent' => '12', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '16', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '8', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'g_title');

        // 工具
        $tools = array('add', 'edit');
        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'g_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'g_name', 'class' => 'w460');
        $addList[] = array('title' => L('LINK'), 'name' => 'g_url', 'class' => 'w460');
        $addList[] = array('title' => L('SORT'), 'name' => 'g_sort', 'class' => 'w460');
        $addList[] = array('title' => L('SHOW'), 'name' => 'g_show', 'label' => 'select', 'data' => array(1 => L('YES'), 9 => L('NO')));
        $addList[] = array('title' => L('STATUS'), 'name' => 'g_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'g_id', $title, $result);
    }
}