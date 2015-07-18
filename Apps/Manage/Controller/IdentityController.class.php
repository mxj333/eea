<?php
namespace Manage\Controller;
class IdentityController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'id_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'id_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'id_fields', 'percent' => '40', 'title' => L('FIELDS'), 'class' => 'showContent');
        $tplList[] = array('id' => 'id_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'id_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $identityFields = D('Identity')->getMemberFields();

        $addList[] = array('name' => 'id_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'id_fields[]', 'title' => L('FIELDS'), 'type' => 'checkbox', 'data' => $identityFields);
        $addList[] = array('title' => L('STATUS'), 'name' => 'id_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'id_id', $title, $result);
    }
}