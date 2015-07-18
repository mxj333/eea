<?php
namespace Manage\Controller;
class AdvertTypeController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'at_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'at_title', 'percent' => '30', 'title' => L('NAME'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '16', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '16', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'at_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'at_title', 'class' => 'w460');
        $addList[] = array('title' => L('DESCRIPTION'), 'name' => 'at_description', 'label' => 'textarea', 'class' => 'h80');
        return generationAddTpl($addList, 'at_id', $title, $result);
    }
}