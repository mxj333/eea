<?php
namespace Manage\Controller;
class TemplateTypeController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'tt_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'tt_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'tt_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '10', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'tt_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'tt_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'tt_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'tt_id', $title, $result);
    }
}