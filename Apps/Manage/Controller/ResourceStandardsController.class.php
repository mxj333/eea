<?php
namespace Manage\Controller;
class ResourceStandardsController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rst_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rst_title', 'percent' => '40', 'title' => L('TITLE'));
        $tplList[] = array('id' => 'rst_status', 'percent' => '30', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rst_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'rst_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'rst_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'rst_id', $title, $result);
    }
}