<?php
namespace Manage\Controller;
class MemberIdentityAttributeController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'miat_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'miat_title', 'percent' => '20', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'miat_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'miat_value', 'percent' => '20', 'title' => L('DEFAULT_VALUE'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'miat_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'miat_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'miat_name', 'class' => 'w460');
        $addList[] = array('title' => L('TYPE'), 'name' => 'miat_type', 'label' => 'select', 'data' => array(1 => L('TEXT'), 2  => L('MULTI_SELECT'), 9 => L('CHOICE')));
        $addList[] = array('title' => L('DEFAULT_VALUE'), 'name' => 'miat_value', 'label' => 'textarea', 'class' => 'h80', 'tip' => L('DEFAULT_VALUE_PROMPT'));

        return generationAddTpl($addList, 'miat_id', $title, $result);
    }

    public function delete() {
        $res = D('MemberIdentityAttribute')->delete(I('id', 0, 'strval'));
        
        $this->show($res, L('DELETE'));
    }
}