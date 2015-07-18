<?php
namespace Manage\Controller;
class AppCategoryController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ac_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ac_title', 'percent' => '40', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ac_status', 'percent' => '20', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '20', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'ac_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'ac_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'ac_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 'ac_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        if (!$result) {
            $result['ac_sort'] = 255;
        }
        return generationAddTpl($addList, 'ac_id', $title, $result);
    }
}