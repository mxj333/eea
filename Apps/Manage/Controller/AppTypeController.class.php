<?php
namespace Manage\Controller;
class AppTypeController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'aty_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'aty_title', 'percent' => '60', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '10', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'aty_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'aty_title', 'title' => L('TITLE'), 'class' => 'w460');

        return generationAddTpl($addList, 'aty_id', $title, $result);
    }
}