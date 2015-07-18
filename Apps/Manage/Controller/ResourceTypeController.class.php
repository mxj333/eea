<?php
namespace Manage\Controller;
class ResourceTypeController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rt_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rt_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_name', 'percent' => '10', 'title' => L('NAME'));
        $tplList[] = array('id' => 'rt_exts', 'percent' => '30', 'title' => L('EXTS'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rt_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'rt_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'rt_name', 'title' => L('NAME'), 'class' => 'w460');
        $addList[] = array('name' => 'rt_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'rt_exts', 'title' => L('EXTS'), 'label' => 'textarea', 'class' => 'h80');

        return generationAddTpl($addList, 'rt_id', $title, $result);
    }
}